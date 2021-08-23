<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\validations\Regex;

class Email extends Regex
{
    const TYPE = ':string:regex:email';

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