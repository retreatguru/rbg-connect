#!/bin/sh
until mysql.sh -e "show databases;" 2> /dev/null > /dev/null
do
    echo -n .
    sleep 1
done
echo