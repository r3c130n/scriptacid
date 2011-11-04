function RedirectTo(url, timeOut) {
	if (url.length > 0) {
		if (timeOut > 0) {
			setTimeout(function() {location = url;}, timeOut);
		} else {
			location = url;
		}
		return false;
	} else {
		return false;	
	}
}
function d(variable) {
	console.log(variable);
}

var showEditor = false;
function ShowPageEditor() {
	var pageText = document.getElementById('pageEditorDiv');
	var selfText = document.getElementById('selfContent');
	if(!showEditor) {
		pageText.style.display = 'block';
		selfText.style.display = 'none';
		showEditor = true;
	} else {
		pageText.style.display = 'none';
		selfText.style.display = 'block';
		showEditor = false;
	}
	
}
var bOpenPanel = false;
function closePanel() {
	var panelDiv = document.getElementById('siteEditorPanel');
	if (bOpenPanel) {
		panelDiv.style.display = 'block';
		bOpenPanel = false;
	} else {
		panelDiv.style.display = 'none';
		bOpenPanel = true;
	}
}