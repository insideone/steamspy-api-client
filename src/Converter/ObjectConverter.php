<?php

namespace Inside\SteamspyApi\Converter;

use Exception;

/**
 * Class ObjectConverter
 * Convert API response from array to object
 * @package Inside\SteamspyApi\Test
 */
class ObjectConverter
{
    /**
     * @var NamingStrategy
     */
    protected $ns;

    /**
     * @var CaseConverter
     */
    protected $converter;
    
    public function __construct(NamingStrategy $ns)
    {
        $this->ns = $ns;
        $this->converter = new CaseConverter($ns);
    }

    /**
     * Convert array to object. In strict mode throw exception when some data fields can't find in object
     * @param string $class
     * @param array $data
     * @param bool $strict
     * @return object
     * @throws Exception
     */
    public function getObject($class, $data, $strict = false)
    {
        if (!class_exists($class, true)) {
            throw new Exception("Unknown class: `{$class}`");
        }

        $object = new $class;
        
        foreach ($data as $field => $value) {
            $field = $this->converter->encode($this->ns, $field);
            if (method_exists($object, $method = 'set'.ucfirst($field))) {
                $object->{$method}($value);
            } elseif (property_exists($object, $field)) {
                $object->{$field} = $value;
            } elseif ($strict) {
                throw new Exception("Can't fill object field `{$field}`");
            }
        }
        
        return $object;
    }
}
