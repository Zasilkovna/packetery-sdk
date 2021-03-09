#!/bin/bash
cliHostName="sdk";
export PHP_IDE_CONFIG="serverName="$cliHostName;
XDEBUG_ENABLE=0

if [ $XDEBUG_ENABLE -eq 0 ]; then
  export XDEBUG_MODE=off
fi

export cliImageName="lavoweb/php-8.0:xdebug"
./envAll.sh php -n -dzend_extension=xdebug.so -dxdebug.mode=debug -dxdebug.client_port=9000 -dxdebug.client_host=host.docker.internal  \
vendor/bin/phpunit "$@"

export cliImageName="lavoweb/php-7.4:xdebug";
./envAll.sh php -n -dzend_extension=xdebug.so -dxdebug.mode=debug -dxdebug.client_port=9000 -dxdebug.client_host=host.docker.internal \
vendor/bin/phpunit "$@"

export cliImageName="lavoweb/php-7.3:xdebug";
./envAll.sh php -n -dzend_extension=xdebug.so -dxdebug.remote_enable=${XDEBUG_ENABLE} -dxdebug.remote_mode=jit -dxdebug.remote_port=9000 -dxdebug.remote_host=host.docker.internal -dxdebug.remote_connect_back=0 \
vendor/bin/phpunit "$@"

export cliImageName="lavoweb/php-7.2:xdebug";
./envAll.sh php -n -dzend_extension=xdebug.so -dxdebug.remote_enable=${XDEBUG_ENABLE} -dxdebug.remote_mode=jit -dxdebug.remote_port=9000 -dxdebug.remote_host=host.docker.internal -dxdebug.remote_connect_back=0 \
vendor/bin/phpunit "$@"

export cliImageName="lavoweb/php-7.1:xdebug";
./envAll.sh php -n -dzend_extension=xdebug.so -dxdebug.remote_enable=${XDEBUG_ENABLE} -dxdebug.remote_mode=jit -dxdebug.remote_port=9000 -dxdebug.remote_host=host.docker.internal -dxdebug.remote_connect_back=0 \
vendor/bin/phpunit "$@"

export cliImageName="lavoweb/php-7.0:xdebug";
./envAll.sh php -n -dzend_extension=xdebug.so -dxdebug.remote_enable=${XDEBUG_ENABLE} -dxdebug.remote_mode=jit -dxdebug.remote_port=9000 -dxdebug.remote_host=host.docker.internal -dxdebug.remote_connect_back=0 \
vendor/bin/phpunit "$@"

export cliImageName="lavoweb/php-5.6:xdebug";
./envAll.sh php -n -dzend_extension=xdebug.so -dxdebug.remote_enable=${XDEBUG_ENABLE} -dxdebug.remote_mode=jit -dxdebug.remote_port=9000 -dxdebug.remote_host=host.docker.internal -dxdebug.remote_connect_back=0 \
vendor/bin/phpunit "$@"
