#!/bin/bash

# Set default posts to host content to Events and Teachers
wp option update rs_remote_settings --format=json '{"rs_domain":"tests", "page": {"programs": 4, "teachers": 5}}'