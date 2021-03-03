#!/bin/bash
./env.sh php -dxdebug.remote_mode=jit vendor/bin/phpunit "$@"

# export PHP_IDE_CONFIG="serverName=sdk" && docker run -it --rm -v $(pwd):/app --env PHP_IDE_CONFIG="serverName=sdk" packetery:apache-5.6 php -dxdebug.remote_enable=1 -dxdebug.remote_mode=jit -dxdebug.remote_port=9000 -dxdebug.remote_host=host.docker.internal -dxdebug.remote_connect_back=0 /app/example.php
#./env.sh php -dxdebug.remote_enable=1 -dxdebug.remote_mode=jit -dxdebug.remote_port=9000 -dxdebug.remote_host=host.docker.internal -dxdebug.remote_connect_back=0 /app/example.php
