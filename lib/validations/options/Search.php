<?PHP

namespace Entity\validations\options;

use Entity\Validation;
use Entity\validations\options\common\Search as Common;

abstract class Search extends Validation
{
    protected $search; // Common

    public function __construct()
    {
        $this->setSearch(new Common());
    }

    public function getSearch() : Common
    {
        return $this->search;
    }

    protected function setSearch(Common $search) : void
    {
        $this->search = $search;
    }
}