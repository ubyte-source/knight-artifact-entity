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

#### ***Class Entity\field\Row usable methods***

##### `public function setName(string $name) : self`

"Set the name property to the given value."

The first line is the function signature. It starts with the function keyword, followed by the name of the function, followed by the 

function parameters in parentheses. The first parameter is called , and it's a reference to the object that's calling the function. You can ignore it

 * **Parameters:** `string` — The name of the parameter.
 * **Returns:** The object itself.

##### `public function getName() :? string`

If the name property is set, return it. Otherwise, return null

 * **Returns:** The name proprerty.

##### `public function setPriority(int $priority) : self`

Set the priority of the task

 * **Parameters:** `int` — The priority of the job.
 * **Returns:** The object itself.

##### `public function getPriority() :? int`

Get the priority of the current task

 * **Returns:** The priority property.

##### `public function human() : stdClass`

Returns an object with all the properties of the class

 * **Returns:** An object with the properties of the class.

<br>

#### ***Class Entity\field\Warning usable methods***

##### `public function __clone()`

Clone the object and all of its properties

 * **Returns:** `Nothing.` 

##### `public function addHandlers(Handler ...$handlers) : self`

Add handlers to the event

 * **Returns:** The object itself.

##### `public function setHandlers(Handler ...$handlers) : self`

Set the handlers for the event

 * **Returns:** The object itself.

##### `public function getHandlers() : array`

Returns an array of all the handlers that have been registered with the logger

 * **Returns:** An array of the handlers.
 
## Built With

* [PHP](https://www.php.net/) - PHP

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
