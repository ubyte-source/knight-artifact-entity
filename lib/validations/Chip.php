<?PHP

namespace Entity\validations;

use stdClass;

use Entity\Field;
use Entity\validations\options\Search;
use Entity\validations\interfaces\Human;

class Chip extends Search implements Human
{
    const TYPE = ':array:chip';

    protected $regex;  // (string)

    public function __construct(?array $default = [])
    {
        parent::__construct();
        $this->setDefault($default);
    }

    public function before(Field $field) : bool
    {
        return true;
    }

    public function action(Field $field) : bool
    {
        return true;
    }

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

    public function getRegex() :? string
    {
        return $this->regex;
    }

    public function setRegex(string $regex) : self
    {
        $this->regex = $regex;
        return $this;
    }

    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $human = new stdClass();
        $human->regex = $this->getRegex();

        $search = $this->getSearch()->human($namespace, $protected);
        if (!!array_filter((array)$search)) $human->search = $search;
        return $human;
    }
}