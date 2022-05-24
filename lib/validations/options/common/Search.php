<?PHP

namespace Entity\validations\options\common;

use stdClass;

use Entity\validations\interfaces\Human;

/* The Search class is used to scrape data from a website */

class Search implements Human
{
    protected $fields = []; // (array)
    protected $response;    // (string)
    protected $unique;      // (string)
    protected $label;       // (string)
    protected $url;         // (string)

    /**
     * *This function pushes the fields passed into the function to the end of the fields array.*
     * 
     * The function is used to push the fields passed into the function to the end of the fields array
     * 
     * @return The number of fields that were added.
     */
    public function pushFields(string ...$fields) : int
    {
        return array_push($this->fields, ...$fields);
    }

    /**
     * Returns an array of the fields in the table
     * 
     * @return An array of field names.
     */
    
    public function getFields() : array
    {
        return $this->fields;
    }

    /**
     * This function sets the response variable to the container passed in
     * 
     * @param string container The name of the container to use.
     * 
     * @return The `setResponse` method returns the `self` object.
     */
    
    public function setResponse(string $container) : self
    {
        $this->response = $container;
        return $this;
    }

    /**
     * * Set the label of the current node
     * 
     * @param string label The label of the field.
     * 
     * @return The object itself.
     */
    
    public function setLabel(string $label) : self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * * Set the URL of the request
     * 
     * @param string url The URL of the page to be scraped.
     * 
     * @return The object itself.
     */
    
    public function setURL(string $url) : self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * * Set the unique property of the class
     * 
     * @param string unique A unique identifier for the column.
     * 
     * @return The object itself.
     */
    
    public function setUnique(string $unique) : self
    {
        $this->unique = $unique;
        return $this;
    }

    /**
     * Returns an object with all the properties of the class
     * 
     * @param namespace The namespace of the class.
     * @param bool protected If true, the property will be protected.
     * 
     * @return An object with the properties of the class.
     */
    
    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $variables = get_object_vars($this);
        $variables = array_filter($variables);
        return (object)$variables;
    }
}
