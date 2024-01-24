<?php

	//회원정보 코인 업데이트 처리
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "inc_lude/conf_mysqli.php";
	include DBCON_MYSQLI;
	include FUNC_MYSQLI;

	//하루 전날
	$yesterday = date('Y-m-d', strtotime(" -1 day"));
	$yester_ex = explode("-",$yesterday);

	$today_date = mktime(0,0,0,date(m),1,date(Y));
	$today_date = date("Y-m-d",$today_date);
	$today_ex = explode("-",$today_date);

	if($today_ex && $yester_ex){

		//오늘날짜 년,월,일
		$to_year = $today_ex[0];
		$to_month = $today_ex[1];
		$to_day = $today_ex[2];

		//어제날짜 년,월,일
		$yester_year = $yester_ex[0];
		$yester_month = $yester_ex[1];
		$yester_day = $yester_ex[2];

		//숫자 0을 제외함(현재날짜의 일에서 0을 삭제)
		$to_day_preg = preg_replace('/(0)(\d)/','$2', $to_day);

		//이전달의 날짜 월1일 ~ 마지막 일
		$curYear = (int)$yester_year;
		$curMonth = (int)$yester_month;
		$pre_month_first_day = date("Y-m-d", mktime(0, 0, 0, $curMonth , 1, $curYear));
		$pre_month_last_day = date("Y-m-d", mktime(0, 0, 0, $curMonth+1 , 0, $curYear));

		//자동적립
		$coin_auto = '1';

		//이전달 
		$info = $curMonth . "월";


		//현재 날짜 1일 때만 실행함
		if($to_day_preg == 1){


			$today_date = TODATE;
			//회원정보 조회(정상적인 회원/관리권한이 아닌 일반 권한 모두 체크)
			$sql = "select idx, companyno, email, name from work_member where state='0' and highlevel!='1'";
			$member_info = selectAllQuery($sql);
			for($i=0; $i<count($member_info['idx']); $i++){

				$companyno = $member_info['companyno'][$i];
				$email = $member_info['email'][$i];
				$name = $member_info['name'][$i];

				$sql = "select sum(coin) + sum(plus_coin) as coin, email from work_com_reward where state='0' and companyno='".$companyno."'";
				$sql = $sql .= " and workdate between '".$pre_month_first_day."' and '".$pre_month_last_day."' and email ='".$email."'";
				$sql = $sql .= " group by email";
				$pre_coin_info = selectQuery($sql);


				$sql = "select sum(coin) + sum(plus_coin) as coin, email from work_like_reward where state='0' and companyno='".$companyno."'";
				$sql = $sql .= " and workdate between '".$pre_month_first_day."' and '".$pre_month_last_day."' and email ='".$email."'";
				$sql = $sql .= " group by email";
				$pre_like_info = selectQuery($sql);


				//코인이 있는 경우(0보다 큰경우)
				if($pre_coin_info['coin'] > 0){

					$input_coin = $pre_coin_info['coin'];

					//코인적립내역
					$coin_info = $info. " 역량 코인 보상";
					$code = "900";

					//달에 적립된 코인내역
					$sql = "select idx from work_coininfo where state='0' and code='".$code."' and coin_auto='".$coin_auto."' and email='".$email."' and workdate='".$today_date."'";
					$coininfo = selectQuery($sql);
					if(!$coininfo['idx']){
						$sql = "update work_member set coin = coin + '".$input_coin."' where state='0' and companyno='".$companyno."' and email='".$email."'";
						$up = updateQuery($sql);
						if($up){
							$sql = "insert into work_coininfo(state, code, coin_auto, reward_type, companyno, email, name, coin, memo, workdate, ip)";
							$sql = $sql .= " values('0', '".$code."', '".$coin_auto."', 'reward', '".$companyno."', '".$email."', '".$name."', '".$input_coin."','".$coin_info."','".TODATE."','".LIP."')";
							$insert_idx = insertIdxQuery($sql);
							if($insert_idx){
								work_data_coin_log('0','24', $insert_idx, $email, $name);
							}
						}
					}
				}


				//코인이 있는 경우(0보다 큰경우)
				if($pre_like_info['coin'] > 0){

					$input_coin = $pre_like_info['coin'];

					//코인적립내역
					$coin_info = $info. " 좋아요 코인 보상";
					$code = "1000";
					//달에 적립된 코인내역
					$sql = "select idx from work_coininfo where state='0' and code='".$code."' and coin_auto='".$coin_auto."' and email='".$email."' and workdate='".$today_date."'";
					$coininfo = selectQuery($sql);
					if(!$coininfo['idx']){
						$sql = "update work_member set coin = coin + '".$input_coin."' where state='0' and companyno='".$companyno."' and email='".$email."'";
						$up = updateQuery($sql);
						if($up){
							$sql = "insert into work_coininfo(state, code, coin_auto, reward_type, companyno, email, name, coin, memo, workdate, ip)";
							$sql = $sql .= " values('0', '".$code."', '".$coin_auto."', 'reward', '".$companyno."', '".$email."', '".$name."', '".$input_coin."','".$coin_info."','".TODATE."','".LIP."')";
							$insert_idx = insertIdxQuery($sql);
							if($insert_idx){
								work_data_coin_log('0','25', $insert_idx, $email, $name);
							}
						}
					}
				}
			}
		}
	}
?>