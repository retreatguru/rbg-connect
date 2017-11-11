#!/bin/bash
PLUGINSLUG="retreat-booking-guru-connect"
MAINFILE="rs-connect.php"
VERSION=`grep "Version:" $MAINFILE | awk -F' ' '{print $NF}'`
FILENAME="$PLUGINSLUG-$VERSION.zip"
zip -r $FILENAME *
echo
echo "Created $FILENAME"
