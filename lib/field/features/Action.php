<?PHP

namespace Entity\field\features;

use stdClass;

use Entity\Map;
use Entity\Field;
use Entity\Validation;

use Entity\validations\File;
use Entity\validations\Matrioska;

use Entity\field\warning\Handler;

class Warning
{
    public $name;    // (string)
    public $message; // (string)
}

trait Action
{
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

    public function getAllFieldsKeys(bool $filter = false) : array /// da rinominare perchè mi da fastidio da quante volte sbaglio a scrivere
    {
        $fields = $this->getFields($filter);
        if (empty($fields)) return array();

        $response = [];
        foreach ($fields as $field) {
            $field_name = $field->getName();
            array_push($response, $field_name);
        }
        return $response;
    }

    public function getAllFieldsRequired() : array
    {
        if (!$fields = $this->getFields()) return [];
        $required = array_filter($fields, function (Field $field) {
            return true === $field->getRequired();
        });
        return $required;
    }

    public function getAllFieldsRequiredName() : array
    {
        $required = $this->getAllFieldsRequired();
        $required = array_map(function (Field $field) {
            $name = $field->getName();
            return $name;
        }, $required);
    
        return $required;
    }

    public function getAllFieldsProtected() : array
    {
        $fields = $this->getFields();
        if (empty($fields)) return array();

        $protected = array_filter($fields, function (Field $field) {
            return true === $field->getProtected();
        });

        return $protected;
    }

    public function getAllFieldsProtectedName() : array
    {
        $protected = $this->getAllFieldsProtected();
        $protected = array_map(function (Field $field) {
            $name = $field->getName();
            return $name;
        }, $protected);

        return $protected;
    }

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

    public function getAllFieldsFileName() : array
    {
        $files = $this->getAllFieldsFile();
        $files = array_map(function (Field $field) {
            $name = $field->getName();
            return $name;
        }, $files);

        return $files;
    }

    public function getAllFieldsMatrioska() : iterable
    {
        $fields = $this->getFields();
        foreach ($fields as $field)
            if (Matrioska::class === $field->getType(false))
                yield $field;
    }

    public function getAllFieldsMatrioskaName() : iterable
    {
        $matrioska = $this->getAllFieldsMatrioska();
        $matrioska = iterator_to_array($matrioska);
        return array_map(function (Field $field) {
            return $field->getName();
        }, $matrioska);
    }

    public function getAllFieldsBabuska() : iterable
    {
        $matrioska = $this->getAllFieldsMatrioska();
        foreach ($matrioska as $field)
            foreach ($field->getPatterns() as $pattern)
                yield $pattern->getBabushka();
    }

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

    public function getAllFieldsWarning(?bool $translate = true) : array
    {
        $fields = $this->getFields();
        $fields_response = [];

        foreach ($fields as $field) {
            $handlers = $field->getWarning(true)->getHandlers();
            if (null === $translate) array_push($fields_response, ...$handlers);
            if (null === $translate
                || empty($handlers)) continue;

            array_walk($handlers, function (Handler $handler) use ($field, $translate, &$fields_response) {
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