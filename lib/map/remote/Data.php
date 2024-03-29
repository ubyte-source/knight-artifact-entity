<?PHP

namespace Entity\map\remote;

use Closure;

use Knight\armor\CustomException;

use Entity\map\Remote;

class Data
{
    const JOINLEFT = 0x1;

    protected $remote;    // (Remote)
    protected $worker;    // (Closure)
    protected $structure; // (string)
    protected $key;       // (string)

    /**
     * The constructor takes a Remote object and stores it in the  property
     * 
     * @param Remote remote The remote to use for the request.
     */
    
    public function __construct(Remote $remote)
    {
        $this->setRemote($remote);
    }

    /**
     * Get the remote object
     * 
     * @return The remote object.
     */
    
    public function getRemote() : Remote
    {
        return $this->remote;
    }

    /**
     * This function takes a string and sets it as the structure name
     * 
     * @param string name The name of the structure.
     * 
     * @return self The object itself.
     */

    public function makeStructureName(string $name) : self
    {
        $this->structure = trim($name);
        return $this;
    }
    
    /**
     * This function returns the name of the structure
     * 
     * @return ? string The structure name.
     */

    public function getStructureName() :? string
    {
        return $this->structure;
    }

    /**
     * Set the key for the current instance of the class
     * 
     * @param string key The key to use for the encryption.
     * 
     * @return The object itself.
     */
    
    public function setKey(string $key) : self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get the key of the current node
     * 
     * @return The key property.
     */
    
    public function getKey() :? string
    {
        return $this->key;
    }

    /**
     * Set the worker closure
     * 
     * @param Closure worker A closure that will be called to process the job.
     * 
     * @return The object itself.
     */
    
    public function setWorker(Closure $worker) : self
    {
        $this->worker = $worker;
        return $this;
    }

    /**
     * Get the remote data and merge it with the local data
     * 
     * @param array results The results of the query.
     * @param int flags The flags parameter is a bitmask that can be used to specify how the join is
     * performed.
     * @param array post The post data to send to the remote.
     * 
     * @return An array of entities.
     */
    
    public function get(array &$results, int $flags = 0, array $post = array()) : void
    {
        $key = $this->getKey();
        $worker = $this->getWorker();
        if (null === $key
            || $worker === null) return;

        $foreign = $this->getRemote()->getForeign();
        $foreign_search = empty($post);

        $post[$foreign] = array_column($results, $key);
        $post[$foreign] = array_unique($post[$foreign]);

        $remote = $worker->call($this, $post);
        if (false === is_array($remote))
            throw new CustomException('entity/remote/not/response/array');

        $structure = $this->getStructureName();
        $structure = false === is_string($structure) || 0 === strlen($structure)
            ? null
            : $structure;

        $remote_column = array_column($remote, $foreign);
        unset($remote[$foreign]);

        $remote = array_combine($remote_column, $remote);
        array_walk($results, function (&$value) use ($key, $flags, $structure, $remote, $foreign_search) {
            if (false === array_key_exists($value[$key], $remote)) {
                if (false === $foreign_search
                    || (bool)(static::JOINLEFT & $flags) === false) 
                        $value = null;
            } else {
                if (null === $structure) {
                    $value += (array)$remote[$value[$key]];
                } else {
                    $value[$structure] = (array)$remote[$value[$key]];
                }
            }
        });

        $results = array_filter($results);
    }

    /**
     * Set the remote object
     * 
     * @param Remote remote The remote to use for the request.
     */
    
    protected function setRemote(Remote $remote) : void
    {
        $this->remote = $remote;
    }

    /**
     * Returns the closure that will be used to process the job
     * 
     * @return The closure that is being returned is the closure that is being passed into the
     * constructor.
     */
    
    protected function getWorker() :? Closure
    {
        return $this->worker;
    }
}
