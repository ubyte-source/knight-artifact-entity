<?PHP

namespace Entity\validations;

use stdClass;

use Knight\armor\Language;

use Entity\Field;
use Entity\Validation;
use Entity\validations\options\Search;
use Entity\validations\interfaces\Human;

/* An enumeration is a search that is limited to a set of values */

class Enum extends Search implements Human
{
    const TYPE = ':enum';

    protected $associative = []; // (array)

    /**
     * The constructor for the PHP class
     * 
     * @param default The default value for the parameter.
     */
    
    public function __construct(?string $default = null)
    {
        parent::__construct();
        $this->setDefault($default);
    }

    /**
     * If the field is not required, and the field value is empty, set the field value to the default
     * value
     * 
     * @param Field field The field object that is being validated.
     * 
     * @return The return value is a boolean value. If the value is true, the field is not required. If
     * the value is false, the field is required.
     */
    
    public function before(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        if (!is_string($field_value) && !is_numeric($field_value)) return false;
        if (0 !== strlen((string)$field_value)) return true;

        $field->setDefault();
        $field_required = $field->getRequired();
        return false === $field_required;
    }

    /**
     * If the field is in read mode, or if the field is in safe mode and the field is not a default
     * value, return true
     * 
     * @param Field field The field to check.
     * 
     * @return The return value is a boolean value.
     */
    
    public function action(Field $field) : bool
    {
        $field_safemode = $field->getSafeMode();
        if ($field_safemode !== true
            || $field->isDefault()) return true;

        $field_value = $field->getValue();
        $field->setValue($field_value = (string)$field_value, Field::OVERRIDE);
        $current_associative_array = $this->getAssociative();
        return empty($current_associative_array)
            || array_key_exists($field_value, $current_associative_array);
    }

    /**
     * This function is called after the field has been processed
     * 
     * @param Field field The field that is being validated.
     * 
     * @return The return value is a boolean value. If the method returns true, the field is added to
     * the form. If the method returns false, the field is not added to the form.
     */
    
    public function after(Field $field) : bool
    {
        return true;
    }

    /**
     * Add an associative array to the object
     * 
     * @param string key The key of the associative array.
     * @param array value The value to be added to the array.
     * 
     * @return The object itself.
     */
    
    public function addAssociative(string $key, array $value = null) : self
    {
        $this->associative[$key] = $value === null ? null : (object)$value;
        return $this;
    }

    /**
     * Returns an array of the keys in the associative array
     * 
     * @return An array of the keys of the associative array.
     */
    
    public function getKeys() : array
    {
        return array_keys($this->associative);
    }

    /**
     * Returns an object with the same structure as the `associative` property, but with translated
     * text
     * 
     * @param namespace The namespace of the field.
     * @param bool protected If true, the field is not included in the human-readable output.
     * 
     * @return An object with two properties:
     */
    
    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $human = new stdClass();
        $human->associative = $this->getAssociative();
        array_walk($human->associative, function (&$value, $key) use ($namespace) {
            if (false === is_object($value)) $value = new stdClass();
            if (false === property_exists($value, Field::TEXT)) $value->{Field::TEXT} = Language::translate($namespace . $key);
        });
        $search = $this->getSearch()->human($namespace, $protected);
        if (array_filter((array)$search)) $human->search = $search;

        return $human;
    }

    /**
     * Returns the associative array of the current object
     * 
     * @return An array of associative arrays.
     */
    
    protected function getAssociative() : array
    {
        return $this->associative;
    }
}
