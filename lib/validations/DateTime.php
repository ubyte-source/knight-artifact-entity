<?PHP

namespace Entity\validations;

use stdClass;
use DateTime as PHPDatetime;

use Knight\armor\CustomException;

use Entity\Field;
use Entity\Validation;

/* The DateTime class is a validation class that validates a date or datetime */

class DateTime extends Validation
{
    const TYPE = ':datetime';

    protected $format_from;        // (string)
    protected $format_conversion;  // (string)
    protected $expectations_type;  // (string)
    protected $expectations = [];  // (array)

    /**
     * * The constructor takes a default value and a format. 
     * * It sets the default value and the format. 
     * * It also sets the format conversion. 
     * * It parses the default value and sets it. 
     * 
     * The constructor is a bit long, but it's not too bad. 
     * 
     * The first thing we do is check if the default value is null. 
     * If it is, we throw an exception. 
     * 
     * Next, we set the format from. 
     * This is the format that the default value is in. 
     * 
     * Next, we set the format conversion. 
     * This is the format that the value will be converted to. 
     * 
     * Next, we parse the default value and set it.
     * 
     * @param default The default value to use if the value is not set.
     * @param string from The format of the date/time that is expected to be passed in.
     * @param string conversion The format to convert the date to.
     */
    
    public function __construct(?PHPDatetime $default = null, string $from = null, string $conversion = null)
    {
        if (null === $from) throw new CustomException('developer/entity/validation/datetime/constructor');

        $this->setFormatFrom($from);
        $this->setFormatConversion($conversion ?? $from);

        $default_parsed = $this->parseDefault($default);
        $this->setDefault($default_parsed);
    }

    /**
     * It checks the expectations of the test.
     * 
     * @return The return value is a boolean value.
     */

    public function test() : bool
    {
        return $this->checkExpectations();
    }

    /**
     * If the field is in safe mode, and the field value is empty, set the field value to the default
     * value
     * 
     * @param Field field The field object that is being validated.
     * 
     * @return The return value is a boolean value. If the value is true, the field is safe. If the
     * value is false, the field is not safe.
     */
    
    public function before(Field $field) : bool
    {
        if (false === $field->getSafeMode()) return true;

        $field_value = $field->getValue();
        if (!is_string($field_value) && !is_numeric($field_value)) return false;
        if (0 === strlen((string)$field_value)) $field->setValue($this->getDefault(), Field::OVERRIDE);

        return true;
    }

    /**
     * If the field is not in read mode, or if the field is in safe mode, then the field is not
     * vulnerable. Otherwise, if the field is in read mode and not in safe mode, then the field is
     * vulnerable
     * 
     * @param Field field The field to check.
     * 
     * @return The return value is a boolean value.
     */
    
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

    /**
     * This function is called after the field has been processed
     * 
     * @param Field field The field that is being validated.
     * 
     * @return The return value is a boolean value. If the method returns true, the field is added to
     * the form. If the method returns false, the field is not added to the form.
     */
    
    public function after(Field $field) : bool
    {
        return true;
    }

    /**
     * If the field's value is not the default value, and the format conversion is the same as the
     * format from, or the field's value is not numeric, then return true
     * 
     * @param Field field The field object that is being processed.
     */
    
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

    /**
     * * Sets the expectations type and expectations
     * 
     * @param string expectations_type The type of expectation.
     * 
     * @return The object itself.
     */
    
    public function setExpectations(string $expectations_type, string ...$expectations) : self
    {
        $this->setExpectationsType($expectations_type);
        if (!!$expectations) array_push($this->expectations, ...$expectations);
        return $this;
    }

    /**
     * Returns a human readable version of the format
     * 
     * @param namespace The namespace of the class to be converted.
     * @param bool protected If true, the property will be protected.
     * 
     * @return An object with two properties: from and conversion.
     */
    
    public function human(?string $namespace = null, bool $protected = false) : stdClass
    {
        $human = new stdClass();
        $human->from = $this->getFormatFrom();
        $human->conversion = $this->getFormatConversion();
        return (object)$human;
    }

    /**
     * Returns the expectations for the test
     * 
     * @return An array of expectations.
     */
    
    protected function getExpectations() : array
    {
        return $this->expectations;
    }

    /**
     * * Sets the expectations type for the test
     * 
     * @param string expectations_type The type of expectations that are being set.
     */
    
    protected function setExpectationsType(string $expectations_type) : void
    {
        $this->expectations_type = $expectations_type;
    }

    /**
     * The getExpectationsType function returns the expectations type
     * 
     * @return The type of expectation.
     */
    
    protected function getExpectationsType() : string
    {
        return $this->expectations_type;
    }

    /**
     * * Set the format from
     * 
     * @param string format_from The format of the data that is being read.
     */
    
    protected function setFormatFrom(string $format_from) : void
    {
        $this->format_from = $format_from;
    }

    /**
     * Returns the format from which the data is being read
     * 
     * @return The format_from property.
     */
    
    protected function getFormatFrom() : string
    {
        return $this->format_from;
    }

    /**
     * * Sets the format conversion for the current row
     * 
     * @param string format_conversion The format conversion to apply to the data.
     */
    
    protected function setFormatConversion(string $format_conversion) : void
    {
        $this->format_conversion = $format_conversion;
    }

    /**
     * Returns the format conversion for the current database
     * 
     * @return The format conversion.
     */
    
    protected function getFormatConversion() : string
    {
        return $this->format_conversion;
    }

    /**
     * If the value is null, then the function checks if the expectations array has two items. If the
     * value is not null, then the function checks if the value is in the expectations array
     * 
     * @param string value The value to check.
     * 
     * @return The return value is a boolean value.
     */
    
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

    /**
     * * If the default value is null, return null.
     * * Otherwise, convert the default value to the format specified by the format conversion.
     * * Return the converted default value
     * 
     * @param default The default value for the column.
     * 
     * @return The default value in the format that the database expects.
     */
        
    private function parseDefault(?PHPDatetime $default) :? string
    {
        if (null === $default) return null;
        $format_conversion = $this->getFormatConversion();
        $format_conversion_datetime = $default->format($format_conversion);
        return $format_conversion_datetime;
    }
}