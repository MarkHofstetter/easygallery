<?php // ###########################################################//
// #  script by tholtkoetter                  www.freitagmorgen.de #//
// #################################################################//

// image root dir
$rootdir = "../../PICTURES";

// filetypes
$filetypes = array("jpg", "jpeg", "png");

// size of thumbnails in pixel
$tnwidth = 140;
$gmwidth = 56;

// #################################################################//
// helper classes

class Preview {
	public $dir;
	public $img;
	public $style;
	function __construct($newdir, $newimg, $newstyle) {
		$this -> dir = $newdir;
		$this -> img = $newimg;
		$this -> style = $newstyle;
	}
}

class Gallery {
	public $images;
	public $dir;
	public $download;
	public $exifavailable;
	function __construct($newimages, $newdir) {
		$this -> images = $newimages;
		$this -> dir = $newdir;
		foreach ($newimages as $image) {
			if (!empty($image -> exif -> gps)) {
				$this -> exifavailable = true;
				break;
			}
		}
		$this -> download = changetoroot($newdir -> src) . '/' . $newdir -> name . '.zip';
	}
}

class Image {
	public $img;
	public $thumbnail;
	public $exif;
	function __construct($newimg, $newthumbnail, $newexif) {
		$this -> img = $newimg;
		$this -> thumbnail = $newthumbnail;
		$this -> exif = $newexif;
	}
}

class Exif {
	public $gps;
	public $style;
	public $datetime;
	public $thumbnail;
	function __construct($newgps, $newdatetime, $newthumbnail) {
		$this -> gps = $newgps;
		$this -> datetime = $newdatetime;
		$this -> thumbnail = $newthumbnail;
		if (isset($this -> gps)) {
			$this -> style = "gps";
		}
	}
}

class Gps {
	public $lat;
	public $lng;
	function __construct($newlat, $newlng) {
		$this -> lat = $newlat;
		$this -> lng = $newlng;
	}
}

class Path {
	public $name;
	public $encodedName;
	public $src;
	function __construct($newname, $newsrc) {
		$this -> name = $newname;
		$this -> encodedName = urlencode($newname);
		$this -> src = $newsrc;
	}
}

// #################################################################//
// main functions

function findfolders() {
	global $rootdir, $folders;
	if (!is_dir($rootdir)) {
		return null;
	}
	if (!isset($folders)) {
		$roothandle = opendir($rootdir);
		$folders = array();
		$i = 1;
		while ($dirname = readdir($roothandle)) {
			$dir = $rootdir . '/' . $dirname;
			if (!isdot($filename) && is_dir($dir)) {
				$path = new Path($dirname, changetoroot($dir));
				$dirhandle = opendir($dir);
				while ($filename = readdir($dirhandle)) {
					if (isvalidfiletype($filename)) {
						$img = ($path -> src) . "/thumbnails/tn_" . $filename;
						array_push($folders, new Preview($path, $img, "stack" . $i));
						createthumb($dir, 'tn', $filename, $tnwidth);
						$i < 4 ? $i++ : $i = 1;
						break;
					}
				}
				closedir($dirhandle);
			}
		}
		closedir($roothandle);
		rsort($folders);
	}
	$folders = array('previews' => $folders);
	return $folders;
}

function changefolder($ordner) {
	global $folders, $tnwidth, $gmwidth;

	// set initial variable $ordner
	if (!isset($ordner)) {
		$ordner = $folders['previews'][0] -> dir -> src;
	}
	$ordner = '../../' . $ordner;
	$files_to_zip = array();

	// scanning directories for image files
	if (is_dir($ordner)) {
		$dirhandle = opendir($ordner);
		$images = array();
		$gdlibchecked = false;
		while ($filename = readdir($dirhandle)) {
			if (!isdot($filename) && is_file($ordner . '/' . $filename) && isvalidfiletype($filename)) {
				// image data
				$thumbnail = changetoroot($ordner . '/thumbnails/tn_' . $filename);
				$image = new Path($filename, changetoroot($ordner . '/' . $filename));
				$files_to_zip[] = $ordner . '/' . $filename;
				$exif = null;
				// extract exif data
				$gps = getGPS($ordner . '/' . $filename);
				if (isset($gps)) {
					$gmthumbnail = changetoroot($ordner . '/thumbnails/gm_' . $filename);
					$exif = new Exif($gps, null, $gmthumbnail);
				}
				array_push($images, new Image($image, $thumbnail, $exif));
				// create thumbs if necessary
				createthumb($ordner, 'tn', $filename, $tnwidth);
				createthumb($ordner, 'gm', $filename, $gmwidth);
			}
		}
		sort($images);
		$dir = new Path(preg_replace('/^(.+\/)*/', '', $ordner), $ordner);
		closedir($dirhandle);
	} else {
		return null;
	}
	$gallery = new Gallery($images, $dir);
	create_zip($files_to_zip, '../../' . $gallery -> download);
	return $gallery;
}

// #################################################################//
// helper functions

function isvalidfiletype($filename) {
	global $filetypes;
	foreach ($filetypes as $filetype) {
		if (preg_match('/(' . $filetype . ')$/i', $filename)) {
			return true;
		}
	}
	return false;
}

function isdot($arg) {
	return preg_match('/$^\.{1,2}/i', $arg);
}

function changetoroot($src) {
	return preg_replace('/^(\.\.\/)*/', '', $src);
}

function createthumb($folder, $prefix, $file, $maxsize) {
	if (!file_exists($folder . '/thumbnails/' . $prefix . '_' . $file)) {
		if (!$gdlibchecked) {
			$gdlibchecked = checkgdlib();
		}
		if (preg_match('/(\.jpg|\.jpeg)$/i', $file)) {
			$image = imagecreatefromjpeg($folder . '/' . $file);
		}
		if (preg_match('/\.png$/i', $file)) {
			$image = imagecreatefrompng($folder . '/' . $file);
		}
		list($width, $height) = getimagesize($folder . '/' . $file);
		$x_offset = ($width > $height) ? ($width - $height) / 2 : 0;
		$y_offset = ($width < $height) ? ($height - $width) / 2 : 0;
		$width = min($width, $height);
		$tn = imagecreatetruecolor($maxsize, $maxsize) or die('Cannot Initialize new GD image stream');
		imagecopyresampled($tn, $image, 0, 0, $x_offset, $y_offset, $maxsize, $maxsize, $width, $width);
		if (!is_dir($folder . '/thumbnails')) {
			mkdir($folder . '/thumbnails', 0775);
		}
		// draw border for google map markers
		if ($prefix == 'gm') {
			$color_white = ImageColorAllocate($tn, 255, 255, 255);
			drawBorder($tn, $color_white, 2);
		}
		imagejpeg($tn, $folder . '/thumbnails/' . $prefix . '_' . $file, 90);
	}
}

function drawBorder($img, $color, $thickness = 1) {
	$x1 = $y1 = 0;
	$x2 = $y2 = ImageSY($img) - 1;
	for ($i = 0; $i < $thickness; $i++) {
		ImageRectangle($img, $x1++, $y1++, $x2--, $y2--, $color);
	}
}

function checkgdlib() {
	$modules = get_loaded_extensions();
	if (!in_array("gd", $modules)) {
		echo("Your webserver doesn't provide the use of the GD library, which is required to create thumbnails. Please create and add your thumbnails manually.");
		return false;
	} else {
		return true;
	}
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
	if (!preg_match('/(\.jpg|\.jpeg)$/i', $image)) {
		return null;
	}
	$exif = exif_read_data($image, 0, true);
	if ($exif) {
		$lat = $exif['GPS']['GPSLatitude'];
		$lng = $exif['GPS']['GPSLongitude'];
		if (!$lat || !$lng)
			return null;
		// latitude values
		$lat_degrees = divide($lat[0]);
		$lat_minutes = divide($lat[1]);
		$lat_seconds = divide($lat[2]);
		$lat_hemi = $exif['GPS']['GPSLatitudeRef'];
		// longitude values
		$lng_degrees = divide($lng[0]);
		$lng_minutes = divide($lng[1]);
		$lng_seconds = divide($lng[2]);
		$lng_hemi = $exif['GPS']['GPSLongitudeRef'];

		$lat_decimal = toDecimal($lat_degrees, $lat_minutes, $lat_seconds, $lat_hemi);
		$lng_decimal = toDecimal($lng_degrees, $lng_minutes, $lng_seconds, $lng_hemi);

		return new Gps($lat_decimal, $lng_decimal);
	} else {
		return null;
	}
}

function create_zip($files = array(), $destination = '') {
	$valid_files = array();
	if (is_array($files)) {
		foreach ($files as $file) {
			if (file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	if (count($valid_files)) {
		$zip = new ZipArchive();
		if ($zip -> open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		foreach ($valid_files as $file) {
			$zip -> addFile($file, $file);
		}
		$zip -> close();
		return file_exists($destination);
	} else {
		return false;
	}
}
?>