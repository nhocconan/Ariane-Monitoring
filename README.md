# Ariane-Monitoring

Ariane-Monitoring is licensed under a
Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.

You should have received a copy of the license along with this
work. If not, see https://creativecommons.org/licenses/by-nc-sa/4.0/

<a href="https://codeclimate.com/github/Ne00n/Ariane-Monitoring"><img src="https://codeclimate.com/github/Ne00n/Ariane-Monitoring/badges/gpa.svg" /></a>
[![Build Status](https://travis-ci.org/Ne00n/Ariane-Monitoring.svg?branch=master)](https://travis-ci.org/Ne00n/Ariane-Monitoring)

![alt tag](https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Cc-by-nc-sa_icon.svg/120px-Cc-by-nc-sa_icon.svg.png)

Server Overview:
![alt tag](http://i.imgur.com/RqAbntO.jpg)

Network History
![alt tag](http://i.imgur.com/VBE16ig.jpg)

Triggers (We will adding more)
![alt tag](http://i.imgur.com/XAXBCP8.jpg)

Well if you dont like the Theme, you can replace it with one from: https://bootswatch.com/

We have already some Theme's included, Default Theme: Flatly

Quick Setup:

- Create a Database, add a User to the Database, Import the sql/ariane.sql file
- Update /pages/config.php with your MySQL login details, your Timezone and your email for alerts.
- Update /scripts/agent.sh and /scripts/install.sh with your URL (wget & curl)
- Edit /tools/create_account.php, remove the exit()
- Run URL/tools/create_account.php in your Browser.
- Login with the Details from create_account.php
- Delete create_account.php or add the exit() again.
- Add the cron.php to your Crontab, like:  "* * * * * /usr/bin/php /var/www/ariane/cron.php"

Wallpaper used: http://aquafugit.deviantart.com/art/Ubuntu-Grunge-165623955

Quick Removal:

- rm /var/spool/cron/crontabs/ariane_agent
- pkill -u ariane_agent
- userdel ariane_agent
- rm -r /etc/ariane_agent
