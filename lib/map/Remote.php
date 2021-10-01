<?PHP

namespace Entity\map;

use Closure;
use stdClass;

use Knight\armor\CustomException;

use Entity\Map;
use Entity\map\remote\Data;
use Entity\Field;

class Remote
{
    protected $parameters; // (array)
    protected $structure;  // (Closure)
    protected $data;       // (Data)

    public function __construct(Map $map, ...$parameters)
    {
        $this->setMap($map);
        $this->setParameter($parameters);
        $this->setData(new Data($this));
    }

    public function getMap() : Map
    {
        return $this->map;
    }

    public function getData() : Data
    {
        return $this->data;
    }

    public function getParameters() : array
    {
        return $this->parameters;
    }

    public function setStructure(Closure $structure) : self
    {
        $this->structure = $structure;
        return $this;
    }

    public function getStructure() :? stdClass
    {
        if ($this->structure instanceof Closure) return $this->structure->call($this);
        return null;
    }

    public function getForeign() : string
    {
        $structure = $this->getStructure();
        $structure_unique = $structure->{Map::UNIQUE};
        if (empty($structure_unique)
            || !property_exists($structure_unique, Field::PRIMARY)
            || 1 !== count($structure_unique->{Field::PRIMARY})) throw new CustomException('entity/remote/key/not/key/primary');

        return reset($structure_unique->{Field::PRIMARY});
    }

    protected function setParameter(array $parameters) : void
    {
        $this->parameters = $parameters;
    }

    protected function setMap(Map $map) : void
    {
        $this->map = $map;
    }

    protected function setData(Data $data) : void
    {
        $this->data = $data;
    }
}