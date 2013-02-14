<?php
	require ("easygallery.php");
	require ("Slim/Slim.php");
	\Slim\Slim::registerAutoloader();
	
	findfolders();
	$imagesApp = new \Slim\Slim();
	$imagesApp->get('/images/:src', function ($dirname) {
		global $folders;
		foreach($folders['previews'][0] as $dir){
			if(strcasecmp($dir -> name, $dirname) == 0)
			{
				echo json_encode(changefolder($dir -> src));
			}
		}
	});
	$imagesApp->run();
?>	