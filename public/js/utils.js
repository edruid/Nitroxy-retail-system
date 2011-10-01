/**
 * Checks if a key pressed event is a comma ',' and replaces it with
 * a dot '.'.
 * @param event e The event that was fired.
 * @param inputElement elem The element that recieved the event.
 * @return bool if a comma was replaced, true is returned.
 */
function fix_comma(e, elem) {
	var keynum, keychar;
	if(window.event) { // IE
		keynum = e.keyCode;
	} else if(e.which) { // Netscape/Firefox/Opera
		keynum = e.which;
	}
	keychar = String.fromCharCode(keynum);
	if(keychar== ',') {
		var str = elem.value;
		var pos = elem.selectionStart+1
		elem.value = str.substr(0,elem.selectionStart) + '.' + str.substr(elem.selectionEnd);
		elem.selectionStart = pos;
		elem.selectionEnd = pos;
		e.preventDefault();
		return true;
	}
	return false;
}
