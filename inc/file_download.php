<?php

	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "inc_lude/conf_mysqli.php";
	include $home_dir . "inc/SHA256/KISA_SHA256.php";
	include DBCON_MYSQLI;
	include FUNC_MYSQLI;

	$pagechk = false;
	$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	$today = date("Ymd");

	//header("Content-Type: text/html; charset=utf8");
	//header("Cache-Control:no-cache, must-revalidate");
	//header("Pragma:no-cache");

//header("Pragma: public"); // required
//header("Expires: 0");
//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//header("Cache-Control: private",false); // required for certain browsers
   


	//ini_set( 'display_errors', '0');
	//ob_clean();
	//ob_start();

	//레퍼러체크
	if ($referer){
		if ( stripos($referer, 'view') !== false || stripos($referer, 'team') !== false || stripos($referer, 'todaywork') !== false|| stripos($referer, 'live') !== false || stripos($referer, 'admin') !== false ){
			$pagechk = true;
		}
	}else{
		$pagechk = false;
	}

	if(!$pagechk){
		echo '잘못된 호출 입니다.';
		exit;
	}

	
	foreach($_POST as $key => $value){
		//$val = str_replace( array(">","&gt;")  , "" , $value);
		//$aaaa[$key] = $val;
		//print $value;
		//print "\n";
	}

	$idx = $_POST['idx'];
	$num = $_POST['num'];
	$mode = $_POST['mode'];

	//챌린지 뷰페이지 첨부파일 다운로드
	if($mode == "challenges_file_down"){


		if($idx){
			$idx = preg_replace("/[^0-9]/", "", $idx);

			//파일정보 불러오기
			$sql = "select idx, file_path, file_name, file_real_name from work_challenges_resultz where state='1' and idx='".$idx."' and file_path!='' and file_name!=''";
			$file_info = selectQuery($sql);

			if($file_info['idx']){

				$file_path = $file_info['file_path'];
				$file_name = $file_info['file_name'];
				$file_real_name = $file_info['file_real_name'];

				$file_path_ex = explode("/", $file_info['file_path']);

				//$file = $_SERVER['DOCUMENT_ROOT'] . "\\" . "datato\\" . "challenges\\files\\" . $file_name;
				$file = $dir_file_path . $file_path . $file_name;

				//파일위치 파일체크여부
				if (is_file($file)) {
						echo "complete|";
						echo $file_real_name."|";
						echo $home_url.$file_path.$file_name;
						exit;
					/*else{
					
						if (preg_match("MSIE", $_SERVER['HTTP_USER_AGENT'])) { 
							header("Content-type: application/octet-stream"); 
							header("Content-Length: ".filesize("$file"));
							header("Content-Disposition: attachment; filename=$file_real_name");
							header("Content-Transfer-Encoding: binary"); 
							header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
							header("Pragma: public"); 
							header("Expires: 0"); 
						}
						else { 
							header("Content-type: file/unknown"); 
							header("Content-Length: ".filesize("$file")); 
							header("Content-Disposition: attachment; filename=$file_real_name");
							header("Content-Description: PHP3 Generated Data"); 
							header("Pragma: no-cache"); 
							header("Expires: 0"); 
						}
				
						$fp = fopen($file, "rb"); 
						fpassthru($fp);
						fclose($fp);
					}*/

				}else {
					echo "해당 파일이 없습니다.";
				}
			}
		}else{
			echo "해당 파일이 없습니다.";
		}

	}else if($mode == "challenges_file_down_new"){
		if($idx){
			$idx = preg_replace("/[^0-9]/", "", $idx);

			//파일정보 불러오기
			$sql = "select idx, file_path, file_name, file_real_name, file_real_img_name from work_challenges_file_info where state='1' and idx='".$idx."' and file_path!='' and file_name!=''";
			$file_info = selectQuery($sql);

			if($file_info['idx']){

				$file_path = $file_info['file_path'];
				$file_name = $file_info['file_name'];
				$file_real_name = $file_info['file_real_img_name'];

				$file_path_ex = explode("/", $file_info['file_path']);

				//$file = $_SERVER['DOCUMENT_ROOT'] . "\\" . "datato\\" . "challenges\\files\\" . $file_name;
				$file = $dir_file_path . $file_path . $file_name;

				//파일위치 파일체크여부
				if (is_file($file)) {
						echo "complete|";
						echo $file_real_name."|";
						echo $home_url.$file_path.$file_name;
						exit;
					/*else{
					
						if (preg_match("MSIE", $_SERVER['HTTP_USER_AGENT'])) { 
							header("Content-type: application/octet-stream"); 
							header("Content-Length: ".filesize("$file"));
							header("Content-Disposition: attachment; filename=$file_real_name");
							header("Content-Transfer-Encoding: binary"); 
							header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
							header("Pragma: public"); 
							header("Expires: 0"); 
						}
						else { 
							header("Content-type: file/unknown"); 
							header("Content-Length: ".filesize("$file")); 
							header("Content-Disposition: attachment; filename=$file_real_name");
							header("Content-Description: PHP3 Generated Data"); 
							header("Pragma: no-cache"); 
							header("Expires: 0"); 
						}
				
						$fp = fopen($file, "rb"); 
						fpassthru($fp);
						fclose($fp);
					}*/

				}else {
					echo "해당 파일이 없습니다.";
				}
			}
		}else{
			echo "해당 파일이 없습니다.";
		}

	}else if($mode == "sample_file_download"){


		$file_path = "/data/member/";
		$file_real_name = "엑셀 양식 다운로드.xlsx";
		$file_name = "sample_member.xlsx";
		echo $file_real_name ."|". $home_url. $file_path . $file_name;
		exit;

	//오늘업무 첨부파일
	}else if($mode == "todaywork"){

		if($idx && !is_null($num)){
			$idx = preg_replace("/[^0-9]/", "", $idx);
			$num = preg_replace("/[^0-9]/", "", $num);
		
			
			//파일정보 불러오기
			$sql = "select idx, file_path, file_name, file_real_name from work_filesinfo_todaywork where state='0' and idx='".$idx."'";
			$file_info = selectQuery($sql);
			if($file_info['idx']){

				$file_path = $file_info['file_path'];
				$file_name = $file_info['file_name'];

				$T = $_SERVER['HTTP_USER_AGENT'];
				if(strrpos($T,"Chrome") || strrpos($T,"Firefox")) {
					$file_name = iconv('EUC-KR','UTF-8',$file_name);
				}else{
					$file_name = iconv('UTF-8','EUC-KR',$file_name);
				}

				$file_real_name = $file_info['file_real_name'];

				$file_path_ex = explode("/", $file_info['file_path']);
				$file = $dir_file_path . $file_path . $file_name;
				
				header("Content-Disposition: attachment; filename=".iconv("UTF-8","CP949",$file));

				//파일위치 파일체크여부
				if (is_file($file)) {

					echo "complete|";
					echo $file_real_name."|";
					echo $home_url. $file_path . $file_name."|".$file;
					exit;

				}else {
					echo "해당 파일이 없습니다.";
				}
			}
		}else{
			echo "해당 파일이 없습니다.";
		}



	}else{

		if($idx && !is_null($num)){
			$idx = preg_replace("/[^0-9]/", "", $idx);
			$num = preg_replace("/[^0-9]/", "", $num);
		

			//파일정보 불러오기
			$sql = "select idx, file_path, file_name, file_real_name from work_filesinfo_file where state='0' and work_idx='".$idx."' and num='".$num."'";
			$file_info = selectQuery($sql);

			if($file_info['idx']){

				$file_path = $file_info['file_path'];
				$file_name = $file_info['file_name'];
				$file_real_name = $file_info['file_real_name'];

				$file_path_ex = explode("/", $file_info['file_path']);

				//$file = $_SERVER['DOCUMENT_ROOT'] . "\\" . "datato\\" . "challenges\\files\\" . $file_name;
				$file = $dir_file_path . $file_path . $file_name;

				//echo $file;
				//exit;

				//파일위치 파일체크여부
				if (is_file($file)) {
					echo "complete|";
					echo $file_real_name."|";
					echo $home_url. $file_path . $file_name;
					exit;

				}else {
					echo "해당 파일이 없습니다.";
				}
			}
		}else{
			echo "해당 파일이 없습니다.";
		}
	}
?>