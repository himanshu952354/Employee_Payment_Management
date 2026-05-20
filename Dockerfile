FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    wget \
    gnupg

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application files
COPY . .

# Install PHP and Node dependencies
RUN composer install --optimize-autoloader --no-dev
RUN npm install
RUN npm run build

# Expose port and start Laravel server
EXPOSE 8000
CMD touch database/database.sqlite && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
