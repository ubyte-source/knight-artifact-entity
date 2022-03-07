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

#### ***Class Entity\validations\options\common\Search usable methods***

##### `public function pushFields(string ...$fields) : int`

*This function pushes the fields passed into the function to the end of the fields array.*

The function is used to push the fields passed into the function to the end of the fields array

 * **Returns:** The number of fields that were added.

##### `public function getFields() : array`

Returns an array of the fields in the table

 * **Returns:** An array of field names.

##### `public function setResponse(string $container) : self`

This function sets the response variable to the container passed in

 * **Parameters:** `string` — The name of the container to use.

     <p>
 * **Returns:** The `setResponse` method returns the `self` object.

##### `public function setLabel(string $label) : self`

* Set the label of the current node

 * **Parameters:** `string` — The label of the field.

     <p>
 * **Returns:** The object itself.

##### `public function setURL(string $url) : self`

* Set the URL of the request

 * **Parameters:** `string` — The URL of the page to be scraped.

     <p>
 * **Returns:** The object itself.

##### `public function setUnique(string $unique) : self`

* Set the unique property of the class

 * **Parameters:** `string` — A unique identifier for the column.

     <p>
 * **Returns:** The object itself.

##### `public function human(?string $namespace = null, bool $protected = false) : stdClass`

Returns an object with all the properties of the class

 * **Parameters:**
   * `namespace` — namespace of the class.
   * `bool` — If true, the property will be protected.

     <p>
 * **Returns:** An object with the properties of the class.


## Built With

* [PHP](https://www.php.net/) - PHP

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details