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

#### ***Class Entity\map\Remote usable methods***

## `public function __construct(?Map $map, ...$parameters)`

The constructor for the PHP class

 * **Parameters:** `map` — map that contains the parameters.

## `public function getMap() :? Map`

Returns the map that is associated with this game

 * **Returns:** The map property.

## `public function getData() : Data`

Get the data from the data source

 * **Returns:** The data property of the Data class.

## `public function getParameters() : array`

Returns an array of the parameters that were passed to the function

 * **Returns:** An array of parameters.

## `public function setStructure(Closure $structure) : self`

The setStructure method takes a Closure as a parameter and sets it as the structure property.

The structure property is a Closure that defines the structure of the table.

The structure property is used by the createTable method to create the table

 * **Parameters:** `Closure` — A closure that returns a structure.

 * **Returns:** The object itself.

## `public function getStructure() :? stdClass`

If the structure is a closure, call it and return the result. Otherwise, return null

 * **Returns:** The structure of the table.

## `public function getForeign() : string`

Get the name of the foreign key field

 * **Returns:** The name of the primary key field.
 
## Built With

* [PHP](https://www.php.net/) - PHP

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details