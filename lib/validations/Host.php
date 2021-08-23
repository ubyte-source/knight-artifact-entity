<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\Validation;

class Host extends Validation
{
    const TYPE = ':host';

    public function __construct(?string $default = null)
    {
        $this->setDefault($default);
    }

    public function before(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        if (!is_string($field_value) && !is_numeric($field_value)) return false;
        if (0 === strlen((string)$field_value)) $field->setValue($this->getDefault(), Field::OVERRIDE);

        return true;
    }

    public function action(Field $field) : bool
    {
        $field_safemode = $field->getSafeMode();
        $field_readmode = $field->getReadMode();
        if (true === $field_readmode
            || $field_safemode === false) return true;

        $field_value = $field->getValue();
        return @preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $field_value);
    }

    public function after(Field $field) : bool
    {
        return true;
    }
}