<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\validations\ShowString;

class Regex extends ShowString
{
    const TYPE = ':string:regex';

    protected $regex;

    public function setRegex(string $regex) : self
    {
        $this->regex = $regex;
        return $this;
    }

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

    protected function getRegex() :? string
    {
        return $this->regex;
    }
}