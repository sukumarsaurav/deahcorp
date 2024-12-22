#!/bin/bash

# Create uploads directory structure
mkdir -p uploads/portfolio

# Set directory permissions
chmod 755 uploads
chmod 755 uploads/portfolio

# Set ownership (replace www-data with your web server user)
chown -R www-data:www-data uploads

echo "Upload directories created with proper permissions" 