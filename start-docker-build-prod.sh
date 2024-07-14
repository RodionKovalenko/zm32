#!/bin/bash

# Build and start the containers
docker-compose -f docker-compose-prod.yml --env-file .env.local up --build -d

# Wait for a few seconds to ensure the service is running
sleep 10

# Open the URL in the default browser
xdg-open https://localhost:4200 || open https://localhost:4200