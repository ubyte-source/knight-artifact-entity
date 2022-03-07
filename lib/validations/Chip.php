<?PHP

namespace Entity\validations;

use stdClass;

use Entity\Field;
use Entity\validations\options\Search;
use Entity\validations\interfaces\Human;

/* The PHP class is used to validate that the field value is an array and that all of the values in the
array match the given regular expression */

class Chip extends Search implements Human
{
    const TYPE = ':array:chip';

    protected $regex;  // (string)

    /**
     * The constructor for the PHP class
     * 
     * @param default The default value for the parameter.
     */
    
    public function __construct(?array $default = [])
    {
        parent::__construct();
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
     * The action function is called when the field is being validated
     * 
     * @param Field field The field that is being checked.
     * 
     * @return The return type is `bool` which means that the function will return `true` or `false`.
     */
    
    public function action(Field $field) : bool
    {
        return true;
    }

    /**
     * If the field value is an array, filter out all values that do not match the regex
     * 
     * @param Field field The field to filter.
     * 
     * @return The `after` method returns a boolean value.
     */
    
    public function after(Field $field) : bool
    {
        $regex = $this->getRegex();
        if (null === $regex) return true;

        $field_value = $field->getValue();
        $field_value_filtered = array_filter($field_value, function ($item) use ($regex) {
            return (is_string($item) || is_numeric($item)) && preg_match($regex, (string)$item);
        });

        return count($field_value) === count($field_value_filtered);
    }

    /**
     * Returns the regular expression that was used to create this object
     * 
     * @return The regex property.
     */
    
    public function getRegex() :? string
    {
        return $this->regex;
    }

    /**
     * Set the regex property to the given regex
     * 
     * @param string regex The regular expression to match.
     * 
     * @return The object itself.
     */
    
    public function setRegex(string $regex) : self
    {
        $this->regex = $regex;
        return $this;
    }

    /**
     * Returns a human readable version of the current object
     * 
     * @param namespace The namespace of the class that the method is in.
     * @param bool protected If true, the search will be protected.
     * 
     * @return An object with two properties: `regex` and `search`.
     */
    
    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $human = new stdClass();
        $human->regex = $this->getRegex();

        $search = $this->getSearch()->human($namespace, $protected);
        if (!!array_filter((array)$search)) $human->search = $search;
        return $human;
    }
}