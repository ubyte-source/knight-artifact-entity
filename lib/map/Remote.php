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

    public function getStructure() :? stdClass
    {
        if (null === $this->structure) return null;
        return $this->callClosure($this->structure);
    }

    public function setData(Closure $data) : self
    {
        $this->data = $data;
        return $this;
    }

    public function getData(array &$results) :? stdClass
    {
        if (null === $this->data) return null;
        $requested = $this->callClosure($this->data, $results);
        return $requested;
    }

    public function getPrimaryKey() : array
    {
        $structure = $this->getStructure();
        $structure_unique = $structure->{Map::UNIQUE};
        if (empty($structure_unique)
            || !property_exists($structure_unique, Field::PRIMARY)) return array();

        return $structure_unique->{Field::PRIMARY};
    }

    protected function setParameter(array $parameter) : void
    {
        $this->parameter = $parameter;
    }

    protected function getParameter() : array
    {
        return $this->parameter;
    }

    protected function callClosure(Closure $closure, array &$results = null) :? stdClass
    {
        $parameters = $this->getParameter();
        if (null !== $results) $parameters[] = &$results;
        return call_user_func_array($closure, $parameters);
    }
}