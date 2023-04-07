#!/bin/bash
echo "Creating local docker image"
docker-compose -f docker-compose.yml up --build  -d --force-recreate
echo "Docker up and running";
docker exec sunya_health_loc php artisan migrate
echo "Migrated"