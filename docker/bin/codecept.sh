#!/bin/sh
cd wp-content/plugins/programs-remote-tests
env PHP_IDE_CONFIG="serverName=rgconnect.test" vendor/bin/codecept "$@"
