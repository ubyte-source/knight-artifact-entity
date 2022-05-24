<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\validations\ShowString;

/* A regex validator */

class Regex extends ShowString
{
    const TYPE = ':string:regex';

    protected $regex;

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
     * If the field is in safe mode, then the regular expression is not used
     * 
     * @param Field field The field to validate.
     * 
     * @return The return value is a boolean value.
     */
    
    public function action(Field $field) : bool
    {
        $action = parent::action($field);
        if (false === $action) return false;

        $regex = $this->getRegex();
        $field_safemode = $field->getSafeMode();
        if (null === $regex
            || $field_safemode !== true) return true;

        $field_value = $field->getValue();
        return @preg_match($regex, $field_value)
            && preg_last_error() === PREG_NO_ERROR;
    }

    /**
     * Returns the regular expression used to parse the file
     * 
     * @return The regex property.
     */
    
    protected function getRegex() :? string
    {
        return $this->regex;
    }
}
