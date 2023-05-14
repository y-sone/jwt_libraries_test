FROM php:8.2-cli
RUN apt-get update && apt-get install -y git zip unzip
COPY . /usr/src/app
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /usr/src/app
