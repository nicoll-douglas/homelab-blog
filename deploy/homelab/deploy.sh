#!/bin/bash

set -e

CURRENT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
REPO_DIR=$CURRENT_DIR/../..

cd $CURRENT_DIR

# import .env variables
set -a
source .env
set +a

cd $REPO_DIR

# build image
echo "Building Docker image: $DOCKERHUB_IMAGE"
docker build -t $DOCKERHUB_IMAGE .

# push image
echo "Pushing image to Docker Hub"
docker push $DOCKERHUB_IMAGE

cd $CURRENT_DIR

# deploy
echo "Deploying to $HOMELAB_HOST..."
ssh -i $HOMELAB_CI_KEY $HOMELAB_CI_USER@$HOMELAB_HOST "$HOMELAB_SERVICE_DIR/deploy.sh"

echo "Deployment complete."