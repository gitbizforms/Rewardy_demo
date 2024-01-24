<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	//define('DB_CHARSET', 'utf8mb4');
	include $home_dir . "/inc_lude/header.php";



	$edit_btn = false;
	if(@in_array($user_id , $edit_user_arr)){
		$edit_btn = true;
	}

	if($user_id=='sadary0@nate.com'){
		//$user_id = "eyson@bizforms.co.kr";
	}

	//챌린지 번호
	$idx = $_GET['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
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
		$sql = $sql .= " title, holiday_chk, attend_chk, sdate, edate, temp_flag from work_challenges where idx='".$idx."'";
		$ch_info = selectQuery($sql);
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
			$sql = $sql .=" where b.state='0' and a.challenges_idx='".$idx."' order by";
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
			//도전여부 체크
			$chamyeo_chk = false;
			if($attend_type == "1"){

				//챌린지 참여 완료 체크
				$chamyeo_btn = false;
				/*$chamyeo_chall_btn = false;
				$sql = "select top 1 idx, state from work_challenges_result where state='1' and challenges_idx='".$idx."' and comment!='' and email='".$user_id."' and convert(varchar(10), comment_regdate, 23)='".TODATE."' order by idx desc";
				$challenges_info_com = selectQuery($sql);
				if($challenges_info_com['idx']){
					$chamyeo_btn = true;
				}
				*/

				//도전중인 챌린지 체크
				$sql = "select idx, state from work_challenges_result where state='0' and challenges_idx='".$idx."' and comment is null and email='".$user_id."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')='".TODATE."' order by idx desc limit 1";
				$challenges_info_dom = selectQuery($sql);
				if($challenges_info_dom['idx']){
					$chamyeo_chall_btn =  true;
					$chall_list_comment_idx = $challenges_info_dom['idx'];
				}

				//챌린지 참여 기간 조건
				$where = " and DATE_FORMAT(comment_regdate, '%Y-%m-%d')>='".$sdate."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')<='".$edate."'";

				//챌린지 참여 횟수가 한번만 참여
				if ($day_type == "0"){
					$sql = "select count(idx) as cnt from work_challenges_result where state='1' and challenges_idx='".$idx."' and comment!='' and email='".$user_id."'".$where."";
					$chall_comment_info = selectQuery($sql);
					if($chall_comment_info['cnt'] >= 1){
						$chamyeo_btn = true;
					}

				//챌린지 참여 횟수 매일참여
				}else if ($day_type == "1"){
					$sql = "select idx from work_challenges_result where state='1' and challenges_idx='".$idx."' and comment!='' and email='".$user_id."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')='".TODATE."'";
					$chall_comment_info = selectQuery($sql);
					if($chall_comment_info['idx']){
						$chamyeo_btn = true;
					}

					//챌린지 참여 회수 체크
					$sql = "select count(idx) as cnt from work_challenges_result where state='1' and challenges_idx='".$idx."' and comment!='' and email='".$user_id."'".$where."";
					$chall_comment_info = selectQuery($sql);

					//챌린지 참여 최대 횟수 제한
					if ($chall_comment_info['cnt'] >= $ch_info['attend']){
						$chamyeo_btn = true;
					}
				}

				$sql = "select count(1) as cnt from work_challenges_result where state in('1','2') and comment!='' and challenges_idx='".$idx."'";
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

				$sql ="select idx, state, email, name, part, comment, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as reg, DATE_FORMAT(comment_regdate, '%Y-%m-%d') as ymd, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as ddd from work_challenges_result where state in ('1','2') and comment!='' and challenges_idx='".$idx."' order by reg desc";
				$list_info = selectAllQuery($sql);
				if($list_info['idx']){
					$list_cnt = number_format(count($list_info['idx']));
				}

				for($i=0; $i<count($list_info['idx']); $i++){
					
					$cidx = $list_info['idx'][$i];
					$cemail = $list_info['email'][$i];
					$cname = $list_info['name'][$i];
					$cpart = $list_info['part'][$i];
					$creg = $list_info['reg'][$i];
					$cymd = $list_info['ymd'][$i];
					$chis = $list_info['ddd'][$i];
					$state = $list_info['state'][$i];
					$comment = urldecode($list_info['comment'][$i]);
					//$contents = strip_tags($contents);


					//요일구함
					$reg_date = @explode("-", $cymd);
					$int_reg = date("w", strtotime($creg));
					$date_yoil =  $weeks[$int_reg];

					if($reg_date){
						$reg_date_m = (int)$reg_date[1];
						$reg_date_d = preg_replace('/(0)(\d)/','$2', $reg_date[2]);
						$date_md = $reg_date_m."월 ". $reg_date_d."일 ";
					}

					//시간 오전,오후
					if($chis){
						$chis = str_replace("  "," ",$chis);
						$chis_tmp = @explode(" ", $chis);
						if ($chis_tmp['2'] == "PM"){
							$after = "오후 ";
						}else{
							$after = "오전 ";
						}
						$ctime = @explode(":", $chis_tmp['1']);
						$chiss = $after . $ctime['0'] .":". $ctime['1'];
					}

					//최근 3건의 내역
					if($i < 3){

						//완료된건만 표기
						if($state == 1){
							$masage_list_top3[$cymd][$i]['date'] = $cymd;
							$masage_list_top3[$cymd][$i]['idx'] = $cidx;
							$masage_list_top3[$cymd][$i]['email'] = $cemail;
							$masage_list_top3[$cymd][$i]['name'] = $cname;
							$masage_list_top3[$cymd][$i]['part'] = $cpart;
							$masage_list_top3[$cymd][$i]['comment'] = $comment;
							$masage_list_top3[$cymd][$i]['yoil'] = $date_yoil;
							$masage_list_top3[$cymd][$i]['md'] = $date_md;
							$masage_list_top3[$cymd][$i]['hi'] = $chiss;
							$masage_list_top3[$cymd][$i]['state'] = $state;
							$masage_list_ymd_top3[]= $cymd;
						}
					}


					//전체내역
					$masage_list[$cymd][$i]['date'] = $cymd;
					$masage_list[$cymd][$i]['idx'] = $cidx;
					$masage_list[$cymd][$i]['email'] = $cemail;
					$masage_list[$cymd][$i]['name'] = $cname;
					$masage_list[$cymd][$i]['part'] = $cpart;
					$masage_list[$cymd][$i]['comment'] = $comment;
					$masage_list[$cymd][$i]['yoil'] = $date_yoil;
					$masage_list[$cymd][$i]['md'] = $date_md;
					$masage_list[$cymd][$i]['hi'] = $chiss;
					$masage_list[$cymd][$i]['state'] = $state;
					$masage_list_ymd[]= $cymd;
				}

				//배열키값 중복제거
				$masage_list_ymd_top3 = array_unique($masage_list_ymd_top3);

				//배열키값 리셋
				$masage_list_ymd_top3 = array_key_reset($masage_list_ymd_top3);

				//배열키값 중복제거
				$masage_list_ymd = array_unique($masage_list_ymd);

				//배열키값 리셋
				$masage_list_ymd = array_key_reset($masage_list_ymd);


				//오늘 참여한 내역 체크
				$sql = "select idx from work_challenges_result where state='1' and challenges_idx='".$idx."' and comment!='' and email='".$user_id."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')='".TODATE."'";
				$ch_masage_info = selectQuery($sql);

				//참여횟수체크
				$sql = "select count(idx) as cnt from work_challenges_result where state in('1') and challenges_idx='".$idx."' and comment!='' and email='".$user_id."'";
				$sql = $sql .=" and DATE_FORMAT(comment_regdate, '%Y-%m-%d')>='".$sdate."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')<='".$edate."'";
				$masage_list_info = selectQuery($sql);
				if($masage_list_info['idx']){
					$chamyeo_cnt = count($masage_list_info['idx']);
				}else{
					$chamyeo_cnt = 0;
				}

				//참여횟수체크
				if($chamyeo_cnt >= $attend || !$ch_masage_info['idx']){
					$chamyeo_chk = false;
				}else{
					$chamyeo_chk = true;
				}

				//챌린지 인증메시지, 날짜별
				$sql ="select DATE_FORMAT(comment_regdate, '%Y-%m-%d') as ymd from work_challenges_result where state in('1','2') and comment!='' and challenges_idx='".$idx."'";
				$sql = $sql .=" group by DATE_FORMAT(comment_regdate, '%Y-%m-%d') order by DATE_FORMAT(comment_regdate, '%Y-%m-%d') desc";
				$date_masage_info = selectAllQuery($sql);

				$auth_edit = false;
				if ($user_masage_cnt=='0'){
					$auth_edit = true;
				}
	
			//인증파일형
			}else if($attend_type == "2"){

				//챌린지 참여 완료 체크
				$chamyeo_btn = false;
				$chamyeo_chall_btn = false;
				$sql = "select idx, state from work_challenges_result where state='1' and file_path!='' and file_name!='' and challenges_idx='".$idx."' and email='".$user_id."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."' order by idx desc limit 1";
				$challenges_info_com = selectQuery($sql);
				if($challenges_info_com['idx']){
					$chamyeo_btn = true;
				}


				//도전중인 챌린지 체크
				$sql = "select idx, state from work_challenges_result where state='0' and file_path is null and file_name is null and challenges_idx='".$idx."' and email='".$user_id."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."' order by idx desc limit 1";
				$challenges_info_dom = selectQuery($sql);
				if($challenges_info_dom['idx']){
					$chamyeo_chall_btn =  true;
					$chall_list_file_idx = $challenges_info_dom['idx'];
				}

				//참여가능기간
				$where = " and DATE_FORMAT(file_regdate, '%Y-%m-%d')>='".$sdate."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')<='".$edate."'";
				//챌린지 참여 횟수가 한번만 참여
				if ($day_type == "0"){

					$sql = "select count(idx) as cnt from work_challenges_result where state='1' and challenges_idx='".$idx."' and file_path!='' and file_name!='' and email='".$user_id."'".$where."";
					$chall_file_info = selectQuery($sql);
					if($chall_file_info['cnt'] >= 1){
						$chamyeo_btn = true;
					}

				}else if ($day_type == "1"){

					//하루 한번 참여가능
					$sql = "select idx from work_challenges_result where state='1' and challenges_idx='".$idx."' and email='".$user_id."' and file_path!='' and file_name!='' and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."'";
					$chall_file_info = selectQuery($sql);
					if($chall_file_info['idx']){
						//참여완료
						$chamyeo_btn = true;
					}

					//챌린지 참여회수 체크
					$sql = "select count(idx) as cnt from work_challenges_result where state='1' and challenges_idx='".$idx."' and file_path!='' and file_name!='' and email='".$user_id."'".$where."";
					$chall_file_info = selectQuery($sql);
					if ($chall_file_info['cnt'] >= $ch_info['attend']){
						$chamyeo_btn = true;
					}
				}


				$sql = "select count(1) as cnt from work_challenges_result where state in('1','2') and file_path!='' and file_name!='' and challenges_idx='".$idx."'";
				$user_tlist_info = selectQuery($sql);
				if($user_tlist_info['cnt']){
					$user_file_cnt = number_format($user_tlist_info['cnt']);
				}else{
					$user_file_cnt = 0;
				}

				//챌린지 참여 파일첨부형
				$sql ="select email, count(idx) as cnt from work_challenges_result where state in('1','2') and file_path!='' and file_name!='' and challenges_idx='".$idx."' group by email";
				$user_list_file = selectAllQuery($sql);
				if($user_list_file['email']){
					$user_file_count = @array_combine($user_list_file['email'], $user_list_file['cnt']);
				}


				//챌린지참여 전체 인증파일 참여횟수
				$sql = "select idx, state, email, name, part, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as reg, DATE_FORMAT(file_regdate, '%Y-%m-%d') as ymd, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as ddd";
				$sql = $sql .=", resize, file_path, file_name, file_real_name, file_ori_path, file_ori_name, file_real_img_name, file_type from work_challenges_result where state in('1','2') and file_path!='' and file_name!='' and challenges_idx='".$idx."' order by reg desc";
				$chall_file_info = selectAllQuery($sql);
				if($chall_file_info['idx']){
					$chamyeo_file_cnt = number_format(count($chall_file_info['idx']));
				}else{
					$chamyeo_file_cnt = "";
				}

				//오늘 참여한 내역 체크
				$sql = "select idx from work_challenges_result where state='1' and challenges_idx='".$idx."' and file_path!='' and file_name!='' and email='".$user_id."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."'";
				$ch_file_info = selectQuery($sql);

				//참여횟수체크

				if($user_file_count[$user_id] >= $attend || !$ch_file_info['idx']){
					$chamyeo_chk = false;
				}else{
					$chamyeo_chk = true;
				}


				for($i=0; $i<count($chall_file_info['idx']); $i++){

					$cidx = $chall_file_info['idx'][$i];
					$cemail = $chall_file_info['email'][$i];
					$cname = $chall_file_info['name'][$i];
					$cpart = $chall_file_info['part'][$i];
					$creg = $chall_file_info['reg'][$i];
					$cymd = $chall_file_info['ymd'][$i];
					$chis = $chall_file_info['ddd'][$i];
					$resize = $chall_file_info['resize'][$i];
				
					$state = $chall_file_info['state'][$i];
					
					if($resize == '0'){
						$file_path = $chall_file_info['file_ori_path'][$i];
						$file_name = $chall_file_info['file_ori_name'][$i];
					}else{
						$file_path = $chall_file_info['file_ori_path'][$i];
						//$file_path = $chall_file_info['file_path'][$i];
						$file_name = $chall_file_info['file_name'][$i];
					}
					
					$file_type = $chall_file_info['file_type'][$i];
					$file_real_name = $chall_file_info['file_real_name'][$i];
					$file_real_img_name = $chall_file_info['file_real_img_name'][$i];

					$cfiles = $file_path.$file_name;

					//오리지널 이미지URL
					$ori_img_src = $chall_file_info['file_ori_path'][$i].$chall_file_info['file_ori_name'][$i];

					//요일구함
					$reg_date = @explode("-", $cymd);
					$int_reg = date("w", strtotime($creg));
					$date_yoil =  $weeks[$int_reg];

					$chall_reg = $reg_date[0]."년 ".$reg_date[1]."월 ".$reg_date[2];
					
					//시간 오전,오후
					if($chis){
						$chis = str_replace("  "," ",$chis);
						$chis_tmp = @explode(" ", $chis);
						if ($chis_tmp['2'] == "PM"){
							$after = "오후 ";
						}else{
							$after = "오전 ";
						}
						$ctime = @explode(":", $chis_tmp['1']);
						$chiss = $after . $ctime['0'] .":". $ctime['1'];
					}


					//전체내역
					$chall_file_list[$cymd][$i]['date'] = $cymd;
					$chall_file_list[$cymd][$i]['idx'] = $cidx;
					$chall_file_list[$cymd][$i]['eamil'] = $cemail;
					$chall_file_list[$cymd][$i]['name'] = $cname;
					$chall_file_list[$cymd][$i]['part'] = $cpart;
					$chall_file_list[$cymd][$i]['files'] = $cfiles;
					$chall_file_list[$cymd][$i]['hi'] = $chiss;

					//이미지형, 파일형
					if(@in_array($file_type , $image_type_array)){
						$chall_file_list[$cymd][$i]['file_real_name'] = $file_real_img_name;
						$chall_file_img[$cidx] = $ori_img_src;
					}else{
						$chall_file_list[$cymd][$i]['file_real_name'] = $file_real_name;
						$chall_file_img[$cidx] = $ori_img_src;
					}
					

					


					$chall_file_list[$cymd][$i]['file_type'] = $file_type;
					$chall_file_list[$cymd][$i]['yoil'] = $date_yoil;
					$chall_file_list[$cymd][$i]['reg'] = $chall_reg;
					$chall_file_list[$cymd][$i]['state'] = $state;
					$chall_file_list_ymd[]= $cymd;
				}

				//배열키값 중복제거
				$chall_file_list_ymd = array_unique($chall_file_list_ymd);

				//배열키값 리셋
				$chall_file_list_ymd = array_key_reset($chall_file_list_ymd);

				//챌린지 인증메시지, 날짜별
				$sql ="select DATE_FORMAT(file_regdate, '%Y-%m-%d') as ymd from work_challenges_result where state='1' and file_path!='' and file_name!='' and challenges_idx='".$idx."'";
				$sql = $sql .=" group by DATE_FORMAT(file_regdate, '%Y-%m-%d') order by DATE_FORMAT(file_regdate, '%Y-%m-%d') desc";
				$date_file_info = selectAllQuery($sql);

				$auth_edit = false;
				if ($user_file_cnt=='0'){
					$auth_edit = true;
				}
	
			//혼합형
			}else if($attend_type == "3"){

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
					$sql = "select idx, comment from work_challenges_result where state='1' and comment!='' and file_path!='' and file_name!='' and challenges_idx='".$idx."' and email='".$user_id."'".$where." order by idx desc limit 1";
					$chall_mix_info = selectQuery($sql);
					if($chall_mix_info['idx']){
						$chamyeo_btn = true;
						$chall_mix_idx = $chall_mix_info['idx'];
						$chall_comment_contents = urldecode($chall_mix_info['comment']);
					}else{

						//도전중인 인증메시지 체크
						$sql = "select idx from work_challenges_result where state='0' and challenges_idx='".$idx."' and comment is null and file_path is null and file_name is null and email='".$user_id."'".$where." order by idx desc limit 1";
						$chall_mix_info = selectQuery($sql);
						if($chall_mix_info['idx']){
							$chall_mix_idx = $chall_mix_info['idx'];
						}
					}

					$chall_list_mix_idx = $chall_mix_idx;

				}else if ($day_type == "1"){

					//하루 한번 참여가능
					$where = " and (DATE_FORMAT(comment_regdate, '%Y-%m-%d')='".TODATE."' or DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."')";
					$sql = "select idx, comment from work_challenges_result where state='1' and challenges_idx='".$idx."' and comment!='' and file_path!='' and file_name!='' and email='".$user_id."'".$where." order by idx desc limit 1";
					$chall_mix_info = selectQuery($sql);
					if($chall_mix_info['idx']){
						$chamyeo_btn = true;
						$chall_mix_idx = $chall_mix_info['idx'];
						$chall_comment_contents = urldecode($chall_mix_info['comment']);
					}else{
						//도전중인 혼합형 체크
						$sql = "select idx from work_challenges_result where state='0' and challenges_idx='".$idx."' and comment!='' and file_path!='' and file_name!='' and email='".$user_id."'".$where." order by idx desc limit 1";
						$chall_mix_info = selectQuery($sql);
						if($chall_mix_info['idx']){
							$chall_mix_idx = $chall_mix_info['idx'];
						}
					}

					$chall_list_mix_idx = $chall_mix_idx;


					//챌린지 참여회수 체크
					$sql = "select count(idx) as cnt from work_challenges_result";
					$sql = $sql .= " where state='1' and challenges_idx='".$idx."' and comment!='' and file_path!='' and file_name!='' and email='".$user_id."'".$where."";
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
				$sql ="select idx, state, email, name, part, comment, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as com_reg, DATE_FORMAT(comment_regdate, '%Y-%m-%d') as com_ymd, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as com_ddd, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as file_reg, DATE_FORMAT(file_regdate, '%Y-%m-%d') as file_ymd, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as file_ddd";
				$sql = $sql .=", resize, file_path, file_name, file_real_name, file_ori_path, file_ori_name, file_real_img_name, file_type from work_challenges_result where state in ('1','2') and comment!='' and file_path!='' and file_name!='' and challenges_idx='".$idx."' order by com_reg desc";
				$list_info = selectAllQuery($sql);
				if($list_info['idx']){
					$list_cnt = number_format(count($list_info['idx']));
				}

				//$sql = "select idx, state, email, name, part, convert(varchar(20), file_regdate, 120) as reg, convert(varchar(10), file_regdate, 23) as ymd, CONVERT(varchar(20), file_regdate, 22) as ddd";
				//$sql = $sql .=", resize, file_path, file_name, file_real_name, file_ori_path, file_ori_name, file_real_img_name, file_type from work_challenges_result where state in('1','2') and challenges_idx='".$idx."' order by reg desc";

				for($i=0; $i<count($list_info['idx']); $i++){
					
					$cidx = $list_info['idx'][$i];
					$cemail = $list_info['email'][$i];
					$cname = $list_info['name'][$i];
					$cpart = $list_info['part'][$i];
					$state = $list_info['state'][$i];

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
					if($resize == '0'){
						$file_path = $list_info['file_ori_path'][$i];
						$file_name = $list_info['file_ori_name'][$i];
					}else{
						$file_path = $list_info['file_path'][$i];
						$file_name = $list_info['file_name'][$i];
					}
					
					$file_type = $list_info['file_type'][$i];
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
					//시간 오전,오후
					if($com_his){
						$com_his = str_replace("  "," ",$com_his);
						$com_his_tmp = @explode(" ", $com_his);

						if ($com_his_tmp['2'] == "PM"){
							$after = "오후 ";
						}else{
							$after = "오전 ";
						}
						$com_ctime = @explode(":", $com_his_tmp['1']);
						$com_chiss = $after . $com_ctime['0'] .":". $com_ctime['1'];
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

					//시간 오전,오후
					if($file_his){
						$file_his = str_replace("  "," ",$file_his);
						$file_chis_tmp = @explode(" ", $file_his);

						if ($file_chis_tmp['2'] == "PM"){
							$after = "오후 ";
						}else{
							$after = "오전 ";
						}
						$file_ctime = @explode(":", $file_chis_tmp['1']);
						$file_chiss = $after . $file_ctime['0'] .":". $file_ctime['1'];
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
							$view_list_top3[$file_ymd][$i]['files'] = $cfiles;


							$view_list_top3[$com_ymd][$i]['com_yoil'] = $com_date_yoil;
							$view_list_top3[$com_ymd][$i]['com_md'] = $com_date_md;
							$view_list_top3[$com_ymd][$i]['com_hi'] = $com_chiss;

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
					$chall_mix_list[$com_ymd][$i]['com_hi'] = $com_chiss;

					$chall_mix_list[$file_ymd][$i]['file_yoil'] = $file_date_yoil;
					$chall_mix_list[$file_ymd][$i]['file_md'] = $file_date_md;
					$chall_mix_list[$file_ymd][$i]['file_hi'] = $file_chiss;

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
				$sql ="select email, count(idx) as cnt from work_challenges_result where state in('1','2') and challenges_idx='".$idx."' and file_path!='' and file_name!='' group by email";
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
				$sql ="select DATE_FORMAT(file_regdate, '%Y-%m-%d') as ymd from work_challenges_result where state='1' and file_path!='' and file_name!='' and challenges_idx='".$idx."'";
				$sql = $sql .=" group by DATE_FORMAT(file_regdate, '%Y-%m-%d') order by DATE_FORMAT(file_regdate, '%Y-%m-%d') desc";
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

	$today_d = date("Y.m.d");
	$today_m = date("Y.m.d",strtotime("+1 months"));

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
<script type="text/javascript">
		$(document).ready(function(){
			setTimeout(function(){
				tuto_position();
			},1300);

			$(window).resize(function(){
				tuto_position();
			}); 

			$(window).scroll(function(){
				tuto_position();
			}); 

			$(".tuto_phase_pause button").click(function(){
				$(".tuto_phase").hide();
			});

			$(".rew_box").removeClass("on");
			$(".rew_menu_onoff button").removeClass("on");
			setTimeout(function(){
				tuto_position();
				$(".tuto_mark_01_01").show();
				$(".tuto_pop_01_01").show();
			},1100);

		});

		function cli_next(idx){
			var next_idx = idx + 1;
			$(".tuto_mark_01_0"+idx).hide();
			$(".tuto_pop_01_0"+idx).hide();
			$(".tuto_mark_01_0"+next_idx).show();
			$(".tuto_pop_01_0"+next_idx).show();
		}

		function tuto_position(){
			$(".tuto").each(function(i){
				var i = i+1;
				var tuto = $(this);
				var tt_l = tuto.offset().left;
				var tt_t = tuto.offset().top;
				var tt_w = tuto.width() / 2;
				var tt_h = tuto.height() / 2;
				var tt_x = tt_l + tt_w;
				var tt_y = tt_t + tt_h;
				var win_w = $(window).width();
				var win_h = $(window).height();
				var win_h2 = $(window).height() / 2;
				var tt_r = win_w - 400;
				var tt_p = $(".tuto_pop_01_0"+i+"").height();
				var tt_ph = tt_p + tt_y;
				if(tt_x > tt_r){
					$(".tuto_pop_01_0"+i+"").css({
						left:"auto",
						right:70,
						opacity:1
					});
					$(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r").addClass("tuto_r");
				}else{
					$(".tuto_pop_01_0"+i+"").css({
						left:(tt_x-47),
						opacity:1
					});
					$(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r").addClass("tuto_l");
				}
				if(tt_ph > (win_h - 70)){
					$(".tuto_pop_01_0"+i+"").css({
						top:(tt_t-tt_p-24),
					});
					$(".tuto_pop_01_0"+i+"").removeClass("tuto_t tuto_b").addClass("tuto_t");
				}else{
					$(".tuto_pop_01_0"+i+"").css({
						top:(tt_y+42),
					});
					$(".tuto_pop_01_0"+i+"").removeClass("tuto_t tuto_b").addClass("tuto_b");
				}
				$(".tuto_mark_01_0"+i+"").css({
					left:tt_x,
					top:tt_y,
					opacity:1
				});
			});
		}


		function tu_end(che_le){
			var fdata = new FormData();
			var mode = "update";
			var url = '/inc/tu_process.php';
			var coin = 100;

			if(che_le == 'p_end'){
				var level = "party";
			}else if(che_le == 'c_end'){
				var level = "challenges";
			}else if(che_le == 'm_end'){
				var level = "main";
				coin = 500;
			}

			fdata.append("mode", mode);
			fdata.append("level", level);
			fdata.append("coin", coin);
			
			$.ajax({
				type: "POST",
				data: fdata,
				contentType: false,
				processData: false,
				url: url,
				success: function(data){

					console.log(data);
					if(data == "complete"){
						$(".tuto_phase").css("display","block");
					}
				}

			});

		}

		$(document).on("click","#cha_lo",function(){
			location.href = "/challenges/tu_chall.php";
		});

		
		function page_loc(sub_level){
			if(sub_level == 'party_v'){
				location.href = "/party/tu_pro_view.php";
			}else if(sub_level == 'party_pre'){
				location.href = "/party/tu_project.php";
			}else if(sub_level == 'party_end'){
				location.href = "/challenges/tu_chall.php";
			}else if(sub_level == 'challenge_v'){
				location.href = "/challenges/tu_chal_view.php?idx=1";
			}else if(sub_level == 'chal_prev'){
				location.href = "/challenges/tu_chall.php"
			}else if(sub_level == 'chal_end'){
				location.href = "/team/tu_team.php";
			}
		}

		function save_end(){
			location.href = "/team/index.php";
		}
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
<div class="tuto_phase" style="display:none;">
		<div class="tuto_phase_deam"></div>
		<div class="tuto_phase_in">
			<div class="tuto_phase_tit">
				<strong>튜토리얼로 보상받기</strong>
				<span>단계별 튜토리얼을 진행하고 코인으로 보상 받아가세요!</span>
			</div>
			<div class="tuto_phase_list">
				<div class="tuto_phase_box phase_01 tuto_clear">
					<p>1</p>
					<button>
						<dl>
							<dt>오늘업무</dt>
							<dd>
								<span>역량</span>
								<strong>1</strong>
							</dd>
							<dd>
								<span>코인</span>
								<strong>100</strong>
							</dd>
						</dl>
						<em>도전하기</em>
					</button>
				</div>
				<div class="tuto_phase_box phase_02 tuto_clear">
					<p>2</p>
					<button>
						<dl>
							<dt>좋아요</dt>
							<dd>
								<span>역량</span>
								<strong>1</strong>
							</dd>
							<dd>
								<span>코인</span>
								<strong>100</strong>
							</dd>
						</dl>
						<em>도전하기</em>
					</button>
				</div>
				<div class="tuto_phase_box phase_03 tuto_clear">
					<p>3</p>
					<button>
						<dl>
							<dt>코인 보상</dt>
							<dd>
								<span>좋아요</span>
								<strong>1</strong>
							</dd>
							<dd>
								<span>코인</span>
								<strong>100</strong>
							</dd>
						</dl>
						<em>도전하기</em>
					</button>
				</div>
				<div class="tuto_phase_box phase_06 tuto_on">
					<p>6</p>
					<button onclick="page_loc('chal_end');">
						<dl>
							<dt>메인</dt>
							<dd>
								<span>좋아요</span>
								<strong>1</strong>
							</dd>
							<dd>
								<span>코인</span>
								<strong>100</strong>
							</dd>
						</dl>
						<em>도전하기</em>
					</button>
				</div>
				<div class="tuto_phase_box phase_05 tuto_clear">
					<p>5</p>
					<button>
						<dl>
							<dt>챌린지 도전</dt>
							<dd>
								<span>역량</span>
								<strong>1</strong>
							</dd>
							<dd>
								<span>코인</span>
								<strong>100</strong>
							</dd>
						</dl>
						<em>도전하기</em>
					</button>
				</div>
				<div class="tuto_phase_box phase_04 tuto_clear">
					<p>4</p>
					<button>
						<dl>
							<dt>파티 체험</dt>
							<dd>
								<span>역량</span>
								<strong>1</strong>
							</dd>
							<dd>
								<span>코인</span>
								<strong>100</strong>
							</dd>
						</dl>
						<em>도전하기</em>
					</button>
				</div>
			</div>
			<div class="tuto_phase_pause">
				<button onclick="save_end();">다음에 이어하기</button>
			</div>
		</div>
	</div>

<div class="rew_tutorial_deam"></div>
<div class="tuto_mark tuto_mark_01_01"><button><span></span></button></div>
<div class="tuto_pop tuto_pop_01_01">
	<div class="tuto_in">
		<div class="tuto_tit">챌린지에 대해 알아보기</div>
		<div class="tuto_pager">1/1</div>
		<div class="tuto_desc">
			<p>참여가가능한 챌린지는 도전하기 버튼이 있어요.</p>
			<p>인증파일을 등록하거나, 메시지를 남기면 챌린지 도전이 완료돼요.</p>
			<p>도전 완료 즉시 코인이 지급돼요.</p>
		</div>
		<div class="tuto_btns">
			<button class="tuto_prev" onclick="page_loc('chal_prev');"><span>이전</span></button>
			<button class="tuto_next" onclick="tu_end('c_end');"><span>완료</span></button>
		</div>
	</div>
</div>
<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<!-- menu -->
				<? include $home_dir . "/inc_lude/tu_menu.php";?>
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
															<li><span>하루 1회</span></li>
															<li><span>최대 1회</span></li>

															<?if($holiday_chk){?>
															<li><span>공휴일제외</span></li>
															<?}?>
															<li><span class="view_date"><?=$today_d?> ~ <?=$today_m?></span></li>
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

										<input type="hidden" id="view_idx" value="<?=$idx?>">
										<input type="hidden" id="service">
										<input type="hidden" id="attend_type" value="<?=$ch_info['attend_type']?>">

										

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
									if($template =='1'){?>

										<div class="cha_view_btn">
											<button class="btn_white" id="btn_back_list">이전</button>

											<!-- <button class="btn_gray" id="">숨기기 OFF</button> -->
											<?if($template_auth || $ch_info['email'] == $user_id){?>

												<?if($view_flag == '1'){?>
													<button class="btn_ok" id="view_hide">숨기기 ON</button>
												<?}else{?>
													<button class="btn_gray" id="view_hide">숨기기 OFF</button>
												<?}?>
												<button class="btn_gray" id="chall_delete">삭제하기</button>
												<button class="btn_black" id="chall_edit">수정하기</button>
											<?}?>

											<?if($template_auth || $template_use_auth){?>
												<button class="btn_ok" id="template_btn"><span>사용하기</span></button>
											<?}?>
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
														<strong class="title_main">인증파일 + 인증메시지</strong>
														<span class="title_point"><?=$chamyeo_file_cnt?></span>
														<?/*<a href="#" class="title_more" ><span>더보기</span></a>*/?>
													</div>
													<div class="mix_zone">
	
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
																$profile_main_img_src = profile_img_info($chall_mix_email);

																?>
																<div class="mix_area">
																	<div class="mix_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
																	<div class="mix_info">
																		<div class="mix_user">
																		<strong><?=$view_list_top3[$date_ymd][$k]['name']?></strong>
																			<span><?=$view_list_top3[$date_ymd][$k]['part']?></span>
																		</div>
																		<div class="mix_box">
																			<p class="mix_txt"><?=$view_list_top3[$date_ymd][$k]['comment']?></p>
																			<span class="mix_time"><?=$view_list_top3[$date_ymd][$k]['com_hi']?></span>
																			
																			<?if($user_id!=$view_list_top3[$date_ymd][$k]['email'] && $user_id=='qohse@nate.com' || $user_id=='sadary0@nate.com'){?>
																				<button class="mix_jjim<?=$work_like_list[$view_list_top3[$date_ymd][$k]['idx']]>0?" on":""?>" id="mix_jjim" value="<?=$view_list_top3[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																			<?}?>
																			
																		</div>
																	</div>
																	
																	<?if($view_list_top3[$date_ymd][$k]['files']){?>
																		<div class="mix_imgs">
																			<div class="mix_imgs_box">
																				<img src="<?=$view_list_top3[$date_ymd][$k]['files']?>" alt=""/>
																			</div>
																		</div>
																	<?}?>
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
											<button class="btn_white" id="btn_back_list">이전</button>
											<button class="btn_ok on tuto tuto_01_01" id="btn_challenge">도전하기</button>
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


	
	

	<div class="layer_result" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_result_in">
			<div class="layer_result_box">
				<div class="layer_result_left">
					<div class="layer_result_search">
						<div class="layer_result_search_box">
							<input type="text" class="input_search" id="input_userfile" placeholder="이름, 부서명을 검색" />
							<button id="file_search_bt"><span>검색</span></button>
						</div>
					</div>

					<div class="layer_result_user">
						<div class="layer_result_user_in">
							<ul>

								<li>
									<button class="on" value="all">
										<div class="user_img" style="background-image:url('/html/images/pre/ico_user_all.png');"></div>
										<div class="user_name">
											<strong>전체</strong>
										</div>
										<span class="user_num<?=($user_file_cnt > 0)?"":"user_num_0"?>">
											<span><?=$user_file_cnt?></span>
										</span>
									</button>
								</li>
								
								<?for($i=0; $i<count($user_info['idx']); $i++){
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

				<div class="layer_result_right" id="file_zone_list2">
					<div class="layer_close">
						<button><span>닫기</span></button>
					</div>
					<div class="layer_result_top">
						<strong>인증 파일</strong>
					</div>
					<div class="layer_result_list" style="opacity:1">
						<div class="layer_result_list_in" id="file_zone_list">
							<div class="list_function">
								<div class="list_function_in">
									<div class="list_function_left">
										전체 <span></span><strong><?=$chamyeo_file_cnt?></strong>
									</div>
									<div class="list_function_right">
										<div class="list_function_sort">
											<div class="list_function_sort_in">
		
											
											
												<input type="hidden" id="user_email" value="all">
												<input type="hidden" id="user_date" value="all">
												<button class="btn_sort_on" id="auth_file_date" value="all"><span>전체보기</span></button>
												<ul>
													<li><button id="file_reg0" value="all"><span>전체보기</span></button></li>
													<?for($i=0; $i<count($date_file_info['ymd']); $i++){?>
														<li><button id="file_reg<?=($i+1)?>" value="<?=$date_file_info['ymd'][$i]?>"><span><?=$date_file_info['ymd'][$i]?></span></button></li>
													<?}?>
												</ul>
											</div>



										</div>
										<div class="list_function_type">
											<button class="type_list"><span>리스트형</span></button>
											<button class="type_img on"><span>이미지형</span></button>
										</div>
									</div>
								</div>
							</div>


							<?if($chall_file_list_ymd){?>
								<div class="list_area" id="list_area_auth">
									<div class="list_area_in">

									<?	$k=0;
										for($i=0; $i<count($chall_file_list_ymd); $i++){
											$date_ymd = trim($chall_file_list_ymd[$i]);
									?>

											<div class="list_box">
												<div class="list_date">
													<span><?=$chall_file_list[$date_ymd][$k]['reg']?> <?=$chall_file_list[$date_ymd][$k]['yoil']?></span>
												</div>


												<div class="list_conts type_img">
													<ul class="list_ul">

													<?for($j=0; $j<count($chall_file_list[$date_ymd]); $j++){
														
														$files_name = $chall_file_list[$date_ymd][$k]['file_real_name'];
														$files_url = $chall_file_list[$date_ymd][$k]['files'];

														$files_name_ext = array_pop(explode(".", strtolower($files_name)));
														$files_full_name = current(explode('.', $files_name)); 

													?>
														<li>
															<div class="list_thumb_wrap<?=($chall_file_list[$date_ymd][$k]['state']=="2")?" lrt_none":""?>" id="file_list_chk<?=$k?>" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>">
																<div class="list_thumb">
																	<?
																	//이미지일때
																	if (@in_array($chall_file_list[$date_ymd][$k]['file_type'], $image_type_array)){?>
																			<div class="list_thumb_img" id="list_thumb_img">
																				<img src="<?=$files_url?>" alt="" />
																			</div>
																		<?if($chall_file_list[$date_ymd][$k]['state']=="2"){?>
																			<div class="list_thumb_none">
																				<span></span>
																				<strong>취소상태</strong>
																			</div>
																			<div class="list_thumb_cover">
																				<span></span>
																				<button class="list_thumb_select"><strong>선택하기</strong></button>
																				<button class="list_thumb_preview" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>"><strong>미리보기</strong></button>
																			</div>
																		<?}else{?>
																			<div class="list_thumb_cover">
																				<span></span>
																				<button class="list_thumb_select"><strong>선택하기</strong></button>
																				<button class="list_thumb_preview" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>"><strong>미리보기</strong></button>
																			</div>
																		<?}?>
																	<?}else{?>
																		<div class="list_thumb_cover">
																			<span></span>
																			<button class="list_thumb_select"><strong>선택하기</strong></button>
																			<button class="list_thumb_preview" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>"><strong>미리보기</strong></button>
																		</div>
																	<?}?>
																</div>
																<div class="list_desc">
																	<div class="list_title">
																		<strong><?=$files_full_name?>.</strong>
																		<span><?=$files_name_ext?></span>
																		<?if($user_id!=$chall_file_list[$date_ymd][$k]['email']){?>
																			<button class="masage_jjim<?=$work_like_list[$chall_file_list[$date_ymd][$k]['idx']]>0?" on":""?>" id="file_jjim" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																		<?}?>
																	</div>
																	<div class="list_user">
																		<span><?=$chall_file_list[$date_ymd][$k]['name']?> <?=$chall_file_list[$date_ymd][$k]['part']?></span>
																	</div>
																</div>
																<div class="list_time">
																	<span><?=$chall_file_list[$date_ymd][$k]['hi']?></span>
																</div>
															</div>
														</li>
													<?
													$k++;
													}
													?>
													</ul>
												</div>
											</div>
										<?}?>
									</div>
								</div>
							<?}else{?>
								<div class="list_area_none">
									<strong>등록된 인증 파일이 없습니다.</strong>
								</div>
							<?}?>

						</div>
					</div>
					<div class="layer_result_btns">
						<div class="layer_result_btns_in">
							<div class="btns_left" id="file_list_sel" style="display:none;">
								<button class="btns_cancel" id="file_sel_cancel"><span>취소</span></button>
								<strong>5 개 선택</strong>
							</div>
							<div class="btns_right">

								<?//if($ch_info['email'] == $user_id){?>
									<button class="btns_del" id="file_user_del" style="display:none;"><span>삭제</span></button>
								<?//}?>

								<button class="btns_down"><span>다운로드</span></button>

								<?if($ch_info['email'] == $user_id){?>
									<button class="btns_coin" id="file_user_dcoin" style="display:none;"><span>무효 후 코인 회수</span></button>
									<button class="btns_re_coin" id="file_user_rcoin" style="display:none;"><span>코인 다시 지급</span></button>
								<?}?>

							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>


	<div class="layer_masage" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_result_in">
			<div class="layer_result_box">
				<div class="layer_result_left">
					<div class="layer_result_search">
						<div class="layer_result_search_box">
							<input type="text" class="input_search" id="input_masage" placeholder="이름, 부서명을 검색" />
							<button id="masage_search_bt"><span>검색r</span></button>
						</div>
					</div>

					<div class="layer_result_user">
						<div class="layer_result_user_in">
							<ul>
								<li>
									<button class="on" value="all">
										<div class="user_img">
											<img src="/html/images/pre/ico_user_all.png" alt="" />
										</div>
										<div class="user_name">
											<strong>전체</strong>
										</div>
										<span class="user_num<?=($user_masage_cnt > 0)?"":"user_num_0"?>">
											<span><?=$user_masage_cnt?></span>
										</span>
									</button>
								</li>

								<?for($i=0; $i<count($user_info['idx']); $i++){
									$user_list_cnt = $user_list_count[$user_info['email'][$i]];
									if($user_list_cnt > 0){
										$user_num = number_format($user_list_cnt);
										$user_num_class = "";
									}else{
										$user_num = "0";
										$user_num_class = " user_num_0";
									}
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
						<strong>인증 메시지</strong>
					</div>
					<div class="layer_result_list" style="opacity:1">
						<div class="layer_result_list_in" id="masage_zone_list">
							<div class="list_function">
								<div class="list_function_in">
									<div class="list_function_left">
										전체 <span></span><strong><?=$user_masage_cnt?></strong>
									</div>
									<div class="list_function_right">
										<div class="list_function_sort">
											<div class="list_function_sort_in">
												<input type="hidden" id="user_email" value="all">
												<input type="hidden" id="user_date" value="all">
												<button class="btn_sort_on" id="auth_masage_date"><span>전체보기</span></button>
												<ul>
													<li><button id="comment_reg0" value="all"><span>전체보기</span></button></li>
													<?for($i=0; $i<count($date_masage_info['ymd']); $i++){?>
														<li><button id="comment_reg<?=($i+1)?>" value="<?=$date_masage_info['ymd'][$i]?>"><span><?=$date_masage_info['ymd'][$i]?></span></button></li>
													<?}?>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>

							<?
							if($masage_list_ymd){?>
								<div class="masage_zone">
									<div class="masage_zone_in">
									<?
										$k=0;
										for($i=0; $i<count($masage_list_ymd); $i++){
												$date_ymd = trim($masage_list_ymd[$i]);
										?>
											<div class="masage_date">
												<span><?=$masage_list[$date_ymd][$k]['md']?> <?=$masage_list[$date_ymd][$k]['yoil']?></span>
											</div>

											<?
											for($j=0; $j<count($masage_list[$date_ymd]); $j++){
												
												$chall_masage_email = $masage_list[$date_ymd][$k]['email'];
												
												if ($user_id == $chall_masage_email){
													$div_class = "masage_area_in";
												}else{
													$div_class = "";
												}

												$profile_main_img_src = profile_img_info($chall_masage_email);
											?>
												<div class="masage_area">
													<div class="masage_area_in<?=($masage_list[$date_ymd][$k]['state']=="2")?" chk_none":""?>" id="masage_list_chk<?=$k?>" value="<?=$masage_list[$date_ymd][$k]['idx']?>">
														<div class="masage_img" style="background-image:url('<?=$profile_main_img_src?>');">

															<?//if($user_id == $masage_list[$date_ymd][$k]['email']){?>
																<strong class="masage_chk"><span>선택</span></strong>
															<?//}?>

														</div>
														<div class="masage_info">
															<div class="masage_user">
																<strong><?=$masage_list[$date_ymd][$k]['name']?></strong>
																<span><?=$masage_list[$date_ymd][$k]['part']?></span>
															</div>
															<div class="masage_box">
																<p class="masage_txt"><?=$masage_list[$date_ymd][$k]['comment']?></p>
																<span class="masage_time"><?=$masage_list[$date_ymd][$k]['hi']?></span>
																<?if($user_id!=$masage_list[$date_ymd][$k]['email'] && $user_id=='qohse@nate.com' || $user_id=='sadary0@nate.com'){?>
																	<button class="masage_jjim<?=$work_like_list[$masage_list[$date_ymd][$k]['idx']]>0?" on":""?>" id="masage_jjim" value="<?=$masage_list[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																<?}?>
															</div>

															<? if($masage_list[$date_ymd][$k]['state']=="2"){?>
																<div class="masage_warning">
																	<span>무효 후 코인 회수 처리되었습니다.</span>
																</div>
															<?}?>

														</div>
													</div>
												</div>
											<?
											$k++;
											}
										}
									?>
									</div>
								</div>

							<?}else{?>
								<div class="masage_area_none">
									<strong>등록된 메시지가 없습니다.</strong>
								</div>
							<?}?>

						</div>
					</div>
					<div class="layer_result_btns" style="display:none;">
						<div class="layer_result_btns_in">
							<div class="btns_left" id="masage_list_sel">
								<button class="btns_cancel" id="masage_sel_cancel"><span>취소</span></button>
								<strong>5 개 선택</strong>
							</div>
							<div class="btns_right">
								<button class="btns_del" id="masage_user_del" style="display:none;"><span>삭제</span></button>

								<?if($ch_info['email'] == $user_id){?>
									<button class="btns_coin" id="masage_user_dcoin" style="display:none;"><span>무효 후 코인 회수</span></button>
									<button class="btns_re_coin" id="masage_user_rcoin" style="display:none;"><span>코인 다시 지급</span></button>
								<?}?>
							</div>
						</div>
					</div>
				</div>

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

								<?for($i=0; $i<count($user_info['idx']); $i++){
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
												$idx = $chall_mix_list[$date_ymd][$k]['idx'];

												$chall_mix_email = $chall_mix_list[$date_ymd][$k]['email'];
												

												//프로필 캐릭터,사진
												$profile_main_img_src = profile_img_info($chall_mix_email);

											?>
												<div class="mix_area">
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
																<p class="mix_txt"><?=$chall_mix_list[$date_ymd][$k]['comment']?></p>
																<span class="mix_time"><?=$chall_mix_list[$date_ymd][$k]['file_hi']?></span>
																<?if($user_id!=$chall_mix_list[$date_ymd][$k]['email'] && $user_id=='qohse@nate.com' || $user_id=='sadary0@nate.com'){?>
																	<button class="mix_jjim<?=$work_like_list[$chall_mix_list[$date_ymd][$k]['idx']]>0?" on":""?>" id="mix_jjim" value="<?=$chall_mix_list[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																<?}?>
															</div>
															<? if($chall_mix_list[$date_ymd][$k]['state']=="2"){?>
																<div class="mix_warning">
																	<span>무효 후 코인 회수 처리되었습니다.</span>
																</div>
															<?}?>
														</div>
														<div class="mix_imgs">
															<div class="mix_imgs_box" id="mix_imgs_box_<?=$idx?>">
																<img src="<?=$chall_mix_list[$date_ymd][$k]['files']?>" alt="" />
															</div>
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
								<strong style="display: none;">5 개 선택</strong>
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


	<div class="layer_cha_join join_type_file" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_cha_join_in">
			<div class="layer_cha_join_box">
				<div class="layer_cha_join_title">
					<strong>챌린지 참여하기</strong>
					<span>사진 및 파일로 챌린지를 인증하세요!</span>
				</div>
				<div class="layer_cha_join_area">
					<div class="layer_cha_join_file">
						<div class="file_box">
							<input type="file" id="ch_file_01" class="input_file" />
							<input type="hidden" id="input_type_idx">
							<label for="ch_file_01" class="label_file"><span>파일첨부</span></label>
							<div id="ch_file_desc_01">
							<div class="file_desc">
								<span>인사챌린지_참고01.hwp</span>
								<button>삭제</button>
							</div>
							</div>
						</div>
					</div>
				</div>
				<div class="layer_result_btns">
					<div class="layer_result_btns_in">
						<div class="btns_right">
							<button class="btns_cha_cancel"><span>취소</span></button>
							<button class="btns_cha_join"><span>참여하기</span></button>
							<!-- <button class="btns_cha_join on"><span>참여하기</span></button> -->
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="layer_cha_join join_type_masage" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_cha_join_in">
			<div class="layer_cha_join_box">
				<div class="layer_cha_join_title">
					<strong>챌린지 참여하기</strong>
					<span>메시지 작성으로 챌린지를 인증하세요!</span>
				</div>
				<div class="layer_cha_join_area">
					<div class="layer_cha_join_input">
						<textarea name="" id="input_type_masage" placeholder="메시지를 작성하세요."></textarea>
						<input type="hidden" id="input_type_idx">
					</div>
				</div>
				<div class="layer_result_btns">
					<div class="layer_result_btns_in">
						<div class="btns_right">
							<button class="btns_cha_cancel"><span>취소</span></button>
							<button class="btns_cha_join"><span>참여하기</span></button>
							<!-- <button class="btns_cha_join on"><span>참여하기</span></button> -->
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="layer_cha_join join_type_mix" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_cha_join_in">
			<div class="layer_cha_join_box">
				<div class="layer_cha_join_title">
					<strong>챌린지 참여하기</strong>
					<span>인증 메시지를 작성하고 사진 및 파일을 등록해 챌린지를 인증하세요!</span>
				</div>
				<div class="layer_cha_join_area">
					<div class="layer_cha_join_input">
						<textarea name="" placeholder="메시지를 작성하세요." id="input_type_mix"><?=strip_tags($chall_comment_contents)?></textarea>
						<input type="hidden" id="input_type_idx">
					</div>
					<div class="layer_cha_join_file">
						<div class="file_box">
							<input type="hidden" id="mix_file_name" value="<?=$chall_files_info['file_real_img_name']?>" />
							<input type="file" id="mix_file_01" class="input_file" />
							<label for="mix_file_01" class="label_file"><span>파일첨부</span></label>
							<div id="mix_file_desc_01">
								<div class="file_desc"<?=$chall_files_info['file_real_img_name']?' style="display: block;"':''?>>
									<span><?=$chall_files_info['file_real_img_name']?></span>
									<button <?=$chall_files_info['file_real_img_name']?' id="mix_file_del_01"':''?>>삭제</button>
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="layer_result_btns">
					<div class="layer_result_btns_in">
						<div class="btns_right">
							<button class="btns_cha_cancel"><span>취소</span></button>
							<button class="btns_cha_join"><span>참여하기</span></button>
							<!-- <button class="btns_cha_join on"><span>참여하기</span></button> -->
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="layer_cha_image" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_cha_image_in">
			<div class="layer_cha_image_box">
				<div class="layer_cha_image_box_in">
					<img src="" alt="" id="layer_cha_img"/>
				</div>
			</div>
		</div>
	</div>

	<?php
		//좋아요 레이어
		include $home_dir . "/layer/member_jjim.php";
	?>


</div>
	<!-- footer start-->
	<?php
		include $home_dir . "/inc_lude/footer.php";
	?>
	<!-- footer end-->
</body>
</html>