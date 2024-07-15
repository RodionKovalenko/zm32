#!/bin/bash

# Build and start the containers
cd ..
docker-compose -f docker-compose-prod.yml --env-file .env.local up --build -d

# Wait for a few seconds to ensure the service is running
sleep 10

# Open the URL in the default browser
xdg-open https://iba.local.de:4200 || open https://iba.local.de:4200