#!/bin/bash

# Create a file with content
echo "This is the content of server.crt" > /etc/ssl/certs/server.crt

# Create another file with content
echo "This is the content of server.key" > /etc/ssl/certs/server.key

# Ensure proper permissions
chmod 600 /etc/ssl/certs/server.crt /etc/ssl/certs/server.key

chrom -R 755 *
