$(document).ready(function(){

	function clear_errors(){
		var $form = $('#addForm');
		$form.find('.form-group').removeClass('has-error');
		$form.find('.help-block').text('');
		$form.find('.alert').remove();
	}

	$('.addStock').click(function(){
		var $modal = $('#addModal');
		var item = $(this).data('item');
		var price = $(this).data('price');
		if(!$modal.length || !item) return;
		clear_errors();
		$modal.find('#addForm').attr('action', '_ajax_add_stock.php?item='+item);
		$modal.find('[name="price"]').val(price ? price : '');
		$modal.find('[name="stock"]').val('');
		$modal.modal();
	});

	$('#addBtn').click(function(){
		var $form = $('#addForm');
		clear_errors();
		$.post($form.attr('action'), $form.serialize(), function(response){
			if(response != 'success') {
				$('<div>', {class: 'alert alert-warning'}).text('server have not returned error or success message').appendTo($form);
				return;
			}
			$('<div>', {class: 'alert alert-success'}).text('successfully updated').appendTo($form);
			setTimeout(function(){
				$('#addModal').modal('hide');
				window.location.reload();
			}, 1000);
		}).fail(function(response){
			if(response.responseJSON && response.responseJSON.errors){	//form validation errors received
				$.each(response.responseJSON.errors, function(name, message){
					var $input = $form.find('[name="'+name+'"]');
					if(!$input.length) return;
					$input.closest('.form-group').addClass('has-error').find('.help-block').text(message);

				});
			} else if(response.responseText.length){	//error text received
				$('<div>', {class: 'alert alert-danger'}).text(response.responseText).appendTo($form);
			} else {	//no response from server
				$('<div>', {class: 'alert alert-danger'}).text('request failed').appendTo($form);
			}
		});
	});
});