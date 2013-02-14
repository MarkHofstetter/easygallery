<?php
	require ("easygallery.php");
	require ("Slim/Slim.php");
	\Slim\Slim::registerAutoloader();

	$foldersApp = new \Slim\Slim();
	$foldersApp->get('/folders', function () {
		echo json_encode(findfolders());
	});
	$foldersApp->run();
?>	