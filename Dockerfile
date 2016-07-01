FROM quintype/docker-base:php-nginx

MAINTAINER Quintype Developers <dev-core@quintype.com>

EXPOSE 3000

RUN ln -s /app/log /var/log/climatedesk

ADD . /app

WORKDIR /app

RUN git log -n1 --pretty="Commit Date: %aD%nBuild Date: `date --rfc-2822`%n%h %an%n%s%n" > public/round-table.txt

RUN apt-get install -y libnotify-bin
RUN rm -rf tmp vendor node_modules
RUN composer install
RUN npm install
RUN ./node_modules/.bin/gulp --production

RUN chown -R www-data.www-data /app

RUN chmod 755 ./docker/start-in-container.sh

CMD ["./docker/start-in-container.sh"]
