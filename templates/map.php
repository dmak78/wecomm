<!DOCTYPE html>

<html>

	<head>
		<?php echo '<script>var lon = '.$lon.'; var lat = '.$lat.';</script>';?>
		 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<link href='http://api.tiles.mapbox.com/mapbox.js/v1.0.3/mapbox.css' rel='stylesheet' />
  <!--[if lte IE 8]>
    <link href='http://api.tiles.mapbox.com/mapbox.js/v1.0.3/mapbox.ie.css' rel='stylesheet' />
  <![endif]-->
  <script src='http://api.tiles.mapbox.com/mapbox.js/v1.0.3/mapbox.js'></script>
		 <style>
    body { margin:0; padding:0; }
    #map { display:block; width:100%;height: 400px; }
  </style>
	</head>

	<body style="background-color:#f8f8f8;">
<?php echo $lat; ?>
<br/>
<?php echo $lon; ?>
<br/>
<?php echo $store_id; ?>
<div id="map"></div>
<div id="links"></div>
<div id="images" style="width:900px;margin:0 auto;clear:both;"></div>

<?php echo '<script type="text/javascript">var store_name="'.$store_id.'";</script>'; ?>
<script type='text/javascript'>

function caps(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}

//alert(lonlat[0]);

var locations;
$.getJSON('http://wecomm.herokuapp.com/stores/list_ids', function (d){
	locations = d.rows;
	for (var i = 0 ; i < locations.length; i++){
		$('#links').append('<a style="display:block;float:left;margin:5px;border:1px solid black;" href="http://wecomm.herokuapp.com/stores/'+locations[i].id+'/map">'+locations[i].id+'</a>');
	}
});
var images;
$.getJSON('http://wecomm.herokuapp.com/insta/'+store_name+'', function (d){
	console.log(d);
	for(var i = 0 ; i < d.length; i++){
		var imageUrl = d[i].images.thumbnail.url;
		var linkUrl = d[i].link;
		$('#images').append('<a style="display:inline-block;margin:10px;" href="'+linkUrl+'" target="_blank"><img src="'+imageUrl+'" /></a>');
	}
});

// console.log(images);
var map = L.mapbox.map('map', 'kbleich.map-yrqd6hcp').on('load', function(){
	map.setView([lat, lon], 14);
	var markerLayer = L.mapbox.markerLayer()
    .addTo(map);
	// a simple GeoJSON featureset with a single point
	// with no properties
	L.featureGroup([markerLayer])
    .bindPopup('West Elm ' + caps(store_name))
    .on('click', function() {  })
    .addTo(map);

	markerLayer.setGeoJSON({
	    type: "FeatureCollection",
	    features: [{
	        type: "Feature",
	        geometry: {
	            type: "Point",
	            coordinates: [lon, lat],
	            name: 'West Elm Location'
	        },
	        properties: {  }
	    }]
	});


});

</script>

	</body>

</html>



