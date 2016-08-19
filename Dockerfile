FROM quintype/docker-base:php-nginx

MAINTAINER Quintype Developers <dev-core@quintype.com>

EXPOSE 3000

ADD . /app

WORKDIR /app

RUN git log -n1 --pretty="Commit Date: %aD%nBuild Date: `date --rfc-2822`%n%h %an%n%s%n" > public/round-table.txt && \
    ln -s /app/storage/log /var/log/application && \
    apt-get update && \
    apt-get install -y libnotify-bin && \
    rm -rf tmp vendor node_modules && \
    composer install && \
    npm install && \
    ./node_modules/.bin/gulp --production && \
    chown -R www-data.www-data /app && \
    chmod 755 ./docker/start-in-container.sh

CMD ["./docker/start-in-container.sh"]
