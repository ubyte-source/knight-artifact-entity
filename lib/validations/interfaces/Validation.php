<?PHP

namespace Entity\validations\interfaces;

use Closure;

use Entity\Field;
use Entity\Validation as EntityValidation;

interface Validation
{
    /* This is the method that will be called when the validation is run. */

    public function runner(Field $field) : bool;

    /* This is the method that will be called before the validation is run. */

    public function before(Field $field) : bool;

    /* This is the method that will be called when the validation is run. */

    public function action(Field $field) : bool;

    /* This is a method that will be called after the validation is run. */
    
    public function after(Field $field) : bool;

    /* This is a method that will be called after the validation is run. */
    
    public function magic(Field $field) : bool;

    /* This is a setter method that will allow us to set the status of the closure magic. */
    
    public function setClosureMagicStatus(bool $status) : EntityValidation;

    /* This is a getter method that will allow us to get the status of the closure magic. */
    
    public function getClosureMagicStatus() : bool;

    /* This is a setter method that will allow us to set the status of the closure magic. */
    
    public function setClosureMagic(Closure $closure) : EntityValidation;

    /* This is a method that will allow us to get the closure that is set. */
    
    public function getClosureMagic() :? Closure;

    /* This is a getter method that will allow us to get the type of the validation. */
    
    public function getType() : string;

    /* This is a getter method that will allow us to get the default value of the validation. */
    
    public function getDefault();
}
