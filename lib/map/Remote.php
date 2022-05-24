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

    /**
     * The constructor for the PHP class
     * 
     * @param map The map that contains the parameters.
     */
    
    public function __construct(?Map $map, ...$parameters)
    {
        $this->setMap($map);
        $this->setParameter($parameters);
        $this->setData(new Data($this));
    }

    /**
     * Returns the map that is associated with this game
     * 
     * @return The map property.
     */
    
    public function getMap() :? Map
    {
        return $this->map;
    }

    /**
     * Get the data from the data source
     * 
     * @return The data property of the Data class.
     */
    
    public function getData() : Data
    {
        return $this->data;
    }

    /**
     * Returns an array of the parameters that were passed to the function
     * 
     * @return An array of parameters.
     */
    
    public function getParameters() : array
    {
        return $this->parameters;
    }

    /**
     * The setStructure method takes a Closure as a parameter and sets it as the structure property. 
     * 
     * The structure property is a Closure that defines the structure of the table. 
     * 
     * The structure property is used by the createTable method to create the table
     * 
     * @param Closure structure A closure that returns a structure.
     * 
     * @return The object itself.
     */
    
    public function setStructure(Closure $structure) : self
    {
        $this->structure = $structure;
        return $this;
    }

    /**
     * If the structure is a closure, call it and return the result. Otherwise, return null
     * 
     * @return The structure of the table.
     */
    
    public function getStructure() :? stdClass
    {
        if ($this->structure instanceof Closure) return $this->structure->call($this);
        return null;
    }

    /**
     * Get the name of the foreign key field
     * 
     * @return The name of the primary key field.
     */
    
    public function getForeign() : string
    {
        $structure = $this->getStructure();
        $structure_unique = $structure->{Map::UNIQUE};
        if (empty($structure_unique)
            || !property_exists($structure_unique, Field::PRIMARY)
            || 1 !== count($structure_unique->{Field::PRIMARY})) throw new CustomException('entity/remote/key/not/key/primary');

        return reset($structure_unique->{Field::PRIMARY});
    }

    /**
     * The setParameter function sets the parameters property to the given array
     * 
     * @param array parameters An array of parameters to pass to the stored procedure.
     */
    
    protected function setParameter(array $parameters) : void
    {
        $this->parameters = $parameters;
    }

    /**
     * Set the map property to the map parameter
     * 
     * @param map The map to be used for the game.
     */
    
    protected function setMap(?Map $map) : void
    {
        $this->map = $map;
    }

    /**
     * The setData function sets the data property to the data parameter
     * 
     * @param Data data The data to be used for the test.
     */
    
    protected function setData(Data $data) : void
    {
        $this->data = $data;
    }
}
