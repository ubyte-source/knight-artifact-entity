<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\Validation;

/* Validates that the value is a valid hostname */

class Host extends Validation
{
    const TYPE = ':host';

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
     * If the field is in safe mode, and the field value is empty, set the field value to the default
     * value
     * 
     * @param Field field The field object that is being validated.
     * 
     * @return The return value is a boolean value. If the return value is true, the field value is not
     * modified. If the return value is false, the field value is modified.
     */
    
    public function before(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        if (!is_string($field_value) && !is_numeric($field_value)) return false;
        if (0 === strlen((string)$field_value)) $field->setDefault();

        return true;
    }

    /**
     * If the field is in read mode, or if the field is not in safe mode, then return true. Otherwise,
     * return true if the field value is a valid IP address
     * 
     * @param Field field The field object that is being validated.
     * 
     * @return The return value is a boolean value.
     */
    
    public function action(Field $field) : bool
    {
        $field_safemode = $field->getSafeMode();
        $field_readmode = $field->getReadMode();
        if (true === $field_readmode
            || $field_safemode === false) return true;

        $field_value = $field->getValue();
        return @preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $field_value);
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