<?include '_header_tpl.php'?>
<style>
    .conversation-container{
        height: 600px;
        overflow-y:auto;
    }
    .messagebox{
        width:100%;
        height: 150px;
    }
    .messageblock{
        padding:20px;
        width: 85%;
        margin: 10px 10px;
    }
    .incoming{
        float:left;
        background-color: #dafcdb;
    }
    .outcoming{
        float:right;
        background-color: #eee;
    }
    .unread{
        font-weight: bold;
    }
    .message-time{
    	float: right;
    	padding-left: 50px;
        font-weight: normal;
        text-align:right;
    }
</style>
<script>
function sendMessage(){
    if($('#message').val() != ''){ 
        $('#patientForm').submit();
    }
    return false;
}
</script>
<section class="content">
        <section class="content-header title-page for-desktop">
          <h2>Request</h2>
        </section>
</section>

<section class="content">
    <?if(!empty($_GET['sent'])){?>
    <p class="success" style="color:#34a136"><strong>Your message has been sent to our support team. We will contact you shortly</strong></p>
    <?}?>
    <form action="" id="patientForm" method="post">
        <input type="hidden" name="sent" value="1" />
        <div class="form-message-1">
            <div class="form-group">
                <label>Please describe your issue:</label>
                <div class="notes-textarea"><textarea class="form-control message" name="message" id="message"></textarea></div>
            </div>      
        </div>
    </form>
</section>
<section class="content">
    <div class="send"><input type="button" class="button" value="Submit" onclick="sendMessage();return false;"/></button></div>
</section>
<section class="content">
	<div class="block-bordtop"></div>
	<?php foreach($messages as $message){ ?>

	<div class="messageblock <?=$message['is_admin'] ? 'incoming ' : 'outcoming ';?><?=$message['status'] ? '' : 'unread'?>">
		<div class="message-time">
			<?=$message['is_admin'] ? '&#8594;' : '&#8592;';?><?= strftime(DATE_FORMAT." %I:%M%p", $message['date_received'])?>
		</div>
		<?=nl2br($message['message'])?>
	</div>

	<?php } ?>
	<div class="clearfix"></div>
</section>
<?include '_footer_tpl.php'?>