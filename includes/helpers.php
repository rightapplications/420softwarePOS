<?
function month_options($selected='',$printable=true){
    $opt='';
    $opt.= '<option  value="01" '.($selected == '01' ? 'selected' : '').'> 01';
    $opt.= '<option  value="02" '.($selected == '02' ? 'selected' : '').'> 02';
    $opt.= '<option  value="03" '.($selected == '03' ? 'selected' : '').'> 03';
    $opt.= '<option  value="04" '.($selected == '04' ? 'selected' : '').'> 04';
    $opt.= '<option  value="05" '.($selected == '05' ? 'selected' : '').'> 05';
    $opt.= '<option  value="06" '.($selected == '06' ? 'selected' : '').'> 06';
    $opt.= '<option  value="07" '.($selected == '07' ? 'selected' : '').'> 07';
    $opt.= '<option  value="08" '.($selected == '08' ? 'selected' : '').'> 08';
    $opt.= '<option  value="09" '.($selected == '09' ? 'selected' : '').'> 09';
    $opt.= '<option  value="10" '.($selected == '10' ? 'selected' : '').'> 10';
    $opt.= '<option  value="11" '.($selected == '11' ? 'selected' : '').'> 11';
    $opt.= '<option  value="12" '.($selected == '12' ? 'selected' : '').'> 12';
    if($printable){
        echo $opt;
    }else{
        return $opt;        
    }
}

function days_options($selected='',$printable=true){
    global $aLang;
    $opt='';
    $opt.= '<option  value="1" '.($selected == '1' ? 'selected' : '').'> '.$aLang['monday'];
    $opt.= '<option  value="2" '.($selected == '2' ? 'selected' : '').'> '.$aLang['tuesday'];
    $opt.= '<option  value="3" '.($selected == '3' ? 'selected' : '').'> '.$aLang['wednesday'];
    $opt.= '<option  value="4" '.($selected == '4' ? 'selected' : '').'> '.$aLang['thursday'];
    $opt.= '<option  value="5" '.($selected == '5' ? 'selected' : '').'> '.$aLang['friday'];
    $opt.= '<option  value="6" '.($selected == '6' ? 'selected' : '').'> '.$aLang['saturday'];
    $opt.= '<option  value="0" '.($selected == '0' ? 'selected' : '').'> '.$aLang['sunday'];    
    if($printable){
        echo $opt;
    }else{
        return $opt;        
    }
}

function date_options($month, $year, $selected='',$printable=true){
    $last_day = get_last_day_of_month($month, $year);
    $opt='';
    for($i=1; $i<=$last_day; $i++){
        if($i == $selected) {$sel = "selected"; } else $sel = "";
        if($i < 10) $d = '0'.$i;
        else $d = $i;
        $opt.= '<option  value="'.$i.'" '.$sel.'>'.$d.'</option>';
    }
    if($printable){
        echo $opt;
    }else{
        return $opt;        
    }
}

function year_options($start,$selected='',$end='',$printable=true){
    if(!$end){
        $cur_date=getdate();
        $cur_year=$cur_date['year'];
        $end = $cur_year;
    }
    $opt='';
    for($i=(int)$start; $i<=(int)$end; $i++){
        if($i == $selected) {$sel = "selected"; } else $sel = "";
        $opt.= '<option  value="'.$i.'" '.$sel.'>'.$i.'</option>';
    }
    if($printable){
        echo $opt;
    }else{
        return $opt;        
    }
}

function count_options($start,$end,$step=1,$selected='',$printable=true){
    $opt='';
    for($i=(int)$start; $i<=(int)$end; $i+=(int)$step){
        if($i == $selected) {$sel = "selected"; } else $sel = "";
        $opt.= '<option  value="'.$i.'" '.$sel.'>'.$i.'</option>';
    }
    if($printable){
        echo $opt;
    }else{
        return $opt;        
    }
}

function select_options($aOptions,$selected,$printable=true){
    $opt='';
    foreach($aOptions as $key=>$val){
        if($key == $selected) {$sel = "selected"; } else $sel = "";
        $opt.= '<option  value="'.$key.'" '.$sel.'>'.$val.'</option>';
    }
    if($printable){
        echo $opt;
    }else{
        return $opt;        
    }
}

function radio_options($name,$aOptions,$selected,$printable=true){
    $opt='';
    foreach($aOptions as $key=>$val){
        if($key == $selected) {$chk = 'checked="checked"'; } else $chk = "";
        $opt.= '<input type="radio" name="'.$name.'" value="'.$key.'" '.$chk.'>'.$val.'</br>';
    }
    if($printable){
        echo $opt;
    }else{
        return $opt;        
    }
}

function checkbox_options($name,$aOptions,$selected,$printable=true){
    $opt='';
    foreach($aOptions as $key=>$val){
        if($key == $selected) {$chk = 'checked="checked"'; } else $chk = "";
        $opt.= '<input type="checkbox" name="'.$name.'['.$key.']" value="'.$key.'" '.$chk.'>'.$val.'</br>';
    }
    if($printable){
        echo $opt;
    }else{
        return $opt;        
    }
}
?>