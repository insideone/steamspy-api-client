# Steamspy API Client
Steamspy API client for PHP (http://steamspy.com/api.php)

## Installing
```bash
composer require inside/steamspy-api-client
```

## Usage
```php
use Inside\SteamspyApi\Steamspy;

$api = new Steamspy;
$game = $api->appdetails(262060);
echo $game->name;
```
