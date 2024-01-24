<?

	//선택다운로드
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "inc_lude/conf_mysqli.php";
	include $home_dir . "inc/SHA256/KISA_SHA256.php";
	include DBCON_MYSQLI;
	include FUNC_MYSQLI;

	$mode = $_POST['mode'];
	if($mode == "file_multi_download"){

		$chll_idx = $_POST['chll_idx'];
		$auth_file_idx = $_POST['auth_file_idx'];
		$chll_idx = preg_replace("/[^0-9]/", "", $chll_idx);

		if($auth_file_idx){
			$auth_file_idx = explode(",", $auth_file_idx);
		}

		if($chll_idx){

			//챌린지정보
			$sql = "select idx from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chll_idx."'";
			$chll_info = selectQuery($sql);
			if($chll_info['idx']){
				if($auth_file_idx){


					//https://rewardy.co.kr/data/challenges/multi_download/2023/02//rewardy_20230208164253.zip
					//$file_save_dir_multidownload = "data/challenges/multi_download";
					//업로드 디렉토리 - /data/고유번호/랜덤폴더/challenges/files/년/월/
					$download_path = $dir_file_path."/".$file_save_dir_multidownload."/".$dir_year."/".$dir_month."/";
					$download_path = str_replace($file_save_dir_multidownload , "data/".$companyno."/".$comfolder."/"."challenges/multi_download" , $download_path);
	
					//디렉토리 없는 경우 권한 부여 및 생성
					if ( !is_dir ( $download_path ) ){
						$mkdir_result = mkdir( $download_path , 0777, true);
					}

					$zip = new ZipArchive();
					$zip_name = iconv("UTF-8","cp949//IGNORE", "rewardy_".date("YmdHis").".zip");
					$res = $zip->open($download_path.$zip_name, ZipArchive::CREATE);

					if($res == true){
						for ($i=0; $i<count($auth_file_idx); $i++){
							$sql = "select idx, file_path, file_name, file_ori_path, file_ori_name, resize, file_type, file_real_img_name, file_real_name from work_challenges_result where state in('1','2') and challenges_idx='".$chll_info['idx']."' and idx='".$auth_file_idx[$i]."' and file_path!='' and file_name!=''";
							$file_info = selectQuery($sql);
							if($file_info['idx']){

								//이미지 파일인 경우
								//$image_type_array >> /inc_lude/conf.php 설정되어있음
								if (in_array($file_info['file_type'] , $image_type_array)){

									if($file_info['resize'] == "0"){
										//$file_path = $dir_file_path . $file_info['file_path'] . $file_info['file_name'];
										$file_path = $dir_file_path . $file_info['file_ori_path'] . $file_info['file_ori_name'] ;
									}else{
										$file_path = $dir_file_path . $file_info['file_ori_path'] . $file_info['file_ori_name'] ;
									}
									$zip->addFile($file_path, $file_info['file_real_img_name']);

								}else{
									$file_path = $dir_file_path . $file_info['file_path'] . $file_info['file_name'];
									$zip->addFile($file_path, $file_info['file_real_name']);
								}
							}
						}
						
						$zip->close();

						if(file_exists(str_replace("\\","/",$download_path.$zip_name))){
							$chkMobile=1;
							if($chkMobile=='1'){
								$domain_url = $home_url;
								$dn_url = str_replace( $dir_file_path , $domain_url, $download_path);
								echo "complete|".$zip_name."|".$dn_url.$zip_name;

							}else{

								header('Content-Description: File Transfer');
								header('Content-Type: application/octet-stream');
								header('Content-Disposition: attachment; filename="'.$zip_name.'"');
								header('Content-Transfer-Encoding: binary');
								header('Expires: 0');
								header('Cache-Control: must-revalidate');
								header('Pragma: public');
								header('Content-Length: '.filesize(str_replace("\\","/",$download_path.$zip_name)));
								ob_clean();
								flush();
								readfile($download_path.$zip_name);
								exit;


								////////
								/*header("Content-Type:application/octet-stream");
								header("Content-Disposition:attachment;filename=".$zip_name);
								header("Content-Transfer-Encoding:binary");
								header("Content-Length:".filesize(str_replace("\\","/",$download_path.$zip_name)));
								header("Cache-Control:cache,must-revalidate");
								header("Pragma:no-cache");
								header("Expires:0");
								if(is_file($download_path."\\".$zip_name)) {
									$fp = fopen($download_path."\\".$zip_name,"r");
									while(!feof($fp)){
										$buf = fread($fp,8096);
										$read = strlen($buf);
										print($buf);
										flush();
									}
									fclose($fp);
								}*/
								////////
							}
						}else{

							echo "nononnono";
						}

					}else{

						echo "". $res;
					}
				}
			}
		}
	}

	exit;

?>