#!/bin/bash
cliNetwork="dev_net";
cliImageName="lavoweb/php-5.6:xdebug";
cliHostName="sdk";
PHP_IDE_CONFIG="serverName="$cliHostName;

docker run -it --rm --network ${cliNetwork} \
--env PHP_MEMORY_LIMIT="15M" \
--env PHP_MAX_EXECUTION_TIME="30" \
-v $(pwd):/app -v $(pwd)/temp/php.ini:/usr/local/etc/php/php.ini -w /app --env PHP_IDE_CONFIG=${PHP_IDE_CONFIG} ${cliImageName} "$@"
