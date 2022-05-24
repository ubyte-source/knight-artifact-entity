<?PHP

namespace Entity\validations\interfaces;

use stdClass;

interface Human
{
    /* A method that returns a stdClass object. */
    
    public function human(?string $namespace = null, bool $protected = false) : stdClass;
}
