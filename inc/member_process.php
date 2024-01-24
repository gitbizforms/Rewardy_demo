<?php

$home_dir = str_replace( basename(__DIR__) , "", __DIR__ );
// if($_SERVER['HTTP_HOST'] == "officeworker.co.kr"){

include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;

// }else{
// 	include $home_dir . "inc_lude/conf.php";
// 	include DBCON;
// 	include FUNC;
// }

//실서버
//include_once(__DIR__."\\PHPMailer\\PHPMailerAutoload.php");
include_once($home_dir."PHPMailer/libphp-phpmailer/PHPMailerAutoload.php");

/*
print "<pre>";
print_r($_SERVER);
print "</pre>";
*/

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


//관리자지정(관리자권한변경)
if($mode == "auth_change"){

	$sw_val = $_POST['sw_val'];
	$onf = $_POST['onf'];
	$sw_val = preg_replace("/[^0-9]/", "", $sw_val);

	if ($sw_val){

		//회원정보 조회
		$sql = "select idx from work_member where state in('0','1') and companyno='".$companyno."' and idx='".$sw_val."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){

			//관리자 권한으로 변경
			if($onf == 'true'){
				$highlevel = '0';
				$sql = "update work_member set highlevel='".$highlevel."', editdate=".DBDATE." where idx='".$mem_info['idx']."'";
				$up = updateQuery($sql);
				if($up){

					//쿠키 다시 생성
					if (is_numeric($highlevel) == true){
						setcookie('user_level', $highlevel , $cookie_limit_time , '/', C_DOMAIN);
					}
				}

			}else{
				$highlevel = '5';
				$sql = "update work_member set highlevel='".$highlevel."', editdate=".DBDATE." where idx='".$mem_info['idx']."'";
				$up = updateQuery($sql);
				if($up){

					//쿠키 다시 생성
					if (is_numeric($highlevel) == true){
						setcookie('user_level', $highlevel , $cookie_limit_time , '/', C_DOMAIN);
					}
				}
			}
		}

		if($up){
			echo "complete";
			exit;
		}
	}
	exit;
}


//멤버별공용코인 리스트
if($mode == "member_comcoin_list"){
	$tclass = $_POST['this_class'];
	$kind = $_POST['kind'];
	$member_search = $_POST['member_search'];
	
	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$tclass = str_replace("btn_sort_","",$tclass);
	$string = $_POST['string'];

	$sql = "select idx, email, name, companyno from work_member where email = '".$user_id."'";
	$member_idx = selectQuery($sql);
	$mem_idx = $member_idx['idx'];

	if($string){
		parse_str($string, $output);
		$page = $output['page'];
		$sdate= $output['sdate'];
		$edate= $output['edate'];
		$nday= $output['nday'];
		$type= $output['type'];
	}
	
	$where = " where state='0' and companyno='".$companyno."' and highlevel!='1'";

	if($member_search){
		$where = $where .= " and (name like '%".$member_search."%' or part like '%".$member_search."%')";
	}


	//전체 카운터수
	$sql = "select count(1) as cnt from work_member ".$where."";
	$comcoin_tot_info = selectQuery($sql);
	if($comcoin_tot_info['cnt']){
		$total_count = $comcoin_tot_info['cnt'];
	}

	if($total_count < 21){
		$p = 1;
	}

	print "<pre>";
	print_r($_POST);
	print "</pre>";

	if($tclass){
		if($tclass =='up'){
			$order = " asc";
		}elseif($tclass == 'down'){
			$order = " desc";
		}
	}else{
		$order = " asc";
	}

	//페이지


	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 20;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	//정렬기준
	if($kind){
		if($kind == 'name'){
			$qry = 'name';
		}else if($kind =='part'){
			$qry = 'part';
		}else if($kind == 'reg'){
			$qry = 'regdate';
		}else if($kind == 'auth'){
			$qry = 'highlevel';
		}else if($kind == 'email'){
			$qry = 'email';
		}else if($kind == 'comcoin'){
			$qry = 'comcoin';
		}else{
			$qry = 'idx';
		}
	}else{
		$qry  = "idx";
	}

	// $pageurl = "comcoin_mem";
	$string = "&page=".$page."&sdate=".$sdate."&edate=".$edate."&nday=".$nday."&type=".$type;


	if($_POST['this_class']){
		$string = $string .= "&this_class=".$_POST['this_class'];
	}

	if($_POST['kind']){
		$string = $string .= "&kind=".$_POST['kind'];
	}

	//공용코인

	// if($_SERVER['HTTP_HOST'] == T_DOMAIN){
		$sql = "select idx, email, name, part, partno, comcoin from work_member";
		$sql = $sql .= " ".$where."";
		$sql = $sql .= " order by ".$qry. $order;
		$sql = $sql .= " limit ". $startnum.", ".$pagesize;

		// echo $sql;
	// }else{
	// 	$sql = "select * from";
	// 	$sql = $sql .= " (select ROW_NUMBER() over(order by idx desc) as r_num, idx, email, name, part, partno, comcoin from work_member";
	// 	$sql = $sql .= "".$where.")";
	// 	$sql = $sql .= " as a where r_num between ". $startnum ." and " .$endnum ."";
	// 	$sql = $sql .= " order by ".$qry. $order;
	// }
	$comcoin_mem_info = selectAllQuery($sql);
	?>
	
	<div class="rew_member_list_in">
		<input id="page_num" value="<?=$p?>">
		<div class="member_list_header">
			<div class="member_list_header_in" id="member_list_comcoin">
				<input type="hidden" value="<?=$kind?>" id="kind">
				<input type="hidden" value="<?=$tclass?>" id="tclass">
				<input type="hidden" id="cli_kind" value="<?=$kind?>">
				<div class="member_list_header_name">
					<strong value="name">이름</strong>
					<em>
						<button class="btn_sort_up<?=$kind=="name" && $tclass=="up"?" on":""?>" title="오름차순"></button>
						<button class="btn_sort_down<?=$kind=="name" && $tclass=="down"?" on":""?>" title="내림차순"></button>
					</em>
				</div>
				<div class="member_list_header_team">
					<strong value="part">부서</strong>
					<em>
						<button class="btn_sort_up<?=$kind=="part" && $tclass=="up"?" on":""?>" title="오름차순"></button>
						<button class="btn_sort_down<?=$kind=="part" && $tclass=="down"?" on":""?>" title="내림차순"></button>
					</em>
				</div>
				<div class="member_list_header_email">
					<strong value="email">이메일</strong>
					<em>
						<button class="btn_sort_up<?=$kind=="email" && $tclass=="up"?" on":""?>" title="오름차순"></button>
						<button class="btn_sort_down<?=$kind=="email" && $tclass=="down"?" on":""?>" title="내림차순"></button>
					</em>
				</div>
				<div class="member_list_header_have_coin">
					<strong value="comcoin">보유한 코인</strong>
					<em>
						<button class="btn_sort_up<?=$kind=="comcoin" && $tclass=="up"?" on":""?>" title="오름차순"></button>
						<button class="btn_sort_down<?=$kind=="comcoin" && $tclass=="down"?" on":""?>" title="내림차순"></button>
					</em>
				</div>
				<div class="member_list_header_give">
					<strong>지급/회수</strong>
				</div>
				<div class="member_list_header_history">
					<strong>내역</strong>
				</div>
			</div>
		</div>
		<div class="member_list_conts">
			<div class="member_list_conts_in">
				<ul id="member_list_conts_ul">
					<input type="hidden" value="<?=$mem_idx?>" id="member_idx">
					<?
					if(count($comcoin_mem_info['idx'])> 0){
						for($i=0; $i<count($comcoin_mem_info['idx']); $i++){
							$comcoin_idx = $comcoin_mem_info['idx'][$i];
							$comcoin_email = $comcoin_mem_info['email'][$i];
							$comcoin_name = $comcoin_mem_info['name'][$i];
							$comcoin_part = $comcoin_mem_info['part'][$i];
							// $sql = "select count(idx) as cnt from work_coininfo where email = '".$comcoin_email."' and reward_user = '".$user_id."' and (code = 600 or code = 620)";
							// $use_list = selectQuery($sql);
							// $use = $use_list['cnt'];
							$comcoin_comcoin = $comcoin_mem_info['comcoin'][$i];
						?>
							<li>
								<div class="member_list_conts_name">
									<strong style="width: 47px;"><?=$comcoin_name?></strong>
								</div>
								<div class="member_list_conts_team">
									<strong style="width: 72px;"><?=$comcoin_part?></strong>
								</div>
								<div class="member_list_conts_email">
									<strong style="width: 177px;"><?=$comcoin_email?></strong>
								</div>
								<div class="member_list_conts_have_coin">
									<strong><?=number_format($comcoin_comcoin)?></strong>
								</div>
								<div class="member_list_conts_give">
									<button class="btn_list_give" id="btn_list_give" value="<?=$comcoin_idx?>"><span>지급</span></button>
									<button class="btn_list_debt" id="btn_list_debt" value="<?=$comcoin_idx?>"><span>회수</span></button>
								</div>
								<div class="member_list_conts_history">
									<form action="comcoin_mem_list.php" method="POST">
										<input type="hidden" id="history_idx" name="email" class="history_idx" value="<?=$comcoin_email?>">
										<?  if($comcoin_comcoin>=0){?>
											<button type="submit" id="btn_list_history" class="btn_list_history<?=$comcoin_comcoin?>" value=<?=$comcoin_email?> ><span>내역</span></button>
										<?}else{?>
											<button type="submit" id="btn_list_history" class="btn_list_history<?=$comcoin_comcoin==0?" zero":""?>" value=<?=$comcoin_email?> disabled style="cursor:default"><span>내역</span></button>
										<?}?>
									</form>
								</div>
							</li>
						<?}?>
					<?}else{?>
						<li class="search_list_none"><span>검색된 결과가 없습니다.</span></li>
					<?}?>
				</ul>
			</div>
		</div>
	</div>|
	<?if($comcoin_mem_info['idx']){?>
		<div class="rew_ard_paging_in">
			<? if($member_search){?>
				<input type="hidden" id="member_search" value="<?=$member_search?>">
			<?}
				//페이징사이즈, 전체카운터, 페이지출력갯수
				echo pageing($pagingsize, $total_count, $pagesize, $string);
			?>
		</div>
	<?}?>|<?=$total_count?>
<?php
	exit;
}

//정렬
if($mode == "member_list"){
	//페이지
	$member_search = $_POST['member_search'];
	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 20;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	$url = "member_list";
	$string = "&page=".$url;

	$tclass = $_POST['this_class'];
	$kind = $_POST['kind'];
	

	$tclass = str_replace("btn_sort_","",$tclass);

	if($tclass){
		if($tclass =='up'){
			$order = " asc";
		}else{
			$order = " desc";
		}
	}else{
		$order = " asc";
	}

	//정렬기준
	if($kind){
		if($kind == 'name'){
			$qry = 'name';
		}else if($kind =='part'){
			$qry = 'part';
		}else if($kind == 'reg'){
			$qry = 'regdate';
		}else if($kind == 'auth'){
			$qry = 'highlevel';
		}else if($kind == 'email'){
			$qry = 'email';
		}else{
			$qry = 'idx';
		}
	}else{
		$qry  = "idx";
	}

	if($member_search!=""){
		$where = " and (name like '%".$member_search."%' or part like '%".$member_search."%' or email like '%".$member_search."%')";
	}elseif($member_search==""){
		$where = "";
	}

	$sql = "select count(*) as cnt from work_member where state in('0','1') and companyno='".$companyno."' and highlevel!='1'".$where."";
	$member_count_info = selectQuery($sql);
	
	if($member_count_info['cnt']){
		$total_count = $member_count_info['cnt'];
	}
	
	if($total_count < 21){
		$p = 1;
	}

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select idx, state, email, name, highlevel, company, part, partno, DATE_FORMAT(regdate, '%Y-%m-%d') as reg from work_member";
	$sql = $sql .=" where state in('0','1') and companyno='".$companyno."' and highlevel!='1'".$where."";
	$sql = $sql .=" order by ".$qry. $order;
	$member_info_cnt = selectAllQuery($sql);
	$sql = $sql .= " limit ". $startnum.", ".$pagesize;
	$member_info = selectAllQuery($sql);

	

	//부서관리
	$sql = "select idx, partname from work_team where state in('0','1') and companyno='".$companyno."' order by idx desc";
	$team_info = selectAllQuery($sql);
	?>
<div class="list_paging" id="list_paging">
	<input id="page_num" value="<?=$p?>">
	<div class="member_list_conts">
		<div class="member_list_conts_in">
			<ul id="member_list_conts_ul">
		<?if(count($member_info['idx']) > 0 ){
			for($i=0; $i<count($member_info['idx']); $i++){
				$mem_idx = $member_info['idx'][$i];
				$highlevel = $member_info['highlevel'][$i];
				$reg = $member_info['reg'][$i];
				$partno = $member_info['partno'][$i];
			?>
				<li id="li_<?=$mem_idx?>">
					<div class="member_list_conts_name" id="member_list_conts_name_<?=$mem_idx?>">
						<strong><?=$member_info['name'][$i]?></strong>
					</div>
					<div class="member_list_conts_team" id="member_list_conts_team_<?=$mem_idx?>">
						<strong><?=$member_info['part'][$i]?></strong>
						<div class="rew_member_list_sort" id="rew_member_list_sort_<?=$mem_idx?>" style="display:none;">
							<div class="rew_member_list_sort_in">
								<button class="btn_sort_on" id="member_team_<?=$mem_idx?>"><span><?=$member_info['part'][$i]?></span></button>
								<input type="hidden" id="ch_part_no_<?=$mem_idx?>" value="<?=$partno?>">
								<ul>
									<?for($j=0; $j<count($team_info['idx']); $j++){
										$part_idx = $team_info['idx'][$j];
										$partname = $team_info['partname'][$j];
									?>
									<li><button value="<?=$part_idx?>"><span><?=$partname?></span></button></li>
									<?}?>
								</ul>
							</div>
						</div>
					</div>

					<div class="member_list_conts_email" id="member_list_conts_email_<?=$mem_idx?>">
						<strong><?=$member_info['email'][$i]?></strong>
					</div>

					<?if($member_info['state'][$i]=='0'){?>

						<div class="member_list_conts_date">
							<strong><?=$reg?></strong>
						</div>

					<?}else if($member_info['state'][$i]=='1'){?>
						<div class="member_list_conts_date">
							<strong>초대중</strong>
							<button class="btn_list_email" id="btn_list_email_<?=$member_info['idx'][$i]?>"><span>메일 재발송</span></button>
						</div>
					<?}?>

					<div class="member_list_conts_admin">
						<div class="btn_switch<?=$highlevel=="0"?" on":""?>" value="<?=$mem_idx?>" id="sw_idx">
							<strong class="btn_switch_on"></strong>
							<span>버튼</span>
							<strong class="btn_switch_off"></strong>
						</div>
					</div>
					<div class="member_list_conts_function">
						<button class="btn_list_regi" id="btn_list_regi_<?=$mem_idx?>" value="<?=$mem_idx?>"><span>수정</span></button>
						<button class="btn_list_del" id="btn_list_del_<?=$mem_idx?>" value="<?=$mem_idx?>"><span>삭제</span></button>
						<button class="btn_list_reset btn_open_repass" id="btn_list_reset_<?=$mem_idx?>" value="<?=$member_info['email'][$i]?>" ><span>초기화</span></button>
						<div class="btn_member_list" id="btn_member_list_<?=$mem_idx?>" style="display:none">
							<button class="btn_list_ok" id="btn_list_ok"><span>확인</span></button>
							<button class="btn_list_cancel" id="btn_list_cancel"><span>취소</span></button>
						</div>
					</div>
				</li>
			<?}?>
				<?}else{?>
					<li class="search_list_none"><span>검색된 결과가 없습니다.</span></li>
				<?}?>
			</ul>
		</div>
	</div>
	<?if($member_info['idx']){?>
	<div class="rew_ard_paging" id="rew_ard_paging">
		<div class="rew_ard_paging_in">
			<input type="hidden" id="tclass" value="<?=$tclass?>">
			<input type="hidden" id="kind" value="<?=$kind?>">
			<?
				//페이징사이즈, 전체카운터, 페이지출력갯수
				echo pageing($pagingsize, $total_count, $pagesize, $string);
			?>
		</div>
	</div>
	<?}?>
</div>	
		|<?=count($member_info_cnt['idx'])?>
<?php
	exit;
}


//부서명관리 > 부서명 변경
if($mode == "member_team"){

	$team_idx = $_POST['team_idx'];
	$team_idx = preg_replace("/[^0-9]/", "", $team_idx);
	$input_team = $_POST['input_team'];
	if($team_idx){

		$sql = "select idx from work_team where state='0' and companyno='".$companyno."' and idx='".$team_idx."'";
		$team_info = selectQuery($sql);
		if($team_info['idx']){
			$sql = "update work_team set partname='".$input_team."', editdate=".DBDATE." where state = '0' and idx='".$team_info['idx']."'";
			$up = updateQuery($sql);

			$sql = "update work_member set part='".$input_team."', editdate=".DBDATE." where state = '0' and partno='".$team_info['idx']."'";
			$wm_up = updateQuery($sql);
			if($up){
				echo "complete";
				exit;
			}
		}
	}

	exit;
}


//부서명관리 > 부서명 추가
if($mode == "team_add"){

	$team_name = $_POST['team_name'];
	if($team_name){
		$sql = "select idx from work_team where state='0' and companyno='".$companyno."' and partname='".$team_name."'";
		$team_info = selectQuery($sql);
		if($team_info['idx']){

			echo "not";
			exit;
		}else{

			$sql = "insert into work_team(companyno,partname,ip) values('".$companyno."','".$team_name."','".LIP."')";
			$insert_idx = insertIdxQuery($sql);
			if($insert_idx){

				$sql = "select idx, partname from work_team where state='0' and companyno='".$companyno."'  order by idx desc";
				$team_list_info = selectAllQuery($sql);

				for($i=0; $i<count($team_list_info['idx']); $i++){

					$team_list_idx = $team_list_info['idx'][$i];
					$team_list_partname = $team_list_info['partname'][$i];
			?>
				<li>
					<div class="tc_input" value="<?=$i?>">
						<div class="team_area" id="team_area_0" value="<?=$team_list_idx?>">
							<input type="text" class="input_team" id="input_team_<?=$i?>" disabled="" value="<?=$team_list_partname?>">
							<button class="btn_team_regi" id="btn_team_regi_<?=$i?>" value="<?=$team_list_idx?>"><span>변경</span></button>
							<button class="btn_team_del" id="btn_team_del_<?=$i?>" value="<?=$team_list_idx?>"><span>삭제</span></button>
						</div>
					</div>
				</li>
			<?php
				}

			}

		}
	}

	exit;
}



//부서명 삭제
if($mode == "team_del"){

	$team_idx = $_POST['team_idx'];
	$team_idx = preg_replace("/[^0-9]/", "", $team_idx);
	if($team_idx){

		$sql = "select idx from work_team where state='0' and companyno='".$companyno."' and idx='".$team_idx."'";
		$team_info = selectQuery($sql);

		
		if($team_info['idx']){
			$sql = "update work_team set state='9', editdate=".DBDATE." where idx='".$team_idx."'";
			$up = updateQuery($sql);
			if($up){
				echo "complete|";

				$sql = "select idx as partno, partname as part from work_team where state='0' and companyno='".$companyno."' order by idx desc";
				$part_info = selectAllQuery($sql);
			}
		} ?>
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_tit">
				<strong>부서명 관리</strong>
				<span>부서명을 등록하고 관리하세요!</span>
			</div>
			<div class="tl_list" id="tl_list">
				<ul>
					<?for($i=0; $i<count($part_info['partno']); $i++){
						$part_idx = $part_info['partno'][$i];
						$partname = $part_info['part'][$i];
					?>
					<li>
						<div class="tc_input" value="<?=$i?>">
							<div class="team_area" id="team_area_<?=$i?>" value="<?=$part_idx?>">
								<input type="text" class="input_team" id="input_team_<?=$i?>" disabled value="<?=$partname?>" />
								<button class="btn_team_regi" id="btn_team_regi_<?=$i?>" value="<?=$part_idx?>"><span>변경</span></button>
								<button class="btn_team_del" id="btn_team_del_<?=$i?>" value="<?=$part_idx?>"><span>삭제</span></button>
							</div>
						</div>
					</li>
					<?}?>
				</ul>
			</div>
			<div class="tl_btn_team">
				<input type="text" placeholder="부서명" id="team_add"/>
				<input type="hidden" id="team_real"/>
				<button id="tl_btn_team_add"><span>추가하기</span></button>
			</div>
		</div>
	<?}
	exit;
}



//맴버 삭제(회원정보 삭제)
if($mode == "member_del"){

	$mem_idx = $_POST['mem_idx'];
	$mem_idx = preg_replace("/[^0-9]/", "", $mem_idx);

	//관리자 권한일경우
	if($user_level == '0'){

		if($mem_idx){
			$sql = "select idx from work_member where companyno='".$companyno."' and idx='".$mem_idx."'";
			$mem_info = selectQuery($sql);
			if($mem_info['idx']){

				$sql = "update work_member set state='9', editdate=".DBDATE." where idx='".$mem_info['idx']."'";
				$up = updateQuery($sql);
				if($up){
					echo "complete";
					exit;
				}
			}
		}

	}else{
		echo "auth_not";
		exit;
	}
	exit;
}

// 코인출금 신청내역
if($mode == "comcoin_list"){
	$pageurl = "admin";

	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];
	$type = $_POST['type'];
	$kind = $_POST['kind'];
	$string = $_POST['string'];
	$tclass = $_POST['this_class'];

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	if($string){
		parse_str($string, $output);
		$nday= $output['nday'];
		$sdate=$output['sdate'];
		$edate=$output['edate'];
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 20;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize ;
	}

	$string = "&page=".$pageurl."&sdate=".$sdate."&edate=".$edate."&nday=".$nday."&type=".$type;

	//공용코인
	$where = " where state = '0' and companyno='".$companyno."' ";
	if($sdate && $edate){
	$where = $where .= " and workdate between '".$sdate."' and '".$edate."'";
	}
				
	if($kind){
		if($kind == 'workdate'){
			$qry = " workdate ";
		}elseif($kind == 'sortname'){
			$qry = " name ";
		}
	}else{
		$qry = " idx ";
	}

	if($tclass){
		if($tclass == 'btn_sort_down'){
			$order = " desc ";
		}elseif($tclass == 'btn_sort_up'){
			$order = " asc ";
		}
	}else{
		$order = " desc ";
	}
	
	$sql = "select idx, state, bank_name, bank_num, workdate, code, email, name, reward_user, reward_name, coin, coin_out, coin_type, memo, DATE_FORMAT(regdate, '%Y.%m.%d') AS ymd, DATE_FORMAT(regdate, '%h:%i:%s') AS his, regdate, commission, amount";
	$sql = $sql .= " from work_account_info";
	$sql = $sql .= " ".$where."";
	$sql = $sql .= " order by ". $qry." ".$order;
	$sql = $sql .= " limit ". $startnum.", ".$pagesize;
	$comcoin_info = selectAllQuery($sql);

	?>

	<div id="list_paging">
		<div class="member_list_conts">
			<div class="member_list_conts_in">
				<ul class="member_list_conts_ul">
					<?php
					
					//전체 카운터수
					$sql = "select count(idx) as cnt from work_account_info ".$where."";
					$comcoin_cnt_info = selectQuery($sql);
					if($comcoin_cnt_info['cnt']){
						$total_count = $comcoin_cnt_info['cnt'];
					}

					if(count($comcoin_info['idx']) > 0){
					for($i=0; $i<count($comcoin_info['idx']); $i++){
							$name = $comcoin_info['name'][$i];
							$work_date = $comcoin_info['workdate'][$i];
							$bank_name = $comcoin_info['bank_name'][$i];
							$bank_num = $comcoin_info['bank_num'][$i];
							$amount = $comcoin_info['amount'][$i];

							if($comcoin_info['state'][$i]==0){
								$state = '확인중';
							}elseif($comcoin_info['state'][$i]==1){
								$state = '출금 대기';
							}elseif($comcoin_info['state'][$i]==9){
								$state = '출금 완료';
							}elseif($comcoin_info['state'][$i]==3){
								$state = '출금 반려';
							}
						?>
							<input type="hidden" value="<?=$sql?>"> 
							<li>
								<div class="member_list_conts_date">
									<strong><?=$work_date?></strong>
								</div>
								<div class="member_list_conts_deposit">
									<strong><?=$name?></strong>
								</div>
								<div class="member_list_conts_deposit">
									<strong><?=$bank_name?></strong>
								</div>
								<div class="member_list_conts_coin">
									<strong><?=$bank_num?></strong>
								</div>
								<div class="member_list_conts_coin">
									<strong><?=number_format($comcoin_info['coin'][$i])?></strong>
								</div>
								<div class="member_list_conts_coin">
									<strong><?=number_format($comcoin_info['commission'][$i])?></strong>
								</div>
								<div class="member_list_conts_coin">
									<strong><?=number_format($amount)?></strong>
								</div>
								<div class="member_list_conts_deposit">
									<strong style="color:grey"><?=$state?></strong>
								</div>
							</li>
						<?php
						}?>
						<!-- <li class="search_list_none"><span><?$sql."/".$kind."/".$tclass."/".$string?></span></li> -->
					<?php
					}else{?>
						<li class="search_list_none"><span>코인출금 신청내역이 없습니다.</span></li>
					<?php
					}?>
				</ul>
			</div>
			<input type="hidden" value="<?=$kind?>" id="kind">
			<input type="hidden" value="<?=$tclass?>" id="tclass">
		</div>
		<?if($comcoin_info['idx']){?>
		<div class="rew_ard_paging">
			<div class="rew_ard_paging_in">
				<?
					//페이징사이즈, 전체카운터, 페이지출력갯수
					echo pageing($pagingsize, $total_count, $pagesize, $string);
				?>
			</div>
		</div>
		<? }?>
	</div>
	<?exit;
}

// 공용코인 출금내역 달력 미적용
if($mode == "comcoin_list_nocal"){
	$pageurl = "member_nocal";
	$type = $_POST['type'];
	$kind = $_POST['kind'];
	$string = $_POST['string'];
	$tclass = $_POST['this_class'];

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 20;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize ;
	}

	$string = "&page=".$pageurl."&type=".$type;

	//공용코인
	$where = " where state = '0' and companyno='".$companyno."' ";
				
	if($kind){
		if($kind == 'workdate'){
			$qry = " workdate ";
		}elseif($kind == 'sortname'){
			$qry = " name ";
		}
	}else{
		$qry = " idx ";
	}

	if($tclass){
		if($tclass == 'btn_sort_down'){
			$order = " desc ";
		}elseif($tclass == 'btn_sort_up'){
			$order = " asc ";
		}
	}else{
		$order = " desc ";
	}
	
	$sql = "select idx, state, bank_name, bank_num, workdate, code, email, name, reward_user, reward_name, coin, coin_out, coin_type, memo, DATE_FORMAT(regdate, '%Y.%m.%d') AS ymd, DATE_FORMAT(regdate, '%h:%i:%s') AS his, regdate, commission, amount";
	$sql = $sql .= " from work_account_info";
	$sql = $sql .= " ".$where."";
	$sql = $sql .= " order by ". $qry." ".$order;
	$sql = $sql .= " limit ". $startnum.", ".$pagesize;
	$comcoin_info = selectAllQuery($sql);

	?>
	<div class="list_paging" id="list_paging">
		<div class="member_list_conts">
			<div class="member_list_conts_in">
				<ul class="member_list_conts_ul">
					<?php
					
					//전체 카운터수
					$sql = "select count(idx) as cnt from work_account_info ".$where."";
					$comcoin_cnt_info = selectQuery($sql);
					if($comcoin_cnt_info['cnt']){
						$total_count = $comcoin_cnt_info['cnt'];
					}

					if(count($comcoin_info['idx']) > 0){
					for($i=0; $i<count($comcoin_info['idx']); $i++){
							$name = $comcoin_info['name'][$i];
							$work_date = $comcoin_info['workdate'][$i];
							$bank_name = $comcoin_info['bank_name'][$i];
							$bank_num = $comcoin_info['bank_num'][$i];
							$amount = $comcoin_info['amount'][$i];
							if($comcoin_info['state'][$i]==0){
								$state = '확인중';
							}elseif($comcoin_info['state'][$i]==1){
								$state = '출금 대기';
							}elseif($comcoin_info['state'][$i]==9){
								$state = '출금 완료';
							}elseif($comcoin_info['state'][$i]==3){
								$state = '출금 반려';
							}
						?>
							<input type="hidden" value="<?=$sql?>"> 
							<li>
								<div class="member_list_conts_date">
									<strong><?=$work_date?></strong>
								</div>
								<div class="member_list_conts_deposit">
									<strong><?=$name?></strong>
								</div>
								<div class="member_list_conts_deposit">
									<strong><?=$bank_name?></strong>
								</div>
								<div class="member_list_conts_coin">
									<strong><?=$bank_num?></strong>
								</div>
								<div class="member_list_conts_coin">
									<strong><?=number_format($comcoin_info['coin'][$i])?></strong>
								</div>
								<div class="member_list_conts_coin">
									<strong><?=number_format($comcoin_info['commission'][$i])?></strong>
								</div>
								<div class="member_list_conts_coin">
									<strong><?=number_format($amount)?></strong>
								</div>
								<div class="member_list_conts_deposit">
									<strong style="color:grey"><?=$state?></strong>
								</div>
							</li>
						<?php
						}?>
						<!-- <li class="search_list_none"><span><?$sql."/".$kind."/".$tclass."/".$string?></span></li> -->
					<?php
					}else{?>
						<li class="search_list_none"><span>코인출금 신청내역이 없습니다.</span></li>
					<?php
					}?>
				</ul>
			</div>
			<input type="hidden" value="<?=$kind?>" id="kind">
			<input type="hidden" value="<?=$tclass?>" id="tclass">
		</div>
		<?if($comcoin_info['idx']){?>
			<div class="rew_ard_paging">
				<div class="rew_ard_paging_in">
					<?
						//페이징사이즈, 전체카운터, 페이지출력갯수
						echo pageing($pagingsize, $total_count, $pagesize, $string);
					?>
				</div>
			</div>
		<?}?>
	</div>
	</div>
	<?exit;
}
	
// 멤버별 공용코인 지급,회수 내역 2023.01.27 ▽
if($mode == "history_list"){
	$tclass = $_POST['this_class'];
	$sort_kind = $_POST['sort_kind'];
	// $tclass = str_replace("btn_sort_","",$tclass);

	if($tclass){
		if($tclass =='btn_sort_up'){
			$order = " asc";
		}elseif($tclass == 'btn_sort_down'){
			$order = " desc";
		}
	}else{
		$order = " desc";
	}

	//정렬기준
	if($sort_kind){
		$qry = "workdate";
	}else{
		$qry = "regdate";
	}

	$pageurl = "history";
	$email = $_POST['email'];

	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];
	$nday = $_POST['nday'];
	$string = $_POST['string'];
	$type = $_POST['type'];
	

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 10;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	if($string){
		parse_str($string, $output);
		$url = $output['page'];
		$sdate = $output['sdate'];
		$edate = $output['edate'];
		$nday = $output['nday'];
		$type = $output['type'];
	}

	$url = "history";
	$string = "&page=".$url."&sdate=".$sdate."&edate=".$edate."&nday=".$nday."&type=".$type;

	//일주일
	$week7 = date("Y-m-d",strtotime("-1 week", TODAYTIME));

	if($sdate && $edate){
		$where = "where email = '".$email."' and state='0' and workdate between '".$sdate."' and '".$edate."'";
	}else{
		$where = "where email = '".$email."' and state='0'";
	}

	
	$kind = $_POST['kind'];
	if($kind=="all"){
		$where = $where .= " and code in (510,520,600,620,710) ";
		$kindname = "전체보기";
	}elseif($kind=="in"){
		$where = $where .= " and code in (510,600) ";
		$kindname = "지급";
	}elseif($kind=="out"){
		$where = $where .= " and code in (520,620,710) ";
		$kindname = "차감";
	}else{
		$where = $where .= " and code in (510,520,600,620,710) ";
		$kindname = "전체보기";
	}
	
	$sql = "select idx,name,email,reward_user,reward_name,coin,coin_out,code,memo,workdate";
	$sql = $sql .= " from work_coininfo";
	$sql = $sql .= " ".$where."";
	$sql = $sql .= " order by ". $qry." ".$order;
	$sql = $sql .= " limit ". $startnum.", ".$pagesize;

	$history_info = selectAllQuery($sql);

	$sql = "select count(*) as cnt from work_coininfo ". $where;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){
		$total_count = $history_info_cnt['cnt'];
	}

	$sql2 = "select name, comcoin, email from work_member where email = '".$email."'";
	$work_member = selectQuery($sql2);

	if($sdate && $edate){
		$sdate = $_POST['sdate'];
		$edate = $_POST['edate'];
	}else{
		$sdate = $week7;
		$edate = TODATE;
	}
	?>
	<div class="list_paging" id="list_paging">
		<div class="member_list_conts" id="member_list_conts">
		<input type="hidden" id="email_history" value="<?=$email?>">
		<input type="hidden" id="kind" value="<?=$kind?>">
			<div class="member_list_conts_in">
				<ul class="member_list_conts_ul">
					<?
					if(count($history_info['idx'])!=0){
						for($i=0; $i<count($history_info['idx']); $i++){
							$id = $history_info['email'][$i];
							$name = $history_info['name'][$i];
							$workdate = $history_info['workdate'][$i];
							$coin = $history_info['coin'][$i];
							$memo = $history_info['memo'][$i];
							if(mb_strlen($memo)>=9){
								$memo = mb_substr($memo,0,9,'utf-8')."..";
							}else{
								$memo = $memo;
							}
							$number_coin = number_format($coin);
							$code = $history_info['code'][$i];
							if(in_array($code,array('600','510'))){
								$kind = "지급";
							}elseif(in_array($code,array('520','620','710'))){
								$kind = "차감";
							}
							$reward_user = $history_info['reward_name'][$i];
							?>
							<li>
								<div class="member_list_conts_deposit">
									<strong><?=$kind?></strong>
								</div>
								<div class="member_list_conts_date">
									<strong><?=$workdate?></strong>
								</div>
								<div class="member_list_conts_deposit">
									<strong><?=$reward_user?></strong>
								</div>
								<div class="member_list_conts_deposit">
									<strong><?=$name?></strong>
								</div>
								<? if(in_array($code,array('510','600'))){?>
									<div class="member_list_conts_coin">
										<strong style="color:blue;font-weight:bold;"><?="+".$number_coin?></strong>
									</div>
								<?}elseif(in_array($code,array('520','620','710'))){?>
									<div class="member_list_conts_coin">
										<strong style="color:red;font-weight:bold;"><?="-".$number_coin?></strong>
									</div>
								<?}?>
								<div class="member_list_conts_deposit">
									<strong><?=$memo?></strong>
								</div>
							</li>
						<?}
					}else{?>
					<li class="search_list_none"><span>조회 결과가 없습니다.</span></li>
					<?}?>
				</ul>
			</div>
		</div>
		<?if($history_info['idx']){?>
			<div class="rew_ard_paging" id="rew_ard_paging">
				<div class="rew_ard_paging_in">
					<input type="hidden" id="history_email" value="<?=$email?>">
					<input type="hidden" id="tclass" value="<?=$tclass?>">
					<?
						//페이징사이즈, 전체카운터, 페이지출력갯수
						echo pageing($pagingsize, $total_count, $pagesize, $string);
					?>
				</div>
			</div>
		<?}?>
		</div>
			
	<? exit;
}

if($mode == "history_list_nocal"){
	$tclass = $_POST['this_class'];
	$sort_kind = $_POST['sort_kind'];
	// $tclass = str_replace("btn_sort_","",$tclass);

	if($tclass){
		if($tclass =='btn_sort_up'){
			$order = " asc";
		}elseif($tclass == 'btn_sort_down'){
			$order = " desc";
		}
	}else{
		$tclass = "btn_sort_down";
		$order = " desc";
	}

	//정렬기준
	if($sort_kind){
		$qry = "workdate";
	}else{
		$qry = "regdate";
	}

	$pageurl = "history";
	$email = $_POST['email'];
	$string = $_POST['string'];
	$type = $_POST['type'];
	

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 10;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	if($string){
		parse_str($string, $output);
		$url = $output['page'];
		$type = $output['type'];
	}

	$url = "history_nocal";
	$string = "&page=".$url."&sdate=".$sdate."&edate=".$edate."&nday=".$nday."&type=".$type;

	//일주일
	$week7 = date("Y-m-d",strtotime("-1 week", TODAYTIME));

	$where = "where email = '".$email."' and state='0'";

	$kind = $_POST['kind'];
	if($kind=="all"){
		$where = $where .= " and code in (510,520,600,620,710) ";
		$kindname = "전체보기";
	}elseif($kind=="in"){
		$where = $where .= " and code in (510,600) ";
		$kindname = "지급";
	}elseif($kind=="out"){
		$where = $where .= " and code in (520,620,710) ";
		$kindname = "차감";
	}else{
		$where = $where .= " and code in (510,520,600,620,710) ";
		$kindname = "전체보기";
	}

	$sql = "select idx,name,email,reward_user,reward_name,coin,coin_out,code,memo,workdate";
	$sql = $sql .= " from work_coininfo";
	$sql = $sql .= " ".$where."";
	$sql = $sql .= " order by ". $qry." ".$order;
	$sql = $sql .= " limit ". $startnum.", ".$pagesize;

	$history_info = selectAllQuery($sql);

	$sql = "select count(*) as cnt from work_coininfo ". $where;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){
		$total_count = $history_info_cnt['cnt'];
	}

	$sql2 = "select name, comcoin, email from work_member where email = '".$email."'";
	$work_member = selectQuery($sql2);

	$sdate = $week7;
	$edate = TODATE;
	?>
	<div class="list_paging" id="list_paging">
		<div class="member_list_conts" id="member_list_conts">
		<input type="hidden" id="email_history" value="<?=$email?>">
		<input type="hidden" id="kind" value="<?=$kind?>">
			<div class="member_list_conts_in">
				<ul class="member_list_conts_ul">
					<?
					if(count($history_info['idx'])!=0){
						for($i=0; $i<count($history_info['idx']); $i++){
							$id = $history_info['email'][$i];
							$name = $history_info['name'][$i];
							$workdate = $history_info['workdate'][$i];
							$coin = $history_info['coin'][$i];
							$number_coin = number_format($coin);
							$code = $history_info['code'][$i];
							$memo = $history_info['memo'][$i];
							if(mb_strlen($memo)>=9){
								$memo = mb_substr($memo,0,9,'utf-8')."..";
							}else{
								$memo = $memo;
							}
							if(in_array($code,array('600','510'))){
								$kind = "지급";
							}elseif(in_array($code,array('520','620','710'))){
								$kind = "차감";
							}
							$reward_user = $history_info['reward_name'][$i]; 
							?>
							<li>
								<div class="member_list_conts_deposit">
									<strong><?=$kind?></strong>
								</div>
								<div class="member_list_conts_date">
									<strong><?=$workdate?></strong>
								</div>
								<div class="member_list_conts_deposit">
									<strong><?=$reward_user?></strong>
								</div>
								<div class="member_list_conts_deposit">
									<strong><?=$name?></strong>
								</div>
								<? if(in_array($code,array('610','600'))){?>
									<div class="member_list_conts_coin">
										<strong style="color:blue;font-weight:bold;"><?="+".$number_coin?></strong>
									</div>
								<?}elseif(in_array($code,array('520','620','710'))){?>
									<div class="member_list_conts_coin">
										<strong style="color:red;font-weight:bold;"><?="-".$number_coin?></strong>
									</div>
								<?}?>
								<div class="member_list_conts_deposit">
									<strong><?=$memo?></strong>
								</div>
							</li>
						<?}
					}else{?>
					<li class="search_list_none"><span>조회 결과가 없습니다.</span></li>
					<?}?>
				</ul>
			</div>
		</div>
		<?if($history_info['idx']){?>
			<div class="rew_ard_paging" id="rew_ard_paging">
				<div class="rew_ard_paging_in">
					<input type="hidden" id="history_email" value="<?=$email?>">
					<input type="hidden" id="tclass" value="<?=$tclass?>">
					<?
						//페이징사이즈, 전체카운터, 페이지출력갯수
						echo pageing($pagingsize, $total_count, $pagesize, $string);
					?>
				</div>
			</div>
		<?}?>
		</div>
		<!--페이징 작업 필요-->
	
			
	<? exit;
}

//공용코인 지급
if($mode == "give"){

	$give_email = $_POST['give_email'];
	if($give_email){

		$company_info = company_info();
		$sql = "select idx, part, name, email, comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$give_email."'";
		$info = selectQuery($sql);

		if($info['idx']){
			$idx = $info['idx'];
			$comcoin = $info['comcoin'];
			$part = $info['part'];
			$name = $info['name'];
			$email = $info['email'];

			//프로필 사진, 프로필 케릭터사진
			$profile_main_img_src = profile_img_info($email);
	?>

		<div class="cg_deam"></div>
			<div class="cg_in">
				<div class="cg_box">
					<div class="cg_box_in">
						<div class="cg_close">
							<button><span>닫기</span></button>
						</div>
						<div class="cg_top">
							<strong>공용코인 지급</strong>
						</div>
						<div class="cg_area">
							<div class="cg_user_area">
								<div class="cg_user_img">
									<div class="cg_user_img_in" style="background-image:url(<?=$profile_main_img_src?>);"></div>
								</div>
								<div class="cg_name">
									<div class="cg_name_user"><?=$name?></div>
									<div class="cg_name_team"><?=$part?></div>
									<input type="hidden" id="cg_name_email" value="<?=$email?>">
								</div>
							</div>

							<div class="cg_box_area">
								<div class="cg_box_area_in">
									<div class="cg_box_calc">
										<ul>
											<li>
												<span class="cg_box_tit">보유한 공용코인</span>
												<strong class="cg_box_coin" id="cg_box_coin_<?=$idx?>"><?=number_format($comcoin)?></strong>
												<span class="cg_box_txt">코인</span>
											</li>
											<li>
												<span class="cg_box_tit">지급할 공용코인</span>
												<input type="text" class="cg_box_input" id="cg_box_input_<?=$idx?>"/>
												<span class="cg_box_txt">코인</span>
											</li>
											<li>
												<span class="cg_box_tit">합계</span>
												<strong class="cg_box_coin" id="give_tot_coin_<?=$idx?>"><?=number_format($comcoin)?></strong>
												<span class="cg_box_txt">코인</span>
											</li>
										</ul>
									</div>
									<div class="cg_box_final">
										<dl>
											<dt>보유한 공용코인</dt>
											<dd><?=number_format($comcoin)?></dd>
										</dl>
										<dl>
											<dt>지급 후 공용코인</dt>
											<dd class="cg_down"><strong id="cg_comcoin_<?=$idx?>"><?=number_format($comcoin)?></strong></dd>
										</dl>
									</div>
								</div>
							</div>

						</div>

						<div class="cg_bottom" id="cg_bottom">
							<button>지급하기</button>
						</div>

					</div>
				</div>
			</div>
	<?
		}
	}
	exit;
}


//공용코인 회수
if($mode == "debt"){
	/*
	print "<pre>";
	print_r($_POST);
	print "</pre>";
	*/
	$give_email = $_POST['give_email'];
	if($give_email){


		$company_info = company_info();

		$sql = "select idx, part, name, email, comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$give_email."'";
		$info = selectQuery($sql);

		if($info['idx']){
			$idx = $info['idx'];
			$comcoin = $info['comcoin'];
			$part = $info['part'];
			$name = $info['name'];
			$email = $info['email'];

			//프로필 사진, 프로필 케릭터사진
			$profile_main_img_src = profile_img_info($email);
	?>


	<div class="cd_deam"></div>
		<div class="cd_in">
			<div class="cd_box">
				<div class="cd_box_in">
					<div class="cd_close">
						<button><span>닫기</span></button>
					</div>
					<div class="cd_top">
						<strong>공용코인 회수</strong>
					</div>
					<div class="cd_area">
						<div class="cd_user_area">
							<div class="cd_user_img">
								<div class="cd_user_img_in" style="background-image:url(<?=$profile_main_img_src?>);"></div>
							</div>
							<div class="cd_name">
								<div class="cd_name_user"><?=$name?></div>
								<div class="cd_name_team"><?=$part?></div>
								<input type="hidden" id="cd_name_email" value="<?=$email?>">
							</div>
						</div>

						<div class="cd_box_area">
							<div class="cd_box_area_in">
								<div class="cd_box_calc">
									<ul>
										<li>
											<span class="cd_box_tit">보유한 공용코인</span>
											<strong class="cd_box_coin" id="cd_box_coin_<?=$idx?>"><?=number_format($comcoin)?></strong>
											<span class="cd_box_txt">코인</span>
										</li>
										<li>
											<span class="cd_box_tit">회수할 공용코인</span>
											<input type="text" class="cd_box_input" id="cd_box_input_<?=$idx?>">
											<span class="cd_box_txt">코인</span>
										</li>
										<li>
											<span class="cd_box_tit">합계</span>
											<strong class="cd_box_coin" id="debt_tot_coin_<?=$idx?>"><?=number_format($comcoin)?></strong>
											<span class="cd_box_txt">코인</span>
										</li>
									</ul>
								</div>
								<div class="cd_box_final">
									<dl>
										<dt>보유한 공용코인</dt>
										<dd><?=number_format($comcoin)?></dd>
									</dl>
									<dl>
										<dt>회수 후 공용코인</dt>
										<dd class="cd_up"><strong id="cd_comcoin_<?=$idx?>"><?=number_format($comcoin)?></strong></dd>
									</dl>
								</div>
							</div>
						</div>

					</div>

					<div class="cd_bottom" id="cd_bottom">
						<button>회수하기</button>
					</div>

				</div>
			</div>
		</div>
	<?
		}
	}
	exit;
}


//공용코인 지급
if($mode == "comcoin_add"){

	//print "<pre>";
	//print_r($_POST);
	//print "</pre>";


	$cg_name_email = $_POST['cg_name_email'];
	$comcoin = $_POST['comcoin'];
	$comcoin = preg_replace("/[^0-9]/", "", $comcoin);

	//보내는 사람
	$sql = "select idx,email,name,comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
	$mem_info = selectQuery($sql);
	if($mem_info['idx']){

		//보유한 공용코인이 지급할 공용코인 보다 같거나 많을경우에만 지급!!
		if($mem_info['comcoin'] >= $comcoin){

			//지급받는 사람
			$sql = "select idx,email,name from work_member where state='0' and companyno='".$companyno."' and email='".$cg_name_email."'";
			$mem_add_info = selectQuery($sql);
			if($mem_add_info['idx']){

				//공용코인 지급
				$sql = "update work_member set comcoin=CASE WHEN comcoin is null THEN '".$comcoin."' ELSE comcoin+'".$comcoin."' END where idx='".$mem_add_info['idx']."'";
				$up_add = updateQuery($sql);
				if($up_add){

					//공용코인 지급내역
					$code=600;
					$memo = "공용코인 지급";
					$sql = "insert into work_coininfo(code,email,name,reward_user,reward_name,companyno,coin,coin_type,memo,ip,workdate) values(";
					$sql = $sql .= "'".$code."','".$mem_add_info['email']."','".$mem_add_info['name']."','".$user_id."','".$user_name."','".$companyno."','".$comcoin."','1','".$memo."','".LIP."','".TODATE."')";
					$insert_idx = insertIdxQuery($sql);
					//echo $sql;
					//echo "\n\n";
				}

				//공용코인 차감
				$sql = "update work_member set comcoin=CASE WHEN comcoin > 0 THEN comcoin-'".$comcoin."' ELSE 0 END where state='0' and companyno='".$companyno."' and email='".$user_id."'";
				$up_remove = updateQuery($sql);
				//echo $sql;
				//echo "\n\n";
				if($up_remove){

					//공용코인 차감내역
					$code=620;
					$memo = "공용코인차감";
					$sql = "insert into work_coininfo(code,email,name,reward_user,reward_name,companyno,coin,coin_type,memo,ip,workdate) values(";
					$sql = $sql .= "'".$code."','".$user_id."','".$user_name."','".$mem_add_info['email']."','".$mem_add_info['name']."','".$companyno."','".$comcoin."','1','".$memo."','".LIP."','".TODATE."')";
					$insert_idx = insertIdxQuery($sql);
					//echo $sql;
					//echo "\n\n";
				}

				//지급 + 차감
				if($up_add && $up_remove){

					echo "complete";
					exit;
				}
			}

		}else{

			echo "small";
			exit;
		}
	}
	exit;
}



//공용코인 회수하기
if($mode == "comcoin_remove"){

	/*print "<pre>";
	print_r($_POST);
	print "</pre>";
	*/

	$cd_name_email = $_POST['cd_name_email'];
	$comcoin = $_POST['comcoin'];
	$comcoin = preg_replace("/[^0-9]/", "", $comcoin);

	//보내는 사람
	$sql = "select idx,email,name from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
	$mem_info = selectQuery($sql);
	if($mem_info['idx']){

		//지급받는 사람
		$sql = "select idx,email,name,comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$cd_name_email."'";
		$mem_add_info = selectQuery($sql);

		$update_comcoin = $mem_add_info['comcoin'] + $comcoin ;

		if($mem_add_info['idx']){


			//보유한 공용코인이 회수하는 공용코인보다 크거나 같은경우만 회수처리
			if($mem_add_info['comcoin']>= $comcoin){

				//공용코인 회수
				$sql = "update work_member set comcoin=CASE WHEN comcoin > 0 THEN comcoin-".$comcoin." ELSE 0 END where idx='".$mem_add_info['idx']."'";
				//echo $sql;
				//echo "\n\n";

				$up_add = updateQuery($sql);
				if($up_add){

					//공용코인 회수내역
					$code=620;
					$memo = "공용코인회수";
					$sql = "insert into work_coininfo(code,email,name,reward_user,reward_name,companyno,coin,coin_type,memo,ip,workdate) values(";
					$sql = $sql .= "'".$code."','".$mem_add_info['email']."','".$mem_add_info['name']."','".$user_id."','".$user_name."','".$companyno."','".$comcoin."','1','".$memo."','".LIP."','".TODATE."')";
					$insert_idx = insertIdxQuery($sql);
					//echo $sql;
					//echo "\n\n";
				}

				//회사 공용코인 지급
				$sql = "update work_company set comcoin=comcoin+".$comcoin." where state='0' and idx='".$companyno."'";
				$up_remove = updateQuery($sql);

				$sql = "update work_member set comcoin=comcoin+".$comcoin." where email = '".$user_id."' ";
				$query = updateQuery($sql);
				//echo $sql;
				//echo "\n\n";
				if($up_remove){

					//공용코인 회수 내역
					$code=600;
					$memo = "공용코인 지급";
					$sql = "insert into work_coininfo(code,email,name,reward_user,reward_name,companyno,coin,coin_type,memo,ip,workdate) values(";
					$sql = $sql .= "'".$code."','".$user_id."','".$user_name."','".$mem_add_info['email']."','".$mem_add_info['name']."','".$companyno."','".$comcoin."','1','".$memo."','".LIP."','".TODATE."')";
					$insert_idx = insertIdxQuery($sql);
					//echo $sql;
					//echo "\n\n";
				}

				//지급 + 차감
				if($up_add && $up_remove){

					echo "complete";
					exit;
				}
				
				

			}else{
				echo "small";
				exit;
			}

		}
	}
	exit;
}


//멤버정보수정
if($mode == "member_edit"){

	$mem_idx = $_POST['mem_idx'];
	$part_no = $_POST['part_no'];
	$name = $_POST['name'];
	$part = $_POST['part'];

	$mem_idx = preg_replace("/[^0-9]/", "", $mem_idx);
	$part_no = preg_replace("/[^0-9]/", "", $part_no);


	// if($user_id=='adsb12@nate.com'){

	// 	print "<pre>";
	// 	print_r($_POST);
	// 	print "</pre>";
	// 	exit;
	// }
	//관리자 권한 체크
	$sql = "select idx,email,highlevel from work_member where state!='9' and companyno='".$companyno."' and email='".$user_id."'";
	$mem_ad_info = selectQuery($sql);
	if($mem_ad_info['highlevel'] == '0'){
		$sql = "select idx,email from work_member where state!='9' and companyno='".$companyno."' and idx='".$mem_idx."'";
		$mem_info = selectQuery($sql);

		if($mem_info['idx']){
			$sql = "update work_member set name='".$name."', part='".$part."', partno='".$part_no."', editdate=".DBDATE." where state!='9' and companyno='".$companyno."' and idx='".$mem_idx."'";
			$up = updateQuery($sql);

			// $sql = "select email from work_member where state !='9' and companyno='".$companyno."' and idx = '".$mem_idx."'";
			// $mem_up_info = selectQuery($sql);

			$sql = "update work_todaywork set name='".$name."', part='".$part."', part_flag='".$part_no."' where state !='9' and companyno='".$companyno."' and email = '".$mem_info['email']."' and workdate >= '".TODATE."' ";
			$work_up = updateQuery($sql);

			if($work_up){
				echo "complete";
				exit;
			}
		}
	}else{

		echo "not_auth";
		exit;
	}
	exit;
}



//멤버 추가
if($mode == "member_add"){
	/*
	print "<pre>";
	print_r($_POST);
	print "</pre>";

	print "<pre>";
	print_r($_FILES);
	print "</pre>";
	*/

	header("Content-type: text/html; charset=utf-8");
	set_time_limit(0);
	@ini_set('memory_limit', '-1');
	@ini_set('max_execution_time', 0);

	include_once($home_dir."inc/PHPExcel-1.8/Classes/PHPExcel.php");
	$objPHPExcel = new PHPExcel();

	include_once($home_dir."inc/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");

	$allData = array();
	$filename = $_FILES['files']['tmp_name'][0];

	try {
		// 업로드한 PHP 파일을 읽어온다.

		$objPHPExcel = PHPExcel_IOFactory::load($filename);
		$sheetsCount = $objPHPExcel -> getSheetCount();

		// 시트Sheet별로 읽기

		for($i = 0; $i < $sheetsCount; $i++) {
			$objPHPExcel -> setActiveSheetIndex($i);
			$sheet = $objPHPExcel -> getActiveSheet();
			$highestRow = $sheet -> getHighestRow();   			           // 마지막 행
			$highestColumn = $sheet -> getHighestColumn();	// 마지막 컬럼

			// 한줄읽기
			for($row = 1; $row <= $highestRow; $row++) {

				// $rowData가 한줄의 데이터를 셀별로 배열처리 된다.
				$rowData = $sheet -> rangeToArray("A" . $row . ":" . $highestColumn . $row, NULL, TRUE, FALSE);

				// $rowData에 들어가는 값은 계속 초기화 되기때문에 값을 담을 새로운 배열을 선안하고 담는다.
				$allData[$row] = $rowData[0];

			}
		}

	} catch(exception $e) {
	//	echo " eeee :: ". $e;
	//	echo "\n\n";
	}

	
	echo "<pre>";
	print_r($allData);
	echo "</pre>";

	//echo "sheetsCount " .$highestRow;
	

	$default_cnt = 5;
	if($highestRow >= $default_cnt){
		$row_cnt = $highestRow;
	}else{
		$row_cnt = $default_cnt;
	}

	$k = 1;
	for($i=2; $i<=$row_cnt; $i++){



		echo $allData[$i][3];
		echo "\n\n";
		

	?>
		<li>
			<div class="member_inputs_num">
				<strong><?=$k?></strong>
			</div>
			<div class="member_inputs_name">
				<input type="text" class="inputs_member_name" placeholder="이름" value="<?=$allData[$i][0]?>" id="inputs_member_name"/>
			</div>
			<div class="member_inputs_team">
				<input type="text" class="inputs_member_team" placeholder="부서명" value="<?=$allData[$i][1]?>" id="inputs_member_team"/>
			</div>
			<div class="member_inputs_email">
				<input type="text" class="inputs_member_email" placeholder="이메일" value="<?=$allData[$i][2]?>" id="inputs_member_email"/>
				<div class="rew_member_inputs_sort" id="rew_member_inputs_sort_<?=$k?>">
					<div class="rew_member_inputs_sort_in">
					
					<?if($allData[$i][3]==""){?>
						<button class="btn_sort_on" id="btn_sort_on_<?=$k?>" value="input"><span>직접입력</span></button>
					<?}else{?>
						<button class="btn_sort_on" id="btn_sort_on_<?=$k?>" value="<?=$allData[$i][3]?>"><span><?=$allData[$i][3]?></span></button>
					<?}?>
						<ul>
							<li><button id="btn_sort_li_<?=$k?>" value="input"><span>직접입력</span></button></li>
							<li><button id="btn_sort_li_<?=$k?>" value="naver"><span>naver.com</span></button></li>
							<li><button id="btn_sort_li_<?=$k?>" value="gmail"><span>gmail.com</span></button></li>
							<li><button id="btn_sort_li_<?=$k?>" value="kakao"><span>kakao.com</span></button></li>
						</ul>
					</div>
				</div>
			</div>
		</li>
	<?
	$k++;
	}
	exit;
}


if($mode == "member_email_send"){
	if($_POST['input_name']){

		$cnt = count($_POST['input_name']);

		//회원정보
		$mem_row_info = member_row_info($user_id);

		for($i=0; $i<$cnt; $i++){

			$input_name = $_POST['input_name'][$i];
			$input_team = $_POST['input_team'][$i];
			$input_email = $_POST['input_email'][$i];
			$input_sw = $_POST['input_sw'][$i];

			$sql = "select idx from work_team where state='0' and partname='".$input_team."'";
			$team_info = selectQuery($sql);
			if(!$team_info['idx']){
				$sql = "insert into work_team(companyno, partname, ip) values('".$companyno."', '".$input_team."', '".LIP."')";
				$team_idx = insertIdxQuery($sql);
			}else{
				$team_idx = $team_info['idx'];
			}


			$title = "리워디 초대 이메일입니다.";


			//관리자 지정 여부:관리자(0), 일반(5)
			if($input_sw == 'true'){
				$input_sw_val = "0";
			}else{
				$input_sw_val = "5";
			}

			if($mem_row_info['idx']){

				$company = $mem_row_info['company'];
				$partno = $mem_row_info['partno'];

				$team_info = team_info($input_team);

				//가입여부 체크
				//$sql = "select idx from work_member where state!='9' and companyno='".$companyno."' and email='".$input_email."'";
				$sql = "select idx from work_member where state!='9' and email='".$input_email."' and password is not null";
				$sendmail_info = selectQuery($sql);
				if($sendmail_info['idx']){
					echo "over|".$i;
					exit;
				}


				//메일초대중(state=1)
				$sql = "select idx from work_member where state='1' and companyno='".$companyno."' and email='".$input_email."'";
				$sendmail_info = selectQuery($sql);

				if(!$sendmail_info['idx']){

					$sql = "insert into work_member(state, email, name, company, companyno, part, partno, highlevel)";
					$sql = $sql ." values('1','".$input_email."','".$input_name."','".$company."','".$companyno."', '".$input_team."','".$team_info['idx']."','".$input_sw_val."')";
					$insert_idx = insertIdxQuery($sql);

					if($insert_idx){

						$secret = "send_email=".$user_id."&to_email=".$input_email."&sendno=".$insert_idx;
						$encrypted = Encrypt($secret);


						$location_url = $home_url."/team/?".$encrypted;

						//$contents = "안녕하세요.\n\n".$company."에서 발송되었습니다.\n\n아래 링크를 클릭하여 인증 바랍니다.\n\n<span style=\"color: red\"><a href='https://rewardy.co.kr/team/?".$encrypted."' target=\"_blank\">사용자 인증</a></span>";

						/*ob_start();
						include str_replace( basename(__DIR__) , "", __DIR__ ) ."layer/mail_send_about.php";
						$contents = ob_get_contents();
						ob_end_clean();*/
						include str_replace( basename(__DIR__) , "", __DIR__ ) ."layer/mail_send_member.php";
						$contents = $mail_html;

						//발신자이름, 발신자이메일, 수신자이메일, 메일제목, 메일내용
						$result = mailer($user_name, 'devmaster@bizforms.co.kr', $input_email, $title, $contents);

						//메일발송성공
						if($result == '1'){
							echo "complete";
							$sql = "update work_member set send_mail_cnt = send_mail_cnt + 1 , sender_ip='".LIP."', mail_send_regdate=".DBDATE." where idx='".$insert_idx."'";
							$res = updateQuery($sql);

						}else{
						//메일발송실패
							echo "faile";
							$state = '2';
							$sql = "update work_member set state='".$state."', send_mail_cnt = send_mail_cnt + 1 , sender_ip='".LIP."', mail_send_regdate=".DBDATE." where idx='".$insert_idx."'";
							$res = updateQuery($sql);

						}
						exit;
					}
				}else{

					$sql = "update work_member set sender_name='".$input_name."', part='".$input_team."', partno='".$team_info['idx']."', highlevel='".$input_sw_val."', sender_ip='".LIP."', send_mail_cnt=send_mail_cnt+1, mail_send_regdate=".DBDATE."  where idx='".$sendmail_info['idx']."'";
					$up = updateQuery($sql);
					if($up){

						$secret = "send_email=".$user_id."&to_email=".$input_email."&sendno=".$sendmail_info['idx'];
						$encrypted = Encrypt($secret);
						//$contents = "안녕하세요.\n\n".$company."에서 발송되었습니다.\n\n아래 링크를 클릭하여 인증 바랍니다.\n\n<span style=\"color: red\"><a href='https://rewardy.co.kr/team/?".$encrypted."' target=\"_blank\">사용자 인증</a></span>";

						$location_url = $home_url."/team/?".$encrypted;

						//파일을 변수로 넣을경우 아래 주석 풀기//
						//ob_start();
						include str_replace( basename(__DIR__) , "", __DIR__ ) ."layer/mail_send_member.php";
						$contents = $mail_html;

						/*
						//파일을 변수로 넣을경우 아래 주석 풀기
						//$contents = ob_get_contents();
						//$contents = preg_replace("/\r\n|\r|\n/","",$contents);
						//ob_end_clean();
						*/

						//발신자이름, 발신자이메일, 수신자이메일, 메일제목, 메일내용
						$result = mailer($user_name, 'devmaster@bizforms.co.kr', $input_email, $title, $contents);
						if($result == '1'){
							echo "complete";
						}else{
							echo "faile";
						}
						exit;
					}
				}
			}
		}
	}
	exit;
}


//출금신청하기
if($mode == "withdraw_add"){

	/*
	print "<pre>";
	print_r($_POST);
	print "</pre>";
	*/

	//출금신청코인
	$coin = $_POST['coin'];
	$bank_name = $_POST['bank_name'];
	$bank_num = $_POST['bank_num'];
	$bank_user = $_POST['bank_user'];

	$coin = preg_replace("/[^0-9]/", "", $coin);
	$bank = preg_replace("/[^0-9]/", "", $bank);
	$bank_num = preg_replace("/[^0-9]/", "", $bank_num);


	//회원정보 체크
	$mebmer_info = member_row_info($user_id);
	if($mebmer_info['idx']){

		//보유 공용코인
		$comcoin = $mebmer_info['comcoin'];

		//공용코인부족
		if($comcoin == 0){
			echo "not";
			exit;
		}

		//보유한 공용코인보다 많이 신청했을 경우
		if($coin > $comcoin){
			echo "over";
			exit;
		}

		//은행명
		$account_name = "";
		$sql = "select idx, name from work_bank where state='0' and idx='".$bank_name."'";
		$bank_info = selectQuery($sql);
		if($bank_info['idx']){
			$account_name = $bank_info['name'];
		}

		$memo = "출금신청";
		//출금신청내역
		$sql = "select idx from work_account_info where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
		$account_info = selectQuery($sql);

		$sql = "insert into work_account_info(companyno, email, name, bank_name, bank_num, coin, ip, memo, workdate) values(";
		$sql = $sql .= "'".$companyno."', '".$user_id."', '".$user_name."', '".$account_name."', '".$bank_num."', '".$coin."', '".LIP."', '".$memo."', '".TODATE."')";
		$insert_idx = insertIdxQuery($sql);
		if($insert_idx){

			//출금신청 공용코인 차감
			$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
			$up = updateQuery($sql);
			if($up){
				//출금신청
				$reward_type = "account";
				$coin_type = "1";

				$sql = "insert into work_coininfo(code, work_idx, reward_type, coin_type, companyno, email, name, coin, memo, workdate, ip) values(";
				$sql = $sql .= "'810','".$insert_idx."','".$reward_type."','".$coin_type."','".$companyno."','".$user_id."','".$user_name."','".$coin."','".$memo."','".TODATE."','".LIP."')";
				$coininfo_idx = insertIdxQuery($sql);
				if($coininfo_idx){
					echo "complete";
					exit;
				}

			}
		}
	}
	exit;
}

if($mode == 'logo_upload'){
	
	if($_FILES['files']){
		$format_ext = array('asp', 'php', 'jsp', 'xml', 'html', 'htm', 'aspx', 'exe', 'exec', 'java', 'js', 'class', 'as', 'pl', 'mm', 'o', 'c', 'h', 'm', 'cc', 'cpp', 'hpp', 'cxx', 'hxx', 'lib', 'lbr', 'ini', 'py', 'pyc', 'pyo', 'bak', '$$$', 'swp', 'sym', 'sys', 'cfg', 'chk', 'log', 'lo');
		$max_file_size = MAX_FILE_SIZE * 1024 * 1024;
		$files_for_size = $_FILES['files']['size'][0];
		$files_for_name = $_FILES['files']['name'][0];

		if($files_for_size > $max_file_size){
			echo "files_size_over|";
			exit;
		}

		$ext = @end(explode('.', $files_for_name));
		if(in_array($ext, $format_ext)){
			echo "files_format";
			exit;
		}

		//파일체크
		$file_upload_check = false;
					
		//파일갯수
		$fileimg_upload_cnt = count($_FILES['files']['name']);

		//첨부파일 이미지
		if ($fileimg_upload_cnt > 0){

			//파일첨부 여부
			$file_upload_check = true;

			//파일순번
			$file_img_num = 1;
			
			//파일확장자 추출
			$filename = $_FILES['files']['name'][0];
			$ext = array_pop(explode(".", strtolower($filename)));

			//허용확장자체크
			// if( !in_array($ext, $img_file_allowed_ext) ) {
			// 	echo "ext_file2===>".$ext;
			// 	exit;
			// }

			//파일타입
			$file_type = $_FILES['files']['type'][0];

			//파일사이즈
			$file_size = $_FILES['files']['size'][0];

			//임시파일명
			$file_tmp_name = $_FILES['files']['tmp_name'][0];

			//파일명
			$file_real_name = $filename;

			$file_source	= $file_tmp_name; //파일명
			//$file_ext		= array_pop(explode('.', $filename)); //확장자 추출 (array_pop : 배열의 마지막 원소를 빼내어 반환)
			$file_info		= getimagesize($file_tmp_name);
			$file_width		= $file_info[0]; //이미지 가로 사이즈
			$file_height	= $file_info[1]; //이미지 세로 사이즈
			$file_type		= $file_type;

			//라사이즈 
			$rezie_file_path = "";
			$rezie_renamefile = "";
			$resize_file = "";
			$resize_val = "0";


			$sql = "select idx from work_company where state='0' and company='".$companyno."'";
			$com_info = selectQuery($sql);
			if($com_info['idx']){
				$com_info_idx = $com_info['idx'];
			}

			//랜덤번호
			$rand_id = name_random();

			//변경되는 파일명
			list($microtime,$timestamp) = explode(' ',microtime());
			$time = $timestamp.substr($microtime, 2, 3);
			$datetime = date("YmdHis", $timestamp).substr($microtime, 2, 3);

			//$renamefile = date("YmdHis")."_{$rand_id}_challenges_{$res_idx}.{$ext}";
			$renamefile = "{$datetime}_{$rand_id}_profile_{$com_info_idx}.{$ext}";

			//년도
			$dir_year = date("Y", TODAYTIME);

			//월
			$dir_month = date("m", TODAYTIME);

			//업로드 디렉토리 -/data/고유번호/(회사폴더명)/profile/img/년/월/
			$upload_path = $dir_file_profile_path."/".$profile_save_dir_img."/";
			$upload_path = str_replace($profile_save_dir_img , "data/".$companyno."/".$comfolder."/"."company/img" , $upload_path);

			//업로드 디렉토리 - /data/고유번호/(회사폴더명)/profile/img_ori/년/월/
			$upload_path_ori = $dir_file_profile_path."/".$profile_save_dir_img_ori."/";
			$upload_path_ori = str_replace($profile_save_dir_img_ori , "data/".$companyno."/".$comfolder."/"."company/img_ori" , $upload_path_ori);

			//디렉토리 없는 경우 권한 부여 및 생성
			if ( !is_dir ( $upload_path ) ){
				mkdir( $upload_path , 0777, true);
			}

			//디렉토리 없는 경우 권한 부여 및 생성 - 원본폴더
			if ( !is_dir ( $upload_path_ori ) ){
				mkdir( $upload_path_ori , 0777, true);
			}

			
			//리사이즈한 업로드될 파일경로/파일명
			$upload_files = $upload_path.$renamefile;

			//원본 업로드될 파일경로/파일명
			$upload_files_ori = $upload_path_ori.$renamefile;

			$new_file_width = 250; //이미지 가로 사이즈 지정
			$rate = $new_file_width / $file_width; //이미지 세로 사이즈 및 파일 사이즈(quality) 조절을 위한 비율 
			$new_file_height = (int)($file_height * $rate); 
			$new_quality = (int)($file_size * $rate);


			//이미지 가로사이즈가 250보다 크면 사이즈 조절
			if ($file_width > $new_file_width){
				switch($file_type){
					case "image/jpeg" :
						$image = imagecreatefromjpeg($file_source);
						break;
					case "image/gif" :
						$image = imagecreatefromgif($file_source);
						break;
					case "image/png" :
						$image = imagecreatefrompng($file_source);
						break;
					default:	
						$image = "";
						break;
				}

				//리사이즈
				$rezie_img = fn_imagejpeg($image, $upload_files, $new_file_width, $new_file_height, $file_width, $file_height, $new_quality);
				

				//원본이미지
				$return = move_uploaded_file($file_tmp_name, $upload_files_ori);

				//리사이즈 파일 용량
				$file_resize = filesize($upload_files); 

			}else{

				$rezie_img = "";
				//파일 업로드
				$return = move_uploaded_file($file_tmp_name, $upload_files_ori);
			}

			if($return){
				//리사이즈이미지 경로
				$file_path = str_replace($dir_file_profile_path , "" , $upload_path);

				//원본이미지 경로
				$file_path_ori = str_replace($dir_file_profile_path , "" , $upload_path_ori);

				//리사이즈 경우
				if($rezie_img == true){
					$rezie_file_path = $file_path;
					$rezie_renamefile = $renamefile;
					$resize_file = $file_resize;
					$resize_val = "1";
				}else{
					$rezie_file_path = $file_path_ori;
					$rezie_renamefile = $renamefile;
					$resize_file = $file_size;
					$resize_val = "0";
				}

				$sql = "select idx, file_path, file_name, file_ori_path, file_ori_name from work_company_logo_img where state='0' and companyno='".$companyno."'";
				$com_logo_info = selectQuery($sql);
				if($com_logo_info['idx']){

					if($com_logo_info['file_path'] && $com_logo_info['file_name']){
						@unlink($dir_file_path.$com_logo_info['file_path'].$com_logo_info['file_name']);
					}

					if($com_logo_info['file_ori_path'] && $com_logo_info['file_ori_name']){
						@unlink($dir_file_path.$com_logo_info['file_ori_path'].$com_logo_info['file_ori_name']);
					}

					$sql = "update work_company_logo_img set resize='".$resize_val."', file_path='".$rezie_file_path."', file_name='".$rezie_renamefile."', file_size='".$resize_file."', file_ori_path='".$file_path_ori."', file_ori_name='".$renamefile."', file_ori_size='".$file_size."', file_real_name='".$file_real_name."',file_type='".$file_type."', editdate=".DBDATE." where companyno='".$companyno."'";
					$up = updateQuery($sql);
					// if($up){
					// 	$sql = "update work_member set profile_type='1', profile_img_idx='".$profile_info['idx']."' where state='0' and companyno='".$companyno."'";
					// 	$mem_up = updateQuery($sql);
					// 	if($mem_up){
							echo "complete";
							exit;
					// 	}
					// }

				}else{

					$sql = "insert into work_company_logo_img(companyno, resize, file_path, file_name, file_size, file_ori_path, file_ori_name, file_ori_size, file_real_name, file_type, ip) values(";
					$sql = $sql .="'".$companyno."','".$resize_val."','".$rezie_file_path."','".$rezie_renamefile."','".$resize_file."','".$file_path_ori."','".$renamefile."','".$file_size."','".$file_real_name."','".$file_type."','".LIP."')";
					$files_idx = insertIdxQuery($sql);
					if($files_idx){
						// $sql = "update work_member set profile_type='1', profile_img_idx='".$files_idx."' where state='0' and email='".$user_id."'";
						// $mem_up = updateQuery($sql);
						// if($mem_up){
							echo "complete";
						// 	exit;
						// }
					}
				}
			}else{
				echo "file_not";
				exit;
			}
		}
		exit;

	}
}

//출금신청하기
if($mode == "admin_penalty"){

	$comp_no = $_POST['companyno'];

	$onoff = $_POST['onoff'];

	$setkind = $_POST['setkind'];

	// 관리자 권한 확인
	$sql = "select idx, email from work_member where email = '".$user_id."' and highlevel = '0' and state = '0' and companyno = '".$comp_no."' ";
	$query = selectQuery($sql);

	if(!$query['idx']){
		echo "You're not admin.";
		exit;
	}else{
		if($setkind == 'penalty' && $onoff == '0'){
			$sql = "update work_company set penalty = '".$onoff."', penalty_in = '".$onoff."', penalty_work = '".$onoff."', penalty_out = '".$onoff."', penalty_challenge = '".$onoff."' where idx = '".$comp_no."' and state = '0'";
		}else{
			$sql = "update work_company set ".$setkind." = '".$onoff."' where idx = '".$comp_no."' and state = '0' ";
		}
		$query = updateQuery($sql);

		echo "complete";
	}
	exit;
}

if($mode == "time_set"){
	// 출근시간
	$intime = $_POST['intime'];

	//퇴근시간
	$outtime = $_POST['outtime'];

	$sql = "select idx, state, highlevel, companyno from work_member where email = '".$user_id."' and highlevel = '0' and state = '0' ";
	$query = selectQuery($sql);

	if($query['idx']){
		$sql = "update work_company set intime = '".$intime."', outtime = '".$outtime."' where";
		$sql = $sql .= " idx = '".$query['companyno']."' and state = '0' ";
		$time_setting = updateQuery($sql);
		if($time_setting){
			echo "success";
		}
	}else{
		echo "not highlevel";
	}
}

if($mode == "mem_charge"){
	$chage = $_POST['mem_coin'];
	
	
	//회사 정보
	$sql = "select idx,company,comcoin from work_company where idx = '".$companyno."' and state = '0'";
	$com_check = selectQuery($sql);
	//회사 멤버 모두 구하기
	$sql = "select idx,email,companyno,comcoin from work_member where state = '0' and companyno = '".$com_check['idx']."'";
	$mem_check = selectAllQuery($sql);
	//회사 멤버 카운트
	$sql = "select idx,count(1) as cnt from work_member where state = '0' and companyno = '".$com_check['idx']."'";
	$mem_count = selectQuery($sql);

	$pp_coin = $chage * count($mem_check);

	if($pp_coin > $com_check['comcoin']){
		echo "fail";
	}else{
		for($i = 0; $i < $mem_count['cnt']; $i++){
			$sql = "update work_member set comcoin = ".$mem_check['comcoin'][$i]." + ".$chage." where companyno = '".$mem_check['companyno'][$i]."' and idx = '".$mem_check['idx'][$i]."' and state = '0'";
			$mem_coin = insertIdxQuery($sql);
		}
		$sql = "update work_company set comcoin = ".$com_check['comcoin']." - ".$pp_coin." where idx = '".$com_check['idx']."'";
		$com_up = updateQuery($sql);
		echo "complete|";
		if($com_up){
			$sql = "select idx,company,comcoin from work_company where idx = '".$com_check['idx']."'";
			$com_result = selectQuery($sql);

			echo $com_result['comcoin'];
		}
	}
	
}
if($mode == "en_charge"){
	$chage = $_POST['en_coin'];
	$sql = "select idx,company,comcoin from work_company where idx = '".$companyno."' and state = '0'";
	$com_check = selectQuery($sql);

	if($chage > $com_check['comcoin']){
		echo "fail";
	}else if($com_check){
		$sql = "select idx,companyno,cp_coin from work_com_rule where companyno = '".$com_check['idx']."'and state = '0'";
		$rule_check = selectQuery($sql);
		if($rule_check){
			$sql = "update work_com_rule set cp_coin = ".$rule_check['cp_coin']." + ".$chage." where idx = '".$rule_check['idx']."'";
			$rule_up = updateQuery($sql);
			if($rule_up){
				$sql = "update work_company set comcoin = ".$com_check['comcoin']." - ".$chage." where idx = '".$rule_check['companyno']."'";
				$com_up = updateQuery($sql);
				echo "complete|";
				if($com_up){
					$sql = "select idx,company,comcoin from work_company where idx = '".$companyno."'";
					$com_result = selectQuery($sql);

					echo $com_result['comcoin'];
				}
			}
		}else{
			$sql = "insert into work_com_rule(state, companyno, cp_coin, like_coin) values('0', '".$companyno."', '".$chage."', '0')";
			$in_coin = insertIdxQuery($sql);
			if($in_coin){
				$sql = "update work_company set comcoin = ".$com_check['comcoin']." - ".$chage." where idx = '".$com_check['idx']."'";
				$com_up = updateQuery($sql);
				echo "complete|";
				if($com_up){
					$sql = "select idx,company,comcoin from work_company where idx = '".$companyno."'";
					$com_result = selectQuery($sql);

					echo $com_result['comcoin'];
				}
			}
		}
	}
}
if($mode == "like_charge"){
	$chage = $_POST['like_coin'];
	$sql = "select idx,company,comcoin from work_company where idx = '".$companyno."' and state = '0'";
	$com_check = selectQuery($sql);

	if($chage > $com_check['comcoin']){
		echo "fail";
	}else if($com_check){
		$sql = "select idx,companyno,like_coin from work_com_rule where companyno = '".$com_check['idx']."'and state = '0'";
		$rule_check = selectQuery($sql);
		if($rule_check){
			$sql = "update work_com_rule set like_coin = ".$rule_check['like_coin']." + ".$chage." where idx = '".$rule_check['idx']."'";
			$rule_up = updateQuery($sql);
			if($rule_up){
				$sql = "update work_company set comcoin = ".$com_check['comcoin']." - ".$chage." where idx = '".$rule_check['companyno']."'";
				$com_up = updateQuery($sql);
				echo "complete|";
				if($com_up){
					$sql = "select idx,company,comcoin from work_company where idx = '".$companyno."'";
					$com_result = selectQuery($sql);

					echo $com_result['comcoin'];
				}
			}
		}else{
			$sql = "insert into work_com_rule(state, companyno, cp_coin, like_coin) values('0', '".$companyno."', '0', '".$chage."')";
			$in_coin = insertIdxQuery($sql);
			if($in_coin){
				$sql = "update work_company set comcoin = ".$com_check['comcoin']." - ".$chage." where idx = '".$com_check['idx']."'";
				$com_up = updateQuery($sql);
				echo "complete|";
				if($com_up){
					$sql = "select idx,company,comcoin from work_company where idx = '".$companyno."'";
					$com_result = selectQuery($sql);

					echo $com_result['comcoin'];
				}
			}
		}
	}
}
?>