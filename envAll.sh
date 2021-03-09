#!/bin/bash
docker run -it --rm \
-v $(pwd):/app -v $(pwd)/php.ini:/usr/local/etc/php/php.ini -w /app \
--env PHP_MEMORY_LIMIT="15M" \
--env PHP_MAX_EXECUTION_TIME="30" \
--env PHP_IDE_CONFIG=${PHP_IDE_CONFIG} \
${cliImageName} "$@"
