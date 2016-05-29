#!/bin/bash
#
# Ariane Agent, All data belongs to us! Bro.
#
# Version 0.1

KEY='INSERT_KEY_HERE';

IP="$(/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}')";
if [ "$IP" == "" ];  then
  IP="$(/sbin/ifconfig venet0:0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}')";
fi
UPTIME=$(cat /proc/uptime | awk '{ print $1 }');
KERNEL=$(uname -r);
CPU=$(cat /proc/cpuinfo | grep 'model name' | awk -F\: '{ print $2 }' | cut -d$'\n' -f1);
CPU_CORES=$(grep -c ^processor /proc/cpuinfo);
CPU_MHZ=$(cat /proc/cpuinfo | grep 'cpu MHz' | awk -F\: '{ print $2 }' | cut -d$'\n' -f1);
CPU_USAGE=$(top -bn2 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/" | awk '{print 100 - $1""}' | cut -d$'\n' -f2);
CPU_STEAL=$(top -bn2 | grep "Cpu(s)" | cut -d$'\n' -f2 | cut -d ',' -f8 | cut -d "s" -f1);
IO_WAIT=$(top -bn2| awk -F"," '/Cpu/{if(p==0){p=1}else{split($5,a,"%");print a[1]}}');

RAM_TOTAL=$(cat /proc/meminfo | grep ^MemTotal: | awk '{ print $2 }');
RAM_FREE=$(cat /proc/meminfo | grep ^MemFree: | awk '{ print $2 }');
RAM_CACHED=$(cat /proc/meminfo | grep ^Cached: | awk '{ print $2 }');
RAM_BUFFER=$(cat /proc/meminfo | grep ^Buffers: | awk '{ print $2 }');
RAM_ACTIVE=$(cat /proc/meminfo | grep ^Active: | awk '{ print $2 }');
RAM_INACTIVE=$(cat /proc/meminfo | grep ^Inactive: | awk '{ print $2 }');

HDD_TOTAL=$(df -P -B 1 | grep '^/' | awk '{ print $2 }' | sed -e :a -e '$!N;s/\n/+/;ta');
HDD_USAGE=$(df -P -B 1 | grep '^/' | awk '{ print $3 }' | sed -e :a -e '$!N;s/\n/+/;ta');

HDD_TOTAL=$((${HDD_TOTAL}));
HDD_USAGE=$((${HDD_USAGE}));

RX="$(cat /sys/class/net/eth0/statistics/rx_bytes)";
if [ "$RX" == "" ];  then
  RX="$(cat /sys/class/net/venet0/statistics/rx_bytes)";
fi
TX="$(cat /sys/class/net/eth0/statistics/tx_bytes)";
if [ "$TX" == "" ];  then
  TX="$(cat /sys/class/net/venet0/statistics/tx_bytes)";
fi

curl -d "KEY=${KEY}&IP=${IP}&UPTIME=${UPTIME}&KERNEL=${KERNEL}&CPU=${CPU}&CPU_CORES=${CPU_CORES}&CPU_MHZ=${CPU_MHZ}&CPU_USAGE=${CPU_USAGE}&RAM_TOTAL=${RAM_TOTAL}&RAM_FREE=${RAM_FREE}&RAM_CACHED=${RAM_CACHED}&RAM_BUFFER=${RAM_BUFFER}&RX=${RX}&TX=${TX}&RAM_ACTIVE=${RAM_ACTIVE}&RAM_INACTIVE=${RAM_INACTIVE}&HDD_TOTAL=${HDD_TOTAL}&HDD_USAGE=${HDD_USAGE}&CPU_STEAL=${CPU_STEAL}&IO_WAIT=${IO_WAIT}" https://yourdomain.net/API.php
