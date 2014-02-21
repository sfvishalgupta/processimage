<?php
class resizeImage
{
	public $source_image;
	public $destination;
	public $aspect_ratio;
	public $quality = 100;
	public $destination_folder;
	public $fillcolor_R = 255;
	public $fillcolor_G = 255;
	public $fillcolor_B = 255;
	public function resizeImage($source_image, $destination_folder){
		$this->source_image = $source_image;
		$this->destination_folder = $destination_folder;
		$this->aspect_ratio = 16/9;
	}
	public function setBackgroundColor($r,$g,$b){
		$this->fillcolor_R = $r;
		$this->fillcolor_G = $g;
		$this->fillcolor_B = $b;
	}
	private function getNewDimentions($source,$tn_w=false,$tn_h=false){
		$src_w = imagesx($source);
    		$src_h = imagesy($source);
		if(!$tn_w){ 
			$tn_w = $src_w;
			$tn_h = $src_w*1/$this->aspect_ratio;
		}
    		$x_ratio = $tn_w / $src_w;
		$y_ratio = $tn_h / $src_h;
		if (($src_w <= $tn_w) && ($src_h <= $tn_h)) {
        		$new_w = $src_w;
        		$new_h = $src_h;
    		}elseif(($x_ratio * $src_h) < $tn_h) {
        		$new_h = ceil($x_ratio * $src_h);
        		$new_w = $tn_w;
    		}else{
        		$new_w = ceil($y_ratio * $src_w);
        		$new_h = $tn_h;
    		}
		return array($src_w,$src_h,$new_w,$new_h,$tn_w, $tn_h);
	}
	public function resize($tn_w=false,$tn_h=false,$wmsource=false){
		$source_image = $this->source_image;
		$info = getimagesize($source_image);
		$this->destination = $this->destination_folder.time().".".pathinfo($source_image,PATHINFO_EXTENSION);
    		$imgtype = image_type_to_mime_type($info[2]);
    		switch ($imgtype){
        		case 'image/jpeg':
            			$source = imagecreatefromjpeg($source_image);
            		break;
        		case 'image/gif':
            			$source = imagecreatefromgif($source_image);
            		break;
        		case 'image/png':
            			$source = imagecreatefrompng($source_image);
            		break;
        		default:
            			die('Invalid image type.');
    		}
		list($src_w,$src_h,$new_w,$new_h,$tn_w, $tn_h) = $this->getNewDimentions($source,$tn_w,$tn_h);
		$newpic = imagecreatetruecolor(round($new_w), round($new_h));
    		$final = imagecreatetruecolor($tn_w, $tn_h);
		imageAlphaBlending($newpic, false);
		imageSaveAlpha($newpic, true);
    		imagecopyresampled($newpic, $source, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
    		$backgroundColor = imagecolorallocate($final, $this->fillcolor_R,$this->fillcolor_G,$this->fillcolor_B);
    		imagefill($final, 0, 0, $backgroundColor);
    		imagecopy($final, $newpic, (($tn_w - $new_w)/ 2), (($tn_h - $new_h) / 2), 0, 0, $new_w, $new_h);
		switch ($imgtype){
        		case 'image/jpeg':
				return imagejpeg($final, $this->destination, $this->quality);
            		break;
        		case 'image/gif':
            			$source = imagecreatefromgif($source_image);
            		break;
        		case 'image/png':
				return imagepng($final, $this->destination);
            		break;
        		default:
            			die('Invalid image type.');
    		}
    		if ($wmsource){
        		$info    = getimagesize($wmsource);
        		$imgtype = image_type_to_mime_type($info[2]);
        		switch ($imgtype) {
            			case 'image/jpeg':
                			$watermark = imagecreatefromjpeg($wmsource);
		                break;
			        case 'image/gif':
                			$watermark = imagecreatefromgif($wmsource);
                		break;
            			case 'image/png':
                			$watermark = imagecreatefrompng($wmsource);
                		break;
            			default:
                			die('Invalid watermark type.');
        		}
        		$wm_w = imagesx($watermark);
        		$wm_h = imagesy($watermark);
        		imagecopy($final, $watermark, $tn_w - $wm_w, $tn_h - $wm_h, 0, 0, $tn_w, $tn_h);
    		}
    		return imagejpeg($final, $this->destination, $this->quality);
    	}
}
