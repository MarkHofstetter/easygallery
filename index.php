<?php // ###########################################################//
// #  script by tholtkoetter                  www.freitagmorgen.de #//
// #################################################################//

// title
$title = "EasyGallery";

// google analytics properties id, UA-XXXXX-Y
$googleanalyticsid = "";

// #################################################################//

// register templating engine
require ("easygallery/php/Mustache/Autoloader.php");
Mustache_Autoloader::register();
$m = new Mustache_Engine(array(
	'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/easygallery/html'),
));

// load dependencies
require ("easygallery/php/easygallery.php");
// init and prepare data
//$folders = findfolders();
// prepare images
//$images = changefolder(null);
?>

<html>
	<head>
		<title><?php echo $title; ?></title>
		<!-- TODO add meta tags -->
		<meta name="description" content="">
		<meta name="version" content=""/>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		
		<link rel="stylesheet" href="easygallery/css/easygallery.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="easygallery/css/ext/jquery.fancybox.css?v=2.1.4" type="text/css" media="screen" />
		<link href='http://fonts.googleapis.com/css?family=Fjalla+One' rel='stylesheet' type='text/css'>

		<!-- #dev head start -->
		<script type="text/javascript" src="easygallery/js/easygallery.js"></script>
		<script type="text/javascript" src="easygallery/js/ext/mustache.js"></script>
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
				
				$.getJSON("easygallery/php/folders.php/folders",
					function(data){
						$.get('easygallery/html/previews.mustache', function(template) {
						    var html = Mustache.to_html(template, data);
						    $("#easygallery").html(html);
						});
					}
				);
			});
			
			$(document).ajaxComplete(function() {
				$("#ajaxgetlink").click(function(){
					$.getJSON("easygallery/php/images.php/images/Pictures%203",
						function(data){
							$.get('easygallery/html/pictures.mustache', function(template) {
							    var html = Mustache.to_html(template, data);
							    $("#easygallery").html(html);
							});
						}
					);
				});
			});
		</script>
	</head>

	<body>
	
	<div id="easygallery"></div>
	
	<?php
		// render previews
		// echo $m->render('previews', $folders);	
		// render images
		// echo $m->render('pictures', $images);	
	?>
	
	<?php
		// render google analytics
		echo $m->render('googleanalytics', array('propertiesId' => $googleanalyticsid));
	?>
	</body>
</html>