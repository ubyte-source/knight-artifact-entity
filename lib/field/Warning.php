<?PHP

namespace Entity\field;

use Entity\field\warning\Handler;

class Warning
{
    protected $handlers = []; // (array) Handler

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

    public function addHandlers(Handler ...$handlers) : self
    {
        array_push($this->handlers, ...$handlers);
        return $this;
    }

    public function setHandlers(Handler ...$handlers) : self
    {
        $this->handlers = $handlers;
        return $this;
    }

    public function getHandlers() : array
    {
        return $this->handlers;
    }
}