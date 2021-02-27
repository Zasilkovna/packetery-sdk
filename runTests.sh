#!/bin/bash
PHP_IDE_CONFIG="serverName=sdk" ./env.sh php vendor/bin/phpunit "$@"
