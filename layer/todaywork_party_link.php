<?

//header페이지
$home_dir = __DIR__;
include $home_dir . "/inc_lude/header_index.php";

//파티 전체 갯수
$sql = "select count(1) as cnt from work_todaywork_project as a where a.state='0' and a.companyno='".$companyno."'";
$project_row = selectQuery($sql);
if($project_row){
	$total_count = $project_row['cnt'];
}
?>

<div class="party_link_layer" id="party_link_layer" style = "display : none;">
	<div class="pll_deam"></div>
	<div class="pll_in">
		<div class="pll_box" id="pll_box_party_link" value="">
			<div class="pll_box_in">
				<div class="pll_close" id="pll_close">
					<button><span>닫기</span></button>
					<input type="hidden" id="work_idx">
				</div>
				<div class="pll_top">
					<strong>파티 연결(<?=number_format($total_count);?>)</strong>
				</div>
				<div class="tdw_search">
					<button class="btn_tdw_search" id="btn_tdw_search"><span>검색</span></button>
				</div>
				<div class="live_drop_left">
					<div class="ldl_in">
						<?for($i=0; $i<count($project_my_info['idx']); $i++){
							$project_wdate = "";
							$project_idx = $project_my_info['idx'][$i];
							$project_info_title = $project_my_info['title'][$i];
							$project_info_sdate = $project_my_info['sdate'][$i];
							

							$project_ex_date = @explode("-", $project_info_sdate);
							$project_ex_year = $project_ex_date[0];
							$project_ex_month = $project_ex_date[1];
							$project_ex_day = $project_ex_date[2];

							$project_ex_time = @explode(":", $project_info_his);
							$project_ex_hh = $project_ex_time[0];
							$project_ex_ii = $project_ex_time[1];
							$project_wdate = $project_ex_month ."/". $project_ex_day;


						?>
						<div class="ldl_box<?=$project_link_info[$project_idx]?" on":""?>" id="ldl_box_<?=$i?>" value="<?=$project_idx?>">
							<div class="ldl_box_in">
								<div class="ldl_chk">
									<button id="ldl_chk" value="<?=$project_idx?>"><span>선택</span></button>
									<input type="hidden" class="party_name" value="<?=$project_info_title?>">
								</div>
								<div class="ldl_box_tit">
									<p><?=$project_info_title?></p>
								</div>
								<div class="ldl_box_time"><?=$project_wdate?></div>

								<div class="ldl_box_user">
									<ul>
										<?for($j=0; $j<count($project_user_list[$project_idx]['email']); $j++){
											
											$project_user_list_email = $project_user_list[$project_idx]['email'][$j];
											$project_user_list_name = $project_user_list[$project_idx]['name'][$j];
											$project_user_list_part = $project_user_list[$project_idx]['part'][$j];
											$project_user_list_profile_img = profile_img_info($project_user_list_email);
											if($user_id==$project_user_list_email){
												$li_class = ' class="ldl_me"';
											}else{
												$li_class = '';
											}

											if($j>2){
												$user_more_cnt = count($project_user_list[$project_idx]['email'])-3;
												$user_more ="<div class=\"cha_user_more\">+".$user_more_cnt."</div>";
											}
											?>

											<li <?=$li_class?> value="<?=$project_user_list_name?>">
												<div class="ldl_box_img" style="background-image:url(<?=$project_user_list_profile_img?>)" title="<?=$project_user_list_name?>"></div>
												<div class="ldl_box_user">
													<strong><?=$project_user_list_name?></strong>
													<span><?=$project_user_list_part?></span>
												</div>
											</li>
										<?}?>
									</ul>
								</div>
							</div>
						</div>
						<?}?>
					</div>
				</div>
				<div class="layer_party_btn">
					<!-- <button class="layer_party_all_slc"><span>전체선택</span></button> -->
					<button class="layer_party_cancel"><span>취소</span></button>
					<button class="layer_party_submit" id = "ppl_com_btn"><span>연결하기</span></button>
				</div>
				<!-- <div class="pll_btn">
					<button id="ppl_com_btn"><span>선택 완료</span></button>
					<button style="display:none;"><span>연결 해제</span></button>
					<button style="display:none;"><span>변경 완료</span></button>
				</div> -->
			</div>
		</div>
	</div>
</div>
<script>

function enterkey(){
		if (window.event.keyCode == 13) {
		
        var input_search = $("#input_part_search_work").val();
        

        var fdata = new FormData();
        
        fdata.append("mode", "party_my_info");
        fdata.append("search", input_search);
        

		
		console.log(input_search);
        $.ajax({
            type: "post",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/party_my_process.php',
            success: function(data) {
                // console.log(data);
				

				// console.log(tindex);
				var html = data;
                 $(".ldl_in").html(html);
            }
       	 });
		}
	}
	
	//파티검색 검색, 인풋박스 입력시

	$(document).on("click", "#input_search_btn_work", function(event) {
        var input_search = $("#input_part_search_work").val();
        if (!input_search) {
            alert("파티명을 입력해주세요.");
            $("#input_part_search_work").focus();
            return false;
        }

        var fdata = new FormData();
        
        fdata.append("mode", "party_my_info");
        fdata.append("search", input_search);
        

		
		console.log(input_search);
        $.ajax({
            type: "post",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/party_my_process.php',
            success: function(data) {
                // console.log(data);
				

				// console.log(tindex);
				var html = data;
                 $(".ldl_in").html(html);
            }
        });
    });

	
</script>