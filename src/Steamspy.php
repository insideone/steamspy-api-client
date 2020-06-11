<?php

namespace Inside\SteamspyApi;

use Exception;
use Inside\SteamspyApi\Converter\CaseConverter;
use Inside\SteamspyApi\Converter\NamingStrategy;
use Inside\SteamspyApi\Converter\ObjectConverter;
use Inside\SteamspyApi\Enitity\App;

/**
 * Class Steamspy
 * @package Inside\SteamspyApi\Test
 * @method App[] top100in2weeks()
 * @method App[] top100forever()
 * @method App[] top100owned()
 * @method App[] all()
 */
class Steamspy
{
    /**
     * API service URL
     */
    const URL = 'https://steamspy.com/api.php';

    /**
     * Default poll rate to API
     */
    const DEFAULT_POLL_RATE_LIMIT = 4;

    /**
     * @var int Allowed poll rate hit/sec. Put zero to disable
     */
    protected $pollRateLimit = self::DEFAULT_POLL_RATE_LIMIT;

    /**
     * @var int Last curl request microtime(true)
     */
    protected $lastRequestTime = 0;

    /**
     * @var bool
     */
    protected $strict = true;

    /**
     * @var array Default curl options
     */
    protected $curlOpts = [
        CURLOPT_FAILONERROR => true,
        CURLOPT_CONNECTTIMEOUT => 1,
        CURLOPT_TIMEOUT => 1,
        CURLOPT_RETURNTRANSFER => true,
    ];

    /**
     * @var ObjectConverter
     */
    protected $converter;

    public function __construct($strict = true, ObjectConverter $converter = null)
    {
        $this->strict = (bool)$strict;

        if ($converter === null) {
            $converter = new ObjectConverter(new NamingStrategy(CaseConverter::SNAKE, CaseConverter::CAMEL));
        }
        $this->converter = $converter;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return App[]
     */
    public function __call($method, $arguments)
    {
        return $this->fetchApps($method);
    }

    /**
     * Return app details
     * @param $appid
     * @return App
     * @throws Exception
     */
    public function appdetails($appid)
    {
        return $this->prepareApp($this->request(__FUNCTION__, compact('appid')));
    }

    /**
     * Return all games by genre
     * @param string $genre
     * @return App[]
     */
    public function genre($genre)
    {
        return $this->fetchApps(__FUNCTION__, compact('genre'));
    }

    /**
     * @param string $method
     * @param array $data
     * @return App[]
     * @throws Exception
     */
    protected function fetchApps($method, $data = [])
    {
        $response = $this->request($method, $data);
        return array_map([$this, 'prepareApp'], $response);
    }

    /**
     * Convert api response (array) to App object
     * @param array $appData
     * @return App
     * @throws Exception
     */
    protected function prepareApp($appData)
    {
        return $this->converter->getObject('Inside\SteamspyApi\Enitity\App', $appData, $this->strict);
    }

    /**
     * API request
     * @param string $method
     * @param array $data
     * @return array
     * @throws Exception Wrong response
     */
    protected function request($method, $data)
    {
        if ($this->pollRateLimit) {
            $requiredPause = (1 / $this->pollRateLimit);
            $elapsedTime = microtime(true) - $this->lastRequestTime;
            if ($requiredPause > $elapsedTime) {
                usleep(ceil(1000000 * ($requiredPause - $elapsedTime)));
            }
        }
        
        $ch = curl_init($url = self::URL.'?'.http_build_query(['request' => $method] + $data));
        curl_setopt_array($ch, $this->curlOpts);

        $this->lastRequestTime = microtime(true);
        $json = curl_exec($ch);
        
        if (!$json) {
            throw new Exception("curl fail: ".curl_error($ch));
        }

        $response = json_decode($json, true);

        if (!$response) {
            throw new Exception("Wrong response: `{$json}`; request: {$url}");
        }

        return $response;
    }

    /**
     * Sets CURL request options
     * @param array $curlOpts
     * @return $this
     */
    public function setRequestOptions($curlOpts)
    {
        $this->curlOpts = array_merge($this->curlOpts, $curlOpts);
        return $this;
    }

    /**
     * Change poll rate limit
     * @param int $pollRateLimit
     * @return $this
     */
    public function setPollRateLimit($pollRateLimit)
    {
        $pollRateLimit = (int)$pollRateLimit;
        if ($pollRateLimit < 0) {
            $pollRateLimit = 0;
        }

        $this->pollRateLimit = $pollRateLimit;
        return $this;
    }

    /**
     * Change poll rate limit to default
     * @return $this
     */
    public function setDefaultPollRateLimit()
    {
        return $this->setPollRateLimit(self::DEFAULT_POLL_RATE_LIMIT);
    }

    /**
     * Disable poll rate limit
     * @return $this
     */
    public function disablePollRateLimit()
    {
        return $this->setPollRateLimit(0);
    }
}
