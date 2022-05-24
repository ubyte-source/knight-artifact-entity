<?PHP

namespace Entity\validations;

use Entity\validations\ShowString;

/* The `Hidden` class is a `ShowString` class that has a `TYPE` of `:string:hidden` */

class Hidden extends ShowString
{
    const TYPE = ':string:hidden';
}
