FROM nginx:alpine
COPY nginx.conf /etc/nginx/nginx.conf

RUN apk add bash \
    nano \
    nmap

## setup reverse proxy cache folder
#RUN mkdir /tmp/nginx && chgrp www-data /tmp/nginx && chmod 700 /tmp/nginx