<?include '_header_tpl.php'?>
<style>
    .patient-filter .p-border-left{float:left;width:auto;margin-right:20px}
    .patient-filter .email-days-td{float:left;position:relative;bottom:5px;}
    @media (max-width: 767px) {
        .patient-filter .p-border-left{float:none;margin-top:10px;}
        .patient-filter .email-days-td{float:none}
        .patient-filter .div-days{width:100px;}
    }
</style>
<?/*<script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>*/?>
<script>
    $(document).ready(function(){
        getNumPatients();
        $('#patientForm input[name=group]').change(function(){
            getNumPatients();            
        });
         $('#patientForm input[name=type]').change(function(){
            getNumPatients();
            if($(this).val() != 1){
                $('#attachment').css('display', 'none');
                $('#charNum').css('display', 'block');
            }else{
                $('#attachment').css('display', 'block');
                $('#charNum').css('display', 'none');
            }
        });
        
        $('body').on('keyup', "#subject", function(event){checkNumChars(this, event)});
        $('body').on('keypress', "#subject", function(event){checkNumChars(this, event)});
        $('body').on('change', "#subject", function(event){checkNumChars(this, event)});
        $('body').on('click', "#subject", function(event){checkNumChars(this, event)});
        $('body').on('keyup', "#message", function(event){checkNumChars(this, event)});
        $('body').on('keypress', "#message", function(event){checkNumChars(this, event)});
        $('body').on('change', "#message", function(event){checkNumChars(this, event)});
        $('body').on('click', "#message", function(event){checkNumChars(this, event)});
	function checkNumChars(e, k){
            calculateChars();
	}
    });
    function getNumPatients(){
        var group = $('#patientForm input[name=group]:checked').val();
        var method = $('#patientForm input[name=type]:checked').val();
        $.get('_ajax_get_num_recepients.php?group='+group+'&type='+method, function(data){
            $('#numRecepients').text(data.result);
        }); 
    }
    
    function calculateChars(){
        var subjText = $('#subject').val();
        var messText = $('#message').val();
        var numChars = subjText.length*1 + messText.length*1;
        if(subjText.length > 0 && messText.length > 0){
            numChars++;
        }
        $('#charNum').find('span').text(numChars);
        if(numChars > 150){
            $('#charNum').find('span').css('color', '#f00');
        }
        return numChars;
    }
    function sendMessage(){
        if($('#patientForm input[name=type]:checked').val() != 1){ 
            if(calculateChars() < 151){
                $('#patientForm').submit();
            }
        }else{
            $('#patientForm').submit();
        }
        return false;
    }
</script>
<script type="text/javascript"><?/*
tinymce.init({
    selector: ".message",
    theme: "modern",
    width: 800,
    height: 300,
    plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
         "save table contextmenu directionality emoticons template paste textcolor"
   ], 
   image_advtab: true,
   toolbar: "undo redo | styleselect | fontselect | fontsize | fontsizeselect | bold italic | forecolor backcolor emoticons | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage", 
   fontsize_formats: "6pt 8pt 10pt 12pt 14pt 18pt 24pt 36pt 48pt"
 });*/?>
</script>

<section class="content">
        <section class="content-header title-page for-desktop">
          <h2>EMAIL</h2>
        </section>
</section>

<form action="" id="patientForm" method="post" enctype="multipart/form-data">
<input type="hidden" name="sent" value="1" />

<section class="content patient-filter">
    <?if(!empty($_GET['queued'])){?>
    <p class="success"><strong>Your message has been queued and will be delivered to your recepients after moderation</strong></p>
    <?}?>
    <?if(!empty($error)){?>
    <p class="error"><strong><?=$error?></strong></p>
    <?}?>
    
    <div class="p-border-left">
        <span></span>All        
    </div>
    <div class="email-days-td"><div class="div-radio"><input type="radio" name="group" value="0"<?if(@$_POST['group'] or !isset($_POST['group'])) echo ' checked'?>/></div></div>
    <div class="clearfix"></div>

    <div class="p-border-left" style="width:284px">
        <span></span>Purchase not made in the last:             
    </div>
    <div class="email-days-td"><div class="div-days">30 days</div><div class="div-radio"><input type="radio" name="group" value="30"<?if(@$_POST['group'] == 30) echo ' checked'?>/></div></div>    
    <div class="email-days-td"><div class="div-days">60 days</div><div class="div-radio"><input type="radio" name="group" value="60"<?if(@$_POST['group'] == 60) echo ' checked'?>/></div></div>
    <div class="email-days-td"><div class="div-days">90 days</div><div class="div-radio"><input type="radio" name="group" value="90"<?if(@$_POST['group'] == 90) echo ' checked'?>/></div></div>
    <div class="clearfix"></div>
    
    <div class="p-border-left" style="width:284px">
        <span></span>Purchase made in the last:        
    </div>
    <div class="email-days-td"><div class="div-days">30 days</div><div class="div-radio"><input type="radio" name="group" value="b30"<?if(@$_POST['group'] == 'b30') echo ' checked'?>/></div></div> 
    <div class="email-days-td"><div class="div-days">60 days</div><div class="div-radio"><input type="radio" name="group" value="b60"<?if(@$_POST['group'] == 'b60') echo ' checked'?>/></div></div>
    <div class="email-days-td"><div class="div-days">90 days</div><div class="div-radio"><input type="radio" name="group" value="b90"<?if(@$_POST['group'] == 'b90') echo ' checked'?>/></div></div>
    <div class="clearfix"></div>
    
    <div class="p-border-left">
        <span></span>Birthday:        
    </div>
    <div class="email-days-td"><div class="div-days">Today</div><div class="div-radio"><input type="radio" name="group" value="bd"<?if(@$_POST['group'] == 'bd') echo ' checked'?>/></div></div>
    <div class="email-days-td"><div class="div-days">Tomorrow</div><div class="div-radio"><input type="radio" name="group" value="bd2"<?if(@$_POST['group'] == 'bd2') echo ' checked'?>/></div></div>
    <div class="email-days-td"><div class="div-days">3 days</div><div class="div-radio"><input type="radio" name="group" value="bd3"<?if(@$_POST['group'] == 'bd3') echo ' checked'?>/></div></div>
    <div class="email-days-td"><div class="div-days">7 days</div><div class="div-radio"><input type="radio" name="group" value="bd7"<?if(@$_POST['group'] == 'bd7') echo ' checked'?>/></div></div>
    <div class="clearfix"></div>    
</section>

<section class="content">
    <div class="block-bordtop">
        <p class="p-border-left"><span></span>Message Type:</p>
        <div class="email-days">
            <div class="email-days-table">
                <div class="email-days-td"><div class="div-days">Email</div><div class="div-radio"><input type="radio" name="type" value="1" <?if(@$_POST['type'] == 1 or !isset($_POST['type'])) echo ' checked'?>/></div></div>
                <div class="email-days-td"><div class="div-days">SMS</div><div class="div-radio"><input type="radio" name="type" value="2" <?if(@$_POST['type'] == 2) echo ' checked'?>/></div></div>
            </div>
        </div>
    </div> 
</section>

<section class="content">
    <div class="block-bordtop">
        <p class="p-border-left color333"><span></span><font id="numRecepients">0</font> patient(s) will receive this message.</p>
    </div>
    <div class="form-message-1">
        <div class="form-group">
            <label>Subject</label>
            <div class="subject-input"><input type="text" class="form-control" placeholder="" name="subject" id="subject" value="<?=@$subject?>"/></div>
        </div>
        <div class="form-group">
            <label>Messasge</label>
            <div class="notes-textarea"><textarea class="form-control message" name="message" id="message"><?=@$message?></textarea></div>
        </div>
        <p id="charNum" <?if(@$_POST['type'] == 1 or !isset($_POST['type'])) echo 'style="display:none;"'?>>You can enter up to 150 characters. Number of the chars: <span>0</span></p>
        <div class="form-group" id="attachment">
                 <label>Attachment</label>
                 <div class="subject-input"><input type="file" name="attachment" /></div>                 
        </div>        
    </div>
</section>
</form>
<section class="content">
<div class="send"><input type="button" class="button" value="Send" onclick="sendMessage();return false;"/></button></div>
</section>

<section class="content">
    <div class="block-bordtop">
        <p class="p-border-left color333">Inbox</p>
        <?if($aInbox and count($aInbox)){?>
        <div class="table-responsive table-2">
            <table>
                <tr class="listTableGreen ">
                    <th></th>
                    <th>Date</th>
                    <th>Phone Number</th>
                    <th>Message text</th>
                </tr>
                <?foreach($aInbox as $k=>$message){?>
                <tr>
                    <td><?=$k?></td>
                    <td<?if($message['New']) echo ' class="new-message"'?>><?=$message['ReceivedOn']?></td>
                    <td><?=$message['PhoneNumber']?></td>
                    <td><?=!empty($message['Subject']) ? $message['Subject'].' ' : ''?><?=$message['Message']?></td>
                </tr>
                <?}?>
                <tr>
                    <td colspan="2" align="left">
                        <?if($prev){?>
                        <a href="patients_emails.php?page=<?=intval($prev)?>"><< Prev</a>
                        <?}?>
                    </td>
                    <td colspan="2" align="right">
                        <a href="patients_emails.php?page=<?=intval($next)?>">Next >></a>
                    </td>
                </tr>
            </table>
        </div>   
        <?}else{?>
        <?if($page == 1){?>
        <strong class="color858585">No inbox messages</strong>
        <?}else{?>
        <strong class="color858585">No inbox messages at this page <a href="patients_emails.php?page=<?=intval($prev)?>"><< Prev</a></strong>
        <?}?>
        <?}?>
    </div>
</section>

<?include '_footer_tpl.php'?>