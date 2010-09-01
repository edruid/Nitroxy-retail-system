function ean_keydown(e) {
	var keynum;
	if(window.event) // IE
		{
		keynum = e.keyCode;
		}
	else if(e.which) // Netscape/Firefox/Opera
		{
		keynum = e.which;
		}
	if(keynum==16) {
		shift_down();
	}
}
function ean_keyup(e) {
	var keynum;
	if(window.event) // IE
		{
		keynum = e.keyCode;
		}
	else if(e.which) // Netscape/Firefox/Opera
		{
		keynum = e.which;
		}
	if(keynum==16) {
		shift_up();
	}
}

var shift_is_down=false;

function shift_down() {
	var remove_help=document.getElementById('remove_help');
	remove_help.innerHTML="Kommer ta bort 1 st";
	shift_is_down=true;
}
function shift_up() {
	var remove_help=document.getElementById('remove_help');
	remove_help.innerHTML="För att ta bort en vara, håll ned SHIFT och scanna streckkoden.";
	shift_is_down=false;
}
