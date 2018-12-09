<?php

namespace Inside\SteamspyApi\Enitity;

class App
{
    const SEPARATOR = ';';
    const HIDDEN_ID = 999999;

    /**
     * @var int Steam Application ID. If it's 999999, then data for this application is hidden on developer's request
     */
    public $id = self::HIDDEN_ID;

    /**
     * @var string the game's name
     */
    public $name = '';

    /**
     * @var string[] list of the developers of the game
     */
    public $developers = [];

    /**
     * @var string[] list of the publishers of the game
     */
    public $publishers = [];

    /**
     * @var int score rank of the game based on user reviews
     */
    public $scoreRank = 0;

    /**
     * @var int owners of this application on Steam. **Beware of free weekends!**
     */
    public $owners = 0;

    /**
     * @var int variance in owners. The real number of owners lies somewhere on owners +/- owners_variance range
     */
    public $ownersVariance = 0;

    /**
     * @var int people that have played this game since March 2009
     */
    public $playersForever = 0;

    /**
     * @var int variance for total players
     */
    public $playersForeverVariance = 0;

    /**
     * @var int people that have played this game in the last 2 weeks
     */
    public $players2weeks = 0;

    /**
     * @var int variance for the number of players in the last two weeks
     */
    public $players2weeksVariance = 0;

    /**
     * @var int average playtime since March 2009. In minutes
     */
    public $averageForever = 0;

    /**
     * @var int average playtime in the last two weeks. In minutes
     */
    public $average2weeks = 0;

    /**
     * @var int median playtime since March 2009. In minutes
     */
    public $medianForever = 0;

    /**
     * @var int median playtime in the last two weeks. In minutes
     */
    public $median2weeks = 0;

    /**
     * @var int peak CCU yesterday
     */
    public $ccu = 0;

    /**
     * @var int US price in cents
     */
    public $price = 0;

    /**
     * @var array the game's tags => votes
     */
    public $tags = [];

    /**
     * @var int Positive reviews count
     */
    public $positive;

    /**
     * @var int Negative reviews count
     */
    public $negative;

    /**
     * App is hidden?
     * @return bool
     */
    public function isHidden()
    {
        return $this->id === self::HIDDEN_ID;
    }

    /**
     * App has tag?
     * @param string $tag
     * @return bool
     */
    public function hasTag($tag)
    {
        return isset($this->tags[$tag]);
    }

    public function setAppid($appId)
    {
        $this->id = $appId;
        return $this;
    }
    
    public function setDeveloper($developer)
    {
        $this->developers = explode(self::SEPARATOR, $developer);
        return $this;
    }

    public function setPublisher($publisher)
    {
        $this->publishers = explode(self::SEPARATOR, $publisher);
        return $this;
    }

    public function getReviewsCount()
    {
        return $this->positive + $this->negative;
    }
}
