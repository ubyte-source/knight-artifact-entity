<?PHP

namespace Entity\validations;

use stdClass;

use Entity\Field;
use Entity\validations\File;
use Entity\validations\interfaces\Human;

/* This class is a subclass of the File class and it is used to store images */

class Image extends File implements Human
{
    const TYPE = ':file:image';

    protected $resize = [];

    /**
     * If the field is in read mode, or if the field is not in safe mode, then the field is not modified
     * 
     * @param Field field The field to resize.
     */

    public function action(Field $field) : bool
    {
        $action = parent::action($field);
        if (false === $action) return false;

        $field_readmode = $field->getReadMode();
        $field_safemode = $field->getSafeMode();
        if (true === $field_readmode
            || $field_safemode === false) return true;

        return $this->resize($field);
    }

    /**
     * If the field is an image, resize it to the given dimensions
     * 
     * @param Field field The field object that is being processed.
     * 
     * @return Nothing.
     */
    
    protected function resize(Field $field) : bool
    {
        $resize_array = $this->getResize();
        if (empty($resize_array)) return true;

        $field_value = $field->getValue();

        $mime = static::getMime($field_value);
        $mime = explode('/', $mime);
        $mime = reset($mime);
        if (false === file_exists($field_value)
            || $mime !== 'image') return false;

        $field_value_contents = file_get_contents($field_value);
        if (false === $field_value_contents) return false;

        $image = imagecreatefromstring($field_value_contents);
        if (false === $image) return false;

        list($x, $y) = $resize_array;
        list($image_x, $image_y) = getimagesize($field_value);
        $ratio = $x / $y <= $image_x / $image_y;
        $new_y = $ratio ? $y : $image_y / ($image_x / $x);
        $new_x = $ratio ? $image_x / ($image_y / $y) : $x;
        $thumbnail = imagecreatetruecolor($x, $y);
        $white = imagecolorallocate($thumbnail, 255, 255, 255);
        imagefill($thumbnail, 0, 0, $white);
        imagecopyresampled($thumbnail, $image, 0 - ($new_x - $x) / 2, 0 - ($new_y - $y) / 2, 0, 0, $new_x, $new_y, $image_x, $image_y);
        @unlink($field_value_contents);
        $field_value_contents = sys_get_temp_dir();
        $field_value_contents = tempnam($field_value_contents, 'image');
        if (false === imagejpeg($thumbnail, $field_value_contents, 72)) return false;

        imagedestroy($thumbnail);
        imagedestroy($image);
        $field->setValue($field_value_contents, Field::OVERRIDE);

        return true;
    }

    /**
     * Returns a human readable version of the object
     * 
     * @param namespace The namespace of the human readable name.
     * @param bool protected If true, the image is protected and cannot be deleted.
     * 
     * @return An object with the following properties:
     */
    
    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $human = parent::human($namespace, $protected);
        $human->crop = $this->getResize();
        return $human;
    }

    /**
     * *This function sets the resize array to the given x and y values.*
     * 
     * *This function is used to set the resize array to the given x and y values.*
     * 
     * @param int x The x-coordinate of the top-left corner of the rectangle.
     * @param int y The height of the image.
     * 
     * @return The number of elements in the resize array.
     */
    
    public function setResize(int $x, int $y) : int
    {
        return array_push($this->resize, $x, $y);
    }

    /**
     * Returns the resize array
     * 
     * @return An array of the resize values.
     */
    
    protected function getResize() : array
    {
        return $this->resize;
    }
}
