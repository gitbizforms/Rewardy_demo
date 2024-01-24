<?
//사이즈 줄이기
exit;
$img = $_SERVER['DOCUMENT_ROOT']."\\datato\\challenges\\20210610175013_sadary3@nate.com_challenges_12.jpg";

function img_resize( $srcimg, $dstimg, $imgpath, $rewidth, $reheight )
{

	echo "$imgpath/$srcimg";

	$src_info = getimagesize("$imgpath/$srcimg");

	print "<pre>";
	print_r($src_info);
	print "</pre>";

	if($rewidth < $src_info[0] || $reheight < $src_info[1]){

		if(($src_info[0] - $rewidth) > ($src_info[1] - $reheight)){
			$reheight = round(($src_info[1]*$rewidth)/$src_info[0]);
		}else{
			$rewidth = round(($src_info[0]*$reheight)/$src_info[1]);
		}

	}else{
		exec("cp $imgpath/$srcimg $imgpath/$dstimg");
		return;
	}


	$dst = imageCreatetrueColor($rewidth, $reheight);

	if($src_info[2] == 1){

		$src = ImageCreateFromGIF("$imgpath/$srcimg");
		imagecopyResampled($dst, $src,0,0,0,0,$rewidth,$reheight,ImageSX($src),ImageSY($src));
		Imagejpeg($dst, "$imgpath/$dstimg", 80);

	}elseif($src_info[2] == 2){

		$src = ImageCreateFromJPEG("$imgpath/$srcimg");
		imagecopyResampled($dst, $src,0,0,0,0,$rewidth,$reheight,ImageSX($src),ImageSY($src));
		Imagejpeg($dst, "$imgpath/$dstimg", 80);

	}elseif($src_info[2] == 3){

		$src = ImageCreateFromPNG("$imgpath/$srcimg");
		imagecopyResampled($dst, $src,0,0,0,0,$rewidth,$reheight,ImageSX($src),ImageSY($src));
		Imagepng($dst, "$imgpath/$dstimg", 80);
	}

	imageDestroy($src);
	imageDestroy($dst);

}


	//사용방법

	$thumbL_width  = '100'; // 리사이징할 가로 사이즈
	$thumbL_height = '100'; // 리사이징할 세로 사이즈

	$upfile_path   =  $_SERVER['DOCUMENT_ROOT']."\\datato\\challenges\\" ; // 화일업로드 경로 (파일명만)
	$srcimg = "20210610175013_sadary3@nate.com_challenges_12.jpg"; // 리사이징할 화일원본
	$dstimg = "re2221.png"; // 리사이징 후 화일명

	$r = img_resize($srcimg, $dstimg, $upfile_path, $thumbL_width, $thumbL_height);
	print_r($r);
?>