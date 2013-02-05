<?php // ###########################################################//
// #  script by tholtkoetter                  www.freitagmorgen.de #//
// #################################################################//

// title
$title = "EasyGallery";

// google analytics properties id, UA-XXXXX-Y
$googleanalyticsid = "";

// #################################################################//

// register templating engine
require ("easygallery/html/Mustache/Autoloader.php");
Mustache_Autoloader::register();
$m = new Mustache_Engine(array(
	'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/easygallery/html'),
));

// load dependencies
require ("easygallery/html/gallery.php");	
// init and prepare data
$galleryfolders = init($_SERVER['PHP_SELF']);
// prepare images
$galleryfiles = changefolder();
?>

<html>
	<head>
		<title><?php echo $title; ?></title>
		<!-- TODO add meta tags -->
		<meta name="description" content="">
		<meta name="version" content=""/>

		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		
		<link rel="stylesheet" href="easygallery/css/easygallery.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="easygallery/css/ext/jquery.fancybox.css?v=2.1.4" type="text/css" media="screen" />

		<!-- #dev head start -->
		<script type="text/javascript" src="easygallery/js/ext/jquery-1.9.1.min.js"></script>
		<script type="text/javascript" src="easygallery/js/ext/jquery.mousewheel-3.0.6.pack.js"></script>
		<script type="text/javascript" src="easygallery/js/ext/jquery.fancybox.pack.js"></script>
		<script type="text/javascript" src="easygallery/js/ext/jquery.fancybox-media.js"></script>
		<!-- #dev head end -->

		<script type="text/javascript">
			$(document).ready(function() {
				$(".fancybox").fancybox();				
				$('.fancybox-media').fancybox({
					openEffect  : 'none',
					closeEffect : 'none',
					helpers : {
						media : {}
					}
				});
			});
		</script>
	</head>

	<body>
	<?php
		// render select
		echo $m->render('select', $galleryfolders);
		// render images
		echo $m->render('pictures', $galleryfiles);	
	?>
	
	<?php
		// render google analytics
		echo $m->render('googleanalytics', array('propertiesId' => $googleanalyticsid));
	?>
	</body>
</html>