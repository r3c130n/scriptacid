var arParamsCache;

function ShowEditorDialog(title, type, arParams) {
	arParamsCache = arParams;
	var arParamsPrep = '';

	if(typeof(arParams) == 'object') {
		for(var param in arParams) {
			if (typeof(arParams[param]) == 'string') {
				arParamsPrep += '&arParams[' + param + ']=' + arParams[param];
			} else {
				for (var par in arParams[param]) {
					arParamsPrep += '&arParams[' + param + '][]=' + arParams[param][par];
				}
			}
		}
	}
	var loadUrl = '/system/admin/visual/?show=' + type + arParamsPrep;
	$('div#dialog-window-form').dialog({
		bgiframe: true,
		autoOpen: false,
		height: 400,
		width: 'auto',
		modal: true,
		title: title,
		buttons: {
			/*
			'Сохранить': function() {
				$(this).html('');
				$(this).dialog('close');
			},*/
			'Отмена': function() {
				$(this).html('');
				$(this).dialog('close');
			}
		},
		close: function() {
		}
	}).load(loadUrl);
	
	
	$('div#dialog-window-form').dialog('open');
}

function SavePage(str) {
	$.post(
		'/system/admin/visual/index.php', {
			param1: str
		  },
		  onAjaxSuccess
		);
}

function onAjaxSuccess(data) {
	if (data == 'OK') {
		$('div#dialog-window-form').dialog('close');
	}
}

function RefreshParamsDialog(params) {
	var arParamsPrep = '';

	arParamsCache[params.key] = params.value;

	var arParams = arParamsCache;

	for(var param in arParams) {
		if (typeof(arParams[param]) == 'string') {
			arParamsPrep += '&arParams[' + param + ']=' + arParams[param];
		} else {
			for (var par in arParams[param]) {
				arParamsPrep += '&arParams[' + param + '][]=' + arParams[param][par];
			}
		}
	}
	
	var loadUrl = '/system/admin/visual/?show=params&path=' + arParams.componentUrl + '&tpl=' + arParams.templateUrl + arParamsPrep;

	$('div#dialog-window-form').load(loadUrl);
}

function ChgPageTitle(pageUrl) {
	ShowEditorDialog('Изменение заголовка страницы', 'page-params', {'pageUrl': pageUrl, 'editorName': 'elm1'});
}

$(document).ready(function () {
	$('#panelCloser').toggle(
			function() {
				$('#siteEditorPanel').show();
			},
			function() {
				$('#siteEditorPanel').hide();
			}
		);
});