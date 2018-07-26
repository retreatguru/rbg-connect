#!/bin/bash

# Set default posts to host content to Events and Teachers
wp option update rs_remote_settings --format=json '{"rs_domain":"tests.qa0", "page": {"programs": 4, "teachers": 5}}'