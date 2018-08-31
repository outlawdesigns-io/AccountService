#!/bin/bash

a2enmod rewrite
sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
service apache2 restart
mv /var/www/html/htaccess /var/www/html/.htaccess
