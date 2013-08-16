function edDoCode (v, id, editId) {

	startv	= v;
	endv	= v;

	// link
	if (startv == 'a') {
 		url = prompt ('Enter the link url', 'http://');
		if (url != '') {
			startv = 'a href="' + url + '"';
		};
	}

	if (startv == 'display_html') {
		jQuery ('#' + editId + ' code').slideToggle (100);
		return;
	}

	var textarea = jQuery ('form #' + id);
	var selection = textarea[0];

	// for IE
	if (document.selection) {

	    var str = document.selection.createRange ().text;
	    textarea.focus ();
	    var sel = document.selection.createRange ();
	    sel.text = '<' + startv + '>' + str + '</' + endv + '>';

	// for mozilla
	} else if (selection.selectionStart != undefined) {
		
		var selLength = selection.textLength;
		var selStart = selection.selectionStart;
		var selEnd = selection.selectionEnd;
		var oldScrollTop = selection.scrollTop;

		var s1 = textarea.val().substring (0, selStart);
		var s2 = textarea.val().substring (selStart, selEnd)
		var s3 = textarea.val().substring (selEnd, selLength);

		textarea.val(s1 + '<' + startv + '>' + s2 + '</' + endv + '>' + s3);

		selection.selectionStart = s1.length;
		selection.selectionEnd = s1.length + 5 + s2.length + startv.length + endv.length;
		selection.scrollTop = oldScrollTop;
		selection.focus ();

	} else {

		var what = '<' + startv + '></' + endv + '>';
		textarea.val (textarea.val () + what);
		textarea.focus ();

	}

}


function edWrite (textarea) {

	id = 'htmlEditor_' + textarea.attr('id');

	if (jQuery ('#' + id).length > 0) {
		return;
	}

	htmlOutput = '';
	htmlOutput += '<div class="html_toolbar clear clearfix" id="' + id + '">';

	htmlOutput += edButton ('strong', 'bold', textarea, id);
	htmlOutput += edButton ('em', 'italic', textarea, id);
	htmlOutput += edButton ('a', 'link', textarea, id);
	htmlOutput += edButton ('del', 'delete', textarea, id);
	htmlOutput += edButton ('pre', 'code', textarea, id);
	htmlOutput += edButton ('blockquote', 'quote', textarea, id);
	htmlOutput += edButton ('display_html', 'Show Allowed HTML', textarea, id);

	htmlOutput += '</div>';

	textarea.before (htmlOutput);

	if (jQuery ('.allowedHtml')) {
		jQuery ('.allowedHtml').clone ().appendTo ('#' + id);
	}

}


function edButton (tag, name, textarea, id) {

	return '<a href="#" class="html_button html_button_' + tag + '" onclick="edDoCode(\'' + tag + '\', \'' + textarea.attr('id') + '\', \'' + id + '\'); return false;">' + name + '</a>';

}