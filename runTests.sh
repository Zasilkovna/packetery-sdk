#!/bin/bash
./env.sh php -n -dzend_extension=xdebug.so -dxdebug.remote_enable=1 -dxdebug.remote_mode=jit -dxdebug.remote_port=9000 -dxdebug.remote_host=host.docker.internal -dxdebug.remote_connect_back=0 vendor/bin/phpunit -c phpunit.coverage.xml "$@"
