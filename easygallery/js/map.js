function initialize() {
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
	
	var styledMap = new google.maps.StyledMapType(styles, {
		name : "Styled Map"
	});
	var myOptions = {
		center : new google.maps.LatLng(53.553312,9.992666),
		zoom : 10,
		mapTypeId : google.maps.MapTypeId.ROADMAP,
		mapTypeControlOptions : {
			mapTypeIds : [google.maps.MapTypeId.ROADMAP, 'map_style']
		}
	};
	var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	map.mapTypes.set('map_style', styledMap);
	map.setMapTypeId('map_style');
	return map;
}

function setRoute(map, images) {
	var thumbnails = [];
	var locations = [];
	var imageRefs = [];
	var count = 0;
	for (var i = 0; i < images.length; i++) {
		if (isNotEmpty(images[i].exif)) {
			thumbnails[count] = images[i].exif.thumbnail;
			locations[count] = new google.maps.LatLng(images[i].exif.gps.lat, images[i].exif.gps.lng);
			imageRefs[count] = images[i].img;
			count++;
		}
	}
	var mypath = new google.maps.Polyline({
		path : locations,
		strokeColor : '#000088',
		strokeOpacity : 0.6,
		strokeWeight : 3,
		fillOpacity : 0,
		map : map
	});

	for (var i = 0; i < count; i++) {	
		var image = new google.maps.MarkerImage(thumbnails[i], new google.maps.Size(56, 56), new google.maps.Point(0, 0), new google.maps.Point(32, 52));
		var marker = new google.maps.Marker({
			position : locations[i],
			map : map,
			icon : image,
			url : imageRefs[i].src,
			title: imageRefs[i].name
		});
		google.maps.event.addListener(marker, 'click', function() {
			window.location.href = this.url;
		});
	}

	var bounds = new google.maps.LatLngBounds()
	for (var i = 0; i < locations.length; i++) {
		bounds.extend(locations[i]);
		map.fitBounds(bounds);
	}
	return map;
}

function isNotEmpty(obj) {
    for(var prop in obj) {
        if(obj.hasOwnProperty(prop))
            return true;
    }
    return false;
}