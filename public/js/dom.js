var display_list=new Array();

function toggle(id) {
	var elem=document.getElementById(id);
	if(elem.style.display=='none') {
		elem.style.display=display_list[id];
	} else {
		display_list[id]=elem.style.display;
		elem.style.display='none';
	}
}

function set_display(id,value) {
	display_list[id]=value;
}

function set_focus(id) {
	var elem=document.getElementById(id);
	elem.focus();
}

function ajax_create() {
	var xmlHttp;
	try {
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e) {
		// Internet Explorer
		try {
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			try {
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) {
				//alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	return xmlHttp;
}

