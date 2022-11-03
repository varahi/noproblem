FROM webdevops/php-nginx-dev:8.1
ENV PROVISION_CONTEXT "development"

# Deploy scripts/configurations
#COPY etc/             /opt/docker/etc/

#COPY etc/nginx/nginx.conf /etc/nginx/nginx.conf
#COPY etc/nginx/default.conf /etc/nginx/conf.d/default.conf

# Fot php 7.3 uses mysql-client
#RUN apt-get update && apt-get -y install mysql-client

# Fot php 7.4 uses mariadb-client
RUN apt-get update && apt-get -y install mariadb-client

#RUN ln -sf /opt/docker/etc/cron/crontab /etc/cron.d/docker-boilerplate \
#    && chmod 0644 /opt/docker/etc/cron/crontab \
#    && echo >> /opt/docker/etc/cron/crontab \
#    && ln -sf /opt/docker/etc/php/development.ini /opt/docker/etc/php/php.ini

# Configure volume/workdir
WORKDIR /
