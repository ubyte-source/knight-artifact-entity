<?PHP

namespace Entity\field;

use Entity\field\warning\Handler;

/* The Warning class is a class that allows you to add handlers to the PHP warning system */
class Warning
{
    protected $handlers = []; // (array) Handler

    /**
     * Clone the object and all of its properties
     * 
     * @return Nothing.
     */
    
    public function __clone()
    {
        $variables = get_object_vars($this);
        $variables = array_keys($variables);
        $variables_glue = [];
        foreach ($variables as $name) array_push($variables_glue, array(&$this->$name));
        array_walk_recursive($variables_glue, function (&$item, $name) {
            if (false === is_object($item)) return;
            $item = clone $item;
        });
    }

    /**
     * Add handlers to the event
     * 
     * @return The object itself.
     */
    
    public function addHandlers(Handler ...$handlers) : self
    {
        array_push($this->handlers, ...$handlers);
        return $this;
    }

    /**
     * Set the handlers for the event
     * 
     * @return The object itself.
     */
    
    public function setHandlers(Handler ...$handlers) : self
    {
        $this->handlers = $handlers;
        return $this;
    }

    /**
     * Returns an array of all the handlers that have been registered with the logger
     * 
     * @return An array of the handlers.
     */
    
    public function getHandlers() : array
    {
        return $this->handlers;
    }
}