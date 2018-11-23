FROM cmptech/auto_alpine_php7_runtime_with_swoole_latest

# install basic lib for cmp
RUN echo "http://nl.alpinelinux.org/alpine/latest-stable/main" > /etc/apk/repositories \
&& echo "http://nl.alpinelinux.org/alpine/edge/testing/" >> /etc/apk/repositories \
&& echo "http://nl.alpinelinux.org/alpine/edge/community/" >> /etc/apk/repositories \
&& echo "nameserver 8.8.8.8" >> /etc/resolv.conf && apk update && apk upgrade \
&& apk add \
php7-openssl \
php7-sqlite3 \
php7-pear \
php7-gmp \
php7-pdo_mysql \
php7-pcntl \
php7-common \
php7-xsl \
php7-fpm \
php7-mysqlnd \
php7-redis \
php7-snmp \
php7-mbstring \
php7-timezonedb \
php7-xmlreader \
php7-pdo_sqlite \
php7-exif \
php7-opcache \
php7-posix \
php7-session \
php7-gettext \
php7-json \
php7-xml \
php7-mongodb \
php7 \
php7-iconv \
php7-sysvshm \
php7-curl \
php7-shmop \
php7-odbc \
php7-phar \
php7-pdo_pgsql \
php7-imap \
php7-pdo_dblib \
php7-pgsql \
php7-pdo_odbc \
php7-zip \
php7-cgi \
php7-ctype \
php7-mcrypt \
php7-bcmath \
php7-calendar \
php7-tidy \
php7-dom \
php7-sockets \
php7-memcached \
php7-soap \
php7-sysvmsg \
php7-zlib \
php7-ssh2 \
php7-ftp \
php7-sysvsem \
php7-pdo \
php7-bz2 \
php7-mysqli \
vim wget curl bash openssl \
&& rm -rf /var/cache/apk/* \
&& rm -rf /tmp/* \
&& ln -s /usr/sbin/php-fpm7 /usr/sbin/php-fpm

COPY php.ini /etc/php7/
#COPY php.ini /etc/php7/conf.d/50-setting.ini
#ADD default_entry.sh /root/
#COPY php-fpm.conf /etc/php7/php-fpm.conf

#EXPOSE 9000
#EXPOSE 9501
RUN php -i
#CMD ["/root/default_entry.sh"]
#CMD ["php-fpm7", "-F"]

# Register the COMPOSER_HOME environment variable
ENV COMPOSER_HOME /composer

# Add global binary directory to PATH and make sure to re-export it
ENV PATH /composer/vendor/bin:$PATH

# Allow Composer to be run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Setup the Composer installer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
  && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
  && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }"

RUN php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot && rm -rf /tmp/composer-setup.php
