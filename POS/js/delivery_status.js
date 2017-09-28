var map;
var distance_service;
var driver = null;
var patient = null;
var messages = null;

function map_height(){
	var page_height = $('.delivery_page').height();
	var height = page_height - $('.delivery_info').outerHeight();
	if(height < page_height / 3) height = page_height / 3;
	$('#delivery_map').height(height);
}

function update_driver(token){
	$.get('delivery/_ajax_driver_position.php', {token: token}, function(response){
		driver.setPosition({lat: parseFloat(response.lat), lng: parseFloat(response.lng)});
		update_time();
		//center_map();
	});
}

function update_time(){
	if(!driver || !patient) return;
	distance_service.getDistanceMatrix({
		origins: [driver.getPosition()],
		destinations: [patient.getPosition()],
		travelMode: 'DRIVING'
	}, function(response, status){
		if(status != 'OK') return false;
		var data = response.rows[0].elements[0];
		if(data.status != 'OK') return false;
		time = new Date();
		time.setSeconds(time.getSeconds()+data.duration.value);
		$('#delivery_eta').text(formatAMPM(time));
	});
}

function formatAMPM(date) {
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var ampm = hours >= 12 ? 'PM' : 'AM';
	hours = hours % 12;
	hours = hours ? hours : 12; // the hour '0' should be '12'
	minutes = minutes < 10 ? '0'+minutes : minutes;
	var strTime = hours + ':' + minutes + ' ' + ampm;
	return strTime;
}

function center_map(){
	if(driver && patient) {	//if we have both driver and patient on map already, fit map to contain both markers
		var bounds = new google.maps.LatLngBounds();
		bounds.extend(driver.getPosition());
		bounds.extend(patient.getPosition());
		map.fitBounds(bounds);
		return;
	}
	if(driver) {
		map.setCenter(driver.getPosition());
		map.setZoom(14);
		return;
	}
	if(patient) {
		map.setCenter(patient.getPosition());
		map.setZoom(14);
	}
}

function update_chat(token){
	$.get('delivery/_ajax_delivery_chat.php', {token: token}, function(response){
		if(!messages) messages = response;
		if(messages.length == response.length) return; //no new messages
		var $container = $('.messages');
		$container.text('');
		$.each(response, function(i, data){
			$container.append('<div class="message '+data.author+'"><span class="time">'+data.time+'</span><span class="text">'+data.message+'</span></div>');
		})
	});
}

function init_map(address, token, driver_location){
	var $map = $('#delivery_map');
	map_height();
	map = new google.maps.Map($map[0], {
		zoom: 5,
		center: {lat: 39.72239846851254, lng: -101.41113370656967}
	});
	distance_service = new google.maps.DistanceMatrixService();

	if(driver_location.lat && driver_location.lng){
		driver = new google.maps.Marker({
			map: map,
			position: driver_location,
			icon: '../POS/images/driver_marker.png'
		});
	}

	var geocoder = new google.maps.Geocoder;
	geocoder.geocode( { address: address}, function(results, status) {
		if (status != 'OK') return;
		map.setCenter(results[0].geometry.location);
		map.setZoom(14);
		patient = new google.maps.Marker({
			map: map,
			position: results[0].geometry.location,
			icon: '../POS/images/home_marker.png'
		});
		update_time();
		center_map();
	});
	setInterval(function(){
		update_driver(token);
		update_chat(token);
	}, 5000);
}