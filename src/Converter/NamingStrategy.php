<?php

namespace Inside\SteamspyApi\Converter;

use InvalidArgumentException;

class NamingStrategy
{
    /**
     * @var int
     */
    protected $from = CaseConverter::UNKNOWN;

    /**
     * @var int
     * @throw Exception
     */
    protected $to = CaseConverter::UNKNOWN;
    
    public function __construct($from, $to)
    {
        if (!CaseConverter::isValidCase($from) || !CaseConverter::isValidCase($to)) {
            throw new InvalidArgumentException;
        }
        
        $this->from = $from;
        $this->to = $to;
    }
    
    public function getFrom()
    {
        return $this->from;
    }
    
    public function getTo()
    {
        return $this->to;
    }
}
