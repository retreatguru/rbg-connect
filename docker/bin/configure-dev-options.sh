#!/bin/bash

# Set default posts to host content to Events and Teachers
PROGRAMS_PAGE=$(wp post list --post_type=page --name=events --field=ID)
TEACHERS_PAGE=$(wp post list --post_type=page --name=leaders --field=ID)
wp option update rs_remote_settings --format=json "{\"rs_domain\":\"tests.qa0\", \"page\": {\"programs\": $PROGRAMS_PAGE, \"teachers\": $TEACHERS_PAGE}}"
