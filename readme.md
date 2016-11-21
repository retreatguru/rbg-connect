## Pre-Deployment Checklist ##

Ensure that you have updated the version number in programs-remote-listings/rs-connect.php
Edit the changelog in programs-remote-listings/readme.txt 
Commit.
Merge commit into master.
Ensure tests are green at https://circleci.com/gh/retreatguru/rbg-connect/tree/master

## Actual Deployment ##

To deploy the plugin directly to the wordpress repository you will need a wordpress username and password and be an author of the plugin. From Vagrant box:

    $ cd /srv/www/rg/remote/htdocs/wp-content/plugins/programs-remote-listings
    $ bash ./deploy.sh
    
Then provide your wordpress username, password and a commit message. 
In about a minute you should see the new version live on the wordpress repository:
https://en-ca.wordpress.org/plugins/retreat-booking-guru-connect/