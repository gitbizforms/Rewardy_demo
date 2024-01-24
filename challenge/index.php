<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	// include $home_dir . "/inc_lude/header.php";
	include $home_dir . "/inc_lude/header_index.php";
	include $home_dir . "/challenge/challenges_header_main.php";

	$onoff = $_COOKIE['onoff'];
?>

<link rel="stylesheet" type="text/css" href="/html/css/challenge.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/rew_head.css<?php echo VER;?>" />

<?
	//정시 출근시간(09:00) 2회이상 일경우
	//incl_ude/func.php 함수선언
	$coaching_chk = member_coaching_chk();
	if($coaching_chk){
		//조건절
		$where = "where a.state in('0','1') and view_flag='0'";
	}else{
		//조건절
		$where = "where a.state in('0','1') and coaching_chk='0'";
	}

	$que = "";
	//샘플페이지
	$template_type = $_GET['pgn']==""?"1":"0";

	$chall_type = $_POST['chall_type'];

	//챌린지템플릿
	if($chall_type == "template"){

		$where .= " and a.view_flag='0'";
		$where .= " and a.template='1'";
		$where .= " and a.temp_flag='0'";
		$where .= " and a.edate >= '".DBDATE."'";
		// $where .= " and a.edate >= convert(varchar(10), getdate(), 120)";


	//내가만든챌린지
	}else if($chall_type == "chmy"){
		$where .= " and a.email ='".$user_id."'";

	//완료한챌린지
	}else if($chall_type == "chcom"){

		$where .= " and a.view_flag='0'";
		$where .= " and b.state='1'";

		//임시저장
		$where .= " and a.temp_flag='0'";

		$where .= " and b.email ='".$user_id."'";

		if($companyno){
			$where .= " and a.companyno ='".$companyno."'";
		}

		$que = $que .= " left join work_challenges_result as b on (a.idx=b.challenges_idx)";

	}else{
		$where .= " and a.view_flag='0'";
		$where .= " and a.temp_flag='0'";


		//관리자 권한이 아닌경우
		if ($user_level != '1'){
			if($companyno){
				$where .= " and a.companyno='".$companyno."'";
			}
		}

		$where .= " and a.template='0'";
	}

	//회원정보
	$member_row_info = member_row_info($user_id);

	//챌린지전체갯수
	//$sql = "select count(1) cnt from work_challenges as a ".$where."";
	$sql = "select count(1) cnt from ( select a.idx from work_challenges as a ".$que. $where." group by a.idx ) as c";
	$list_row = selectQuery($sql);
	
	if($list_row){
		$total_count = $list_row['cnt'];
	}

	if(!$gp) {
		$gp = 1;
	}

	$pagesize = 12;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $gp * $pagesize;			//페이지 끝번호

	//시작번호
	if ($gp == 1){
		$startnum = 1;
	}else{
		$startnum = ($gp - 1) * $pagesize + 1;
	}

	//페이징 갯수
	if ( ($total_count % $pagesize) > 0 ){
		$page_count = floor($total_count/$pagesize)+1;
	}else{
		$page_count = floor($total_count/$pagesize);
	}

	//완료한 챌린지 갯수 체크
	$sql = "select challenges_idx, state from work_challenges_result where state='1' and email='".$user_id."'";
	$challenges_result = selectAllQuery($sql);

	for($i=0; $i<count($challenges_result['challenges_idx']); $i++){

		$chall_result_chall_idx = $challenges_result['challenges_idx'][$i];
		$chall_result_chall_state = $challenges_result['state'][$i];
		if($chall_result_chall_state == 1){
			$chall_result_cnt[$chall_result_chall_idx]++;
		}
	}

	//완료한 챌린지 체크
	$sql = "select challenges_idx, state from work_challenges_result where state!='9' and email='".$user_id."' group by challenges_idx, state";
	$chall_result = selectAllQuery($sql);
	if($chall_result['challenges_idx']){
		$chall_result_arr = @array_combine($chall_result['challenges_idx'], $chall_result['state']);
	}

	//정렬
	$orderby = " order by CASE WHEN chllday > 0 THEN chllday END desc , CASE WHEN a.state = '0' THEN a.idx END  desc";

	//챌린지 리스트
	// $sql = "select a.idx, a.state, a.day_type, a.attend_type, attend, a.cate, a.title, a.companyno, a.email";
	// $sql .=" ,a.keyword, a.sdate, a.edate, TIMESTAMPDIFF(DAY, DATE_FORMAT(now(), '%Y-%m-%d'), a.edate) as chllday, temp_flag,";
	// $sql .=" (SELECT count(idx) FROM work_challenges_user WHERE state='0' and challenges_idx = a.idx) AS chamyeo, a.coin,";
	// $sql .=" (CASE WHEN a.day_type='1' THEN a.coin * a.attend WHEN a.day_type='0' THEN a.coin END ) as maxcoin,";
	// $sql .=" (CASE WHEN a.attend_type ='1' THEN (SELECT count(DISTINCT email) FROM work_challenges_result";
	// $sql .=" WHERE state='1' and comment!='' and challenges_idx = a.idx)";
	// $sql .=" WHEN a.attend_type ='2' THEN (SELECT count(DISTINCT email) FROM work_challenges_result";
	// $sql .=" WHERE state='1' and (comment!='' or file_path!='' and file_name!='') and challenges_idx = a.idx)";
	// $sql .=" WHEN a.attend_type ='3' THEN (SELECT count(DISTINCT email) FROM work_challenges_result";
	// $sql .=" WHERE state='1' and (comment!='' or file_path!='' and file_name!='') and challenges_idx = a.idx) end) as challenge";
	// $sql .=" from work_challenges as a left join work_challenges_result as b on(a.idx=b.challenges_idx) ".$where."";
	// $sql .=" group by a.idx, a.state, a.attend_type, a.cate, a.title, a.coin, a.companyno, a.email";
	// $sql .=" , a.day_type, attend, a.temp_flag, a.keyword, a.sdate, a.edate, TIMESTAMPDIFF(DAY, a.sdate, a.edate)";
	// $sql .= "".$orderby."";
	// $sql .= " limit ". ($gp-1)*$startnum.", ".$endnum;
	
	$sql = "select idx, state, attend, title, cate, temp_flag, temp_flag, keyword, coin, day_type, limit_count,
			TIMESTAMPDIFF(DAY, DATE_FORMAT(now(), '%Y-%m-%d'), edate) as chllday,
			(SELECT count(idx) FROM work_challenges_user WHERE state='0' and challenges_idx = work_challenges.idx) AS chamyeo,
			(SELECT count(idx) FROM work_challenges_result WHERE state='1' and challenges_idx = work_challenges.idx) AS challenge
			from work_challenges where 1=1 
			and state in ('0','1') 
			and view_flag = '0' 
			and temp_flag = '0' 
			and template = '0' 
			and companyno = '".$companyno."'
			order by CASE WHEN chllday >= 0 THEN chllday END desc , CASE WHEN state in ('0','1') THEN idx END  desc";
	$sql .= " limit ". ($gp-1)*$startnum.", ".$endnum;
	$chall_info = selectAllQuery($sql);


	$html = "";
	if($chall_info['idx']){
		for($i=0; $i<count($chall_info['idx']); $i++){
			$idx = $chall_info['idx'][$i];
			$state = $chall_info['state'][$i];
			$bstate = $chall_info['bstate'][$i];
			$attend = $chall_info['attend'][$i];
			$cate = $chall_info['cate'][$i];
			$title = $chall_info['title'][$i];
			$temp_flag = $chall_info['temp_flag'][$i];
			$keyword = $chall_info['keyword'][$i];
			$limit_cnt = $chall_info['limit_count'][$i];
			
			if($limit_cnt>0){
				$limit = "(!선착순!)";
			}else{
				$limit = "";
			}

			if($chall_info['day_type'][$i] == '1'){
				$coin = "최대 " .number_format($chall_info['coin'][$i] * $chall_info['attend'][$i]);
				// $coin = "최대 " .number_format($chall_info['maxcoin'][$i]);
			}else{
				
				$coin = number_format($chall_info['coin'][$i]);
			}

			$chllday = $chall_info['chllday'][$i];
			if($chllday == 0){
				$chlldays = "D - Day";
			}else if($chllday < 0){
				$chlldays = "종료";
			}else{
				$chlldays = "D - ".$chllday;
			}
			$chamyeo = number_format($chall_info['chamyeo'][$i]);
			$challenge = number_format($chall_info['challenge'][$i]);
			$title = urldecode($title);

			// if($challenge > 0){
			// 	$chall_list_challenge['idx'][] = $idx;
			// 	$chall_list_challenge['cate'][] = $cate;
			// 	$chall_list_challenge['title'][] = $title;
			// 	$chall_list_challenge['coin'][] = $coin;
			// }

			if($chall_result_arr[$idx] == '0' || ($chall_result_cnt[$idx] && $chall_result_cnt[$idx] != $attend) ){
				$html_to = '도전중';
			}else if($chall_result_arr[$idx] == '1' && $chall_result_cnt[$idx] == $attend ){
				$html_to = '도전완료';
			}else{
				$html_to = '';
			}

			$html = $html .= '<li class="'.($chllday<0?"cha_dend ":"").'category_0'.$cate.'" value="'.$idx.'">';
			$html = $html .= '	<a href="#null" onclick="javascript:void(0);">';
			$html = $html .= '		<div class="cha_box">';
			$html = $html .= '			<div class="cha_box_m">';
			$html = $html .= '				<div class="cha_info">';
			if($keyword){
				$html = $html .= '				<span class="cha_cate">'.$keyword.'</span>';
			}
			$html = $html .= '				</div>';
			$html = $html .= '				<span class="cha_coin"><strong>'.$coin.'</strong></span>';
			$html = $html .= '			</div>';
			$html = $html .= '			<div class="cha_box_t">';
			$html = $html .= '				<span class="cha_title">'.$title.'</span>';
			$html = $html .= '			</div>';
			$html = $html .= '			<div class="cha_box_b">';
			$html = $html .= '				<span class="cha_member"><strong>'.$challenge.'</strong>/'.$chamyeo.'명'.$limit.'</span>';
			$html = $html .= '				<span class="cha_dday">'.$chlldays.'</span>';
			$html = $html .= '			</div>';
			$html = $html .= '		</div>';
			$html = $html .= '	</a>';
			$html = $html .= '</li>';

		}

	}else{

	//	$html = " 진행 중인 챌린지가 없습니다. 챌린지를 만들어 보세요.";
		$html = $html .='	<div class="tdw_list_none">';
		$html = $html .='		<strong><span>등록된 챌린지가 없습니다.</span></strong>';
		$html = $html .='	</div>';
	}

	//프로필 캐릭터 사진
	$character_img_info = character_img_info();
?>

<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box <?=$onoff==0?' on':''?>">
			<div class="rew_box_in">
				<? include $home_dir . "/inc_lude/header_new.php";?>
				<!-- //상단 -->
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">
						<div class="rew_cha_list_func">
							<div class="rew_cha_list_func_in">
								<div class="rew_cha_count">
									<span>전체</span>
									<strong><?=$total_count?></strong>
									<input type="text" id="pageno" value="<?=$gp?>">
									<input type="text" id="page_count" value="0">
									<input type="hidden" id="chall_type">
									<input type="hidden" id="chall_cate">
								</div>
								<div class="rew_cha_tab_sort" style="right:406px">
									<div class="rew_cha_tab_sort_in">
										<button class="btn_sort_on" value="all"><span>전체</span></button>
										<ul>
											<li><button value="all"><span>전체</span></button></li>
											<?for($i=0; $i<count($cate_info['idx']); $i++){?>
												<li><button value="<?=$cate_info['idx'][$i]?>"><span><?=$cate_info['name'][$i]?></span></button></li>
											<?}?>
										</ul>
									</div>
								</div>
								<div class="rew_cha_sort" style="right:240px" id="rew_cha_sort_list">
									<div class="rew_cha_sort_in">
										<button id="rank" class="btn_sort_on" value="4"><span id="rank_title">최신 순</span></button>
										<ul>
											<li><button value="4"><span>최신 순</span></button></li>
											<li><button value="2"><span>기간 짧은 순</span></button></li>
											<li><button value="3"><span>코인 높은 순</span></button></li>
											<li><button value="1"><span>참여자 많은 순</span></button></li>
										</ul>
									</div>
								</div>
								<div class="rew_cha_search" style="right:10px">
									<div class="rew_cha_search_box">
										<input type="text" class="input_search" placeholder="챌린지명 검색" id="input_search"/>
										<button id="input_search_btn"><span>검색</span></button>
									</div>
								</div>
								<div class="rew_cha_chk_tab">
									<ul>
										<li>
											<div class="chk_tab">
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_all" checked />
												<label for="cha_chk_tab_all">전체</label>
											</div>
										</li>
										<li>
											<div class="chk_tab">
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_wait" checked />
												<label for="cha_chk_tab_wait">도전가능한 챌린지</label>
											</div>
										</li>
										<li>
											<div class="chk_tab">
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_ing" checked />
												<label for="cha_chk_tab_ing">도전중인 챌린지</label>
											</div>
										</li>
										<li>
											<div class="chk_tab">
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_comp" checked />
												<label for="cha_chk_tab_comp">내가 완료한 챌린지</label>
											</div>
										</li>
										<li>
											<div class="chk_tab">
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_end" checked />
												<label for="cha_chk_tab_end">종료된 챌린지</label>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</div>

						<div class="rew_conts_scroll_04">

							<div class="rew_cha_list" id="rew_cha_list">
								<div class="rew_cha_list_in">
									<ul class="rew_cha_list_ul">
										<?=$html?>
									</ul>
									<?if($gp >= $page_count){?>
										<div class="rew_cha_more" id="rew_cha_more" style="display:none">
											<button><span>more</span></button>
										</div>
									<?}else{?>
										<div class="rew_cha_more" id="rew_cha_more">
											<button><span>more</span></button>
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
</div>

<?php
	//튜토리얼 시작 레이어
	include $home_dir . "/layer/tutorial_start.php";

	//튜토리얼 시작 레이어
	include $home_dir . "/layer/tutorial_main_level.php";

	//아이템 레이어
	include $home_dir . "/layer/item_img_buy.php";
	//프로필 팝업
	include $home_dir . "/layer/pro_pop.php";
	//캐릭터 팝업
	include $home_dir . "/layer/char_pop.php";

?>
<?php
	//비밀번호 재설정
	include $home_dir . "/layer/member_repass.php";
?>
<!-- footer start-->

<? include $home_dir . "/inc_lude/footer.php";?>
<!-- footer end-->
<?php
	//로딩 페이지
	include $home_dir . "loading.php";
?>
	<script type="text/javascript" src = "/js/index_new.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			window.onbeforeunload = function () { $('.rewardy_loading_01').css('display', 'block'); }
			$(window).load(function () {          //페이지가 로드 되면 로딩 화면을 없애주는 것
				$('.rewardy_loading_01').css('display', 'none');
			});
		});
		window.onpageshow = function(event) {
 	     if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
			  $('.rewardy_loading_01').css('display', 'none');
  		  }
		}
	</script>
</body>


</html>
