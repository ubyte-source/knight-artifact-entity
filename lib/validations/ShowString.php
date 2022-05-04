<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\Validation;

/* This class is used to validate that a field is a string */

class ShowString extends Validation
{
    const TYPE = ':string';

    protected $min;
    protected $max;

    /**
     * The constructor sets the default value of the property
     * 
     * @param default The default value to use if none is provided.
     */
    
    public function __construct(?string $default = null)
    {
        $this->setDefault($default);
    }

    /**
     * If the field is not in safe mode, return true. If the field is in safe mode, and the field value
     * is not a string, return false. If the field is in safe mode and the field value is a string, and
     * the field value is empty, set the field value to the default value. If the field is in safe mode
     * and the field value is a string, and the field value is not empty, and the field value is less
     * than the minimum value, set the field value to the minimum value. If the field is in safe mode
     * and the field value is a string, and the field value is not empty, and the field value is
     * greater than the maximum value, set the field value to the maximum value. If the field is in
     * safe mode and the field value is a string, and the field value is not empty, and the field value
     * is between the minimum and maximum values, return true
     * 
     * @param Field field The field object that is being validated.
     */
    
    public function before(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        if (!is_string($field_value) && !is_numeric($field_value)) return false;
        if (0 === strlen((string)$field_value)) {
            $field->setDefault();
            return true;
        }

        if (is_numeric($field_value)) $field->setValue($field_value = (string)$field_value, Field::OVERRIDE);
        if (false === is_string($field_value)) return false;

        $min = $this->getMin();
        $max = $this->getMax();
        return ($min === null || $min <= strlen($field_value))
            && ($max === null || strlen($field_value) <= $max);
    }

    /**
     * The action function is called when the field is being validated
     * 
     * @param Field field The field that is being checked.
     * 
     * @return The return type is `bool` which means that the function will return `true` or `false`.
     */
    
    public function action(Field $field) : bool
    {
        return true;
    }

    /**
     * This function is called after the field has been processed
     * 
     * @param Field field The field that is being validated.
     * 
     * @return The return value is a boolean value. If the method returns true, the field is added to
     * the form. If the method returns false, the field is not added to the form.
     */
    
    public function after(Field $field) : bool
    {
        return true;
    }

    /**
     * "Set the minimum value of the range."
     * 
     * The function name is setMin. It takes an integer as a parameter and returns a new instance of
     * the class
     * 
     * @param int min The minimum value of the parameter.
     * 
     * @return The object itself.
     */
    
    public function setMin(int $min) : self
    {
        $this->min = $min;
        return $this;
    }

    /**
     * Set the maximum number of items to return
     * 
     * @param int max The maximum number of items to return.
     * 
     * @return The object itself.
     */
    
    public function setMax(int $max) : self
    {
        $this->max = $max;
        return $this;
    }

    /**
     * Returns the minimum value of the array
     * 
     * @return The min property.
     */
    
    protected function getMin() :? int
    {
        return $this->min;
    }

    /**
     * Returns the maximum number of items that can be stored in the queue
     * 
     * @return The max property.
     */
    
    protected function getMax() :? int
    {
        return $this->max;
    }
}