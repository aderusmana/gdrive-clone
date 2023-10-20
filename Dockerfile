# Use an official PHP runtime as a parent image
FROM php:8.0-fpm

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the current directory contents into the container at /var/www/html
COPY . /var/www/html

# Install any needed packages specified in requirements.txt
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip

# Make port 80 available to the world outside this container
EXPOSE 80

# Define environment variables
ENV WWWUSER=${WWWUSER} \
    WWWGROUP=${WWWGROUP} \
    LARAVEL_SAIL=1 \
    XDEBUG_MODE=${SAIL_XDEBUG_MODE:-off} \
    XDEBUG_CONFIG=${SAIL_XDEBUG_CONFIG:-client_host=host.docker.internal} \
    IGNITION_LOCAL_SITES_PATH=${PWD}

# Start the PHP-FPM server
CMD ["php-fpm"]

# You may need to add more configurations depending on your Laravel application
