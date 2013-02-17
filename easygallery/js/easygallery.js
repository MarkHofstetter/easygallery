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
	
	var googleanalytics = {propertiesId: ""};
	$.get('easygallery/html/googleanalytics.mustache', function(template) {
	    var html = Mustache.to_html(template, googleanalytics);
	    $("#googleanalytics").html(html);
	});
});

$(document).ajaxComplete(function() {
	$(".gallerylink").click(function(e){
		var rest = $(this).parent().data('gallery');
		$.getJSON("easygallery/php/images.php/images/" + rest,
			function(data){
				$.get('easygallery/html/pictures.mustache', function(template) {
				    var html = Mustache.to_html(template, data);
				    $("#easygallery").html(html);
				});
			}
		);
	});
	
	$("#backlink").click(function(e){
		$.getJSON("easygallery/php/folders.php/folders",
			function(data){
				$.get('easygallery/html/previews.mustache', function(template) {
				    var html = Mustache.to_html(template, data);
				    $("#easygallery").html(html);
				});
			}
		);
	});
	
	$("#gmlink").click(function(e){
		var rest = $(this).parent().data('gallery');
		$.getJSON("easygallery/php/images.php/images/" + rest,
			function(data){
				initialize(data);
			});
		}
	);
});

function initialize(data) {
	// Create an array of styles.
	var styles = [{
		stylers : [{
			hue : "#00ffe6"
		}, {
			saturation : -10
		}]
	}, {
		featureType : "road",
		elementType : "geometry",
		stylers : [{
			lightness : 100
		}, {
			visibility : "simplified"
		}]
	}, {
		featureType : "road",
		elementType : "labels",
		stylers : [{
			visibility : "off"
		}]
	}];
	// Create a new StyledMapType object, passing it the array of styles,
	// as well as the name to be displayed on the map type control.
	var styledMap = new google.maps.StyledMapType(styles, {
		name : "Styled Map"
	});
	var myOptions = {
		zoom : 14,
		mapTypeId : google.maps.MapTypeId.ROADMAP,
		mapTypeControlOptions : {
			mapTypeIds : [google.maps.MapTypeId.ROADMAP, 'map_style']
		}
	};
	$("#gallerycanvas").height('90%');
	var map = new google.maps.Map(document.getElementById("gallerycanvas"), myOptions);
	//Associate the styled map with the MapTypeId and set it to display.
	map.mapTypes.set('map_style', styledMap);
	map.setMapTypeId('map_style');

	// fill with data
	var thumbnails = [];
	var coords = [];
	var count = 0;
	for(var i=0;i<data.images.length;i++){
		if(!jQuery.isEmptyObject(data.images[i].exif)){
			thumbnails[count] = data.images[i].exif.thumbnail;
			coords[count] = new google.maps.LatLng(data.images[i].exif.gps.lat, data.images[i].exif.gps.lng);
			count++;
		} 
	}
	// center map
	var latsum = 0;
	var lngsum = 0;
	for(var i=0;i<coords.length;i++){
		latsum += coords[i].lat();
		lngsum += coords[i].lng(); 
	}
	var center = new google.maps.LatLng(latsum / coords.length, lngsum / coords.length);
	map.setCenter(center);
	// Construct the polygon
	var mypath = new google.maps.Polyline({
		path : coords,
		strokeColor : '#000088',
		strokeOpacity : 0.6,
		strokeWeight : 3,
		fillOpacity : 0,
		map : map
	});
	// set markers
	setMarkers(map, coords, thumbnails);
}

function setMarkers(map, locations, images) {
	for (var i = 0; i < locations.length; i++) {
		var image = new google.maps.MarkerImage(images[i], new google.maps.Size(48, 48),
			new google.maps.Point(0, 0),
			new google.maps.Point(24, 46));
		var marker = new google.maps.Marker({
			position : locations[i],
			map : map,
			icon : image,
			url : '//localhost/projects/SRC/'
		});
		google.maps.event.addListener(marker, 'click', function() {
			window.location.href = marker.url;
		});
	}
}