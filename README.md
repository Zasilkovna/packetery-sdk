# Packetery SDK

Library that aims to simplify manipulation with packetery APIs.

## Requirements

- PHP 5.6, 7.0, 7.1, 7.2, 7.3, 7.4, 8.0
- configuration:
  - allow_url_fopen=1

## Installation

- copy entire SDK directory contents to your project accessible folder. E.g.: /var/www/htdocs/lib/packeterySdk/
- require /autoload.php in your index.php or bootstrap file
- create container instance in your Controller or register it in your own container (e.g.: config.neon)
  - requires config array. See config.php.dist
  
## Usage

- see example.php in root directory
