<?PHP

namespace Entity\validations\interfaces;

use stdClass;

interface Human
{
    public function human(?string $namespace = null, bool $protected = false) : stdClass;
}