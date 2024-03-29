<?PHP

namespace Entity\validations;

use stdClass;

use Knight\armor\CustomException;

use Entity\Map;
use Entity\Field;
use Entity\Validation;

use Entity\field\warning\Handler;

use Entity\validations\interfaces\Human;

/* The Matrioska class is a validation class that validates that the value of a field is an array of
objects that are a clone of a Map object */

final class Matrioska extends Validation implements Human
{
    const TYPE = ':matrioska';
    const DISABLE_PRIMARY_MANDATORY = 0x1;

    protected $multiple = false; // (bool)
    protected $babushka;         // Map

    /**
     * It finds the related matrioska of a given matrioska, based on the given fields
     * 
     * @param Map matrioska the matrioska object
     * @param int flags 
     * 
     * @return Map A Map object.
     */

    public static function findRelated(Map $matrioska, int $flags = 0, ...$fields) : Map
    {
        $related = $matrioska;
        if (count($fields) <= 1) return $related;

        while (1 !== count($fields)) {
            list($name) = array_splice($fields, 0, 1);

            $field = $related->getField($name);
            $field_patterns = $field->getPatterns();
            foreach ($field_patterns as $item) {
                $babushka = $item->getBabushka();
                $babushka_keys = $babushka->getAllFieldsKeys();
                if (!in_array($fields[0], $babushka_keys))
                    continue;

                $related = $babushka;
                break;
            }
        }

        if ((bool)($flags & static::DISABLE_PRIMARY_MANDATORY)) return $related;

        $related_keys = $related->getAllFieldsUniqueGroups();
        if (!array_key_exists(Field::PRIMARY, $related_keys)
            || !in_array(array_pop($fields), $related_keys[Field::PRIMARY]))
                throw new CustomException('developer/matrioska/use/only/primary/key');

        return $related;
    }

    /**
     * It sets the default values for the babushka and the default map.
     * 
     * @param Map default The default value for the parameter.
     */
    
    public function __construct(Map $default)
    {
        $this->setBabushka($default);
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
     * If the field value is an object or an array, then it will be converted to a Map
     * 
     * @param Field field The field to be checked.
     * 
     * @return Nothing.
     */
    
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
                && false === is_array($field_value)) return false;

            $clone = clone $babushka;
            $clone->setSafeMode($field_safemode)->setReadMode($field_readmode);
            $intra = $clone->setFromAssociative((array)$intra, (array)$intra);

            if (true === $field_readmode
                && $clone->isDefault())
                    $intra = null;
        }

        unset($intra);

        if ($this->getMultiple()) {
            $field_value = array_filter($field_value, function ($item) {
                return null !== $item;
            });
            $field_value = array_values($field_value);
        }

        $field->setValue($field_value, Field::OVERRIDE);

        return true;
    }

    /**
     * If the field is protected, then it's safe
     * 
     * @param Field field The field that is being checked.
     * 
     * @return The `after` method returns a boolean value.
     */
    
    public function after(Field $field) : bool
    {
        $deals = true;
        if ($field->getSafeMode()
            && $field->getProtected()) return $deals;

        $field_value = $field->getValue();
        $field_value = array($field_value);
        array_walk_recursive($field_value, function ($item) use (&$deals) {
            if (false === ($item instanceof Map)
                || $item->isDefault()) $deals = false;
        });

        return $deals;
    }

    /**
     * Returns a human readable version of the default value
     * 
     * @param namespace The namespace of the class to be returned.
     * @param bool protected If true, the humanized version will be protected.
     * 
     * @return An object with the following properties:
     */
    
    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $default = $this->getBabushka();
        if (null === $default) return $default;
        $default = (object)$default->human($protected);
        $default->multiple = $this->getMultiple();
        return $default;
    }

    /**
     * Set the default value for the field
     * 
     * @param bool multiple If true, the field will be rendered as a multi-select.
     * 
     * @return The object itself.
     */
    
    public function setMultiple(bool $multiple = true) : self
    {
        $default = $multiple ? array() : $this->getBabushka();
        $this->setDefault($default);
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * Returns a boolean value indicating whether the field is a multi-value field
     * 
     * @return The value of the `multiple` property.
     */
    
    public function getMultiple() : bool
    {
        return $this->multiple;
    }

    /**
     * Get the babushka map
     * 
     * @return The babushka map.
     */
    
    public function getBabushka() : Map
    {
        return $this->babushka;
    }

    /**
     * It sets the babushka property of the class to the babushka parameter.
     * 
     * @param Map babushka The map of babushka parameters.
     */
    
    protected function setBabushka(Map $babushka) : void
    {
        $this->babushka = $babushka;
    }
}
