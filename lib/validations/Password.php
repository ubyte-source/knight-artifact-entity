<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\validations\Regex;

class Password extends Regex
{
    const TYPE = ':string:regex:password';
}