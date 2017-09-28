<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Delivery</title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
		<meta name="format-detection" content="telephone=no" />
		<meta http-equiv="x-rim-auto-match" content="none" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<link rel="icon" href="favicon.ico" type="image/x-icon" />
		<link rel="icon" href="favicon.png" type="image/x-icon" />
		<link href="https://fonts.googleapis.com/css?family=Questrial" rel="stylesheet">
		<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="font-awesome-4.6.3/css/font-awesome.css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
		<link rel="stylesheet" href="dist/css/admin.css">
		<link rel="stylesheet" href="css/delivery_status.css">
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<header>
			<a href="/" class="logo">
				<img src="images/drivers_logo.png" alt="">
			</a>
		</header>

		<div class="delivery_page">
			<?php if(!empty($review_completed)) { ?>
				<div class="success"><div class="message">Thank you for your review, your order has been completed. This page will be no longer available.</div></div>
			<?php } else { ?>
			<div id="delivery_map"></div>
			<div class="delivery_info">
				<div class="driver_data">
					<div class="driver_img" style="background-image:url('<?=DRIVER_IMG_URL.$driver['img'];?>')"></div>
					<p class="name"><?=$driver['firstname'];?> <?=$driver['lastname'];?></p>
				</div>
				<div class="chat right <?=$order['status'] == ORDER_STATUS_COMPLETED ? 'hidden' : '';?>">
					<div class="messages">
						<?php foreach($messages as $message){ ?>
							<div class="message <?=$message['author'] == AUTHOR_PATIENT ? 'patient' : 'driver';?>">
								<span class="time"><?=date('H.i', strtotime($message['date']));?></span>
								<span class="text"><?=$message['message'];?></span>
							</div>
						<?php }?>
					</div>
					<form method="post" class="new_message">
						<input type="text" name="message" placeholder="Create message"/>
						<button type="submit"></button>
					</form>
					<div class="delivery_time">Estimated delivery time : <span id="delivery_eta"></span></div>
				</div>
				<div class="delivery_review right <?=$order['status'] == ORDER_STATUS_COMPLETED ? '' : 'hidden';?>">
					<div class="delivery_message">Your order has been delivered</div>
					<form method="post" class="review">
						<div class="stars">
							<?php for($i=5; $i>=1; $i--) {?><input type="radio" name="rating" value="<?=$i?>" id="rating<?=$i?>"><label for="rating<?=$i?>"></label><?php } ?>
						</div>
						<textarea type="text" name="review"></textarea>
						<button type="submit">Submit</button>
						<div class="delivery_request">Please leave your review to our driver</div>
					</form>
				</div>
			</div>
			<?php } ?>
		</div>

		<footer class="main-footer">
			<?$cD = getdate();?>
			Copyright &copy; <?=$cD['year']?>
		</footer>
		<?php if(empty($review_completed)) { ?>
		<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
		<script type="text/javascript" src="js/delivery_status.js"></script>
		<script type="text/javascript">
			function init(){
				init_map('<?=$order['address'];?>', '<?=$order['delivery_token'];?>', {lat: <?=(float)$driver['lat'];?>, lng: <?=(float)$driver['lng'];?>});
			}
		</script>
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0qrvayv1BSCN4t1yGTtItLumHi9f6N6U&callback=init"></script>
		<?php } ?>
	</body>
</html>