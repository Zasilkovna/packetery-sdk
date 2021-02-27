#!/bin/bash

cliNetwork="dev_net";
cliImageName="packetery:apache-5.6";
#cliImageName="packetery:cli-7.1";
cliHostName="sdk";
cliXdebugRemoteHost="host.docker.internal";
PHP_IDE_CONFIG="serverName="$cliHostName;

docker run -it --rm --network ${cliNetwork} -v $(pwd):/app --env NETTE_DEBUG=1 --env XDEBUG_REMOTE_ENABLE=1 --env XDEBUG_SESSION=1 --env XDEBUG_SESSION_START=sdk --env HOST_NAME=${cliHostName} --env XDEBUG_REMOTE_HOST=${cliXdebugRemoteHost} --env XDEBUG_REMOTE_PORT=9000 --env XDEBUG_IDEKEY=XDEBUG_ECLIPSE --env PHP_IDE_CONFIG=${PHP_IDE_CONFIG} ${cliImageName} "$@"
