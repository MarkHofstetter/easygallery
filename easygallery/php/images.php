<?php
require ("easygallery.php");
require ("Slim/Slim.php");
\Slim\Slim::registerAutoloader();

findfolders();
$imagesApp = new \Slim\Slim();
$imagesApp->get('/images/:src', function ($dirname) {
	global $folders;	
	foreach($folders['previews'] as $preview){	
		if(strcasecmp($preview -> dir -> name, $dirname) == 0)
		{
			echo json_encode(changefolder($preview -> dir -> src));
		}
	}
});
$imagesApp->run();
?>	