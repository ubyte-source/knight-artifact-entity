<?PHP

namespace Entity\validations;

use stdClass;

use Knight\armor\Language;

use Entity\Field;
use Entity\validations\ShowArray;
use Entity\validations\interfaces\Human;

class Checkbox extends ShowArray implements Human
{
    const TYPE = ':array:checkbox';

    protected $options = []; // (array)

    /**
     * If the field value is not an array, or if the field value contains any values that are not in
     * the list of valid values, then return false
     * 
     * @param Field field The field object that is being validated.
     * 
     * @return bool A boolean value.
     */
   
    public function action(Field $field) : bool
    {
        if (true !== $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        $field_value_valid = $this->getKeys();
        if (true !== is_array($field_value)
            || (bool)array_diff($field_value, $field_value_valid)) return false;

        return true;
    }

    /**
     * This function adds an option to the array
     * 
     * @param string key The key of the option.
     * @param array value The value of the option.
     * 
     * @return self An instance of the class.
     */

    public function addOption(string $key, array $value = null) : self
    {
        $this->options[$key] = $value === null ? null : (object)$value;
        return $this;
    }
    
    /**
     * This function returns an array of all the keys in the options
     * 
     * @return array The keys of the options array.
     */

    public function getKeys() : array
    {
        return array_keys($this->options);
    }

    /**
     * This function returns a human readable version of the options
     * 
     * @param namespace The namespace of the language file.
     * @param bool protected If true, the field will be protected from being edited by the user.
     * 
     * @return stdClass An object with the options and their human readable names.
     */

    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $human = new stdClass();
        $human->options = $this->getOptions();
        array_walk($human->options, function (&$value, $key) use ($namespace) {
            if (false === is_object($value)) $value = new stdClass();
            if (false === property_exists($value, Field::TEXT)) $value->{Field::TEXT} = Language::translate($namespace . $key);
        });

        return $human;
    }

    /**
     * It returns the options array.
     * 
     * @return array The options array.
     */

    protected function getOptions() : array
    {
        return $this->options;
    }
}
