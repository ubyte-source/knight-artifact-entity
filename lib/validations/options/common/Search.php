<?PHP

namespace Entity\validations\options\common;

use stdClass;

use Entity\validations\interfaces\Human;

class Search implements Human
{
    protected $fields = []; // (array)
    protected $response;    // (string)
    protected $unique;      // (string)
    protected $label;       // (string)
    protected $url;         // (string)

    public function pushFields(string ...$fields) : int
    {
        return array_push($this->fields, ...$fields);
    }

    public function getFields() : array
    {
        return $this->fields;
    }

    public function setResponse(string $container) : self
    {
        $this->response = $container;
        return $this;
    }

    public function setLabel(string $label) : self
    {
        $this->label = $label;
        return $this;
    }

    public function setURL(string $url) : self
    {
        $this->url = $url;
        return $this;
    }

    public function setUnique(string $unique) : self
    {
        $this->unique = $unique;
        return $this;
    }

    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $variables = get_object_vars($this);
        $variables = array_filter($variables);
        return (object)$variables;
    }
}