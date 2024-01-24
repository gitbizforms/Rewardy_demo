<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header_index.php";
?>
<link rel="stylesheet" type="text/css" href="/html/css/set_01.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/all.min.css" />
<?
	if($user_level != '0'){
		header("Location:https://rewardy.co.kr/index.php");
		exit;
	}

	//if( $user_level != '0'){
	//	alertMove("접속권한이 없습니다.");
	//	exit;
	//}

	//부서관리
	$team_info = member_part_info();

	//현재 회사 설정
	$sql = "select idx, penalty, penalty_in, penalty_work, penalty_out, penalty_challenge, ";
  $sql = $sql .= "DATE_FORMAT(intime, '%H') as IH, DATE_FORMAT(intime, '%i') as Im, DATE_FORMAT(outtime, '%H') as OH, DATE_FORMAT(outtime, '%i') as Om";
  $sql = $sql .= " from work_company where idx = '".$companyno."' and state = '0' ";
	$setting = selectQuery($sql);

?>
<script>
var team_info_arr = new Array();
<?
for($i=0; $i<count($team_info['partno']); $i++){?>
	team_info_arr['<?=$team_info['partno'][$i]?>'] = '<?=$team_info['part'][$i]?>';
<?}?>

</script>

<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<div class="rew_conts">
					<input type="hidden" value="<?=$companyno?>" id="comp_no">
					<div class="rew_conts_in">
						<div class="rew_member_tab">
							<div class="rew_member_tab_in">
								<ul>
									<li class="member_list"><a href="#"><span>멤버관리</span></a></li>
									<li class="comlogo on"><a href="#"><span>홈페이지 설정</span></a></li>
									<li class="comcoin"><a href="#"><span>공용코인 관리</span></a></li>
									<li class="comcoin_member"><a href="#"><span>멤버별 공용코인</span></a></li>
									<li class="comcoin_out_page"><a href="#"><span>코인출금 신청내역</span></a></li>
								</ul>
							</div>
						</div>
						<div class="rew_conts_scroll_01">
              <div class="rew_member">
                <div class="rew_member_in">
                  <div class="rew_member_func">
                    <div class="rew_member_func_in">
                      <div class="rew_member_count">
                        <span>사용자 설정</span>
                      </div>
                    </div>
                  </div>
                  <div class="rew_member_list">
                    <div class="rew_member_list_in">
                      <div class="member_list_conts">
                        <div class="member_list_conts_in">
                          <div class="list_conts_top">
                            <div class="set_list_title">
                              <strong>회사 로고 이미지 설정</strong>
                              <span>세로 34px 이상의 배경이 투명한 이미지</span>
                            </div>
                            <div class="set_logo_file">
                              <div class="logo_file on">
                                <span class="file_name">logo_file.png</span>
                                <input type="file" id="rew_logo" style="display:none;"/>
                                <button class="file_down on"><span>저장</span></button>
                                <input type="hidden" id="comp_no" value="<?=$companyno?>"/>
                                <button class="file_down" style="display: none;"><span>삭제</span></button>
                              </div>
                            </div>
                          </div>
                          <ul>
                            <li>
                              <div class="set_list_title">
                                <strong>패널티 설정</strong>
                                <span>페널티를 받을 경우 좋아요와 코인을 24시간(00시~24시)동안 받지 못합니다.</span>
                              </div>
                              <div class="member_list_conts_setting all">
                                <div class="btn_switch<?=$setting['penalty']=="1"?" on":""?>" id="admin_penalty">
                                  <strong class="btn_switch_on"></strong>
                                  <span>버튼</span>
                                  <strong class="btn_switch_off"></strong>
                                </div>
                              </div>
                            </li>
                            <li>
                              <div class="set_list_title">
                                <strong>출퇴근 시간 설정</strong>
                                <span>근무 시간 설정, 출근기록 페널티에 반영됩니다.</span>
                              </div>
                              <div class="member_list_conts_admin">
                                <div class="time_work">
                                  <div class="rew_member_sort">
                                    <div class="rew_member_sort_in">
                                      <button class="btn_sort_on" id="in_hour"><span><?=$setting['IH']?></span></button>
                                      <ul>
                                        <? for($i=0; $i<24; $i++){?>
                                          <li><button><span><?=$i?></span></button></li>
                                        <?}?>
                                      </ul>
                                    </div>
                                  </div>
                                  <div class="rew_member_sort">
                                    <div class="rew_member_sort_in">
                                      <button class="btn_sort_on" id="in_minite"><span><?=$setting['Im']?></span></button>
                                      <ul>
                                          <li><button><span>00</span></button></li>
                                          <li><button><span>30</span></button></li>
                                      </ul>
                                    </div>
                                  </div>
                                </div>
                                <div class="time_end">
                                  <div class="rew_member_sort">
                                    <div class="rew_member_sort_in">
                                      <button class="btn_sort_on" id="out_hour"><span><?=$setting['OH']?></span></button>
                                      <ul>
                                        <? for($i=0; $i<24; $i++){?>
                                          <li><button><span><?=$i?></span></button></li>
                                        <?}?>
                                      </ul>
                                    </div>
                                  </div>
                                  <div class="rew_member_sort">
                                    <div class="rew_member_sort_in">
                                      <button class="btn_sort_on" id="out_minite"><span><?=$setting['Om']?></span></button>
                                      <ul>
                                          <li><button><span>00</span></button></li>
                                          <li><button><span>30</span></button></li>
                                      </ul>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <button class="file_down on" id="time_save"><span>저장</span></button>
                            </li>
                            <li>
                              <div class="set_list_title">
                                <strong>출근기록</strong>
                                <span>매주 지각 2회 이상 느낌표 알림, 3회이상 페널티 발동</span>
                              </div>
                              <div class="member_list_conts_setting list">
                                <div class="btn_switch<?=$setting['penalty_in']=="1"?" on":""?>" id="admin_in">
                                  <strong class="btn_switch_on"></strong>
                                  <span>버튼</span>
                                  <strong class="btn_switch_off"></strong>
                                </div>
                              </div>
                            </li>
                            <li>
                              <div class="set_list_title">
                                <strong>오늘업무</strong>
                                <span>매주 오늘업무 작성갯수 1개이하 2회 느낌표알림, 2회이상 페널티카드 발동</span>
                              </div>
                              <div class="member_list_conts_setting list">
                                <div class="btn_switch<?=$setting['penalty_work']=="1"?" on":""?>" id="admin_work">
                                  <strong class="btn_switch_on"></strong>
                                  <span>버튼</span>
                                  <strong class="btn_switch_off"></strong>
                                </div>
                              </div>
                            </li>
                            <li>
                              <div class="set_list_title">
                                <strong>퇴근기록</strong>
                                <span>매주 퇴근기록 미작성 2회이상 느낌표알림, 3회이상 페널티 카드 발동</span>
                              </div>
                              <div class="member_list_conts_setting list">
                                <div class="btn_switch<?=$setting['penalty_out']=="1"?" on":""?>" id="admin_out">
                                  <strong class="btn_switch_on"></strong>
                                  <span>버튼</span>
                                  <strong class="btn_switch_off"></strong>
                                </div>
                              </div>
                            </li>
                            <li>
                              <div class="set_list_title">
                                <strong>챌린지</strong>
                                <span>챌린지 대상자이면서 2회이상 불참 느낌표알림, 3회이상 페널티 카드 발동</span>
                              </div>
                              <div class="member_list_conts_setting list">
                                <div class="btn_switch<?=$setting['penalty_challenge']=="1"?" on":""?>" id="admin_chall">
                                  <strong class="btn_switch_on"></strong>
                                  <span>버튼</span>
                                  <strong class="btn_switch_off"></strong>
                                </div>
                              </div>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
			</div>
			<div class="logo_btn" value="<?php echo $companyno?>">
					<button><span>등록하기</span></button>
				</div>
			</div>
				<!-- //콘텐츠 -->
				
			</div>
		</div>
	</div>

	<?php
	    include $home_dir . "/layer/member_repass.php";
	?>
	<?php
		//로딩 페이지
		include $home_dir . "loading.php";
	?>	

	<script type="text/javascript">
		$(document).ready(function(){

			// $(document).on("click", ".btn_list_reset", function () {
			// 	var val = $(this).val();

			// 	if(val){ 
			// 		if(confirm("\ "+val+"\ 님의 비밀번호를 초기화하시겠습니까?")){
			// 			location.href="https://rewardy.co.kr/etc/mem_repass.php?mode=passreset&email_str="+val;
			// 		}else{
			// 			alert("비밀번호 변경이 취소되었습니다."); 
			// 			parent.close();
			// 		}
			// 	}
			// });

			var list_leng = $(".rew_layer_team_management .tl_list > ul > li").length - 1;
			if(list_leng>4){
				$(".t_layer.rew_layer_team_management").css({"height":619,"marginTop":-320});
			}else{
				var list_lengx = 65*list_leng;
				$(".t_layer.rew_layer_team_management").css({"height":359+list_lengx,"marginTop":-(359+list_lengx)/2});
			}

			$(".btn_open_join").click(function(){
				$(".rew_layer_join").show();
			});
			$(".btn_open_login").click(function(){
				$(".rew_layer_login").show();
			});

			$(document).on("click", ".btn_open_repass", function () {
				$(".rew_layer_repass").show();
				var val = $(this).val();

				$(".input_001").val(val);
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
</div>
	<!-- footer start-->
	
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->

</body>


</html>
