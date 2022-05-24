<?PHP

namespace Entity\field\features;

use stdClass;

use Entity\Map;
use Entity\Field;
use Entity\Validation;

use Entity\validations\File;
use Entity\validations\Matrioska;

use Entity\field\warning\Handler;

/* A Warning is a class that has a name and a message */

class Warning
{
    public $name;    // (string)
    public $message; // (string)
}


/* It's a trait that allows you to use the `getAllFields` methods. */

trait Action
{
    /**
     * Get all the fields values of the current object
     * 
     * @param bool filter If true, only return fields that are not null.
     * @param bool raw If true, the values will be returned as is. If false, the values will be
     * converted to the appropriate type.
     * 
     * @return The values of the fields.
     */
    
    public function getAllFieldsValues(bool $filter = false, bool $raw = true) : array
    {
        $fields = $this->getFields($filter);
        if (empty($fields)) return array();

        $response = [];
        foreach ($fields as $field) {
            $field_name = $field->getName();
            $response[$field_name] = $field->getValue();
        }

        if ($raw) return $response;

        $arguments = func_get_args();
        array_walk_recursive($response, function (&$item) use ($arguments) {
            if ($item instanceof Map) $item = $item->getAllFieldsValues(...$arguments);
        });

        return $response;
    }

    /**
     * It returns an array of all the field names of the table
     * 
     * @param bool filter If true, only the fields that are not null will be returned.
     * 
     * @return An array of field names.
     */
    
    public function getAllFieldsKeys(bool $filter = false) : array /// da rinominare perchè mi da fastidio da quante volte sbaglio a scrivere
    {
        $fields = $this->getFields($filter);
        if (empty($fields)) return array();

        $response = array();
        foreach ($fields as $field) {
            $field_name = $field->getName();
            array_push($response, $field_name);
        }

        return $response;
    }

    /**
     * Get all the fields that are required
     * 
     * @return An array of fields that are required.
     */
    
    public function getAllFieldsRequired() : array
    {
        if (!$fields = $this->getFields()) return [];
        $required = array_filter($fields, function (Field $field) {
            return true === $field->getRequired();
        });
        return $required;
    }

    /**
     * Get all the fields that are required
     * 
     * @return An array of field names.
     */
    
    public function getAllFieldsRequiredName() : array
    {
        $required = $this->getAllFieldsRequired();
        $required = array_map(function (Field $field) {
            $name = $field->getName();
            return $name;
        }, $required);
    
        return $required;
    }

    /**
     * Get all the fields that are protected
     * 
     * @return An array of fields that are protected.
     */
    
    public function getAllFieldsProtected() : array
    {
        $fields = $this->getFields();
        if (empty($fields)) return array();

        $protected = array_filter($fields, function (Field $field) {
            return true === $field->getProtected();
        });

        return $protected;
    }

    /**
     * Get all the protected fields in the class
     * 
     * @return An array of field names.
     */
    
    public function getAllFieldsProtectedName() : array
    {
        $protected = $this->getAllFieldsProtected();
        $protected = array_map(function (Field $field) {
            $name = $field->getName();
            return $name;
        }, $protected);

        return $protected;
    }

    /**
     * Returns an array of all the fields that have a File validation
     * 
     * @return An array of fields that have a File validation.
     */
    
    public function getAllFieldsFile() : array
    {
        $fields = $this->getFields();
        if (empty($fields)) return array();

        $response = [];
        foreach ($fields as $field) {
            $patterns = $field->getPatterns();
            $patterns = array_filter($patterns, function (Validation $object) {
                return $object instanceof File;
            });
            if (false === empty($patterns)) array_push($response, $field);
        }
        return $response;
    }

    /**
     * Get all the field names from all the files
     * 
     * @return An array of field names.
     */
    
    public function getAllFieldsFileName() : array
    {
        $files = $this->getAllFieldsFile();
        $files = array_map(function (Field $field) {
            $name = $field->getName();
            return $name;
        }, $files);

        return $files;
    }

    /**
     * Get all the fields that are of type Matrioska
     */
    
    public function getAllFieldsMatrioska() : iterable
    {
        $fields = $this->getFields();
        foreach ($fields as $field)
            if (Matrioska::class === $field->getType(false))
                yield $field;
    }

    /**
     * Get all the fields in the matrioska
     * 
     * @return An array of field names.
     */
    
    public function getAllFieldsMatrioskaName() : iterable
    {
        $matrioska = $this->getAllFieldsMatrioska();
        $matrioska = iterator_to_array($matrioska);
        return array_map(function (Field $field) {
            return $field->getName();
        }, $matrioska);
    }

    /**
     * Get all the babushka's from all the patterns in all the fields in all the matrioska's
     */
    
    public function getAllFieldsBabuska() : iterable
    {
        $matrioska = $this->getAllFieldsMatrioska();
        foreach ($matrioska as $field)
            foreach ($field->getPatterns() as $pattern)
                yield $pattern->getBabushka();
    }

    /**
     * Get all the fields that are unique and return them grouped by the unique group name
     * 
     * @return An array of arrays. The first level is the unique group name. The second level is the
     * field name.
     */
    
    public function getAllFieldsUniqueGroups() : array
    {
        $fields = $this->getFields();
        if (empty($fields)) return array();

        $response = [];
        foreach ($fields as $name => $field) {
            $field_name = $field->getName();
            $field_unique = $field->getUniqueness();

            foreach ($field_unique as $group_name) {
                if (!array_key_exists($group_name, $response)) $response[$group_name] = [];
                array_push($response[$group_name], $field_name);
            }
        }
        return $response;
    }

    /**
     * Get all the warnings for all the fields in the map
     * 
     * @param int flags 
     * 
     * @return An array of warnings.
     */
    
    public function getAllFieldsWarning(int $flags = 0) : array
    {
        $fields = $this->getFields();
        $fields_response = [];

        foreach ($fields as $field) {
            $handlers = $field->getWarning(true)->getHandlers();
            if ((bool)($flags & Map::DISABLE_TRANSLATE_WARNING)) array_push($fields_response, ...$handlers);
            if ((bool)($flags & Map::DISABLE_TRANSLATE_WARNING)
                || empty($handlers)) continue;

            array_walk($handlers, function (Handler $handler) use ($field, &$fields_response) {
                $warning = new Warning();
                $warning->name = $handler->getName() ?? $field->getName();
                $warning->name = preg_replace('/^(\[(\w+)\])/', '$2', $warning->name);
                $warning->message = $handler->translate();
                array_push($fields_response, $warning);
            });
        }

        return $fields_response;
    }
}
