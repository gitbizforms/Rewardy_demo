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


			//챌린지 도전가능
			$sql = "select count(1) cnt from work_challenges as a
					where a.state='0' 
					AND ( TIMESTAMPDIFF(DAY, DATE_FORMAT(now(), '%Y-%m-%d'), a.edate) >= 0)
					and a.coaching_chk='0' 
					and a.view_flag='0' 
					and a.temp_flag='0'
					and a.template='0' 
					and a.companyno ='".$companyno."' 
					group by a.idx";
			
			$chall_po_info = selectQuery($sql);
			if($chall_po_info['cnt']){
				$chall_po_cnt = $chall_po_info['cnt'];
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
			$sql = "select count(1) cnt from work_challenges a 
				left join work_challenges_result b on a.idx = b.challenges_idx
				where 1=1
				and b.email = '".$user_id."'
				and a.companyno = '".$companyno."'
				and b.state = '1'";
				$chall_complete_info = selectQuery($sql);
			if($chall_complete_info['cnt']){
				$chall_com_cnt = $chall_complete_info['cnt'];
			}else{
				$chall_com_cnt = 0;
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

	
		?>