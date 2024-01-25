<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header_index.php";

  $type_flag = ($chkMobile)?1:0;
?>
<link rel="stylesheet" type="text/css" href="/html/css/set_01.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/all.min.css" />
<?
	if($user_level != '0'){
		header("Location:http://demo.rewardy.co.kr/index.php");
		exit;
	}

	//if( $user_level != '0'){
	//	alertMove("접속권한이 없습니다.");
	//	exit;
	//}

	//부서관리
	$team_info = member_part_info();

	//현재 회사 설정
	$sql = "select idx, penalty, penalty_in, penalty_work, penalty_out, penalty_challenge,comcoin, ";
  $sql = $sql .= "DATE_FORMAT(intime, '%H') as IH, DATE_FORMAT(intime, '%i') as Im, DATE_FORMAT(outtime, '%H') as OH, DATE_FORMAT(outtime, '%i') as Om";
  $sql = $sql .= " from work_company where idx = '".$companyno."' and state = '0' ";
	$setting = selectQuery($sql);

  //로고 디렉토리
  $sql = "select idx, state, file_path, file_name from work_company_logo_img where companyno = '".$companyno."' and state = '0'";
  $logo = selectQuery($sql);
  
  $logo_dir = $logo['file_path'].$logo['file_name'];
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
            <? include $home_dir . "/admin/admin_menubar.php";?>
						<div class="rew_conts_scroll_01">
              <div class="rew_member">
                <div class="rew_member_in">
                  <div class="rew_member_func">
                    <div class="rew_member_func_in">
                      <div class="rew_member_count">
                        <span>초기설정</span>
                      </div>
                    </div>
                  </div>
                  <div class="rew_member_list">
                    <div class="rew_member_list_in">
                      <div class="member_list_conts">
                        <div class="member_list_conts_in">
                          <!-- <div class="list_conts_top">
                            <div class="set_list_title">
                              <strong>회사 로고 이미지 설정</strong>
                              <span>세로 34px 이상의 배경이 투명한 이미지</span>
                            </div>
                            <div class="set_logo_file">
                              <div class="logo_file on">
                                <span class="file_name">logo_file.png</span>
                                <img id="previewImage" src="" alt="img" style="display:none";>
                                <input type="file" id="rew_logo"/>
                                <button class="file_down on"><span>저장</span></button>
                                <button class="file_down" style="display: none;"><span>삭제</span></button>
                              </div>
                            </div>
                          </div> -->
                          <div class="list_conts_top">
                            <div class="set_list_title">
                              <strong>1. 회사 로고 이미지 설정</strong>
                              <span>세로 34px 이상의 배경이 투명한 이미지</span>
                            </div>
                            <div class="set_logo">
                              <div class="set_logo_file file_01" style="display: none;">
                                <button class="logo_down"><span>12123124.png</span></button>
                                <button class="file_down"><span>삭제</span></button>
                              </div>
                              <div class="set_logo_file file_02">
                                <button class="logo_down"><span>파일첨부</span></button>
                                <input type="file" id="rew_logo" style="display: none;"/>
                                <input type="hidden" id="comp_no" value="<?=$companyno?>"/>
                                <button class="file_down"><span>저장</span></button>
                              </div>
                              <div class="set_logo_show">
                                <img src="<?=$logo_dir?>" id="previewImage" src="" alt="img" >
                              </div>
                            </div>
                          </div>
                          <div class="set_list set_list_time">
                              <div class="set_list_title">
                                <strong>2. 출퇴근 시간 설정</strong>
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
                              <button class="file_down on"><span>저장</span></button>
                            </div>

                            <div class="set_list set_list_coin">
                              <div class="set_list_title">
                                <strong>3. 공용코인 충전</strong>
                                <span>※ 공용코인은 구성원들에게 보상할 수 있는 가상머니입니다.
                                  <br>※ 회사의 정책에 따라서 출금신청을 한 구성원들에게 매월 급여에 포함하여 지급하거나 따로 성과포상을 하시면 됩니다. <a href="#">출금신청
                                    리스트 바로가기</a>
                                  <br>※ 공용코인 충전 시 5%의 충전수수료가 발생합니다.</span>
                              </div>
                              <div class="coin_show">
                                <label for="coin_input">현재 공용코인</label>
                                <input type="number" name="coin_input" class = "coin_input" placeholder="<?php echo number_format($setting['comcoin'])?>">
                                <button><span>충전하기</span></button>
                              </div>
                            </div>
                            <div class="set_list set_list_share">
                              <div class="set_list_title">
                                <strong>4. 자동 코인분배 설정</strong>
                              </div>
                              <div class="share_list">
                                <div class="share_mem share_list_cont">
                                  <h3>멤버 코인</h3>
                                  <div class="share_input">
                                    <input type="number" class = "share_mem_input" placeholder="금액을 입력해주세요.">
                                  </div>
                                  <button><span>설정하기</span></button>
                                  <div class="share_text">
                                    <span>※ 매월 초 모든 구성원들에게 설정한 금액의 공용코인이 자동으로 분배됩니다.
                                      <br>(매월 1일 자동충전)</span>
                                  </div>
                                </div>
                                <div class="share_en share_list_cont">
                                  <h3>역량 코인</h3>
                                  <div class="share_input">
                                    <input type="number"  class = "share_en_input" placeholder="금액을 입력해주세요.">
                                  </div>
                                  <button><span>설정하기</span></button>
                                  <div class="share_text">
                                    <span>※ 활발한 업무활동을 통해 역량 점수를 획득하는 구성원들에게 리워디Ai가 자동으로 코인을 분배하여 구성원들의 업무몰입도를 높여줍니다.
                                      <br>(매월 1일 자동충전)</span>
                                  </div>
                                </div>
                                <div class="share_like share_list_cont">
                                  <h3>좋아요 코인</h3>
                                  <div class="share_input">
                                    <input type="text"  class = "share_like_input" placeholder="금액을 입력해주세요.">
                                  </div>
                                  <button><span>설정하기</span></button>
                                  <div class="share_text">
                                    <span>※ 매월 “좋아요”를 받는 구성원들에게 리워디Ai가 자동으로 코인을 분배하여 구성원들의 업무 즐거움을 높여줍니다.
                                      <br>(매월 1일 자동충전)</span>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="set_list set_list_pena">
                              <div class="set_list_title">
                                <strong>5. 패널티 설정</strong>
                                <span>페널티를 받을 경우 좋아요와 코인을 24시간(00시~24시)동안 받지 못합니다.</span>
                              </div>
                              <div class="member_list_conts_setting all pena_btn_head">
                                <div class="btn_switch<?=$setting['penalty']=="1"?" on":""?>" id="admin_penalty">
                                  <strong class="btn_switch_on"></strong>
                                  <span>버튼</span>
                                  <strong class="btn_switch_off"></strong>
                                </div>
                              </div>
                              <ul style="<?=$setting['penalty']=="1"?" display:block;":""?>">
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

      if($type_flag == '1'){
        include $home_dir . "/payment/coin_pay/coin_pay_mo_pop.php";  
      }else{
        include $home_dir . "/payment/coin_pay/coin_pay_pop.php";
      }
	    include $home_dir . "/layer/member_repass.php";
	?>
	<?php
		//로딩 페이지
		include $home_dir . "loading.php";
	?>	
<script>
// function formatAmount(input) {
//   // Remove non-numeric characters
//   let value = input.value.replace(/[^0-9]/g, '');

//   // Add commas to the numeric value
//   value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

//   // Update the input value
//   input.value = value;
// }
</script>
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

      
		});
    $(document).ready(function () {

      $(".coin_show").click(function(){
        $('.rew_rec').show();
      });
      $('.share_list_cont').click(function () {
        $(this).addClass('on')
        $(this).siblings().removeClass('on')
      })

      $('.pena_btn_head .btn_switch').click(function () {
        if ($(this).hasClass('on') === true) {
          $('.set_list_pena ul').show()
        } else {
          $('.set_list_pena ul').hide()
        }

      })
    })
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
