FROM dock0/service
MAINTAINER akerl <me@lesaker.org>
USER root
EXPOSE 80
RUN pacman -S --noconfirm nginx
ADD nginx.conf /etc/nginx/nginx.conf
ADD run /service/nginx/run
