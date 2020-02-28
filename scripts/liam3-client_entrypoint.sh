#!/bin/sh
set -e

/usr/local/bin/production_server_master.sh

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
        set -- apache2-foreground "$@"
fi

exec "$@"