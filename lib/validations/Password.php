<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\validations\Regex;

/* This class is used to validate that a string is a valid password */

class Password extends Regex
{
    const TYPE = ':string:regex:password';
}
