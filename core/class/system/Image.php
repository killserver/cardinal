<?php
/*
 *
 * @version 0.3
 * @copyright 2014-2018 KilleR for Cardinal Engine
 *
 * Version Engine: 10.3
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class Image {

	const IMG_FLIP_HORIZONTAL = 0;
	const IMG_FLIP_VERTICAL = 1;
	const IMG_FLIP_BOTH = 2;

	private $transparent = 127;
	private $compress = 75;
	private $mime = '';
	private $imageWidth = 0;
	private $imageHeight = 0;
	private $cropWidth = 0;
	private $cropHeight = 0;
	private $source_image = false;
	private $cropType = "cover";
	private $typeImage = '';
	private $fill = array(255, 255, 255);
	private $resizeHeight = false;
	private $resizeWidth = false;
	private $resizePositionY = 0;
	private $resizePositionX = 0;
	private $flip = array();
	private $rotate = 0;
	private $toSize = true;
	private $headers = false;
	private $filter = false;

	function __construct($imgUrl) {
		$what = getimagesize($imgUrl);
		$this->imageWidth = $what[0];
		$this->imageHeight = $what[1];
		switch(strtolower($what['mime'])) {
			case 'image/png':
				$this->typeImage = 'png';
			break;
			case 'image/jpeg':
				$this->typeImage = 'jpg';
			break;
			case 'image/gif':
				$this->typeImage = 'gif';
			break;
			case 'image/vnd.wap.wbmp':
				$this->typeImage = 'webp';
			break;
			default:
				throw new Exception("image type not supported", 1);
				die();
			break;
		}
		$imgUrl = file_get_contents($imgUrl);
		$this->source_image = imagecreatefromstring($imgUrl);
		return $this;
	}

	private function nimageflip($image, $mode) {
		switch($mode) {
			case Image::IMG_FLIP_HORIZONTAL:
				$max_x = imagesx($image) - 1;
				$half_x = $max_x / 2;
				$sy = imagesy($image);
				if(imageistruecolor($image)) {
					$temp_image = imagecreatetruecolor(1, $sy);
				} else {
					$temp_image = imagecreate(1, $sy);
				}
				list($red, $green, $blue) = $this->fill;
				$color = imagecolorallocate($temp_image, $red, $green, $blue);
				imagefill($temp_image, 0, 0, $color);
				for($x=0;$x<$half_x;++$x) {
					imagecopy($temp_image, $image, 0, 0, $x, 0, 1, $sy);
					imagecopy($image, $image, $x, 0, $max_x - $x, 0, 1, $sy);
					imagecopy($image, $temp_image, $max_x - $x, 0, 0, 0, 1, $sy);
				}
			break;
			case Image::IMG_FLIP_VERTICAL:
				$sx = imagesx($image);
				$max_y = imagesy($image) - 1;
				$half_y = $max_y / 2;
                $sy = imagesy($image);
				if(imageistruecolor($image)) {
					$temp_image = imagecreatetruecolor(1, $sy);
				} else {
					$temp_image = imagecreate(1, $sy);
				}
				list($red, $green, $blue) = $this->fill;
				$color = imagecolorallocate($temp_image, $red, $green, $blue);
				imagefill($temp_image, 0, 0, $color);
				for ($y=0;$y<$half_y;++$y) {
					imagecopy($temp_image, $image, 0, 0, 0, $y, $sx, 1);
					imagecopy($image, $image, 0, $y, 0, $max_y - $y, $sx, 1);
					imagecopy($image, $temp_image, 0, $max_y - $y, 0, 0, $sx, 1);
				}
			break;
			case Image::IMG_FLIP_BOTH:
				$sx = imagesx($image);
				$sy = imagesy($image);
				$temp_image = imagerotate($image, 180, 0);
				imagecopy($image, $temp_image, 0, 0, 0, 0, $sx, $sy);
			break;
			default:
				return;
			break;
		}
		imagedestroy($temp_image);
	}

	function compress($compress = 75) {
		if($compress>0) {
			$this->compress = $compress/100*$compress;
		}
		$this->compress = round($compress);
		return $this;
	}

	function fill($color = array(255, 255, 255)) {
		if(!is_array($color)) {
			throw new Exception("Fill must be type array", 1);
			die();
		}
		if(sizeof($color)<3) {
			throw new Exception("Fill must be exists colors red, green, blue", 1);
			die();
		}
		$this->fill = $color;
		return $this;
	}

	function transparent($persent = 100) {
		if(!is_numeric($persent)) {
			$persent = 127;
		}
		$this->transparent = round(127-(127/100*$persent));
		return $this;
	}

	function opacity($persent = 100) {
		return $this->transparent($persent);
	}

	function flip($dir) {
		if($dir!==Image::IMG_FLIP_HORIZONTAL && $dir!==Image::IMG_FLIP_VERTICAL && $dir!==Image::IMG_FLIP_BOTH) {
			throw new Exception("Flipping must be used as part Image library", 1);
			die();
		}
		$this->flip[] = $dir;
		return $this;
	}

	function flipReset() {
		$this->flip = array();
		return $this;
	}

	function resizePX($width, $height) {
		if(!is_numeric($width) || !is_numeric($height)) {
			throw new Exception("On resize width and height must be set as integer", 1);
			die();
		}
		if($this->cropType === "contain" && $this->imageWidth > $this->imageHeight) {
			$new_width = $width;
			$new_height = intval($this->imageHeight * $new_width / $this->imageWidth);
		} else {
			$new_height = $height;
			$new_width = intval($this->imageWidth * $new_height / $this->imageHeight);
		}
		$this->resizeHeight = $new_height;
		$this->resizeWidth = $new_width;
		$this->resizePositionX = intval(($width - $new_width) / 2);
		$this->resizePositionY = intval(($height - $new_height) / 2);
		return $this;
	}

	function resizePersent($persent = 50) {
		if(!is_numeric($persent)) {
			throw new Exception("On resize persent must be set as integer", 1);
			die();
		}
		if($persent<0) {
			$persent = 50;
		}
		$this->resizeWidth = round($this->imageWidth/100*$persent);
		$this->resizeHeight = round($this->imageHeight/100*$persent);
		return $this;
	}

	function rotate($angle = 0, $toSize = true) {
		if(!is_numeric($angle)) {
			throw new Exception("On rotate angle must be set as integer", 1);
			die();
		}
		$this->rotate = $angle;
		$this->toSize = boolval($toSize);
		return $this;
	}

	function setHeader() {
		$this->headers = true;
		return $this;
	}

	function filter($class) {
		$this->filter = $class;
		return $this;
	}

	function save($file = false, $type = false) {
		$width = ($this->resizeWidth===false ? $this->imageWidth : $this->resizeWidth);
		$height = ($this->resizeHeight===false ? $this->imageHeight : $this->resizeHeight);

		$new_image = imagecreatetruecolor($width, $height); // creates new image, but with a black background

		list($red, $green, $blue) = $this->fill;
		imagesavealpha($new_image, true);
		imagealphablending($new_image, true);
		$color = imagecolorallocatealpha($new_image, $red, $green, $blue, $this->transparent);
		imagefill($new_image, 0, 0, $color);
		if(function_exists("imageantialias")) {
			imageantialias($new_image, true);
		}

		if($this->filter!==false && is_object($this->filter) && method_exists($this->filter, "apply")) {
			$this->filter->apply($this->source_image);
		}

		if($this->resizeWidth!==false && $this->resizeWidth!==false) {
			imagecopyresampled($new_image, $this->source_image, $this->resizePositionY, $this->resizePositionX, 0, 0, $this->resizeWidth, $this->resizeHeight, $this->imageWidth, $this->imageHeight);
		} else {
			$new_image = $this->source_image;
		}
		if(sizeof($this->flip)>0) {
			for($i=0;$i<sizeof($this->flip);$i++) {
				$this->nimageflip($new_image, $this->flip[$i]);
			}
		}
		
	    if($this->rotate>0) {
			$rotated_image = imagerotate($new_image, -$this->rotate, $color);
			unset($new_image);
		} else {
			$rotated_image = $new_image;
			unset($new_image);
		}

		if($this->toSize) {
			// find new width & height of rotated image
			$rotated_width = imagesx($rotated_image);
			$rotated_height = imagesy($rotated_image);
			// diff between rotated & original sizes
			$dx = $rotated_width - $width;
			$dy = $rotated_height - $height;
			// crop rotated image to fit into original rezized rectangle
			$cropped_rotated_image = imagecreatetruecolor($width, $height);
			imagesavealpha($cropped_rotated_image, true);
			imagealphablending($cropped_rotated_image, true);
			if(function_exists("imageantialias")) {
				imageantialias($cropped_rotated_image, true);
			}
			imagefill($cropped_rotated_image, 0, 0, $color);
			imagecolortransparent($cropped_rotated_image, $color);
			imagecopyresampled($cropped_rotated_image, $rotated_image, 0, 0, $dx / 2, $dy / 2, $width, $height, $width, $height);
			unset($rotated_image);
			$rotated_image = $cropped_rotated_image;
		}

		//if($type===false) { $type = $this->typeImage; }

		if($this->typeImage=="jpg") {
			if($this->headers===true) { header("Content-type: image/jpeg"); }
			$imgt = "ImageJPEG";
		} else if($this->typeImage=="png") {
			if($this->headers===true) { header("Content-type: image/png"); }
			$imgt = "ImagePNG";
			if($this->compress<=100 && $this->compress>=10) {
				$this->compress /= 11;
				$this->compress = round($this->compress);
			}
		} else if($this->typeImage=="gif") {
			if($this->headers===true) { header("Content-type: image/gif"); }
			$imgt = "ImageGIF";
		} else if($this->typeImage=="webp") {
			if($this->headers===true) { header("Content-type: image/wbmp"); }
			$imgt = "ImageWBMP";
		}
        if(!is_callable($imgt)) {
            return false;
        }
		if($file!==false && $type===false) {
			return call_user_func_array($imgt, array($rotated_image, $file, $this->compress));
		} else if($file!==false && $type!==false) {
			return call_user_func_array($imgt, array($rotated_image, $file.".".$type, $this->compress));
		} else {
            return call_user_func_array($imgt, array($rotated_image, null, $this->compress));
		}
	}

}

class ImageFilter {

	private $negate = false;
	private $grayscale = false;
	private $brightness = 0;//-255 255
	private $contrast = 0;//-100 100
	private $colorize = array(0,0,0,1);//0 255
	private $gaussian_blur = false;
	private $selective_blur = false;
	private $eskis = false;
	private $smooth = 0;//1 -2
	private $pixelate = 0;//1 -2 + third param = true
	private $motion = false;
	private $lighten = false;
	private $darken = false;
	private $sharpen = false;
	private $sharpenAlt = false;
	private $emboss = false;
	private $embossAlt = false;
	private $blur = false;
	private $edge = false;
	private $edgeAlt = false;
	private $draw = false;
	private $mean = false;

	function negate($val = true) {
		$this->negate = boolval($val);
		return $this;
	}

	function grayscale($val) {
		$this->grayscale = boolval($val);
		return $this;
	}

	function brightness($val = 0) {
		if(!is_numeric($val)) {
			throw new Exception("image filter brightness must be integer", 1);
			die();
		}
		if($val<-255 || $val>255) {
			$val = 0;
		}
		if($val>=0) {
			$val = 255-(255/100*$val);
		} else {
			$val = -255+(-255/100*$val);
		}
		$this->brightness = $val;
		return $this;
	}

	function contrast($val = 0) {
		if(!is_numeric($val)) {
			throw new Exception("image filter contrast must be integer", 1);
			die();
		}
		if($val<-100 || $val>100) {
			$val = 0;
		}
		if($val>=0) {
			$val = 100-(100/100*$val);
		} else {
			$val = -100+(-100/100*$val);
		}
		$this->brightness = $val;
		return $this;
	}

	function colorize($red, $green = 0, $blue = 0, $alpha = 1) {
		if(!is_numeric($red) || !is_numeric($green) || !is_numeric($blue) || !is_numeric($alpha)) {
			throw new Exception("image filter colorize must be integer", 1);
			die();
		}
		$this->colorize = array($red, $green, $blue, $alpha);
		return $this;
	}

	function gaussian_blur($val) {
		$this->gaussian_blur = intval($val);
		return $this;
	}

	function selective_blur($val) {
		$this->selective_blur = intval($val);
		return $this;
	}

	function eskis($val) {
		$this->eskis = intval($val);
		return $this;
	}

	function smooth($val) {
		$this->smooth = intval($val);
		return $this;
	}

	function pixelate($val) {
		$this->pixelate = intval($val);
		return $this;
	}

	function lighten($val) {
		$this->lighten = intval($val);
		return $this;
	}

	function darken($val) {
		$this->darken = intval($val);
		return $this;
	}

	function sharpen($val) {
		$this->sharpen = intval($val);
		return $this;
	}

	function sharpenAlt($val) {
		$this->sharpenAlt = intval($val);
		return $this;
	}

	function emboss($val) {
		$this->emboss = intval($val);
		return $this;
	}

	function embossAlt($val) {
		$this->embossAlt = intval($val);
		return $this;
	}

	function blur($val) {
		$this->blur = intval($val);
		return $this;
	}

	function edge($val) {
		$this->edge = intval($val);
		return $this;
	}

	function edgeAlt($val) {
		$this->edgeAlt = intval($val);
		return $this;
	}

	function draw($val) {
		$this->draw = intval($val);
		return $this;
	}

	function mean($val) {
		$this->mean = intval($val);
		return $this;
	}

	function motion($val) {
		$this->motion = intval($val);
		return $this;
	}

	function apply(&$img) {
		if($this->negate) {
			imagefilter($img, IMG_FILTER_NEGATE);
		}
		if($this->grayscale) {
			imagefilter($img, IMG_FILTER_GRAYSCALE);
		}
		if($this->gaussian_blur!==false) {
			for($x=1;$x<=$this->gaussian_blur;$x++) {
				if($x%11 == 0) {//each 10th time apply 'IMG_FILTER_SMOOTH' with 'level of smoothness' set to -7
					imagefilter($img, IMG_FILTER_SMOOTH, -7);
				}
				imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
			}
		}
		if($this->selective_blur!==false) {
			for($x=1;$x<=$this->selective_blur;$x++) {
				imagefilter($img, IMG_FILTER_SELECTIVE_BLUR);
			}
		}
		if($this->eskis!==false) {
			for($x=1;$x<=$this->eskis;$x++) {
				imagefilter($img, IMG_FILTER_MEAN_REMOVAL);
			}
		}
		if($this->brightness) {
			imagefilter($img, IMG_FILTER_BRIGHTNESS, $this->brightness);
		}
		if($this->contrast) {
			imagefilter($img, IMG_FILTER_CONTRAST, $this->contrast);
		}
		if($this->colorize) {
			imagefilter($img, IMG_FILTER_COLORIZE, $this->colorize[0], $this->colorize[1], $this->colorize[2], $this->colorize[3]);
		}
		if($this->smooth) {
			imagefilter($img, IMG_FILTER_SMOOTH, $this->smooth);
		}
		if($this->pixelate) {
			imagefilter($img, IMG_FILTER_PIXELATE, $this->pixelate, true);
		}
		if($this->motion!==false) {
			$laplacian = array(array(1,0,0), array(0,1,0), array(0,0,1));
			for($i=0;$i<$this->motion;$i++) {
				imageconvolution($img, $laplacian, 3, 0);
			}
		}
		if($this->lighten!==false) {
			$laplacian = array(array(0,0,0), array(0,12,0), array(0,0,0));
			for($i=0;$i<$this->lighten;$i++) {
				imageconvolution($img, $laplacian, 9, 0);
			}
		}
		if($this->darken!==false) {
			$laplacian = array(array(0,0,0), array(0,6,0), array(0,0,0));
			for($i=0;$i<$this->darken;$i++) {
				imageconvolution($img, $laplacian, 9, 0);
			}
		}
		if($this->sharpen!==false) {
			$laplacian = array(array(-1,-1,-1), array(-1,16,-1), array(-1,-1,-1));
			for($i=0;$i<$this->sharpen;$i++) {
				imageconvolution($img, $laplacian, 8, 0);
			}
		}
		if($this->sharpenAlt!==false) {
			$laplacian = array(array(0,-1,0), array(-1,5,-1), array(0,-1,0));
			for($i=0;$i<$this->sharpenAlt;$i++) {
				imageconvolution($img, $laplacian, 1, 0);
			}
		}
		if($this->emboss!==false) {
			$laplacian = array(array(1,1,-1), array(1,3,-1), array(1,-1,-1));
			for($i=0;$i<$this->emboss;$i++) {
				imageconvolution($img, $laplacian, 3, 0);
			}
		}
		if($this->embossAlt!==false) {
			$laplacian = array(array(-2,-1,0), array(-1,1,1), array(0,1,2));
			for($i=0;$i<$this->embossAlt;$i++) {
				imageconvolution($img, $laplacian, 3, 0);
			}
		}
		if($this->blur!==false) {
			$laplacian = array(array(1,1,1), array(1,15,1), array(1,1,1));
			for($i=0;$i<$this->blur;$i++) {
				imageconvolution($img, $laplacian, 23, 0);
			}
		}
		if($this->edge!==false) {
			$laplacian = array(array(-1,-1,-1), array(-1,8,-1), array(-1,-1,-1));
			for($i=0;$i<$this->edge;$i++) {
				imageconvolution($img, $laplacian, 9, 0);
			}
		}
		if($this->edgeAlt!==false) {
			$laplacian = array(array(0,1,0), array(1,-4,1), array(0,1,0));
			for($i=0;$i<$this->edgeAlt;$i++) {
				imageconvolution($img, $laplacian, 1, 0);
			}
		}
		if($this->draw!==false) {
			$laplacian = array(array(0,-1,0), array(-1,5,-1), array(0,-1,0));
			for($i=0;$i<$this->draw;$i++) {
				imageconvolution($img, $laplacian, 0, 0);
			}
		}
		if($this->mean!==false) {
			$laplacian = array(array(1,1,1), array(1,1,1), array(1,1,1));
			for($i=0;$i<$this->mean;$i++) {
				imageconvolution($img, $laplacian, 9, 0);
			}
		}
	}

}