
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

 $("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

 $('.eqLogicAttr[data-l1key=configuration][data-l2key=mode]').on('change',function(){
    $('.mode').hide();
    $('.mode.'+$(this).value()).show();
});

 $('#bt_syncPattern').on('click',function(){
$.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/blink1/core/ajax/blink1.ajax.php", // url du fichier php
        data: {
            action: "syncPattern",
            id: $('.eqLogicAttr[data-l1key=id]').value(),
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) { // si l'appel a bien fonctionné
        if (data.state != 'ok') {
            $('#div_alert').showAlert({message: data.result, level: 'danger'});
            return;
        }
        window.location.href = 'index.php?v=d&p=blink1&m=blink1&id='+$('.eqLogicAttr[data-l1key=id]').value();
    }
});
});


 function printEqLogic(_eqLogic){
    $('#span_watchUrl').append(wathUrl+'&id='+_eqLogic.id);
}

/*
 * Fonction pour l'ajout de commande, appelé automatiquement par plugin.template
 */
 function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 180px;" placeholder="{{Nom}}">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="id"  style="display : none;">';
    tr += '</td>';

    tr += '<td>';
    tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="action" disabled style="margin-bottom : 5px;width : 120px;" />';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    tr += '<td>';
    if(!isset(_cmd.logicalId) || _cmd.logicalId == '' || _cmd.logicalId == 'pattern'){
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="logicalId" style="display : none;" value="pattern">';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="pattern" placeholder="{{Nom du pattern (en mode API ou URL) ou commande (en mode SSH ou local)}}">';
    }
    tr += '</td>';
    tr += '<td>';
    tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/> {{Afficher}}<br/></span>';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}
