# start-docker-build.ps1

# Set execution policy for the current user (run this separately if needed)
# Set-ExecutionPolicy RemoteSigned -Scope CurrentUser

# Start Docker Compose
docker-compose -f docker-compose-prod.yml --env-file .env.local down