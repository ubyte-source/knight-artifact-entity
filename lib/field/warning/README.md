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

#### ***Class Entity\field\warning\Handler usable methods***

##### `public function __construct(Field $field, string $text)`

This function is used to create a new instance of the class

 * **Parameters:**
   * `Field` — The field that this label is for.
   * `string` — The text to be translated.

##### `public function setName(string $name) : self`

* Set the name of the person

 * **Parameters:** `string` — The name of the parameter.
 * **Returns:** The object itself.

##### `public function addName(string ...$name) : self`

Add a name to the beginning of the name of the object

 * **Returns:** The object itself.

##### `public function getName() :? string`

If the name property is set, return it. Otherwise, return null

 * **Returns:** The name proprierty.

##### `public function setText(string $text) : self`

* Set the text of the message

 * **Parameters:** `string` — The text of the comment.
 * **Returns:** The object itself.

##### `public function getText() : string`

Get the text of the current node

 * **Returns:** The text of the question.

##### `public function setVariables(string ...$variables) : self`

Set the variables that will be used in the query

 * **Returns:** The object itself.

##### `public function getVariables() : array`

Returns an array of all the variables in the current scope

 * **Returns:** An array of variables.

##### `public function translate() : string`

This function will translate the text of the current message

 * **Returns:** The translated text.
 
## Built With

* [PHP](https://www.php.net/) - PHP

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details