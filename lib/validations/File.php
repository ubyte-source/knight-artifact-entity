<?PHP

namespace Entity\validations;

use stdClass;

use Knight\armor\CustomException;

use Entity\Field;
use Entity\field\warning\Handler;
use Entity\Validation;
use Entity\validations\interfaces\Human;

/* The File class is a validation class that validates the existence of a file and its mime type */

class File extends Validation implements Human
{
    const TYPE = ':file';

    const MIME = 'mime';
    const SIZE = 'size';

    protected $size_max;               // (int)
    protected $mime_valid = [];        // (array)
    protected $mime_translaction = []; // (array)

    /**
     * The constructor takes a default value as a parameter and sets it as the default value for the
     * property
     * 
     * @param default The default value for the parameter.
     */
    
    public function __construct(?string $default = null)
    {
        $default_parsed = $this->parseDefault($default);
        $this->setDefault($default_parsed);
    }

    /**
     * If the field is set to read mode, or if the field is set to safe mode and the file exists,
     * return true
     * 
     * @param Field field The field object that is being validated.
     */
    
    public function before(Field $field) : bool
    {
        $field_readmode = $field->getReadMode();
        $field_safemode = $field->getSafeMode();
        if (true === $field_readmode
            || $field_safemode === false) return true;

        return $this->checkFileExists($field)
            && $this->checkMimeValid($field);
    }

    /**
     * It returns true.
     * 
     * @param Field field The field that is being checked.
     * 
     * @return The return type is `bool` which means that the function will return `true` or `false`.
     */
    
    public function action(Field $field) : bool
    {
        return true;
    }

    /**
     * If the field is in read mode, or if the field is in safe mode and the file size is less than the
     * safe mode limit, return true. Otherwise, return false
     * 
     * @param Field field The field object that is being validated.
     * 
     * @return The return value is a boolean value. If the file size is greater than the max file size,
     * then the return value is false. Otherwise, the return value is true.
     */
    
    public function after(Field $field) : bool
    {
        $field_readmode = $field->getReadMode();
        $field_safemode = $field->getSafeMode();
        if (true === $field_readmode
            || $field_safemode === false) return true;

        return $this->checkFileSize($field);
    }

    /**
     * *This function adds a new mime type to the list of mime types that are valid for this file.*
     * 
     * *Note:* The mime types are case sensitive
     * 
     * @return The number of mime types added to the array.
     */
    
    public function setMimeValid(string ...$mime_valid) : int
    {
        return array_push($this->mime_valid, ...$mime_valid);
    }

    /**
     * Set the maximum size of the file
     * 
     * @param int size_max The maximum size of the file in bytes.
     * 
     * @return The object itself.
     */
    
    public function setMaxSize(int $size_max) : self
    {
        $this->size_max = $size_max;
        return $this;
    }

    /**
     * Set the mime translaction for a source to a destination
     * 
     * @param string source The source MIME type.
     * @param string destination The destination MIME type.
     * 
     * @return The object itself.
     */
    
    public function setMimeTranslaction(string $source, string $destination) : self
    {
        $this->mime_translaction[$source] = $destination;
        return $this;
    }

    /**
     * Returns the mime translaction array
     * 
     * @return An array of strings.
     */
    
    public function getMimeTranslaction() : array
    {
        return $this->mime_translaction;
    }

    /**
     * Returns a human readable object with the size and mime type of the file
     * 
     * @param namespace The namespace of the file.
     * @param bool protected If true, the uploaded file will be protected from being deleted.
     * 
     * @return A `stdClass` object.
     */
    
    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $human = new stdClass();
        $human->size = $this->getMaxSize();
        $human->mime = $this->getMimeValid();
        return $human;
    }

    /**
     * It returns the mime type of a file.
     * 
     * @param string file_path The path to the file you want to get the mime type of.
     * 
     * @return The MIME type of the file.
     */
    
    public static function getMime(string $file_path) : string
    {
        if (!file_exists($file_path)
            || is_dir($file_path)) throw new CustomException('developer/entity/validations/file/mime');

        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $file_mime = finfo_file($file_info, $file_path);
        finfo_close($file_info);

        return $file_mime;
    }

    /**
     * Returns the list of valid MIME types
     * 
     * @return An array of valid mime types.
     */
    
    protected function getMimeValid() : array
    {
        return $this->mime_valid;
    }

    /**
     * Get the maximum size of the file
     * 
     * @return The maximum size of the file.
     */
    
    protected function getMaxSize() :? int
    {
        return $this->size_max;
    }

    /**
     * If the file exists and is not a directory, return true. Otherwise, return false
     * 
     * @param Field field The field that is being validated.
     * 
     * @return Nothing.
     */
    
    protected function checkFileExists(Field $field) : bool
    {
        $field_value = $field->getValue();
        if (file_exists($field_value)
            && false === is_dir($field_value)) return true;

        $handler = new Handler($field, Field::REQUIRED);
        $field->getWarning()->addHandlers($handler);

        return false;
    }

    /**
     * If the mime type of the file is not in the mime valid array, then add a warning handler to the
     * field
     * 
     * @param Field field The field object that is being validated.
     * 
     * @return Nothing.
     */
    
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

    /**
     * If the file size is less than or equal to the maximum size, return true. Otherwise, return false
     * 
     * @param Field field The field object that is being validated.
     * 
     * @return Nothing.
     */
    
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

    /**
     * If the string is null, return null. If the string is not a file or a directory, throw an
     * exception. Otherwise, return the string
     * 
     * @param string The default value for the parameter.
     * 
     * @return The file path.
     */
    
    private function parseDefault(?string $string) :? string
    {
        if (null === $string) return null;
        if (!file_exists($string)
            || is_dir($string)) throw new CustomException('developer/entity/validations/file/default');

        return $string;
    }
}
