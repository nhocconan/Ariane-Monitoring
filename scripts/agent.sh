#!/bin/bash
#
# Ariane Agent, All data belongs to us! Bro.
#
# Version 0.3

KEY='INSERT_KEY_HERE';
VERSION='0.3';

IP=$(ip route get 8.8.8.8 | cut -d' ' -f8); IFS='\n' read -r -a array <<< "$IP"; IP=${array[0]};
NIC=$(ip route get 8.8.8.8 | cut -d' ' -f5); IFS='\n' read -r -a array <<< "$NIC"; NIC=${array[0]};
if [ "$IP" == "" ];  then
  IP="$(/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}')";
fi

UPTIME=$(cat /proc/uptime | cut -d ' ' -f1);
KERNEL=$(uname -r);
CPU=$(cat /proc/cpuinfo | grep 'model name' | cut -d: -f 2);
CPU_CORES=$(grep -c ^processor /proc/cpuinfo);
CPU_MHZ=$(cat /proc/cpuinfo | grep 'cpu MHz' | cut -d: -f2);
CPU_USAGE=$(top -bn2 | grep "Cpu(s)" | cut -d, -f1 | cut -d: -f2); #User Usage
CPU_USAGE_SYS=$(top -bn2 | grep "Cpu(s)" | cut -d, -f2); #System Usage
CPU_STEAL=$(top -bn2 | grep "Cpu(s)" | cut -d, -f8);
IO_WAIT=$(top -bn2 | grep "Cpu(s)" | cut -d, -f5);

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

RX="$(cat /sys/class/net/${NIC}/statistics/rx_bytes)"
TX="$(cat /sys/class/net/${NIC}/statistics/tx_bytes)";
if [ "$RX" == "" ];  then
  RX="$(cat /sys/class/net/eth0/statistics/rx_bytes)";
fi
if [ "$TX" == "" ];  then
  TX="$(cat /sys/class/net/eth0/statistics/tx_bytes)";
fi

curl -d "KEY=${KEY}&IP=${IP}&UPTIME=${UPTIME}&KERNEL=${KERNEL}&CPU=${CPU}&CPU_CORES=${CPU_CORES}&CPU_MHZ=${CPU_MHZ}&CPU_USAGE=${CPU_USAGE}&CPU_USAGE_SYS=${CPU_USAGE_SYS}&RAM_TOTAL=${RAM_TOTAL}&RAM_FREE=${RAM_FREE}&RAM_CACHED=${RAM_CACHED}
&RAM_BUFFER=${RAM_BUFFER}&RX=${RX}&TX=${TX}&RAM_ACTIVE=${RAM_ACTIVE}&RAM_INACTIVE=${RAM_INACTIVE}&HDD_TOTAL=${HDD_TOTAL}&HDD_USAGE=${HDD_USAGE}&CPU_STEAL=${CPU_STEAL}&IO_WAIT=${IO_WAIT}&VERSION=${VERSION}" https://yourdomain.net/API.php
