# start-docker-build.ps1

# Set execution policy for the current user (run this separately if needed)
# Set-ExecutionPolicy RemoteSigned -Scope CurrentUser

# Start Docker Compose
docker-compose -f docker-compose-prod.yml --env-file .env.local down

sleep 5
# Remove specific volumes (excluding my_sqldata)
docker volume rm zm32_symfony_code zm32_angular_dist

sleep 5
docker rmi zm32-symfony zm32-nginx

