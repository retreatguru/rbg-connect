FROM retreatguru/connect-plugin:php81

# Copy bin files to Docker install
COPY docker/bin /usr/local/bin

# install subversion for wp plugin deployment
RUN apt-get update
RUN apt-get -y install subversion
