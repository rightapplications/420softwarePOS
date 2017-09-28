<?include '_header_tpl.php'?>
<style>
    .schedule input{
        width:120px;
        height:32px;
        margin:5px;
        line-height: 10px;
        padding-left: 5px;
        padding-right:5px;
    }
</style>
<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Schedule</h2>
    </section>
</section>
<!-- stop content title-page -->


<!-- start Save Cancel -->
<section class="content">
    <div class="checkou-block">
        <div class="checkou-button">
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1 or !empty($_SESSION[CLIENT_ID]['user_superclinic']['edit_schedule'])){?>
            <?if($edit_mode){?>
            <button class="button" onclick="document.schedule_form.submit();">Save</button>
            <button class="button" onclick="parent.location='employees_schedule.php'">Cancel</button>
            <?}else{?>
            <button class="button" onclick="parent.location='employees_schedule.php?edit=1'">Edit</button>
            <?}?>
            <?}?>
            <button class="button" onclick="window.print()">Print</button>
        </div>
    </div>	  
</section>
<!-- stop Save Cancel -->


<section class="content">
    <div class="table-responsive table-3 employees-list">
        <form action="" method="post" name="schedule_form">
        <input type="hidden" name="sent" value="1" />
        <table class="schedule">
            <tr>
                <th class="th-serial-number"></th>
                <th><font>Name</font></th>
                <th><font>Position</font></th>
                <th><font>Mon</font></th>
                <th><font>Tue</font></th>
                <th><font>Wed</font></th>
                <th><font>Thu</font></th>
                <th><font>Fri</font></th>
                <th><font>Sat</font></th>
                <th><font>Sun</font></th>
            </tr>
           <?if(!empty($aUsers)){?>
                <?foreach($aUsers as $k=>$user){?>
            <tr>
                <td class="td-serial-number"><?=$k+1?></td>
                <td class="td-name"><span><?=$user['firstname']?> <?=$user['lastname']?></span></td>
                <td><span><?=$aUserRoles[$user['role']]?></span></td>
                <td><span><?if(!$edit_mode){?><?=$user['monday']?><?}else{?><input type="text" name="user[<?=$user['id']?>][monday]" value="<?=$user['monday']?>"/><?}?></span></td>
                <td><span><?if(!$edit_mode){?><?=$user['tuesday']?><?}else{?><input type="text" name="user[<?=$user['id']?>][tuesday]" value="<?=$user['tuesday']?>"/><?}?></span></td>
                <td><span><?if(!$edit_mode){?><?=$user['wednesday']?><?}else{?><input type="text" name="user[<?=$user['id']?>][wednesday]" value="<?=$user['wednesday']?>"/><?}?></span></td>
                <td><span><?if(!$edit_mode){?><?=$user['thursday']?><?}else{?><input type="text" name="user[<?=$user['id']?>][thursday]" value="<?=$user['thursday']?>"/><?}?></span></td>
                <td><span><?if(!$edit_mode){?><?=$user['friday']?><?}else{?><input type="text" name="user[<?=$user['id']?>][friday]" value="<?=$user['friday']?>"/><?}?></span></td>
                <td><span><?if(!$edit_mode){?><?=$user['saturday']?><?}else{?><input type="text" name="user[<?=$user['id']?>][saturday]" value="<?=$user['saturday']?>"/><?}?></span></td>
                <td><span><?if(!$edit_mode){?><?=$user['sunday']?><?}else{?><input type="text" name="user[<?=$user['id']?>][sunday]" value="<?=$user['sunday']?>"/><?}?></span></td>
            </tr>
                <?}?>
            <?}?>
        </table>
        </form> 
    </div>
</section>

<?include '_footer_tpl.php'?>