<?include '_header_tpl.php'?>

<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Employees List</h2>
    </section>
</section>
<!-- stop content title-page -->

<?php if( $_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>
<section class="content">
        <button class="button" onclick="parent.location='employees.php'">Employees</button>
</section>
<?php }?>

<section class="content">
    <div class="table-responsive table-3 employees-list">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th><font>Name</font></th>
                <th><font>Position</font></th>
                <th><font>Phone</font></th>
                <th><font>Shift Started</font></th>
                <th>&nbsp;</th>
            </tr>
            <?if(!empty($aUsers)){?>
                <?foreach($aUsers as $k=>$user){?>
            <tr>
                <td class="td-serial-number"><?=$k+1?></td>
                <td class="td-name"><span><?=$user['firstname']?> <?=$user['lastname']?></span></td>
                <td><span><?=$aUserRoles[$user['role']]?></span></td>
                <td><span><?=$user['phone']?></span></td>
                <td>
                    <span>
                        <?if($user['login']){?>
                        <?=strftime(DATE_FORMAT." %I:%M%p",$user['login'])?>
                        <?}else{?>
                        --
                        <?}?>
                    </span>
                </td>
                <td class="td-activ">
                    <div class="cont-t3">
                         
                    <?if($user['login']){?>
                    <a href="employees_shifts.php?end=<?=$user['id']?>"><span>END</span></a>
                    <?}else{?>
                    <a href="employees_shifts.php?start=<?=$user['id']?>"><span>START</span></a>
                    <?}?>
                         
                    </div>
                </td>
            </tr>
                <?}?>
            <?}?>
        </table>
    </div>
</section>

<?include '_footer_tpl.php'?>