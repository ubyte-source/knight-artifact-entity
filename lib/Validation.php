<?PHP

namespace Entity;

use Closure;
use ReflectionClass;

use Entity\Map as Entity;
use Entity\validations\interfaces\Validation as InterfaceValidation;

abstract class Validation implements InterfaceValidation
{
    // const TYPE = ':type'

    const TEST = 'test';
    const PATTERN = 'pattern';

    const OPERATIONS_EXECUTE = [
        'before',
        'action',
        'after',
        'magic'
    ];

    protected $default;                      // (null)
    protected $closure_magic_status = true;  // (bool)
    protected $closure_magic;                // Closure

    public static function factory(string $name, ...$parameters) : self
    {
        $instance = __namespace__ . '\\' . 'validations' . '\\' . $name;
        $instance = new $instance(...$parameters);
        return $instance;
    }

    public function __clone()
    {
        $variables = get_object_vars($this);
        $variables = array_keys($variables);
        $variables_glue = [];
        foreach ($variables as $name) array_push($variables_glue, array(&$this->$name));
        array_walk_recursive($variables_glue, function (&$item, $name) {
            if (false === is_object($item)) return;
            $clone = clone $item;
            if ($clone instanceof Entity) $clone->cloneHashEntity($item);
            $item = $clone;
        });
    }

    public function runner(Field $field) : bool
    {
        foreach (static::OPERATIONS_EXECUTE as $method) {
            if (false === method_exists($this, $method)) continue;
            if (false === call_user_func_array(array($this, $method), array($field))) return false;
        }
        return true;
    }

    public function magic(Field $field) : bool
    {
        $closure_magic = $this->getClosureMagic();
        $closure_magic_status = $this->getClosureMagicStatus();
        if (false === $closure_magic_status
            || null === $closure_magic) return true;

        return call_user_func($closure_magic, $field);
    }

    public function setClosureMagicStatus(bool $status) : self
    {
        $this->closure_magic_status = $status;
        return $this;
    }

    public function getClosureMagicStatus() : bool
    {
        return $this->closure_magic_status;
    }

    public function setClosureMagic(Closure $closure) : self
    {
        $this->closure_magic = $closure;
        return $this;
    }

    public function getClosureMagic() :? Closure
    {
        return $this->closure_magic;
    }

    public function getType(bool $human = true) : string
    {
        $reflection = new ReflectionClass($this);
		$reflection_constants = $reflection->getConstants();
        if ($human) return array_key_exists('TYPE', $reflection_constants) ? static::TYPE : ':undefined';
        return $reflection->getName();
    }

    public function getDefault()
    {
        return $this->default;
    }

    protected function setDefault($default) : void
    {
        $this->default = $default;
    }
}