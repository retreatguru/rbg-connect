#!/bin/bash

# Set up the plugin using symbolic links
mkdir -p wp-content/plugins
cd wp-content/plugins/
ln -fs ../../../programs-* .
cd - > /dev/null

# Install wordpress
wp core download
wp config create --dbname=$WORDPRESS_DB_NAME --dbhost=$WORDPRESS_DB_HOST --dbuser=$WORDPRESS_DB_USER --dbpass=$WORDPRESS_DB_PASSWORD
wp db reset --yes
wp core install \
    --url=rgconnect.dev \
    --title="RG Connect Site" \
    --admin_email=info@retreat.guru \
    --admin_user=admin \
    --admin_password=admin \
    --skip-email
wp plugin activate programs-remote-listings
wp rewrite structure --hard /%postname%/
wp option update rewrite_rules ''

# Set up sidebar to show list of pages
wp widget reset sidebar-1
wp widget add pages sidebar-1

# Seed root pages for testing
wp post create --post_type=page --post_title='Events' --post_status=publish
wp post create --post_type=page --post_title='Leaders' --post_status=publish
wp post create --post_type=page --post_title='Retreats' --post_status=publish
wp post create --post_type=page --post_title='Gurus' --post_status=publish

# Seed shortcode pages for testing
wp post create --post_type=page --post_title='Shortcode Event List' --post_status=publish --post_content="[rs_programs category='plant-medicine']"
wp post create --post_type=page --post_title='Shortcode Event List Table' --post_status=publish --post_content="[rs_programs table show_title show_register_link show_date show_price_first show_more_link show_availability show_availability_words]"
wp post create --post_type=page --post_title='Shortcode Teachers' --post_status=publish --post_content="[rs_teachers]"
wp post create --post_type=page --post_title='Shortcode Event List Hide Text' --post_status=publish --post_content="[rs_programs hide_text]"
wp post create --post_type=page --post_title='Shortcode Event List Hide Location' --post_status=publish --post_content="[rs_programs hide_location]"
wp post create --post_type=page --post_title='Shortcode Event List Hide Stuff' --post_status=publish --post_content="[rs_programs hide_text hide_location]"
wp post create --post_type=page --post_title='Shortcode Event List Table Less' --post_status=publish --post_content="[rs_programs table show_title show_date]"
wp post create --post_type=page --post_title='Shortcode Event List Table by Category' --post_status=publish --post_content="[rs_programs table show_title show_date category='plant-medicine']"

# Set default posts to host content to Events and Teachers
wp option update rs_remote_settings --format=json '{"rs_domain":"tests", "page": {"programs": 3, "teachers": 4}}'
