<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\Validation;

/* If the field is not a boolean, it will be converted to a boolean */

class ShowBool extends Validation
{
    const TYPE = ':bool';

    /**
     * The constructor for the PHP class
     * 
     * @param default The default value for the parameter.
     */
    
    public function __construct(?bool $default = null)
    {
        $this->setDefault($default);
    }

    /**
     * This function is called before the field is rendered
     * 
     * @param Field field The field that is being validated.
     * 
     * @return The return value is a boolean value. If the method returns true, the field is allowed to
     * be rendered. If the method returns false, the field is not rendered.
     */
    
    public function before(Field $field) : bool
    {
        return true;
    }

    /**
     * If the field is in safe mode, then the field value is converted to a boolean value
     * 
     * @param Field field The field object that is being validated.
     * 
     * @return The field value.
     */
    
    public function action(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        $field_value_default = $this->getDefault();

        if ($field_value !== $field_value_default) {
            $field_value = filter_var($field_value, FILTER_VALIDATE_BOOLEAN);
            $field->setValue($field_value, Field::OVERRIDE);
        }

        return $field_value === true
            || $field_value === false;
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
