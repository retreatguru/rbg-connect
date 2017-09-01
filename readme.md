## Installing dev environment
1. Make sure docker is installed and configured
1. Checkout the branch
1. Run ``./dev up``
1. Add rgconnect.dev to your hosts file
1. Verify the site is accessible at http://rgconnect.dev
1. Configure the plugin in http://rgconnect.dev/wp-admin accordingly
1. Ensure tests pass by running `./dev test`

## Pre-Deployment Checklist ##

1. Update the version number in programs-remote-listings/rs-connect.php on line 6
1. Update the $plugin_version near the top of programs-remote-listings/rs-connect.php
1. Edit the changelog in programs-remote-listings/readme.txt with the same version number
1. Commit.
1. Merge commit into master.
1. Ensure tests are green at https://circleci.com/gh/retreatguru/rbg-connect/tree/master

## Actual Deployment ##

To deploy the plugin directly to the wordpress repository you will need a wordpress username and password and be an author of the plugin. From Vagrant box:

    $ cd /srv/www/rg/remote/htdocs/wp-content/plugins/programs-remote-listings
    $ bash ./deploy.sh
    
Then provide your wordpress username, password and a commit message. 
In about a minute you should see the new version live on the wordpress repository:
https://en-ca.wordpress.org/plugins/retreat-booking-guru-connect/
