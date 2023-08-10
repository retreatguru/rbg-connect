FROM php:8.1-apache

# Copy bin files to Docker install
COPY docker/bin /usr/local/bin
RUN chmod +x /usr/local/bin/*.sh

# Enabled apache modules
RUN a2enmod rewrite expires

# Install required packages
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libfreetype6-dev \
        libjpeg-dev \
        libzip-dev \
        libtidy-dev \
        default-mysql-client \
        git \
        zip \
        unzip \
        wget \
        less \
        vim \
        nano \
        ssh \
        sudo \
        cron \
        subversion \
    && rm -rf /var/lib/apt/lists/*

# Install the PHP extensions we need
RUN set -ex \
    && docker-php-ext-configure gd --with-jpeg=/usr --with-freetype=/usr/ \
    && docker-php-ext-install gd mysqli opcache pdo_mysql tidy \
    && pecl install xdebug zip \
    && docker-php-ext-enable zip

# Install wp-cli to control Wordpress installations
RUN set -ex \
    && curl -# https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /tmp/wp-cli.phar \
    && chmod +x /tmp/wp-cli.phar \
    && mv /tmp/wp-cli.phar /usr/local/bin/wp

# Install composer for installing php dependencies for tests
RUN set -ex \
    && curl -# https://getcomposer.org/installer | \
       php -- --install-dir=/usr/local/bin --filename=composer

# Set up default working directory
WORKDIR /var/www/html

# Add non root user
RUN useradd -m noroot -u 1000 -s /bin/bash -g www-data
RUN echo 'noroot ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers