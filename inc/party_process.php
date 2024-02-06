<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
//연결된 도메인으로 분리
include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;

/*
print "<pre>";
print_r($_SERVER);
print "</pre>";
*/

//mode값이 없을경우 중지처리
if(!$_POST["mode"]){
//	echo "out";
//	exit;
}

/*
print "<pre>";
print_r($_POST);
print "</pre>";
*/
//exit;

$mode = $_POST["mode"];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
}

//보상하기
if($mode == "party_coin_reward"){
	// party idx
	$lr_work_idx = $_POST['lr_work_idx'];

	//보상 코인정보
	$lr_val = $_POST['lr_val'];

	//보상 코인
	$coin = $_POST['coin'];

	//보상메시지
	$lr_input_text = $_POST['lr_input_text'];

	$replace_input_text = replace_text($lr_input_text);

	$coin = preg_replace("/[^0-9]/", "", $coin);

	$lr_val = preg_replace("/[^0-9]/", "", $lr_val);

	// 코인 지급자 정보
	$sql = "select idx, coin, comcoin from work_member where state='0' and companyno='".$companyno."' and email = '".$user_id."' limit 1";
	$m_info = selectQuery($sql);

	if($m_info['idx']){

		if($m_info['comcoin'] < $coin){
			
			echo "none";
			exit;

		} else {

			// 코인 지급 종류
			$sql = "select idx, code, coin, memo from work_coin_reward_info where state = '0' and kind='live' and idx='".$lr_val."'";
			$reward_info = selectQuery($sql);
			
			if($reward_info['idx']){

				if($replace_input_text){
					$coin_memo = $replace_input_text;
				}else{
					$coin_memo = $reward_info['memo'];
				}

				// 코인 차감 내역
				$reward_type = "party";
				$sql = "insert into work_coininfo(state, code, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip, coin_work_idx) values('0','1110','".$reward_type."','".$companyno."','".$user_id."','".$user_name."','그룹','파티','".$coin."','".$coin_memo."','".TODATE."','".LIP."','".$lr_work_idx."')";
				$coininfo_chagam = insertIdxQuery($sql);
				if($coininfo_chagam){

					$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
					$cha_coin = updateQuery($sql);

					// 회사 공용코인 차감
					$sql = "update work_company set comcoin = comcoin - '".$coin."' where idx = '".$companyno."' and state = '0'";
					$coin_company = updateQuery($sql);

				}

				//코인 적립 내역
				$sql = "insert into work_coininfo(state, code, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip, coin_work_idx) values('0','1120','".$reward_type."','".$companyno."','그룹','파티','".$user_id."','".$user_name."','".$coin."','".$coin_memo."','".TODATE."','".LIP."','".$lr_work_idx."')";
				$coininfo_add = insertIdxQuery($sql);

				if($coininfo_add){

					$sql = "update work_todaywork_project set com_coin_pro = com_coin_pro + '".$coin."' where state='0' and companyno='".$companyno."' and idx='".$lr_work_idx."'";
					$pro_coin = updateQuery($sql);

				}

				if($coininfo_chagam && $cha_coin && $coininfo_add && $pro_coin){

					echo "complete";
					exit;

				}
			}
		}
	}
}

//보상 코인
if($mode == "member_coin_reward"){

	//work_idx
	$lr_work_idx = $_POST['lr_work_idx'];

	//보상 대상 회원번호
	$lr_uid = $_POST['lr_uid'];

	//보상 코인정보
	$lr_val = $_POST['lr_val'];

	//보상 코인
	$coin = $_POST['coin'];

	//종류 보고 : 1 공유 : 2
	$work_flag = $_POST['work_flag'];

	//보상메시지
	$lr_input_text = $_POST['lr_input_text'];

	$replace_input_text = replace_text($lr_input_text);

	$coin = preg_replace("/[^0-9]/", "", $coin);

	$lr_val = preg_replace("/[^0-9]/", "", $lr_val);

	if($coin && $lr_uid && $lr_val){

		$send_info = member_row_info($lr_uid);
		$penalty = member_penalty($send_info['email']);
		if($penalty['penalty_state']>0){
			echo "penalty";
			exit;
		}

		$sql = "select idx, coin, comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."' limit 1";
		$mb_info = selectQuery($sql);
		if($mb_info['idx']){

			//본인에게 보상 지급 안됨
			if($user_id == $send_info['email']){

				echo "id_same";
				exit;

			//공용코인이 지급할 코인보다 작을경우
			}else if($mb_info['comcoin'] < $coin){

				echo "none";
				exit;

			}else{

				$sql = "select idx, code, coin, memo from work_coin_reward_info where state='0' and kind='live' and idx='".$lr_val."'";
				$coin_reward_info = selectQuery($sql);
				if($coin_reward_info['idx']){

					//입력한 보상 메시지
					if($replace_input_text){
						$coin_info = $replace_input_text;
					}else{
						$coin_info = $coin_reward_info['memo'];
					}

					//work_coin_reward code값 확인
					//코인차감내역
					$reward_type = "party";
					$sql = "insert into work_coininfo(state, code, work_idx, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip, coin_work_idx) values('0','710','".$coin_reward_info['idx']."','".$reward_type."','".$companyno."','".$user_id."','".$user_name."','".$send_info['email']."','".$send_info['name']."','".$coin."','".$coin_info."','".TODATE."','".LIP."','".$lr_work_idx."')";
					$coininfo_chagam = insertIdxQuery($sql);
					if($coininfo_chagam){
						//코인차감
						$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
						$res_comcoin = updateQuery($sql);

						// 회사 공용코인 차감
						$sql = "update work_company set comcoin = comcoin - '".$coin."' where idx = '".$companyno."' and state = '0'";
						$coin_company = updateQuery($sql);


						//타임라인(코인 보상함)
						work_data_log('0','20', $coininfo_chagam, $user_id, $user_name, $send_info['email'], $send_info['name']);

						//역량평가지표(보상하기)
						work_cp_reward("reward", "0001", $user_id, $coininfo_chagam);


					}

					//코인정보 comment에 저장
					// $sql = "insert into work_todaywork_comment(cmt_flag,link_idx,work_idx,companyno,comment,ip,regdate)";
					// $sql = $sql .= " values(2,'".$lr_work_idx."','".$lr_work_idx."','".$companyno."','".$coin_info."'";
					// $sql = $sql .= ",'".LIP."',now())";
					// $coin_comment_idx = insertIdxQuery($sql);

					//코인 적립 내역
					
					if($work_flag == "1"){
						$lr_work_idx = $lr_work_idx + 1 ;
						$sql = "insert into work_coininfo(state, code, work_idx, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip, coin_work_idx) values('0','700','".$coin_reward_info['idx']."','".$reward_type."','".$companyno."','".$send_info['email']."','".$send_info['name']."','".$user_id."','".$user_name."','".$coin."','".$coin_info."','".TODATE."','".LIP."','".$lr_work_idx."')";
						$coininfo_idx = insertIdxQuery($sql);
					}else{
						$sql = "insert into work_coininfo(state, code, work_idx, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip, coin_work_idx) values('0','700','".$coin_reward_info['idx']."','".$reward_type."','".$companyno."','".$send_info['email']."','".$send_info['name']."','".$user_id."','".$user_name."','".$coin."','".$coin_info."','".TODATE."','".LIP."','".$lr_work_idx."')";
						$coininfo_idx = insertIdxQuery($sql);
					}

					$tokenTitle = $coin_info;
					$tokenMessage = $coin."코인을 보상 받았습니다.";
					pushToken($tokenTitle,$tokenMessage,$send_info['email'],'reward','21',$user_id,$user_name,$coininfo_idx['idx'],null,'party');

					if($coininfo_idx){
						$sql = "update work_member set coin = coin + '".$coin."' where state='0' and companyno='".$companyno."' and email='".$send_info['email']."'";
						$res_coin = updateQuery($sql);

						//타임라인(코인 보상받음)
						work_data_log('0','21', $coininfo_idx, $send_info['email'], $send_info['name'], $user_id, $user_name, $lr_work_idx, $coin);

						//역량평가지표(보상받기)
						work_cp_reward("reward", "0002", $send_info['email'], $coininfo_idx);
					}

					if($coininfo_chagam && $coininfo_idx && $res_comcoin && $res_coin){

						//코인보상으로 가산점
						work_cp_reward_plus("cp", "0007", $coininfo_idx, $send_info['email'], "reward");
						echo "complete";
						exit;
					}
				}
			}
		}
	}else{
		echo "not";
		exit;

	}

	exit;

} else if($mode == "share_coin"){

	$p_idx = $_POST['p_idx'];
	$p_edate = $_POST['p_edate'];
	$p_coin = $_POST['p_coin'];

	// 파티장 정보
	// $sql = "select email, name from work_todaywork_project where state = 0 and companyno = '".$companyno."' and idx = '".$p_idx."'";
	// $p_king = selectQuery($sql);

	// if($p_king['email']){
	// 	$pk_email = $p_king[email];
	// 	$pk_name = $p_king[name];
	// }

	$sql = "select email,name from work_todaywork_project_user where state=0 and companyno='".$companyno."' and project_idx='".$p_idx."'";
	$pm_list = selectAllQuery($sql);

	$sql = "select idx, state, companyno, email, title, name from work_todaywork_project where state = 0 and companyno='".$companyno."' and idx='".$p_idx."'";
	$pm_title = selectQuery($sql);

	// $title = "파티 종료";
	// $contents = "[".$pm_title['title']."] 파티가 종료됐습니다.";
	
	if($pm_list['email']){
		for($i=0;$i<count($pm_list['email']); $i++){
			$pm_email = $pm_list['email'][$i];
			$pm_name = $pm_list['name'][$i];

			if($p_coin != 0){

				$sql = "update work_member set coin = coin + '".$p_coin."' where state = 0 and companyno = '".$companyno."' and email = '".$pm_email."'";
				$reward_party_coin = updateQuery($sql);

			//코인 적립 내역
			$reward_type = "party_reward";
			$coin_info = "파티코인 분배";
			$sql = "insert into work_coininfo(state, code, work_idx, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip, coin_work_idx) values('0','1100', '".$p_idx."', '".$reward_type."','".$companyno."','".$pm_email."','".$pm_name."','".$pm_email."','".$pm_name."','".$p_coin."','".$coin_info."','".TODATE."','".LIP."','".$p_idx."')";
			$coininfo_idx = insertIdxQuery($sql);

			$coin_title = "파티코인 보상";
			$coin_contents = "[".$pm_title['title']."]파티가 종료되어 ".$p_coin."코인을 보상 받았습니다.";
			
			work_data_log('0','21', $coininfo_idx,$pm_email,$pm_name,'그룹','파티',$p_idx,$p_coin);
			pushToken($coin_title,$coin_contents,$pm_email,'reward','21','none','none',$p_idx,$pm_title['title'],'party');
			}
			// pushToken($title,$contents,$pm_email,'party','28',$pm_title['email'],$pm_title['name'],$p_idx);
		
		work_cp_reward("party","0002",$pm_email,$p_idx);
		}

		$sql = "update work_todaywork_project set com_coin_pro = 0, enddate = '".$p_edate."', state = 1 where state = 0 and companyno='".$companyno."' and idx = '".$p_idx."'";
		$reset_par_co = updateQuery($sql);

		$sql = "update work_todaywork set state = '9' where work_idx = '".$p_idx."' and companyno = '".$companyno."' ";
		$up = updateQuery($sql);
		
		if($reset_par_co){

			$sql = "select email,name from work_todaywork_project_user where state = 0 and companyno = '".$companyno."' and project_idx = '".$p_idx."'";
			$pm_cnt = selectAllQuery($sql);

			if($pm_cnt){
				for($i=0;$i<count($pm_cnt['email']); $i++){
					$mem_email = $pm_cnt['email'][$i];
					$mem_name = $pm_cnt['name'][$i];
					//타임라인 메모 추가
					work_data_log('0','28', $p_idx, $mem_email, $mem_name);
				}
			}
			if($coininfo_idx && $reward_party_coin){
				echo "complete";
				exit;
			}else{
				echo "ncoin";
				exit;
			}

		}

	}

}


?>
