angular.module('easygallery', []).
  config(function($routeProvider, $locationProvider) {
    $routeProvider.
      when('/', {controller:GalleryCtrl, templateUrl:'easygallery/html/gallery.html'}).
      when('/gallery/:folder', {controller:ImageCtrl, templateUrl:'easygallery/html/images.html'}).
      when('/map/:folder', {controller:MapCtrl, templateUrl:'easygallery/html/map.html'}).
      otherwise({redirectTo:'/'});
  });

function GalleryCtrl($scope, $http) {
	$http({
	    url: "easygallery/php/gallery.php/gallery",
	    method: "GET"
	}).success(function(data, status, headers, config) {
	    $scope.previews = data.previews;
	}).error(function(data, status, headers, config) {
	    $scope.status = status;
	});
}

function ImageCtrl($scope, $http, $routeParams) {
	$http({
	    url: "easygallery/php/images.php/images/" + $routeParams.folder,
	    method: "GET"
	}).success(function(data, status, headers, config) {
	    $scope.images = data.images;
	    $scope.dir = data.dir;
	    $scope.exifavailable = data.exifavailable;
	}).error(function(data, status, headers, config) {
	    $scope.status = status;
	});
}

function MapCtrl($scope, $routeParams) {

}

// TODO google map
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
		zoom : 1,
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
	
	var bounds = new google.maps.LatLngBounds()
	for ( var i=0;i<coords.length;i++) {
	    bounds.extend(coords[i]);
	    map.fitBounds(bounds);
	}
}

function setMarkers(map, locations, images) {
	for (var i = 0; i < locations.length; i++) {
		var image = new google.maps.MarkerImage(images[i], new google.maps.Size(56, 56),
			new google.maps.Point(0, 0),
			new google.maps.Point(32, 52));
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