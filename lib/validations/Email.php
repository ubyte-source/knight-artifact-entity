<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\validations\Regex;

/* If the field is read-only, or if the field is safe-mode, then the field is not validated */

class Email extends Regex
{
    const TYPE = ':string:regex:email';

    /**
     * If the field is in read mode, or if the field is not in safe mode, then return true. Otherwise,
     * return false
     * 
     * @param Field field The field object that is being validated.
     */
    
    public function after(Field $field) : bool
    {
        if (false === parent::after($field)) return false;

        $field_readmode = $field->getReadMode();
        $field_safemode = $field->getSafeMode();
        if (true === $field_readmode
            || $field_safemode !== true) return true;

        $field_value = $field->getValue();
        return filter_var($field_value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * If the field is in safe mode, then the field value is converted to lowercase
     * 
     * @param Field field The field object that is being validated.
     */
    
    public function magic(Field $field) : bool
    {
        $field_safemode = $field->getReadMode();
        if (true === $field_safemode
            || $this->getClosureMagicStatus() === false) return true;

        $field_value = $field->getValue();
        $field_value = strtolower($field_value);
        $field->setValue($field_value, Field::OVERRIDE);

        return parent::magic($field);
    }
}