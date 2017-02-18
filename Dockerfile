FROM quintype/docker-base:php-nginx-alpine

MAINTAINER Quintype Developers <dev-core@quintype.com>

EXPOSE 3000

RUN ln -s /app/storage/log /var/log/app

ADD . /app

WORKDIR /app

RUN git log -n1 --pretty="Commit Date: %aD%nBuild Date: `date --rfc-2822`%n%h %an%n%s%n" > public/round-table.txt && \
    rm -rf tmp vendor node_modules && \
    composer install && \
    npm install && \
    ./node_modules/.bin/gulp --production && \
    chown -R nginx:nginx /app && \
    chmod 755 ./docker/start-in-container.sh

CMD ["./docker/start-in-container.sh"]
