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
use Entity\field\features\Action;
use Entity\map\Remote;

abstract class Map
{
    use Configuration, Action;

    const PRINT = 'PRINTABLE';

    const CONFIGURATION_COLLECTION = 0xa0cda;
    const CONFIGURATION_COLLECTION_DEFALULT = 'COLLECTION';

    const CONFIGURATION_CONSTRUCT_CALL_METHODS = 0xa0d3e;
    const CONFIGURATION_CONSTRUCT_CALL_METHODS_DEFAULT = [
        'before',
        'initialize',
        'after'
    ];

    const DISABLE_TRANSLATE_WARNING = 0x16;

    const FIELDS = 'fields';
    const UNIQUE = 'unique';

    // const COLLECTION = 'collection_name'

    protected $fields = [];      // (array) Field

    private $adapter;            // Adapter
    private $safemode = true;    // (bool)
    private $readmode = false;   // (bool)
    private $duplicable = false; // (bool)
    private $collection;         // (string)
    private $hash;               // (string)

    protected $remotes = [];     // (array) Remote

    /**
     * Create an instance of a class
     * 
     * @param string classname The name of the class to instantiate.
     * 
     * @return Nothing.
     */

    public static function factory(string $classname) : self
    {
        $instance = new $classname();
        if ($instance instanceof Map) return $instance;

        throw new CustomException('developer/factory');
    }

    /**
     * * If an error occurred, throw an exception
     * 
     * @param string name The name of the property that is being set.
     * @param value The value to be set.
     */
    
    public function __set(string $name, $value)
    {
        throw new CustomException('developer/create');
    }

    /**
     * If the adapter is set, call the method on the adapter, otherwise throw an exception
     * 
     * @param string method The method name that was called.
     * @param array arguments The arguments passed to the method.
     * 
     * @return Nothing.
     */
    
    public function __call(string $method, array $arguments)
    {
        if (null !== $this->adapter && method_exists($this->adapter, $method)) return $this->adapter->$method($this, ...$arguments);

        throw new CustomException('developer/method');
    }

    /**
     * The constructor calls all the methods in the order they are defined in the static
     * getConstructCallMethods() method
     */
    
    public function __construct()
    {
        $order = static::getConstructCallMethods();
        foreach ($order as $method) {
            if (!method_exists($this, $method)) continue;

            call_user_func([$this, $method]);
        }
    }

   /**
    * Clone the current table and all its fields
    */
    
    public function __clone()
    {
        $fields = $this->getFields();
        $this->removeAllFields();
        array_walk($fields, function (Field $field) {
            $clone = $this->addFieldClone($field);
            $field_uniqueness = $field->getUniqueness();
            $clone->setUniqueness(...$field_uniqueness);
        });
        $this->hash = null;
        $adapter = $this->getAdapter();
        if (null !== $adapter) $this->setAdapter(clone $adapter);
    }

    /**
     * Return a ReflectionClass object for the class
     * 
     * @return The ReflectionClass object.
     */
    
    public function getReflection() : ReflectionClass
    {
        return new ReflectionClass($this);
    }

    /**
     * *Returns the hash of the object.*
     * 
     * @return The hash of the object.
     */
    
    public function getHash() : string
    {
        return null !== $this->hash ? $this->hash : spl_object_hash($this);
    }

    /**
     * Clone the hash of the entity
     * 
     * @param Map entity The entity to clone.
     * 
     * @return The object itself.
     */
    
    public function cloneHashEntity(Map $entity) : self
    {
        $this->hash = $entity->getHash();
        return $this;
    }

    /**
     * * The function accepts a string that represents the name of the adapter to use. 
     * * If a namespace is provided, the function will attempt to use the adapter in the namespace. 
     * * If the adapter is not found in the namespace, the function will use the adapter in the default
     * namespace. 
     * * The function will instantiate the adapter and set it as the current adapter. 
     * * The function returns the current instance of the class.
     * 
     * @param string adapter The name of the adapter to use.
     * @param string namespace The namespace of the adapter.
     * 
     * @return The instance of the adapter.
     */
    
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

    /**
     * Unset the adapter property
     * 
     * @return The object itself.
     */
    
    public function unsetAdapter() : self
    {
        $this->adapter = null;
        return $this;
    }

    /**
     * "Returns true if the adapter is set."
     * 
     * The function name is `hasAdapter`. It returns a boolean value. The function has no parameters
     * 
     * @return A boolean value.
     */
    
    public function hasAdapter() : bool
    {
        $adapter = $this->getAdapter();
        return null !== $adapter;
    }

    /**
     * Returns the adapter for this connection
     * 
     * @return Nothing.
     */
    
    public function getAdapter() :? Adapter
    {
        return $this->adapter;
    }

    /**
     * * Sets the safe mode option for the connection
     * 
     * @param bool option The option to set.
     * 
     * @return The object itself.
     */
    
    public function setSafeMode(bool $option) : self
    {
        $this->safemode = $option;
        return $this;
    }

    /**
     * Returns the value of the `safe_mode` setting
     * 
     * @return The value of the safemode property.
     */
    
    public function getSafeMode() : bool
    {
        return $this->safemode;
    }

    /**
     * * Set the read mode of the connection
     * 
     * @param bool option The option to set.
     * 
     * @return The object itself.
     */
    
    public function setReadMode(bool $option) : self
    {
        $this->readmode = $option;
        return $this;
    }

    /**
     * Returns the read mode of the connection
     * 
     * @return The value of the readmode property.
     */
    
    public function getReadMode() : bool
    {
        return $this->readmode;
    }

    /**
     * * Set the remotes for the repository
     * 
     * @return The object itself.
     */
    
    public function setRemotes(Remote ...$remotes) : self
    {
        $this->remotes = $remotes;
        return $this;
    }

    /**
     * Add a remote to the list of remotes
     * 
     * @param Remote remote The remote to add.
     * 
     * @return The object itself.
     */
    
    public function addRemote(Remote $remote) : self
    {
        array_push($this->remotes, $remote);
        return $this;
    }

    /**
     * Get all the remotes that have the given fields
     * 
     * @return An array of Remote objects.
     */
    
    public function getRemotes(string ...$contains) : array
    {
        if (empty($contains)) return $this->remotes;
        return array_filter($this->remotes, function (Remote $remote) use ($contains) {
            $fields = $remote->getStructure();
            $fields = array_column($fields->{static::FIELDS}, Field::NAME);
            $fields = array_intersect($fields, $contains);
            return !!$fields;
        });
    }

    /**
     * Set the name of the collection to be used for the current operation
     * 
     * @param string name The name of the collection.
     * 
     * @return The object itself.
     */
    
    public function setCollectionName(string $name) : self
    {
        $this->collection = $name;
        return $this;
    }

    /**
     * Returns the name of the collection that the model is mapped to
     * 
     * @return The collection name.
     */
    
    public function getCollectionName() :? string
    {
        $reflection_constants = $this->getReflection();
        $reflection_constants = $reflection_constants->getConstants();
        return array_key_exists('COLLECTION', $reflection_constants) ? static::COLLECTION : $this->collection;
    }

    /**
     * Add a field to the table
     * 
     * @param string name The name of the field.
     * 
     * @return The new field.
     */
    
    public function addField(string $name) : Field
    {
        if ($this->checkFieldExists($name)) throw new CustomException('developer/alredy_exixts');

        $field = new Field($this, $name, $this->safemode, $this->readmode);
        array_push($this->fields, $field);
        return $field;
    }

    /**
     * Add a field to the core
     * 
     * @param Field field The field to add.
     * 
     * @return The field that was added.
     */
    
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
        $field_clone->setUniqueness();

        array_push($this->fields, $field_clone);

        return $field_clone;
    }

    /**
     * Remove all fields from the query
     * 
     * @return The object itself.
     */

    public function removeAllFields() : self
    {
        $this->fields = array();
        return $this;
    }

    /**
     * Returns an array of all the fields in the table
     * 
     * @param bool filter If true, only return fields that are not default.
     * 
     * @return An array of fields that are not default.
     */
    
    public function getFields(bool $filter = false) : array
    {
        $fields = $this->fields;
        if (empty($fields)
            || false === $filter) return $fields;

        return array_filter($fields, function (Field $field) {
            return false === $field->isDefault();
        });
    }

    /**
     * Get a field by name
     * 
     * @param string name The name of the field.
     * 
     * @return The field object.
     */
    
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

    /**
     * RemoveField() removes a field from the list of fields
     * 
     * @param string name The name of the field to remove.
     * 
     * @return The object itself.
     */
    
    public function removeField(string $name) : self
    {
        $this->fields = array_filter($this->fields, function (Field $field) use ($name) {
            return $name !== $field->getName();
        });
        return $this;
    }

    
    /**
    * * This code is setting the values of the fields from the associative array.
    * * Get all the fields that are files.
    * * Get all the fields that are files and have a name.
    * * For each field that is a file, set the value to the file.
    * * Return the object
    * 
    * @param array post The  array.
    * @param array files An array of files that were uploaded.
    * 
    * @return The object itself.
    */

    public function setFromAssociative(array $post, array $files = []) : self
    {
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
                || !array_key_exists($field_name, $post)) continue;

            $this->getField($field_name)->setValue($post[$field_name]);
        }

        return $this;
    }

    /**
     * * Clone all fields from an entity
     * 
     * @param Map entity The entity to clone from.
     * 
     * @return The current object.
     */
    
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

    /**
     * Check that all required fields are set
     * 
     * @param bool deep If true, the checkRequired method will check the required status of all the
     * fields in the form.
     * 
     * @return The object itself.
     */
    
    public function checkRequired(bool $deep = false) : self
    {
        $fields = $this->getFields();
        array_walk($fields, function (Field $field) use ($deep) {
            $field->try($deep);
        });
        return $this;
    }

   /**
    * Returns a human readable version of the current object
    * 
    * @param bool protected If true, only fields that are protected will be returned.
    * 
    * @return The human readable version of the structure.
    */

    public function human(bool $protected = false) : stdClass
    {
        $response = new stdClass();
        $constant = $this->getReflection()->getConstants();
        if (array_key_exists(static::PRINT, $constant)) foreach ($constant[static::PRINT] as $key => $value) $response->{$key} = $value;
        if (array_key_exists($collection = static::getCollectionConstant(), $constant)) $response->collection = $constant[$collection];

        $response->{static::FIELDS} = [];
        $response->{static::UNIQUE} = $this->getAllFieldsUniqueGroups();

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
            if (false === property_exists($item, Field::TEXT)) $item->{Field::TEXT} = Language::translate($namespace . $item->name);
            array_push($response->{static::FIELDS}, $item);
        }

        $remote = $this->getRemotes();
        $remotes_skip = $this->getAllFieldsKeys();
        foreach ($remote as $item) {
            $structure = $item->getStructure();
            if (null === $structure
                || false === property_exists($structure, static::FIELDS)
                || !is_array($structure->{static::FIELDS})) continue;

            $structure = array_filter((array)$structure->{static::FIELDS}, function (object $object) use ($remotes_skip) {
                return !in_array($object->{Field::NAME}, $remotes_skip);
            });
            if (false === empty($structure)) array_push($response->{static::FIELDS}, ...$structure);
        }

        return $response;
    }

    /**
     * This function takes an array of objects and returns an array of objects with the same structure
     * but with the text field translated
     * 
     * @param array array The array to translate.
     * 
     * @return An array of translated strings.
     */
    
    public static function translation(array $array) : array
    {
        $response = array();
        foreach ($array as $item)
            if (is_array($item) || $item instanceof stdClass)
                $response += static::translation((array)$item);

        if (!array_key_exists(Field::NAME, $array)
            || !array_key_exists(Field::TEXT, $array)) return $response;

        $response[$array[Field::NAME]] = $array[Field::TEXT];
        return $response;
    }

    /**
     * Check if a field exists in the table
     * 
     * @param string name The name of the field to check for.
     * 
     * @return An array of Field objects.
     */
    
    public function checkFieldExists(string $name) : bool
    {
        $fields_filtered = $this->getFields();
        $fields_filtered = array_filter($fields_filtered, function (Field $field) use ($name) {
            return $name === $field->getName();
        });
        return false === empty($fields_filtered);
    }

    /**
     * Reset all fields to their default values
     * 
     * @return The object itself.
     */
    
    public function reset() : self
    {
        $fields = $this->getFields();
        array_walk($fields, function (Field $field) {
            $field->setDefault();
        });
        return $this;
    }

    /**
     * Returns true if all fields are default
     * 
     * @return The return value is a boolean value.
     */
    
    public function isDefault() : bool
    {
        $fields = $this->getFields();
        foreach ($fields as $field) {
            $field_status = $field->isDefault();
            if (false === $field_status) return false;
        }
        return true;
    }

    /**
     * The setAdapter function is used to set the adapter property to the adapter object passed in
     * 
     * @param Adapter adapter The adapter that will be used to communicate with the database.
     */
    
    protected function setAdapter(Adapter $adapter) : void
    {
        $this->adapter = $adapter;
    }

    /**
     * Returns the list of methods that should be called when a new instance of the class is created
     * 
     * @return An array of method names that will be called when the object is constructed.
     */
    
    protected static function getConstructCallMethods() : array
    {
        return static::getConfiguration(static::CONFIGURATION_CONSTRUCT_CALL_METHODS) ?? static::CONFIGURATION_CONSTRUCT_CALL_METHODS_DEFAULT;
    }

    /**
     * Returns the collection constant for the current class
     * 
     * @return The collection constant.
     */
    
    protected static function getCollectionConstant() : string
    {
        return static::getConfiguration(static::CONFIGURATION_COLLECTION) ?? static::CONFIGURATION_COLLECTION_DEFALULT;
    }
}
