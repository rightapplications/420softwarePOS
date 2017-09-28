<?
 $s_page=1;
    if (isset($_GET['pg'])) {
        $s_page = intval($_GET['pg']);
        $start = ($s_page-1) * $SEARCH_ROWS_MAX;
    }
    else{
        $start=0;
    }
   
 // prepare page listing
    $aTmp = preg_split("/(&?pg=\d+)/", $_SERVER['REQUEST_URI']);
    $moduleFile = '';
    foreach ($aTmp as $val) {
        $moduleFile .= $val; 
    }
    if (strpos($moduleFile, '?')=== false) {
        $moduleFile .= '?';
    } else{
        $moduleFile .= '&';
    }
    $countpage = @intval(($iCount - 1) / $SEARCH_ROWS_MAX) + 1;
     if($countpage > $s_page + 3)
            $more_b = '<div class="pc-td"><span>...</span></div>';
     else
         $more_b = '';
     if($s_page - 3 > 1)
        $more_m = '<div class="pc-td"><span>...</span></div>';
     else
        $more_m = '';

        if ($s_page != 1)
            $pervpage = '<div class="pc-td"><a class="prev-link" href="'.$moduleFile.'pg=1" title="first"><img src="images/icon_prev.png" alt="" /></a></div>
                                       <div class="pc-td"><a  class="prev-link one" href="'.$moduleFile.'pg='. ($s_page - 1) .'" title="prev"><img src="images/icon_prev_one.png" alt="" /></a></div>'.$more_m;

        if ($s_page != $countpage)
            $nextpage = $more_b.'<div class="pc-td"><a class="next-link one" href="'.$moduleFile.'pg='. ($s_page + 1) .'" title="next"><img src="images/icon_next_one.png" alt="" /></a></div><div class="pc-td"><a class="next-link" href="'.$moduleFile.'pg=' .$countpage.'" title="last"><img src="images/icon_next.png" alt="" /></a></div>';

        if($s_page - 3 > 0) $s_page3left = '<div class="pc-td"><a class="page-link" href= "'.$moduleFile.'pg='. ($s_page - 3) .'">'. ($s_page - 3) .'</a></div>';
        if($s_page - 2 > 0) $s_page2left = '<div class="pc-td"><a class="page-link" href= "'.$moduleFile.'pg='. ($s_page - 2) .'">'. ($s_page - 2) .'</a></div>';
        if($s_page - 1 > 0) $s_page1left = '<div class="pc-td"><a class="page-link" href= "'.$moduleFile.'pg='. ($s_page - 1) .'">'. ($s_page - 1) .'</a></div>';
        if($s_page + 3 <= $countpage) $s_page3right = '<div class="pc-td"><a class="page-link" href= "'.$moduleFile.'pg='. ($s_page + 3) .'">'. ($s_page + 3) .'</a></div>';
        if($s_page + 2 <= $countpage) $s_page2right = '<div class="pc-td"><a class="page-link" href= "'.$moduleFile.'pg='. ($s_page + 2) .'">'. ($s_page + 2) .'</a></div>';
        if($s_page + 1 <= $countpage) $s_page1right = '<div class="pc-td"><a class="page-link" href= "'.$moduleFile.'pg='. ($s_page + 1) .'">'. ($s_page + 1) .'</a></div>';
    if(@$iCount > $SEARCH_ROWS_MAX){
        $sPageListing = @$pervpage.@$s_page3left.@$s_page2left.@$s_page1left.'<div class="pc-td"><a class="page-link active" href="#" title="">'.$s_page.'</a></div>'.@$s_page1right.@$s_page2right.@$s_page3right.@$nextpage;
    }
    else{
        $sPageListing = '';
    }
 ?>