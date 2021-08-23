<?PHP

namespace Entity\validations;

use stdClass;

use Entity\Map;
use Entity\Field;
use Entity\Validation;

use Entity\field\warning\Handler;

use Entity\validations\interfaces\Human;

final class Matrioska extends Validation implements Human
{
    const TYPE = ':matrioska';

    protected $multiple = false; // (bool)
    protected $babushka;         // Map

    public function __construct(Map $default)
    {
        $this->setBabushka($default);
        $this->setDefault($default);
    }

    public function before(Field $field) : bool
    {
        return true;
    }

    public function action(Field $field) : bool
    {
        $field_value = $field->getValue();
        if (!is_object($field_value) && !is_array($field_value)) return false;

        $value = array(&$field_value);
        if (false === $this->getMultiple()) $value = array($value);

        $value = &$value[0];

        $babushka = $this->getBabushka();
        $babushka_field = $babushka->getAllFieldsKeys();
        $babushka_field = array_fill_keys($babushka_field, null);

        $field_readmode = $field->getReadMode();
        $field_safemode = $field->getSafeMode();
        foreach ($value as $index => &$intra) if (false === ($intra instanceof Map)) {
            $check = array_diff_key((array)$intra, $babushka_field);
            if (false === empty($check)
                || 0 === count((array)$intra)) continue;

            if ($this->getMultiple()
                && false === is_int($index)) return false;

            $clone = clone $babushka;
            $clone->setSafeMode($field_safemode)->setReadMode($field_readmode);
            $intra = $clone->setFromAssociative((array)$intra, (array)$intra);
        }

        unset($intra);
        $field->setValue($field_value, Field::OVERRIDE);

        return true;
    }

    public function after(Field $field) : bool
    {
        $deals = true;
        if ($field->getSafeMode() && $field->getProtected()) return $deals;

        $field_value = $field->getValue();
        $field_value = array($field_value);
        array_walk_recursive($field_value, function ($item) use (&$deals) {
            if ($item instanceof Map) return;
            $deals = false;
        });
        return $deals;
    }

    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $default = $this->getBabushka();
        if (null === $default) return $default;
        $default = (object)$default->human($protected);
        $default->multiple = $this->getMultiple();
        return $default;
    }

    public function setMultiple(bool $multiple = true) : self
    {
        $default = $multiple ? array() : $this->getBabushka();
        $this->setDefault($default);
        $this->multiple = $multiple;
        return $this;
    }

    public function getMultiple() : bool
    {
        return $this->multiple;
    }

    public function getBabushka() : Map
    {
        return $this->babushka;
    }

    protected function setBabushka(Map $babushka) : void
    {
        $this->babushka = $babushka;
    }

}