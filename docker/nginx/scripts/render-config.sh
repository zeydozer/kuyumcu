#!/bin/sh
set -eu

: "${DOMAIN:?DOMAIN environment variable is required}"

APP_UPSTREAM="${APP_UPSTREAM:-app:80}"
CLIENT_MAX_BODY_SIZE="${CLIENT_MAX_BODY_SIZE:-25m}"
CERT_PATH="/etc/letsencrypt/live/${DOMAIN}"
TEMPLATE="/opt/nginx/templates/site.http.conf.template"

if [ -f "${CERT_PATH}/fullchain.pem" ] && [ -f "${CERT_PATH}/privkey.pem" ]; then
    TEMPLATE="/opt/nginx/templates/site.ssl.conf.template"
fi

export DOMAIN APP_UPSTREAM CLIENT_MAX_BODY_SIZE

envsubst '${DOMAIN} ${APP_UPSTREAM} ${CLIENT_MAX_BODY_SIZE}' \
    < "${TEMPLATE}" \
    > /etc/nginx/conf.d/default.conf
