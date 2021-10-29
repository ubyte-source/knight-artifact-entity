<?PHP

namespace Entity\map\remote;

use Closure;

use Knight\armor\CustomException;

use Entity\map\Remote;

class Data
{
    const JOINLEFT = 0x1;

    protected $remote; // (Remote)
    protected $worker; // (Closure)
    protected $name;   // (string)
    protected $key;    // (string)

    public function __construct(Remote $remote)
    {
        $this->setRemote($remote);
    }

    public function getRemote() : Remote
    {
        return $this->remote;
    }

    public function setName(string $name) : self
    {
        $this->name = trim($name);
        return $this;
    }

    public function getName() :? string
    {
        return $this->name;
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

    public function get(array &$results, int $flags = 0, array $post = array()) : void
    {
        $key = $this->getKey();
        $worker = $this->getWorker();
        if (null === $key || null === $worker) return;

        $post_foreign = $this->getRemote()->getForeign();
        $post[$post_foreign] = array_column($results, $key);
        $post[$post_foreign] = array_unique($post[$post_foreign]);

        $remote = $worker->call($this, $post);
        if (false === is_array($remote)) throw new CustomException('entity/remote/not/response/array');

        $locale = $this->getName();
        $locale = false === is_string($locale) || 0 === strlen($locale)
            ? null
            : $locale;
        $remote_column = array_column($remote, $this->getRemote()->getForeign());
        $remote = array_combine($remote_column, $remote);
        array_walk($results, function (&$value) use ($key, $flags, $locale, $remote) {
            if (false === array_key_exists($value[$key], $remote)) {
                if ((bool)(static::JOINLEFT & $flags) === false) 
                    $value = null;
            } else {
                if (null === $locale) {
                    $value += (array)$remote[$value[$key]];
                } else {
                    $value[$locale] = (array)$remote[$value[$key]];
                }
            }
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