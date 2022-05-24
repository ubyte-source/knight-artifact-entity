<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\Validation;

/* This class is used to validate that a field is an array */

class ShowArray extends Validation
{
    const TYPE = ':array';

    /**
     * Convert a PHP array to a JSON object
     * 
     * @param array value The value to convert.
     * 
     * @return An array.
     */
    
    public static function convert(array $value) : array
    {
        return json_decode(json_encode($value), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * Given an array, return a new array with only the values that pass the callback
     * 
     * @param array array The array to be filtered.
     * 
     * @return An array of values that meet the condition of the callback.
     */
    
    public static function filter(array $array) : array
    {
        $callback = array(static::class, 'callback');
        $filtered = static::convert($array);
        return array_filter($filtered, $callback);
    }

    /**
     * The constructor sets the default values for the object
     * 
     * @param default The default value for the parameter.
     */
    
    public function __construct(?array $default = [])
    {
        $this->setDefault($default);
    }

    /**
     * If the field's value is a string, convert it to an object
     * 
     * @param Field field The field that is being validated.
     * 
     * @return The return value is a boolean value. If the method returns true, the field value will be
     * converted. If the method returns false, the field value will not be converted.
     */
    
    public function before(Field $field) : bool
    {
        $field_value = $field->getValue();
        if (is_string($field_value)) {
            $field_value_converted = json_decode($field_value);
            $field->setValue($field_value_converted, Field::OVERRIDE);
        }
        return true;
    }

    /**
     * If the field is not in safe mode, then return true. Otherwise, return whether or not the field
     * value is an array
     * 
     * @param Field field The field object that is being validated.
     * 
     * @return `true`
     */
    
    public function action(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        return is_array($field_value);
    }

    /**
     * If the field is in safe mode, then the field's value is converted to an array and the array is
     * sorted
     * 
     * @param Field field The field object that is being validated.
     */
    
    public function after(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        $field_value = array_values($field_value);
        $field->setValue($field_value, Field::OVERRIDE);

        return true;
    }

    /**
     * Returns true if the item is not null
     * 
     * @param item The item to be tested.
     * 
     * @return The callback function is being called with the item as an argument. If the item is not
     * null, then the callback function returns true. Otherwise, it returns false.
     */
    
    protected static function callback($item) : bool
    {
        return !is_null($item);
    }
}
