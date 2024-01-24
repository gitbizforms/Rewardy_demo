<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;

//mode값이 없을경우 중지처리
if(!$_POST["mode"]){
//	echo "out";
//	exit;
}

$mode = $_POST["mode"];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
}

if($mode == "item_img_layer"){

	$img_idx = $_POST['img_idx'];

	$sql = "select file_path, file_name, item_price, item_title, item_date from work_member_character_img where idx = '".$img_idx."'";
	$img_info = selectQuery($sql);

	if($img_info){
		$img_file_path = $img_info['file_path'];
		$img_file_name = $img_info['file_name'];
		$img_price = $img_info['item_price'];
		$img_title = $img_info['item_title'];
		$img_date = $img_info['item_date'];

		if($item_date == 0){
			$item_period = "영구소장";
		}else{
			$item_date = ($item_date * 24);
			$item_period = $item_date."시간";
		}

		$img_full_path = $img_file_path.$img_file_name;
	}

	?>
	<div class="is_layer_deam"></div>
		<div class="is_layer_in">
			<div class="is_layer_tit">
				<strong>Rewardy 아이템샵</strong>
			</div>
			<div class="is_layer_area">
				<div class="is_layer_list">
					<ul class="is_layer_ul">
						<li class="is_layer_li">
							<div class="is_profile_box">
								<div class="is_profile_img" style="background-image:url(<?=$img_full_path?>);"></div>
							</div>
							<div class="is_coin">
								<strong><span><?=$img_price?></span></strong>
							</div>
							<div class="is_desc">
								<ul>
									<li>아이템 : <?=$img_title?></li>
									<li>가격 : <?=$img_price?> Coin</li>
									<li>효과 : 내 프로필 캐릭터 변경</li>
									<li>사용기간 : <?=$item_period?></li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<div class="is_layer_btns">
				<button class="is_layer_btn_off"><span>취소</span></button>
				<button class="is_layer_btn_on" id="item_img_buy"><span>구매하기</span></button>
			</div>
		</div>
	<?
}

if($mode == "item_change"){
	$tem_idx = $_POST['tem_idx'];
	$def_flag = $_POST['def_flag'];

	if($def_flag == 1){
		$sql = "update work_member set profile_type = '0', profile_img_idx = '5' where email = '".$user_id."' and state = 0 and companyno = '".$companyno."'";
		$de_ch = updateQuery($sql);

		if($de_ch){
			echo "def";
			exit;
		}
	}

	$sql = "select item_idx,item_kind_flag from work_item_info where member_email = '".$user_id."' and state = 0 and item_idx = '".$tem_idx."'";
	$chang_item = selectQuery($sql);

	if($chang_item){
		$item_idx = $chang_item['item_idx'];
		$item_kind_flag = $chang_item['item_kind_flag'];

		if($item_kind_flag == 0){
			$sql = "update work_member set profile_type = '0', profile_img_idx = '".$item_idx."' where email = '".$user_id."' and state = 0 and companyno = '".$companyno."'";
			$update_img = updateQuery($sql);

			if($update_img){
				echo "complete";
				exit;
			}
		}
	}
}

?>