Originally backup-manager cannot clean S3 bucket you're storing your backups to.

All you need to do is just to install Zend Framework and add this line

    php -f /path/to/backup-manager-s3-cleaner/cleaner.php
    
to your crontab.

The script takes all the configuration from `/etc/backup-manager.conf` so you don't need to configure it.