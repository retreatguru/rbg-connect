
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

## Running Tests

Run tests with different levels of specificity:

    $ vendor/bin/codecept run acceptance

## Gotchas

Remote tests rely on http://dev.programs.dev to be setup properly. Visit http://dev.programs.dev/?rs_clear_database_and_seed=true to ensure it's reset.
There is no test reset function (yet). The best way to get to a correct state is to delete all pages and then re-add the pages using wp-cli