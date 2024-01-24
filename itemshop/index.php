<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";

	date_default_timezone_set('Asia/Seoul');
	$time = date("Y-m-d H:i:s",time());;

	$sql = "select idx, file_path, file_name, fp_flag, item_price, item_title, item_date, item_effect, item_open_flag from work_member_character_img where state = 0 and fp_flag <> 0 order by item_open_flag";
	$item_list_sql = selectAllQuery($sql);

	$sql = "select a.idx, a.file_path, a.file_name, a.item_price, a.item_title, a.item_date, b.buy_date, b.item_kind_flag ";
 	$sql = $sql .= "from work_member_character_img a join work_item_info b on (a.idx = b.item_idx) ";
 	$sql = $sql .= "where a.state = 0 and b.state = 0 and a.fp_flag <> 0 and b.member_email = '".$user_id."'";
 	$sql = $sql .= " and (b.buy_date <= '".$time."' and b.end_date >= '".$time."' or b.item_kind_flag = 0) order by a.item_open_flag";
 	$my_item_list = selectAllQuery($sql);

 	$sql = "select profile_img_idx from work_member where email = '".$user_id."' and profile_type = 0 and state = 0 and companyno = '".$companyno."'";
 	$check_my_pro = selectQuery($sql);

?>
<body>
<script type="text/javascript">
	$(document).ready(function(){

	});

	
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

						<div class="rew_is_func">
							<div class="rew_is_func_in">
								<div class="rew_is_count">
									<span>아이템샵 목록</span>
									<strong><?=count($item_list_sql['idx'])?></strong>
								</div>
							</div>
						</div>

						<div class="rew_conts_scroll_13">

							<div class="is_list">
								<div class="is_list_in">
									<ul class="is_list_ul">
										<input type="hidden" id="item_idx">
										<?
											if($item_list_sql['idx']){
												for($i=0; $i<count($item_list_sql['idx']); $i++){
													$item_idx = $item_list_sql['idx'][$i];
													$item_file_path = $item_list_sql['file_path'][$i];
													$item_file_name = $item_list_sql['file_name'][$i];
													$item_price = $item_list_sql['item_price'][$i];
													$item_title = $item_list_sql['item_title'][$i];
													$item_date = $item_list_sql['item_date'][$i];
													$item_effect = $item_list_sql['item_effect'][$i];
													$item_open_flag = $item_list_sql['item_open_flag'][$i];
													$item_file_full_path = "";
													$open_layer = "";

													if($item_open_flag == 1){
														$pre_open = "class='off'";
													}else{
														$open_layer = "item_layer";
													}

													if($item_file_path && $item_file_name){
														$item_file_full_path = $item_file_path.$item_file_name;
													}

													if($item_date == 0){
														$item_period = "영구소장";
													}else{
														$item_date = ($item_date * 24);
														$item_period = $item_date."시간";
													}
											
										?>
												<li <?=$pre_open?>>
													<button class="is_box <?=$open_layer?>" value="<?=$item_idx?>">
														<div class="is_box_in">
															<div class="is_thumb">
																<div class="is_thumb_in">
																	<?if($item_file_full_path != ''){?>
																		<img src="<?=$item_file_full_path?>" alt="<?=$item_title?>" />
																	<?}?>
																</div>
															</div>
															<div class="is_exp">
																<dl>
																	<dt>아이템 : <?=$item_title?></dt>
																	<dd>가격 : <?=$item_price?> Coin</dd>
																	<dd>효과 : <?=$item_effect?></dd>
																	<dd>사용기간 : <?=$item_period?></dd>
																</dl>
															</div>
														</div>
													</button>
												</li>
											<?}?>
										<?}?>
									</ul>

								</div>
							</div>
						</div>

					</div>
				</div>
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>


	<!-- 아이템샵 레이어 -->
	<?
		include $home_dir . "/layer/item_img_buy.php";
	?>

	<script type="text/javascript">
		$(document).ready(function(){

			var ff_class = "";
			var ff_text = "";

			$(".btn_feeling_banner").click(function(){
				$(".feeling_first").show();
			});
			$(".ff_area button").click(function(){
				ff_class = $(this).attr("class");
				if($(this).attr("class") == "btn_ff_01"){ff_text="최고의";}
				if($(this).attr("class") == "btn_ff_02"){ff_text="뿌듯한";}
				if($(this).attr("class") == "btn_ff_03"){ff_text="기분 좋은";}
				if($(this).attr("class") == "btn_ff_04"){ff_text="감사한";}
				if($(this).attr("class") == "btn_ff_05"){ff_text="재밌는";}
				if($(this).attr("class") == "btn_ff_06"){ff_text="수고한";}
				if($(this).attr("class") == "btn_ff_07"){ff_text="무난한";}
				if($(this).attr("class") == "btn_ff_08"){ff_text="지친";}
				if($(this).attr("class") == "btn_ff_09"){ff_text="속상한";}
				$(".ff_area button").not(this).removeClass("on");
				$(this).addClass("on");
				$(".ff_bottom button").removeClass("btn_off").addClass("btn_on");
			});
			$(".ff_close button").click(function(){
				$(".feeling_first").hide();
				$(".ff_area button").removeClass("on");
				$(".ff_bottom button").removeClass("btn_on").addClass("btn_off");
			});

			$(".ff_bottom button").click(function(){
				if($(this).hasClass("btn_on")){
					$(".feeling_first").hide();
					$(".ff_area button").removeClass("on");
					$(".ff_bottom button").removeClass("btn_on").addClass("btn_off");
					$(".feeling_layer").show();
					$(".fl_area .fl_desc").removeClass().addClass("fl_desc");
					$(".fl_area .fl_desc").addClass(ff_class);
					$(".fl_area .fl_desc span").text(ff_text);
				}else{

				}
			});

			$(".fl_bottom button").click(function(){
				$(".feeling_layer").hide();
				$(".tdw_feeling_banner").removeClass().addClass("tdw_feeling_banner");
				$(".tdw_feeling_banner").addClass(ff_class);
				var inputfl = $(".fl_area .input_fl").val();
				$(".tdw_feeling_banner p").text(inputfl);
			});
			$(".fl_close button").click(function(){
				$(".feeling_layer").hide();
			});

			$(".btn_penalty_banner").click(function(){
				$(".penalty_layer").show();
			});
			$(".pl_close button").click(function(){
				$(".penalty_layer").hide();
			});
			$(".pl_img_on img").click(function(){
				$(".layer_cha_image").show();
			});
			$(".layer_cha_image .layer_cha_image_in, .layer_cha_image .layer_deam").click(function(){
				$(".layer_cha_image").hide();
			});

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
<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/Rewardy"; /* 페이지 이름 지정 Contents Path */
</script>

</body>


</html>
