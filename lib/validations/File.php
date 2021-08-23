<?PHP

namespace Entity\validations;

use stdClass;

use Knight\armor\CustomException;

use Entity\Field;
use Entity\field\warning\Handler;
use Entity\Validation;
use Entity\validations\interfaces\Human;

class File extends Validation implements Human
{
    const TYPE = ':file';

    const MIME = 'mime';
    const SIZE = 'size';

    protected $size_max;               // (int)
    protected $mime_valid = [];        // (array)
    protected $mime_translaction = []; // (array)

    public function __construct(?string $default = null)
    {
        $default_parsed = $this->parseDefault($default);
        $this->setDefault($default_parsed);
    }

    public function before(Field $field) : bool
    {
        $field_readmode = $field->getReadMode();
        $field_safemode = $field->getSafeMode();
        if (true === $field_readmode
            || $field_safemode === false) return true;

        return $this->checkFileExists($field) && $this->checkMimeValid($field);
    }

    public function action(Field $field) : bool
    {
        return true;
    }

    public function after(Field $field) : bool
    {
        $field_readmode = $field->getReadMode();
        $field_safemode = $field->getSafeMode();
        if (true === $field_readmode
            || $field_safemode === false) return true;

        return $this->checkFileSize($field);
    }

    public function setMimeValid(string ...$mime_valid) : int
    {
        return array_push($this->mime_valid, ...$mime_valid);
    }

    public function setMaxSize(int $size_max) : self
    {
        $this->size_max = $size_max;
        return $this;
    }

    public function setMimeTranslaction(string $source, string $destination) : self
    {
        $this->mime_translaction[$source] = $destination;
        return $this;
    }

    public function getMimeTranslaction() : array
    {
        return $this->mime_translaction;
    }

    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $human = new stdClass();
        $human->size = $this->getMaxSize();
        $human->mime = $this->getMimeValid();
        return $human;
    }

    public static function getMime(string $file_path) : string
    {
        if (!file_exists($file_path)
            || is_dir($file_path)) throw new CustomException('developer/entity/validations/file/mime');

        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $file_mime = finfo_file($file_info, $file_path);
        finfo_close($file_info);

        return $file_mime;
    }

    protected function getMimeValid() : array
    {
        return $this->mime_valid;
    }

    protected function getMaxSize() :? int
    {
        return $this->size_max;
    }

    protected function checkFileExists(Field $field) : bool
    {
        $field_value = $field->getValue();
        if (file_exists($field_value)
            && false === is_dir($field_value)) return true;

        $handler = new Handler($field, Field::REQUIRED);
        $field->getWarning()->addHandlers($handler);

        return false;
    }

    protected function checkMimeValid(Field $field) : bool
    {
        $mime_valid = $this->getMimeValid();
        if (empty($mime_valid)) return true;

        $field_value = $field->getValue();
        $field_value_mime = static::getMime($field_value);
        $field_value_mime_translaction = $this->getMimeTranslaction();

        if (array_key_exists($field_value_mime, $field_value_mime_translaction)) $field_value_mime = $field_value_mime_translaction[$field_value_mime];
        if (in_array($field_value_mime, $mime_valid)) return true;

        $handler = new Handler($field, static::MIME);
        $field->getWarning()->addHandlers($handler);

        return false;
    }

    protected function checkFileSize(Field $field) : bool
    {
        $size_max = $this->getMaxSize();
        if (null === $size_max) return true;

        $field_value = $field->getValue();
        if (file_exists($field_value) && filesize($field_value) <= $size_max) return true;

        $handler = new Handler($field, static::SIZE);
        $field->getWarning()->addHandlers($handler);

        return false;
    }

    private function parseDefault(?string $string) :? string
    {
        if (null === $string) return null;
        if (!file_exists($string)
            || is_dir($string)) throw new CustomException('developer/entity/validations/file/default');

        return $string;
    }
}