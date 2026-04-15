#!/bin/sh
set -eu

MARKER_FILE="${CERTBOT_WEBROOT:-/var/www/certbot}/.nginx-reload"
POLL_SECONDS="${NGINX_RELOAD_POLL_SECONDS:-60}"
LAST_MTIME="$(stat -c '%Y' "${MARKER_FILE}" 2>/dev/null || true)"

watch_for_cert_updates() {
    while :; do
        CURRENT_MTIME="$(stat -c '%Y' "${MARKER_FILE}" 2>/dev/null || true)"

        if [ -n "${CURRENT_MTIME}" ] && [ "${CURRENT_MTIME}" != "${LAST_MTIME}" ]; then
            LAST_MTIME="${CURRENT_MTIME}"
            render-config.sh
            if [ -f /var/run/nginx.pid ]; then
                nginx -s reload || true
            fi
        fi

        sleep "${POLL_SECONDS}"
    done
}

render-config.sh
watch_for_cert_updates &

exec nginx -g 'daemon off;'
