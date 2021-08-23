<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\Validation;

class Number extends Validation
{
    const TYPE = ':number';

    protected $min; // (float)
    protected $max; // (float)

    public function __construct(?float $default = null, ...$other)
    {
        $this->setDefault($default);
    }

    public function before(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        if (!is_string($field_value) && !is_numeric($field_value)) return false;
        if (0 === strlen((string)$field_value)) $field->setValue($this->getDefault(), Field::OVERRIDE);

        $field_value = $field->getValue();
        return is_numeric($field_value);
    }

    public function action(Field $field) : bool
    {
        $field_safemode = $field->getSafeMode();
        if ($field_safemode !== true
            || $field->isDefault()) return true;

        $field_value = $field->getValue();
        $field_value = (double)($field_value);
        $field->setValue($field_value, Field::OVERRIDE);
        return true;
    }

    public function after(Field $field) : bool
    {
        $field_safemode = $field->getSafeMode();
        if (false === $field_safemode) return true;

        $field_value = $field->getValue();

        $min = $this->getMin();
        $max = $this->getMax();
        return ($min === null || $min <= $field_value)
            && ($max === null || $field_value <= $max);
    }

    public function setMin(?string $min) : self
    {
        $this->min = $min;
        return $this;
    }

    public function setMax(?int $max) : self
    {
        $this->max = $max;
        return $this;
    }

    protected function getMin() :? float
    {
        return $this->min;
    }

    protected function getMax() :? float
    {
        return $this->max;
    }
}