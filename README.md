## Installation

- copy entire SDK directory contents to your project accessible folder. E.g.: /var/www/htdocs/lib/packeterySdk/
- require /autoload.php in your index.php or bootstrap file. Use $container returned by "require". See /example.php for correct usage.
- Feed is downloaded on demand a chached for 1 day. No need to setup cron.
- If you want make sure feed is not downloaded on demand, then setup cron to call:
  - `Cache::clearAll('/htdocs/lib/packeterySdk/temp');`
  - `$container->getFeedServiceBrain()->getSimpleCarrierExport();`
  
## Usage

- see example.php in root directory
