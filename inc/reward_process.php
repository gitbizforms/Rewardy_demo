<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
//연결된 도메인으로 분리
include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;



$mode = $_POST["mode"];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
}


//보상하기
if($mode == "coin_reward"){
	//보상받을 회원번호
	$user_chk_val = $_POST['user_chk_val'];

	//보상사유
	$input_reason = $_POST['input_reason'];

	//보상코인
	$input_coin = $_POST['input_coin'];
	$input_coin = preg_replace("/[^0-9]/", "", $input_coin);
	$user_chk_val = str_replace(" ","", $user_chk_val);

	$timestamp = strtotime("-1 minutes");
	$timezone = date("Y-m-d H:i:s", $timestamp);

	if($user_chk_val && $input_coin && $input_reason){
		//$user_chk_val_ex = @explode(",", $user_chk_val);
		$sql = "select idx,email from work_member where idx in(".$user_chk_val.") order by idx desc";
		$query = selectAllQuery($sql);
		
		for($i=0;$i<count($query['idx']);$i++){
			$sql = "select idx,email,code,regdate,coin,reward_user from work_coininfo where email = '".$query['email'][$i]."' and code = '700' and reward_user = '".$user_id."' and regdate > '".$timezone."' order by idx desc";
			$db_chk = selectQuery($sql);
			if($db_chk['idx']){
				echo "duplication";
				exit;
			}
		}

		//공용코인
		$sql = "select idx, coin, comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."' order by idx desc limit 1";
		$common_info = selectQuery($sql);
		
		$sql = "select idx, email, name from work_member where state='0' and idx in(".$user_chk_val.")";
		$sql = $sql .= " order by ";
		$sql = $sql .= " CASE WHEN email='".$user_id."' THEN email END DESC,";
		$sql = $sql .= " CASE WHEN live_1_regdate is null THEN name END DESC";
		$member_reward_info = selectAllQuery($sql);

		for($i=0; $i<count($member_reward_info['idx']); $i++){
			$reward_email = $member_reward_info['email'][$i];
			$penalty = member_penalty($reward_email);
			if($penalty['penalty_state']>0){
				echo "penalty";
				exit;
			}
		}

		//본인이 포함되었을경우
		if(@in_array($user_id, $member_reward_info['email'])){
			echo "user_me";
			exit;
		}

		if($common_info['idx']){
			$comcoin = $common_info['comcoin'];

			//지급할 공용코인 = 보상코인 * 보상받을 사람인원 수
			$out_coin = $input_coin * count($member_reward_info['idx']);

			//보유한 공용코인이 지급할 코인보다 작은경우
			if($out_coin > $comcoin ){
				echo "none";
				exit;
			}
		
			//내용에 홑따옴표 포함되었을때 처리
			$coin_info = replace_text($input_reason);
			for($i=0; $i<count($member_reward_info['idx']); $i++){

				$reward_idx = $member_reward_info['idx'][$i];
				$reward_email = $member_reward_info['email'][$i];
				$reward_name = $member_reward_info['name'][$i];


				//코인 차감 내역 저장
				$reward_type = "reward";
				$sql = "insert into work_coininfo(code, work_idx, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('710','".$reward_idx."','".$reward_type."','".$companyno."','".$user_id."','".$user_name."','".$reward_email."','".$reward_name."','".$input_coin."','".$coin_info."','".TODATE."','".LIP."')";
				$coininfo_chagam = insertIdxQuery($sql);

				//코인차감
				if($coininfo_chagam){
					$sql = "update work_member set comcoin = comcoin - '".$input_coin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
					$res_comcoin = updateQuery($sql);

					//타임라인(코인 보상함)
					work_data_log('0','20', $coininfo_chagam, $user_id, $user_name, $reward_email, $reward_name);
				}


				//코인 적립 내역
				$sql = "insert into work_coininfo(code, work_idx, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('700','".$reward_idx."','".$reward_type."','".$companyno."','".$reward_email."','".$reward_name."','".$user_id."','".$user_name."','".$input_coin."','".$coin_info."','".TODATE."','".LIP."')";
				$coininfo_idx = insertIdxQuery($sql);
				if($coininfo_idx){
					$sql = "update work_member set coin = coin + '".$input_coin."' where state='0' and companyno='".$companyno."' and email='".$reward_email."'";
					$res_coin = updateQuery($sql);
					$title_reward = $user_name."님이 ".$input_coin."코인을 보냈어요"; 

					//타임라인(코인 보상받음)
					work_data_coin_log('0','21', $coininfo_idx, $reward_email, $reward_name, $user_id, $user_name);
					pushToken($title_reward,$coin_info,$reward_email,'reward','21',$user_id,$user_name,$reward_idx,null,'reward');
				}
			}

			if($coininfo_chagam && $coininfo_idx && $res_comcoin && $res_coin){
				echo "complete";
				exit;
			}

		}else{
			echo "comcoin_not";
			exit;
		}

		exit;
	}

	exit;
}

if($mode == "coin_reward_chk"){
	//보상받을 회원번호
	$user_chk_val = $_POST['user_chk_val'];
	//보상사유
	$input_reason = $_POST['input_reason'];
	//보상코인
	$input_coin = $_POST['input_coin'];
	$input_coin = preg_replace("/[^0-9]/", "", $input_coin);
	$user_chk_val = str_replace(" ","", $user_chk_val);

	if($user_chk_val && $input_coin && $input_reason){
		//$user_chk_val_ex = @explode(",", $user_chk_val);

		//공용코인
		$sql = "select idx, coin, comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."' order by idx desc limit 1";
		$common_info = selectQuery($sql);
		
		$sql = "select idx, email, name from work_member where state='0' and idx in(".$user_chk_val.")";
		$sql = $sql .= " order by ";
		$sql = $sql .= " CASE WHEN email='".$user_id."' THEN email END DESC,";
		$sql = $sql .= " CASE WHEN live_1_regdate is null THEN name END DESC";
		$member_reward_info = selectAllQuery($sql);

		//본인이 포함되었을경우

		if($common_info['idx']){
			$comcoin = $common_info['comcoin'];

			//지급할 공용코인 = 보상코인 * 보상받을 사람인원 수
			$out_coin = $input_coin * count($member_reward_info['idx']);

			//보유한 공용코인이 지급할 코인보다 작은경우
		
			//내용에 홑따옴표 포함되었을때 처리
			$coin_info = replace_text($input_reason);
			for($i=0; $i<count($member_reward_info['idx']); $i++){

				$reward_idx = $member_reward_info['idx'][$i];
				$reward_email = $member_reward_info['email'][$i];
				$reward_name = $member_reward_info['name'][$i];

				//코인 차감 내역 저장
				$reward_type = "reward";
				$sql = "insert into work_coininfo(code, work_idx, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('710','".$reward_idx."','".$reward_type."','".$companyno."','".$user_id."','".$user_name."','".$reward_email."','".$reward_name."','".$input_coin."','".$coin_info."','".TODATE."','".LIP."')";
				$coininfo_chagam = insertIdxQuery($sql);

				//코인차감
				if($coininfo_chagam){
					$sql = "update work_member set comcoin = comcoin - '".$input_coin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
					$res_comcoin = updateQuery($sql);

					//타임라인(코인 보상함)
					work_data_log('0','20', $coininfo_chagam, $user_id, $user_name, $reward_email, $reward_name);
				}

				//코인 적립 내역
				$sql = "insert into work_coininfo(code, work_idx, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('700','".$reward_idx."','".$reward_type."','".$companyno."','".$reward_email."','".$reward_name."','".$user_id."','".$user_name."','".$input_coin."','".$coin_info."','".TODATE."','".LIP."')";
				$coininfo_idx = insertIdxQuery($sql);
				if($coininfo_idx){
					$sql = "update work_member set coin = coin + '".$input_coin."' where state='0' and companyno='".$companyno."' and email='".$reward_email."'";
					$res_coin = updateQuery($sql);

					//타임라인(코인 보상받음)
					work_data_coin_log('0','21', $coininfo_idx, $reward_email, $reward_name, $user_id, $user_name);
				}
			}
			if($coininfo_chagam && $coininfo_idx && $res_comcoin && $res_coin){
				echo "complete";
				exit;
			}
		}else{
			echo "comcoin_not";
			exit;
		}
		exit;
	}
	exit;
}



//보상리스트
if($mode == "reward_list"){?>
	<div class="rew_ard_list">
		<div class="rew_ard_list_in">
			<div class="rew_ard_list_tab">
				<span class="rew_ard_tab_tit">상세내용</span>
				<span class="rew_ard_tab_coin">코인</span>
				<span class="rew_ard_tab_date">일시</span>
				<span class="rew_ard_tab_kind">유형</span>
				<span class="rew_ard_tab_type">구분</span>
			</div>
			<div class="rew_ard_list_desc">
				<ul>
				<?
				$sdate = $_POST['sdate'];
				$edate = $_POST['edate'];
				$nday = $_POST['nday'];
				$string = $_POST['string'];
				$type = $_POST['type'];

				//페이지
				$p = $_POST['p']?$_POST['p']:$_GET['p'];
				if (!$p){
					$p = 1;
				}

				if($string){
					parse_str($string, $output);
					$sdate= $output['sdate'];
					$edate= $output['edate'];
					$nday= $output['nday'];
					$type= $output['type'];
				}

				$pagingsize = 5;					//페이징 사이즈
				$pagesize = 20;						//페이지 출력갯수
				$startnum = 0;						//페이지 시작번호
				$endnum = $p * $pagesize;			//페이지 끝번호

				//시작번호
				if ($p == 1){
					$startnum = 0;
				}else{
					$startnum = ($p - 1) * $pagesize ;
				}

				//조건절
				//if($user_id=='sadary0@nate.com'){
				//	$where = " where state='0' and companyno='".$companyno."'";
				//}else{
					$where = " where state!='9' and email='".$user_id."' and companyno='".$companyno."'";
				//}
				if($sdate && $edate && $nday){
					$where = $where .= " and workdate between '".$sdate."' and '".$edate."'";
				}

				//전체보기,보상,차감
				if($type == "add"){
					$where = $where .= " and code in('500','700')";
				}else if($type == "out"){
					$where = $where .= " and code in ('510','520', '710','810')";
				}

				//전체 카운터수
				$sql = "select count(1) as cnt from work_coininfo ".$where."";
				$workcoin_info = selectQuery($sql);
				if($workcoin_info['cnt']){
					$total_count = $workcoin_info['cnt'];
				}

				//보상내역리스트
				$sql = "select idx, code, coin_auto, work_idx, reward_user, reward_type ,reward_name, coin, coin_type, memo";
				$sql = $sql .", DATE_FORMAT(regdate, '%Y-%m-%d') as ymd, DATE_FORMAT(regdate, '%H:%i:%s') as his";
				$sql = $sql .", DATE_FORMAT(regdate, '%Y-%m-%d %H:%i:%s') as regdate from work_coininfo";
				$sql = $sql .= " ".$where;
				$sql = $sql .= " order by idx desc";
				$sql = $sql .= " limit ".$startnum. ",".$pagesize;
				$reward_info = selectAllQuery($sql);


				//보상코인정보
					$sql = "select idx, kind, code, memo from work_coin_reward where state='0' and code is not null order by idx desc";
					$coin_info = selectAllQuery($sql);
					if($coin_info['idx']){
					for($i=0; $i<count($coin_info['idx']); $i++){

						$kind = $coin_info['kind'][$i];
						$code = $coin_info['code'][$i];
						$memo = $coin_info['memo'][$i];
						$reward_list[$code]['memo'] = $memo;
						$reward_list[$code]['code'] = $kind;
					}
				}

				if(count($reward_info['idx'])!=0){
				for($i=0; $i<count($reward_info['idx']); $i++){							
					$idx = $reward_info['idx'][$i];
					$code = $reward_info['code'][$i];
					$memo = $reward_info['memo'][$i];
					$coin = $reward_info['coin'][$i];
					$coin_type = $reward_info['coin_type'][$i];
					$work_idx = $reward_info['work_idx'][$i];
					$ymd = $reward_info['ymd'][$i];
					$his = $reward_info['his'][$i];

					$reg_date = $ymd . " ". $his;

					$reward_user = $reward_info['reward_user'][$i];
					$reward_name = $reward_info['reward_name'][$i];
					$reward_type = $reward_info['reward_type'][$i];

					if($reward_type=="challenge"){
						$reward_type_name = "챌린지";
					}elseif($reward_type=="party"){
						$reward_type_name = "파티";
					}elseif($reward_type=="reward"){
						$reward_type_name = "리워드";
					}else if($reward_type=="party_reward"){
						$reward_type_name = "파티보상";
					}else if($reward_type=="live"){
						$reward_type_name = "라이브";
					}else if($reward_type=="account"){
						$reward_type_name = "지급";
					}else{
						$reward_type_name = "공용코인";
					}
					$chall_title = $chall_info_list[$work_idx];

			
			//공용코인
			if($coin_type == '1'){
				if ($reward_list[$code]['code'] == "plus"){
					$span_class = "coin_plus";
					$span_ho = "+";
					$resard_list_memo = "보상";
					$reward_desc = $reward_list[$code]['memo'];
				}else if($reward_list[$code]['code'] == "minus"){
					$span_class = "coin_minus";
					$span_ho = "-";
					$resard_list_memo = "차감";
					$reward_desc = $reward_list[$code]['memo'];
				}
				$coin_type_text = "공용코인";

			//일반코인
			}else{

				if($reward_list[$code] && !$reward_user){
					$reward_desc = "-";

				}else if ($reward_list[$code]['code']== "plus"){
					$span_class = "coin_plus";
					$span_ho = "+";
					$resard_list_memo = "보상";
					$reward_desc = $resard_list_memo ."(". $reward_name.")";

				}else if($reward_list[$code]['code'] == "minus"){
					$span_class = "coin_minus";
					$span_ho = "-";
					$resard_list_memo = "차감";
					$reward_desc = $resard_list_memo ."(". $reward_name.")";

				}
				$coin_type_text = "";
			}



		?>
				<li>
					<span class="rew_ard_desc_tit"><a href="#"><?=$memo?><?=$chall_title?"(".$chall_title.")":""?><?=$coin_type_text?" (".$coin_type_text.")":""?></a></span>
					<span class="rew_ard_desc_coin <?=$span_class?>"><?=$span_ho?><?=number_format($coin)?></span>
					<span class="rew_ard_desc_date"><?=$reg_date?></span>
					<span class="rew_ard_desc_kind"><?=$reward_type_name?></span>
					<span class="rew_ard_desc_type"><?=$reward_desc?></span>
				</li>
				<?}
				}else{?>
					<li><span class="rew_ard_desc">조회된 결과가 없습니다.</span></li>
				<?}?>
					</ul>
				</div>
			</div>
		</div>


		<?if($reward_info['idx']){?>
			<div class="rew_ard_paging">
				<div class="rew_ard_paging_in">
					<?
						//페이징사이즈, 전체카운터, 페이지출력갯수
						echo pageing($pagingsize, $total_count, $pagesize, $string);
					?>
				</div>
			</div>
		<?}?>
	<?php

	exit;
}


//출금신청 하기
if($mode == "withdraw_add"){

	//신청금액의 5%는 회사 수익으로 처리
	//출금신청코인
	$coin = $_POST['coin'];
	// $bank_name = $_POST['bank_name'];
	// $bank_num = $_POST['bank_num'];
	// $bank_user = $_POST['bank_user'];
	
	// $coin = preg_replace("/[^0-9]/", "", $coin);
	// $bank = preg_replace("/[^0-9]/", "", $bank);
	// $bank_num = preg_replace("/[^0-9]/", "", $bank_num);

	$log_folder = "reward";

	//회원정보 체크
	$mebmer_info = member_row_info($user_id);

	// $sql = "select idx, email, coin, comcoin from work_member where email = '".$user_id."' and state ='0' and companyno = '".$companyno."' ";
	// $member_info = selectQuery($sql);
	if($mebmer_info['idx']){

		//보유 코인
		$memcoin = $mebmer_info['coin'];
		$memcoin = preg_replace("/[^0-9]/", "", $memcoin);

		//공용코인부족
		if($memcoin == 0){
			echo "not";
			exit;	
		}

		//보유한 코인보다 많이 신청했을 경우
		if($coin > $memcoin){
			echo "over";
			exit;
		}

		$log = "출금신청시작";
		write_log_dir($log, $log_folder);

		//은행명
		// $account_name = "";
		// $sql = "select idx, name from work_bank where state='0' and idx='".$bank_name."'";
		// $bank_info = selectQuery($sql);
		// if($bank_info['idx']){
		// 	$account_name = $bank_info['name'];
		// }

		$memo = "출금신청";

		//수수료계산 + 리워디 정책변화로 수수료 비활성화 (20240109)
		// $commission = $coin * 0.05;
		$commission = 0;

		//출금신청한 금액에서 수수료(5%) 차감
		$acc_coin = $coin - $commission;

		$log = "출금신청 아이디 : ".$user_id;
		write_log_dir($log, $log_folder);

		$member_row_info = member_row_info($user_id);
		$log = "보유한 코인 : ".$member_row_info['coin'];
		write_log_dir($log, $log_folder);

		//출금신청내역
		$sql = "select idx from work_account_info where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
		$account_info = selectQuery($sql);

		$log = "출금신청 신청금액 : ".$coin;
		write_log_dir($log, $log_folder);

		$amount = $acc_coin;

		// $sql = "insert into work_account_info(companyno, email, name, part, bank_name, bank_num, coin, commission, amount, mem_coin, ip, memo, workdate) values(";
		// $sql = $sql .= "'".$companyno."', '".$user_id."', '".$user_name."', '".$part_name."', '".$account_name."', '".$bank_num."', '".$coin."', '".$commission."', '".$amount."', '".$member_row_info['coin']."', '".LIP."', '".$memo."', '".TODATE."')";

		$sql = "insert into work_account_info(companyno, email, name, part, coin, commission, amount, mem_coin, ip, memo, workdate) values(";
		$sql = $sql .= "'".$companyno."', '".$user_id."', '".$user_name."', '".$part_name."', '".$coin."', '".$commission."', '".$amount."', '".$member_row_info['coin']."', '".LIP."', '".$memo."', '".TODATE."')";
		$insert_idx = insertIdxQuery($sql);
		if($insert_idx){
			//------------출금신청 수수료 차감 (비활성화)--------------//
			// $sql = "update work_member set coin = coin - '".$commission."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
			// $up = updateQuery($sql);
			// if($up){

			// 	$log = "출금수수료 :".$commission;
			// 	write_log_dir($log, $log_folder);

			// 	$memo_info = "출금신청 수수료";
			// 	$code = '830';
			// 	$sql = "insert into work_coininfo set code='".$code."', email='".$user_id."', name='".$user_name."', coin='".$commission."', reward_type='account', companyno='".$companyno."', memo='".$memo_info."', workdate='".TODATE."'";
			// 	insertQuery($sql);
			// }
			//------------출금신청 수수료 차감 (비활성화)--------------//	

			$sql = "update work_member set coin = coin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
			$up = updateQuery($sql);
			if($up){

				$member_row_info = member_row_info($user_id);
				$log = "출금신청후 남은코인 : ".$member_row_info['coin'];
				write_log_dir($log, $log_folder);

				//출금신청
				$reward_type = "account";
				$coin_type = "0";
				$code = "810";
				$sql = "insert into work_coininfo(code, work_idx, reward_type, coin_type, companyno, email, name, coin, memo, workdate, ip) values(";
				$sql = $sql .= "'".$code."','".$insert_idx."','".$reward_type."','".$coin_type."','".$companyno."','".$user_id."','".$user_name."','".$coin."','".$memo."','".TODATE."','".LIP."')";
				$coininfo_idx = insertIdxQuery($sql);
				
				if($coininfo_idx){
					echo "complete";
					exit;
				}

			}
		}
	}
	exit;
}

?>

