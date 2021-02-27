#!/bin/bash
./env.sh php -dxdebug_remote=jit vendor/bin/phpunit "$@"

