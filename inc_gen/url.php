<?php
/**
 * Innehåller funktioner för att hantera urler.
 */

function public_root($called_from_notindex=false) {
	$script_name=$_SERVER['SCRIPT_NAME'];
	if($called_from_notindex) {
		$script_name=dirname($script_name);
	}
	$root=dirname($script_name);
	if(substr($root,-1,1)=='/') {
		$root=substr($root,0,-1);
	}
	return $root;
}

function absolute_path($path,$called_from_notindex=false) {
	/**
	 * Returnerar en halvabsolut sökväg till $path
	 */
	if(substr($path,0,1)!='/') {
		$path='/'.$path;
	}
	$absolute_path=public_root($called_from_notindex).$path;
	return $absolute_path;
}

function kickback_url($request = null) {
	if($request == null) {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			return $_SERVER['HTTP_REFERER'];
		}
		$request = $_SERVER['REQUEST_URI'];
	}
	if($request[0] == '/') $request = substr($request, 1);
	$http = (isset($_SERVER['HTTPS'])?'https':'http');
	return "$http://{$_SERVER['HTTP_HOST']}/$request";
}

function kick($path) {
	// Empty output buffer to be able to send header after content
	// (buffered output will be discarded)
	ob_clean();

	if(preg_match('/^https?:\/\//', $path)) {
		header("Location: $path");
		die();
	}
	// Construct URL
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
		$proto='https';
	} else {
		$proto='http';
	}
	$host=$_SERVER['HTTP_HOST'];
	$path=absolute_path($path,true);

	header("Location: $proto://$host$path");
	exit;
}

function show_404() {
	?>
	<h1>Sidan du försöker visa finns inte</h1>
	<p>Vänligen kontrollera url:en och försök igen.</p>
	<?php
	die();
}
?>
