#!/bin/bash
cliNetwork="dev_net";
cliImageName="lavoweb/php-5.6:xdebug";
cliHostName="sdk";
PHP_IDE_CONFIG="serverName="$cliHostName;

docker run -it --rm --network ${cliNetwork} -v $(pwd):/app -w /app --env PHP_IDE_CONFIG=${PHP_IDE_CONFIG} ${cliImageName} "$@"
