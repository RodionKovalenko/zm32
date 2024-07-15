# start-docker-build.ps1

# Set execution policy for the current user (run this separately if needed)
# Set-ExecutionPolicy RemoteSigned -Scope CurrentUser

# Start Docker Compose
cd ..
docker-compose -f docker-compose-dev.yml --env-file .env.local up --build -d

# Wait a few seconds
Start-Sleep -Seconds 10

# Open the URL in the default browser
Start-Process "https://iba.local.de:4200"
