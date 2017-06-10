<?php

namespace Inside\SteamspyApi\Converter;

use Exception;
use ReflectionClass;
use Nayjest\StrCaseConverter\Str;

/**
 * Class CaseConverter
 * @package Inside\SteamspyApi\Converter
 */
class CaseConverter
{
    const UNKNOWN = 0;
    const SNAKE = 1;
    const CAMEL = 2;

    static protected $const;

    public static function getCases()
    {
        if (self::$const === null) {
            $class = new ReflectionClass(__CLASS__);
            self::$const = $class->getConstants();

            self::$const = array_combine(
                array_map('strtolower', array_keys(self::$const)),
                self::$const
            );
        }

        return self::$const;
    }

    public static function isValidCase($case, $byName = false)
    {
        $cases = self::getCases();
        return $byName ? isset($cases[$case]) : in_array($case, $cases);
    }
    
    public function getCaseName($case)
    {
        $cases = self::getCases();
        return array_search($case, $cases);
    }

    public function convert(NamingStrategy $ns, $value, $invert = false)
    {
        $method = [$this->getCaseName($ns->getFrom()), $this->getCaseName($ns->getTo())];
        if ($invert) {
            $method = array_reverse($method);
        }
        
        $method = "{$method[0]}To".ucfirst($method[1]);
        
        if (method_exists($this, $method)) {
            return $this->{$method}($value);
        }
        
        throw new Exception("Unknown method: `{$method}`");
    }

    public function decode(NamingStrategy $ns, $value)
    {
        return $this->convert($ns, $value, true);
    }

    public function encode(NamingStrategy $ns, $value)
    {
        return $this->convert($ns, $value, false);
    }

    public function snakeToCamel($snake)
    {
        return lcfirst(Str::toCamelCase($snake));
    }

    public function camelToSnake($camel)
    {
        return Str::toSnakeCase($camel);
    }
}
