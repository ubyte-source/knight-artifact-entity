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

#### ***Class Entity\map\remote\Data usable methods***

##### `public function __construct(Remote $remote)`

The constructor takes a Remote object and stores it in the property

 * **Parameters:** `Remote` — The remote to use for the request.

##### `public function getRemote() : Remote`

Get the remote object

 * **Returns:** The remote object.

##### `public function makeStructureName(string $name) : self`

This function takes a string and sets it as the structure name

 * **Parameters:** `string` — The name of the structure.
 
 * **Returns:** The object itself.

##### `public function getStructureName() :? string`

This function returns the name of the structure

 * **Returns:** The structure name.

##### `public function setKey(string $key) : self`

Set the key for the current instance of the class

 * **Parameters:** `string` — The key to use for the encryption.
 
 * **Returns:** The object itself.

##### `public function getKey() :? string`

Get the key of the current node

 * **Returns:** The key property.

##### `public function setWorker(Closure $worker) : self`

Set the worker closure

 * **Parameters:** `Closure` — A closure that will be called to process the job.
 
 * **Returns:** The object itself.

##### `public function get(array &$results, int $flags = 0, array $post = array()) : void`

Get the remote data and merge it with the local data

 * **Parameters:**
   * `array` — The results of the query.
   * `int` — The flags parameter is a bitmask that can be used to specify how the join is

     performed.
   * `array` — The post data to send to the remote.
   
 * **Returns:** An array of entities.

## Built With

* [PHP](https://www.php.net/) - PHP

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
