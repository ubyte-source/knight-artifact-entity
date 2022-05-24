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
    const PRIMARY = 'primary';
    const TEXT = 'text';
    const NAME = 'name';

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

    /**
     * If the string is a valid JSON string, return true. Otherwise, return false
     * 
     * @param string The string to check.
     * 
     * @return A boolean value.
     */
    
    public static function json($string) : bool
    {
        if (false === is_string($string) || is_numeric($string)) return false;
        $decoded = @json_decode($string);
        return json_last_error() == JSON_ERROR_NONE
            && $decoded !== $string;
    }

    /**
     * Clone the object and all its properties
     * 
     * @return Nothing.
     */
    
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

    /**
     * The constructor for the class
     * 
     * @param Entity core The core object that is used to access the database.
     * @param string name The name of the table.
     * @param bool safemode If true, the table will be read only.
     * @param bool readmode This is a boolean value that indicates whether the current user has read
     * access to the table.
     */
    
    public function __construct(Entity $core, string $name, bool &$safemode, bool &$readmode)
    {
        $this->setCore($core);
        $this->setName($name);
        $this->setSafeMode($safemode);
        $this->setReadMode($readmode);

        $this->setWarning(new Warning());
        $this->setRow(new Row());
    }

    /**
     * The setCore function sets the core property of the class to the core parameter
     * 
     * @param Entity core The core entity that is being used to create the new entity.
     * 
     * @return The object itself.
     */
    
    public function setCore(Entity $core) : self
    {
        $this->core = $core;
        return $this;
    }

    /**
     * Returns the core of the entity
     * 
     * @return The core entity.
     */
    
    public function getCore() : Entity
    {
        return $this->core;
    }

    /**
     * * Set the validation patterns for the field
     * 
     * @return The object itself.
     */
    
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

    /**
     * If the field is a Matrioska field, and the field is not a default value, and the field is
     * required, then return the warning
     * 
     * @param bool deep If true, the warning will be returned for all the fields in the entity.
     * 
     * @return The warning object.
     */
    
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

    /**
     * The setTrigger method takes a Closure as a parameter and sets it as the trigger for the event
     * 
     * @param Closure closure A closure that will be called when the event is triggered.
     * 
     * @return The object itself.
     */
    
    public function setTrigger(Closure $closure) : self
    {
        $this->trigger = $closure;
        return $this;
    }

    /**
     * Returns the trigger function for the event
     * 
     * @return The closure that is being returned is the closure that is being assigned to the trigger
     * property.
     */
    
    public function getTrigger() :? Closure
    {
        return $this->trigger;
    }

    /**
     * The setRow function sets the row property to the value of the row parameter
     * 
     * @param Row row The row that was just added to the table.
     * 
     * @return The object itself.
     */
    
    public function setRow(Row $row) : self
    {
        $this->row = $row;
        return $this;
    }

    /**
     * Returns the current row of the result set
     * 
     * @return The row object.
     */
    
    public function getRow() : Row
    {
        return $this->row;
    }

    /**
     * If the value is not set, set it to the default value. If the value is set, check it against the
     * validation pattern. If it passes, return true. If it doesn't, return false
     * 
     * @param value The value to set.
     * @param int flags 
     */
    
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

    /**
     * This function sets the default value for the field
     * 
     * @return The object itself.
     */
    
    public function setDefault() : self
    {
        $item = $this->getDefaults();
        $item = empty($item) ? null : reset($item);
        $this->setValue($item, static::OVERRIDE);
        return $this;
    }

    /**
     * Returns an array of default values for all the patterns in the validation
     * 
     * @return An array of default values.
     */
    
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

    /**
     * If the field value is an entity, return the entity's isDefault() value. Otherwise, return false
     * 
     * @return The `isDefault()` method returns a boolean value.
     */
    
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

    /**
     * Returns the value of the field
     * 
     * @param bool readable If true, the value will be returned as a readable value.
     * @param int flags 
     * 
     * @return The value of the field.
     */
    
    public function getValue(bool $readable = false, int $flags = 0)
    {
        if ($readable !== static::READABLE) return $this->value;
    
        $value = $this->value;
        if (true === ($value instanceof Entity)) $value = clone $value;

        $value_recursive = array(&$value);
        array_walk_recursive($value_recursive, function (&$item) use ($flags) {
            if ($item instanceof Entity) $item = $item->getAllFieldsValues(
                (bool)($flags & static::READABLE_VALUE_FILTER),
                (bool)($flags & static::READABLE_VALUE_RAW)
            );
        });

        return $value;
    }

    /**
     * * Set the protected property to the value of the argument
     * 
     * @param bool protected Whether or not the property is protected.
     * 
     * @return Nothing.
     */
    
    public function setProtected(bool $protected = true) : self
    {
        $this->protected = $protected;
        return $this;
    }

    /**
     * "Get the value of the protected property."
     * 
     * The function name is `getProtected()`
     * 
     * @return The protected property of the class.
     */
    
    public function getProtected() : bool
    {
        return $this->protected === true;
    }

    /**
     * Set the required flag to true or false
     * 
     * @param bool required Whether or not the field is required.
     * 
     * @return Nothing.
     */
    
    public function setRequired(bool $required = true) : self
    {
        $this->required = $required;
        return $this;
    }

    /**
     * Returns a boolean value indicating whether the field is required
     * 
     * @return The value of the `required` property.
     */
    
    public function getRequired() : bool
    {
        return $this->required === true;
    }

    /**
     * If the field is required, and the field is not protected, and the field has a default value,
     * then return true
     * 
     * @param bool deep If true, the checkRequired method will be called recursively on all child
     * entities.
     * 
     * @return Nothing.
     */
    
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

    /**
     * Add a group to the list of groups that this group is unique to
     * 
     * @return The object itself.
     */
    
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

    /**
     * Returns the uniqueness of the column
     * 
     * @return An array of the unique columns.
     */
    
    public function getUniqueness() : array
    {
        return $this->unique;
    }

    /**
     * Set the uniqueness of the column
     * 
     * @return The object itself.
     */
    
    public function setUniqueness(string ...$uniqueness) : self
    {
        $this->unique = $uniqueness;
        return $this;
    }

    /**
     * * Set the name of the field
     * 
     * @param string name The name of the field.
     * 
     * @return The object itself.
     */
    
    public function setName(string $name) : self
    {
        $trim = trim($name);
        if (!preg_match('/^\w{1,}$/', $trim)) throw new CustomException('developer/entity/field/name/set');

        $this->name = $trim;
        return $this;
    }

    /**
     * Get the name of the person
     * 
     * @return The name of the person.
     */
    
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Returns an array of patterns that are used to match the file name
     * 
     * @return An array of strings.
     */
    
    public function getPatterns() : array
    {
        return $this->patterns;
    }

    /**
     * Returns the type of the pattern
     * 
     * @param bool human If true, the type will be returned in human-readable format.
     * 
     * @return The type of the first pattern.
     */
    
    public function getType(bool $human = true) :? string
    {
        $patterns = $this->getPatterns();
        $patterns = reset($patterns);
        return $patterns === false ? null : $patterns->getType($human);
    }

    /**
     * The setSafeMode function sets the safemode property of the class to the value of the passed in
     * parameter
     * 
     * @param bool safemode If true, the database will be put into a safe mode before running the
     * script.
     * 
     * @return The object itself.
     */
    
    public function setSafeMode(bool &$safemode) : self
    {
        $this->safemode = &$safemode;
        return $this;
    }

    /**
     * Set the safemode flag to the value of the safemode parameter
     * 
     * @param bool safemode If true, the database will be put into a safe mode before the backup is
     * taken.
     * 
     * @return The object itself.
     */
    
    public function setSafeModeDetached(bool $safemode) : self
    {
        $this->safemode = &$safemode;
        return $this;
    }

    /**
     * Returns the value of the `safemode` property
     * 
     * @return The value of the safemode property.
     */
    
    public function getSafeMode() : bool
    {
        return $this->safemode === true;
    }

    /**
     * * Set the read mode of the connection
     * 
     * @param bool readmode A reference to a boolean value that is set to true if the connection is in
     * read mode.
     * 
     * @return The object itself.
     */
    
    public function setReadMode(bool &$readmode) : self
    {
        $this->readmode = &$readmode;
        return $this;
    }

    /**
     * * Sets the read mode to detached
     * 
     * @param bool readmode If true, the connection will be in read mode. If false, the connection will
     * be in write mode.
     * 
     * @return The object itself.
     */
    
    public function setReadModeDetached(bool $readmode) : self
    {
        $this->readmode = &$readmode;
        return $this;
    }

    /**
     * Returns the read mode of the connection
     * 
     * @return The value of the readmode property.
     */
    
    public function getReadMode() : bool
    {
        return $this->readmode === true;
    }

    /**
     * Returns an object with all the properties of the class, but with the properties of the parent
     * class removed
     * 
     * @param namespace The namespace to use for the human readable object.
     * @param bool protected If true, the object will be protected from being overwritten.
     * 
     * @return An object with the properties of the object, but with the values of the human() method.
     */
    
    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $clone = clone $this;
        $clone_remove = new ReflectionObject($clone);
        $clone_remove = $clone_remove->getProperties(ReflectionProperty::IS_PRIVATE);
        $clone_remove = array_column($clone_remove, 'class', static::NAME);

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

    /**
     * This function returns an array of patterns that are suitable for the field
     * 
     * @param self field The field that is being validated.
     * 
     * @return An array of Validation objects that are suitable for the field.
     */
    
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

    /**
     * Check the patterns for validity
     * 
     * @return The return value is a boolean value.
     */
    
    protected function checkPatterns() : bool
    {
        $patterns = $this->getPatterns();
        foreach ($patterns as $pattern) {
            if (false === method_exists($pattern, Validation::TEST)) continue;
            if (false === $pattern->test()) return false;
        }
        return true;
    }

    /**
     * If the value is not valid, then the warning is set and the function returns false
     * 
     * @return The return value is a boolean value.
     */
    
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

    /**
     * The setWarning function is used to set the warning property of the class
     * 
     * @param Warning warning The warning to set.
     */

    protected function setWarning(Warning $warning) : void
    {
        $this->warning = $warning;
    }


    /**
     * *This function overrides the default validation patterns with the ones provided.*
     */
    
    protected function override(Validation ...$patterns) : void
    {
        $this->patterns = $patterns;
    }
}
