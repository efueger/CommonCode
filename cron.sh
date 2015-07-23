#!/bin/sh
sudo -u apache php -d memory_limit=-1 cron.php
