#!/bin/bash
echo "********************************************************"
echo "*             Installation des dépendances             *"
echo "********************************************************"
sudo apt-get update  -y -q
sudo apt-get install snmpd
PS3='Comment utilisez-vous LIRC ?'   # le prompt
options=("[1] En local sur un Raspberry avec Jeedom avec un emetteur différent du USB-UIRT" "[2] En local sur un DEBIAN/UBUNTU avec Jeedom avec un emetteur différent du USB-UIRT" "[3] En local sur un Raspberry avec Jeedom avec un emetteur USB-UIRT" "[4] En local sur un DEBIAN/JEEDOM avec Jeedom avec un emetteur USB-UIRT" "[5] En distant sur un RASPBERRY avec un emetteur différent du USB-UIRT" "[6] En distant sur un DEBIAN/UBUNTU avec un emetteur différent du USB-UIRT" "[7] En distant sur un RASPBERRY avec un emetteur USB-UIRT" "[8] En distant sur un UBUNTU/DEBIAN avec un emetteur USB-UIRT")
select opt in "${options[@]}" 
do
    case $opt in
        "[1] En local sur un Raspberry avec Jeedom avec un emetteur différent du USB-UIRT")
        	echo "INSTALLATION LOCALE SUR RASPBERRY, VOUS DEVREZ CONFIGURER MANUELLEMENT LE FICHIER /etc/lirc/hardware.conf"
        	sudo cp /usr/share/nginx/www/jeedom/plugins/lirc/ressources/lirc_web /etc/init.d/lirc_web
        	echo "INSTALLATION Nodejs Raspberry"
        	sudo wget http://node-arm.herokuapp.com/node_latest_armhf.deb 
			sudo dpkg -i node_latest_armhf.deb
        	break
        	;;
        "[2] En local sur un DEBIAN/UBUNTU avec Jeedom avec un emetteur différent du USB-UIRT")
        	echo "INSTALLATION LOCALE SUR DEBIAN/UBUNTU, VOUS DEVREZ CONFIGURER MANUELLEMENT LE FICHIER /etc/lirc/hardware.conf"
        	sudo cp /usr/share/nginx/www/jeedom/plugins/lirc/ressources/lirc_web /etc/init.d/lirc_web
        	echo "INSTALLATION NodeJS Ubuntu/Debian"
        	sudo curl -sL https://deb.nodesource.com/setup | bash -
			sudo apt-get install -y nodejs
        	break
        	;;
        "[3] En local sur un Raspberry avec Jeedom avec un emetteur USB-UIRT")
        	echo "INSTALLATION LOCALE SUR RASPBERRY, CONFIGURATION AUTO DU FICHIER /etc/lirc/hardware.conf"
        	sudo cp /usr/share/nginx/www/jeedom/plugins/lirc/ressources/lirc_web /etc/init.d/lirc_web
        	echo "INSTALLATION Nodejs Raspberry"
        	sudo wget http://node-arm.herokuapp.com/node_latest_armhf.deb 
			sudo dpkg -i node_latest_armhf.deb
			echo "Port USB du USBUIRT (/dev/ttyUSBx)"
        	read reponse
        	cat >/etc/lirc/hardware.conf <<EOL
# /etc/lirc/hardware.conf
#
# Arguments which will be used when launching lircd
LIRCD_ARGS="-d ${reponse}"

#Don't start lircmd even if there seems to be a good config file
#START_LIRCMD=false

#Don't start irexec, even if a good config file seems to exist.
#START_IREXEC=false

#Try to load appropriate kernel modules
LOAD_MODULES=true

# Run "lircd --driver=help" for a list of supported drivers.
DRIVER="uirt2_raw"
# usually /dev/lirc0 is the correct setting for systems using udev
DEVICE="${reponse}"
MODULES="lirc_rpi"

# Default configuration files for your hardware if any
LIRCD_CONF=""
LIRCMD_CONF=""
EOL
        	break
        	;;
        "[4] En local sur un DEBIAN/JEEDOM avec Jeedom avec un emetteur USB-UIRT")
        	echo "INSTALLATION LOCALE SUR DEBIAN/UBUNTU, CONFIGURATION AUTO DU FICHIER /etc/lirc/hardware.conf"
        	sudo cp /usr/share/nginx/www/jeedom/plugins/lirc/ressources/lirc_web /etc/init.d/lirc_web
        	echo "INSTALLATION NodeJS Ubuntu/Debian"
        	sudo curl -sL https://deb.nodesource.com/setup | bash -
			sudo apt-get install -y nodejs
			echo "Port USB du USBUIRT (/dev/ttyUSBx)"
        	read reponse
        	cat >/etc/lirc/hardware.conf <<EOL
# /etc/lirc/hardware.conf
#Chosen Remote Control
REMOTE="Custom"
REMOTE_MODULES=""
REMOTE_DRIVER="usb_uirt_raw"
REMOTE_DEVICE="${reponse}"
REMOTE_LIRCD_CONF=""
REMOTE_LIRCD_ARGS=""

#Chosen IR Transmitter
TRANSMITTER="USB_UIRT"
TRANSMITTER_MODULES=""
TRANSMITTER_DRIVER="usb_uirt_raw"
TRANSMITTER_DEVICE="${reponse}"
#TRANSMITTER_LIRCD_CONF=""
TRANSMITTER_LIRCD_ARGS="-d ${reponse}"

#Enable lircd
START_LIRCD="true"

#Don’t start lircmd even if there seems to be a good config file
#START_LIRCMD="false"

#Try to load appropriate kernel modules
#LOAD_MODULES="true"
LOAD_MODULES="false"

# Default configuration files for your hardware if any
LIRCMD_CONF=""

#Forcing noninteractive reconfiguration
#If lirc is to be reconfigured by an external application
#that doesn’t have a debconf frontend available, the noninteractive
#frontend can be invoked and set to parse REMOTE and TRANSMITTER
#It will then populate all other variables without any user input
#If you would like to configure lirc via standard methods, be sure
#to leave this set to “false”
FORCE_NONINTERACTIVE_RECONFIGURATION="false"
START_LIRCMD=""
EOL
        	break
        	;;
        "[5] En distant sur un RASPBERRY avec un emetteur différent du USB-UIRT")
        	echo "INSTALLATION DISTANTE SUR RASPBERRY, VOUS DEVREZ CONFIGURER MANUELLEMENT LE FICHIER /etc/lirc/hardware.conf"
        	echo "Adresse IP de Jeedom"
        	read reponse
        	sudo wget -O /etc/init.d/lirc_web "http://$reponse/jeedom/plugins/lirc/ressources/lirc_web"
        	echo "INSTALLATION Nodejs Raspberry"
        	sudo wget http://node-arm.herokuapp.com/node_latest_armhf.deb 
			sudo dpkg -i node_latest_armhf.deb
        	break
        	;;
        "[6] En distant sur un DEBIAN/UBUNTU avec un emetteur différent du USB-UIRT")
        	echo "INSTALLATION DISTANTE SUR DEBIAN/UBUNTU, VOUS DEVREZ CONFIGURER MANUELLEMENT LE FICHIER /etc/lirc/hardware.conf"
        	echo "Adresse IP de Jeedom"
        	read reponse
        	sudo wget -O /etc/init.d/lirc_web "http://$reponse/jeedom/plugins/lirc/ressources/lirc_web"
        	echo "INSTALLATION NodeJS Ubuntu/Debian"
        	sudo curl -sL https://deb.nodesource.com/setup | bash -
			sudo apt-get install -y nodejs
        	break
        	;;
        "[7] En distant sur un RASPBERRY avec un emetteur USB-UIRT")
        	echo "INSTALLATION DISTANTE SUR RASPBERRY, CONFIGURATION AUTO DU FICHIER /etc/lirc/hardware.conf"
        	echo "Adresse IP de Jeedom"
        	read reponse
        	sudo wget -O /etc/init.d/lirc_web "http://$reponse/jeedom/plugins/lirc/ressources/lirc_web"
        	echo "INSTALLATION Nodejs Raspberry"
        	sudo wget http://node-arm.herokuapp.com/node_latest_armhf.deb 
			sudo dpkg -i node_latest_armhf.deb
			echo "Port USB du USBUIRT (/dev/ttyUSBx)"
        	read reponse
        	cat >/etc/lirc/hardware.conf <<EOL
# /etc/lirc/hardware.conf
#
# Arguments which will be used when launching lircd
LIRCD_ARGS="-d ${reponse}"

#Don't start lircmd even if there seems to be a good config file
#START_LIRCMD=false

#Don't start irexec, even if a good config file seems to exist.
#START_IREXEC=false

#Try to load appropriate kernel modules
LOAD_MODULES=true

# Run "lircd --driver=help" for a list of supported drivers.
DRIVER="uirt2_raw"
# usually /dev/lirc0 is the correct setting for systems using udev
DEVICE="${reponse}"
MODULES="lirc_rpi"

# Default configuration files for your hardware if any
LIRCD_CONF=""
LIRCMD_CONF=""
EOL
        	break
        	;;
        "[8] En distant sur un UBUNTU/DEBIAN avec un emetteur USB-UIRT")
        	echo "INSTALLATION DISTANTE SUR DEBIAN/UBUNTU, CONFIGURATION AUTO DU FICHIER /etc/lirc/hardware.conf"
        	echo "Adresse IP de Jeedom"
        	read reponse
        	sudo wget -O /etc/init.d/lirc_web "http://$reponse/jeedom/plugins/lirc/ressources/lirc_web"
        	echo "INSTALLATION NodeJS Ubuntu/Debian"
        	sudo curl -sL https://deb.nodesource.com/setup | bash -
			sudo apt-get install -y nodejs
			echo "Port USB du USBUIRT (/dev/ttyUSBx)"
        	read reponse
        	cat >/etc/lirc/hardware.conf <<EOL
# /etc/lirc/hardware.conf
#Chosen Remote Control
REMOTE="Custom"
REMOTE_MODULES=""
REMOTE_DRIVER="usb_uirt_raw"
REMOTE_DEVICE="${reponse}"
REMOTE_LIRCD_CONF=""
REMOTE_LIRCD_ARGS=""

#Chosen IR Transmitter
TRANSMITTER="USB_UIRT"
TRANSMITTER_MODULES=""
TRANSMITTER_DRIVER="usb_uirt_raw"
TRANSMITTER_DEVICE="${reponse}"
#TRANSMITTER_LIRCD_CONF=""
TRANSMITTER_LIRCD_ARGS="-d ${reponse}"

#Enable lircd
START_LIRCD="true"

#Don’t start lircmd even if there seems to be a good config file
#START_LIRCMD="false"

#Try to load appropriate kernel modules
#LOAD_MODULES="true"
LOAD_MODULES="false"

# Default configuration files for your hardware if any
LIRCMD_CONF=""

#Forcing noninteractive reconfiguration
#If lirc is to be reconfigured by an external application
#that doesn’t have a debconf frontend available, the noninteractive
#frontend can be invoked and set to parse REMOTE and TRANSMITTER
#It will then populate all other variables without any user input
#If you would like to configure lirc via standard methods, be sure
#to leave this set to “false”
FORCE_NONINTERACTIVE_RECONFIGURATION="false"
START_LIRCMD=""
EOL
        	break
        	;;        
        *) echo invalid option $opt;;
    esac
done
echo "********************************************************"
echo "*             Installation de LircWeb                  *"
echo "********************************************************"
sudo mkdir /opt
sudo git clone git://github.com/alexbain/lirc_web.git /opt/lirc_web
cd /opt/lirc_web
sudo npm install
sudo chmod +x /etc/init.d/lirc_web
sudo update-rc.d lirc_web defaults
sudo /etc/init.d/lirc_web stop
sudo /etc/init.d/lirc_web start
echo "********************************************************"
echo "*             Installation terminée                    *"
echo "********************************************************"
