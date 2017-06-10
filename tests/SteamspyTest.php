<?php

namespace Inside\SteamspyApi\Test;

use Inside\SteamspyApi\Steamspy;

class SteamspyTest extends \PHPUnit_Framework_TestCase
{
    const APP_NAME = 'Darkest Dungeon';
    const APP_ID = 262060;
    
    /**
     * @var Steamspy
     */
    protected $steam;

    public function testAppdetails()
    {
        $game = $this->steam->appdetails(self::APP_ID);
        
        $this->assertSame(self::APP_NAME, $game->name);
        $this->assertSame(self::APP_ID, $game->id);
    }
    
    public function testGenre()
    {
        $games = $this->steam->genre('Early Access');
        
        $this->assertGreaterThan(0, count($games));
        
        $game = reset($games);
        $this->assertGreaterThan(0, $game->id);
    }
    
    public function testPollRate()
    {
        $this->steam->setDefaultPollRateLimit();
        $startTime = microtime(true);
        for ($i = 0; $i <= Steamspy::DEFAULT_POLL_RATE_LIMIT; $i++) {
            $this->steam->appdetails(self::APP_ID);
        }
        $this->assertGreaterThan(1, microtime(true) - $startTime);
        
        
        $this->steam->disablePollRateLimit();
        $startTime = microtime(true);
        for ($i = 0; $i < Steamspy::DEFAULT_POLL_RATE_LIMIT; $i++) {
            $this->steam->appdetails(self::APP_ID);
        }
        $this->assertLessThan(1, microtime(true) - $startTime);
    }

    protected function setUp()
    {
        $this->steam = new Steamspy;
    }
}
