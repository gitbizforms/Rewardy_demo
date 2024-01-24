
<?php
exit;
include str_replace( basename(__DIR__) , "", __DIR__ ) ."inc_lude/conf.php";
include DBCON;
include FUNC;

//header('Content-Type: text/html; charset=UTF-8');
//header("Content-Type: text/html; charset=CP949");


print "<pre>";
print_r($_SERVER);
print "</pre>";

//print_r($_POST);
//exit;

$mode = $_POST["mode"];									////mode값 전달받음
$type_flag = ($chkMobile)?1:0;							//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];

}

//챌린지참여 파일첨부형
if($mode == "challenges_file"){

	$idx = $_POST['idx'];
	$chll_idx = preg_replace("/[^0-9]/", "", $idx);
	$chamyeo_idx = $_POST['chamyeo_idx'];
	$chamyeo_idx = preg_replace("/[^0-9]/", "", $chamyeo_idx);

	print "<pre>";
	print_r($_POST);
	print "</pre>";

	print "<pre>";
	print_r($_FILES);
	print "</pre>";
	
	
	if($chll_idx){

		//챌린지 확인
		$sql = "select idx, attend_type, email, name, coin, title, sdate, edate, day_type, attend, outputchk from work_challenges where state='0' and idx='".$chll_idx."'";
		$challenges_info = selectQuery($sql);
		if($challenges_info['idx']){
			$coin = $challenges_info['coin'];
			$memo = "챌린지 참여 보상";
			$reward_user = $challenges_info['email'];
			$reward_name = $challenges_info['name'];

			/*print "<pre>";
			print_r($_POST);
			print_r($_FILES);
			print "</pre>";*/

			//챌린지 참여 기간내에 참여 할수 있도록 처리
			if( TODATE >= $challenges_info['sdate'] && TODATE <= $challenges_info['edate'] ){

				//참여가능기간
				$where = " and convert(char(10), file_regdate, 120)>='".$challenges_info['sdate']."' and convert(char(10), file_regdate, 120)<='".$challenges_info['edate']."'";
				//챌린지 참여 횟수가 한번만 참여
				if ($challenges_info['day_type'] == "0"){

					$sql = "select count(idx) as cnt from work_challenges_result where state='1' and challenges_idx='".$chll_idx."' and file_path!='' and file_name!='' and email='".$user_id."'".$where."";
					$chall_file_info = selectQuery($sql);
					if($chall_file_info['cnt'] >= 1){
				//		echo "chamyeo|";
				//		exit;
					}

				}else if ($challenges_info['day_type'] == "1"){

					//하루 한번 참여가능
					$sql = "select idx from work_challenges_result where state='1' and challenges_idx='".$chll_idx."' and file_path!='' and file_name!='' and email='".$user_id."' and convert(char(10), file_regdate, 120)='".TODATE."'";
					$chall_file_info = selectQuery($sql);
					if($chall_file_info['idx']){
						//참여완료
					//	echo "chamyeo|";
					//	exit;
					}

					//챌린지 참여회수 체크
					$sql = "select count(idx) as cnt from work_challenges_result where state='1' and challenges_idx='".$chll_idx."' and file_path!='' and file_name!='' and email='".$user_id."'".$where."";
					$chall_file_info = selectQuery($sql);
					if ($chall_file_info['cnt'] >= $challenges_info['attend']){
						echo "chamyeo_max|".$challenges_info['attend'];
						exit;
					}
				}

				//첨부한 파일이 있을경우
				if($_FILES){

					//파일타입(이미지파일:true, 일반파일:false)
					$file_type_img = false;

					//파일명
					$filename = $_FILES['files']['name'][0];
					$ext = array_pop(explode(".", strtolower($filename)));

					//이미지파일 허용확장자
					if(in_array($ext , $img_file_allowed_ext)){
						$file_type_img = true;
					}

					//부서별정보
					if($user_part){
						$sql = "select idx, partname from work_team where state='0' and idx='".$user_part."'";
						$team_info = selectQuery($sql);
						if ($team_info['idx']){
							$partname = $team_info['partname'];
						}
					}


					//이미지 처리부분
					if($file_type_img){

						//파일순번
						$file_img_num = 1;

						//파일확장자 추출
						$filename = $_FILES['files']['name'][0];
						$ext = array_pop(explode(".", strtolower($filename)));

						//허용확장자체크
						if( !in_array($ext, $img_file_allowed_ext) ) {
							echo "ext_file";
							exit;
						}

						//파일타입
						$file_type = $_FILES['files']['type'][0];

						//파일사이즈
						$file_size = $_FILES['files']['size'][0];

						//임시파일명
						$file_tmp_name = $_FILES['files']['tmp_name'][0];

						//파일명
						$file_real_name = $filename;


						$file_source	= $file_tmp_name; //파일명
						//$file_ext		= array_pop(explode('.', $filename)); //확장자 추출 (array_pop : 배열의 마지막 원소를 빼내어 반환)
						$file_info		= getimagesize($file_tmp_name);
						$file_width		= $file_info[0]; //이미지 가로 사이즈
						$file_height	= $file_info[1]; //이미지 세로 사이즈
						$file_type		= $file_type;


						//라사이즈 
						$rezie_file_path = "";
						$rezie_renamefile = "";
						$resize_file = "";
						$resize_val = "0";


						//랜덤번호
						$rand_id = name_random();

						//변경되는 파일명
						list($microtime,$timestamp) = explode(' ',microtime());
						$time = $timestamp.substr($microtime, 2, 3);
						$datetime = date("YmdHis", $timestamp).substr($microtime, 2, 3);

						//$renamefile = date("YmdHis")."_{$rand_id}_challenges_{$res_idx}.{$ext}";
						$renamefile = "{$datetime}_{$rand_id}_challenges_{$chll_idx}.{$ext}";

						//년도
						$dir_year = date("Y", TODAYTIME);

						//월
						$dir_month = date("m", TODAYTIME);

						//업로드 디렉토리 - /data/challenges/files/년/월/
						$upload_path = $dir_file_path."/".$file_save_dir_img."/".$dir_year."/".$dir_month."/";

						//업로드 디렉토리 - /data/challenges/files/년/월/
						$upload_path_ori = $dir_file_path."/".$file_save_dir_img_ori."/".$dir_year."/".$dir_month."/";


						//디렉토리 없는 경우 권한 부여 및 생성
						if ( !is_dir ( $upload_path ) ){
							mkdir( $upload_path , 0777, true);
						}

						//디렉토리 없는 경우 권한 부여 및 생성 - 원본폴더
						if ( !is_dir ( $upload_path_ori ) ){
							mkdir( $upload_path_ori , 0777, true);
						}

						
						//리사이즈한 업로드될 파일경로/파일명
						$upload_files = $upload_path.$renamefile;

						//원본 업로드될 파일경로/파일명
						$upload_files_ori = $upload_path_ori.$renamefile;


						$new_file_width = 250; //이미지 가로 사이즈 지정
						$rate = $new_file_width / $file_width; //이미지 세로 사이즈 및 파일 사이즈(quality) 조절을 위한 비율 
						$new_file_height = (int)($file_height * $rate); 
						$new_quality = (int)($file_size * $rate);

						//이미지 가로사이즈가 250보다 크면 사이즈 조절
						if ($file_width > $new_file_width){
							switch($file_type){
								case "image/jpeg" :
									$image = imagecreatefromjpeg($file_source);
									break;
								case "image/gif" :
									$image = imagecreatefromgif($file_source);
									break;
								case "image/png" :
									$image = imagecreatefrompng($file_source);
									break;
								default:	
									$image = "";
									break;
							}

							//사이즈 조정으로 이미지가 회전하는걸 막기위함
							$degrees = "-90";
							$image = imagerotate($image, $degrees, 0);

							//리사이즈
							$rezie_img = fn_imagejpeg($image, $upload_files, $new_file_width, $new_file_height, $file_width, $file_height, $new_quality);
							

							//원본이미지
							$return = move_uploaded_file($file_tmp_name, $upload_files_ori);

							//리사이즈 파일 용량
							$file_resize = filesize($upload_files); 

						}else{

							$rezie_img = "";
							//파일 업로드
							$return = move_uploaded_file($file_tmp_name, $upload_files_ori);
						}


						if($return){
							//리사이즈이미지 경로
							$file_path = str_replace($dir_file_path , "" , $upload_path);

							//원본이미지 경로
							$file_path_ori = str_replace($dir_file_path , "" , $upload_path_ori);

							//리사이즈 조정이 안된경우
							if($rezie_img == true){
								$rezie_file_path = $file_path;
								$rezie_renamefile = $renamefile;
								$resize_file = $file_resize;
								$resize_val = "1";
							}else{
								$rezie_file_path = "";
								$rezie_renamefile = "";
								$resize_file = "";
								$resize_val = "0";
							}


							echo "\n";
							echo $file_path.$renamefile;
							echo "\n";
							echo $file_path_ori.$renamefile;
							

							//$sql = "resize='".$resize_val."', file_path='".$file_path."', file_name='".$renamefile."', file_size='".$file_size."',file_ori_path='".$file_path_ori."', file_ori_name='".$renamefile."', file_ori_size='".$file_size."', file_real_img_name='".$file_real_name."' , file_type='".$file_type."', file_regdate=".DBDATE." where challenges_idx='".$chll_idx."' and idx='".$chall_file_info['idx']."'";
							//echo $sql;

							exit;
							//참여완료하기
							
							// $sql = "select idx from work_challenges_result where state='0' and challenges_idx='".$chll_idx."' and email='".$user_id."' and idx='".$chamyeo_idx."'";
							// $chall_file_info = selectQuery($sql);
							// if($chall_file_info['idx']){


							// 	$sql = "update work_challenges_result set attend_type='".$challenges_info['attend_type']."', state='1', resize='".$resize_val."', file_path='".$file_path."', file_name='".$renamefile."', file_size='".$file_size."',file_ori_path='".$file_path_ori."', file_ori_name='".$renamefile."', file_ori_size='".$file_size."', file_real_img_name='".$file_real_name."' , file_type='".$file_type."', file_regdate=".DBDATE." where challenges_idx='".$chll_idx."' and idx='".$chall_file_info['idx']."'";
							// 	$res_idx = updateQuery($sql);
							// 	if($res_idx){

							// 		//회원정보 - 정상
							// 		$sql = "select idx from work_member where state='0' and email='".$user_id."'";
							// 		$mem_info = selectQuery($sql);
							// 		if($mem_info['idx']){

							// 			//챌린지에서 지급받은 내역있는 체크
							// 			$sql = "select idx from work_coininfo where state='0' and code='".$ch_code[0]."' and work_idx='".$idx."' and reward_type='".$reward_type[2]."' and auth_file_idx='".$chall_file_info['idx']."' and convert(char(10), regdate, 120) = '".TODATE."'";
							// 			$reward_info = selectQuery($sql);
							// 			if (!$reward_info['idx']){


							// 				//챌린지 작성자 코인 차감
							// 				$sql = "select top 1 coin, highlevel, comcoin from work_member where state='0' and email='".$challenges_info['email']."' order by idx desc";
							// 				$mem_info_coin = selectQuery($sql);
							// 				if($mem_info_coin['coin']){

							// 					//작성한자의 코인이 지급할 코인보다 클경우만
							// 					if($mem_info_coin['comcoin'] >= $coin){

							// 						$memo_min = "챌린지 참여 보상으로 차감";
							// 						$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_file_idx, email, name, reward_user, reward_name, coin, memo, ip)";
							// 						$sql = $sql .= " values('".$ch_code[2]."','".$idx."', '".$reward_type[2]."', '".$chall_file_info['idx']."', '".$reward_user."', '".$reward_name."','".$user_id."', '".$user_name."', '".$coin."', '".$memo_min."', '".LIP."')";
							// 						$coin_min_info = insertQuery($sql);
							// 						if($coin_min_info){

							// 							//관리자권한
							// 							if($mem_info_coin['highlevel']=='0'){
							// 								//챌린지 작성자 코인 차감
							// 								$sql = "update work_member set coin = coin - '".$coin."' where state='0' and email='".$reward_user."'";
							// 								$up = updateQuery($sql);
							// 							//일반사용자
							// 							}else if($mem_info_coin['highlevel']=='5'){
							// 								//챌린지 작성자 코인 차감
							// 								$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and email='".$reward_user."'";
							// 								$up = updateQuery($sql);
							// 							}

							// 							//챌린지 참여자 코인 지급
							// 							$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_file_idx, email, name, reward_user, reward_name, coin, memo, ip)";
							// 							$sql = $sql .= " values('".$ch_code[0]."','".$chll_idx."', '".$reward_type[2]."', '".$chall_file_info['idx']."', '".$user_id."', '".$user_name."','".$reward_user."', '".$reward_name."', '".$coin."', '".$memo."', '".LIP."')";
							// 							$coin_add_info = insertQuery($sql);

							// 							//챌린지 참여자 코인 지급
							// 							$sql = "update work_member set coin = coin + '".$coin."' where state='0' and email='".$user_id."'";
							// 							$up = updateQuery($sql);

							// 							//챌린지 참여시 라이브ON 상태변경
							// 							$sql = "select idx, live_1 from work_member where state='0' and live_1='0' and email='".$user_id."'";
							// 							$mem_live_info = selectQuery($sql);
							// 							if($mem_live_info['idx']){
							// 								$sql = "update work_member set live_1='1', live_1_regate=".DBDATE." where idx='".$mem_live_info['idx']."'";
							// 								$live_up = updateQuery($sql);
							// 							}

							// 							if($coin_add_info && $up){
							// 								echo "complete";
							// 								exit;
							// 							}
							// 						}
							// 					}
							// 				}
							// 			}else{
							// 				echo "coin_info";
							// 				exit;
							// 			}
							// 		}
							// 	}else{
							// 		echo "update_err";
							// 		exit;
							// 	}
							// }

						}else{
							echo "file_not_upload";
							exit;
						}
					
					//일반파일처리
					}
				}
			}else{
				echo "day_expire";
				exit;
			}
		}
	}
	exit;
}

?>