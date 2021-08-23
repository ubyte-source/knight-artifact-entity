<?PHP

namespace Entity\validations;

use stdClass;

use Entity\Field;
use Entity\Validation;

class ShowObject extends Validation
{
    const TYPE = ':object';

    public function __construct(?stdClass $default = null)
    {
        $this->setDefault($default);
    }

    public function before(Field $field) : bool
    {
        $field_value = $field->getValue();
        if (is_string($field_value)) {
            $field_value_converted = json_decode($field_value);
            $field->setValue($field_value_converted, Field::OVERRIDE);
        }
        return true;
    }

    public function action(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        return $field_value instanceof stdClass;
    }

    public function after(Field $field) : bool
    {
        return true;
    }
}