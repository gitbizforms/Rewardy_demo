<?

include str_replace( basename(__DIR__) , "", __DIR__ ) ."inc_lude/conf.php";
include DBCON;
include FUNC;
//역량 할당코인_매일갱신

		global $companyno, $user_name, $month_first_day, $month_last_day, $max_array;

    $user_id = 'adsb12@nate.com';
    $date = '2022-12-16';

		//어제 날짜
		//$yday = date("Y-m-d", strtotime($day." -1 day"));
		//$yday = TODATE;
		//echo $yday;

		//획득역량점수
		$sql = "select sum(type1) + sum(type2) +sum(type3) + sum(type4) + sum(type5) + sum(type6) as com from work_cp_reward_list";
		$sql = $sql .= " where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".$date."'";
    echo $sql;
		$work_reward_info = selectQuery($sql);

		if($work_reward_info['com']){
			//역량점수
			$reward_com_int = $work_reward_info['com'];
		}


		//역량합계
		$max_array_sum = array_sum($max_array);

		$work_com_int = $max_array_sum;

		//50%계산
		$reward_com_jumsu = @round($work_com_int * 0.5);

		//회원 전체수 : 최고관리자 제외
		$sql = "select count(1) as cnt from work_member where state='0' and companyno='".$companyno."' and highlevel!='1'";
		$mem_auth_info = selectQuery($sql);
		if($mem_auth_info){
			$member_auth_cnt = $mem_auth_info['cnt'];
		}

		//역량 할당된 코인
		//역량 할당 코인(200000), 좋아요 할당코딩(100000)
		$sql = "select cp_coin  from work_com_rule where state='0' and companyno='".$companyno."'";
		$rule_info = selectQuery($sql);
		if($rule_info){
			$cp_coin = $rule_info['cp_coin'];
		}


		//역량 할당된 금액 / 회원전체수
		//역량 개당점수: 10000
		$reward_com_price = @round($cp_coin / $member_auth_cnt);

		//종아요 개당 : 500
		//$reward_like_price = @round($like_coin / $member_auth_cnt);


		//역량 개당 코인점수
		$work_com_gaedang = @round($reward_com_price / $reward_com_jumsu, 2);

		//개당점수
		$total_com_coin = @round($reward_com_int * $work_com_gaedang);

		//달성목표치
		$cp_per = round($reward_com_int / $reward_com_jumsu * 100);


		//$date_tmp = explode("-", TODATE);
		//$month = $date_tmp[0]."-". $date_tmp[1];

		//오늘날짜의 획득한 코인
		$sql = "select idx, cp, coin from work_com_reward where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".$date."'";
		$info = selectQuery($sql);
		if(!$info['idx']){
			$sql = "insert into work_com_reward(companyno, email, name, cp, coin, ip, workdate) values(";
			$sql = $sql .= "'".$companyno."','".$user_id."', '".$user_name."', '".$reward_com_int."', '".$total_com_coin."', '".LIP."', '".$date."')";
			$insert_idx = insertIdxQuery($sql);
		}else{

			//역량점수
			$info_cp = $info['cp'];

      echo $info_cp ."!@#". $reward_com_int;
			//업데이트 기준
			//역량점수와 추가역량점수가 다르고, 추가역역량점수가 큰경우
			if($info_cp != $reward_com_int && $reward_com_int > $info_cp){
				$plus_coin = $total_com_coin - $info['coin'];
				$sql = "update work_com_reward set plus_cp='".$reward_com_int."', plus_coin='".$plus_coin."', editdate=getdate() where idx='".$info['idx']."'";
				updateQuery($sql);
			}
		}


  ?>
