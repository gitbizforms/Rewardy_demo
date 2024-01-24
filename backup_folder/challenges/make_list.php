<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";


	//정시 출근시간(09:00) 2회이상 일경우
	//incl_ude/func.php 함수선언
	$coaching_chk = member_coaching_chk();
	if($coaching_chk){
		//조건절
		$where = "where a.state='0' and a.view_flag='0'";
	}else{
		//조건절
		$where = "where a.state='0' and a.coaching_chk='0'";
	}

	$que = "";
	//샘플페이지
	$template_type = $_GET['pgn']==""?"1":"0";

	$chall_type = $_POST['chall_type'];

	$where .= " and a.email ='".$user_id."'";

	//정렬
	$orderby = "order by a.idx desc";


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

	//완료한 챌린지 체크
	$sql = "select challenges_idx, state from work_challenges_result where state='1' and email='".$user_id."' group by challenges_idx, state";
	$chall_result = selectAllQuery($sql);
	if($chall_result['challenges_idx']){
		$chall_result_arr = @array_combine($chall_result['challenges_idx'], $chall_result['state']);
	}


	//챌린지 리스트
	$sql = "select * from (";
	$sql = $sql .=" select ROW_NUMBER() over(order by a.idx desc) as r_num, a.idx, a.state, a.day_type, a.attend_type, a.attend, a.cate, a.title, a.company, a.email, a.keyword, a.sdate, a.edate, DATEDIFF(DD, convert(varchar(10), getdate(), 120), a.edate ) as chllday, temp_flag, a.view_flag,";
	$sql = $sql .=" (SELECT count(idx) FROM work_challenges_user WHERE state='0' and challenges_idx = a.idx) AS chamyeo, a.coin,";
	$sql = $sql .=" (CASE WHEN a.day_type='1' THEN a.coin * a.attend WHEN a.day_type='0' THEN a.coin END ) as maxcoin,";
	$sql = $sql .=" (CASE WHEN a.attend_type ='1' THEN (SELECT count(DISTINCT email) FROM work_challenges_result WHERE state='1' and comment!='' and challenges_idx = a.idx)";
	$sql = $sql .=" WHEN a.attend_type ='2' THEN (SELECT count(DISTINCT email) FROM work_challenges_result WHERE state='1' and file_path!='' and file_name!='' and challenges_idx = a.idx)";
	$sql = $sql .=" WHEN a.attend_type ='3' THEN (SELECT count(DISTINCT email) FROM work_challenges_result WHERE state='1' and comment!='' and file_path!='' and file_name!='' and challenges_idx = a.idx) end) as challenge";
	$sql = $sql .=" from work_challenges as a left join work_challenges_result as b on(a.idx=b.challenges_idx) ".$where."";
	$sql = $sql .=" group by a.idx, a.state, a.attend_type, a.cate, a.title, a.coin, a.company, a.email, a.day_type, attend, a.temp_flag, a.view_flag, a.keyword, a.sdate, a.edate, DATEDIFF(DD,a.sdate, a.edate)";
	$sql = $sql .=" ) as a where r_num between ". $startnum ." and " .$endnum." ";
	$sql = $sql .= "".$orderby."";
	$chall_info = selectAllQuery($sql);
	//echo $sql;
	
	
	$html = "";
	if($chall_info['idx']){
		for($i=0; $i<count($chall_info['idx']); $i++){
			$idx = $chall_info['idx'][$i];
			$state = $chall_info['state'][$i];
			$bstate = $chall_info['bstate'][$i];
			$cate = $chall_info['cate'][$i];
			$title = $chall_info['title'][$i];
			$temp_flag = $chall_info['temp_flag'][$i];
			$keyword = $chall_info['keyword'][$i];
			$attend = $chall_info['attend'][$i];
			$view_flag = $chall_info['view_flag'][$i];


			if($chall_info['day_type'][$i] == '1'){
				$coin = number_format($chall_info['maxcoin'][$i]);

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

			if($challenge > 0){
				$chall_list_challenge['idx'][] = $idx;
				$chall_list_challenge['cate'][] = $cate;
				$chall_list_challenge['title'][] = $title;
				$chall_list_challenge['coin'][] = $coin;
			}


			$html = $html .= '<li class="sli2'.($chllday<0?" cha_dend":"").' category_0'.$cate.''.$offset.'" value="'.$chall_info['idx'][$i].'">';
			$html = $html .= '	<a href="#null" onclick="javascript:void(0);">';
			$html = $html .= '		<div class="cha_box">';
			$html = $html .= '			<div class="cha_box_m">';
			$html = $html .= '				<div class="cha_info">';
			if($keyword){
				$html = $html .= '				<span class="cha_cate">'.$keyword.'</span>';
			}

			//임시저장
			if($temp_flag == '1'){
				$html = $html .= '					<span class="cha_save">임시저장</span>';
			}

			if($view_flag == '1'){
				$html = $html .= '					<span class="cha_hide">숨김</span>';
			}

			//도전중
			if($challenges_list_arr[$chall_info['idx'][$i]]){
				$html = $html .= '					<span class="cha_ing">도전중</span>';
			}else{

				//완료한수와 참여수가 같으면 도전성공
				if($chall_result_arr[$idx] == $attend){
					$html = $html .= '				<span class="cha_comp">도전성공</span>';
				}else{
					if($chllday < 0 ){
						$html = $html .= '			<span class="cha_comp">도전실패</span>';
					}
				}
			}

			$html = $html .= '				</div>';
			$html = $html .= '				<span class="cha_coin"><strong>'.$coin.'</strong>코인</span>';
			$html = $html .= '			</div>';
			$html = $html .= '			<div class="cha_box_t">';
			$html = $html .= '				<span class="cha_title">'.$title.'</span>';
			$html = $html .= '			</div>';
			$html = $html .= '			<div class="cha_box_b">';
			$html = $html .= '				<span class="cha_member"><strong>'.$challenge.'</strong>/'.$chamyeo.'명(도전중)</span>';
			$html = $html .= '				<span class="cha_dday">'.$chlldays.'</span>';
			$html = $html .= '			</div>';
			$html = $html .= '		</div>';
			$html = $html .= '	</a>';
			$html = $html .= '</li>';

		}
	}else{

		//	$html = " 진행 중인 챌린지가 없습니다. 챌린지를 만들어 보세요.";
		$html = $html ='	<div class="tdw_list_none">';
		$html = $html .='		<strong><span>등록된 챌린지가 없습니다.</span></strong>';
		$html = $html .='	</div>';

	}



?>
<script>
var chall_category_arr = new Array();
	chall_category_arr["all"] = "전체";
<?
	foreach($chall_category as $key => $val){
	?>
		chall_category_arr["<?=$key?>"] = "<?=$val?>";
	<?
	}
?>
</script>
<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
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
									<input type="text" id="page_count" value="<?=$page_count?>">
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
											<li><button value="1"><span>참여자 많은 순</span></button></li>
											<li><button value="2"><span>기간 짧은 순</span></button></li>
											<li><button value="3"><span>코인 높은 순</span></button></li>
											<li><button value="4"><span>최신 순</span></button></li>
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

							<div class="rew_cha_list">
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


	<div class="t_layer rew_layer_join" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_tit">
				<strong>가입하기</strong>
				<span>리워디에서 인증을요청합니다. <br />
				리워디와 함께 하세요!</span>
			</div>
			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="z5" name="user_id" class="input_001" placeholder="이메일" />
							<label for="z5" class="label_001">
								<strong class="label_tit">이메일을 입력하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>인증메일 발송</span></button>
			</div>
			<div class="tl_descript">
				<p>리워디에서 인증을 요청합니다.<br />
				아래 링크를 클릭하셔서, 비밀번호를 설정해 주세요.<br />
				링크가 클릭되지 않으시면 아래 주소를 복사하여 인터넷 브라우저에 붙여<br />
				넣어주세요.<br />
				<br />
				https://www.rewardy.co.kr/<br />
				<br />
				기타 문의사항은 1588-8443으로 문의해 주세요.<br />
				리워디와 함께 해주셔서 감사합니다.
				</p>
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_repass" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_tit">
				<strong>비밀번호 재설정</strong>
				<span>비밀번호를 초기화할 수 있는 링크를 보내드립니다.<br />
				리워디에 가입한 이메일 주소를 입력해 주세요.</span>
			</div>
			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="z3" name="user_id" class="input_001" placeholder="이메일" />
							<label for="z3" class="label_001">
								<strong class="label_tit">이메일을 입력하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>비밀번호 재설정 메일 보내기</span></button>
			</div>
			<div class="tl_back">
				<button><span>이전으로</span></button>
			</div>
		</div>
	</div>


	<div class="t_layer rew_layer_setting" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_prof">
				<div class="tl_prof_box">
					<div class="tl_prof_img">
					</div>
					<div class="tl_prof_slc">
						<div class="tl_prof_slc_in">
							<button class="button_prof"><span>프로필 변경</span></button>
							<ul>
								<li>
									<input type="file" id="prof" class="input_prof" />
									<label for="prof" class="label_prof"><span>사진 변경</span></label>
								</li>
								<li><button id="btn_slc_character"><span>캐릭터 선택</span></button></li>
								<li><button class="default_on"><span>기본 이미지로 변경</span></button></li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="z7" name="user_id" class="input_002" disabled value="young@bizforms.co.kr" />
							<label for="z7" class="label_001">
								<strong class="label_tit">이메일</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input tc_50">
							<input type="text" id="z8" name="user_name" class="input_002" disabled placeholder="윤지혜" />
							<label for="z8" class="label_001">
								<strong class="label_tit">이름을 입력하세요</strong>
							</label>
						</div>
						<div class="tc_input tc_50">
							<input type="text" id="z9" name="user_name" class="input_002" disabled placeholder="디자인팀" />
							<label for="z9" class="label_001">
								<strong class="label_tit">이름을 입력하세요</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<input type="password" id="z10" name="user_pwd" class="input_001" placeholder="비밀번호" />
							<label for="z10" class="label_001">
								<strong class="label_tit">비밀번호를 입력하세요</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<input type="password" id="z11" name="user_repwd" class="input_001" placeholder="비밀번호 재확인" />
							<label for="z11" class="label_001">
								<strong class="label_tit">비밀번호를 확인하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			
			<div class="tl_btn">
				<button><span>가입하기</span></button>
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_character" style="display:none;">
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
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(images/pre/img_prof_01.png);">
								<button class="btn_profile" id="profile_img_01"><span>기본 프로필 이미지1 선택</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(images/pre/img_prof_02.png);">
								<button class="btn_profile" id="profile_img_02"><span>기본 프로필 이미지2 선택</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(images/pre/img_prof_03.png);">
								<button class="btn_profile" id="profile_img_03"><span>기본 프로필 이미지3 선택</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(images/pre/img_prof_04.png);">
								<button class="btn_profile" id="profile_img_04"><span>기본 프로필 이미지4 선택</span></button>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>적용</span></button>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function(){
			$(".btn_open_join").click(function(){
				$(".rew_layer_join").show();
			});
			$(".btn_open_login").click(function(){
				$(".rew_layer_login").show();
			});
			$(".btn_open_repass").click(function(){
				$(".rew_layer_repass").show();
			});
			$(".btn_open_setting").click(function(){
				$(".rew_layer_setting").show();
			});
			$(".tl_close button").click(function(){
				$(this).closest(".t_layer").hide();
			});

			$(".button_prof").click(function(){
				$(".tl_prof_slc ul").show();
			});
			$("#btn_slc_character").click(function(){
				$(".rew_layer_character").show();
			});
			$(".rew_layer_character .tl_btn").click(function(){
				$(".rew_layer_character").hide();
			});
			$(".btn_profile").click(function(){
				$(".btn_profile").removeClass("on");
				$(this).addClass("on");
			});
			$(".tl_prof_slc").mouseleave(function(){
				$(".tl_prof_slc ul").hide();
			});
		});
	</script>
</div>

<!-- footer start-->
<? include $home_dir . "/inc_lude/footer.php";?>
<!-- footer end-->

</body>


</html>
