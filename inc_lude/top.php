<?php

	//로그인 상태 유지
	//2022-01-28 주석처리
	/*if($_COOKIE['worksinput']){
		//worksinput 쿠키값 복후화
		parse_str(Decrypt($_COOKIE['worksinput']));
	}*/

	if($user_id){
		$sql = "select idx, email, name, highlevel, partno, companyno, coin from work_member where email='".$user_id."'";
		$res = selectQuery($sql);

		//회원등급
		if($res['idx']){
			//쿠키값과 맞지 않을경우 다시 쿠키 생성함
			if($user_level != $res['highlevel']){
				$highlevel = preg_replace("/[^0-9]/", "", $res['highlevel']);
				if (is_numeric($highlevel) == true){
					setcookie('user_level', $highlevel , COOKIE_TIME , '/', C_DOMAIN);
				}
			}

			//부서별
			if($user_part != $res['partno']){
				$partno = preg_replace("/[^0-9]/", "", $res['partno']);
				if (is_numeric($partno) == true){
					setcookie('user_part', $partno , COOKIE_TIME , '/', C_DOMAIN);
				}
			}

			//코인
			if($res['coin']){
				$mem_coin = preg_replace("/[^0-9]/", "", $res['coin']);
				if (is_numeric($mem_coin) == true){
					setcookie('user_coin', $mem_coin , COOKIE_TIME , '/', C_DOMAIN);
				}
			}

			//회사별
			if($res['companyno']){
				$companyno = preg_replace("/[^0-9]/", "", $res['companyno']);
				if (is_numeric($companyno) == true){
					setcookie('companyno', $companyno , COOKIE_TIME , '/', C_DOMAIN);
				}
			}
		}

		//어제적립된 코인
		$sql = "select convert(varchar(10), regdate, 120) as wdate, sum(coin) as coin from work_coininfo ";
		$sql = $sql . " where convert( varchar(10) , regdate , 120) between convert( varchar(10), DATEADD(day, -1 ,getdate()) , 120) and convert( varchar(10) , DATEADD(day, 0 ,getdate()) , 120)";
		$sql = $sql . " and email='".$user_id."'";
		$sql = $sql . " group by convert( varchar(10) , regdate , 120)";
		$res = selectAllQuery($sql);
		for($i=0; $i<count($res['wdate']); $i++){
			$wcoin[$res['wdate'][$i]] = $res['coin'][$i];
		}

		$w_yesterday = date('Y-m-d', strtotime('-1 day'));
		$w_today = date('Y-m-d', time());
		$w_reward = $wcoin[$w_today] - $wcoin[$w_yesterday];

		$sql = "select idx, state, email, reward_user, reward_name,coin from work_coininfo where state !='9' and convert( varchar(10) , regdate , 120) = convert( varchar(10) , getdate() , 120) and email='".$user_id."'";
		$coin_info = selectAllQuery($sql);
		if($coin_info['idx']){
			$today_coin = count($coin_info['idx']);
		}


		//오늘 챌린지내역
		//$sql = "select count(idx) as cnt from work_challenges where state='0' and (sdate >= convert(varchar(10), getdate(), 120) or convert(varchar(10), getdate(), 120) <= edate)";
		//$sql = $sql .= " and idx NOT IN ( select distinct challenges_idx FROM work_challenges_com where email='".$user_id."')";

		$sql = "select count(idx) as cnt from work_challenges where state !='9' and convert(varchar(10), getdate(), 120) <= edate";
		$sql = $sql .= " and idx NOT IN ( select distinct challenges_idx FROM work_challenges_com where email='".$user_id."')";

		$challenges_info = selectQuery($sql);
		if($challenges_info['cnt'] > 0){
			$challenges_cnt = $challenges_info['cnt'];
		}

		//최근 코인내역
		$sql = "select idx, state, reward_name, coin from work_coininfo where state='1' and convert( varchar(10) , regdate , 120) = convert( varchar(10) , getdate() , 120) and email='".$user_id."' order by idx desc";
		$coin_newinfo = selectQuery($sql);
		if($coin_newinfo['idx']){
			$coin_new_state = $coin_newinfo['state'];
			$coin_new_name = $coin_newinfo['reward_name'];
			$coin_new_coin = number_format($coin_newinfo['coin']);

		}
	}

?>

	<div class="t_header">


		<div class="th_in">
			<div class="th_menu_logo">
				<a href="/" class="menu_logo_biz"><span>비즈폼</span></a>
				<?php
					if( in_array( get_dirname() , array("admin","works","coins","challenge"))){?>
						<!-- <a href="/" class="menu_logo_today"><span>오늘업무</span></a> -->
				<?php
				}
				?>
			</div>
			<div class="th_menu">
				<div class="th_menu_in">
					<div class="menu_login">
						<?if(!$_COOKIE['user_id']){?>
							<a href="javascript:void(0);" class="th_login" id="login_btn"><span>로그인</span></a>
						<?}?>
					</div>

					<?if($_COOKIE['user_id']){?>
						<div class="menu_coin">
							<a href="/coins/list.php"><i class="far fa-copyright"></i><span><?=@number_format($mem_coin)?></span></a>
							<strong>coin</strong>
							<?if($w_reward > 0){?>
								<em class="menu_coin_arrow arrow_up">▲</em>
							<?}else{?>
								<em class="menu_coin_arrow arrow_up">▼</em>
							<?}?>
							<strong><?=@number_format(abs($w_reward));?></strong>
						</div>
					<?}?>

					<div class="menu_reward">
						<a href="javascript:void(0);" onclick="location_works('reward');"><i class="fas fa-gift"></i><span>보상</span> <?if($today_coin > 0){?><strong class="menu_reward_ico"><?=$today_coin?></strong><?}?></a>
					</div>
					<div class="menu_challenge">
						<a href="javascript:void(0);" onclick="location_works('challenge');""><i class="fas fa-medal"></i><span>챌린지</span> <?if($challenges_cnt > 0 ){?><strong class="menu_challenge_ico"><?=$challenges_cnt?><?}?></strong></a>
					</div>
					<div class="menu_list">
						<button class="menu_open"><i class="fas fa-bars"></i></button>
						<div class="menu_box">
							<button class="menu_close"><span>X</span></button>
							<ul>
								<li><a href="#"><span>출퇴근</span></a></li>
								<li><a href="javascript:void(0);" onclick="location_works('works');"><span>오늘업무</span></a></li>
								<li><a href="#"><span>회의록</span></a></li>
								<li><a href="#"><span>업무공유</span></a></li>
								<li><a href="javascript:void(0);" onclick="location_works('challenge');"><span>챌린지</span></a></li>
								<li><a href="javascript:void(0);" onclick="location_works('reward');"><span>보상</span></a></li>

								<?if($_COOKIE['user_id']){?>
									<li><a href="javascript:void(0);" id="logout_btn"><span>로그아웃</span></a></li>
								<?}?>

							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>