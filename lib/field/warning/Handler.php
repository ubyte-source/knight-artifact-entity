<?PHP

namespace Entity\field\warning;

use Knight\armor\Language;

use Entity\Field;

class Handler
{
    protected $text;            // (string)
    protected $name;            // (string)
    protected $variables = [];  // (array)

    public function __construct(Field $field, string $text)
    {
        $reflection = $field->getCore()->getReflection();
        Language::dictionary($reflection->getFileName());

        $namespaced = $reflection->getNamespaceName() . '\\' . $text;
        $this->setText($namespaced);
        $this->setName($field->getName());
    }

    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

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

    public function getName() :? string
    {
        return $this->name;
    }

    public function setText(string $text) : self
    {
        $this->text = $text;
        return $this;
    }

    public function getText() : string
    {
        return $this->text;
    }

    public function setVariables(string ...$variables) : self
    {
        $this->variables = $variables;
        return $this;
    }

    public function getVariables() : array
    {
        return $this->variables;
    }

    public function translate() : string
    {
        $translate = $this->getText();
        $variables = $this->getVariables();
        $translate = Language::translate($translate, ...$variables);
        return $translate;
    }
}