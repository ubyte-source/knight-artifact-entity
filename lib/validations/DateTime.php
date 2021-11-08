<?PHP

namespace Entity\validations;

use stdClass;
use DateTime as PHPDatetime;

use Knight\armor\CustomException;

use Entity\Field;
use Entity\Validation;

class DateTime extends Validation
{
    const TYPE = ':datetime';

    protected $format_from;        // (string)
    protected $format_conversion;  // (string)
    protected $expectations_type;  // (string)
    protected $expectations = [];  // (array)

    public function __construct(?PHPDatetime $default = null, string $from = null, string $conversion = null)
    {
        if (null === $from) throw new CustomException('developer/entity/validation/datetime/constructor');

        $this->setFormatFrom($from);
        $this->setFormatConversion($conversion ?? $from);

        $default_parsed = $this->parseDefault($default);
        $this->setDefault($default_parsed);
    }

    public function test() : bool
    {
        return $this->checkExpectations();
    }

    public function before(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        if (!is_string($field_value) && !is_numeric($field_value)) return false;
        if (0 === strlen((string)$field_value)) $field->setValue($this->getDefault(), Field::OVERRIDE);

        return true;
    }

    public function action(Field $field) : bool
    {
        $field_readmode = $field->getReadMode();
        $field_safemode = $field->getSafeMode();
        if (true === $field_readmode
            || $field_safemode === false) return true;

        $field_value = $field->getValue();
        if (null === $field_value
            || !is_string($field_value)) return true;

        return $this->checkExpectations($field_value);
    }

    public function after(Field $field) : bool
    {
        return true;
    }

    public function magic(Field $field) : bool
    {
        $readmode = $field->getReadMode();
        $format_from = $readmode ? $this->getFormatConversion() : $this->getFormatFrom();
        $format_conversion = $readmode ? $this->getFormatFrom() : $this->getFormatConversion();

        $field_value = $field->getValue();

        if ($format_from === $format_conversion
            || $field_value === $this->getDefault()) return true;

        $create_datetime = PHPDatetime::createFromFormat($format_from, $field_value);
        if (false === $create_datetime) return $readmode;

        $create_datetime_format = $create_datetime->format($format_conversion);
        if (is_numeric($create_datetime_format)) $create_datetime_format = (int)$create_datetime_format;

        $field->setValue($create_datetime_format, Field::OVERRIDE);

        return parent::magic($field);
    }

    public function setExpectations(string $expectations_type, string ...$expectations) : self
    {
        $this->setExpectationsType($expectations_type);
        if (!!$expectations) array_push($this->expectations, ...$expectations);
        return $this;
    }

    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $human = new stdClass();
        $human->from = $this->getFormatFrom();
        $human->conversion = $this->getFormatConversion();
        return (object)$human;
    }

    protected function getExpectations() : array
    {
        return $this->expectations;
    }

    protected function setExpectationsType(string $expectations_type) : void
    {
        $this->expectations_type = $expectations_type;
    }

    protected function getExpectationsType() : string
    {
        return $this->expectations_type;
    }

    protected function setFormatFrom(string $format_from) : void
    {
        $this->format_from = $format_from;
    }

    protected function getFormatFrom() : string
    {
        return $this->format_from;
    }

    protected function setFormatConversion(string $format_conversion) : void
    {
        $this->format_conversion = $format_conversion;
    }

    protected function getFormatConversion() : string
    {
        return $this->format_conversion;
    }

    protected function checkExpectations(string $value = null) : bool
    {
        $expectations = $this->getExpectations();
        if (empty($expectations)) return true;

        $expectations_type = $this->getExpectationsType();
        if ($expectations_type == 'interval') {
            if ($value === null) return count($expectations) === 2;

            $format_from = $this->getFormatFrom();
            $create_datetime = PHPDatetime::createFromFormat($format_from, $value);
            sort($expectations);
            return $create_datetime && $expectations[0] <= $value && $value <= $expectations[1];
        } else {
            $format_from = $this->getFormatFrom();
            foreach ($expectations as $item) {
                $create_datetime_format = $value === null ? $expectations_type : $format_from;
                $create_datetime = PHPDatetime::createFromFormat($create_datetime_format, $value ?? $item);
                if ($value === null && false === $create_datetime) throw new CustomException('developer/validation/' . $expectations_type . '/' . $item);
                if ($value === null
                    || $create_datetime && $create_datetime->format($expectations_type) == $item) return true;
            }
        }

        return false;
    }

    private function parseDefault(?PHPDatetime $default) :? string
    {
        if (null === $default) return null;
        $format_conversion = $this->getFormatConversion();
        $format_conversion_datetime = $default->format($format_conversion);
        return $format_conversion_datetim;
    }
}