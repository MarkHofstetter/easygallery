<?php
require ("easygallery.php");
require ("Slim/Slim.php");
\Slim\Slim::registerAutoloader();

$galleryApp = new \Slim\Slim();
$galleryApp->get('/gallery', function () {
	echo json_encode(findfolders());
});
$galleryApp->run();
?>	