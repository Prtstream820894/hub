FROM php:8.2-apache
RUN apt-get update && apt-get install -y libxml2-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install dom
COPY . /var/www/html/
EXPOSE 80
