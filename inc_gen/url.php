<?
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

function kickback_url($request = false) {
	return "http".(isset($_SERVER['HTTPS'])?'s':'')."://{$_SERVER['HTTP_HOST']}".($request?'/'.$request:$_SERVER['REQUEST_URI']);
}

function kick($path) {
	// Empty output buffer to be able to send header after content
	// (buffered output will be discarded)
	ob_clean();

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
?>
