var geocoder = null;
var map = null;
var distance_service = null;
var directions_service = null;

function init_map(){
	var $map = $('#delivery_map');
	map = new google.maps.Map($map[0], {
		zoom: 5,
		center: {lat: 39.72239846851254, lng: -101.41113370656967}
	});
	geocoder = new google.maps.Geocoder;
	distance_service = new google.maps.DistanceMatrixService();
	directions_service = new google.maps.DirectionsService();
	var bounds = new google.maps.LatLngBounds();
	$.each(orders, function(index, order){
		add_order_marker(order);
		if(order.marker) bounds.extend(order.marker.getPosition());
	});
	$.each(groups, function(index, group){
		if(!group.orders) return;
		$.each(group.orders, function(index, order){
			add_order_marker(order, group.color);
			if(order.marker) bounds.extend(order.marker.getPosition());
		});
		update_group_route(group);
	});
	if(home.coords){
		home.marker = new google.maps.Marker({
			map: map,
			position: home.coords,
			icon: '../POS/images/home_marker.png'
		});
		bounds.extend(home.marker.getPosition());
	}
	$.each(drivers, function(index, driver){
		if(!driver.lat || !driver.lng) return;
		driver.marker = create_driver_marker(driver);
		bounds.extend(driver.marker.getPosition());
	});
	map.fitBounds(bounds);
}

function create_driver_marker(driver){
	var marker = new google.maps.Marker({
		map: map,
		position: {lat: parseFloat(driver.lat), lng: parseFloat(driver.lng)},
		icon: '../POS/images/driver_marker.png'
	});
	marker.addListener('click', function() {
		if(!marker.infowindow){	//already was calculated
			marker.infowindow = new google.maps.InfoWindow({
				content: '<img class="map_img" src="../gallery/drivers/'+driver.img+'"><span class="map_driver">'+driver.firstname+' '+driver.lastname+'<br/>'+driver.phone+'</span>'
			});
		}
		marker.infowindow.open(map, driver.marker);
	});
	return marker;
}

function create_marker_icon(text, color){
	if(!color) color = 'FE7569';
	var base = {x:19 ,y:34};	//base size of 0.5 icon
	var modifier = (text.length - 1) * 0.4 + 1;
	return {
		url: 'http://chart.apis.google.com/chart?chst=d_map_spin&chld='+(0.5*modifier)+'|0|'+color+'|12|_|'+text ,
		size: new google.maps.Size(base.x*modifier, base.y*modifier),
		origin: new google.maps.Point(0, 0),
		anchor: new google.maps.Point(base.x*modifier/2, base.y*modifier)
	};
}

function add_order_marker(order, color){
	if(!order.address) return;

	function create_marker(location){
		order.marker = new google.maps.Marker({
			map: map,
			position: location,
			icon: create_marker_icon(order.id, color)
		});
		order.marker.order = order;
		order.marker.addListener('mouseover', function() {
			order.elm.addClass('highlight');
		});
		order.marker.addListener('mouseout', function() {
			order.elm.removeClass('highlight');
		});
		order.marker.addListener('click', function() {
			calc_distance_to_home(order.marker);
		});
	}

	if(parseFloat(order.lat) && parseFloat(order.lng)) {
		create_marker({lat: parseFloat(order.lat), lng: parseFloat(order.lng)});
		return;
	}
	geocoder.geocode( {
		address: order.address,
		componentRestrictions: {
			country: 'US'
		}
	}, function(results, status) {
		if (status != 'OK') return;
		var location = results[0].geometry.location;
		order.lat = location.lat();
		order.lng = location.lng();
		create_marker(location);
	});
}

function calc_distance_to_home(marker){
	if(marker.infowindow){	//already was calculated
		marker.infowindow.open(map, marker);
		return;
	}
	marker.infowindow = new google.maps.InfoWindow({
		content: home.coords ? 'calculating...' : 'delivery base coordinates are unknown, cannot calculate distance to it'
	});
	marker.infowindow.open(map, marker);
	if(!home.coords) return;
	distance_service.getDistanceMatrix({
		origins: [home.coords],
		destinations: [marker.getPosition()],
		travelMode: 'DRIVING'
	}, function(response, status){
		if(status != 'OK') {
			marker.infowindow.setContent('unable to calculate');
			return false;
		}
		var data = response.rows[0].elements[0];
		if(data.status != 'OK') {
			marker.infowindow.setContent('unable to calculate');
			return false;
		}
		time = new Date();
		time.setSeconds(data.duration.value);
		marker.infowindow.setContent('<div>distance: '+data.distance.text+'</div><div>time: '+data.duration.text+'</div>');
	});
}

function format_time(time) {
    var sec_num = parseInt(time, 10); // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    return hours+':'+minutes+':'+seconds;
}

function get_miles(i) {
     return (i*0.000621371192).toFixed(2);
}

function update_group_route(group){
	if(!group) return;
	if(!group.orders.length) {	//no orders left int group, do not recalculate and just hide route
		group.directions_display.setMap(null);
		return;
	}
	var waypoints = [];
	$.each(group.orders, function(i, order){
		waypoints.push({
			//use order address if order is not geolocated
			location: (parseFloat(order.lat) && parseFloat(order.lng)) ? {lat: parseFloat(order.lat), lng: parseFloat(order.lng)} : order.address,
			stopover: true
		});
	});
	var last = waypoints.pop();
	directions_service.route({
		origin: home.coords,
		destination: last.location,
		travelMode: 'DRIVING',
		unitSystem: google.maps.UnitSystem.IMPERIAL,
		waypoints: waypoints,
	}, function(result, status) {
		if (status != 'OK') return;
		var route = result.routes[0];
		var distance = 0;
		var duration = 0;
		$.each(route.legs, function(index, part){
			if(!group.orders[index]) return;
			group.orders[index].elm.find('.directions').text(part.distance.text+', '+part.duration.text);
			distance += part.distance.value;
			duration += part.duration.value;
		});
		group.elm.find('.group_directions').text('total: '+get_miles(distance)+' miles, '+format_time(duration));
		if(!group.directions_display){
			//each group has its own directions display because single directions display can only display single route
			group.directions_display = new google.maps.DirectionsRenderer();
			group.directions_display.setMap(map);
			group.directions_display.setOptions({draggable: false, suppressMarkers: true});
		}
		group.directions_display.setOptions({polylineOptions: {strokeColor: '#'+group.color}});
		group.directions_display.setDirections(result);
	});
}

$(document).ready(function(){

	function show_message(message, elm){	//this should be improved to show popup errors
		alert(message);
	}

	//update database data on server
	function update_group(group){
		if(!group || !group.orders) return;
		var data = [];
		$.each(group.orders, function(i, order){
			order.position = order.elm.index();
			data.push({id: order.id, position: order.position});
		});
		group.orders.sort(function(a, b){return a.position - b.position});	//sort orders in group array same as they are in html
		$.post('delivery/_ajax_order_group.php', {group: group.id, orders: data}, function(response){
			if(response.group_id) group.id = response.group_id;
			group.elm.find('.driver_change').show();
		});
		update_group_route(group);
	}

	function save_origin(event, ui){
		$origin = ui.item.parent();
	}

	function order_drop(event, ui){
		var $destination = ui.item.parent();
		var togroup = $destination.closest('.group').data('group');
		if(togroup && togroup.status == 3) {
			if(!$.browser.mobile) $(this).sortable('cancel');
			alert('this group is already being delivered and cannot be changed');
		}

		var order = ui.item.data('order');
		//moved to another container
		if($origin[0] != $destination[0]) {
			order_change_group(order, $origin.closest('.group').data('group'), togroup);
			return;
		}
		//move was in a same container
		if(!togroup) return;	//order was moved in orders container, no changes were made
		update_group(togroup);	//update orders order
	}

	//moves order to specified group. if group not specified it is moved to orders
	function order_change_group(order, fromgroup, togroup){
		if(!order) return;
		if(fromgroup == togroup) return;
		//determine from which array to which element should be transferred and transfer it
		var from = fromgroup ? fromgroup.orders : orders;
		var to = togroup ? togroup.orders : orders;
		var i = from.indexOf(order);
		if(i >= 0) {
			to.push(order);
			from.splice(i, 1);
		}
		if(fromgroup && !fromgroup.orders.length) remove_group(fromgroup);
		if(!togroup){	//moved from group to orders list
			$.post('delivery/_ajax_order_ungroup.php', {order: order.id}, function(){	//remove group id from order in database
				order.elm.find('.directions').text('');
				set_marker_color(order, 'FE7569');
			});
			return;
		}
		if(fromgroup) update_group_route(fromgroup);
		set_marker_color(order, togroup.color);
		update_group(togroup);
	}

	function set_marker_color(order, color){
		if(!order.marker || !color) return;
		order.marker.setIcon(create_marker_icon(order.id, color));
	}

	function remove_group(group){
		group.elm.remove();
		var i = groups.indexOf(group);
		if(i >= 0) groups.splice(i, 1);
		if(group.directions_display) group.directions_display.setMap(null);
		update_group_selectors();
	}

	function approve_group(){
		var $button = $(this);
		var $group = $button.closest('.group');
		var group = $group.data('group');
		if(!$group.find('.order').length) {
			show_message('You should add at least single order to group before giving it to drivers', $group);
			return;
		}
		$.post('delivery/_ajax_group_status.php', {group: group.id, status: 2}, function(response){
			if(response.driver) {
				group.driver = response.driver;
				$group.find('.group_driver').text(response.driver);
			}
			if(response.status){
				group.status = response.status;
				var label = status_labels[response.status] ? status_labels[response.status] : 'ready';
				$group.find('.group_status').text(label);
			}
		});
		$button.remove();
	}

	function cancel_group(){
		var $group = $(this).closest('.group');
		var group = $group.data('group');
		if(group.status == 3 && !confirm('are you sure you want to delete group that is already being delivered?')) return;
		if(group.id) {
			$.each(group.orders, function(i, order){
				set_marker_color(order, 'FE7569');
				order.elm.appendTo('.orders');
			});
			orders = orders.concat(group.orders);
			$.post('delivery/_ajax_delete_group.php', {id: group.id});
		}
		remove_group(group);
	}
	
	function update_data(){
		$.get('delivery/_ajax_delivery_update.php', function(response){
			$.each(response.orders, function(i, order){
				var found = false;
				$.each(orders, function(i, existing){
					if(existing.id == order.id) {
						found = true;
						return false;
					}
				});
				if(!found){	//append new order to orders list
					orders.push(order);
					order.elm = create_order_elm(order)
					$('.dev_groups_page > .orders').append(order.elm);
					if(!$.browser.mobile) $('.dev_groups_page > .orders').sortable('refresh');
					add_order_marker(order);
				}
			});
			$.each(response.drivers, function(i, updated){
				$.each(drivers, function(i, existing){
					if(existing.id != updated.id) return;
					if(!updated.lat || !updated.lng) return;
					existing.lat = parseFloat(updated.lat);
					existing.lng = parseFloat(updated.lng);
					existing.status = updated.status;
					if(!existing.marker) existing.marker = create_driver_marker(existing);
					existing.marker.setPosition({lat: existing.lat, lng: existing.lng});
					return false;
				});
			});
			groups = $.grep(groups, function(group){
				if(!group.id) return true;	//empty new group
				var found = false;
				$.each(response.groups, function(id, srv_group){	//check if group status or driver has changed
					if(group.id != id) return;
					group.driver = srv_group.driver;
					update_driver_selector(group);
					if(group.status != srv_group.status && status_labels[srv_group.status]) {
						group.status = srv_group.status;
						group.elm.find('.group_status').text(status_labels[srv_group.status]);
						if(group.status == 3 || group.status == 4) group.elm.addClass('uneditable');
						if(group.status == 4) group.elm.find('.group_payed').show();
					}
					found = true;
				});
				if(!found) remove_group(group);	//group was delivered
				return found;
			});
		});
	}

	function create_order_elm(order){
		var $elem = $(order_template);
		$elem.find('.id').html(order.id);
		$elem.find('.date').html('<b>ordered at:</b> '+order.date);
		if(order.patient){
			$elem.find('.patient').html('<b>patient:</b> '+order.patient);
		}
		$elem.find('.address').html('<b>address:</b> '+order.address);
		if(order.appointment_time){
			$elem.find('.appoint').html('<b>appointment at:</b> '+order.appointment_time);
		}
		if(order.items){
			$elem.find('.items').html('<b>items:</b> '+order.items);
			$elem.find('.total').html('<b>total:</b> $'+order.total);
		}
		if($.browser.mobile) {
			var $sel = $elem.find('.group-sel');
			$sel.click(function(){
				$(this).toggleClass('open');
			}).on('click', '.select div', function(){
				var newgrp = $(this).data('group');
				order_change_group(order, $elem.closest('.group').data('group'), newgrp);
				var move_to = newgrp ? newgrp.elm.find('.orders') : $('.orders.col');
				$elem.appendTo(move_to);
				$sel.css('background-color', newgrp ? '#'+newgrp.color : '#fff');
			});
		}
		$elem.data('order', order);
		return $elem;
	}

	function create_group_elm(group){
		var $elem = $(group_template);
		var $orders = $elem.find('.orders');

		group.elm = $elem;
		$elem.data('group', group);

		$.each(group.orders, function(i, order){
			if(!order.elm) order.elm = create_order_elm(order);
			order.elm.appendTo($orders);
		});

		if(group.status == 1) $elem.find('.group_approve').click(approve_group);
		if(group.status != 1) $elem.find('.group_approve').remove();
		if(!$.browser.mobile && (group.status == 1 || group.status == 2)) $orders.sortable(settings).disableSelection();
		if(group.status == 3 || group.status == 4) $elem.addClass('uneditable');
		if(group.status != 4) $elem.find('.group_payed').hide();

		$elem.find('.group_payed').click(function(){
			$.post('delivery/_ajax_group_payed.php', {group: group.id}, function(){
				remove_group(group);
			});
		});

		$elem.find('.group_status').text(status_labels[group.status]);
		$elem.css('background', '#'+group.color);
		$elem.find('.group_cancel').click(cancel_group);
		update_driver_selector(group);
		var $select = group.elm.find('.driver_change');
		$select.change(function(){
			var driver = $(this).val();
			$.post('delivery/_ajax_set_driver.php', {driver: driver, group: group.id}, function(){
				group.driver = driver;
			});
		});
		if(!group.id) $select.hide();
	}

	function add_group(){
		if(last_color >= colors.length) last_color = 0;
		var group = {
			id: 0,
			color: colors[last_color],
			elm: null,
			orders: [],
			status: 1,
			driver: null
		}
		last_color++;
		create_group_elm(group);
		$('.groups').prepend(group.elm);
		groups.push(group);
		if($.browser.mobile) update_group_selectors();
		return group;
	}

	function update_driver_selector(group){
		var $select = group.elm.find('.driver_change');
		$select.html('');	//remove existing options
		$select.append('<option value="">Autoselect</option>');
		$.each(drivers, function(i, driver){
			$select.append('<option value="'+driver.id+'">'+driver.firstname+' '+driver.lastname+'('+(driver.status==1 ? 'free' : 'busy')+')</option>')
		});
		if(group.driver) $select.val(group.driver);
		if(group.status == 3 || group.status == 4) $select.attr('disabled', 'disabled')
	}

	//updates group selection elements which are used in mobile version
	function update_group_selectors(){
		var selection = $('<div>').css('background-color', '#fff');
		$.each(groups, function(i, group){
			var newgrp = $('<div>');
			newgrp.css('background-color', '#'+group.color);
			newgrp.data('group', group);
			selection = selection.add(newgrp);
		});
		$('.group-sel .select').html(selection);
	}

	var $origin;
	var settings = {
		connectWith: '.orders',
		cursor: 'move',
		items: '.order',
		start: save_origin,
		stop: order_drop
	};
	var order_template = $('#order_template').html();
	var group_template = $('#group_template').html();

	if($.browser.mobile) $('.dev_groups_page').addClass('mobile');

	var $orders = $('.orders');
	$.each(orders, function(i, order){
		order.elm = create_order_elm(order);
		order.elm.appendTo($orders);
	});
	if(!$.browser.mobile) $('.orders').sortable(settings).disableSelection();

	var $groups = $('.groups');
	$.each(groups, function(i, group){
		create_group_elm(group);
		group.elm.appendTo($groups);
	});
	update_group_selectors();

	$('.add_group').click(add_group);
	setInterval(update_data, 5000);
});