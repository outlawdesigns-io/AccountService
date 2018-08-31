FROM ulsmith/rpi-raspbian-apache-php
ADD ./ /var/www/html
RUN chmod -R 0755 /var/www/html
RUN rm /var/www/html/index.html
EXPOSE 80
CMD ["/run.sh"]
