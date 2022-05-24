<?PHP

namespace Entity\validations;

use stdClass;

use Entity\Field;
use Entity\Validation;

/* If the field's value is a string, it will be converted to a stdClass object */

class ShowObject extends Validation
{
    const TYPE = ':object';

    /**
     * The constructor takes a default value as a parameter
     * 
     * @param default The default value to use if the parameter is not set.
     */
    
    public function __construct(?stdClass $default = null)
    {
        $this->setDefault($default);
    }

    /**
     * If the field's value is a string, convert it to an array
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
     * If the field is safe, return true
     * 
     * @param Field field The field object that is being validated.
     * 
     * @return `true`
     */
    
    public function action(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        return $field_value instanceof stdClass;
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
}
