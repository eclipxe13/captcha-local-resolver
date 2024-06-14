FROM php:8.3-cli-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . /opt/captcha-local-resolver

RUN set -e \
    && composer update --working-dir=/opt/captcha-local-resolver --no-dev --prefer-dist --optimize-autoloader --no-interaction \
    && rm -rf "$(composer config cache-dir --global)" "$(composer config data-dir --global)" "$(composer config home --global)"

ENTRYPOINT ["php", "/opt/captcha-local-resolver/bin/service.php"]
