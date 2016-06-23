FROM jruby:9.0-jdk

MAINTAINER Quintype Developers <dev-core@quintype.com>

ENV RAILS_ENV production
ENV RACK_ENV production
ENV ASSET_HOST https://fea.quintype.com
ENV RAILS_SERVE_STATIC_FILES true
ENV JRUBY_OPTS -J-Xms512m -J-Xmx768m

EXPOSE 3000

RUN apt-get update && apt-get install -y nodejs npm htop git \
    && useradd -ms /bin/bash app
RUN ln -s /usr/bin/nodejs /usr/bin/node
RUN ln -s /app/log /var/log/climatedesk

ADD . /app
RUN chown -R app:app /app

USER app
WORKDIR /app

RUN git log -n1 --pretty="Commit Date: %aD%nBuild Date: `date --rfc-2822`%n%h %an%n%s%n" > public/round-table.txt
RUN bundle install --without development --deployment
RUN npm install
RUN bundle exec rake assets:precompile

ENTRYPOINT ["bundle"]
CMD [ "exec" ,  "puma", "-t", "8:24"]

# USAGE: docker run -d -i -t -p 3000:3000 quintype/climatedesk
