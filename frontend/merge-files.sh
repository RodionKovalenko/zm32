#!/bin/sh

# Ensure the necessary directories exist
mkdir -p /var/www/symfony/public

# Copy files from /tmp/angular-build to /var/www/symfony/public
cd /tmp/angular-build
find . -type f | while read file; do
    dest="/var/www/symfony/public/${file#./}"
    if [ ! -f "$dest" ]; then
        echo "Copying $file to $dest"
        mkdir -p "$(dirname "$dest")"
        cp "$file" "$dest"
    else
        echo "$dest already exists, skipping."
    fi
done

# Clean up
rm -rf /tmp/angular-build
