<?php

namespace hrustbb2\Validator;

use Respect\Validation\Rules;
use Respect\Validation\Validatable;
use Respect\Validation\Rules\AbstractRule;

abstract class AbstractNode {

    const ALPHA_NUM = 'alpha_num';
    const ALPHA = 'alpha';
    const ALWAYS_INVALID = 'always_invalid';
    const ALWAYS_VALID = 'always_valid';
    const ANY_OF = 'any_of';
    const ARRAY_TYPE = 'array_type';
    const ARRAY_VALUE = 'array_value';
    const BASE_64 = 'base_64';
    const BETWEEN = 'between';
    const BOOL_TYPE = 'bool_TYPE';
    const BOOL_VAL = 'bool_val';
    const CONTAINS = 'contains';
    const CONTAINS_ANY = 'contains_any';
    const COUNTRY_CODE = 'country_code';
    const CURRENCY_CODE = 'currency_code';
    const DATE = 'date';
    const DATE_TIME = 'date_time';
    const DECIMAL = 'decimal';
    const DIGIT = 'digit';
    const DOMAIN = 'domain';
    const EMAIL = 'email';
    const EQUALS = 'equals';
    const EQUIVALENT = 'equivalent';
    const EVEN = 'even';
    const FLOAT_TYPE = 'float_type';
    const FLOAT_VAL = 'float_val';
    const GREATER_THAN = 'greater_than';
    const HEX_RGB_COLOR = 'hex_rgb_color';
    const IN = 'IN';
    const INT_TYPE = 'INT_TYPE';
    const INT_VAL = 'INT_VAL';
    const IP = 'IP';
    const JSON = 'JSON';
    const LANGUAGE_CODE = 'LANGUAGE_CODE';
    const LENGTH = 'LENGTH';
    const LESS_THAN = 'LESS_THAN';
    const MAC_ADDRESS = 'MAC_ADDRESS';
    const NOT_BLANK = 'NOT_BLANK';
    const NOT_EMPTY = 'NOT_EMPTY';
    const NOT_EMOJI = 'NOT_EMOJI';
    const NULLABLE = 'NULLABLE';
    const NUMBER = 'NUMBER';
    const NUMERIC_VAL = 'NUMERIC_VAL';
    const REGEX = 'REGEX';
    const SLUG = 'SLUG';
    const STRING_TYPE = 'STRING_TYPE';
    const STRING_VAL = 'STRING_VAL';
    const TIME = 'TIME';
    const URL = 'URL';

    protected array $nestedNodes = [];

    protected array $errors = [];

    protected array $rules = [];

    protected $cleanData;

    public function parse(string $pathStr, array $rules, array $messages = []): void
    {
        $pathSegments = explode('.', $pathStr);
        $pathSegment = array_shift($pathSegments);
        if(count($pathSegments) == 0){
            $node = $this->createNode($pathSegment);
            $node->setRules($rules, $messages);
            return;
        }
        if(!($node = $this->nestedNodes[$pathSegment] ?? null)){
            $node = $this->createNode($pathSegment);
        }
        $node->parse(implode('.', $pathSegments), $rules, $messages);
    }

    protected function createNode(string $name)
    {
        if($name == '*'){
            $node = new NodesArray();
        }else{
            $node = new Node();
        }
        $this->nestedNodes[$name] = $node;
        return $node;
    }

    public function setRules(array $rules, array $messages): void
    {
        $result = [];
        $isNullable = in_array(self::NULLABLE, $rules);
        foreach($rules as $key=>$val){
            $ruleObj = null;
            if($val instanceof AbstractRule){
                $ruleObj = $val;
            }
            if(is_string($key)){
                $ruleObj = $this->createRule($key, $val);
                $message = $val['message'] ?? $messages[$key] ?? '';
            }
            if(!$ruleObj){
                $ruleObj = $this->createRule($val);
                $message = $messages[$val] ?? '';
            }
            if($isNullable){
                $ruleObj = new Rules\Nullable($ruleObj);
            }
            if($message){
                $ruleObj->setTemplate($message);
            }
            $result[] = $ruleObj;
        }
        $this->rules = $result;
    }

    protected function createRule(string $ruleStr, array $params = []): ?Validatable
    {
        if($ruleStr == self::ALPHA_NUM){
            $additional = $params['additional'] ?? [];
            return new Rules\Alnum($additional);
        }
        if($ruleStr == self::ALPHA){
            $additional = $params['additional'] ?? [];
            return new Rules\Alpha($additional);
        }
        if($ruleStr == self::ALWAYS_INVALID){
            return new Rules\AlwaysInvalid();
        }
        if($ruleStr == self::ALWAYS_VALID){
            return new Rules\AlwaysValid();
        }
        if($ruleStr == self::ARRAY_TYPE){
            return new Rules\ArrayType();
        }
        if($ruleStr == self::ARRAY_VALUE){
            return new Rules\ArrayVal();
        }
        if($ruleStr == self::BASE_64){
            return new Rules\Base64();
        }
        if($ruleStr == self::BETWEEN){
            $max = $params['max'];
            $min = $params['min'];
            return new Rules\Between($min, $max);
        }
        if($ruleStr == self::BOOL_TYPE){
            return new Rules\BoolType();
        }
        if($ruleStr == self::BOOL_VAL){
            return new Rules\BoolVal();
        }
        if($ruleStr == self::CONTAINS){
            $containsVal = $params['contains_val'];
            $identical = $params['identical'] ?? false;
            return new Rules\Contains($containsVal, $identical);
        }
        if($ruleStr == self::CONTAINS_ANY){
            $needles = $params['contains_val'];
            $identical = $params['identical'] ?? false;
            return new Rules\ContainsAny($needles, $identical);
        }
        if($ruleStr == self::COUNTRY_CODE){
            $set = $params['set'] ?? Rules\CountryCode::ALPHA2;
            return new Rules\CountryCode($set);
        }
        if($ruleStr == self::CURRENCY_CODE){
            return new Rules\CurrencyCode();
        }
        if($ruleStr == self::DATE){
            $format = $params['format'] ?? 'Y-m-d';
            return new Rules\Date($format);
        }
        if($ruleStr == self::DATE_TIME){
            $format = $params['format'] ?? null;
            return new Rules\DateTime($format);
        }
        if($ruleStr == self::DECIMAL){
            $decimals = $params['decimals'];
            return new Rules\Decimal($decimals);
        }
        if($ruleStr == self::DIGIT){
            $additionalChars = $params['additional_chars'] ?? [];
            return new Rules\Digit($additionalChars);
        }
        if($ruleStr == self::DOMAIN){
            $tldCheck = $params['tld_check'] ?? true;
            return new Rules\Domain($tldCheck);
        }
        if($ruleStr == self::EMAIL){
            return new Rules\Email();
        }
        if($ruleStr == self::EQUALS){
            $compareTo = $params['compare_to'];
            return new Rules\Equals($compareTo);
        }
        if($ruleStr == self::EQUIVALENT){
            $compareTo = $params['compare_to'];
            return new Rules\Equivalent($compareTo);
        }
        if($ruleStr == self::EVEN){
            return new Rules\Even();
        }
        if($ruleStr == self::FLOAT_TYPE){
            return new Rules\FloatType();
        }
        if($ruleStr == self::FLOAT_VAL){
            return new Rules\FloatVal();
        }
        if($ruleStr == self::GREATER_THAN){
            $maxValue = $params['max_value'];
            return new Rules\GreaterThan($maxValue);
        }
        if($ruleStr == self::HEX_RGB_COLOR){
            return new Rules\HexRgbColor();
        }
        if($ruleStr == self::IN){
            $haystack = $params['haystack'];
            $identical = $params['identical'] ?? false;
            return new Rules\In($haystack, $identical);
        }
        if($ruleStr == self::INT_TYPE){
            return new Rules\IntType();
        }
        if($ruleStr == self::INT_VAL){
            return new Rules\IntVal();
        }
        if($ruleStr == self::IP){
            $range = $params['range'] ?? '*';
            $options = $params['options'] ?? null;
            return new Rules\Ip($range, $options);
        }
        if($ruleStr == self::JSON){
            return new Rules\Json();
        }
        if($ruleStr == self::LANGUAGE_CODE){
            $set = $params['set'] ?? Rules\LanguageCode::ALPHA2;
            return new Rules\LanguageCode($set);
        }
        if($ruleStr == self::LENGTH){
            $min = $params['min'];
            $max = $params['max'];
            return new Rules\Length($min, $max);
        }
        if($ruleStr == self::LESS_THAN){
            $max = $params['max'];
            return new Rules\LessThan($max);
        }
        if($ruleStr == self::MAC_ADDRESS){
            return new Rules\MacAddress();
        }
        if($ruleStr == self::NOT_BLANK){
            return new Rules\NotBlank();
        }
        if($ruleStr == self::NOT_EMPTY){
            return new Rules\NotEmpty();
        }
        if($ruleStr == self::NOT_EMOJI){
            return new Rules\NotEmoji();
        }
        if($ruleStr == self::NUMBER){
            return new Rules\Number();
        }
        if($ruleStr == self::NUMERIC_VAL){
            return new Rules\NumericVal();
        }
        if($ruleStr == self::REGEX){
            $regex = $params['regex'];
            return new Rules\Regex($regex);
        }
        if($ruleStr == self::SLUG){
            return new Rules\Slug();
        }
        if($ruleStr == self::STRING_TYPE){
            return new Rules\StringType();
        }
        if($ruleStr == self::STRING_VAL){
            return new Rules\StringVal();
        }
        if($ruleStr == self::TIME){
            $format = $params['format'] ?? 'H:i:s';
            return new Rules\Time($format);
        }
        if($ruleStr == self::URL){
            return new Rules\Url();
        }
        return null;
    }

}