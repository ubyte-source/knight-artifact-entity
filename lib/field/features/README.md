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

#### ***Class Entity\field\features\Action usable methods***

##### `public function getAllFieldsValues(bool $filter = false, bool $raw = true) : array`

Get all the fields values of the current object

 * **Parameters:**
   * `bool` — If true, only return fields that are not null.
   * `bool` — If true, the values will be returned as is. If false, the values will be

     converted to the appropriate type.
 * **Returns:** The values of the fields.

##### `public function getAllFieldsKeys(bool $filter = false) : array`

It returns an array of all the field names of the table

 * **Parameters:** `bool` — If true, only the fields that are not null will be returned.
 * **Returns:** An array of field names.

##### `public function getAllFieldsRequired() : array`

Get all the fields that are required

 * **Returns:** An array of fields that are required.

##### `public function getAllFieldsRequiredName() : array`

Get all the fields that are required

 * **Returns:** An array of field names.

##### `public function getAllFieldsProtected() : array`

Get all the fields that are protected

 * **Returns:** An array of fields that are protected.

##### `public function getAllFieldsProtectedName() : array`

Get all the protected fields in the class

 * **Returns:** An array of field names.

##### `public function getAllFieldsFile() : array`

Returns an array of all the fields that have a File validation

 * **Returns:** An array of fields that have a File validation.

##### `public function getAllFieldsFileName() : array`

Get all the field names from all the files

 * **Returns:** An array of field names.

##### `public function getAllFieldsMatrioska() : iterable`

Get all the fields that are of type Matrioska

##### `public function getAllFieldsMatrioskaName() : iterable`

Get all the fields in the matrioska

 * **Returns:** An array of field names.

##### `public function getAllFieldsBabuska() : iterable`

Get all the babushka's from all the patterns in all the fields in all the matrioska's

##### `public function getAllFieldsUniqueGroups() : array`

Get all the fields that are unique and return them grouped by the unique group name

 * **Returns:** An array of arrays. The first level is the unique group name. The second level is the
     field name.

##### `public function getAllFieldsWarning(int $flags = 0) : array`

Get all the warnings for all the fields in the map

 * **Parameters:** `int`
 * **Returns:** An array of warnings.

## Built With

* [PHP](https://www.php.net/) - PHP

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details