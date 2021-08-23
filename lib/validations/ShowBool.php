<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\Validation;

class ShowBool extends Validation
{
    const TYPE = ':bool';

    public function __construct(?bool $default = null)
    {
        $this->setDefault($default);
    }

    public function before(Field $field) : bool
    {
        return true;
    }

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

    public function after(Field $field) : bool
    {
        return true;
    }
}