<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */
 
require_once __DIR__ . '/../../../../core/php/core.inc.php';

class SNMP extends eqLogic
{

    public static function pull($_options)
    {
        foreach (eqLogic::byType('SNMP') as $SNMP) {
            $SNMP->getInformations();
        }
    }

    public static function dependancy_info()
    {
        $return                  = array();
        $return['log']           = 'SNMP_dep';
        $return['progress_file'] = '/tmp/SNMP_dep';
        $snmpget                 = '/usr/bin/snmpget';
        $return['progress_file'] = '/tmp/SNMP_dep';
        if (is_file($snmpget)) {
            $return['state'] = 'ok';
        } else {
            exec('echo SNMP script dependency not found : ' . $snmpget . ' > ' . log::getPathToLog('SNMP_log') . ' 2>&1 &');
            $return['state'] = 'nok';
        }
        return $return;
    }

    public static function dependancy_install()
    {
        log::add('SNMP', 'info', 'Installation des dépéndances php5-snmp, snmp et snmp-mibs-downloader');
        $resource_path = realpath(dirname(__FILE__) . '/../../3rdparty');
        passthru('/bin/bash ' . $resource_path . '/install.sh ' . $resource_path . ' > ' . log::getPathToLog('SNMP_dep') . ' 2>&1 &');
    }
 
    public function postUpdate()
    {
        foreach (eqLogic::byType('SNMP') as $SNMP) {
            $SNMP->getInformations();
        }
    }

    /**
     * 
     * @return type
     */
    public function getInformations()
    {

        foreach ($this->getCmd() as $cmd) {
            $nas_ip         = $this->getConfiguration('ip');
            $snmp_community = $this->getConfiguration('snmp_community');
            $snmp_version   = $this->getConfiguration('snmp_version');
            $OID            = $cmd->getConfiguration('oid');
            $MIB            = $cmd->getConfiguration('mib');
            $type           = $cmd->getConfiguration('type');
            $value          = $cmd->getConfiguration('value');

            if ($type == "" || $type == "get") {
                if ($MIB == "") {
                    $OID    = escapeshellarg($OID);
                    log::add('SNMP', 'error', 'Envoi' . $OID);
                    $result = exec("snmpget -v $snmp_version -c $snmp_community $nas_ip '$OID'");
                    //log::add('SNMP', 'error', 'snmpget -v '.$snmp_version'. -c '.$snmp_community.' '.$nas_ip.' \''.$OID'\'');
                } else {
                    $result = exec("snmpget -v $snmp_version -m /usr/share/nginx/www/jeedom/plugins/SNMP/ressources/$MIB.txt -c $snmp_community -O qv $nas_ip $OID");
                }
            } else if ($type == "set") {
                if ($MIB == "") {
                    $result = exec("snmpset -v $snmp_version -c $snmp_community -O qv $nas_ip $OID");
                } else {
                    $result = exec("snmpset -v $snmp_version -m /usr/share/nginx/www/jeedom/plugins/SNMP/ressources/$MIB.txt -c $snmp_community -O qv $nas_ip $OID -i $value");
                }
            }

            $data = $result;
            $cmd->event($result);
        }
        return str_replace('"', '', $result);
    }

    public function postSave()
    {
        if ($this->getConfiguration('applyDevice') != $this->getConfiguration('device')) {
            $this->applyModuleConfiguration();
        }
    }

    public static function devicesParameters($_device = '')
    {
        $path = __DIR__ . '/../config/devices';
        if (isset($_device) && $_device != '') {
            $files = ls($path, $_device . '.json', false, array('files', 'quiet'));
            if (count($files) == 1) {
                try {
                    $content = file_get_contents($path . '/' . $files[0]);
                    if (is_json($content)) {
                        $deviceConfiguration = json_decode($content, true);
                        return $deviceConfiguration[$_device];
                    }
                } catch (\Exception $e) {
                    return array();
                }
            }
        }
        $files  = ls($path, '*.json', false, array('files', 'quiet'));
        $return = array();
        foreach ($files as $file) {
            try {
                $content = file_get_contents($path . '/' . $file);
                if (is_json($content)) {
                    $return = array_merge($return, json_decode($content, true));
                }
            } catch (Exception $e) {
                
            }
        }
        if (isset($_device) && $_device != '') {
            if (isset($return[$_device])) {
                return $return[$_device];
            }
            return array();
        }
        return $return;
    }

    public static function formatIp($_ip)
    {
        if (strpos($_ip, 'http') !== false) {
            return $_ip;
        }
        return 'http://' . $_ip;
    }

    /**
     * 
     * @return boolean
     */
    public function applyModuleConfiguration()
    {
        $this->setConfiguration('applyDevice', $this->getConfiguration('device'));
        if ($this->getConfiguration('device') == '') {
            $this->save();
            return true;
        }
        $device = self::devicesParameters($this->getConfiguration('device'));
        if (!is_array($device) || !isset($device['commands'])) {
            return true;
        }
        if (isset($device['configuration'])) {
            foreach ($device['configuration'] as $key => $value) {
                $this->setConfiguration($key, $value);
            }
        }

        $cmd_order = 0;
        $link_cmds = array();
        foreach ($device['commands'] as $command) {
            $cmd = null;
            foreach ($this->getCmd() as $liste_cmd) {
                if (isset($command['name']) && $liste_cmd->getName() == $command['name']) {
                    $cmd = $liste_cmd;
                    break;
                }
            }
            try {
                if ($cmd === null || !is_object($cmd)) {
                    $cmd = new SNMPCmd();
                    $cmd->setOrder($cmd_order);
                    $cmd->setEqLogic_id($this->getId());
                } else {
                    $command['name'] = $cmd->getName();
                }
                utils::a2o($cmd, $command);
                if (isset($command['value'])) {
                    $cmd->setValue(null);
                }
                $cmd->save();
                $cmd_order++;
            } catch (Exception $exc) {
                error_log($exc->getMessage());
            }
        }
        $this->save();
    }

    public static function shareOnMarket(&$market)
    {
        $deviceFile = __DIR__ . '/../config/devices/' . $market->getLogicalId() . '.json';
        if (!file_exists($deviceFile)) {
            throw new Exception('Impossible de trouver le fichier de conf ' . $deviceFile);
        }
        $tmp = __DIR__ . '/../../../../tmp/' . $market->getLogicalId() . '.zip';
        if (file_exists($tmp)) {
            if (!unlink($tmp)) {
                throw new Exception(__('Impossible de supprimer : ', __FILE__) . $tmp . __('. V\érifiez les droits', __FILE__));
            }
        }
        if (!create_zip($deviceFile, $tmp)) {
            throw new Exception(__('Echec de cr\éation du zip. R\épertoire source : ', __FILE__) . $deviceFile . __(' / R\épertoire cible : ', __FILE__) . $tmp);
        }

        return $tmp;
    }

    public static function getFromMarket(&$market, $_path)
    {
        $cibDir = __DIR__ . '/../config/devices/';
        if (!file_exists($cibDir)) {
            throw new \Exception(__('Impossible d\'installer la configuration du mod\èle le repertoire n\éxiste pas : ', __FILE__) . $cibDir);
        }
        $zip = new ZipArchive;
        if ($zip->open($_path) === true) {
            $zip->extractTo($cibDir . '/');
            $zip->close();
        } else {
            throw new \Exception('Impossible de d\écompresser le zip : ' . $_path);
        }
        $deviceFile = __DIR__ . '/../config/devices/' . $market->getLogicalId() . '.json';
        if (!file_exists($deviceFile)) {
            throw new \Exception(__('Echec de l\'installation. Impossible de trouver le mod\èle ', __FILE__) . $deviceFile);
        }

        foreach (eqLogic::byTypeAndSearhConfiguration('snmp', $market->getLogicalId()) as $eqLogic) {
            $eqLogic->applyModuleConfiguration();
        }
    }

    public static function removeFromMarket(&$market)
    {
        $deviceFile = __DIR__ . '/../config/devices/' . $market->getLogicalId() . '.json';
        if (!file_exists($deviceFile)) {
            throw new \Exception(__('Echec lors de la suppression. Impossible de trouver le mod\èle ', __FILE__) . $deviceFile);
        }
        if (!unlink($deviceFile)) {
            throw new \Exception(__('Impossible de supprimer le fichier :  ', __FILE__) . $deviceFile . '. Veuillez v\érifier les droits');
        }
    }

    public static function listMarketObject()
    {
        $return = array();
        foreach (SNMP::devicesParameters() as $logical_id => $name) {
            $return[] = $logical_id;
        }
        return $return;
    }

}

class SNMPCmd extends cmd
{
    public function execute($options = null)
    {

        $SNMP           = $this->getEqLogic();
        $nas_ip         = $SNMP->getConfiguration('ip');
        $snmp_community = $SNMP->getConfiguration('snmp_community');
        $snmp_version   = $SNMP->getConfiguration('snmp_version');
        $OID            = $this->getConfiguration('oid');
        $MIB            = $this->getConfiguration('mib');
        if ($MIB == "") {
            $OID    = escapeshellarg($OID);
            log::add('SNMP', 'error', 'Envoi' . $OID);
            $result = exec("snmpget -v $snmp_version -c $snmp_community $nas_ip '$OID'");
        } else {
            $result = exec("snmpget -v $snmp_version -m /usr/share/nginx/www/jeedom/plugins/SNMP/ressources/$MIB.txt -c $snmp_community -O qv $nas_ip $OID");
        }
        //$result = str_replace("\"","",$result);
        return str_replace('"', '', $result);
    }

}
