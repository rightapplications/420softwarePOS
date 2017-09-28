$(document).ready(function(){

	function load_history(){
		var $btn = $(this);
		$.get('delivery/_ajax_orders_history.php', history_range, function(response){
			if(!response.orders) {
				console.error('no orders received');
				return;
			}
			var template = $('.order_template').html();
			if(!template.length) {
				console.error('order template not found');
				return;
			}
			$.each(response.orders, function(i, order){
				var $elm = $(template);
				$elm.find('.id').html(order.id).attr('href', 'reports_order_details.php?id='+order.id);
				$elm.find('.date').html('<b>ordered at:</b> '+order.date);
				$elm.find('.patient').html('<b>ordered by:</b> '+(order.patient ? order.patient : 'unknown patient'));
				$elm.find('.address').html('<b>address:</b> '+order.address);
				$elm.find('.driver').html('<b>delivered by:</b> '+(order.driver ? order.driver : ''));
				if(order.items){
					$elm.find('.items').html('<b>items:</b> '+order.items);
					$elm.find('.total').html('<b>total:</b> $'+order.total);
				}
				$('.orders_history').append($elm);
			});
			$btn.remove();
		});
	}

	$('.load_history').click(load_history);
});