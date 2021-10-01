<?PHP

namespace Entity\map\remote;

use Closure;

use Entity\map\Remote;

class Data
{
    protected $remote; // (Remote)
    protected $worker; // (Closure)
    protected $key;    // (string)

    public function __construct(Remote $remote)
    {
        $this->setRemote($remote);
    }

    public function getRemote() : Remote
    {
        return $this->remote;
    }

    public function setKey(string $key) : self
    {
        $this->key = $key;
        return $this;
    }

    public function getKey() :? string
    {
        return $this->key;
    }

    public function setWorker(Closure $worker) : self
    {
        $this->worker = $worker;
        return $this;
    }

    public function get(array &$results) : void
    {
        $key = $this->getKey();
        $worker = $this->getWorker();
        if (null === $key || null === $worker) return;

        $post = array();
        $post_foreign = $this->getRemote()->getForeign();
        $post[$post_foreign] = array_column($results, $key);
        $post[$post_foreign] = array_unique($post[$post_foreign]);

        $remote = $worker->call($this, $post);
        if (false === is_array($remote)) throw new CustomException('entity/remote/not/response/array');

        $remote_column = array_column($remote, $this->getRemote()->getForeign());
        $remote = array_combine($remote_column, $remote);
        array_walk($results, function (&$value) use ($key, $remote) {
            if (array_key_exists($value[$key], $remote)) return $value += (array)$remote[$value[$key]];
            $value = null;
        });

        $results = array_filter($results);
    }

    protected function setRemote(Remote $remote) : void
    {
        $this->remote = $remote;
    }

    protected function getWorker() :? Closure
    {
        return $this->worker;
    }
}