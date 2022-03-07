# Documentation knight-artifact-entity

Knight PHP library to map data as an ORM (Object Relational Mapping).

**NOTE:** This repository is part of [Knight](https://github.com/energia-source/knight). Any
support requests, bug reports, or development contributions should be directed to
that project.

## Structure

library:
- [Entity\field\features](https://github.com/energia-source/knight-artifact-entity/tree/main/lib/field/features)
- [Entity\field\warning](https://github.com/energia-source/knight-artifact-entity/tree/main/lib/field/warning)
- [Entity\field](https://github.com/energia-source/knight-artifact-entity/tree/main/lib/field)
- [Entity\map\remote](https://github.com/energia-source/knight-artifact-entity/tree/main/lib/map/remote)
- [Entity\map](https://github.com/energia-source/knight-artifact-entity/tree/main/lib/map)
- [Entity\validations\options\common](https://github.com/energia-source/knight-artifact-entity/tree/main/lib/options/common)
- [Entity\validations\options](https://github.com/energia-source/knight-artifact-entity/tree/main/lib/options)
- [Entity\validations](https://github.com/energia-source/knight-artifact-entity/tree/main/lib/validations)
- [Entity](https://github.com/energia-source/knight-knight-artifact-entity/blob/main/lib)

<br>

#### ***Class Entity\Adapter usable methods***

##### `public static function getAdapterNamespace() : string`

Returns the namespace of the adapter

 * **Returns:** The namespace of the adapter.

##### `public static function getAdapterDirectory() : string`

Returns the directory where the adapter files are stored

 * **Returns:** The value of the configuration directory.

#### ***Class Entity\Field usable methods***

##### `public static function json($string) : bool`

If the string is a valid JSON string, return true. Otherwise, return false

 * **Parameters:** `string` — string to check.
 * **Returns:** `A` — boolean value.

##### `public function __clone()`

Clone the object and all its properties

 * **Returns:** `Nothing.` 

##### `public function __construct(Entity $core, string $name, bool &$safemode, bool &$readmode)`

The constructor for the class

 * **Parameters:**
   * `Entity` — The core object that is used to access the database.
   * `string` — The name of the table.
   * `bool` — If true, the table will be read only.
   * `bool` — This is a boolean value that indicates whether the current user has read

     access to the table.

##### `public function setCore(Entity $core) : self`

The setCore function sets the core property of the class to the core parameter

 * **Parameters:** `Entity` — The core entity that is being used to create the new entity.
 * **Returns:** The object itself.

##### `public function getCore() : Entity`

Returns the core of the entity

 * **Returns:** The core entity.

##### `public function setPatterns(Validation ...$patterns) : self`

* Set the validation patterns for the field

 * **Returns:** The object itself.

##### `public function getWarning(bool $deep = false) : Warning`

If the field is a Matrioska field, and the field is not a default value, and the field is required, then return the warning

 * **Parameters:** `bool` — If true, the warning will be returned for all the fields in the entity.
 * **Returns:** The warning object.

##### `public function setTrigger(Closure $closure) : self`

The setTrigger method takes a Closure as a parameter and sets it as the trigger for the event

 * **Parameters:** `Closure` — A closure that will be called when the event is triggered.
 * **Returns:** The object itself.

##### `public function getTrigger() :? Closure`

Returns the trigger function for the event

 * **Returns:** The closure that is being returned is the closure that is being assigned to the trigger property.

##### `public function setRow(Row $row) : self`

The setRow function sets the row property to the value of the row parameter

 * **Parameters:** `Row` — The row that was just added to the table.
 * **Returns:** The object itself.

##### `public function getRow() : Row`

Returns the current row of the result set

 * **Returns:** The row object.

##### `public function setValue($value, int $flags = 0) : bool`

If the value is not set, set it to the default value. If the value is set, check it against the validation pattern. If it passes, return true. If it doesn't, return false

 * **Parameters:**
   * `value` — value to set.
   * `int` — 

##### `public function setDefault() : self`

This function sets the default value for the field

 * **Returns:** The object itself.

##### `public function getDefaults() : array`

Returns an array of default values for all the patterns in the validation

 * **Returns:** An array of default values.

##### `public function isDefault() : bool`

If the field value is an entity, return the entity's isDefault() value. Otherwise, return false

 * **Returns:** The `isDefault()` method returns a boolean value.

##### `public function getValue(bool $readable = false, int $flags = 0)`

Returns the value of the field

 * **Parameters:**
   * `bool` — If true, the value will be returned as a readable value.
   * `int`
 * **Returns:** The value of the field.

##### `public function setProtected(bool $protected = true) : self`

* Set the protected property to the value of the argument

 * **Parameters:** `bool` — Whether or not the property is protected.
 * **Returns:** `Nothing.` 

##### `public function getProtected() : bool`

"Get the value of the protected property."

The function name is `getProtected()`

 * **Returns:** The protected property of the class.

##### `public function setRequired(bool $required = true) : self`

Set the required flag to true or false

 * **Parameters:** `bool` — Whether or not the field is required.
 * **Returns:** `Nothing.` 

##### `public function getRequired() : bool`

Returns a boolean value indicating whether the field is required

 * **Returns:** The value of the `required` property.

##### `public function try(bool $deep = false) : bool`

If the field is required, and the field is not protected, and the field has a default value, then return true

 * **Parameters:** `bool` — If true, the checkRequired method will be called recursively on all child entities.
 * **Returns:** `Nothing.` 

##### `public function addUniqueness(string ...$groups_name) : self`

Add a group to the list of groups that this group is unique to

 * **Returns:** The object itself.

##### `public function getUniqueness() : array`

Returns the uniqueness of the column

 * **Returns:** An array of the unique columns.

##### `public function setUniqueness(string ...$uniqueness) : self`

Set the uniqueness of the column

 * **Returns:** The object itself.

##### `public function setName(string $name) : self`

* Set the name of the field

 * **Parameters:** `string` — The name of the field.
 * **Returns:** The object itself.

##### `public function getName() : string`

Get the name of the person

 * **Returns:** The name of the person.

##### `public function getPatterns() : array`

Returns an array of patterns that are used to match the file name

 * **Returns:** An array of strings.

##### `public function getType(bool $human = true) :? string`

Returns the type of the pattern

 * **Parameters:** `bool` — If true, the type will be returned in human-readable format.
 * **Returns:** The type of the first pattern.

##### `public function setSafeMode(bool &$safemode) : self`

The setSafeMode function sets the safemode property of the class to the value of the passed in parameter

 * **Parameters:** `bool` — If true, the database will be put into a safe mode before running the
     script.
 * **Returns:** The object itself.

##### `public function setSafeModeDetached(bool $safemode) : self`

Set the safemode flag to the value of the safemode parameter

 * **Parameters:** `bool` — If true, the database will be put into a safe mode before the backup is
     taken.
 * **Returns:** The object itself.

##### `public function getSafeMode() : bool`

Returns the value of the `safemode` property

 * **Returns:** The value of the safemode property.

##### `public function setReadMode(bool &$readmode) : self`

* Set the read mode of the connection

 * **Parameters:** `bool` — A reference to a boolean value that is set to true if the connection is in
     read mode.
 * **Returns:** The object itself.

##### `public function setReadModeDetached(bool $readmode) : self`

* Sets the read mode to detached

 * **Parameters:** `bool` — If true, the connection will be in read mode. If false, the connection will
     be in write mode.
 * **Returns:** The object itself.

##### `public function getReadMode() : bool`

Returns the read mode of the connection

 * **Returns:** The value of the readmode property.

##### `public function human(?string $namespace = null, bool $protected = false) : stdClass`

Returns an object with all the properties of the class, but with the properties of the parent class removed

 * **Parameters:**
   * `namespace` — namespace to use for the human readable object.
   * `bool` — If true, the object will be protected from being overwritten.
 * **Returns:** An object with the properties of the object, but with the values of the human() method.

<br>

#### ***Class Entity\Map usable methods***

##### `public static function factory(string $classname) : self`

Create an instance of a class

 * **Parameters:** `string` — The name of the class to instantiate.
 * **Returns:** `Nothing.` 

##### `public function __set(string $name, $value)`

* If an error occurred, throw an exception

 * **Parameters:**
   * `string` — The name of the property that is being set.
   * `value` — value to be set.

##### `public function __call(string $method, array $arguments)`

If the adapter is set, call the method on the adapter, otherwise throw an exception

 * **Parameters:**
   * `string` — The method name that was called.
   * `array` — The arguments passed to the method.
 * **Returns:** `Nothing.` 

##### `public function __construct()`

The constructor calls all the methods in the order they are defined in the static getConstructCallMethods() method

##### `public function __clone()`

Clone the current table and all its fields

##### `public function getReflection() : ReflectionClass`

Return a ReflectionClass object for the class

 * **Returns:** The ReflectionClass object.

##### `public function getHash() : string`

*Returns the hash of the object.*

 * **Returns:** The hash of the object.

##### `public function cloneHashEntity(Map $entity) : self`

Clone the hash of the entity

 * **Parameters:** `Map` — The entity to clone.
 * **Returns:** The object itself.

##### `public function useAdapter(string $adapter, string $namespace = null) : self`

* The function accepts a string that represents the name of the adapter to use. * If a namespace is provided, the function will attempt to use the adapter in the namespace. * If the adapter is not found in the namespace, the function will use the adapter in the default namespace. * The function will instantiate the adapter and set it as the current adapter. * The function returns the current instance of the class.

 * **Parameters:**
   * `string` — The name of the adapter to use.
   * `string` — The namespace of the adapter.
 * **Returns:** The instance of the adapter.

##### `public function unsetAdapter() : self`

Unset the adapter property

 * **Returns:** The object itself.

##### `public function hasAdapter() : bool`

"Returns true if the adapter is set."

The function name is `hasAdapter`. It returns a boolean value. The function has no parameters

 * **Returns:** `A` — boolean value.

##### `public function getAdapter() :? Adapter`

Returns the adapter for this connection

 * **Returns:** `Nothing.` 

##### `public function setSafeMode(bool $option) : self`

* Sets the safe mode option for the connection

 * **Parameters:** `bool` — The option to set.
 * **Returns:** The object itself.

##### `public function getSafeMode() : bool`

Returns the value of the `safe_mode` setting

 * **Returns:** The value of the safemode property.

##### `public function setReadMode(bool $option) : self`

* Set the read mode of the connection

 * **Parameters:** `bool` — The option to set.
 * **Returns:** The object itself.

##### `public function getReadMode() : bool`

Returns the read mode of the connection

 * **Returns:** The value of the readmode property.

##### `public function setRemotes(Remote ...$remotes) : self`

* Set the remotes for the repository

 * **Returns:** The object itself.

##### `public function addRemote(Remote $remote) : self`

Add a remote to the list of remotes

 * **Parameters:** `Remote` — The remote to add.
 * **Returns:** The object itself.

##### `public function getRemotes(string ...$contains) : array`

Get all the remotes that have the given fields

 * **Returns:** An array of Remote objects.

##### `public function setCollectionName(string $name) : self`

Set the name of the collection to be used for the current operation

 * **Parameters:** `string` — The name of the collection.
 * **Returns:** The object itself.

##### `public function getCollectionName() :? string`

Returns the name of the collection that the model is mapped to

 * **Returns:** The collection name.

##### `public function addField(string $name) : Field`

Add a field to the table

 * **Parameters:** `string` — The name of the field.
 * **Returns:** The new field.

##### `public function addFieldClone(Field $field) : Field`

Add a field to the core

 * **Parameters:** `Field` — The field to add.
 * **Returns:** The field that was added.

##### `public function removeAllFields() : self`

Remove all fields from the query

 * **Returns:** The object itself.

##### `public function getFields(bool $filter = false) : array`

Returns an array of all the fields in the table

 * **Parameters:** `bool` — If true, only return fields that are not default.
 * **Returns:** An array of fields that are not default.

##### `public function getField(string $name) : Field`

Get a field by name

 * **Parameters:** `string` — The name of the field.
 * **Returns:** The field object.

##### `public function removeField(string $name) : self`

RemoveField() removes a field from the list of fields

 * **Parameters:** `string` — The name of the field to remove.
 * **Returns:** The object itself.

##### `public function setFromAssociative(array $post, array $files = []) : self`

* This code is setting the values of the fields from the associative array. * Get all the fields that are files. * Get all the fields that are files and have a name. * For each field that is a file, set the value to the file. * Return the object

 * **Parameters:**
   * `array` — The  array.
   * `array` — An array of files that were uploaded.
 * **Returns:** The object itself.

##### `public function cloneAllFieldsFromEntity(Map $entity) : self`

* Clone all fields from an entity

 * **Parameters:** `Map` — The entity to clone from.
 * **Returns:** The current object.

##### `public function checkRequired(bool $deep = false) : self`

Check that all required fields are set

 * **Parameters:** `bool` — If true, the checkRequired method will check the required status of all the
     fields in the form.
 * **Returns:** The object itself.

##### `public function human(bool $protected = false) : stdClass`

Returns a human readable version of the current object

 * **Parameters:** `bool` — If true, only fields that are protected will be returned.
 * **Returns:** The human readable version of the structure.

##### `public static function translation(array $array) : array`

This function takes an array of objects and returns an array of objects with the same structure but with the text field translated

 * **Parameters:** `array` — The array to translate.
 * **Returns:** An array of translated strings.

##### `public function checkFieldExists(string $name) : bool`

Check if a field exists in the table

 * **Parameters:** `string` — The name of the field to check for.
 * **Returns:** An array of Field objects.

##### `public function reset() : self`

Reset all fields to their default values

 * **Returns:** The object itself.

##### `public function isDefault() : bool`

Returns true if all fields are default

 * **Returns:** The return value is a boolean value.

<br>

#### ***Class Entity\Validation usable methods***

##### `public static function factory(string $name, ...$parameters) : self`

The factory function is used to create an instance of a class

 * **Parameters:** `string` — The name of the class to be instantiated.
 * **Returns:** An instance of the class that was called.

##### `public function __clone()`

Clone the object and all of its properties

 * **Returns:** `Nothing.` 

##### `public function runner(Field $field) : bool`

For each operation, if the operation is defined, call it with the field

 * **Parameters:** `Field` — The field to be validated.
 * **Returns:** The return value is a boolean value.

##### `public function magic(Field $field) : bool`

If the closure magic is set, call it and return the result

 * **Parameters:** `Field` — The field that is being validated.
 * **Returns:** The return value of the closure.

##### `public function setClosureMagicStatus(bool $status) : self`

The `setClosureMagicStatus` method is a method that sets the `closure_magic_status` property to the value of the `$status` parameter

 * **Parameters:** `bool` — Whether or not the closure magic is enabled.
 * **Returns:** The object itself.

##### `public function getClosureMagicStatus() : bool`

Returns the status of the closure magic feature

 * **Returns:** The closure_magic_status property of the object.

##### `public function setClosureMagic(Closure $closure) : self`

*This function sets the closure magic variable to the closure passed in.*

The above function is a setter for the closure_magic variable. It takes in a closure and sets the closure_magic variable to the closure passed in

 * **Parameters:** `Closure` — The closure to be executed.
 * **Returns:** The object itself.

##### `public function getClosureMagic() :? Closure`

Returns the closure magic variable if it exists, otherwise returns null

 * **Returns:** The closure magic.

##### `public function getType(bool $human = true) : string`

Returns the type of the object

 * **Parameters:** `bool` — If true, the type will be returned in human-readable format. If false, the
     type will be returned in the format of the class name.
 * **Returns:** The class name.

##### `public function getDefault()`

Returns the default value for the column

 * **Returns:** The default value of the column.

## Built With

* [PHP](https://www.php.net/) - PHP

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details