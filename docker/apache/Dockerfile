FROM httpd:2.4-bookworm

# Update packages and install vim
RUN apt-get update && apt-get dist-upgrade -y && apt-get -y install vim && apt-get clean

# Enable necessary Apache modules
RUN sed -i '/LoadModule rewrite_module/s/^#//g' /usr/local/apache2/conf/httpd.conf && \
    sed -i '/LoadModule headers_module/s/^#//g' /usr/local/apache2/conf/httpd.conf && \
    sed -i '/LoadModule proxy_module/s/^#//g' /usr/local/apache2/conf/httpd.conf && \
    sed -i '/LoadModule proxy_fcgi_module/s/^#//g' /usr/local/apache2/conf/httpd.conf && \
    sed -i '/LoadModule setenvif_module/s/^#//g' /usr/local/apache2/conf/httpd.conf && \
    sed -i '/LoadModule ssl_module/s/^#//g' /usr/local/apache2/conf/httpd.conf && \
    sed -i '/LoadModule socache_shmcb_module/s/^#//g' /usr/local/apache2/conf/httpd.conf


# Set a global ServerName to suppress warnings
RUN echo "ServerName epimanager.episciences.org" >> /usr/local/apache2/conf/httpd.conf

# Include custom VirtualHost configuration
RUN echo "Include conf/extra/episciences-manager.conf" >> /usr/local/apache2/conf/httpd.conf

# Copy entrypoint script
COPY ./docker/apache/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Listen on additional ports
RUN echo "Listen 80" >> /usr/local/apache2/conf/httpd.conf
RUN echo "Listen 443" >> /usr/local/apache2/conf/httpd.conf

# Copy SSL certificates into the expected ssl directory
COPY docker/apache/ssl/server.crt /usr/local/apache2/ssl/server.crt
COPY docker/apache/ssl/server.key /usr/local/apache2/ssl/server.key

# Configure SSL session cache for performance
RUN echo "SSLSessionCache shmcb:/usr/local/apache2/logs/ssl_scache(512000)" >> /usr/local/apache2/conf/extra/httpd-ssl.conf && \
    echo "SSLSessionCacheTimeout 300" >> /usr/local/apache2/conf/extra/httpd-ssl.conf

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["httpd-foreground"]
