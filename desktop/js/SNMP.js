
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

$('body').delegate('#bt_getFromMarket', 'click', function () {
	$('#md_modal').dialog({title: "{{Partager sur le market}}"});
	$('#md_modal').load('index.php?v=d&modal=market.list&type=snmp').dialog('open');
});

$('body').delegate('#bt_shareOnMarket', 'click', function () {
	var logicalId = $('.eqLogicAttr[data-l1key=configuration][data-l2key=device]').value();
	if (logicalId == '') {
	$('#div_alert').showAlert({message: '{{Vous devez d\'abord séléctionner une configuration à partager}}', level: 'danger'});
	return;
}
$('#md_modal').dialog({title: "{{Partager sur le market}}"});

$('#md_modal').load('index.php?v=d&modal=market.send&type=SNMP&logicalId=' + encodeURI(logicalId) + '&name=' + encodeURI($('.eqLogicAttr[data-l1key=configuration][data-l2key=device] option:selected').text())).dialog('open');
});

$('#bt_uploadConfSNMP').fileupload({
    replaceFileInput: true,
    dataType: 'json',
    done: function(e, data) {
        if (data.result.state != 'ok') {
            $('#div_alert').showAlert({message: data.result.result, level: 'danger'});
            return;
        }
        if (modifyWithoutSave) {
            $('#div_alert').showAlert({message: '{{Fichier ajouté avec succes. Vous devez rafraichir pour vous en servir}}', level: 'success'})
        } else {
            window.location.reload();
        }
    }
});

$(function() {
    $("#table_cmd tbody").delegate(".listCmdSNMP", 'click', function(event) {
        $('.description').hide();
        $('.version').hide();
        $('.required').hide();
        $('.description.' + $('#sel_addPreConfigCmdSNMP').value()).show();
        $('.version.' + $('#sel_addPreConfigCmdSNMP').value()).show();
        $('.required.' + $('#sel_addPreConfigCmdSNMP').value()).show();
        $('#md_addPreConfigCmdSNMP').modal('show');
        $('#bt_addPreConfigCmdSNMPSave').undelegate().unbind();
        var tr = $(this).closest('tr');
		$("#div_mainContainer").delegate("#bt_addPreConfigCmdSNMPSave", 'click', function(event) {
            var cmd_data = json_decode($('.json_cmd.' + $('#sel_addPreConfigCmdSNMP').value()).value());
            tr.setValues(cmd_data, '.cmdAttr');
			$('#md_addPreConfigCmdSNMP').modal('hide');
        });
    });

    $("#sel_addPreConfigCmdSNMP").on('change', function(event) {
        $('.description').hide();
        $('.version').hide();
        $('.required').hide();
        $('.description.' + $(this).value()).show();
        $('.version.' + $(this).value()).show();
        $('.required.' + $(this).value()).show();
    });

    $("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
});

function addCmdToTable(_cmd) {
     if (!isset(_cmd)) {
        var _cmd = {};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td class="name">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name"></td>';
    tr += '<td class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType();
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span></td>';
    tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="mib" style="margin-top : 5px;" placeholder="{{Nom du fichier MIB à utiliser, peut être vide}}" >';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="oid" style="margin-top : 5px;" placeholder="{{OID}}">';
		tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="configuration" data-l2key="set" checked>{{Set}}<br/></span>';
		tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="configuration" data-l2key="value" style="margin-top : 5px;" placeholder="{{Valeur à envoyer, lié au set}}">';
    tr += '<a class="btn btn-default listCmdSNMP form-control input-sm" style="margin-top : 5px;"><i class="fa fa-list-alt cursor"></i> {{Ajouter une commande prédéfinie}}</a>';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
	tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked>{{Afficher}}<br/></span>';
    if (_cmd.type!="numeric") {
	tr += '<br>';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" placeholder="{{Unité}}">';
	tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isHistorized"/> {{Historiser}}<br/></span>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
}
