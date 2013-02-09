<?php // ###########################################################//
// #  script by tholtkoetter                  www.freitagmorgen.de #//
// #################################################################//

// Root directory
$rootdir = "PICTURES";

// filetypes
$filetypes = array("jpg", "jpeg", "png");

// size of thumbnails in pixel
$tnwidth = 140;
$gmwidth = 48;

// #################################################################//
// helper classes

class SelectData {
	public $action;
	public $paths;
	function __construct($newaction, $newpaths) {
		$this -> action = $newaction;
		$this -> paths = $newpaths;
	}
}

// #################################################################//
// main functions

function init($phpself_init) {
	global $rootdir, $phpself;

	// init self-reference, remove anchors
	$phpself = preg_replace('/#.+$/i', '', $phpself_init);
	// check and prepare root dir
	if (strpos($rootdir, 'www') === 0) {
		$rootdir = 'http://' . $rootdir;
	}
	$local = parse_url($rootdir);
	if (strpos($rootdir, 'http://') === 0) {
		foreach (count_chars($phpself,1) as $i => $val) {
			if (chr($i) == '/') {
				$rootdir = substr($local['path'], 1);
				for ($j = 1; $j < $val; $j++)
					$rootdir = '../' . $rootdir;
			}
		}
		if (strpos($rootdir, $local['path']) === 0) {
			$rootdir = ".";
		}
	}
	if (!is_dir($rootdir)) {
		printError("Empty or missing root directory. Please drop your images into the PICTURES/ folder or change the \$rootdir variable in gallery.php.");
	}
	// init folders
	return findfolders();
}

function findfolders() {
	global $rootdir, $ordner, $phpself, $folders;

	// check for image folders
	$roothandle = opendir($rootdir);
	$data = array();
	while ($dirname = readdir($roothandle)) {
		if (!isdot($filename) && is_dir($rootdir.'/'.$dirname)) {
			$dirhandle = opendir($rootdir .'/'.$dirname);
			while ($filename = readdir($dirhandle)) {
				if (isvalidfiletype($filename)) {
					$path = $rootdir.'/'.$dirname;
					$selected = ($ordner == $rootdir.'/'. $dirname) ? 'selected' : '';
					array_push($data, array('path' => $path, 'name' => $dirname, 'selected' => $selected));
					break;
				}
			}
			closedir($dirhandle);
		}
	}
	if (!$data) {
		printError("No image folders found. Please drop your images into the PICTURES/ folder or change the \$rootdir variable in gallery.php.");
	}
	closedir($roothandle);
	rsort($data);

	$folders = new SelectData($phpself, $data);
	return $folders;
}

function changefolder() {
	global $ordner, $folders, $tnwidth;
	extract($_POST);

	// set initial variable $ordner
	if (!isset($ordner)) {
		$ordner = $folders -> paths[0]['path'];
	}

	// scanning directories for image files
	if (is_dir($ordner)) {
		$dirhandle = opendir($ordner);
		$files = array();
		$gdlibchecked = FALSE;
		while ($filename = readdir($dirhandle)) {			
			if (!isdot($filename) && is_file($ordner.'/'.$filename) && isvalidfiletype($filename)) {
				$gps = getGPS($ordner.'/'.$filename);
				$gpsstyle = empty($gps) ? "" : "gps";
				$thumbpath = $ordner.'/thumbnails/tn_'.$filename;
				array_push($files, array('gallery' => $ordner, 'path' => $ordner.'/'.$filename, 'thumbpath' => $thumbpath, 'name' => $filename, 'gps' => $gps, 'gpsstyle' => $gpsstyle));
				if (!file_exists($thumbpath)) {
					if(!$gdlibchecked){
						$gdlibchecked = checkgdlib();
					}
					if (!createthumb($ordner, 'tn', $filename, $tnwidth)) {
						printError("Thumbnail creation failed.");
					}
				}
				// if (!file_exists($ordner.'/thumbnails/gm_'.$filename)) {
					// if(!$gdlibchecked){
						// $gdlibchecked = checkgdlib();
					// }
					// if (!createthumb($ordner, 'gm', $filename, $gmwidth)) {
						// printError("Thumbnail creation failed.");
					// }
				// }
			}
		}
		sort($files);
		closedir($dirhandle);
	} else {
		printError("No images found. Please drop your images into the PICTURES/ folder or change the \$rootdir variable in gallery.php.");
	}
	updatefolders();
	
	return array('paths' => $files);
}

// #################################################################//
// helper functions

function updatefolders() {
	global $ordner, $folders;

	// update selected field
	for ($i = 0; $i < sizeof($folders -> paths); $i++) {
		if ($folders -> paths[$i]['path'] == $ordner) {
			$folders -> paths[$i]['selected'] = 'selected';
		} else {
			$folders -> paths[$i]['selected'] = '';
		}
	}
}

function isvalidfiletype($filename) {
	global $filetypes;
	$result = FALSE;
	for ($i = 0; $i < sizeof($filetypes); $i++) {
		$postmp = strpos(strtolower($filename), strtolower($filetypes[$i]));
		if ($postmp > 0) {
			$result = TRUE;
		}
	}
	return $result;
}

function isdot($arg) {
	return strcmp($arg, '.') == 0 || strcmp($arg, '..') == 0;
}

function createthumb($folder, $prefix, $file, $maxsize) {
	if(preg_match('/(\.jpg|\.jpeg)$/i', $file)){
		$image = imagecreatefromjpeg($folder.'/'.$file);
	}
	if(preg_match('/\.png$/i', $file)){
		$image = imagecreatefrompng($folder.'/'.$file);
	}
	list($width, $height) = getimagesize($folder.'/'.$file);
	$x_offset = ($width > $height) ? ($width - $height) / 2 : 0;
	$y_offset = ($width < $height) ? ($height - $width) / 2 : 0;
	$width = min($width, $height);
	$tn = imagecreatetruecolor($maxsize, $maxsize)
		or die('Cannot Initialize new GD image stream');
	imagecopyresampled($tn, $image, 0, 0, $x_offset, $y_offset, $maxsize, $maxsize, $width, $width);
	if (!is_dir($folder.'/thumbnails')) {
		mkdir($folder.'/thumbnails', 0775);
	}
	// draw border for google map markers
	if($prefix == 'gm'){
		$color_white = ImageColorAllocate($tn,255,255,255); 
		drawBorder($tn, $color_white, 2); 
	}
	imagejpeg($tn, $folder.'/thumbnails/'.$prefix.'_'.$file, 90);
	return true;
}

function drawBorder(&$img, &$color, $thickness = 1) { 
    $x1 = $y1 = 0;
    $x2 = $y2 = ImageSY($img) - 1; 
    for($i = 0; $i < $thickness; $i++){ 
        ImageRectangle($img, $x1++, $y1++, $x2--, $y2--, $color); 
    } 
} 

function checkgdlib() {
	$modules = get_loaded_extensions();
	if (!in_array("gd", $modules)) {
		printError("Your webserver doesn't provide the use of the GD library, which is required to create thumbnails. Please create and add your thumbnails manually.");
	}
	return TRUE;
}

function toDecimal($deg, $min, $sec, $hemi) {
	$d = $deg + $min / 60 + $sec / 3600;
	return ($hemi == 'S' || $hemi == 'W') ? $d *= -1 : $d;
}

function divide($a) {
	// evaluate the string fraction and return a float
	$e = explode('/', $a);
	// prevent division by zero
	if (!$e[0] || !$e[1]) {
		return 0;
	} else {
		return $e[0] / $e[1];
	}
}

function getGPS($image) {
	if (!preg_match('/(\.jpg|\.jpeg)$/i', $image)){
		return null;
	}
	$exif = exif_read_data($image, 0, true);
	if ($exif) {
		$lat = $exif['GPS']['GPSLatitude'];
		$log = $exif['GPS']['GPSLongitude'];
		if (!$lat || !$log)
			return null;
		// latitude values
		$lat_degrees = divide($lat[0]);
		$lat_minutes = divide($lat[1]);
		$lat_seconds = divide($lat[2]);
		$lat_hemi = $exif['GPS']['GPSLatitudeRef'];

		// longitude values
		$log_degrees = divide($log[0]);
		$log_minutes = divide($log[1]);
		$log_seconds = divide($log[2]);
		$log_hemi = $exif['GPS']['GPSLongitudeRef'];

		$lat_decimal = toDecimal($lat_degrees, $lat_minutes, $lat_seconds, $lat_hemi);
		$log_decimal = toDecimal($log_degrees, $log_minutes, $log_seconds, $log_hemi);

		return array('lat' => $lat_decimal, 'log' => $log_decimal);
	} else {
		return null;
	}
}

function printError($text) {
	echo "<div class=\"error\">";
	echo "<span class=\"content\">ERROR: $text</span>";
	echo "</div>";
	exit();
}
?>