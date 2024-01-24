<?	//전체 사용되는 인클루드
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "inc_lude/conf.php";
	include DBCON;
	include FUNC;


	/*
	$pagename = basename($_SERVER['PHP_SELF']);
	$page_arr = explode(".", $pagename);
	echo $page_arr[0];
	echo $page_arr[1];
	*/

	$filename = basename($_SERVER['PHP_SELF']); 
	$file_extension = substr($filename, 0, strrpos($filename, ".")); 
	$file_extension = str_replace("list_","", $file_extension);

	if( $filename =='list_01.php' || $filename =='list_02.php' || $filename =='list_03.php' || $filename =='list_04.php'){
		$file_extension = "_".$file_extension;
	}else{
		$file_extension = "";
	}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge; chrome=1" />
<title>오늘일</title>

<!-- 노토산스 -->
<link href="https://www.bizforms.co.kr/magazine/content/hotclick/css/style_font_notosans.css" rel="stylesheet" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script src="/js/common<? echo $file_extension?>.js?v=<?echo date("YmdHis",time());?>"></script> 

</head>
<body>
