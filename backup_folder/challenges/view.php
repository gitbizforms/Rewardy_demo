<?php
	$newUrl = "https://rewardy.co.kr/challenge/view.php";
	header("Location: $newUrl");
	exit;
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	//define('DB_CHARSET', 'utf8mb4');
	include $home_dir . "/inc_lude/header_index.php";
	include $home_dir . "/challenges/challenges_header.php";

	// if($_SERVER['HTTP_REFERER']){
	// 	alert("URI 형식으로 접근이 불가능합니다.");
	// 	location_link('https://rewardy.co.kr/challenges/index.php');
	// }

?>
<link rel="stylesheet" type="text/css" href="/html/css/challenge_03.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/challenge_pop.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/challenge_pop_03.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script> -->
<script src="/html/js/slick.min.js"></script> 
<script src="/html/js/slick.js"></script> 
<!-- <link rel="stylesheet" type="text/css" href="/html/css/mainy.css<?php echo VER;?>" /> -->
<style>
	.slick_prev { left: 100px; background: url(/html/images/pre/ico_prev.png) no-repeat 50% 50%; } 
	.slick_next { right: 100px; background: url(/html/images/pre/ico_next.png) no-repeat 50% 50%; } 

	.slick_prev:hover { background: url(/html/images/pre/ico_prev.png) no-repeat 50% 50% #333; border: none; } 
	.slick_next:hover { background: url(/html/images/pre/ico_next.png) no-repeat 50% 50% #333; border: none; } 
</style>
<?
	$edit_btn = false;
	if(@in_array($user_id , $edit_user_arr)){
		$edit_btn = true;
	}


	//챌린지 번호
	$idx = $_GET['idx'];
	// $idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	//카테고리 번호
	$cate_num = $_GET['cate'];
	// $cate_num = $_POST['cate'];	
	// $cate = preg_replace("/[^0-9]/", "", $cate);

	//백오피스 - 챌린지 템플릿 경로를 통해서만 수정이 가능함
	$temp = $_GET['temp_auth'];
	// $temp = $_POST['temp_auth'];

	if($idx){

		//챌린지카테고리
		$sql = "select idx, name from work_category where state='0' order by rank asc";
		$cate_info = selectAllQuery($sql);
		for($i=0; $i<count($cate_info['idx']); $i++){
			$category[$cate_info['idx'][$i]] = $cate_info['name'][$i];
		}

		//샘플페이지 경우
		$sample_btn = false;
		/*if ($_SERVER['HTTP_REFERER']){
			if (strstr($_SERVER['HTTP_REFERER'] , "sample")){
				$sample_btn = true;
			}
		}*/

		//챌린지 정보
		$sql = "select idx, email, attend_type, cate, template, name, day_type, attend, view_flag,  coin, (CASE WHEN day_type='1' THEN (coin * attend) WHEN day_type='0' THEN coin END ) as maxcoin,";
		$sql = $sql .= " title, holiday_chk, attend_chk, sdate, edate, temp_flag, limit_count from work_challenges where state in ('0','1') and companyno='".$companyno."' and idx='".$idx."'";
		$ch_info = selectQuery($sql);
		//챌린지 삭제,비삭제여부
		$sql = "select state from work_challenges where companyno='".$companyno."' and idx='".$idx."'";
		$ch_state = selectQuery($sql);
		$page_state = $ch_state['state'];

		if($ch_info['idx']){
			$idx = $ch_info['idx'];
			$attend_type = $ch_info['attend_type'];
			$ch_cate = $ch_info['cate'];
			$template = $ch_info['template'];
			$title = $ch_info['title'];
			$attend = $ch_info['attend'];
			$day_type = $ch_info['day_type'];
			$maxcoin = $ch_info['maxcoin'];
			$coin = $ch_info['coin'];
			$view_flag = $ch_info['view_flag'];
			$limit_cnt = $ch_info['limit_count'];
			$ch_title = urldecode($title);

			//br이 포함되어 있는경우, 제목이 35자 이하 일때 태그제거함
			//	echo mb_strpos($ch_title , "br");

			if(strpos($ch_title, "br") !== false){
				if(mb_strpos($ch_title , "br") < 35){
					//$ch_title = str_replace("<br>", "", $ch_title);
					//$ch_title = preg_replace('/r|n/', '', $ch_title);
					//$ch_title = preg_replace('/\r\n|\r|\n/','',$ch_title);
					$ch_title = strip_tags($ch_title);
				}
			}
			$cate_arr = ['실행','협업','성장','성실','에너지','성과'];
			if($ch_cate){
				for($i=1;$i<=6;$i++){
					if($ch_cate == $i){
						$cate_name = $cate_arr[$i-1];
					}
				}
			}

			//매일참여가능한 챌린지
			if($day_type=='1'){
				$ch_coin = "최대 ". number_format($ch_info['maxcoin']);
			}else if($day_type=='0'){
				$ch_coin = number_format($ch_info['coin']);
			}

			$holiday_chk = $ch_info['holiday_chk'];
			if($holiday_chk){
				$holiday_chk_text = "";
			}

			$sdate = $ch_info['sdate'];
			$edate = $ch_info['edate'];
			

			$sdate_re = str_replace("-", ".",$sdate);
			$edate_re = str_replace("-", ".",$edate);
			$name = $ch_info['name'];

			if($template == "1"){
				$template_btn = true;
			}


			//챌린지작성자 부서명
			$chall_editor = $member_list_all['partname'][$ch_info['email']];

			//조회수 업데이트
			$sql = "update work_challenges set pageview = CASE WHEN pageview >=0 THEN pageview + 1 ELSE 0 END where idx='".$ch_info['idx']."'";
			updateQuery($sql);

			//챌린지내용
			$sql = "select idx, contents from work_contents where state='0' and work_idx='".$ch_info['idx']."'";
			$contents_info = selectQuery($sql);
			if($contents_info['idx']){
				//$ch_contents =  urldecode($contents_info['contents']);
				$ch_contents =  $contents_info['contents'];
				//$ch_contents =  rawurldecode($contents_info['contents']);
				$ch_contents = preg_replace('/\r\n|\r|\n/','',$ch_contents);
				//	$ch_contents = addslashes($ch_contents);
			}

			//참여자정보
			//$sql = "select idx, email, name, from work_challenges_user where challenges_idx='".$idx."'";
			$sql = "select a.idx, a.email, a.name, b.part, b.partno from work_challenges_user as a left join work_member as b on(a.email=b.email)";
			$sql = $sql .=" where b.state='0' and b.companyno = '".$companyno."' and a.challenges_idx='".$idx."' order by";
			$sql = $sql .= " CASE WHEN a.email = '".$user_id."' THEN a.email END DESC, CASE WHEN a.email <> '".$user_id."' THEN a.name end asc";
			$user_info = selectAllQuery($sql);
			if($user_info['idx']){
				$total_cnt = count($user_info['idx']);

				if( $total_cnt >= 3){
					$user_total_cnt = $total_cnt - 3;
				}
			}


			//첨부파일정보
			$sql = "select idx, file_path, file_name, file_real_name from work_filesinfo_file where state='0' and work_idx='".$idx."'";
			$file_info = selectAllQuery($sql);

			//첨부이미지정보
			$sql = "select idx, resize, file_path, file_name,file_ori_path, file_ori_name, file_real_name from work_filesinfo_img where state='0' and work_idx='".$idx."'";
			$img_info = selectAllQuery($sql);


			//챌린지 참여형태, 1:메시지형, 2:파일첨부, 3:혼합형
			if($attend_type == "3"){

				//도전여부 체크
				$chamyeo_btn = false;

				//등록된 인증파일, 메시지
				$chamyeo_chall_btn = false;

				//참여가능기간
				//$where = " and convert(char(10), file_regdate, 120)>='".$sdate."' and convert(char(10), file_regdate, 120)<='".$edate."'";
				$where = " and (DATE_FORMAT(comment_regdate, '%Y-%m-%d')>='".$sdate."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')<='".$edate."' or DATE_FORMAT(file_regdate, '%Y-%m-%d')>='".$sdate."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')<='".$edate."')";
				//챌린지 참여 횟수가 한번만 참여
				if ($day_type == "0"){

					//완료한 인증메시지 + 인증파일 체크
					$sql = "select idx, comment from work_challenges_result where state='1' and challenges_idx='".$idx."' and email='".$user_id."'".$where." order by idx desc limit 1";
					$chall_mix_info = selectQuery($sql);
					if($chall_mix_info['idx']){
						$chamyeo_btn = true;
						$chall_mix_idx = $chall_mix_info['idx'];
						$chall_comment_contents = urldecode($chall_mix_info['comment']);
					}else{

						//도전중인 인증메시지 체크
						$sql = "select idx from work_challenges_result where state='0' and challenges_idx='".$idx."' and (comment is null or file_path is null) and file_name is null and email='".$user_id."'".$where." order by idx desc limit 1";
						$chall_mix_info = selectQuery($sql);
						if($chall_mix_info['idx']){
							$chall_mix_idx = $chall_mix_info['idx'];
						}
					}

					$chall_list_mix_idx = $chall_mix_idx;

				}else if ($day_type == "1"){

					//하루 한번 참여가능
					$where = " and (DATE_FORMAT(comment_regdate, '%Y-%m-%d')='".TODATE."' or DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."')";
					$sql = "select idx, comment from work_challenges_result where state='1' and challenges_idx='".$idx."' and email='".$user_id."'".$where." order by idx desc limit 1";
					$chall_mix_info = selectQuery($sql);
					if($chall_mix_info['idx']){
						$chamyeo_btn = true;
						$chall_mix_idx = $chall_mix_info['idx'];
						$chall_comment_contents = urldecode($chall_mix_info['comment']);
					}else{
						//도전중인 혼합형 체크
						$sql = "select idx from work_challenges_result where state='0' and challenges_idx='".$idx."' and (comment!='' or (file_path!='' and file_name!='')) and email='".$user_id."'".$where." order by idx desc limit 1";
						$chall_mix_info = selectQuery($sql);
						if($chall_mix_info['idx']){
							$chall_mix_idx = $chall_mix_info['idx'];
						}
					}

					$chall_list_mix_idx = $chall_mix_idx;


					//챌린지 참여회수 체크
					$sql = "select count(idx) as cnt from work_challenges_result";
					$sql = $sql .= " where state='1' and challenges_idx='".$idx."'  and email='".$user_id."'".$where."";
					$chall_files_info = selectQuery($sql);
					if ($chall_files_info['cnt'] >= $ch_info['attend']){
						$chamyeo_btn = true;
					}
				}


				$sql = "select count(1) as cnt from work_challenges_result where state in('1','2') and challenges_idx='".$idx."'";
				$user_tlist_info = selectQuery($sql);
				if($user_tlist_info['cnt']){
					$user_masage_cnt = number_format($user_tlist_info['cnt']);
				}else{
					$user_masage_cnt = 0;
				}

				//챌린지 참여 메시지형
				$sql ="select email, count(idx) as cnt from work_challenges_result where state in('1','2') and comment!='' and challenges_idx='".$idx."' group by email";
				$user_list_info = selectAllQuery($sql);
				if($user_list_info['email']){
					$user_list_count = @array_combine($user_list_info['email'], $user_list_info['cnt']);
				}


				//혼합형
				$sql ="select idx, state, email, name, part, comment, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as com_reg, DATE_FORMAT(comment_regdate, '%Y-%m-%d') as com_ymd, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as com_ddd, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as file_reg, DATE_FORMAT(file_regdate, '%Y-%m-%d') as file_ymd, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as file_ddd, DATE_FORMAT(comment_regdate, '%l:%i %p') as com_time";
				$sql = $sql .=", resize, file_path, file_name, file_real_name, file_ori_path, file_ori_name, file_real_img_name, file_type from work_challenges_result where state in ('1','2') and challenges_idx='".$idx."' order by com_reg desc";

				$list_info = selectAllQuery($sql);
				if($list_info['idx']){
					$list_cnt = number_format(count($list_info['idx']));
				}

				for($i=0; $i<count($list_info['idx']); $i++){
					
					$cidx = $list_info['idx'][$i];
					$cemail = $list_info['email'][$i];
					$cname = $list_info['name'][$i];
					$cpart = $list_info['part'][$i];
					$state = $list_info['state'][$i];

					$com_time = $list_info['com_time'][$i];

					//인증메시지
					$com_reg = $list_info['com_reg'][$i];
					$com_ymd = $list_info['com_ymd'][$i];
					$com_his = $list_info['com_ddd'][$i];

					//인증파일
					$file_reg = $list_info['file_reg'][$i];
					$file_ymd = $list_info['file_ymd'][$i];
					$file_his = $list_info['file_ddd'][$i];

					$comment = urldecode($list_info['comment'][$i]);
					$resize = $list_info['resize'][$i];
					$file_type = $list_info['file_type'][$i];

					if($file_type == "video/mp4"){
						$file_path = $list_info['file_path'][$i];
						$file_name = $list_info['file_name'][$i];
					}else if($resize == '0'){
						$file_path = $list_info['file_ori_path'][$i];
						$file_name = $list_info['file_ori_name'][$i];
					}else{
						$file_path = $list_info['file_path'][$i];
						$file_name = $list_info['file_name'][$i];
					}
					
					$file_real_name = $list_info['file_real_name'][$i];
					$file_real_img_name = $list_info['file_real_img_name'][$i];
					$cfiles = $file_path.$file_name;

					$ori_img_src = $list_info['file_ori_path'][$i].$list_info['file_ori_name'][$i];

					//인증메시지 요일구함
					$com_reg_date = explode("-", $com_ymd);
					$int_reg = date("w", strtotime($com_reg));
					$com_date_yoil =  $weeks[$int_reg];
					if($com_reg_date){
						$com_reg_date_m = (int)$com_reg_date[1];
						$com_reg_date_d = preg_replace('/(0)(\d)/','$2', $com_reg_date[2]);
						$com_date_md = $com_reg_date_m."월 ". $com_reg_date_d."일 ";
					}
					
					//인증파일 요일구함
					$file_reg_date = explode("-", $file_ymd);
					$int_reg = date("w", strtotime($file_reg));
					$file_date_yoil =  $weeks[$int_reg];

					if($file_reg_date){
						$file_reg_date_m = $file_reg_date[1];
						$file_reg_date_d = preg_replace('/(0)(\d)/','$2', $file_reg_date[2]);
						$file_date_md = $file_reg_date_m."월 ". $file_reg_date_d."일 ";
					}

					//최근 3건의 내역
					if($i < 3){

						//완료된건만 표기
						if($state == 1){
							$view_list_top3[$com_ymd][$i]['date'] = $com_ymd;
							$view_list_top3[$com_ymd][$i]['idx'] = $cidx;
							$view_list_top3[$com_ymd][$i]['email'] = $cemail;
							$view_list_top3[$com_ymd][$i]['name'] = $cname;
							$view_list_top3[$com_ymd][$i]['part'] = $cpart;

							$view_list_top3[$com_ymd][$i]['comment'] = $comment;
							$view_list_top3[$com_ymd][$i]['files'] = $cfiles;
							$view_list_top3[$file_ymd][$i]['file_type'] = $file_type;


							$view_list_top3[$com_ymd][$i]['com_yoil'] = $com_date_yoil;
							$view_list_top3[$com_ymd][$i]['com_md'] = $com_date_md;
							$view_list_top3[$com_ymd][$i]['com_hi'] = $com_time;

							$view_list_top3[$file_ymd][$i]['file_yoil'] = $file_date_yoil;
							$view_list_top3[$file_ymd][$i]['file_md'] = $file_date_md;
							$view_list_top3[$file_ymd][$i]['file_hi'] = $file_chiss;

							$view_list_top3[$com_ymd][$i]['state'] = $state;
							$view_list_ymd_top3[]= $com_ymd;
						}
					}

					//전체내역
					$chall_mix_list[$com_ymd][$i]['date'] = $com_ymd;
					$chall_mix_list[$com_ymd][$i]['idx'] = $cidx;
					$chall_mix_list[$com_ymd][$i]['email'] = $cemail;
					$chall_mix_list[$com_ymd][$i]['name'] = $cname;
					$chall_mix_list[$com_ymd][$i]['part'] = $cpart;
					$chall_mix_list[$com_ymd][$i]['comment'] = $comment;
					$chall_mix_list[$file_ymd][$i]['files'] = $cfiles;
					$chall_mix_list[$com_ymd][$i]['com_yoil'] = $com_date_yoil;
					$chall_mix_list[$com_ymd][$i]['com_md'] = $com_date_md;
					$chall_mix_list[$com_ymd][$i]['com_hi'] = $com_time;

					$chall_mix_list[$file_ymd][$i]['file_yoil'] = $file_date_yoil;
					$chall_mix_list[$file_ymd][$i]['file_md'] = $file_date_md;
					$chall_mix_list[$file_ymd][$i]['file_hi'] = $file_chiss;
					$chall_mix_list[$file_ymd][$i]['file_type'] = $file_type;
					
					$chall_mix_list[$com_ymd][$i]['state'] = $state;
					$chall_mix_list_ymd[]= $com_ymd;

					$chall_mix_img[$cidx] = $ori_img_src;
				}

				//배열키값 중복제거
				$view_list_ymd_top3 = array_unique($view_list_ymd_top3);

				//배열키값 리셋
				$view_list_ymd_top3 = array_key_reset($view_list_ymd_top3);

				//배열키값 중복제거
				$chall_mix_list_ymd = array_unique($chall_mix_list_ymd);

				//배열키값 리셋
				$chall_mix_list_ymd = array_key_reset($chall_mix_list_ymd);

				//선착순비교
				if($day_type == 1){
					$sql = "select count(idx) as cnt from work_challenges_result where challenges_idx = '".$idx."' and state = '1' and DATE_FORMAT(comment_regdate, '%Y-%m-%d') = '".TODATE."' ";
					$query = selectQuery($sql);
					$cham_cnt = $query['cnt'];
				}else{
					$sql = "select count(idx) as cnt from work_challenges_result where challenges_idx='".$idx."' and state = '1'";
					$query = selectQuery($sql);
					$cham_cnt = $query['cnt'];
				}

				//오늘 참여한 내역 체크
				$sql = "select idx from work_challenges_result where state='1' and challenges_idx='".$idx."' and email='".$user_id."' and comment!='' and file_path!='' and file_name!='' and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')='".TODATE."'";
				$ch_masage_info = selectQuery($sql);

				//참여횟수체크
				$sql = "select count(idx) as cnt from work_challenges_result where state='1' and challenges_idx='".$idx."' and email='".$user_id."'";
				$sql = $sql .=" and comment!='' and file_path!='' and file_name!='' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')>='".$sdate."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')<='".$edate."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')>='".$sdate."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')<='".$edate."'";
				$masage_list_info = selectQuery($sql);
				if($masage_list_info['idx']){
					$chamyeo_cnt = count($masage_list_info['idx']);
				}else{
					$chamyeo_cnt = 0;
				}

				if($chamyeo_cnt >= $attend || !$ch_masage_info['idx']){
					$chamyeo_chk = false;
				}else{
					$chamyeo_chk = true;
				}

				//챌린지 인증메시지, 날짜별
				$sql ="select DATE_FORMAT(comment_regdate, '%Y-%m-%d') as ymd from work_challenges_result where state in('1','2') and challenges_idx='".$idx."' and comment!='' and file_path!='' and file_name!=''";
				$sql = $sql .=" group by DATE_FORMAT(comment_regdate, '%Y-%m-%d') order by DATE_FORMAT(comment_regdate, '%Y-%m-%d') desc";
				$date_masage_info = selectAllQuery($sql);

				$sql = "select count(1) as cnt from work_challenges_result where state in('1','2') and challenges_idx='".$idx."' and file_path!='' and file_name!=''";
				$user_tlist_info = selectQuery($sql);
				if($user_tlist_info['cnt']){
					$user_file_cnt = number_format($user_tlist_info['cnt']);
				}else{
					$user_file_cnt = 0;
				}

				//챌린지 참여 파일첨부형
				$sql ="select email, count(idx) as cnt from work_challenges_result where state in('1','2') and challenges_idx='".$idx."'";
				// $sql = $sql .= " and file_path!='' and file_name!=''";
				$sql = $sql .= " group by email";
				$user_list_file = selectAllQuery($sql);
				if($user_list_file['email']){
					$user_file_count = @array_combine($user_list_file['email'], $user_list_file['cnt']);
				}

				//챌린지참여 전체 인증파일 참여횟수
				$sql = "select idx, state, email, name, part, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as reg, DATE_FORMAT(file_regdate, '%Y-%m-%d') as ymd, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as ddd";
				$sql = $sql .=", resize, file_path, file_name, file_real_name, file_ori_path, file_ori_name, file_real_img_name, file_type from work_challenges_result where state in('1','2') and challenges_idx='".$idx."' order by reg desc";

				$chall_file_info = selectAllQuery($sql);
				if($chall_file_info['idx']){
					$chamyeo_file_cnt = number_format(count($chall_file_info['idx']));
				}else{
					$chamyeo_file_cnt = "";
				}

				//오늘 참여한 내역 체크
				$sql = "select idx from work_challenges_result where state='1' and challenges_idx='".$idx."' and email='".$user_id."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."'";
				$ch_file_info = selectQuery($sql);

				//참여횟수체크
				if($user_file_count[$user_id] >= $attend || !$ch_file_info['idx']){
					$chamyeo_chk = false;
				}else{
					$chamyeo_chk = true;
				}

				//챌린지 인증메시지, 날짜별
				$sql ="select DATE_FORMAT(comment_regdate, '%Y-%m-%d') as ymd from work_challenges_result where state='1' and challenges_idx='".$idx."'";
				$sql = $sql .=" group by DATE_FORMAT(comment_regdate, '%Y-%m-%d') order by DATE_FORMAT(comment_regdate, '%Y-%m-%d') desc";
				$date_mix_info = selectAllQuery($sql);

				$auth_edit = false;
				if ($user_file_cnt=='0' && $user_masage_cnt=='0'){
					$auth_edit = true;
				}

			}
		}

		//좋아요 리스트
		$like_flag_list = array();
		$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and send_email='".$user_id."' and workdate='".TODATE."'";
		$like_info = selectAllQuery($sql);
		for($i=0; $i<count($like_info['idx']); $i++){
			$like_info_idx = $like_info['idx'][$i];
			$like_info_email = $like_info['email'][$i];
			$like_info_work_idx = $like_info['work_idx'][$i];
			$like_info_like_flag = $like_info['like_flag'][$i];
			$like_info_send_email = $like_info['send_email'][$i];
			
			$work_like_list[$like_info_work_idx] = $like_info_idx;
		}

	}
	//로그인아이디
	//echo $user_id;
	//프로필 캐릭터 사진
	$character_img_info = character_img_info();
?>

<!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"> -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="/editor/summernote/summernote-lite.css<?php echo VER;?>" rel="stylesheet">
<script src="/editor/summernote/summernote-lite.js<?php echo VER;?>"></script>
<script src="/editor/summernote/lang/summernote-ko-KR.min.js<?php echo VER;?>"></script>
<script>
	$('#chall_contents').summernote('fontName', 'Noto Sans KR');
	$('#chall_contents').summernote('fontSize', '12');
</script>


<script>
	var ori_img_src = new Array();
	<? foreach($chall_mix_img as $key => $val){?>
		ori_img_src[<?=$key?>] = "<?=$val?>";
	<?}?>

	var file_ori_img_src = new Array();
	<? foreach($chall_file_img as $key => $val){?>
		file_ori_img_src[<?=$key?>] = "<?=$val?>";
	<?}?>
</script>
<style>
	
	@import url(//fonts.googleapis.com/earlyaccess/nanumgothic.css);
	.nanumgothic * {
		font-family: 'Nanum Gothic';
	}

	.img-box { border:1px solid; padding:10px; width:200px;height:120px; }

	.remove_img_preview {
		position:relative;
		top:-25px;
		right:5px;
		background:black;
		color:white;
		border-radius:50px;
		font-size:0.9em;
		padding: 0 0.3em 0;
		text-align:center;
		cursor:pointer;
	}

	.thumb {
		width: 100%;
		height: 100%;
		margin: 0.2em -0.7em 0 0;
	}

	.note-editable p {
		margin: 0;
	}

	.note-editable hr {
		border: 1px solid #c1c1c1;
	}
</style>
<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<? include $home_dir . "/inc_lude/header_new.php";?>
				<!--상단-->
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in" id="rew_conts_in">
						<!-- <div class="rew_header">
							<div class="rew_header_in">
								<div class="rew_header_notice">
									<span></span>
								</div>
							</div>
						</div> -->


						<?/* 상단 카테고리, 제목 주석처리
						<div class="rew_cha_view_top">
							<div class="rew_cha_view_top_in">
								<div class="view_top_title">
									<div class="view_top_title_in">
										<button class="btn_back_list"><span>목록으로</span></button>
										<strong>[<?=$chall_category[$ch_cate]?>] <?=$ch_title?></strong>

										<?if ($chamyeo_chk){?>
											
										<?}else{?>
											<?if($sample_btn){?>
												<button class="btn_join_ok" id="sample_btn"><span>사용하기</span></button>
											<?}else{?>
												
											<?}?>
										<?}?>
									</div>
								</div>
								<div class="view_top_nav">
									<div class="view_top_nav_in">
										<ul>
											<li><button class="on" id="go_view_01"><span>챌린지 보기</span></button></li>
											<li><button id="go_view_02"><span>인증 파일</span></button></li>
											<li><button id="go_view_03"><span>인증 메시지</span></button></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						*/?>

						<div class="rew_conts_scroll_06">
							<input type="hidden" id="chall_cate" value="<?=$cate_num?>">
							<? if($sdate > TODATE || $edate < TODATE){?>
								<input type="hidden" id="edit_pos" value="true">
							<?}?>
							<div class="rew_cha_view">
								<div class="rew_cha_view_in">
									<div class="rew_cha_view_box">
										<div class="rew_cha_view_header">
											<div class="rew_cha_view_header_in">
												<div class="view_left">
													<div class="view_title">[<?=$chall_category[$ch_cate]?>] <?=$ch_title?></div>
													<div class="view_coin">
														<strong><?=$ch_coin?></strong>
														<span>코인</span>
													</div>
													<div class="view_info">
														<ul>
															<?if($limit_cnt>0){?>
															<li><span>선착순 <?=$limit_cnt?>명</span></li>
															<?}?>
															<li><span>하루 1회</span></li>
															<li><span>최대 <?=$attend?>회</span></li>
															<?if($holiday_chk){?>
															<li><span>공휴일제외</span></li>
															<?}?>
															<li><span class="view_date"><?=$sdate_re?> ~ <?=$edate_re?></span></li>
															<li><span><?=$cate_name?> UP</span></li>
														</ul>
													</div>
												</div>
												<div class="view_right">
													<div class="view_user">
														<ul>
															<?if($total_cnt > 0){?>
																<?if($total_cnt == 1){ ?>
																	<li><button><img src="/html/images/pre/ico_user_001.png" alt="" /></button></li>
																<?}else if($total_cnt == 2){?>
																	<li><button><img src="/html/images/pre/ico_user_001.png" alt="" /></button></li>
																	<li><button><img src="/html/images/pre/ico_user_002.png" alt="" /></button></li>
																<?}else{?>
																	<li><button><img src="/html/images/pre/ico_user_001.png" alt="" /></button></li>
																	<li><button><img src="/html/images/pre/ico_user_002.png" alt="" /></button></li>
																	<li><button><img src="/html/images/pre/ico_user_003.png" alt="" /></button></li>
																	<li><button><span>+ <?=$user_total_cnt?></span></button></li>
																<?}?>
															<?}?>
															
														</ul>
													</div>
													<div class="view_writer">
														<span>editor.</span>
														<strong><?=$name?><?=$chall_editor?"(".$chall_editor.")":""?></strong>
													</div>
												</div>
											</div>
										</div>

										<div class="rew_cha_view_editor">
											<div class="rew_cha_view_editor_in">
												<?=$ch_contents?>
											</div>
										</div>
										<input type="hidden" id="cate_num" value="<?=$ch_cate?>">
										<input type="hidden" id="view_idx" value="<?=$idx?>">
										<input type="hidden" id="page_state" value="<?=$page_state?>">
										<input type="hidden" id="service">
										<input type="hidden" id="attend_type" value="<?=$ch_info['attend_type']?>">
										<input type="hidden" id="user_id_on" value="<?=$user_id?>">
										<?if($file_info['idx']){?>
											<div class="rew_cha_view_file">
												<div class="rew_cha_view_file_in">
													<div class="title_area">
														<strong class="title_main">첨부파일을 확인하세요!</strong>
													</div>
													<ul>
														<?for($i=0; $i<count($file_info['idx']); $i++){?>
														<li>
															<div class="file_box">
																<div class="file_desc" id="file_down<?=$i?>" value="<?=$i?>">
																	<span><?=$file_info['file_real_name'][$i]?></span>
																	<strong>다운로드</strong>
																</div>
															</div>
														</li>
														<?}?>
														<!-- <li>
															<div class="file_box">
																<div class="file_desc">
																	<span>인사챌린지_참고2222222.ppt</span>
																	<strong>다운로드</strong>
																</div>
															</div>
														</li> -->
													</ul>
												</div>
											</div>
										<?}?>

										<?if($img_info['idx']){?>
											<div class="rew_cha_view_img">
												<div class="rew_cha_view_img_in">
													<div class="title_area">
														<strong class="title_main">[인증샷 예시] 이렇게 찍어주세요!</strong>
													</div>
													<ul>
													<?for($i=0; $i<count($img_info['idx']); $i++){
														
														$resize = $img_info['resize'][$i];

														if($resize == '0'){
															$file_path = $img_info['file_ori_path'][$i];
															$file_name = $img_info['file_ori_name'][$i];
														}else{
															$file_path = $img_info['file_ori_path'][$i];
															//$file_path = $img_info['file_path'][$i];
															$file_name = $img_info['file_name'][$i];
														}


														$file_real_name = $file_path.$file_name;
													?>
														<li>
															<div class="file_box">
																<div class="file_desc">
																	<span><img src="<?=$file_real_name?>" alt="" /></span>
																</div>
															</div>
														</li>
													<?}?>
													</ul>
												</div>
											</div>
										<?}?>
									</div>



									<?
									//챌린지 샘플인경우
									if($template =='1'){
										$temp_auth = challenges_auth();?>
										<div class="cha_view_btn">
											<div class="view_btn_type_01">
												<button class="btn_white" id="btn_back_list">이전</button>
												<!-- <button class="btn_gray" id="">숨기기 OFF</button> -->
												<?if($temp_auth == "1" && $temp == "1"){?>
													<?if($view_flag == '1'){?>
														<button class="btn_ok" id="view_hide">숨기기 ON</button>
													<?}else{?>
														<button class="btn_gray" id="view_hide">숨기기 OFF</button>
													<?}?>
													<input type="hidden" id="template_idx" value="1">
													<button class="btn_gray" id="template_delete">삭제하기</button>
													<button class="btn_black" id="template_edit">수정하기</button>
												<?}?>
												<?if($template_auth || $template_use_auth){?>
													<button class="btn_ok" id="template_btn"><span>사용하기</span></button>
												<?}?>
											</div>
										</div> 
									<?}else if($template == "0"){
									//챌린지 샘플이 아닌경우
										//인증메시지형
										if(in_array($ch_info['attend_type'], array(1))){?>
											<div class="rew_cha_view_masage">
												<div class="rew_cha_view_masage_in" id="view_masage_in">
													<div class="title_area">
														<strong class="title_main">인증 메시지</strong>
														<span class="title_point"><?=$list_cnt?></span>
														<?/*<a href="#" class="title_more"><span>더보기</span></a>*/?>
													</div>
													<div class="masage_zone">
														<?
														if ($masage_list_ymd_top3){
															$k=0;
															for($i=0; $i<count($masage_list_ymd_top3); $i++){
																$date_ymd = trim($masage_list_ymd_top3[$i]);
															?>

																<div class="masage_date">
																	<span><?=$masage_list_top3[$date_ymd][$k]['md']?> <?=$masage_list_top3[$date_ymd][$k]['yoil']?></span>
																</div>

																<?for($j=0; $j<count($masage_list_top3[$date_ymd]); $j++){

																	$chall_masage_email = $masage_list_top3[$date_ymd][$k]['email'];
																	$profile_main_img_src = profile_img_info($chall_masage_email);
																?>

																<div class="masage_area">
																	<div class="masage_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
																	<div class="masage_info">
																		<div class="masage_user">
																			<strong><?=$masage_list_top3[$date_ymd][$k]['name']?></strong>
																			<span><?=$masage_list_top3[$date_ymd][$k]['part']?></span>
																		</div>
																		<div class="masage_box">
																			<p class="masage_txt"><?=$masage_list_top3[$date_ymd][$k]['comment']?></p>
																			<span class="masage_time"><?=$masage_list_top3[$date_ymd][$k]['hi']?></span>

																			<?if($user_id!=$masage_list_top3[$date_ymd][$k]['email']){?>
																				<button class="masage_jjim<?=$work_like_list[$masage_list_top3[$date_ymd][$k]['idx']]>0?" on":""?>" id="masage_jjim" value="<?=$masage_list_top3[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																			<?}?>
																		</div>

																		<?/* if($masage_list_top3[$date_ymd][$k]['state']=="2"){?>
																			<div class="masage_warning">
																				<span>무효 후 코인 회수 처리되었습니다.</span>
																			</div>
																		<?}*/?>

																	</div>
																</div>
																<?
																$k++;
																}
															}?>

														

														<?}else{?>

														<div class="rew_none">
															등록된 인증 메시지가 없습니다.
														</div>

														<?}?>
													</div>
												</div>

												<?if ($masage_list_ymd_top3){?>
													<div class="rew_cha_more" id="masage_more">
														<button><span>more</span></button>
													</div>
												<?}?>

											</div>
										<?}?>

										<?//인증파일형
										if(in_array($ch_info['attend_type'], array(2))){?>
											<div class="rew_cha_view_result">
												<div class="rew_cha_view_result_in">
													<div class="title_area">
														<strong class="title_main">인증 파일</strong>
														<span class="title_point"><?=$chamyeo_file_cnt?></span>
														<?/*<a href="#" class="title_more"><span>더보기</span></a>*/?>
													</div>

													<?if($chall_file_info['idx']){?>

														<ul>
															<?for($i=0; $i<5; $i++){
																$chall_file_idx = $chall_file_info['idx'][$i];
																$chall_file_type = $chall_file_info['file_type'][$i];

																if($chall_file_info['resize'][$i]=='0'){
																	$chall_file_path = $chall_file_info['file_ori_path'][$i];
																	$chall_file_name = $chall_file_info['file_ori_name'][$i];
																}else{
																	$chall_file_path = $chall_file_info['file_ori_path'][$i];
																	//$chall_file_path = $chall_file_info['file_path'][$i];
																	$chall_file_name = $chall_file_info['file_name'][$i];
																}

																$file_url = $chall_file_path . $chall_file_name;

																if($chall_file_type){
																	if (@in_array($chall_file_type, $image_type_array)){?>
																		<li>
																			<button>
																				<img src="<?=$file_url?>" />
																				<span>더보기</span>
																			</button>
																		</li>
																	<?}else{?>
																		<li>
																			<button>
																				<img src="/html/images/pre/ico_list_file.png" alt="" />
																				<span>더보기</span>
																			</button>
																		</li>
																	<?}
																}
															}?>
														</ul>
													<?}else{?>
														<div class="rew_none">
															등록된 인증 파일이 없습니다.
														</div>
													<?}?>
												</div>
												<?if($chall_file_info['idx']){?>
													<div class="rew_cha_more" id="file_more">
														<button><span>more</span></button>
													</div>
												<?}?>

											</div>
										<?}?>

										<?//혼합형
										if(in_array($ch_info['attend_type'], array(3))){?>

											<div class="rew_cha_view_mix">
												<div class="rew_cha_view_mix_in" id="rew_cha_view_mix_in">
													<div class="title_area">
														<strong class="title_main">챌린지 도전완료</strong>
														<span class="title_point"><?=$chamyeo_file_cnt?></span>
														<?/*<a href="#" class="title_more" ><span>더보기</span></a>*/?>
													</div>
													<div class="mix_zone" id="mix_zone">
	
													<?if ($view_list_ymd_top3){

														$k=0;
														for($i=0; $i<count($view_list_ymd_top3); $i++){
															$date_ymd = trim($view_list_ymd_top3[$i]);
														?>
															<div class="mix_date">
																<span><?=$view_list_top3[$date_ymd][$k]['com_md']?> <?=$view_list_top3[$date_ymd][$k]['com_yoil']?></span>
															</div>

															<?for($j=0; $j<count($view_list_top3[$date_ymd]); $j++){
																$chall_mix_email = $view_list_top3[$date_ymd][$k]['email'];
																$chall_mix_idx = $view_list_top3[$date_ymd][$k]['idx'];
																$profile_main_img_src = profile_img_info($chall_mix_email);
																
																$sql = "select count(1) as cnt from work_todaywork_like where work_idx = '".$chall_mix_idx."' and state = '0' and service = 'challenge' ";
																$like_cnt = selectQuery($sql);
																$heart_cnt = $like_cnt['cnt']; 

																?>
																<div class="mix_area">
																	<div class="mix_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
																	<div class="mix_info">
																		<div class="mix_user">
																		<strong><?=$view_list_top3[$date_ymd][$k]['name']?></strong>
																			<span><?=$view_list_top3[$date_ymd][$k]['part']?></span>
																		</div>
																		<div class="mix_box">
																			<?if($view_list_top3[$date_ymd][$k]['comment']){?>
																				<p class="mix_txt"><?=textarea_replace($view_list_top3[$date_ymd][$k]['comment'])?></p>
																				<span class="mix_time"><?=$view_list_top3[$date_ymd][$k]['com_hi']?></span>
																			<?}?>
																			<?if($user_id!=$view_list_top3[$date_ymd][$k]['email']){?>
																				<button class="mix_jjim<?=$work_like_list[$view_list_top3[$date_ymd][$k]['idx']]>0?" on":""?>" id="mix_jjim" value="<?=$view_list_top3[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																			<?}?>
																		</div>
																	</div>
																	<div class="mix_imgs">
																		<? $sql = "select idx,file_ori_path, file_ori_name, state, file_real_img_name from work_challenges_file_info where state = '1' and challenges_idx = '".$chall_mix_idx."' and file_type like '%image%' order by idx desc ";
																		$query = selectAllQuery($sql); ?>
																		<div class="mix_imgs_box">
																		<?	for($z=0;$z<count($query['idx']);$z++){
																			$file_print = $query['file_ori_path'][$z].$query['file_ori_name'][$z];
																			?>
																			<img src="<?=$file_print?>" alt="" value="<?=$query['idx'][$z]?>" id="img_<?=$z?>"/>
																		<?}?>
																		</div>
																		<div class="tdw_list_file_box">
																		<?
																		$sql = "select idx, file_path, file_name, state, file_real_img_name from work_challenges_file_info where state = '1' and challenges_idx = '".$chall_mix_idx."' and file_type not like '%image%' order by idx desc ";
																		$query2 = selectAllQuery($sql);
																		for($u=0;$u<count($query2['idx']);$u++){
																			$file_real = $query2['file_path'][$u].$query2['file_name'][$u];?>
																			<button class="btn_list_file" id="btn_list_file_<?=$k?>" value="<?=$query2['idx'][$u]?>"><span><?=$query2['file_real_img_name'][$u]?></span></button>
																		<?}?>
																		</div>
																			
																		<?if($view_list_top3[$date_ymd][$k]['email']==$user_id){?>
																			<div class="mesage_btn">
																				<input type="hidden" value="<?=$view_list_top3[$date_ymd][$k]['idx']?>" id="chall_view_update">
																				<button class="mesage_corr" id="mesage_corr_3"><span>수정</span></button>
																				<button class="mesage_del" id="mesage_del_3"><span>삭제</span></button>
																			</div>
																		<?}?>
																	</div>
																	
																</div>
															<?
															$k++;
															}
														}
													}?>
													</div>
												</div>

												<?if ($view_list_ymd_top3){?>
													<div class="rew_cha_more" id="mix_more">
														<button><span>more</span></button>
													</div>
												<?}?>

											</div>
										<?}?>

										<div class="cha_view_btn">
											<div class="view_btn_type_03">
											<? 	if($ch_info['email'] == $user_id){?>
												<button class="btn_black" id="chall_edit">수정하기</button>
												<button class="btn_gray" id="chall_delete">삭제하기</button>
											<?}?>
											<button class="btn_white" id="btn_back_list">이전</button>
											<? //임시저장 아님
											if($ch_info['temp_flag'] == '0'){
												if(TODATE >= $sdate && TODATE <= $edate || $chamyeo_btn == true){
													if($attend_type == "3"){?>
														<?if($chamyeo_btn == true){?>
															<button class="btn_ok" id="btn_challenge_com" style="background:#999;border-color:#999;color:#fff">도전완료</button>
														<?}else{ 
															 if($limit_cnt > 0 && $limit_cnt == $cham_cnt){?>
																<button class="btn_ok" id="btn_magam" style="background:#999;border-color:#999;color:#fff">도전마감</button>
															<?}else {?>
																<button class="btn_ok" id="btn_challenge">도전하기</button>
															<?}?>
														<?}?>
													<?}?>
												<?}else if(TODATE < $sdate){?>
													<button class="btn_gray" id="btn_challenge">준비중....</button>
												<?}else{?>
													<button class="btn_ok" id="btn_challenge" style="background:#999;border-color:#999;color:#fff">도전실패</button>
												<?}?>
											<?}?>
											</div>
										</div>
									<?}?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>

	<div class="layer_mix" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_result_in">
			<div class="layer_result_box">
				<div class="layer_result_left">
					<div class="layer_result_search">
						<div class="layer_result_search_box">
							<input type="text" class="input_search" id="input_mix" placeholder="이름, 부서명을 검색" />
							<button id="mix_search_bt"><span>검색</span></button>
						</div>
					</div>

					<div class="layer_result_user">
						<div class="layer_result_user_in">
							<ul>
								<li>
									<button class="on" value="all">
										<div class="user_img" style="background-image:url('/html/images/pre/img_prof_default.png');"></div>
										<div class="user_name">
											<strong>전체</strong>
										</div>
										<span class="user_num">
											<span><?=$user_masage_cnt?></span>
										</span>
									</button>
								</li>

								<? for($i=0; $i<count($user_info['idx']); $i++){
									$user_list_cnt = $user_file_count[$user_info['email'][$i]];
									if($user_list_cnt > 0){
										$user_num = number_format($user_list_cnt);
										$user_num_class = "";
									}else{
										$user_num = "0";
										$user_num_class = " user_num_0";
									}

										//프로필 캐릭터,사진
										$profile_main_img_src = profile_img_info($user_info['email'][$i]);
									?>
									<li>
										<button value="<?=$user_info['email'][$i]?>">
											<?=($user_id == $user_info['email'][$i])?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"";?>
											<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
											<div class="user_name">
												<strong><?=$user_info['name'][$i]?></strong>
												<?=$user_info['part'][$i]?>
											</div>
											<span class="user_num<?=$user_num_class?>">
												<span><?=$user_num?>
											</span>
											</span>
										</button>
									</li>
								<?}?>
							</ul>
						</div>
					</div>
				</div>

				<div class="layer_result_right">
					<div class="layer_close">
						<button><span>닫기</span></button>
					</div>
					<div class="layer_result_top">
						<strong>인증파일 + 인증메시지</strong>
					</div>
					<div class="layer_result_list" style="opacity:1">
						<div class="layer_result_list_in" id="mix_zone_list">
							<div class="list_function">
								<div class="list_function_in">
									<div class="list_function_left">
										전체 <span></span><strong><?=$list_cnt?></strong>
									</div>
									<div class="list_function_right">
										<div class="list_function_sort">
											<div class="list_function_sort_in">
												<input type="hidden" id="user_email" value="all">
												<input type="hidden" id="user_date" value="all">
												<button class="btn_sort_on"><span>전체보기</span></button>
												<ul>
													<li><button id="mix_reg0" value="all"><span>전체보기</span></button></li>
													<?for($i=0; $i<count($date_mix_info['ymd']); $i++){?>
														<li><button id="mix_reg<?=($i+1)?>" value="<?=$date_mix_info['ymd'][$i]?>"><span><?=$date_mix_info['ymd'][$i]?></span></button></li>
													<?}?>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>

							<?if($chall_mix_list_ymd){?>
								
								<div class="mix_zone">
									<div class="mix_zone_in">

									<?	$k=0;
										for($i=0; $i<count($chall_mix_list_ymd); $i++){
											$date_ymd = trim($chall_mix_list_ymd[$i]);
											$view_date = $chall_mix_list[$date_ymd][$k]['date'];
											$tmp_date = explode("-",$view_date);
											if($tmp_date){
												//$tmp_month = preg_replace('/(0)(\d)/','$2', $tmp_date[1]);
												//$tmp_day = preg_replace('/(0)(\d)/','$2', $tmp_date[2]);
												$tmp_month = (int)$tmp_date[1];
												$tmp_day = (int)$tmp_date[2];

												$real_date = $tmp_date[0] . "년 " .$tmp_month."월 " .$tmp_day."일 ";
											}
										?>

											<div class="mix_date">
												<span><?=$real_date?><?=$chall_mix_list[$date_ymd][$k]['com_yoil']?></span>
											</div>

											<?for($j=0; $j<count($chall_mix_list[$date_ymd]); $j++){
												$result_idx = $chall_mix_list[$date_ymd][$k]['idx'];
												// $chall_mix_idx = $view_list_top3[$date_ymd][$k]['idx'];
												$chall_mix_email = $chall_mix_list[$date_ymd][$k]['email'];

												//프로필 캐릭터,사진
												$profile_main_img_src = profile_img_info($chall_mix_email);

												$sql = "select count(1) as cnt from work_todaywork_like where work_idx = '".$result_idx."' and state = '0' and service = 'challenge' ";
												$like_cnt = selectQuery($sql);
												$heart_cnt = $like_cnt['cnt']; 

											?>
												<div class="mix_area">
													<input type="hidden" class="imsi_count" value="<?=$heart_cnt?>">
													<div class="mix_area_in<?=($chall_mix_list[$date_ymd][$k]['state']=="2")?" chk_none":""?>" id="mix_list_chk<?=$k?>" value="<?=$chall_mix_list[$date_ymd][$k]['idx']?>">
														<div class="mix_img" style="background-image:url('<?=$profile_main_img_src?>');">
															<button class="mix_chk"><span>선택</span></button>
														</div>
														<div class="mix_info">
															<div class="mix_user">
																<strong><?=$chall_mix_list[$date_ymd][$k]['name']?></strong>
																<span><?=$chall_mix_list[$date_ymd][$k]['part']?></span>
															</div>
															<div class="mix_box">
																<?if($chall_mix_list[$date_ymd][$k]['comment']){?> <!-- date_ymd = 날짜별 1차정렬 -->
																	<p class="mix_txt"><?=textarea_replace($chall_mix_list[$date_ymd][$k]['comment'])?></p>
																	<span class="mix_time"><?=$chall_mix_list[$date_ymd][$k]['com_hi']?></span>
																	<?if($user_id!=$chall_mix_list[$date_ymd][$k]['email']){?>
																		<button class="mix_jjim<?=$work_like_list[$chall_mix_list[$date_ymd][$k]['idx']]>0?" on":""?>" id="mix_jjim" value="<?=$chall_mix_list[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																	<?}?>
																<?}?>
																<!-- <button class="chall_view_memo" value="<?=$result_idx?>" style="display:none;">memo</button> -->
																<button class="mix_memo chall_view_memo" value="<?=$result_idx?>" id=""><span>메모하기</span></button>					
															</div>
															<? if($chall_mix_list[$date_ymd][$k]['state']=="2"){?>
																<div class="mix_warning">
																	<span>무효 후 코인 회수 처리되었습니다.</span>
																</div>
															<?}?>
														</div>
														<div class="mix_imgs">
															<? 
															// $sql = "select idx,file_ori_path, file_ori_name, state, file_real_img_name from work_challenges_file_info where state = '1' and challenges_idx = '".$result_idx."'order by  case when file_type like '%image%' then idx end desc, case when file_type not like '%image%' then idx end desc ";

															$sql = "select idx,file_ori_path, file_ori_name, state, file_real_img_name from work_challenges_file_info where state = '1' and challenges_idx = '".$result_idx."' and file_type like '%image%' order by idx desc ";
															$query = selectAllQuery($sql); ?>
															<div class="mix_imgs_box">
															<?	for($z=0;$z<count($query['idx']);$z++){
																$file_print = $query['file_ori_path'][$z].$query['file_ori_name'][$z];
																?>
																<img src="<?=$file_print?>" alt="" value="<?=$query['idx'][$z]?>" id="img_<?=$z?>"/>
															<?}?>
															</div>
															<div class="tdw_list_file_box">
															<?
															$sql = "select idx, file_path, file_name, state, file_real_img_name from work_challenges_file_info where state = '1' and challenges_idx = '".$result_idx."' and file_type not like '%image%' order by idx desc ";
															$query2 = selectAllQuery($sql);
															for($u=0;$u<count($query2['idx']);$u++){
																$file_real = $query2['file_path'][$u].$query2['file_name'][$u];?>
																<button class="btn_list_file" id="btn_list_file_<?=$k?>" value="<?=$query2['idx'][$u]?>"><span><?=$query2['file_real_img_name'][$u]?></span></button>
															<?}?>
															</div>
															<?if(!$chall_mix_list[$date_ymd][$k]['comment']){?>
																<span class="mix_time"><?=$chall_mix_list[$date_ymd][$k]['com_hi']?></span> <!--파일형에서 코멘트형 포함으로 변경-->
																<?if($user_id!=$chall_mix_list[$date_ymd][$k]['email']){?>
																	<button class="mix_jjim<?=$work_like_list[$chall_mix_list[$date_ymd][$k]['idx']]>0?" on":""?>" id="mix_jjim" value="<?=$chall_mix_list[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																<?}?>
															<?}?>
															<?if($chall_mix_list[$date_ymd][$k]['email']==$user_id){?>
															<div class="mesage_btn">
																<input type="hidden" value="<?=$chall_mix_list[$date_ymd][$k]['idx']?>" id="chall_view_update">
																<button class="mesage_corr" id="mesage_corr_list"><span>수정</span></button>
																<button class="mesage_del" id="mesage_del_list"><span>삭제</span></button>
															</div>
															<?}?>
														</div>
													</div>
													<?
														$sql = "select idx,email,name,comment,regdate from work_challenges_comment where state = '1' and result_idx = '".$result_idx."' order by idx desc";
														$query = selectAllQuery($sql);
													?>
														<div class="tdw_list_memo_area" id="chall_memo_area_<?=$query['idx'][$p]?>">
															<div class="tdw_list_memo_area_in" id="memo_area_in_<?=$result_idx?>">
															<? for($p=0; $p<count($query['idx']); $p++){?>
															<div class="tdw_list_memo_desc" id="resultCo_<?=$query['idx'][$p]?>" value="<?=$query['idx'][$p]?>">
																<div class="tdw_list_memo_name"><?=$query['name'][$p]?></div>
																<input type="hidden" id="memo_id" value="<?=$query['email'][$p]?>">
																<div class="tdw_list_memo_conts">
																	<div class="tdw_list_memo_conts_txt" value="<?=$query['idx'][$p]?>">
																		<strong><?=$query['comment'][$p]?></strong>
																		<div class="tdw_list_memo_regi" id="chall_memo_<?=$query['idx'][$p]?>">
																			<textarea name="" class="textarea_regi"><?=$query['comment'][$p]?></textarea>
																			<div class="btn_regi_box">
																				<button class="btn_regi_submit" value="<?=$query['idx'][$p]?>"><span>확인</span></button>
																				<button class="btn_regi_cancel" value="<?=$query['idx'][$p]?>"><span>취소</span></button>
																			</div>
																		</div>
																	</div>
																	<em class="tdw_list_memo_conts_date"><?=$query['regdate'][$p]?>
																		<? if($query['email'][$p] == $user_id){?>
																			<button class="btn_memo_del" id="layer_memo_delete" value="<?=$query['idx'][$p]?>"><span>삭제</span></button>
																		<?}?>
																	</em>
																</div>
															</div>
															<?}?>
														</div>
													</div>
												</div>
											<?
											$k++;
											}?>
										<?}?>
									</div>
								</div>
							<?}?>

							<div class="mix_area_none">
								<strong>등록된 메시지가 없습니다.</strong>
							</div>

						</div>
					</div>
					<div class="layer_result_btns" style="display: none;">
						<div class="layer_result_btns_in">
							<div class="btns_left" id="mix_list_sel">
								<button class="btns_cancel"id="mix_sel_cancel" style="display: none;"><span>취소</span></button>
								<strong style="display: none;">5개 선택</strong>
							</div>
							<div class="btns_right">
								<button class="btns_del" id="mix_user_del" style="display:none;"><span>삭제</span></button>
								<button class="btns_down" id="btns_down" style="display:none;"><span>다운로드</span></button>

								<?if($ch_info['email'] == $user_id){?>
									<button class="btns_coin" id="mix_user_dcoin" style="display:none;"><span>무효 후 코인 회수</span></button>
									<button class="btns_re_coin" id="mix_user_rcoin" style="display:none;"><span>코인 다시 지급</span></button>
								<?}?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="layer_cha_join join_type_mix" id="layer_cha_join" style="display:none;">
	<input type="hidden" id="view_idx" value="<?=$idx?>">
		<div class="layer_deam"></div>
		<div class="layer_cha_join_in">
			<div class="layer_cha_join_box">
				<div class="layer_cha_join_title">
					<strong>챌린지 참여하기</strong>
					<span>※ 확인 메시지를 작성하고 사진 및 파일을 등록해 챌린지를 인증하세요!</span>
				</div>
				<div class="layer_cha_join_area">
					<div class="layer_cha_join_input">
						<textarea name="" placeholder="메시지를 작성하세요." id="input_type_mix"></textarea>
					</div>
					<div class="layer_cha_join_file_desc" >
					<input type="hidden" id="mix_file_name" value="<?=$chall_files_info['file_real_img_name']?>" />
					</div>
					<div class="layer_cha_join_file" id="layer_cham">
						<div class="file_box">
							<input type="file" id="file_01" class="input_file" multiple/>
							<label for="file_01" class="label_file"><span>파일첨부</span></label>
						</div>
					</div>
				</div>
				<div class="layer_result_btns">
					<div class="layer_result_btns_in">
						<div class="btns_right">
							<button class="btns_cha_cancel" id="btns_cha_cancel"><span>취소</span></button>
							<button class="btns_cha_join" id="btns_cha_join"><span>참여하기</span></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="layer_cha_join join_type_mix join_update" id="layer_cha_update_list" style="display:none;"></div>

	<div class="layer_memo" style="display:none;">
		<div class="layer_deam"></div>
		<input type="hidden" id="chall_memo_result" value="">
		<div class="layer_memo_in">
			<div class="layer_memo_box">
				<textarea name="textarea_memo" class="textarea_memo" placeholder="메모를 작성해주세요." id="textarea_memo"></textarea>
			</div>
			<div class="layer_memo_btn">
				<button class="layer_memo_cancel" id="layer_memo_cancel"><span>취소</span></button>
				<button class="layer_memo_submit" id="layer_memo_submit"><span>등록하기</span></button>
			</div>
		</div>	
	</div>

	<div class="layer_cha_img" style="display:none">
		<div class="layer_deam"></div>
		<div class="layer_cha_img_slide">
			<ul class="layer_cha_slide">
			<li id="imgList_1" style="display:none"><img src="/html/images/pre/sample_01.png" alt="img"></li>
			<li id="imgList_2" class="btn_on"><img src="/html/images/pre/sample_02.png" alt="img"></li>
			<li id="imgList_3" style="display:none"><img src="/html/images/pre/bg_0024.jpg" alt="img"></li>
			</ul>
			<div class="layer_cha_slide_btn">
				<div class="slide_btn_prev"></div>
				<div class="slide_btn_next"></div>
			</div>
		</div>
	</div>
	<div class="t_layer rew_layer_character item_prof" style="display:none;">
		<input type='hidden' id='check_profile'>
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_tit">
				<strong>캐릭터 설정</strong>
				<span>리워디에서 기본으로 제공하는 <br />캐릭터입니다.</span>
			</div>
			<div class="tl_profile">
				<ul>
					<?for($i=0; $i<count($character_img_info['idx']); $i++){

						$idx = $character_img_info['idx'][$i];
						$file_path = $character_img_info['file_path'][$i];
						$file_name = $character_img_info['file_name'][$i];
						$fp_flag = $character_img_info['fp_flag'][$i];

						$character_img_src = $file_path.$file_name;

						$posi = $i + 1;

						if($fp_flag == 1){
							$pos_cn = $pos_cn + 1;
							$pos_ht = "class='pos_ht kp$pos_cn'";
						}
					?>
						<li id="posi_<?=$posi?>" <?=$pos_ht?>>
							<div class="tl_profile_box">
								<div class="tl_profile_img" style="background-image:url(<?=$character_img_src?>);">
									<?if($fp_flag == 0 || $img_buy_arr[$idx] != ''){?>
										<button class="btn_profile<?=$member_row_info['profile_type']=='0' && $member_row_info['profile_img_idx']==$idx?" on":""?>" id="profile_img_0<?=$idx?>" value="<?=$idx?>"><span>기본 프로필 이미지1 선택</span></button>
									<?}else{?>
										<button class="btn_profile" id="item_img_0<?=$idx?>" value="<?=$idx?>"><span>기본 프로필 이미지1 선택</span></button>
									<?}?>
								</div>
							</div>
							<?if($fp_flag == 1 && $img_buy_arr[$idx] == ''){?>
								<button class="btn_prof_lock"><span>닫힘</span></button>
							<?}?>
						</li>
					<?}?>
				</ul>
			</div>
			<div class="tl_btn">
				<button id="tl_profile_bt"><span>적용</span></button>
			</div>
		</div>
	</div>

	
	<?php
		//로딩 페이지
		include $home_dir . "loading.php";
	?>
	
	<?php
		//아이템 레이어
		include $home_dir . "/layer/item_img_buy.php";
		//프로필 팝업
		include $home_dir . "/layer/pro_pop.php";
		//캐릭터 팝업
		include $home_dir . "/layer/char_pop.php";
		//좋아요 레이어
		include $home_dir . "/layer/member_jjim.php";
	?>
	<?php
	//비밀번호 재설정
		include $home_dir . "/layer/member_repass.php";
	?>
</div>
	<!-- footer start-->
	<?php
		include $home_dir . "/inc_lude/footer.php";
	?>
	<!-- footer end-->
	<script type="text/javascript" src = "/js/index_new.js"></script>								
	<script type="text/javascript">
		$(document).ready(function(){
			//URL 입력 방식 접근 금지
			if (document.referrer === '') {
				alert('========URL 형식으로의 접근을 금지합니다========');
				window.location.href = 'https://rewardy.co.kr/challenges/index.php';
			}

			var page_idx = $("#page_state").val();
		    if(page_idx == '9'){
				alert("존재하지 않는 챌린지 입니다.");
				// history.back();
				location.href = "/challenges/index.php";
			}

			window.onbeforeunload = function () { $('.rewardy_loading_01').css('display', 'block'); }
			$(window).load(function () {          //페이지가 로드 되면 로딩 화면을 없애주는 것
				$('.rewardy_loading_01').css('display', 'none');
			});
		});
		// 뒤로가기로 페이지 이동 시 로딩 스크립트 금지
		window.onpageshow = function(event) {
 	     if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
			  $('.rewardy_loading_01').css('display', 'none');
  		  }
		}
	</script>
</body>


</html>