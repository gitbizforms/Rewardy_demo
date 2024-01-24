<?php
//챌린지카테고리
		$sql = "select idx, name from work_category where state='0' order by rank asc";
		$cate_info = selectAllQuery($sql);
		for($i=0; $i<count($cate_info['idx']); $i++){
			$chall_category[$cate_info['idx'][$i]] = $cate_info['name'][$i];
		}

		//챌린지 오픈 중 참여 완료한 챌린지 확인, 전체코인/참여한 코인
		$sql = " select a.idx, (CASE WHEN a.day_type='1' THEN a.coin * a.attend WHEN a.day_type='0' THEN a.coin END ) as maxcoin,";
		$sql = $sql .= " (select challenges_idx from work_challenges_result as b where state='1' and email='".$user_id."' and challenges_idx=a.idx order by idx desc limit 1) as chamyeo_cidx";
		$sql = $sql .= " from work_challenges as a where a.state='0' and a.companyno='".$companyno."' and a.template='0' and a.temp_flag='0' and a.edate >='".TODATE."'";

		$get_chcoin_info = selectAllQuery($sql);
		for($i=0; $i<count($get_chcoin_info['idx']); $i++){
			$cha_idx = $get_chcoin_info['idx'][$i];
			$cha_maxcoin = $get_chcoin_info['maxcoin'][$i];
			$chamyeo_cidx = $get_chcoin_info['chamyeo_cidx'][$i];
			if($chamyeo_cidx){
				$chamyeo_coin[$chamyeo_cidx] = $cha_maxcoin;
			}
			//챌린지 코인합계
			$chamyeo_hapcoin += $cha_maxcoin;
		}

		//참여한 챌린지 코인 차감
		for($i=0; $i<count($get_chcoin_info['idx']); $i++){
			$get_maxcoin = $get_chcoin_info['idx'][$i];
			$get_chamyeo_coin = $chamyeo_coin[$get_maxcoin];
			if($get_chamyeo_coin){
				$chamyeo_hapcoin = $chamyeo_hapcoin - $get_chamyeo_coin;
			}
		}

		//획득가능한코인
		$sql = "select (CASE WHEN day_type='1' THEN (coin * attend) WHEN day_type='0' THEN coin  END ) as coin, (select sum(coin) as coin from work_challenges as a left join work_challenges_result as b on(a.idx=b.challenges_idx)";
		$sql = $sql .=" where a.state='0' and a.companyno='".$companyno."' and b.email='".$user_id."' and b.state=1) as chamyeo_coin";
		$sql = $sql .= " from work_challenges as a left join work_challenges_user as b on(a.idx=b.challenges_idx) where a.state='0' and a.companyno='".$companyno."' and a.template='0' and a.temp_flag='0' and b.email='".$user_id."'";
		$sql = $sql .= " and a.edate >='".TODATE."'";

		$get_chall_info = selectAllQuery($sql);
		if($chamyeo_hapcoin){
			$get_chall_coin = @number_format($chamyeo_hapcoin);
		}else{
			$get_chall_coin = 0;
		}

		if ($get_dirname == "challenges"){

			//도전중인 챌린지
			$sql = "select idx, state, attend_type, cate, title, coin, ";
			$sql = $sql .=" CASE WHEN attend_type ='1' THEN (SELECT count(idx) FROM work_challenges_comment WHERE challenges_idx = idx and state in (0))";
			$sql = $sql .=" WHEN attend_type ='2' THEN (SELECT count(idx) FROM work_challenges_file WHERE challenges_idx = idx and state in (0))";
			$sql = $sql .=" WHEN attend_type ='3' THEN ( SELECT count(idx) FROM work_challenges_file WHERE challenges_idx = idx and state in (0)) end as challenge";
			$sql = $sql .=" from work_challenges as a where a.state='0' and a.companyno='".$companyno."' and a.template='0' ".$where_challenge." and a.edate >= DATE_FORMAT(now(), '%Y-%m-%d')";
			$sql = $sql .=" order by a.idx desc";
			$chall_ing_list = selectAllQuery($sql);


			//챌린지 도전가능
			$sql = "select a.idx from work_challenges as a";
			$sql = $sql .=" left join work_challenges_result as b on(a.idx=b.challenges_idx)";
			$sql = $sql .= " where a.state='0' AND ( TIMESTAMPDIFF(DAY, DATE_FORMAT(now(), '%Y-%m-%d'), a.edate) >= 0)";
			$sql = $sql .= " and a.coaching_chk='0' and a.view_flag='0' and a.temp_flag='0'";
			$sql = $sql .= " and a.template='0' and a.companyno ='".$companyno."' group by a.idx";
			
			$chall_po_info = selectQuery($sql);
			if($chall_po_info['idx']){
				$chall_po_cnt = count($chall_po_info['idx']);
			}else{
				$chall_po_cnt = 0;
			}


			//챌린지 도전중
			$sql = "select count(1) cnt from ( select a.idx from work_challenges as a  left join work_challenges_result as b on(a.idx=b.challenges_idx)";
			$sql = $sql .= " where a.state='0' AND ( (b.state='0' and b.email='".$user_id."' and TIMESTAMPDIFF(DAY, DATE_FORMAT(now(), '%Y-%m-%d'), a.edate) >= 0))";
			$sql = $sql .= " and a.coaching_chk='0' and a.view_flag='0' and a.temp_flag='0' and a.template='0' and a.companyno ='".$companyno."' group by a.idx ) as c";
			$chall_ing_info = selectQuery($sql);
			if($chall_ing_info['cnt']){
				$chall_ing_cnt = $chall_ing_info['cnt'];
			}else{
				$chall_ing_cnt = 0;
			}

			//챌린지 도전완료
			$sql = "select count(1) cnt from ( select a.idx from work_challenges as a left join work_challenges_result as b on(a.idx=b.challenges_idx)";
			$sql = $sql .= " where a.state='0' AND ( (b.state='1' and a.attend=(select count(1) from work_challenges_result where state='1' and challenges_idx=a.idx and email='".$user_id."')))";
			$sql = $sql .= " and a.coaching_chk='0' and a.view_flag='0' and a.temp_flag='0' and a.template='0' and a.companyno ='".$companyno."' group by a.idx ) as c";
			$chall_complete_info = selectQuery($sql);
			if($chall_complete_info['cnt']){
				$chall_com_cnt = $chall_complete_info['cnt'];
			}else{
				$chall_com_cnt = 0;
			}

			//임시저장 챌린지
			$sql = "select count(1) cnt from ( select a.idx from work_challenges as a where a.state='0' and a.companyno ='".$companyno."' and a.coaching_chk='0' and a.temp_flag='1' and a.email='".$user_id."') as a";
			$chall_temp_info = selectQuery($sql);
			if($chall_temp_info['cnt']){
				$chall_temp_cnt = $chall_temp_info['cnt'];
			}else{
				$chall_temp_cnt = 0;
			}

			//내가 만든 챌린지
			$sql = "select count(1) cnt from ( select a.idx from work_challenges as a where a.state='0' and a.companyno ='".$companyno."' and a.coaching_chk='0' and a.email='".$user_id."') as c";
			$chall_temp_info = selectQuery($sql);
			if($chall_create_info['cnt']){
				$chall_create_cnt = $chall_create_info['cnt'];
			}else{
				$chall_create_cnt = 0;
			}


			//마감임박 챌린지 최근 3건, 7일 남은 챌린지
			$sql = "select a.idx, a.state, a.day_type, a.attend_type, attend, a.cate, a.title,";
			$sql = $sql .= " a.sdate, a.edate, TIMESTAMPDIFF(DAY, DATE_FORMAT(now(), '%Y-%m-%d'), a.edate) as chllday, temp_flag,";
			$sql = $sql .= " (SELECT count(idx) FROM work_challenges_user WHERE state='0' and challenges_idx = a.idx) AS chamyeo, a.coin,";
			$sql = $sql .= " (CASE WHEN a.day_type='1' THEN a.coin * a.attend WHEN a.day_type='0' THEN 0 END ) as maxcoin";
			$sql = $sql .= " from work_challenges as a left join work_challenges_result as b on(a.idx=b.challenges_idx) where a.state='0' and a.coaching_chk='0' and a.temp_flag='0' and a.companyno='".$companyno."' and a.template='0'";
			//and a.edate >= getdate()";
			$sql = $sql .= " and  TIMESTAMPDIFF(DAY, DATE_FORMAT(now(), '%Y-%m-%d'), a.edate)>='0' and TIMESTAMPDIFF(DAY, DATE_FORMAT(now(), '%Y-%m-%d'), a.edate)<='7' group by a.idx, a.state, a.attend_type, cate, title, coin, a.day_type, attend, temp_flag, sdate, edate, TIMESTAMPDIFF(DAY, a.sdate, a.edate) limit 3";
			$chall_deadline_list = selectAllQuery($sql);

			//챌린지 코인
			$sql = "select sum(coin) as coin from work_challenges where state='0' and template='0' and companyno='".$companyno."' and edate >= DATE_FORMAT(now(), '%Y-%m-%d')";
			$chall_coin_info = selectQuery($sql);
		}

		//회원별 공용코인
		$sql = "select idx, highlevel, coin, comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."' order by idx desc limit 1";
		$common_info = selectQuery($sql);
		if($common_info['idx']){

			//관리자 권한 일때
			if ($common_info['highlevel'] == '0'){

				if($common_info['comcoin'] > 0){
					$common_coin = number_format($common_info['comcoin']);
				}else{
					$common_coin = 0;

					if($common_info['coin']){
						$chall_coin_hap = $common_info['coin'] - $chall_coin_info['coin'];
					}

				}
			//일반 권한 경우, 공용코인으로 전환
			}else if ($common_info['highlevel'] == '5'){
				if($common_info['comcoin'] > 0 ){
					$chall_coin_hap = $common_info['comcoin'] - $chall_coin_info['coin'];
					$common_coin = number_format($chall_coin_hap);
				}else{
					$common_coin = 0;
				}
			}
		}

		//전체 공용코인
		$sql = "select sum(comcoin) as comcoin from work_member where state='0' and companyno='".$companyno."' and comcoin > 0";
		$mem_common_info = selectQuery($sql);
		if($mem_common_info['comcoin']){
			$mem_common_coin = number_format($mem_common_info['comcoin']);
		}else{
			$mem_common_coin = 0;
		}


		//챌린지 테마리스트 정보
		$thema_user_info = challenges_thema_list_info();

		?>