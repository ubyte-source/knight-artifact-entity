<?PHP

namespace Entity\validations;

use stdClass;

use Knight\armor\Language;

use Entity\Map;
use Entity\Field;
use Entity\Validation;
use Entity\validations\options\Search;
use Entity\validations\interfaces\Human;

class Enum extends Search implements Human
{
    const TYPE = ':enum';

    protected $associative = []; // (array)

    public function __construct(?string $default = null)
    {
        parent::__construct();
        $this->setDefault($default);
    }

    public function before(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        if (!is_string($field_value) && !is_numeric($field_value)) return false;
        if (0 !== strlen((string)$field_value)) return true;

        $field->setValue($this->getDefault(), Field::OVERRIDE);
        return !$field->getRequired();
    }

    public function action(Field $field) : bool
    {
        $field_readmode = $field->getReadMode();
        $field_safemode = $field->getSafeMode();
        if (true === $field_readmode
            || $field_safemode !== true
            || $field->isDefault()) return true;

        $field_value = $field->getValue();
        $field->setValue($field_value = (string)$field_value, Field::OVERRIDE);
        $current_associative_array = $this->getAssociative();
        return empty($current_associative_array)
            || array_key_exists($field_value, $current_associative_array);
    }

    public function after(Field $field) : bool
    {
        return true;
    }

    public function addAssociative(string $key, array $value = null) : self
    {
        $this->associative[$key] = $value === null ? null : (object)$value;
        return $this;
    }

    public function getKeys() : array
    {
        return array_keys($this->associative);
    }

    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $human = new stdClass();
        $human->associative = $this->getAssociative();
        array_walk($human->associative, function (&$value, $key) use ($namespace) {
            if (false === is_object($value)) $value = new stdClass();
            if (false === property_exists($value, Map::FRONT)) $value->{Map::FRONT} = Language::translate($namespace . $key);
        });
        $search = $this->getSearch()->human($namespace, $protected);
        if (array_filter((array)$search)) $human->search = $search;

        return $human;
    }

    protected function getAssociative() : array
    {
        return $this->associative;
    }
}