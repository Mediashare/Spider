FROM alpine:latest
RUN apk upgrade && apk update
RUN apk add php php-xml php-curl php-ctype php-tokenizer php-pdo php-dom php-session
RUN apk add composer git
WORKDIR /home
RUN git clone https://github.com/Mediashare/WebSpider webspider
WORKDIR /home/webspider
RUN composer install
#RUN bin/console doctrine:database:create
#RUN bin/console make:migration
#RUN bin/console doctrine:migrations:migrate -n
#VOLUME ["/home"]
EXPOSE 80 443