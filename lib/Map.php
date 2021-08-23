<?PHP

namespace Entity;

use stdClass;
use ReflectionClass;

use Knight\Configuration;
use Knight\armor\Language;
use Knight\armor\CustomException;

use Entity\Field;
use Entity\Adapter;
use Entity\Validation;
use Entity\validations\ShowArray;
use Entity\field\features\Action;

abstract class Map
{
    use Configuration, Action;

    const PRINT = 'PRINTABLE';
    const FRONT = 'text';

    const CONFIGURATION_COLLECTION = 0xa0cda;
    const CONFIGURATION_COLLECTION_DEFALULT = 'COLLECTION';

    const CONFIGURATION_CONSTRUCT_CALL_METHODS = 0xa0d3e;
    const CONFIGURATION_CONSTRUCT_CALL_METHODS_DEFAULT = [
        'before',
        'initialize',
        'after'
    ];

    // const COLLECTION = 'collection_name'

    protected $fields = [];      // (array)
    protected $remote;           // stdClass

    private $adapter;            // Adapter
    private $safemode = true;    // (bool)
    private $readmode = false;   // (bool)
    private $duplicable = false; // (bool)
    private $collection;         // (string)
    private $hash;               // (string)

    public static function factory(string $classname) : self
    {
        $instance = new $classname();
        if ($instance instanceof Map) return $instance;

        throw new CustomException('developer/factory');
    }

    public function __set(string $name, $value)
    {
        throw new CustomException('developer/create');
    }

    public function __call(string $method, array $arguments)
    {
        if (null !== $this->adapter && method_exists($this->adapter, $method)) return $this->adapter->$method($this, ...$arguments);

        throw new CustomException('developer/method');
    }

    public function __construct()
    {
        $order = static::getConstructCallMethods();
        foreach ($order as $method) {
            if (!method_exists($this, $method)) continue;

            call_user_func([$this, $method]);
        }
    }

    public function __clone()
    {
        $fields = $this->getFields();
        $this->removeAllFields();
        array_walk($fields, function (Field $field) {
            $this->addFieldClone($field);
        });
        $this->hash = null;
        $adapter = $this->getAdapter();
        if (null !== $adapter) $this->setAdapter(clone $adapter);
    }

    public function getReflection() : ReflectionClass
    {
        return new ReflectionClass($this);
    }

    public function getHash() : string
    {
        return null !== $this->hash ? $this->hash : spl_object_hash($this);
    }

    public function cloneHashEntity(Map $entity) : self
    {
        $this->hash = $entity->getHash();
        return $this;
    }

    public function useAdapter(string $adapter, string $namespace = null) : self
    {
        $instance_path = Adapter::getAdapterNamespace();
        $instance_name_directory = Adapter::getAdapterDirectory();
        $instance = $instance_path . '\\' . $instance_name_directory . '\\' . $adapter;

        if (null !== $namespace
            && class_exists($instance_internal = $namespace . '\\' . $instance_name_directory . '\\' . $adapter, true)) $instance = $instance_internal;

        $instance = new $instance($this);
        $this->setAdapter($instance);

        return $this;
    }

    public function unsetAdapter() : self
    {
        $this->adapter = null;
        return $this;
    }

    public function hasAdapter() : bool
    {
        $adapter = $this->getAdapter();
        return null !== $adapter;
    }

    public function getAdapter() :? Adapter
    {
        return $this->adapter;
    }

    public function setSafeMode(bool $option) : self
    {
        $this->safemode = $option;
        return $this;
    }

    public function getSafeMode() : bool
    {
        return $this->safemode;
    }

    public function setReadMode(bool $option) : self
    {
        $this->readmode = $option;
        return $this;
    }

    public function getReadMode() : bool
    {
        return $this->readmode;
    }

    public function getRemote() :? stdClass
    {
        return $this->remote;
    }

    public function setCollectionName(string $name) : self
    {
        $this->collection = $name;
        return $this;
    }

    public function getCollectionName() :? string
    {
        $reflection_constants = $this->getReflection();
        $reflection_constants = $reflection_constants->getConstants();
        return array_key_exists('COLLECTION', $reflection_constants) ? static::COLLECTION : $this->collection;
    }

    public function addField(string $name) : Field
    {
        if ($this->checkFieldExists($name)) throw new CustomException('developer/alredy_exixts');

        $field = new Field($this, $name, $this->safemode, $this->readmode);
        array_push($this->fields, $field);
        return $field;
    }

    public function addFieldClone(Field $field) : Field
    {
        $field_name = $field->getName();
        $field_filtered = $this->getFields();
        $field_filtered = array_filter($field_filtered, function (Field $field) use ($field_name) {
            return $field_name === $field->getName();
        });

        if (false !== reset($field_filtered)) $this->removeField($field_name);

        $field_clone = clone $field;
        $field_clone->setCore($this);
        $field_clone->setSafeMode($this->safemode);
        $field_clone->setReadMode($this->readmode);

        array_push($this->fields, $field_clone);

        return $field_clone;
    }

    public function removeAllFields() : self
    {
        $this->fields = array();
        return $this;
    }

    public function getFields(bool $filter = false) : array
    {
        $fields = $this->fields;
        if (empty($fields)
            || false === $filter) return $fields;

        return array_filter($fields, function (Field $field) {
            return false === $field->isDefault();
        });
    }

    public function getField(string $name) : Field
    {
        $fields_filtered = $this->getFields();
        $fields_filtered = array_filter($fields_filtered, function (Field $field) use ($name) {
            return $name === $field->getName();
        });
        $fields_filtered = reset($fields_filtered);
        if (false !== $fields_filtered) return $fields_filtered;

        throw new CustomException('developer/field_not_exists/' . $name);
    }

    public function removeField(string $name) : self
    {
        $this->fields = array_filter($this->fields, function (Field $field) use ($name) {
            return $name !== $field->getName();
        });
        return $this;
    }

    public function setFromAssociative(array $post, array $files = []) : self
    {
        $filter = ShowArray::filter($post);
        $fields = $this->getFields();
        $fields_files = $this->getAllFieldsFile();
        array_walk($fields_files, function (Field $field) use ($files) {
            $field_name = $field->getName();
            if (array_key_exists($field_name, $files))
                $this->getField($field_name)->setValue($files[$field_name]);
        });
        
        $fields_files_name = $this->getAllFieldsFileName();
        foreach ($fields as $field) {
            $field_name = $field->getName();
            if (in_array($field_name, $fields_files_name)
                || !array_key_exists($field_name, $filter)) continue;

            $this->getField($field_name)->setValue($filter[$field_name]);
        }

        return $this;
    }

    public function cloneAllFieldsFromEntity(Map $entity) : self
    {
        $read_mode = $this->getReadMode();
        $safe_mode = $this->getSafeMode();

        if ($safe_mode) $this->setSafeMode(false);
        if ($read_mode === false) $this->setReadMode(true);

        $this->setFromAssociative($entity->getAllFieldsValues(false, false));

        if ($read_mode === false) $this->setReadMode(false);
        if ($safe_mode) $this->setSafeMode(true);

        return $this;
    }

    public function checkRequired(bool $deep = false) : self
    {
        $fields = $this->getFields();
        array_walk($fields, function (Field $field) use ($deep) {
            $field->try($deep);
        });
        return $this;
    }

    public function human(bool $protected = false) : stdClass
    {
        $response = new stdClass();
        $constant = $this->getReflection()->getConstants();
        if (array_key_exists(static::PRINT, $constant)) foreach ($constant[static::PRINT] as $key => $value) $response->{$key} = $value;
        if (array_key_exists($collection = static::getCollectionConstant(), $constant)) $response->collection = $constant[$collection];

        $response->fields = [];

        $reflection = $this->getReflection();
        $reflection_filename = $reflection->getFileName();
        $reflection_filename_extension = pathinfo($reflection_filename, PATHINFO_EXTENSION);
        $reflaction_filename_specifics = basename($reflection_filename, '.' . $reflection_filename_extension);
        $reflaction_filename_specifics = strtolower($reflaction_filename_specifics);

        Language::dictionary($reflection_filename);

        $namespace = $reflection->getNamespaceName();
        $namespace = $namespace . '\\' . $reflaction_filename_specifics . '\\' . 'field' . '\\';

        $fields = $this->getFields();
        foreach ($fields as $field) {
            $field_protected = $field->getProtected();
            if (true === $field_protected
                && $protected === false) continue;

            $item = $field->human($namespace, $protected);
            if (false === property_exists($item, static::FRONT)) $item->{static::FRONT} = Language::translate($namespace . $item->name);
            array_push($response->fields, $item);
        }

        $remote = $this->getRemote();
        if (null !== $remote && is_array($remote->fields)) {
            $fields = array_values($remote->fields);
            array_push($response->fields, ...$fields);
        }

        return $response;
    }

    public function checkFieldExists(string $name) : bool
    {
        $fields_filtered = $this->getFields($name);
        $fields_filtered = array_filter($fields_filtered, function (Field $field) use ($name) {
            return $name === $field->getName();
        });
        return false === empty($fields_filtered);
    }

    public function reset() : self
    {
        $fields = $this->getFields();
        array_walk($fields, function (Field $field) {
            $field->setDefault();
        });
        return $this;
    }

    public function isDefault() : bool
    {
        $fields = $this->getFields();
        foreach ($fields as $field) {
            $field_status = $field->isDefault();
            if (false === $field_status) return false;
        }
        return true;
    }

    protected function setAdapter(Adapter $adapter) : void
    {
        $this->adapter = $adapter;
    }

    protected static function getConstructCallMethods() : array
    {
        return static::getConfiguration(static::CONFIGURATION_CONSTRUCT_CALL_METHODS) ?? static::CONFIGURATION_CONSTRUCT_CALL_METHODS_DEFAULT;
    }

    protected static function getCollectionConstant() : string
    {
        return static::getConfiguration(static::CONFIGURATION_COLLECTION) ?? static::CONFIGURATION_COLLECTION_DEFALULT;
    }
}