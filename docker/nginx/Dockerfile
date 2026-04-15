FROM nginx:1.30-alpine

COPY docker/nginx/scripts/render-config.sh /usr/local/bin/render-config.sh
COPY docker/nginx/scripts/start-nginx.sh /usr/local/bin/start-nginx.sh
COPY docker/nginx/templates /opt/nginx/templates

RUN chmod +x /usr/local/bin/render-config.sh /usr/local/bin/start-nginx.sh

CMD ["start-nginx.sh"]
