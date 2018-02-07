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
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<div id='div_updateSNMPAlert' style="display: none;"></div>
<pre id='preSNMPupdate' style='overflow: auto; height: 90%;with:90%;'></pre>


<script>
	$.ajax({
		type: 'POST',
		url: 'plugins/SNMP/core/ajax/SNMP.ajax.php',
		data: {
			action: 'updateSNMP'
		},
		dataType: 'json',
		global: false,
		error: function (request, status, error) {
			handleAjaxError(request, status, error, $('#div_updateSNMPAlert'));
		},
		success: function () {
			getkodiLog(1);
		}
	});
	function getkodiLog(_autoUpdate) {
		$.ajax({
			type: 'POST',
			url: 'core/ajax/log.ajax.php',
			data: {
				action: 'get',
				logfile: 'SNMP_update',
			},
			dataType: 'json',
			global: false,
			error: function (request, status, error) {
				setTimeout(function () {
					getJeedomLog(_autoUpdate, _log)
				}, 1000);
			},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_alert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				var log = '';
				var regex = /<br\s*[\/]?>/gi;
				for (var i in data.result.reverse()) {
					log += data.result[i][2].replace(regex, "\n");
				}
				$('#pre_SNMPupdate').text(log);
				$('#pre_SNMPupdate').scrollTop($('#pre_SNMPupdate').height() + 200000);
				if (!$('#pre_SNMPupdate').is(':visible')) {
					_autoUpdate = 0;
				}
				if (init(_autoUpdate, 0) == 1) {
					setTimeout(function () {
						getkodiLog(_autoUpdate)
					}, 1000);
				}
			}
		});
	}
</script>
