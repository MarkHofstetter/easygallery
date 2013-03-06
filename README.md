EasyGallery
===========
EasyGallery is a smart and simple way to present photos on your own website without a complex installation or administration. Just upload and everything is there.

EasyGallery uses the [fancybox](http://fancyapps.com/fancybox/) jquery plugin (licensed under Creative Commons Attribution-NonCommercial 3.0 license). It comes with the [Slim](http://www.slimframework.com/) PHP framework, [AngularJS](http://angularjs.org/) and uses [Google Maps](https://developers.google.com/maps/).

How to use
----------
### Installation
1. Extract and copy to your Webserver
2. Make the `PICTURES/` folder writeable (`chmod 775`)
3. Copy your images to the `PICTURES/` folder

### Example
	<head>
		...
		<link rel="stylesheet" media="screen,print" type="text/css" href="easygallery/css/app-min.css" />
		<link href='http://fonts.googleapis.com/css?family=Fjalla+One' rel='stylesheet' type='text/css'>
		...
	</head>
	<body>
		...
		<div ng-app="easygallery">
			<div ng-view></div>
			<div id="map_canvas"></div>
		</div>
		...
		<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.0.5/angular.min.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
		<script src="easygallery/js/app-min.js"></script>
	</body>

##### What do I need to run EasyGallery?
You need a Webserver with at least PHP 4 installed. Furthermore the GD library is required for thumbnail creation. You can check your PHP version by calling `<?php phpinfo(); ?>`.

##### GD library is not installed on my server.
Unfortunately you will have to generate your thumbnails manually before using the gallery. Add a `thumbnails/` directory to each of your album folders and for each `PICTUREXX.jpg` create a thumbnail `tn_PICTUREXX.jpg` with 140x140px size. Many image processing programs provide the possibility to automate this process, like for example Irfanview.

If your images contain EXIF geolocation data, you furthermore need 56x56px thumbnails named `gm_PICTUREXX.jpg`.

##### How can i change the size of the gallery?
Take the surrounding div and give it a max-width:

    <div ng-app="easygallery" style="max-width:1280px;">

##### I don't want to use fancybox, can i use another lightbox implementation?
Feel free to customize the code. Replace fancybox in `index.html`, `js/easygallery.js` and `html/gallery.html`.