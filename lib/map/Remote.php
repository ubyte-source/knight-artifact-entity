<?PHP

namespace Entity\map;

use Closure;
use stdClass;

use Entity\Map as Entity;

class Remote
{
    const FIELDS = 'fields';

    protected $map;      // (Map)
    protected $callable; // (Closure)

    public function __construct(Entity $map, Closure $callable)
    {
        $this->map = $map;
        $this->callable = $callable;
    }

    public function request() : stdClass
    {
        static $requested;
        if ($requested instanceof stdClass) return $requested;

        $requested = $this->getCallable();
        $requested = call_user_func($requested, $this);

        return $requested;
    }

    protected function getMap() : Entity
    {
        return $this->map;
    }

    protected function getCallable() : Closure
    {
        return $this->callable;
    }
}