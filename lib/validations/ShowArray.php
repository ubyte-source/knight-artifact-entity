<?PHP

namespace Entity\validations;

use Entity\Field;
use Entity\Validation;

class ShowArray extends Validation
{
    const TYPE = ':array';

    public static function convert(array $value) : array
    {
        return json_decode(json_encode($value), JSON_OBJECT_AS_ARRAY);
    }

    public static function filter(array $array) : array
    {
        $callback = array(static::class, 'callback');
        $filtered = static::convert($array);
        return array_filter($filtered, $callback);
    }

    public function __construct(?array $default = [])
    {
        $this->setDefault($default);
    }

    public function before(Field $field) : bool
    {
        $field_value = $field->getValue();
        if (is_string($field_value)) {
            $field_value_converted = json_decode($field_value);
            $field->setValue($field_value_converted, Field::OVERRIDE);
        }
        return true;
    }

    public function action(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        return is_array($field_value);
    }

    public function after(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        $field_value = array_values($field_value);
        $field->setValue($field_value, Field::OVERRIDE);

        return true;
    }

    protected static function callback($item) : bool
    {
        return !is_null($item);
    }
}