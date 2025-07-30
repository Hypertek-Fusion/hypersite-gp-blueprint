#!/bin/bash

# Load the config file
source config.cfg

# Check if SSH agent is running, if not start it
if [ -z "$SSH_AGENT_PID" ] || ! ps -p "$SSH_AGENT_PID" > /dev/null 2>&1; then
    echo "Starting ssh-agent..."
    eval "$(ssh-agent -s)"
fi

# Check if the key is already added
if ! ssh-add -l | grep -q "$(ssh-keygen -lf ~/.ssh/id_rsa | awk '{print $2}')"; then
    ssh-add ~/.ssh/id_rsa
fi

echo "Building vite package"
cd wp-content/plugins/HyperSiteReviews

npm run build

echo "Build Success"
cd ../../../

sleep 3

# Add Stage
echo "Adding files to repo"
git add .

# Commit Stage
echo "Enter your commit message"
read MESSAGE

echo "Commiting \"$MESSAGE\""
git commit -m "$MESSAGE"

# Push phase
echo "Pushing to repo"
git push origin $BRANCH
