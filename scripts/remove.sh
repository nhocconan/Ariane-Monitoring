#!/bin/bash
#
# Ariane Removal Tool
#
# Version 0.1

pkill -u ariane_agent
userdel ariane_agent
rm /var/spool/cron/crontabs/ariane_agent
rm -r /etc/ariane_agent
