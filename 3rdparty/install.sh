#!/bin/bash
cd "$(dirname "$0")"
echo "########### Installation en cours ##########"
sudo apt-get update  -y -q
sudo apt-get install -y php5-snmp snmp snmp-mibs-downloader
sudo download-mibs
echo "########### Fin ##########"
