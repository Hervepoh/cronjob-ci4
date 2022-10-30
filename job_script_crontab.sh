#!/bin/bash

cd /mnt/c/appStore/CI4/cronjob/public && /mnt/c/xampp/php/php.exe index.php tasks

# crontab -l
# crontab -e

# */1 * * * * /mnt/c/appStore/CI4/cronjob/job_script_crontab.sh
# The best practice is
# * * * * * cd /path-to-your-project && php spark cronjob:run >> /dev/null 2>&1
# sudo service cron restart
# tail var/log/syslog

