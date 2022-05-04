<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\Validation;

/* The Number class is a validation class that checks if the value is a number */

class Number extends Validation
{
    const TYPE = ':number';

    protected $min; // (float)
    protected $max; // (float)

    /**
     * The constructor for the PHP class
     * 
     * @param default The default value for the parameter.
     */
    
    public function __construct(?float $default = null, ...$other)
    {
        $this->setDefault($default);
    }

    /**
     * If the field is in safe mode, and the field value is not a string or a number, then set the
     * field value to the default value
     * 
     * @param Field field The field object that is being validated.
     * 
     * @return The return value is a boolean value. If the value is numeric, then true is returned.
     * Otherwise, false is returned.
     */
    
    public function before(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        if (!is_string($field_value) && !is_numeric($field_value)) return false;
        if (0 === strlen((string)$field_value)) $field->setDefault();

        $field_value = $field->getValue();
        return is_numeric($field_value);
    }

    /**
     * If the field is in safe mode, and the field is not a default value, then convert the field value
     * to a double
     * 
     * @param Field field The field that is being validated.
     * 
     * @return The return value is a boolean value. If the value is true, the field is safe. If the
     * value is false, the field is unsafe.
     */
    
    public function action(Field $field) : bool
    {
        $field_safemode = $field->getSafeMode();
        if ($field_safemode !== true
            || $field->isDefault()) return true;

        $field_value = $field->getValue();
        $field_value = (double)($field_value);
        $field->setValue($field_value, Field::OVERRIDE);
        return true;
    }

    /**
     * If the field's value is less than or equal to the minimum value, or greater than or equal to the
     * maximum value, return true. Otherwise, return false
     * 
     * @param Field field The field that is being validated.
     * 
     * @return The return value is a boolean value.
     */
    
    public function after(Field $field) : bool
    {
        $field_safemode = $field->getSafeMode();
        if (false === $field_safemode) return true;

        $field_value = $field->getValue();

        $min = $this->getMin();
        $max = $this->getMax();
        return ($min === null || $min <= $field_value)
            && ($max === null || $field_value <= $max);
    }

    /**
     * Set the minimum value for the field
     * 
     * @param min The minimum value of the parameter.
     * 
     * @return The object itself.
     */
    
    public function setMin(?string $min) : self
    {
        $this->min = $min;
        return $this;
    }

    /**
     * Set the maximum number of items to return
     * 
     * @param max The maximum number of items to return.
     * 
     * @return The object itself.
     */
    
    public function setMax(?int $max) : self
    {
        $this->max = $max;
        return $this;
    }

    /**
     * Returns the minimum value of the array
     * 
     * @return The min proprerty.
     */
    
    protected function getMin() :? float
    {
        return $this->min;
    }

    /**
     * "Get the maximum value of the data set."
     * 
     * The function name is `getMax()`. The function returns a `float` value. The function has one
     * parameter, ``. The function returns `null` if the maximum value is not set
     * 
     * @return The max property.
     */
    
    protected function getMax() :? float
    {
        return $this->max;
    }
}