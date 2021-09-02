<?PHP

namespace Entity;

use Closure;
use stdClass;
use ReflectionObject;
use ReflectionProperty;

use Knight\armor\CustomException;

use Entity\Map as Entity;
use Entity\Validation;
use Entity\validations\Matrioska;
use Entity\validations\ShowArray;
use Entity\field\Row;
use Entity\field\Warning;
use Entity\field\warning\Handler;

class Field
{
    const OVERRIDE = 0x1;
    const SKIPJSON = 0x2;

    const READABLE = true;
    const READABLE_VALUE_FILTER = 0x4;
    const READABLE_VALUE_RAW = 0x8;

    const HUMAN = 'human';
    const REQUIRED = 'required';
    const TEXT = 'text';

    protected $row;               // Row
    protected $name;              // (string)
    protected $unique = [];       // (array)
    protected $patterns = [];     // (array)
    protected $required = false;  // (bool)
    protected $protected = false; // (bool)
    protected $warning;           // Warning
    protected $trigger;           // Closure

    private $core;                // Entity
    private $value;               // (mixed)

    private $safemode = false;    // (bool)
    private $readmode = false;    // (bool)

    public static function json($string) : bool
    {
        if (false === is_string($string) || is_numeric($string)) return false;
        $decoded = @json_decode($string);
        return json_last_error() == JSON_ERROR_NONE
            && $decoded !== $string;
    }

    public function __clone()
    {
        $this->setWarning(new Warning());
        $variables = get_object_vars($this);
        $variables = array_keys($variables);
        $variables_remove = [];
        array_push($variables_remove, 'core');
        $variables = array_diff($variables, $variables_remove);
        $variables_glue = [];
        foreach ($variables as $name) array_push($variables_glue, array(&$this->$name));
        array_walk_recursive($variables_glue, function (&$item, $name) {
            if (false === is_object($item)) return;
            $clone = clone $item;
            if ($clone instanceof Entity) $clone->cloneHashEntity($item);
            $item = $clone;
        });
    }

    public function __construct(Entity $core, string $name, bool &$safemode, bool &$readmode)
    {
        $this->setCore($core);
        $this->setName($name);
        $this->setSafeMode($safemode);
        $this->setReadMode($readmode);

        $this->setWarning(new Warning());
        $this->setRow(new Row());
    }

    public function setCore(Entity $core) : self
    {
        $this->core = $core;
        return $this;
    }

    public function getCore() : Entity
    {
        return $this->core;
    }

    public function setPatterns(Validation ...$patterns) : self
    {
        if (empty($patterns)) return $this;

        $first = reset($patterns);
        $first_name = get_class($first);

        $checked = array_filter($patterns, function (Validation $validation) use ($first_name) {
            return $first_name === get_class($validation);
        });

        $first_name = str_replace('\\', DIRECTORY_SEPARATOR, $first_name);
        $first_name = basename($first_name);
        $first_name = mb_strtolower($first_name);

        if (count($checked) !== count($patterns)) throw new CustomException('developer/entity/field/pattern/type/' . $first_name);

        $this->override(...$patterns);

        if (false === $this->checkPatterns()) throw new CustomException('developer/entity/field/pattern/test/' . $first_name);
        if (false === $this->isDefault()) {
            $field_protected = $this->getProtected();
            $this->setProtected(false)->setValue($first->getDefault(), static::OVERRIDE);
            $this->setProtected($field_protected);
        }

        return $this;
    }

    public function getWarning(bool $deep = false) : Warning
    {
        $type = $this->getType();
        $patterns = $this->getPatterns();
        if (Matrioska::TYPE !== $type
            || false === $deep
            || false === reset($patterns)) return $this->warning;

        $warning = new Warning();
        $warning_handlers = $this->warning->getHandlers();
        $warning->addHandlers(...$warning_handlers);

        $field_value = $this->getValue();
        $field_value = array($field_value);

        array_walk_recursive($field_value, function ($item, $index) use ($warning) {
            if (false === ($item instanceof Entity)
                || $item->isDefault() && false === $this->getRequired()) return;

            $multiple = $this->getValue();
            $multiple = is_array($multiple);
            $handlers = $item->getAllFieldsWarning(Entity::DISABLE_TRANSLATE_WARNING);
            foreach ($handlers as $handler) {
                $clone = clone $handler;
                $warning->addHandlers($clone);
                if ($multiple) $clone->addName($index);
                $clone->addName($this->getName());
            }
        });

        return $warning;
    }

    public function setTrigger(Closure $closure) : self
    {
        $this->trigger = $closure;
        return $this;
    }

    public function getTrigger() :? Closure
    {
        return $this->trigger;
    }

    public function setRow(Row $row) : self
    {
        $this->row = $row;
        return $this;
    }

    public function getRow() : Row
    {
        return $this->row;
    }

    public function setValue($value, int $flags = 0) : bool
    {
        if (false === (bool)(static::OVERRIDE & $flags)
            && $this->getSafeMode() && $this->getProtected()) return false;

        if (false === (bool)(static::SKIPJSON & $flags))
            $this->value = static::json($value) ? json_decode($value) : $value;

        $trigger = $this->getTrigger();
        if (null !== $trigger) $trigger->call($this);
        if ((bool)(static::OVERRIDE & $flags)
            || true === $this->checkValue()) return true;

        $this->setDefault();

        if (null === $value) return true;
        if ($this->getType() === Matrioska::TYPE) return false;

        $handler = new Handler($this, Validation::PATTERN);
        $this->getWarning()->addHandlers($handler);

        return false;
    }

    public function setDefault() : self
    {
        $item = $this->getDefaults();
        $item = empty($item) ? null : reset($item);
        $this->setValue($item, static::OVERRIDE);
        return $this;
    }

    public function getDefaults() : array
    {
        $patterns = $this->getPatterns();
        $patterns = array_map(function (Validation $pattern) {
            if ($pattern instanceof Matrioska && $pattern->getMultiple()) return array();
            return $pattern->getDefault();
        }, $patterns);
        $patterns = array_unique($patterns, SORT_REGULAR);
        $patterns = array_values($patterns);
        return $patterns;
    }

    public function isDefault() : bool
    {
        $field_value = $this->getValue();
        if ($field_value instanceof Entity) return $field_value->isDefault();

        $field_deafults = $this->getDefaults();
        foreach ($field_deafults as $item) {
            if ($item !== $field_value) continue;
            return true;
        }

        return false;
    }

    public function getValue(bool $readable = false, int $flags = 0)
    {
        if ($readable !== static::READABLE) return $this->value;
    
        $value = $value instanceof Entity
            ? clone $value
            : $this->value;
        $value_recursive = array(&$value);
        array_walk_recursive($value_recursive, function (&$item) use ($flags) {
            if ($item instanceof Entity) $item = $item->getAllFieldsValues(
                (bool)($flags & static::READABLE_VALUE_FILTER),
                (bool)($flags & static::READABLE_VALUE_RAW)
            );
        });
        return $value;
    }

    public function setProtected(bool $protected = true) : self
    {
        $this->protected = $protected;
        return $this;
    }

    public function getProtected() : bool
    {
        return $this->protected === true;
    }

    public function setRequired(bool $required = true) : self
    {
        $this->required = $required;
        return $this;
    }

    public function getRequired() : bool
    {
        return $this->required === true;
    }

    public function try(bool $deep = false) : bool
    {
        if ($deep) {
            $field_value = $this->getValue();
            $field_value = array($field_value);
            array_walk_recursive($field_value, function ($item) use ($deep) {
                if (false === ($item instanceof Entity)) return;
                $item->checkRequired($deep);
            });
        }

        if ($this->getProtected()
            || false === $this->getRequired()
            || false === $this->isDefault()) return true;

        $handler = new Handler($this, static::REQUIRED);
        $this->getWarning()->addHandlers($handler);

        return true;
    }

    public function addUniqueness(string ...$groups_name) : self
    {
        $groups = array_filter($groups_name, 'strlen');

        if (empty($groups)) array_push($groups, $this->getName());

        $groups = array_unique($groups, SORT_STRING);
        $groups = array_diff($groups, $this->unique);
        $groups = array_values($groups);
        if (!!$groups) array_push($this->unique, ...$groups);

        return $this;
    }

    public function getUniqueness() : array
    {
        return $this->unique;
    }

    public function setUniqueness(string ...$uniqueness) : self
    {
        $this->unique = $uniqueness;
        return $this;
    }

    public function setName(string $name) : self
    {
        $trim = trim($name);
        if (!preg_match('/^\w{1,}$/', $trim)) throw new CustomException('developer/entity/field/name/set');

        $this->name = $trim;
        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getPatterns() : array
    {
        return $this->patterns;
    }

    public function getType(bool $human = true) :? string
    {
        $patterns = $this->getPatterns();
        $patterns = reset($patterns);
        return $patterns === false ? null : $patterns->getType($human);
    }

    public function setSafeMode(bool &$safemode) : self
    {
        $this->safemode = &$safemode;
        return $this;
    }

    public function setSafeModeDetached(bool $safemode) : self
    {
        $this->safemode = &$safemode;
        return $this;
    }

    public function getSafeMode() : bool
    {
        return $this->safemode === true;
    }

    public function setReadMode(bool &$readmode) : self
    {
        $this->readmode = &$readmode;
        return $this;
    }

    public function setReadModeDetached(bool $readmode) : self
    {
        $this->readmode = &$readmode;
        return $this;
    }

    public function getReadMode() : bool
    {
        return $this->readmode === true;
    }

    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $clone = clone $this;
        $clone_remove = new ReflectionObject($clone);
        $clone_remove = $clone_remove->getProperties(ReflectionProperty::IS_PRIVATE);
        $clone_remove = array_column($clone_remove, 'class', 'name');

        $namespace_name = $clone->getName();
        $namespace_name = $namespace . $namespace_name . '\\';

        $variables = get_object_vars($clone);
        $variables = array_diff_key($variables, $clone_remove);
        array_walk_recursive($variables, function (&$item, $name) use ($namespace_name, $protected) {
            if (false === is_object($item)) return;
            if (false === method_exists($item, static::HUMAN)) return $item = null;
            $item = (array)$item->{static::HUMAN}($namespace_name, $protected);
        });
        $variables = ShowArray::filter($variables);
        $variables = (object)$variables;
        $variables->type = $this->getType();

        return $variables;
    }

    protected static function suitablePatterns(self $field) : array
    {
        $patterns = $field->getPatterns();
        $patterns = array_filter($patterns, function (Validation $pattern) use ($field) {
            if (Matrioska::class !== get_class($pattern)) return true;
            $field_clone = clone $field;
            $field_clone->setSafeModeDetached(true)->setProtected();
            $field_clone_response = $pattern->runner($field_clone);
            $field_clone_response = boolval($field_clone_response);
            return true === $field_clone_response
                && $field_clone->isDefault() === false;
        });
        return $patterns;
    }

    protected function checkPatterns() : bool
    {
        $patterns = $this->getPatterns();
        foreach ($patterns as $pattern) {
            if (false === method_exists($pattern, Validation::TEST)) continue;
            if (false === $pattern->test()) return false;
        }
        return true;
    }

    protected function checkValue() : bool
    {
        $patterns = $this->getPatterns();
        if (0 === count($patterns)) return true;

        $suited = static::suitablePatterns($this);
        $handle = $this->getWarning()->getHandlers();
        foreach ($suited as $pattern) {
            $response = $pattern->runner($this);
            $response = boolval($response);
            $response_handle = $this->getWarning()->getHandlers();
            if (true !== $response
                || count($handle) !== count($response_handle)) continue;

            $this->setWarning(new Warning());
            return true;
        }

        return false;
    }

    protected function setWarning(Warning $warning) : void
    {
        $this->warning = $warning;
    }

    protected function override(Validation ...$patterns) : void
    {
        $this->patterns = $patterns;
    }
}