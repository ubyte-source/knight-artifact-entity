<?PHP

namespace Entity\field;

use stdClass;

class Row
{
    protected $name;       // (string)
    protected $priority;   // (int)

    /**
     * "Set the name property to the given value."
     * 
     * The first line is the function signature. It starts with the function keyword, followed by the
     * name of the function, followed by the function parameters in parentheses. The first parameter is
     * called , and it's a reference to the object that's calling the function. You can ignore it
     * 
     * @param string name The name of the parameter.
     * 
     * @return The object itself.
     */

    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * If the name property is set, return it. Otherwise, return null
     * 
     * @return The name proprerty.
     */
    
    public function getName() :? string
    {
        return $this->name;
    }

    /**
     * Set the priority of the task
     * 
     * @param int priority The priority of the job.
     * 
     * @return The object itself.
     */
    
    public function setPriority(int $priority) : self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get the priority of the current task
     * 
     * @return The priority property.
     */
    
    public function getPriority() :? int
    {
        return $this->priority;
    }

    /**
     * Returns an object with all the properties of the class
     * 
     * @return An object with the properties of the class.
     */
    
    public function human() : stdClass
    {
        $variables = get_object_vars($this);
        $variables = array_filter($variables);
        return (object)$variables;
    }
}