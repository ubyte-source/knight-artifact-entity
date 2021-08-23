<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\Validation;

class ShowString extends Validation
{
    const TYPE = ':string';

    protected $min;
    protected $max;

    public function __construct(?string $default = null)
    {
        $this->setDefault($default);
    }

    public function before(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        if (!is_string($field_value) && !is_numeric($field_value)) return false;
        if (0 === strlen((string)$field_value)) {
            $field->setValue($this->getDefault(), Field::OVERRIDE);
            return true;
        }

        if (is_numeric($field_value)) $field->setValue($field_value = (string)$field_value, Field::OVERRIDE);
        if (false === is_string($field_value)) return false;

        $min = $this->getMin();
        $max = $this->getMax();
        return ($min === null || $min <= strlen($field_value))
            && ($max === null || strlen($field_value) <= $max);
    }

    public function action(Field $field) : bool
    {
        return true;
    }

    public function after(Field $field) : bool
    {
        return true;
    }

    public function setMin(int $min) : self
    {
        $this->min = $min;
        return $this;
    }

    public function setMax(int $max) : self
    {
        $this->max = $max;
        return $this;
    }

    protected function getMin() :? int
    {
        return $this->min;
    }

    protected function getMax() :? int
    {
        return $this->max;
    }
}