function printPreview () {
	setActiveStyleSheet ('Print Preview');
	jQuery ('#printpreview').toggleClass ('information');
	jQuery ('#printpreview').html ('<strong>Print Version</strong> - <a href="#" onclick="printCancel();" >Click here</a> to return to the normal view');
}

function printCancel () {
	setActiveStyleSheet ('default');
	jQuery ('#printpreview').toggleClass ('information');
	jQuery ('#printpreview').html ('');
}

function setActiveStyleSheet(title) {

	var i, a, main;
	
	for (i = 0; (a = document.getElementsByTagName ('link')[i]); i++) {
		if (a.getAttribute ('rel').indexOf ('style') != -1 && a.getAttribute ('title')) {
			a.disabled = true;
			if (a.getAttribute ('title') == title) {
				a.disabled = false;
			};
		}
	}
	
}