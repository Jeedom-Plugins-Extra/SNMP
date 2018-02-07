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

global $listCmdSNMP;
$listCmdSNMP = array(
    array(
        'name' => 'Temps d\'activité',
	'logicalId' => 'systemUPTIME',
        'configuration' => array(
            'mib' => '',
            'oid' => '1.3.6.1.2.1.1.3.0',
        ),
        'type' => 'info',
        'subType' => 'string',
        'description' => 'Affiche le temps d\'activité du matériel en JJ:HH:MM:SS:MS',
        'version' => '0.1',
        'required' => '',
    ),
    array(
        'name' => 'CPU Load (1 minute)',
	'logicalId' => 'cpu1minuteLOAD',
        'configuration' => array(
            'mib' => '',
            'oid' => '.1.3.6.1.4.1.2021.10.1.3.1',
        ),
        'type' => 'info',
        'subType' => 'numeric',
        'description' => 'Affiche la charge CPU sur la dernière minute',
        'version' => '0.1',
        'required' => '',
    ),
    array(
        'name' => 'CPU Load (5 minutes)',
	'logicalId' => 'cpu5minuteLOAD',
        'configuration' => array(
            'mib' => '',
            'oid' => '.1.3.6.1.4.1.2021.10.1.3.2',
        ),
        'type' => 'info',
        'subType' => 'numeric',
        'description' => 'Affiche la charge CPU sur les cinq dernières minutes',
        'version' => '0.1',
        'required' => '',
    ),
    array(
        'name' => 'CPU Load (15 minutes)',
	'logicalId' => 'cpu15minuteLOAD',
        'configuration' => array(
            'mib' => '',
            'oid' => '.1.3.6.1.4.1.2021.10.1.3.3',
        ),
        'type' => 'info',
        'subType' => 'numeric',
        'description' => 'Affiche la charge CPU sur les quinze dernières minutes',
        'version' => '0.1',
        'required' => '',
    ),
    array(
        'name' => 'Space Used',
	'logicalId' => 'percentageOFspaceUSEDonDISK',
        'configuration' => array(
            'mib' => '',
            'oid' => '.1.3.6.1.4.1.2021.9.1.9.1',
        ),
        'type' => 'info',
        'subType' => 'numeric',
        'description' => 'Affiche le pourcentage d\'espace disque utilisé',
	'unite' => '%',
        'version' => '0.1',
        'required' => '',
    ),
    array(
        'name' => 'Total RAM',
	'logicalId' => 'totalRAMinMACHINE',
        'configuration' => array(
            'mib' => '',
            'oid' => '.1.3.6.1.4.1.2021.4.5.0',
        ),
        'type' => 'info',
        'subType' => 'numeric',
        'description' => 'Affiche la quantité de RAM totale',
	'unite' => 'Mo',
        'version' => '0.1',
        'required' => '',
    ),
    array(
        'name' => 'RAM Used',
	'logicalId' => 'OIDtotalRAMused',
        'configuration' => array(
            'mib' => '',
            'oid' => '.1.3.6.1.4.1.2021.4.6.0',
        ),
        'type' => 'info',
        'subType' => 'numeric',
        'description' => 'Affiche la quantité de RAM utilisée',
	'unite' => 'Mo',
        'version' => '0.1',
        'required' => '',
    ),
);

?>
