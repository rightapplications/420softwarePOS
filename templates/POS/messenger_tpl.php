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
        width:100%;
        font-weight: normal;
        text-align:right;
    }
    a.delete-x{
        color:#f00!important;
        text-decoration: none!important;
    }
    #newMessage{display:none; width:100%; text-align: center; padding:10px}
    
     .petty_cash{
        float:left;
        margin-left: 50px;
        padding-top: 10px;
    }
    .pc_table td{
        border:1px #ccc solid;
    }
</style>
<script>
    <?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1 and !$calledUser){?>
    $(document).ready(function(){
        getUnreadMessByUsers();
        setInterval(getUnreadMessByUsers, 30000);
    });
    function getUnreadMessByUsers(){ 
        $('.userRow').find('span').css('display','none');
        $.get('_ajax_check_incoming_messages_by_users.php', function(data){
            $('.numContainer').text('');
            if(data.result){
                var numbers = data.data; 
                $.each(numbers, function(key, val){
                    if(val.num != 0 ){
                        $('#mess'+val.id).css('display', 'inline');
                        $('#mess'+val.id).text(val.num);
                    }
                });
            }
        });
    }
    <?}else{?>
    $(document).ready(function(){
        getUnreadUserMess(<?=intval($recepient)?>);
        setInterval(function(){getUnreadUserMess(<?=intval($recepient)?>)}, 30000);
    });
    function getUnreadUserMess(sender){ 
        $.get('_ajax_check_incoming_user_messages.php?sender='+sender, function(data){
            var newMessText ;
            if(data.result){
                var numMessages = data.data;
                if(numMessages > 0){
                    if(numMessages == 1){
                        newMessText = 'New message received. Click here to read.';
                    }else{
                        newMessText = numMessages +' new messages received. Click here to read.';
                    }
                }else{
                    newMessText = '';
                }
            }
            if(newMessText == ''){
                $('#newMessage').css('display', 'none');
            }else{
                $('#newMessage').css('display', 'block');
            }
            $('#newMessage').find('a').text(newMessText);
        });
    }
    <?}?>   
</script>


<!-- start content title-page -->
<section class="content">
        <section class="content-header title-page for-desktop">
                <h2>TASKS</h2>
        </section>
</section>
<!-- stop content title-page -->

<?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1 and !$calledUser){?>
<!-- start Mailing Address -->
<section class="content">
      <div class="block-bordtop" style="border-top:none">
              <p class="p-border-left"><span></span>User List</p>
      </div> 
</section>
<!-- stop Mailing Address -->
<!-- start Category List -->
<section class="content">
    <div class="checkou-button user-button">
        <?foreach($aUsers as $user){?>
            <?if($user['id'] != 1){?>
        <button class="button" onclick="parent.location='messenger.php?id=<?=$user['id']?>'"><?=$user['firstname']?> <?=$user['lastname']?> <span id="mess<?=$user['id']?>" class="numContainer num-unread-messages"></span></button>
            <?}?>
        <?}?>
    </div>  		
</section>
<!-- stop Category List -->
<?}else{?>
<!-- start Mailing Address -->
<section class="content">
    <div class="block-bordtop">
        <p class="p-border-left"><span></span>Write a message to <?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?><?=$aCalledUser['firstname']?> <?=$aCalledUser['lastname']?><?}else{?>Administrator<?}?>:</p>
    </div> 
</section>
<!-- stop Mailing Address -->
<form action="" method="post">
<section class="content">
    <div class="form-message-1 notes-1">
        <div class="form-group">
            <label>Notes</label>
            <div class="notes-textarea">
                <textarea name="message" cols="" rows="" class="form-control messagebox"></textarea>
            </div>
        </div>
        <div class="send"><input type="submit" class="button" value="Send"></button></div>
    </div> 		
</section>    
</form>


<section class="content">
    <div class="block-bordtop"></div> 
    <?if(!empty($aMessages)){?>
        <div class="conversation-container">
            <div id="newMessage"><a href="<?=$_SERVER['REQUEST_URI']?>"></a></div>
            <?foreach($aMessages as $mess){?>
        <div class="messageblock <?if($mess['sender_id'] == $_SESSION[CLIENT_ID]['user_superclinic']['id']) echo 'outcoming ';else echo 'incoming ';?><?if($mess['status'] == 0) echo 'unread'?>">
            <div class="message-time">
                <?if($mess['sender_id'] == $_SESSION[CLIENT_ID]['user_superclinic']['id']) echo '&#8592;';else echo '&#8594;';?><?= strftime(DATE_FORMAT." %I:%M%p", $mess['date'])?>
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>
                <a href="messenger.php?id=<?=$calledUser?>&delmessage=<?=$mess['id']?>" onclick="return confirm('Are you sure you want to delete this message?')" class="delete-x"><i class="fa fa-times"></i></a>
                <?}?>
            </div>
            <?=  nl2br($mess['content'])?>
        </div>
        <div class="clearfix"></div>                
            <?}?>
        </div>
    <?}else{?>
        <p>You don't have a conversation with this user</p>
    <?}?>
</section> 

<?}?>

<?include '_footer_tpl.php'?>