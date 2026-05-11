# Menggunakan PHP 8.4 dengan Apache sebagai web server
FROM php:8.4-apache

# Menginstal dependensi sistem yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Membersihkan cache apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Menginstal ekstensi PHP (pdo_mysql dan mbstring). 
# Catatan: curl, openssl, dan json sudah otomatis bawaan dari base image ini.
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Mengaktifkan mod_rewrite Apache (wajib untuk routing Laravel)
RUN a2enmod rewrite

# Mengubah DocumentRoot Apache ke folder /public milik Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Menginstal Composer versi terbaru (2.x)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Menetapkan direktori kerja di dalam container
WORKDIR /var/www/html

# Menyalin seluruh file project ke dalam container
COPY . .

# Menginstal dependensi PHP via Composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Mengatur hak akses agar Laravel bisa menulis log dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Mengekspos port 80
EXPOSE 80
