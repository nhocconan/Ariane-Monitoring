# Ariane-Monitoring

Ariane-Monitoring is licensed under a
Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.

You should have received a copy of the license along with this
work. If not, see https://creativecommons.org/licenses/by-nc-sa/4.0/

![alt tag](https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Cc-by-nc-sa_icon.svg/120px-Cc-by-nc-sa_icon.svg.png)

![alt tag](http://i.imgur.com/xkuf6q5.png)

Quick Setup:

- Create a Database with a User, Import the ariane.sql file
- Update /pages/functions.php with your mysql login details, your timezone and your email for alerts.
- Update agent.sh and install.sh with your URL (wget & curl)
- Edit /pages/create_account.php, remove the exit()
- Run URL/pages/create_account.php in your Browser.
- Login with the Details from create_account.php
- Delete create_account.php or add the exit() again.
- Add the cron.php to your Crontab
