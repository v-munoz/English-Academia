#!/bin/bash

#!No me funciona > https://dev.to/gavinsykes/build-a-modern-api-with-slim-4-set-up-your-env-file-58o3


env_file="/.env"


if [ ! -f "$env_file" ]; then
  echo "Error: .env file not found."
  exit 1
fi

for var in "${required_env_vars[@]}"; do
  grep -q "^$var=" "$env_file"
  if [ $? -ne 0 ]; then
    echo "Error: $var is missing in the $env_file file."
    exit 1
  fi
done

ENVIRONMENT=$(grep "^_ENVIRONMENT=" "$env_file" | cut -d'=' -f2)
allowed_environments=("development" "staging" "demo" "production")

if ! [[ " ${allowed_environments[@]} " =~ " $ENVIRONMENT " ]]; then
  echo "Error: _ENVIRONMENT must be one of ${allowed_environments[@]}"
  exit 1
fi

echo "All environment variables present and correct in $env_file."

exit 0
