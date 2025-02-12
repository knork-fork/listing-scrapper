FROM phpdockerio/php:8.4-fpm
WORKDIR "/application"

# Install dependencies for PHP and Tor
RUN apt-get update && apt-get install -y \
    tor \
    curl \
    netcat-openbsd \
    php-curl \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Configure Tor to allow ControlPort
RUN echo "ControlPort 9051" >> /etc/tor/torrc && \
    echo "CookieAuthentication 0" >> /etc/tor/torrc

# Copy Supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Ensure Supervisor runs and manages Tor and PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
