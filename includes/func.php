<?

function removing_illegal_characters($string){
    $aIllegal_characters = array('„','†','‡','…','Œ','œ','Š','š','Ÿ','ƒ','¡','¢','£','¤','¥','¦','§','¨','ª','«','¬','®','¯','°','±','²','³','´','µ','¶','·','¸','¹','º','»','¼','½','¾','¿','À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','×','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î');
    $aEmpty = array();
    $aDel_illegal = str_replace($aIllegal_characters, $aEmpty, $string);
    $aSearch = array('‰','€','™','•','©');
    $aReplace = array('&permil;','&euro;','&trade;','&bull','&copy;');
    $result = str_replace($aSearch, $aReplace, $aDel_illegal);
    
    return $result;
}

function copy_file($file){
    $aFileSrc1 = explode("/", $file['0']['image1']);
    $newfile1 = $aFileSrc1['0'].'/'.$_SESSION[CLIENT_ID]['user_superclinic']['id'].'/'.$aFileSrc1['2'];
    $aFileSrc2 = explode("/", $file['0']['th_image1']);
    $newfile2 = $aFileSrc2['0'].'/'.$_SESSION[CLIENT_ID]['user_superclinic']['id'].'/'.$aFileSrc2['2'];    
    $x1=copy(ABS.$file['0']['image1'], ABS.$newfile1);
    $x2=copy(ABS.$file['0']['th_image1'], ABS.$newfile2);

    $aNewFile['0'] = $newfile1;
    $aNewFile['1'] = $newfile2;
    return $aNewFile;
}

function dump($var){
    echo("<pre>");
    	print_r($var);
    echo("</pre><hr>\n");
}

function  del_dir($dirname)  {
        if  (is_dir($dirname))
                $dir_handle  =  @opendir($dirname);
        if  (!@$dir_handle)
                return  false;
        while($file  =  readdir($dir_handle))  {
                if  ($file  !=  "."  &&  $file  !=  "..")  {
                        if  (!is_dir($dirname."/".$file))
                                unlink($dirname."/".$file);
                        else
                                del_dir($dirname.'/'.$file);                       
                }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return  true;
}

function getTimezones(){
    $timezones = DateTimeZone::listIdentifiers();
    $zones = array();
    foreach( $timezones as $key => $zone ){
        $dateTimeZone = new DateTimeZone($zone);
        $dateTime = new DateTime("now", $dateTimeZone);
        $gmtOffset = secToHoursMin($dateTimeZone->getOffset($dateTime));
        $zones[$zone] = 'GMT '. ($gmtOffset['h'] >= 0 ? '+'.$gmtOffset['h'] :  $gmtOffset['h']).":".(abs($gmtOffset['m']) >= 10 ? abs($gmtOffset['m']) : abs($gmtOffset['m']).'0');
    }
    return @$zones ? $zones : false;
}

function secToHoursMin($seconds, $str=false){
    if($seconds >= 0){
        $sign = 1;
    }else{
        $sign = -1;
    }
    $absSeconds = abs($seconds);
    $hours = floor($absSeconds/3600);
    if($hours > 0){
        $rest = $absSeconds - $hours*3600;
        $minutes= floor($rest/60);
        
    }else{
        $minutes= floor($absSeconds/60);        
    }
    if($minutes >= 0 and $minutes < 10){
        $minutes = '0'.$minutes;
    }
    if($sign < 0){
        $minutes = '-'.$minutes;
    }
    if($str){
        return $hours*$sign.':'.$minutes;
    }else{
        return array('h'=>$hours*$sign,'m'=>$minutes);
    }
}

function time2seconds($time='00:00:00')
{
    list($hours, $mins, $secs) = explode(':', $time);
    return ($hours * 3600 ) + ($mins * 60 ) + $secs;
}

function HMtoDec($hours){
    $aHM = explode(':', $hours);
    $decMin = round($aHM[1]/60, 2);
    $decHours = $aHM[0]+$decMin;
    return $decHours;
}

function timeRange($start, $end, $step){
    $aStart = date_parse($start);
    $aEnd = date_parse($end);
    if($aEnd['hour'] < $aStart['hour']){
        $lastMonthDay = get_last_day_of_month($aEnd['month'], $aEnd['year']);
        if($aEnd['day'] < $lastMonthDay){
            $aEnd['day']++;
        }else{
            $aEnd['day'] = 1;
            if($aEnd['month'] < 12){
                $aEnd['month']++;
            }else{
                $aEnd['month'] = 1;
                $aEnd['year']++;
            }
        }
    }
    
    $aRange = array();
    $startMin = $aStart['month'].'/'.$aStart['day'].'/'.$aStart['year'].' '.$aStart['hour'].':'.$aStart['minute'];
    $endMin = $aEnd['month'].'/'.$aEnd['day'].'/'.$aEnd['year'].' '.$aEnd['hour'].':'.$aEnd['minute'];
    $startTS = strtotime($startMin);
    $endTS = strtotime($endMin);
    $k = 0;
    for($i = $startTS;$i < $endTS;$i+=$step*3600){ 
        $aRange[$k]['s'] = strftime('%H:%M',$i);
        $aRange[$k]['e'] = strftime('%H:%M',$i+$step*3600);
        $k++;
    }
    if(!empty($aRange)) return $aRange;
    else return false;
}

function intervalByHours($from, $to){
    $aIntervalByHours = array();
    $aStart = getdate($from);
    $start = mktime($aStart['hours'], 0 , 0, $aStart['mon'], $aStart['mday'], $aStart['year']);
    $aEnd = getdate($to);
    $end = mktime($aEnd['hours'], 59 , 59, $aEnd['mon'], $aEnd['mday'], $aEnd['year']);
    $hour = $start;
    $i = 0;
    while($hour <= $end){ 
        $aIntervalByHours[$i]['start'] = $hour;
        $aIntervalByHours[$i]['end'] = strtotime("+1 hour", $hour)-1;
        $hour= strtotime("+1 hour", $hour);
        $i++;
    }
    return $aIntervalByHours;
}

function intervalByDays($from, $to){
    $aIntervalByDays = array();
    $aStart = getdate($from);
    $start = mktime(0, 0 , 0, $aStart['mon'], $aStart['mday'], $aStart['year']);
    $aEnd = getdate($to);
    $end = mktime(0, 0 , 0, $aEnd['mon'], $aEnd['mday'], $aEnd['year']);
    $day = $start;
    $i = 0;
    while($day <= strtotime("+1 day", $end)-1){ 
        $aIntervalByDays[$i]['start'] = $day;
        $aIntervalByDays[$i]['end'] = strtotime("+1 day", $day)-1;
        $day= strtotime("+1 day", $day);
        $i++;
    }
    return $aIntervalByDays;
}

function intervalByWeeks($from, $to){
    $aIntervalByWeeks = array();
    $aStart = getdate($from);
    $start = mktime(0, 0 , 0, $aStart['mon'], $aStart['mday'], $aStart['year']);
    $aEnd = getdate($to);
    $end = mktime(0, 0 , 0, $aEnd['mon'], $aEnd['mday'], $aEnd['year']);
    $day = $start;
    $i = 0;
    while($day <= strtotime("+1 day", $end)-1){ 
        $aIntervalByWeeks[$i]['start'] = $day;
        $aIntervalByWeeks[$i]['end'] = strtotime("+1 week", $day)-1;
        $day= strtotime("+1 week", $day);
        $i++;
    }
    return $aIntervalByWeeks;
}

function decimal_to_time($dec) {
    $seconds = $dec * 3600;
    $hours = floor($dec);
    $seconds -= $hours * 3600;
    $minutes = floor($seconds / 60);
    $seconds -= $minutes * 60;
    return lz($hours).":".lz($minutes);
}
function lz($num){
    return (strlen($num) < 2) ? "0{$num}" : $num;
}

function time_to_decimal($time) {
    $timeArr = explode(':', $time);
    $decTime = $timeArr[0] + ($timeArr[1]/60) + (@$timeArr[2]/60); 
    return $decTime;
}

function date_us2db ($date) {
	//m?*d?*yy* => yyyy.mm.dd
	if ($date === '')
		return '';
	@list ($month, $day, $year) = preg_split ("/\//", trim($date));
	if (@checkdate($month, $day, $year))
	    return '20'.$year.'-'.$month.'-'.$day;
	else
	    return '';
}

function time24to12($h){
    $h24 = intval($h);
    if($h24 == 0 or $h24 > 23){
        $h12 = '12:00 AM';
    }elseif($h24 > 0 and $h24 <= 11){
        $h12 = $h24.':00 AM';
    }elseif($h24 == 12){
        $h12 = '12:00 PM';
    }elseif($h24 > 12 and $h24 <= 23){
        $h12 = ($h24-12).':00 PM';
    }
    return $h12;
}

function get_last_day_of_month($mm, $yy) {
    for ($dd = 28; $dd <= 31; $dd++) {
        $tdate = getdate(mktime(0, 0, 0, $mm, $dd, $yy));
        if ($tdate["mon"] != $mm)
        	break;
    }
    $dd--;
    return $dd;
}

function get_month_name($m){
    global $aLang;
    switch($m){
        case 1: $mn = $aLang['January'];break;
        case 2: $mn = $aLang['February'];break;
        case 3: $mn = $aLang['March'];break;
        case 4: $mn = $aLang['April'];break;
        case 5: $mn = $aLang['May'];break;
        case 6: $mn = $aLang['June'];break;
        case 7: $mn = $aLang['July'];break;
        case 8: $mn = $aLang['August'];break;
        case 9: $mn = $aLang['September'];break;
        case 10: $mn = $aLang['October'];break;
        case 11: $mn = $aLang['November'];break;
        case 12: $mn = $aLang['December'];break;
    }
    return $mn;
}

function get_short_month_name($m){
    global $aLang;
    switch($m){
        case 1: $mn = $aLang['Jan'];break;
        case 2: $mn = $aLang['Feb'];break;
        case 3: $mn = $aLang['Mar'];break;
        case 4: $mn = $aLang['Apr'];break;
        case 5: $mn = $aLang['May'];break;
        case 6: $mn = $aLang['Jun'];break;
        case 7: $mn = $aLang['Jul'];break;
        case 8: $mn = $aLang['Aug'];break;
        case 9: $mn = $aLang['Sep'];break;
        case 10: $mn = $aLang['Oct'];break;
        case 11: $mn = $aLang['Nov'];break;
        case 12: $mn = $aLang['Dec'];break;
    }
    return $mn;
}

function get_short_month_name_en($m){
    switch($m){
        case 1: $mn = 'Jan';break;
        case 2: $mn = 'Feb';break;
        case 3: $mn = 'Mar';break;
        case 4: $mn = 'Apr';break;
        case 5: $mn = 'May';break;
        case 6: $mn = 'Jun';break;
        case 7: $mn = 'Jul';break;
        case 8: $mn = 'Aug';break;
        case 9: $mn = 'Sep';break;
        case 10: $mn = 'Oct';break;
        case 11: $mn = 'Nov';break;
        case 12: $mn = 'Dec';break;
    }
    return $mn;
}

function gramsToPounds($grams){
    $pounds = $grams/450;
    $floorPounds = floor($pounds);
    $restPounds = $pounds - $floorPounds;
    $restGrams = floor($restPounds*450);
    $sWeight = $floorPounds.($floorPounds > 1 ? ' lbs.' : ' lb');
    if($restGrams){
        $sWeight = $sWeight." ".$restGrams.($restGrams > 1 ? ' grams' : ' gram');
    }
    return $sWeight;
}

function checkAccess($aRoles, $redirect='/login'){
    if (!isset($_SESSION[CLIENT_ID]['user_superclinic']['id']) or empty($_SESSION[CLIENT_ID]['user_superclinic']['id'])){
        if(!empty($redirect)){
            header('Location: '.$redirect);
            exit();
        }else{
            return false;
        }
    }else{
        if(in_array($_SESSION[CLIENT_ID]['user_superclinic']['role'], $aRoles) ){
            return true;
        }else{
            if(!empty($redirect)){
                header('Location: '.$redirect);
                exit();
            }else{
                return false;
            }
        }
    }
}

function checkRights($aAdminRights=null,$aRequiredRights=null){
    if($_SESSION[CLIENT_ID]['admin_superclinic'] != 1){
        if(!empty($aRequiredRights) and @is_array($aRequiredRights)){
            $right = 0;
            foreach($aRequiredRights as $k=>$v){
                if(!empty($aAdminRights[$v])){
                    $right++;
                }
            }
            return $right ? true : false;            
        }else{
            return false;
        }
    }else{
        return true;
    }
}


function generate_password($length = 8,$number=false) {
    srand((double)microtime()*1000000);
    if($number){
        $min= pow(10,$length-1);
        $max= pow(10,$length)-1;
        $unique_str = rand($min,$max);
    }else{
        $unique_str = md5(rand(0,9999999));
        $unique_str = substr( $unique_str, 0, $length);
    }
    return $unique_str;
}

function download($filename, $mimetype='image/jpeg') {
	if (!file_exists($filename)) die('File not found');

	$from=$to=0; $cr=NULL;

	if (isset($_SERVER['HTTP_RANGE'])) {
		$range=substr($_SERVER['HTTP_RANGE'], strpos($_SERVER['HTTP_RANGE'], '=')+1);
		$from=strtok($range, '-');
		$to=strtok('/'); if ($to>0) $to++;
		if ($to) $to-=$from;
		header('HTTP/1.1 206 Partial Content');
		$cr='Content-Range: bytes ' . $from . '-' . (($to)?($to . '/' . $to+1):filesize($filename));
	} else	header('HTTP/1.1 200 Ok');

	$etag=md5($filename);
	$etag=substr($etag, 0, 8) . '-' . substr($etag, 8, 7) . '-' . substr($etag, 15, 8);
	header('ETag: "' . $etag . '"');

	header('Accept-Ranges: bytes');
	header('Content-Length: ' . (filesize($filename)-$to+$from));
	if ($cr) header($cr);

	header('Connection: close');
	header('Content-Type: ' . $mimetype);
	header('Last-Modified: ' . gmdate('r', filemtime($filename)));
	$f=fopen($filename, 'r');
	header('Content-Disposition: attachment; filename="' . basename($filename) . '";');
	if ($from) fseek($f, $from, SEEK_SET);
	if (!isset($to) or empty($to)) {
		$size=filesize($filename)-$from;
	} else {
		$size=$to;
	}
	$downloaded=0;
	while(!feof($f) and !connection_status() and ($downloaded<$size)) {
		echo fread($f, 512000);
		$downloaded+=512000;
		flush();
	}
	fclose($f);
}

function sortableHeader($name, $link, $sort_param, $sort_order, $default = false, $selected_sort_param=''){
    if(strstr($link, '?')){
        $glue = '&';
    }else{
        $glue = '?';
    }
    if(empty($sort_order) or $sort_order == 'ASC'){
        $sorting = 'DESC';
    }else{
        $sorting = 'ASC';
    }
    echo '<div class="icon-table"><span class="icon-select"><a href="'. $link.$glue.'ordby='.$sort_param.'&ord='.$sorting.'">';
    if($selected_sort_param == $sort_param or ($default and !$selected_sort_param)){
        if(empty($sort_order) or $sort_order == 'ASC'){
            $arrow_ending = 'up';
        }else{
            $arrow_ending = 'down';
        }
        echo '<i class="for-mobile"><img src="images/icon_select_'.$arrow_ending.'m.png" alt="" /></i>
              <i class="for-desktop"><img src="images/icon_select_'.$arrow_ending.'.png" alt="" /></i>';    
    }else{
        echo '<i class="for-mobile"><img src="images/icon_select_disabledm.png" alt="" /></i>
              <i class="for-desktop"><img src="images/icon_select_disabled.png" alt="" /></i>';
    }                   
    echo '</a></span><font>'.$name.'</font></div>';
}

if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data) {
        $f = @fopen($filename, 'w');
        if (!$f) {
            return false;
        } else {
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }
}

if (!function_exists('scandir')) {
    function scandir($dir) {
        $dh  = opendir($dir);
        while (false !== ($filename = readdir($dh))){
            $files[] = $filename;
        }
        sort($files);
        return $files;
    }
}

function sort_array($array,$key,$type='ASC'){
        $sorted_array = array();
        if(@is_array($array) and count($array)>0){
            foreach($array as $k=>$row){
                @$key_values[$k] = $row[$key];
            }
            if($type == 'ASC' ){
                asort($key_values);
            }else{
                arsort($key_values);
            }
            foreach($key_values as $k=>$v){
               $sorted_array[] = $array[$k];
            }
            return $sorted_array;
        }else{
            return false;
        }

    }

    function translit($str){
        $tr = array(
        "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
        "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
        "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
        "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
        "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"Y","Ь"=>"",
        "Ї"=>"yi","Є"=>"E","І"=>"i",
        "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"y","ь"=>"","э"=>"e","ё"=>"e","ю"=>"yu","я"=>"ya",
        "ї"=>"yi","є"=>"e","і"=>"i"
        );
        $result = strtr($str,$tr);
        $result = strtolower($result);
        $result=preg_replace('/[^\.0-9a-zA-Z ]/', '', $result);
        $result = str_replace(' ','-',$result);
        return $result;
    }

function get_main_menu($aMenu,$active=''){
    if(isset($_SESSION[CLIENT_ID]['user_superclinic']['rights'])){
        $aMenuRights = array('menu'=>$_SESSION[CLIENT_ID]['user_superclinic']['menu'], 'pos'=>$_SESSION[CLIENT_ID]['user_superclinic']['pos'], 'inventory'=>$_SESSION[CLIENT_ID]['user_superclinic']['inventory'], 'employees'=>$_SESSION[CLIENT_ID]['user_superclinic']['employess'], 'rewards'=>$_SESSION[CLIENT_ID]['user_superclinic']['billing'], 'statistic'=>$_SESSION[CLIENT_ID]['user_superclinic']['statistic'], 'billing'=>$_SESSION[CLIENT_ID]['user_superclinic']['billing'], 'online_training_menu'=>1);
    }else{
        $aMenuRights = array('menu'=>1, 'pos'=>1, 'inventory'=>1, 'employees'=>1, 'rewards'=>1, 'statistic'=>1, 'billing'=>1, 'online_training_menu'=>1);
    }
    foreach($aMenu as $key=>$item){
        if(in_array($_SESSION[CLIENT_ID]['user_superclinic']['package'], $item['display'])){
            if($aMenuRights[$key]){
                $aDisplayedMenu[$key] = $item;
            }
        }
    }
    if($active and isset($aDisplayedMenu[$active])){
        $aDisplayedMenu[$active]['active'] = 1;
    }
    return isset($aDisplayedMenu) ? $aDisplayedMenu : null;
}

function phone_number_format($number){
    $sect2 = substr($number,-4);
    $sect1 = substr($number,-7,3);
    $code = substr($number,-10,3);
    return $code."-".$sect1."-".$sect2;
}

function cropStr($str, $size){ 
  if(mb_strlen($str) > $size){
      $ending = '...';
  }else{
      $ending = '';
  }
  return mb_substr($str,0,$size,'utf-8').$ending;  
}

function bytesToKb($val){
    return round($val/1024);
}

function array_to_csv($header,$data,$file_name, $cell_separator=';'){
    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment;filename='.$file_name );
    if(count($data) == 0){
        echo "Data empty";
    }else{
        $header_count = count($header);
        if(count(current($data)) != $header_count){
            echo 'Header doesn\'t match data rows';
        }else{
            $header_row = '';
            foreach($header as $k=>$h){
                if($k<$header_count-1){
                    $separator = $cell_separator;
                }else{
                    $separator = '';
                }
                $header_row.='"'.str_replace('"', '""',$h).'"'.$separator;
            }
            $data_rows = '';
            foreach($data as $d){
                $count_data_row = count($d);                
                $i = 0;
                foreach($d as $n=>$v){
                    if($i<$count_data_row-1){
                        $separator = $cell_separator;
                    }else{
                        $separator = '';
                    }
                    $data_rows.='"'.str_replace('"', '""',$v).'"'.$separator;
                    $i++;
                }
                $data_rows.="\r\n";
            }
            $csv = $header_row."\r\n".$data_rows;
            echo $csv;
        }
    }
}

function get_csv($data, $cell_separator=';', $header=array()){
    if(count($data) == 0){
        return false;
    }else{
        if(!empty($header)){
            $header_count = count($header);
        }else{
            $header_count = 0;
        }
        if($header_count > 0 and count(current($data)) != $header_count){
            return false;
        }else{
            $header_row = '';
            if($header_count){
                foreach($header as $k=>$h){
                    if($k<$header_count-1){
                        $separator = $cell_separator;
                    }else{
                        $separator = '';
                    }
                    $header_row.='"'.str_replace('"', '""',$h).'"'.$separator;
                }
            }
            $data_rows = '';
            foreach($data as $d){
                $count_data_row = count($d);                
                $i = 0;
                foreach($d as $n=>$v){
                    if($i<$count_data_row-1){
                        $separator = $cell_separator;
                    }else{
                        $separator = '';
                    }
                    $data_rows.='"'.str_replace('"', '""',$v).'"'.$separator;
                    $i++;
                }
                $data_rows.="\r\n";
            }
            $csv = $header_row."\r\n".$data_rows;
            return $csv;
        }
    }
}

function getClientIP(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function escapePostGet() {    
    if (!empty($_POST)) {
       foreach ($_POST as $key => $value) {
           $_POST[$key] = htmlspecialchars($value, ENT_QUOTES);
       }
    }
    
    if (!empty($_GET)) {
       foreach ($_GET as $key => $value) {
           $_GET[$key] = htmlspecialchars($value, ENT_QUOTES);
       }
    }
}

function post_curl_request($url, $data){
    $sData = '';
    if(is_array($data)){
        $aData = array();
        foreach($data as $k=>$v){
            if(is_array($v)){
                foreach($v as $paramItemVal){
                    $aData[] = $k.'='.urlencode($paramItemVal);
                }
            }else{
                $aData[] = $k.'='.urlencode($v);
            }
        }
        $sData.= implode('&', $aData);
    }
    //$url.=$sData; 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $sData); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE,FALSE);
    $result = curl_exec($ch); //dump($result);
    return $result; 
}

    
function geocode($address){
	$address = urlencode($address);
	$url = "http://maps.google.com/maps/api/geocode/json?address={$address}&components=country:US";
	$resp = json_decode(file_get_contents($url), true);
	if($resp['status'] != 'OK') return false;

	$lat = $resp['results'][0]['geometry']['location']['lat'];
	$lng = $resp['results'][0]['geometry']['location']['lng'];

	if(!$lat || !$lng) return false;
	return [
		'lat' => $lat,
		'lng' => $lng
	];
}

function ajax_error($message){
	http_response_code(400);
	die($message);
}