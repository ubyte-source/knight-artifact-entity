<?PHP

namespace Entity;

use Closure;
use ReflectionClass;

use Entity\Map as Entity;
use Entity\validations\interfaces\Validation as InterfaceValidation;

/* This class is used to validate data */

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

    /**
     * The factory function is used to create an instance of a class
     * 
     * @param string name The name of the class to be instantiated.
     * 
     * @return An instance of the class that was called.
     */

    public static function factory(string $name, ...$parameters) : self
    {
        $instance = __namespace__ . '\\' . 'validations' . '\\' . $name;
        $instance = new $instance(...$parameters);
        return $instance;
    }

    /**
     * Clone the object and all of its properties
     * 
     * @return Nothing.
     */
    
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

    /**
     * For each operation, if the operation is defined, call it with the field
     * 
     * @param Field field The field to be validated.
     * 
     * @return The return value is a boolean value.
     */
    
    public function runner(Field $field) : bool
    {
        foreach (static::OPERATIONS_EXECUTE as $method) {
            if (false === method_exists($this, $method)) continue;
            if (false === call_user_func_array(array($this, $method), array($field))) return false;
        }
        return true;
    }

    /**
     * If the closure magic is set, call it and return the result
     * 
     * @param Field field The field that is being validated.
     * 
     * @return The return value of the closure.
     */
    
    public function magic(Field $field) : bool
    {
        $closure_magic = $this->getClosureMagic();
        $closure_magic_status = $this->getClosureMagicStatus();
        if (false === $closure_magic_status
            || null === $closure_magic) return true;

        return call_user_func($closure_magic, $field);
    }    

    /**
     * The `setClosureMagicStatus` method is a method that sets the `closure_magic_status` property to
     * the value of the `$status` parameter
     * 
     * @param bool status Whether or not the closure magic is enabled.
     * 
     * @return The object itself.
     */
    
    public function setClosureMagicStatus(bool $status) : self
    {
        $this->closure_magic_status = $status;
        return $this;
    }

    /**
     * Returns the status of the closure magic feature
     * 
     * @return The closure_magic_status property of the object.
     */
    
    public function getClosureMagicStatus() : bool
    {
        return $this->closure_magic_status;
    }

    /**
     * *This function sets the closure magic variable to the closure passed in.*
     * 
     * The above function is a setter for the closure_magic variable. It takes in a closure and sets
     * the closure_magic variable to the closure passed in
     * 
     * @param Closure closure The closure to be executed.
     * 
     * @return The object itself.
     */
    
    public function setClosureMagic(Closure $closure) : self
    {
        $this->closure_magic = $closure;
        return $this;
    }

    /**
     * Returns the closure magic variable if it exists, otherwise returns null
     * 
     * @return The closure magic.
     */
    
    public function getClosureMagic() :? Closure
    {
        return $this->closure_magic;
    }

    /**
     * Returns the type of the object
     * 
     * @param bool human If true, the type will be returned in human-readable format. If false, the
     * type will be returned in the format of the class name.
     * 
     * @return The class name.
     */
    
    public function getType(bool $human = true) : string
    {
        $reflection = new ReflectionClass($this);
		$reflection_constants = $reflection->getConstants();
        if ($human) return array_key_exists('TYPE', $reflection_constants) ? static::TYPE : ':undefined';
        return $reflection->getName();
    }

    /**
     * Returns the default value for the column
     * 
     * @return The default value of the column.
     */
    
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set the default value for the column
     * 
     * @param default The default value for the column.
     */
    
    protected function setDefault($default) : void
    {
        $this->default = $default;
    }
}