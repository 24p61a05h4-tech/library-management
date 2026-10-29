FROM php:8.2-apache
RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite
WORKDIR /var/www/html
COPY . /var/www/html/
COPY render-entrypoint.sh /usr/local/bin/render-entrypoint.sh
RUN chmod +x /usr/local/bin/render-entrypoint.sh \
    && chown -R www-data:www-data /var/www/html
ENV PORT=10000
EXPOSE 10000
CMD ["/usr/local/bin/render-entrypoint.sh"]
