<?PHP

namespace Entity\map;

use Closure;
use stdClass;

use Entity\Map;
use Entity\Field;

class Remote
{
    protected $parameter; // (array)
    protected $structure; // (Closure)
    protected $data;      // (Closure)

    public function __construct(...$parameter)
    {
        $this->setParameter($parameter);
    }

    public function setStructure(Closure $structure) : self
    {
        $this->structure = $structure;
        return $this;
    }

    public function getStructure(...$parameters) :? stdClass
    {
        if (null === $this->structure) return null;
        $requested = $this->getParameter();
        $requested = $this->callClosure($this->structure, $requested);
        return $requested;
    }

    public function setData(Closure $data) : self
    {
        $this->data = $data;
        return $this;
    }

    public function getData(...$parameters) :? stdClass
    {
        if (null === $this->data) return null;
        $requested = $this->getParameter();
        $requested = $this->callClosure($this->data, $requested);
        return $requested;
    }

    public function getPrimaryKey() : array
    {
        $structure = $this->getStructure();
        $structure_unique = $structure->{Map::UNIQUE};
        if (empty($structure_unique)
            || !array_key_exists(Field::PRIMARY, $structure_unique)) return array();
        return $structure_unique[Field::PRIMARY];
    }

    protected function setParameter(array $parameter) : void
    {
        $this->parameter = $parameter;
    }

    protected function getParameter() : array
    {
        return $this->parameter;
    }

    protected function callClosure(Closure $closure, array $parameters) : stdClass
    {
        $requested = $this->getParameter();
        array_push($requested, ...$parameters);
        return call_user_func_array($closure, $requested);
    }
}