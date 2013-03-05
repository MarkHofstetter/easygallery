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
	    document.getElementById("map_canvas").className = 'hide';
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
	    $scope.download = data.download;
	    $scope.exifavailable = data.exifavailable;
		document.getElementById("map_canvas").className = 'hide';
	}).error(function(data, status, headers, config) {
	    $scope.status = status;
	});
	// open in fancybox
	$(".images").fancybox();
	$(".images-media").fancybox({
		openEffect  : 'none',
		closeEffect : 'none',
		helpers : {
			media : {}
		}
	});
}

function MapCtrl($scope, $http, $routeParams) {
	$http({
	    url: "easygallery/php/images.php/images/" + $routeParams.folder,
	    method: "GET"
	}).success(function(data, status, headers, config) {
	    $scope.images = data.images;
	    $scope.dir = data.dir;
	    $scope.download = data.download;
	    document.getElementById("map_canvas").className = 'show';
	    var map = initialize(data);
	    map = setRoute(map, data.images);
	    $scope.map = map;
	}).error(function(data, status, headers, config) {
	    $scope.status = status;
	});
}