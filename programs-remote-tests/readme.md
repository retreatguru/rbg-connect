
## Installing Codeception

First ensure you have composer installed properly: https://getcomposer.org/download/

Then install codeception dependancies (can take a while):

    $ php composer.phar install

## Install wp-cli

follow instructions here: http://wp-cli.org/docs/installing/ you'll only need this to initially setup the test pages

## Setting Up Tests

look in circle.yml and run all the wp-cli.phar commands there. This will setup the proper test environment. for example:

    $ php wp-cli.phar post create --post_type=page --post_title='Events' --post_status=publish
    ...

## Setup Test Dependancies

Remote tests totally rely on the production server being in a certain state. 
Go to https://tests.secure.retreat.guru/wp-admin/admin.php?page=rs-demo-tools and click "Clear database and reset for tests" 

## Running Tests

Run tests with different levels of specificity:

    $ vendor/bin/codecept run acceptance
