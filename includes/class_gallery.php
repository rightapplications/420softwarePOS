<?
class class_gallery{

    protected $load_time;

    function  __construct() {
        $this->load_time = time();
    }

    function resize($img,$w,$h,$path=""){
        $err="Unsupported format";
        $size=getimagesize($img);
        $x=$size[0];
        $y=$size[1];        
        if($size[2]==1)
            $from=imageCreateFromGif($img);
        elseif($size[2]==2)
            $from=imageCreateFromJpeg($img);
        elseif($size[2]==3)
            $from=imageCreateFromPng($img);
        else return $err;        
        if($x>$y and $x/$y > $w/$h){
            $div = $x/$y;
            $w_dest =$h*$div;
            $dest = imageCreateTrueColor($w_dest,$h);        
            imageCopyResampled($dest,$from,0,0,0,0,$w_dest,$h,$x,$y);        
            $img_center= ceil($w_dest/2);
            $to = imageCreateTrueColor($w,$h);

            imagecopy( $to, $dest, 0, 0, $img_center-ceil($w/2), 0, $w, $h );
            $white = imagecolorallocate($to, 255, 255, 255);
            if($w_dest < $w){
                imagefilledrectangle($to, 0, 0,abs($img_center-ceil($w/2)),$h, $white);
                imagefilledrectangle($to, $img_center+ceil($w/2)-2, 0,$w,$h, $white);
            }
        }
        else{
            $div = $y/$x;
            $h_dest =$w*$div;
            $dest = imageCreateTrueColor($w,$h_dest);

            imageCopyResampled($dest,$from,0,0,0,0,$w,$h_dest,$x,$y);
            $img_center= ceil($h_dest/2);
            $to = imageCreateTrueColor($w,$h);

            imagecopy( $to, $dest, 0, 0, 0,$img_center-ceil($h/2),  $w, $h );
            $white = imagecolorallocate($to, 255, 255, 255);
            if($h_dest < $h){
                imagefilledrectangle($to, 0, 0,$w,abs($img_center-ceil($h/2)), $white);
                imagefilledrectangle($to, 0,$img_center+ceil($h/2)-2, $w,$h, $white);
            }
        }
        
        imagedestroy($from);
        if($size[2]==1){
            if(!empty($path))
                imageGif($to,$path);
            else
                imageGif($to);
        }
        elseif($size[2]==2){
            if(!empty($path))
                imageJpeg($to,$path);
            else
                imageJpeg($to);
        }
        elseif($size[2]==3){
            if(!empty($path))
                imagePng($to,$path);
            else
                imagePng($to);
        }        
        imagedestroy($to);
    }
    
    function resize_one($img,$wh,$param='h',$path=""){
	    $err="Unsupported format";
	    $size=getimagesize($img);
	    $x=$size[0];
	    $y=$size[1];
	    if($size[2]==1)
	        $from=imageCreateFromGif($img);
	    elseif($size[2]==2)
	        $from=imageCreateFromJpeg($img);
	    elseif($size[2]==3)
	        $from=imageCreateFromPng($img);
	    else return $err;
		if($param == 'h'){
                    $div = $x/$y;
                            $w = $wh*$div;
                    $to = imageCreateTrueColor($w,$wh);
                    if($size[2]==3 or $size[2]==1){
                        imagealphablending($to, false);
                        $this->setTransparency($to, $from);
                    }
                    imageCopyResampled($to,$from,0,0,0,0,$w,$wh,$x,$y);
                    if($size[2]==3 or $size[2]==1){
                        imagesavealpha($to, true);
                    }
                    $xy['w'] = $w;
                    $xy['h'] = $wh;
		}
		else{
                    $div = $y/$x;
                    $h = $wh*$div;
                    $to = imageCreateTrueColor($wh,$h);
                    if($size[2]==3){
                        imagealphablending($to, false);
                        $this->setTransparency($to, $from);
                    }
                    imageCopyResampled($to,$from,0,0,0,0,$wh,$h,$x,$y);
                    if($size[2]==3){
                        imagesavealpha($to, true);
                    }
                    $xy['w'] = $wh;
                    $xy['h'] = $h;
		}
                imagedestroy($from);
                if($size[2]==1){
                    if(!empty($path))
                        imageGif($to,$path);
                    else
                        imageGif($to);
                }
                elseif($size[2]==2){
                    if(!empty($path))
                        imageJpeg($to,$path);
                    else
                        imageJpeg($to);
                }
                elseif($size[2]==3){
                    if(!empty($path))
                        imagePng($to,$path);
                    else
                        imagePng($to);
                }
		imagedestroy($to);
		return $xy;
    }
    
    function setTransparency($new_image, $image_source) {
            $transparencyIndex = imagecolortransparent($image_source);
            $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);

            if ($transparencyIndex >= 0)
                $transparencyColor = imagecolorsforindex($image_source, $transparencyIndex);

            $transparencyIndex = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
            imagefill($new_image, 0, 0, $transparencyIndex);
            imagecolortransparent($new_image, $transparencyIndex);
    }

    function make_preview($file, $preview='', $width='', $height=''){   
        
        $size=getimagesize($file);
        $x=$size[0];
        $y=$size[1];
        if($x > IMAGE_ORIGINAL_WIDTH){
            $this->resize_one($file, IMAGE_ORIGINAL_WIDTH, 'w', $file);
        }
        if($preview){
            $prew_size = $this->resize($file, $width, $height, $preview);
        }        
    }
    
    function get_preview($image){
        $aFileName = explode('/',$image);
        if(count($aFileName)>1){
            $sFileName = array_pop($aFileName);
            $thumbName = 'th_'.$sFileName;
            array_push($aFileName, $thumbName);
            $sFullThumbName = implode('/', $aFileName);
        }else{
            $sFullThumbName = 'th_'.$image;
        }
        return $sFullThumbName;
    }
    
}
?>