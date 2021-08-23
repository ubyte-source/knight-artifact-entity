<?PHP

namespace Entity\validations\interfaces;

use Closure;

use Entity\Field;
use Entity\Validation as EntityValidation;

interface Validation
{
    public function runner(Field $field) : bool;

    public function before(Field $field) : bool;

    public function action(Field $field) : bool;

    public function after(Field $field) : bool;

    public function magic(Field $field) : bool;

    public function setClosureMagicStatus(bool $status) : EntityValidation;

    public function getClosureMagicStatus() : bool;

    public function setClosureMagic(Closure $closure) : EntityValidation;

    public function getClosureMagic() :? Closure;

    public function getType() : string;

    public function getDefault();
}