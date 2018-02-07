<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

global $listCmdSNMP;
include_file('core', 'SNMP', 'config', 'SNMP');
include_file('3rdparty', 'jquery.fileupload/jquery.ui.widget', 'js');
include_file('3rdparty', 'jquery.fileupload/jquery.iframe-transport', 'js');
include_file('3rdparty', 'jquery.fileupload/jquery.fileupload', 'js');
sendVarToJS('eqType', 'SNMP');
sendVarToJS('marketAddr', config::byKey('market::address'));
$eqLogics = eqLogic::byType('SNMP');
?>

<div class="row row-overflow">
    <div class="col-lg-2">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
		<a class="btn btn-default btn-sm tooltips" id="bt_getFromMarket" title="Récuperer du market" style="width : 100%;"><i class="fa fa-shopping-cart"></i> {{Market}}</a>
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un équipement}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('SNMP') as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName() . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>

  <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend>{{Mes équipements SNMP}}
            <span style="font-size: 0.7em;color:#c5c5c5">
                Vous devez être connecté à internet pour voir les prévisualisations
            </span>
        </legend>
        <div class="eqLogicThumbnailContainer">
            <?php
            if (count($eqLogics) == 0) {
                echo "<br/><br/><br/><center><span style='color:#767676;font-size:1.2em;font-weight: bold;'>Vous n'avez pas encore de module SNMP. Cliquez sur le bouton inclusion  à gauche et suivez la documentation de votre module pour associer celui-ci à Jeedom</span></center>";
            } else {
                foreach ($eqLogics as $eqLogic) {
                    echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
                    echo "<center>";
                    $urlPath = config::byKey('market::address') . '/market/SNMP/images/' . $eqLogic->getConfiguration('device') . '.jpg';
                    $urlPath2 = config::byKey('market::address') . '/market/SNMP/images/' . $eqLogic->getConfiguration('device') . '_icon.png';
                    $urlPath3 = config::byKey('market::address') . '/market/SNMP/images/' . $eqLogic->getConfiguration('device') . '_icon.jpg';

                    echo '<img class="lazy" src="plugins/SNMP/doc/images/SNMP_icon.png" data-original3="' . $urlPath3 . '" data-original2="' . $urlPath2 . '" data-original="' . $urlPath . '" height="105" width="95" />';
                    echo "</center>";
                    echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
	    <div class="col-lg-10 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
	       <div class="row">
		    <div class="col-lg-6">
			<form class="form-horizontal">
			    <fieldset>
				<legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}} <i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i></legend>
				<div class="form-group">
				    <label class="col-lg-3 control-label">{{Nom de l'équipement SNMP}}</label>
				    <div class="col-lg-6">
				        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
				        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement SNMP}}"/>
				    </div>
				</div>
				<div class="form-group">
				    <label class="col-lg-3 control-label" >{{Objet parent}}</label>
				    <div class="col-lg-6">
				        <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
				            <option value="">{{Aucun}}</option>
				            <?php
				            foreach (object::all() as $object) {
				                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
				            }
				            ?>
				        </select>
				    </div>
				</div>
				<div class="form-group">
				    <label class="col-lg-3 control-label">{{Catégorie}}</label>
				    <div class="col-lg-8">
				        <?php
				        foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
				            echo '<label class="checkbox-inline">';
				            echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
				            echo '</label>';
				        }
				        ?>

				    </div>
				</div>
				<div class="form-group">
				    <label class="col-lg-4 control-label" >{{Activer}}</label>
				    <div class="col-lg-1">
				        <input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>
				    </div>
				    <label class="col-lg-4 control-label" >{{Visible}}</label>
				    <div class="col-lg-1">
				        <input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>
				    </div>
				</div>
			    </fieldset> 
			</form>
		</div>
		<div class="col-lg-6">
                <legend>{{Configuration}}</legend>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
				<label class="col-lg-3 control-label">{{Adresse IP}}</label>
				<div class="col-lg-4">
					<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ip" placeholder="{{DNS ou IP}}"/>
				</div>
				192.168.1.100 ou mon.nas.fr
				</div>
				<div class="form-group">
				    <label class="col-lg-3 control-label">{{Matériel}}</label>
			            <div class="col-lg-4">
			                <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="device">
			                    <option value="">{{Aucun}}</option>
			                    <?php
			                    foreach (SNMP::devicesParameters() as $id => $info) {
			                        echo '<option value="' . $id . '">' . $info['name'] . '</option>';
			                    }
			                    ?>
			                </select>
				    </div>
					 <div class="col-lg-5">
		                        <a class="btn btn-warning" id="bt_shareOnMarket"><i class="fa fa-cloud-upload"></i> {{Partager}}</a>
		                    </div>
		                </div>
				<div class="form-group expertModeVisible">
		                    <label class="col-lg-3 control-label">{{Envoyer une configuration}}</label>
		                    <div class="col-lg-5">
		                        <input id="bt_uploadConfSNMP" type="file" name="file" data-url="plugins/SNMP/core/ajax/SNMP.ajax.php?action=uploadConfSNMP">
		                    </div>
		                </div>
				<div class="form-group">
				    <label class="col-lg-3 control-label">{{Communauté}}</label>
				    <div class="col-lg-5">
					<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="snmp_community" placeholder="{{Communauté SNMP}}"/>
				    </div>
					public (bien souvent)
				</div>
				<div class="form-group">
				    <label class="col-lg-3 control-label">{{Version SNMP}}</label>
				    <div class="col-lg-5">
					<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="snmp_version" placeholder="{{Version SNMP}}"/>
				    </div>
					1 2c ou 3
				</div>
                    </fieldset> 
                </form>
            </div>
	</div>
        <legend>Commandes</legend>
        <a class="btn btn-success btn-sm cmdAction" data-action="add"><i class="fa fa-plus-circle"></i>{{Ajouter une commande SNMP}}</a><br/><br/>

        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
			<th style="width: 10%;">{{Nom}}</th>
                    	<th style="width: 10%;">{{Type}}</th>
			<th style="width: 40%;">{{Paramètre(s)}}</th>
			<th style="width: 100px;"></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>

<div class="modal fade" id="md_addPreConfigCmdSNMP">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>{{Ajouter une commande prédefinie}}</h3>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" style="display: none;" id="div_addPreConfigCmdSNMPError"></div>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-4 control-label" for="in_addPreConfigCmdSNMPName">{{Fonctions}}</label>
                            <div class="col-lg-8">
                                <select class="form-control" id="sel_addPreConfigCmdSNMP">
                                    <?php
                                    foreach ($listCmdSNMP as $key => $cmdSNMP) {
                                        echo "<option value='" . $key . "'>" . $cmdSNMP['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>

                <!--<div class="alert alert-success" style="display:none">
                    <center><h4>{{MIB/OID}} 
                            <?php
                            foreach ($listCmdSNMP as $key => $cmdSNMP) {
                                echo '<span class="json_cmd ' . $key . ' hide" style="display : none;" >' . json_encode($cmdSNMP,JSON_UNESCAPED_UNICODE) . '</span>';
								echo '<span class="oid ' . $key . '" style="display : none;" >'  ;
								foreach ($key as $config => $cmdSNMPConfig) {
                                					echo $config['oid'] . '</span>';
								}
                            }
                            ?>
                        </h4></center>
                </div> -->
                <div class="alert alert-info">
                    <center><h4>{{Description}}</h4></center>
                    <?php
                    foreach ($listCmdSNMP as $key => $cmdSNMP) {
                        echo '<span class="description ' . $key . '" style="display : none;">' . $cmdSNMP['description'] . '</span>';
                    }
                    ?>
                </div>
                <div class="alert alert-danger" style="display:none">
                    <center><h4>{{Pré-requis}}</h4></center>
                    <?php
                    foreach ($listCmdSNMP as $key => $cmdSNMP) {
                        echo '<span class="required ' . $key . '" style="display : none;">' . $cmdSNMP['required'] . '</span>';
                    }
                    ?>
                </div> 
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> {{Annuler}}</a>
                <a class="btn btn-success" id="bt_addPreConfigCmdSNMPSave"><i class="fa fa-check-circle"></i> {{Ajouter}}</a>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'SNMP', 'js', 'SNMP'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
