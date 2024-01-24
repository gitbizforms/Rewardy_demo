<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

//연결된 도메인으로 분리
include $home_dir . "inc_lude/hader.php";
include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;


/*
print "<pre>";
print_r($_SERVER);
print "</pre>";
*/

//mode값이 없을경우 중지처리
if(!$_POST["mode"]){
	echo "out";
	exit;
}else{
	echo $_POST["mode"];
}

/*
print "<pre>";
print_r($_POST);
print "</pre>";
*/
//exit;

$mode = $_POST["mode"];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
}


//프로젝트 리스트
if($mode == "party_my_info"){

	
	
	$search = $_POST['search'];
	

	$whereis = "";
	//지연일수(1:1일이내, 3:3일간 이내, 7:7일간 업데이트 없음)
	

	if($search){
		$where_search = " and a.title like '%".$search."%'";
	}


	$sql = "select a.idx, a.state, a.title, a.email, com_coin_pro, date_format(a.regdate, '%Y-%m-%d %H:%i') as sdate, date_format(a.editdate, '%Y-%m-%d %H:%i') as udate, ";
	$sql = $sql .= "date_format(a.enddate, '%Y-%m-%d %H:%i') as edate, case when a.editdate is null then datediff(now(), a.regdate) when a.editdate is not null then datediff(a.editdate , a.regdate) end as reg, ";
	$sql = $sql .= "(select count(1) from work_todaywork_project_info where party_idx=a.idx and state!='9') as work, b.state as bstate ";
	$sql = $sql .= "from work_todaywork_project as a left join ";
	$sql = $sql .= "(select state, project_idx from work_project_like where state = 1 and email = '".$user_id."' and companyno='".$companyno."') ";
	$sql = $sql .= "as b on (a.idx = b.project_idx) where a.state='0' and a.companyno='".$companyno."' ";
	$sql = $sql .= $where_search;
	$sql = $sql .= "order by a.state asc, b.state desc,CASE WHEN a.state='0' THEN a.idx END desc";

	$project_my_info = selectAllQuery($sql);


	//전체 프로젝트 내역
	$sql = "select idx, project_idx, email, name, part from work_todaywork_project_user where state!='9' and companyno='".$companyno."' order by idx asc";
	//echo $sql;
	$project_user_info = selectAllQuery($sql);
	for($i=0; $i<count($project_user_info['idx']); $i++){
		$project_user_idx = $project_user_info['project_idx'][$i];
		$project_user_email = $project_user_info['email'][$i];
		$project_user_name = $project_user_info['name'][$i];
		$project_user_part = $project_user_info['part'][$i];
		$project_user_list[$project_user_idx]['email'][] = $project_user_email;
		$project_user_list[$project_user_idx]['name'][] = $project_user_name;
		$project_user_list[$project_user_idx]['part'][] = $project_user_part;
		$project_use[$project_user_idx][] = $project_user_email;
	}

	//파티 전체 갯수
	$sql = "select count(1) as cnt from work_todaywork_project as a where a.state='0' and a.companyno='".$companyno."' and a.title like '%".$search."%' ";
	$project_row = selectQuery($sql);
	if($project_row){
		$total_count = $project_row['cnt'];
	}
}
?>
<div class="party_link_layer" id="party_link_layer">
	<div class="pll_deam"></div>
	<div class="pll_in">
		<div class="pll_box" id="pll_box_party_link">
			<div class="pll_box_in">
				<div class="pll_close" id="pll_close">
					<button><span>닫기</span></button>
					<input type="hidden" id="work_idx">
				</div>
				<div class="pll_top">
					<strong>파티 연결(<?=number_format($total_count);?>)</strong>
				</div>
				<div class="pll_search">
						<div class="pll_search_box">
							<input type="text" class="input_search" placeholder="파티명 검색" id="input_part_search_party" onkeyup="enter()">
							<button id="input_search_btn_party"><span>검색</span></button>
						</div>
				</div>
				</div>
				<div class="live_drop_left">
					
					<div class="ldl_in">
						<?php
						if($project_my_info['idx']){
						?>
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
						<div class="ldl_box<?=$project_link_info[$project_idx]?" on":""?>" id="ldl_box">
							<div class="ldl_box_in">
								<div class="ldl_chk"><button id="ldl_chk" value="<?=$project_idx?>"><span>선택</span></button></div>
								<div class="ldl_box_tit">
									<p><?=$project_info_title?></p>
								</div>
								<div class="ldl_box_time"><?=$project_wdate?></div>

								<div class="ldl_box_user">
									<ul>
									<?for($j=0; $j<count($project_user_list[$project_idx]['email']); $j++){
										$user_cnt = 0;
										$project_user_list_email = $project_user_list[$project_idx]['email'][$j];
										$project_user_list_name = $project_user_list[$project_idx]['name'][$j];
										$project_user_list_part = $project_user_list[$project_idx]['part'][$j];
										$project_user_list_profile_img = profile_img_info($project_user_list_email);
										if($user_id==$project_user_list_email){
											$li_class = ' class="ldl_me"';
										}else{
											$li_class = '';
										}

										if($j>1){	
											$user_more_cnt = count($project_user_list[$project_idx]['email'])-1;	
											$user_more ="<div class=\"ldl_box_img cha_user_more\">+".$user_more_cnt."</div>";	
											$user_cnt = 1;	
										}		
										?>	
										<?if($j<1){?>	
											<li <?=$li_class?>>	
												<div class="ldl_box_img" style="background-image:url(<?=$project_user_list_profile_img?>)" title="<?=$project_user_list_name?>"></div>	
												<div class="ldl_box_user">	
													<strong><?=$project_user_list_name?></strong>	
													<span><?=$project_user_list_part?></span>	
												</div>	
											</li>	
										<?}?>	
									<?}?>	
									<?if($user_cnt == 1){?>	
										<li <?=$li_class?>>	
											<?=$user_more?>
										</li>
									<?}?>
									</ul>
								</div>
							</div>
						</div>
						<?}?>
						<?php }else{?>
						<div class="ldl_list_none">
							<strong><span>현재 생성된 파티가 없습니다.</span></strong>
						</div>
							<?php }?>
					</div>
				</div>
				<div class="layer_party_btn">
					<?if($project_link_btn==true){?>
						<button class="layer_party_cancel"><span>취소</span></button>
						<button style="display:;" id="party_link_edit" class="layer_party_change"><span>변경 완료</span></button>
					<?}else{?>
						<!-- <button class="layer_party_all_slc"><span>전체선택</span></button> -->
						<button class="layer_party_cancel"><span>취소</span></button>
						<button class="layer_party_submit" id="ppl_com_btn" value = "submit"><span>연결하기</span></button>
					<?}?>
				</div>
				
			</div>
		</div>
	</div>
</div>
<script>
	
	function enter(){
		if (window.event.keyCode == 13) {
		
        var input_search = $("#input_part_search_party").val();
        

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
	
	// 파티 연결 파티 검색 

	$(document).on("click", "#input_search_btn_party", function(event) {
        var input_search = $("#input_part_search_party").val();
        if (!input_search) {
            alert("파티명을 입력해주세요.");
            $("#input_part_search_party").focus();
            return false;
        }

        var fdata = new FormData();
        
        fdata.append("mode", "party_my_info");
        fdata.append("search", input_search);
        
        $.ajax({
            type: "post",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/party_my_process.php',
            success: function(data) {
              
				var html = data;
                 $(".ldl_in").html(html);
            }
        });
    });
</script>