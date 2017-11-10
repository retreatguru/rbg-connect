#!/bin/bash
MAINFILE="rs-connect.php"
VERSION=`grep "Version:" $MAINFILE | awk -F' ' '{print $NF}'`
FILENAME="rbg-connect-$VERSION.zip"
zip -r $FILENAME *
echo
echo "Created $FILENAME"
