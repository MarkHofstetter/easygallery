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
		var rest = $(this).data('gallery');
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
});