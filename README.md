## Installation

- copy entire SDK directory contents to your project accessible folder. E.g.: /var/www/htdocs/lib/packeterySdk/
- require /autoload.php in your index.php or bootstrap file
- setup cron to call:
  - `Cache::clearAll('/htdocs/lib/packeterySdk/temp');`
  - `$container->getDatabaseFeedService()->updateData();`
- create container instance in your Controller or register it in your own container (e.g.: config.neon)
  - requires config array. See config.php.dist
  - you can pass your own connection driver. SDK will create new connection if no driver is specified
  
## Usage

- see example.php in root directory
