<?PHP

namespace Entity\validations\options;

use Entity\Validation;
use Entity\validations\options\common\Search as Common;

/* The Search class is an abstract class that is used to search for data */

abstract class Search extends Validation
{
    protected $search; // Common

    /**
     * The constructor is called when the class is instantiated. 
     * 
     * The constructor is a special function that is called when a new instance of the class is
     * created. 
     * 
     * The constructor is used to initialize the class.  
     * 
     * The constructor is used to set the search property to an instance of the Common class. 
     * 
     * The constructor is used to set the search property
     */
    
    public function __construct()
    {
        $this->setSearch(new Common());
    }

    /**
     * Returns the search object
     * 
     * @return The search object.
     */
    
    public function getSearch() : Common
    {
        return $this->search;
    }

   /**
    * The setSearch function sets the search property of the class to the search parameter
    * 
    * @param Common search The search object that will be used to search for the data.
    */
    
    protected function setSearch(Common $search) : void
    {
        $this->search = $search;
    }
}
