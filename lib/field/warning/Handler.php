<?PHP

namespace Entity\field\warning;

use Knight\armor\Language;

use Entity\Field;

class Handler
{
    protected $text;            // (string)
    protected $name;            // (string)
    protected $variables = [];  // (array)

    /**
     * This function is used to create a new instance of the class
     * 
     * @param Field field The field that this label is for.
     * @param string text The text to be translated.
     */
    
    public function __construct(Field $field, string $text)
    {
        $reflection = $field->getCore()->getReflection();
        Language::dictionary($reflection->getFileName());

        $namespaced = $reflection->getNamespaceName() . '\\' . $text;
        $this->setText($namespaced);
        $this->setName($field->getName());
    }

    /**
     * * Set the name of the person
     * 
     * @param string name The name of the parameter.
     * 
     * @return The object itself.
     */
    
    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Add a name to the beginning of the name of the object
     * 
     * @return The object itself.
     */
    
    public function addName(string ...$name) : self
    {
        $previous = is_string($this->name) ? preg_split('/\W/', $this->name) : [];
        $previous = array_filter($previous, 'strlen');
        array_unshift($previous, ...$name);

        $previous = preg_filter('/^.*/', chr(91) . '$0' . chr(93), $previous);
        $previous = implode($previous);

        $this->name = $previous;
        return $this;
    }

    /**
     * If the name property is set, return it. Otherwise, return null
     * 
     * @return The name proprierty.
     */
    
    public function getName() :? string
    {
        return $this->name;
    }

    /**
     * * Set the text of the message
     * 
     * @param string text The text of the comment.
     * 
     * @return The object itself.
     */
    
    public function setText(string $text) : self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get the text of the current node
     * 
     * @return The text of the question.
     */
    
    public function getText() : string
    {
        return $this->text;
    }

    /**
     * Set the variables that will be used in the query
     * 
     * @return The object itself.
     */
    
    public function setVariables(string ...$variables) : self
    {
        $this->variables = $variables;
        return $this;
    }

    /**
     * Returns an array of all the variables in the current scope
     * 
     * @return An array of variables.
     */

    public function getVariables() : array
    {
        return $this->variables;
    }

    /**
     * This function will translate the text of the current message
     * 
     * @return The translated text.
     */
    
    public function translate() : string
    {
        $translate = $this->getText();
        $variables = $this->getVariables();
        $translate = Language::translate($translate, ...$variables);
        return $translate;
    }
}
