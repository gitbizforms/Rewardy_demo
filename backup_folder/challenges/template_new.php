<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";

	?>

<style>
	.rew_cha_list .rew_cha_list_in .tdw_list_drag {
    background: #fff url(../images/pre/ico_list_move.png) 50% 50% no-repeat;
    width: 39px;
    height: 39px;
    text-indent: -9999px;
    display: inline-block;
    vertical-align: top;
    cursor: move;
    border-radius: 8px;
	}
</style>	

	
	<?





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


	//템플릿 리스트 갯수
	$sql = "select count(1) cnt from ( select a.idx from work_challenges as a where a.state='0' and a.coaching_chk='0' and a.temp_flag='0' and a.template='1' ) as a";
	$list_row = selectQuery($sql);
	if($list_row){
		$total_count = $list_row['cnt'];
	}

	//페이징 갯수
	if ( ($total_count % $pagesize) > 0 ){
		$page_count = floor($total_count/$pagesize)+1;
	}else{
		$page_count = floor($total_count/$pagesize);
	}


	//테마 리스트 정보
	$thema_list_info = challenges_thema_list_info();

	//정렬
	$orderby = "order by a.idx desc";
	//$orderby = "order by a.pageview desc";


	//관리권한
	if($template_auth){
		$where = " and a.template='1'";
	}else{
		$where = " and a.temp_flag='0' and a.template='1' and view_flag='0'";
	}

	//챌린지 템플릿 리스트
	$sql = "select * from (select ROW_NUMBER() over(order by a.idx desc) as r_num, a.idx, a.state, a.cate, a.title, a.company, a.email, a.keyword, a.pageview, a.temp_flag, a.view_flag";
	$sql = $sql .= ", (select count(1) from work_challenges_thema_zzim_list where a.idx=challenges_idx and state='0' and email='".$user_id."') as zzim";
	if ($thema_list_info['thema_idx']){
		$sql = $sql .= ",";
	}

	for($i=0; $i<count($thema_list_info['thema_idx']); $i++){
		$thema_idx = $thema_list_info['thema_idx'][$i];

		if ($thema_idx != end($thema_list_info['thema_idx'])){
			$field = "themaidx{$thema_idx},";
		}else{
			$field = "themaidx{$thema_idx}";
		}
		$sql = $sql .=" (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='".$thema_idx."' and a.idx=challenges_idx ) as ".$field."";
	}

	$sql = $sql .= " from work_challenges as a where a.state='0' and a.coaching_chk='0'".$where."";
	$sql = $sql .= " ) as a where r_num between ". $startnum ." and " .$endnum."";
	$sql = $sql .= " ".$orderby."";
	$chall_info = selectAllQuery($sql);

	if($user_id=='sadary0@nate.com'){
	//	echo $sql;
	}

	//카테고리정보
	$category = challenges_category();


	//테마리스트
	$sql = "select challenges_idx, thema_idx from work_challenges_thema_list where state='0' order by idx desc";
	$chall_thema_list_info = selectAllQuery($sql);

	for($i=0; $i<count($chall_thema_list_info['challenges_idx']); $i++){
		$ch_idx = $chall_thema_list_info['challenges_idx'][$i];
		$ch_thema_idx = $chall_thema_list_info['thema_idx'][$i];
		$thema_list_info_title[$ch_idx][] = $ch_thema_idx;
	}


	$html = "";
	if($chall_info['idx']){
		for($i=0; $i<count($chall_info['idx']); $i++){
											
			$idx = $chall_info['idx'][$i];
			$state = $chall_info['state'][$i];
			$cate = $chall_info['cate'][$i];
			$title = $chall_info['title'][$i];
			$zzim = $chall_info['zzim'][$i];
			$temp_flag = $chall_info['temp_flag'][$i];
			$pageview = $chall_info['pageview'][$i];
			$view_flag = $chall_info['view_flag'][$i];
			$keyword = $chall_info['keyword'][$i];
			$title = urldecode($title);


			$html = $html .= '<li class="sli2'.($chllday<0?" cha_dend":"").' category_0'.$cate.''.$offset.'" value="'.$chall_info['idx'][$i].'" id="themaslist_'.$chall_info['idx'][$i].'">';
			$html = $html .= '<button class="cha_jjim'.($zzim>0?" on":"").'" id="cha_zzim_'.$idx.'"><span>찜하기</span></button>';
			$html = $html .= '	<a href="#null" onclick="javascript:void(0);">';
			$html = $html .= '	<div class="tdw_list_drag" title="순서 변경"><span>드래그 드랍 기능</span></div>';
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

			$html = $html .= '				</div>';
			$html = $html .= '			</div>';
			$html = $html .= '			<div class="cha_box_t">';
			$html = $html .= '				<span class="cha_title">'.$title.'</span>';
			$html = $html .= '			</div>';
			
			$html = $html .= '			<div class="cha_box_b">';
			$html = $html .= '				<span class="cha_hit">조회수 '.number_format($pageview).'</span>';
			$html = $html .= '			</div>';

			$html = $html .= '		</div>';
			$html = $html .= '	</a>';
			$html = $html .= '</li>';

		}
	}

?>

<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in" id="rew_conts_in">

						<div class="rew_cha_list_func">
							<div class="rew_cha_list_func_in">
								<div class="rew_cha_count">
									<span class="thema_title" id="thema_title">전체</span>
									<?if($template_auth){?>
										<div class="thema_title_regi" id="thema_title_edit" style="display:none;">
											<input type="text" value="힘을 내요 신입사원" class="input_thema_title" id="input_thema_title"/>
											<div class="btn_thema_title">
												<button class="btn_thema_submit" id="btn_thema_submit">확인</button>
												<button class="btn_thema_cancel" id="btn_thema_cancel">취소</button>
											</div>
										</div>
									<?}?>
									<strong><?=$total_count?></strong>
									<input type="text" id="pageno" value="<?=$gp?>">
									<input type="text" id="page_count" value="<?=$page_count?>">
									<input type="hidden" id="chall_type">
									<input type="hidden" id="chall_cate">
									<input type="hidden" id="thema_zzim">
									<input type="hidden" id="thema_idx">
								</div>

								<div class="rew_cha_sort" style="right:240px" id="template_sort">
									<div class="rew_cha_sort_in">
										<button class="btn_sort_on" id="btn_sort_on" value="3"><span>최근 등록 순</span></button>
										<ul>
											<li><button value="1"><span>조회수 순</span></button></li>
											<li><button value="2"><span>찜 많은 순</span></button></li>
											<li><button value="3"><span>최근 등록 순</span></button></li>
										</ul>
									</div>
								</div>
								<div class="rew_cha_search" style="right:10px" id="rew_cha_search">
									<div class="rew_cha_search_box">
										<input type="text" class="input_search" id="input_search_thema" placeholder="키워드 검색" />
										<button id="input_search_thema_btn"><span>검색</span></button>
									</div>
								</div>
								
								<?if($template_auth){?>
									<div class="rew_cha_chk_tab" id="rew_cha_chk_tab">
										<ul>
											<li>
												<div class="chk_tab">
													<input type="checkbox" name="cha_template_tab" id="cha_template_tab_all" checked />
													<label for="cha_template_tab_all">전체</label>
												</div>
											</li>
											<li>
												<div class="chk_tab">
													<input type="checkbox" name="cha_template_tab" id="cha_chk_tab_save" checked>
													<label for="cha_chk_tab_save">임시저장 챌린지</label>
												</div>
											</li>
											<li>
												<div class="chk_tab">
													<input type="checkbox" name="cha_template_tab" id="cha_chk_tab_hide" checked>
													<label for="cha_chk_tab_hide">숨긴 챌린지</label>
												</div>
											</li>
										</ul>
									</div>
								<?}?>

							</div>
						</div>

						<div class="rew_conts_scroll_04" id="rew_conts_scroll_04">

							<div class="rew_cha_list">
								<div class="rew_cha_list_in">
									<ul class="rew_cha_list_ul" id="template_list">
										<?=$html?>
									</ul>

									<?if($gp >= $page_count){?>
										<div class="rew_cha_more" id="template_more" style="display:none">
											<button><span>more</span></button>
										</div>
									<?}else{?>
										<div class="rew_cha_more" id="template_more">
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
