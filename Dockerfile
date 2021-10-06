FROM outlawstar4761/rpi-raspbian-apache-php
ENV TZ=America/Chicago
ADD ./ /var/www/html
RUN chmod -R 0755 /var/www/html
RUN rm /var/www/html/index.html
RUN chmod +x /var/www/html/Libs/ContainerSetup/webContainerSetup.sh
RUN /var/www/html/Libs/ContainerSetup/webContainerSetup.sh /mnt/LOE/log/accounts.access.log
EXPOSE 443
CMD ["/run.sh"]
