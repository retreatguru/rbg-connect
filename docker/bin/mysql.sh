#!/bin/sh
mysql -h$WORDPRESS_DB_HOST -P${WORDPRESS_DB_PORT:-3306} -u$WORDPRESS_DB_USER -p$WORDPRESS_DB_PASSWORD "$@"