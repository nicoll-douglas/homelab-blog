#!/bin/bash

SCRIPTS_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)

cd $SCRIPTS_DIR/..

docker compose -f docker-compose.dev.yml up --build --remove-orphans