FROM registry.access.redhat.com/ubi8/php-81:latest

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN microdnf install -y nano

EXPOSE 80
