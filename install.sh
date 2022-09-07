#!/bin/bash

DIR="api"

# look for empty dir
if [ -d "${DIR}" ]; then
     echo "Lumen project already installed"
else
    docker run -v $(pwd):/app composer/composer create-project --prefer-dist laravel/lumen ${DIR}
fi

docker compose build
docker compose up -d

