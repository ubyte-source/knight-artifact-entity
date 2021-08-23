<?PHP

namespace Entity\field;

use stdClass;

class Row
{
    protected $name;       // (string)
    protected $priority;   // (int)

    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    public function getName() :? string
    {
        return $this->name;
    }

    public function setPriority(int $priority) : self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getPriority() :? int
    {
        return $this->priority;
    }

    public function human() : stdClass
    {
        $variables = get_object_vars($this);
        $variables = array_filter($variables);
        return (object)$variables;
    }
}