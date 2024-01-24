<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;


//메모리 제한 풀기
ini_set('memory_limit','-1');

//header('Content-Type: text/html; charset=UTF-8');
//header("Content-Type: text/html; charset=CP949");

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


//print_r($_POST);
//exit;
/*
echo "GG";
print "<pre>";
print_r($_POST);
print "</pre>";


print "<pre>";
print_r($_FILES);
print "</pre>";

echo "XXXXXXX";
*/

$mode = $_POST["mode"];									////mode값 전달받음
$type_flag = ($chkMobile)?1:0;							//구분(0:사이트, 1:모바일)

if($_COOKIE){

	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];

}

//테마이동
if($mode == "thema_move"){

	if($_POST['listsort']){

		$_POST['listsort'] = str_replace("thema[]=all&","",$_POST['listsort']);

		//챌린지 템플리 관리권한을 가진경우
		if($template_auth){

			//테마 정렬 업데이트
			$item = explode("&",$_POST['listsort']);
			$i=1;
			$up = array();
			foreach ($item as $value) {
				$value = str_replace("themasort[]=", "", $value);
				$sql = "update work_challenges_thema set sort='".$i."', editdate=".DBDATE." where idx='".$value."'";
				echo $sql;
				echo "\n";

				$up[] = updateQuery($sql);
				$i++;
			}

			if(count($up) == count($item)){
				echo "complete";
			}

		}else{

			//회원별 테마리스트 정보
			$sql = "select count(1) as cnt from work_challenges_thema_user_list where state='0' and email='".$user_id."'";
			$thema_user_info = selectQuery($sql);
			if($thema_user_info['cnt'] > 0 ){
				$list_cnt = $thema_user_info['cnt'];
			}else{
				//테마리스트 입력
				$sql = "select idx, title from work_challenges_thema where state='0' and companyno='".$companyno."'";
				$thema_info = selectAllQuery($sql);
				$inser_idx = array();
				for($i=0; $i<count($thema_info['idx']); $i++){
					$sql = "insert into work_challenges_thema_user_list(email, thema_idx, title, companyno, ip) values('".$user_id."','".$thema_info['idx'][$i]."', '".$thema_info['title'][$i]."','".$companyno."','".LIP."')";
					$inser_idx[] = insertQuery($sql);
				}
				$list_cnt = count($inser_idx);
			}

			//테마 정렬 업데이트
			$item = explode("&",$_POST['listsort']);
			$i=1;
			$up = array();

			foreach ($item as $value) {
				$value = str_replace("themasort[]=", "", $value);
				$sql = "update work_challenges_thema_user_list set sort='".$i."', editdate=".DBDATE." where thema_idx='".$value."' and email='".$user_id."'";
				echo $sql;
				echo "<br>";
				$up[] = updateQuery($sql);
				$i++;
			}

			if(count($up) == $list_cnt){
				echo "complete";
			}
		}
	}
	exit;
}


//테마리스트 이동
if($mode == "thema_list_move"){

	$thema_idx = $_POST['thema_idx'];
	$thema_idx = preg_replace("/[^0-9]/", "", $thema_idx);
	if($_POST['listsort']){

		$_POST['listsort'] = str_replace("thema[]=all&","",$_POST['listsort']);

		//챌린지 템플리 관리권한을 가진경우
		if($template_auth){
			//테마 정렬 업데이트
			$item = explode("&",$_POST['listsort']);
			$i=1;
			$up = array();
			foreach ($item as $value) {
				$value = str_replace("themaslist[]=", "", $value);
				$sql = "update work_challenges_thema_list set sort='".$i."', editdate=".DBDATE." where thema_idx='".$thema_idx."' and challenges_idx='".$value."'";
				//echo $sql;
				//echo "\n\n";
				$up[] = updateQuery($sql);
				$i++;
			}

			if(count($up) == count($item)){
				echo "complete";
			}

		}
	}
	exit;
}




//테마 추천설정 및 해제
if($mode == "thema_recom"){

	/*print "<pre>";
	print_r($_POST);
	print "</pre>";*/

	$idx = preg_replace("/[^0-9]/", "", $_POST['val']);
	$recom = preg_replace("/[^0-9]/", "", $_POST['recom']);

	if($idx){
		$sql = "select idx from work_challenges_thema where state='0' and idx='".$idx."'";
		$thema_info = selectQuery($sql);
		if($thema_info['idx']){
			$thema_idx = $thema_info['idx'];
			$sql = "update work_challenges_thema set recom='".$recom."', editdate=".DBDATE." where idx='".$thema_idx."'";
			$up = updateQuery($sql);
			if($up){

				//추천설정
				if ($recom == '1'){
					//조건(정상챌린지:state=0, 템플릿설정:template=0, 코칭이아닌값:coaching_chk=0, 임시저장:temp_flag=0, 숨기기:view_flag=0)
					$sql = "select a.idx, (select top 1 thema_idx from work_challenges_thema_list where state='0' and a.idx=challenges_idx and thema_idx='".$thema_idx."') as thema_idx,";
					$sql = $sql .= " (select top 1 sort from work_challenges_thema_list where a.idx=challenges_idx and state='0' and sort > 0 order by idx desc) as sort";
					$sql = $sql .=" from work_challenges as a where a.state='0' and a.template='1' and a.coaching_chk='0' and temp_flag='0' and view_flag='0'";
					$sql = $sql .= " and (select top 1 thema_idx from work_challenges_thema_list where state='0' and companyno='".$companyno."' and thema_idx='".$thema_idx."' and a.idx=challenges_idx)='".$thema_idx."'";
					$sql = $sql .= " order by a.idx asc";
					$recom_info = selectAllQuery($sql);
					for($i=0; $i<count($recom_info['idx']); $i++){

						$chall_idx = $recom_info['idx'][$i];
						$chall_sort = $recom_info['sort'][$i];
						$sql = "select idx from work_challenges_thema_recom_list where state='0' and companyno='".$companyno."' and thema_idx='".$thema_idx."' and challenges_idx='".$chall_idx."'";
						$list_info = selectQuery($sql);
						if (!$list_info['idx']){
							$sql = "insert into work_challenges_thema_recom_list(thema_idx, challenges_idx, sort, companyno, ip) values('".$thema_idx."','".$chall_idx."','".$chall_sort."','".$companyno."','".LIP."')";
							$insert_idx = insertIdxQuery($sql);
						}
					}

					if($insert_idx){
						echo "complete";
						exit;
					}

				//추천해제
				}else if($recom == '0'){

					$sql = "select idx from work_challenges_thema_recom_list where state='0' and companyno='".$companyno."' and thema_idx='".$thema_idx."'";
					$list_info = selectAllQuery($sql);
					for($i=0; $i<count($list_info['idx']); $i++){
						$sql = "update work_challenges_thema_recom_list set state='9', editdate=".DBDATE." where idx='".$list_info['idx'][$i]."'";
						$up = updateQuery($sql);
					}

					if($up){
						echo "complete";
						exit;
					}
				}
			}
		}
	}
	exit;
}



//챌린티 템플릿 테마 리스트
if($mode == "thema_recom_list"){

	//챌린지 테마추천
	$sql = "select idx, title from work_challenges_thema where state='0' and recom='1' order by sort asc";
	$chall_rec_info = selectAllQuery($sql);

	//챌린지 테마정보
	$thema_info_title = challenges_thema_info();

	//카테고리정보
	$category = challenges_category();

	//챌린지 추천 리스트
	$sql = "select * from (select a.idx, a.state, a.cate, a.title, a.companyno, a.email, a.pageview, a.temp_flag, a.view_flag, a.keyword,";
	$sql = $sql .= " (select count(1) from work_challenges_thema_zzim_list where a.idx=challenges_idx and state='0' and email='".$user_id."') as zzim,";
	$sql = $sql .= " (select sort from work_challenges_thema_list where a.idx=challenges_idx and state='0' limit 1) as sort,";
	$sql = $sql .= " b.idx as bidx, b.thema_idx from work_challenges as a left join work_challenges_thema_recom_list as b on (a.idx=b.challenges_idx)";
	$sql = $sql .= " where a.state='0' and a.template='1' and a.temp_flag='0' and a.view_flag='0' and a.coaching_chk='0' and b.state='0'";
	$sql = $sql .= " and b.idx is not null ) as a";
	$sql = $sql .= " ORDER BY case when sort is not null then sort end asc, case when sort is null then a.idx end desc";
	//echo $sql;
	//$sql = $sql .= " where r_num between 1 and 12 order by a.idx desc";

	$thema_recom_info = selectAllQuery($sql);

	for($i=0; $i<count($thema_recom_info['idx']); $i++){

		$thema_recom_idx = $thema_recom_info['idx'][$i];
		$thema_recom_cate = $thema_recom_info['cate'][$i];
		
		$thema_recom_title = $thema_recom_info['title'][$i];
		$thema_recom_thema_idx = $thema_recom_info['thema_idx'][$i];
		$thema_recom_pageview = $thema_recom_info['pageview'][$i];
		$thema_recom_zzim = $thema_recom_info['zzim'][$i];
		$thema_recom_keyword = $thema_recom_info['keyword'][$i];

		$thema_recom_list[$thema_recom_thema_idx]['idx'][] = $thema_recom_idx;
		$thema_recom_list[$thema_recom_thema_idx]['title'][] = urldecode($thema_recom_title);
		$thema_recom_list[$thema_recom_thema_idx]['cate'][] = $thema_recom_cate;
		$thema_recom_list[$thema_recom_thema_idx]['pageview'][] = number_format($thema_recom_pageview);
		$thema_recom_list[$thema_recom_thema_idx]['thema_idx'][] = $thema_recom_thema_idx;
		$thema_recom_list[$thema_recom_thema_idx]['zzim'][] = $thema_recom_zzim;
		$thema_recom_list[$thema_recom_thema_idx]['keyword'][] = $thema_recom_keyword;
	}

	/*print "<pre>";
	print_r($thema_recom_list);
	//print_r($thema_info_title);
	//print_r($category);
	print "</pre>";
	*/
	
?>
		<input type="hidden" id="pageno" value="<?//=$gp?>">
		<input type="text" id="page_count" value="<?//=$page_count?>">
		<input type="hidden" id="chall_type">
		<input type="hidden" id="chall_cate">
		<input type="hidden" id="thema_zzim">
		<input type="hidden" id="thema_idx">

		<div class="rew_thema_tit">
			<strong>추천테마</strong>
		</div>
		
		<div class="rew_conts_scroll_04_t">
			<div class="rew_thema_tit">
				<strong>추천테마</strong>
			</div>
			
			
			<?
			if($chall_rec_info['idx']){

				for($i=0; $i<count($chall_rec_info['idx']); $i++){
					$thema_idx = $chall_rec_info['idx'][$i];
					$chall_rec_info_title = $chall_rec_info['title'][$i];

				?>
					<div class="rew_cha_thema_tit">
						<span class="thema_title"><?=$chall_rec_info_title?></span>
						<strong>
							<?=count($thema_recom_list[$thema_idx]['idx'])?>
						</strong>
					</div>

					<div class="rew_cha_list" id="rew_cha_list">
						<div class="rew_cha_list_in">
							<ul class="rew_cha_list_ul" id="template_list">

							<?if($thema_recom_list[$thema_idx]){
								$html = "";
								for($j=0; $j<count($thema_recom_list[$thema_idx]['idx']); $j++){
									
									$chall_idx = $thema_recom_list[$thema_idx]['idx'][$j];
									$title = $thema_recom_list[$thema_idx]['title'][$j];
									$cate = $thema_recom_list[$thema_idx]['cate'][$j];
									$pageview = $thema_recom_list[$thema_idx]['pageview'][$j];
									$zzim = $thema_recom_list[$thema_idx]['zzim'][$j];
									$chall_thema_idx = $thema_recom_list[$thema_idx]['thema_idx'][$j];
									$keyword = $thema_recom_list[$thema_idx]['keyword'][$j];

									if($chall_idx){


										$html = $html .= '<li class="sli2'.($chllday<0?" cha_dend":"").' category_0'.$cate.''.$offset.'" value="'.$chall_idx.'">';
										$html = $html .= '<button class="cha_jjim'.($zzim>0?" on":"").'" id="cha_zzim_'.$chall_idx.'"><span>찜하기</span></button>';
										$html = $html .= '	<a href="#null" onclick="javascript:void(0);">';

										//if($user_id=='marketing@bizforms.co.kr'){
										//	$html = $html .= '	<div class="tdw_list_drag" title="순서 변경"><span>드래그 드랍 기능</span></div>';
										//}

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
									}else{?>
									<?}?>
								<?}
								echo $html;
							?>
							<?}else{?>
								<div class="tdw_list_none">
									<strong><span>등록된 챌린지 테마가 없습니다.</span></strong>
								</div>
							<?}?>
							</ul>
							<div class="rew_cha_more">
								<button><span>more</span></button>
							</div>
						</div>
					</div>
				<?}?>
			<?}else{?>
				<div class="tdw_list_none">
					<strong><span>등록된 추천 테마가 없습니다.</span></strong>
				</div>
			<?}?>
		</div>

	<?php
	exit;
}


//챌린지 템플릿 리스트
if($mode == "challenges_template_list"){

	print "<pre>";
	print_r($_POST);
	print "</pre>";

	//페이지
	$gp = $_POST['gp'];

	//정렬기준
	$rank = $_POST['rank'];
	$rank = preg_replace("/[^0-9]/", "", $rank);

	//테마번호
	$thema_idx = $_POST['thema_idx'];
	if($thema_idx){
		$thema_idx = preg_replace("/[^0-9]/", "", $thema_idx);
	}else{
		$thema_idx = "";
	}

	//내가 찜한 챌린지
	$zzim = $_POST['zzim'];
	$zzim = preg_replace("/[^0-9]/", "", $zzim);

	//챌린지 체크박스(전체:all, 임시저장챌린지:1, 숨김챌린지:2)
	// $viewchk = $_POST['viewchk'];
	$viewchk_all = $_POST['viewchk_all'];
	$viewchk_save = $_POST['viewchk_save']; //temp_flag
	$viewchk_hide = $_POST['viewchk_hide']; //view_flag
	$viewchk = $_POST['viewchk']; //0

	//임시저장 조회
	$temp = $_POST['temp'];

	//수정권한
	$temp_auth = $_POST['temp_auth'];

	//검색어
	$search = $_POST['search'];

	//챌린지 체크박스(전체:all, 임시저장챌린지:1, 숨김챌린지:2)
	$viewchk = $_POST['viewchk'];

	//정렬
	if($rank){
		switch($rank){

			//조회수
			case "1":
				$orderby = " order by a.pageview desc";
				$btn_sort_rank = "조회수 순";
				break;

			//찜많은순
	  		case "2":
				$orderby = " order by a.zzim desc";
				$btn_sort_rank = "찜많은 순";
				//$orderby = "order by CASE WHEN a.chllday > 0 THEN a.chllday END DESC, CASE WHEN a.chllday < 0 THEN a.chllday end desc";
				break;

			//최신등록순
	  		case "3":
				//$orderby = " order by a.idx desc";
				$orderby = " ORDER BY case when sort is not null then sort end asc, case when sort is null then a.idx end desc";
				$btn_sort_rank = "최근 등록 순";
				break;
			default :
				$orderby = " order by a.idx desc";
				$btn_sort_rank = "최근 등록 순";
				break;
		}
	}else{
		$orderby = " order by a.idx desc";
		// $orderby = " ORDER BY case when sort is not null then sort end asc, case when sort is null then a.idx end desc";


		$btn_sort_rank = "최근 등록 순";
	}

	//정상:state=0, 템플릿:template=1, 코칭체크:coaching_chk=0, 숨김여부:view_flag=0, 임시저장:temp_flag=0
	//$where = " where a.state='0' and a.template='1' and a.coaching_chk='0' and a.view_flag='0' and a.temp_flag='0'";

	if($temp == ""){
		$where = " where a.state='0' and a.template='1' and a.coaching_chk='0' and view_flag='0' and temp_flag='0'";
	}else{
		// $where = " where a.state='0' and temp_flag='1' and a.companyno='".$companyno."'";		
	}

	if($temp == '1'){
		$where .= " and a.email='".$user_id."'";
		$thema_list_title = "임시저장 챌린지";
	}

	if($viewchk == "0"){
		$where = $where .= " and (a.temp_flag = '0' and a.view_flag = '0') and a.template = '1'";
	}else{
		if($viewchk_all){
			$where = $where .= " ";
		}else{
			if($viewchk_save){
				$where = $where .= " and a.temp_flag = '1'";
			}
		
			if($viewchk_hide){
				$where = $where .= " and a.view_flag = '1'";
			}
		}
	}

	//챌린지 테마정보
	$thema_info_title = challenges_thema_info();

	//테마 리스트 정보
	$thema_list_info = challenges_thema_list_info();


	/*print "<pre>";
	print_r($thema_list_info);
	print "</pre>";*/

	//테마번호
	if($temp == ""){
		if($thema_idx){
			$where .= " and (select thema_idx from work_challenges_thema_list where state='0' and thema_idx='".$thema_idx."' and a.idx=challenges_idx limit 1)='".$thema_idx."'";
			$thema_list_title = $thema_info_title[$thema_idx];
		}else{
			$thema_list_title = "전체";
		}
	}

	//찜하기
	if($temp == ""){
		if($zzim){
			//$where .= " and b.state=0 and b.email='".$user_id."'";
			$join = " left join work_challenges_thema_zzim_list as b on(a.idx=b.challenges_idx) ";
			$join_where = " and b.state='0' and b.email='".$user_id."'";
			$thema_list_title = "내가 찜한 챌린지";

			$thema_list_content = "찜한 챌린지가 없습니다.";
		}else{
			$thema_list_content = "등록된 챌린지 테마가 없습니다.";
		}
	}

	//검색어
	if($search){
		$search_keyword = $search;
		// $search = urlencode($search); encode로 인하여 검색어 인식 못함 encode 제거 2023.08.10
		$where .= " and (a.title like '%".$search."%' or a.keyword like '%".$search."%') ";
	}

	//템플릿 리스트 갯수
	$sql = "select count(1) cnt from ( select a.idx from work_challenges as a ".$join. $where. $join_where." ) as a";

	if($user_id=='sadary0@nate.com'){
		echo "sql### ".$sql;
	}

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

	if($temp == ""){
		$temp = '0';
	}

	//챌린지 템플릿 리스트
	// $sql = "select * from (select a.idx, a.state, a.cate, a.title, a.companyno, a.email, a.keyword, a.pageview, a.temp_flag, a.view_flag";
	// $sql = $sql .= ", (select count(1) from work_challenges_thema_zzim_list where a.idx=challenges_idx and state='0' and email='".$user_id."') as zzim";
	// $sql = $sql .= ", (select sort from work_challenges_thema_list where a.idx=challenges_idx and state='0' and sort > 0 order by idx desc limit 1) as sort";
	// if ($thema_list_info['thema_idx']){
	// 	$sql = $sql .= ",";
	// }

	// for($i=0; $i<count($thema_list_info['thema_idx']); $i++){
	// 	$thema_idx_loop = $thema_list_info['thema_idx'][$i];
		
	// 	if ($thema_idx_loop != end($thema_list_info['thema_idx'])){
	// 		$field = "themaidx{$thema_idx_loop},";
	// 	}else{
	// 		$field = "themaidx{$thema_idx_loop}";
	// 	}
	// 	$sql = $sql .=" (select thema_idx from work_challenges_thema_list where state='0' and thema_idx='".$thema_idx_loop."' and a.idx=challenges_idx limit 1) as ".$field."";
	// }

	// $sql = $sql .= " from work_challenges as a ".$join. $where . $join_where." ";
	// //$sql = $sql .= " ) as a where r_num between ". $startnum ." and " .$endnum." and view_flag='0' and temp_flag='".$temp."'";
	// $sql = $sql .= " ) as a ".$where;
	// $sql = $sql .= "".$orderby."";

	$sql = "select a.idx, a.state, a.cate, a.title, a.companyno, a.email, a.keyword, a.pageview, a.temp_flag, a.view_flag";
	$sql = $sql .= " , (select count(1) from work_challenges_thema_zzim_list where a.idx=challenges_idx and state='0' and email='".$user_id."') as zzim";
	$sql = $sql .= ", (select sort from work_challenges_thema_list where a.idx=challenges_idx and state='0' and sort > 0 order by idx desc limit 1) as sort";
	$sql = $sql .= " from work_challenges as a ".$where;  


	if($startnum == 1){
		$startnum = 0;
	}

	$sql = $sql .= " limit ".$startnum.", ".$pagesize."";
	if($user_id=='sadary0@nate.com'){
		echo $sql;
		echo "\n";
	}
	
	echo "임시저장체크:".$sql;
	//echo "\n\n";
	//echo "전체리스트";

	$chall_info = selectAllQuery($sql);
	
	//카테고리정보
	$category = challenges_category();


	//테마정보
	$sql = "select challenges_idx, thema_idx from work_challenges_thema_list where state='0' order by idx desc";
	$chall_thema_list_info = selectAllQuery($sql);
	for($i=0; $i<count($chall_thema_list_info['challenges_idx']); $i++){
		$ch_idx = $chall_thema_list_info['challenges_idx'][$i];
		$ch_thema_idx = $chall_thema_list_info['thema_idx'][$i];
		$thema_list_info_title[$ch_idx][] = $ch_thema_idx;
	}

	?>

	<div class="rew_cha_list_func">
    	<div class="rew_cha_list_func_in">
			<div class="rew_cha_count">
				<span class="thema_title" id="thema_title"><?=$thema_list_title?></span>
					<?if($template_auth){?>
						<!-- <div class="thema_title_regi" id="thema_title_edit" style="display:none;">
							<input type="text" value="<?=$thema_list_title?>" class="input_thema_title" id="input_thema_title"/>
							<div class="btn_thema_title">
								<button class="btn_thema_submit" id="btn_thema_submit">확인</button>
								<button class="btn_thema_cancel" id="btn_thema_cancel">취소</button>
							</div>
						</div> -->
					<?}?>
					<strong><?=$total_count?></strong>
					<input type="hidden" id="template_auth" value="<?=$temp_auth?>">
					<input type="hidden" id="pageno" value="<?=$gp?>">
					<input type="text" id="page_count" value="<?=$page_count?>">
					<input type="hidden" id="chall_type">
					<input type="hidden" id="chall_cate">
					<input type="hidden" id="thema_zzim" value="<?=$zzim?>">
					<input type="hidden" id="thema_idx" value="<?=$thema_idx?>">
					<input type="hidden" id="thema_temp" value="<?=$temp?>">
			</div>

			<div class="rew_cha_sort" style="right:240px" id="template_sort">
				<div class="rew_cha_sort_in">
					<button class="btn_sort_on" id="btn_sort_on" value="<?=$rank?>"><span><?=$btn_sort_rank?></span></button>
					<ul>
						<li><button value="1"><span>조회수 순</span></button></li>
						<li><button value="2"><span>찜많은 순</span></button></li>
						<li><button value="3"><span>최근 등록 순</span></button></li>
					</ul>
				</div>
			</div>
			<div class="rew_cha_search" style="right:10px" id="rew_cha_search">
				<div class="rew_cha_search_box">
					<input type="text" class="input_search" id="input_search_thema" placeholder="키워드 검색" value="<?=$search?>">
					<button id="input_search_thema_btn"><span>검색</span></button>
				</div>
			</div>
			
			<?if($temp_auth){?>
				<div class="rew_cha_chk_tab" id="rew_cha_chk_tab">
					<ul>
						<li>
							<div class="chk_tab">
								<input type="checkbox" name="cha_template_tab" id="cha_template_tab_all" checked="">
								<label for="cha_template_tab_all">전체</label>
							</div>
						</li>
						<li>
							<div class="chk_tab">
								<input type="checkbox" name="cha_template_tab" id="cha_chk_tab_save" checked="">
								<label for="cha_chk_tab_save">임시저장 챌린지</label>
							</div>
						</li>
						<li>
							<div class="chk_tab">
								<input type="checkbox" name="cha_template_tab" id="cha_chk_tab_hide" checked="">
								<label for="cha_chk_tab_hide">숨긴 챌린지</label>
							</div>
						</li>
					</ul>
				</div>
			<?}?>
		</div>
	</div>

	<div class="rew_conts_scroll_04" id="rew_conts_scroll_04_list">
		<div class="rew_cha_list" id="rew_cha_list">
			<div class="rew_cha_list_in">
				<ul class="rew_cha_list_ul" id="template_list">

				<?php
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

						if($startnum > 1 && $i==0){
							$offset = " offset0";
						}else{
							$offset ="";
						}


						$html = $html .= '<li class="sli2'.($chllday<0?" cha_dend":"").' category_0'.$cate.''.$offset.'" value="'.$chall_info['idx'][$i].'"  id="themaslist_'.$chall_info['idx'][$i].'">';
						$html = $html .= '<button class="cha_jjim'.($zzim>0?" on":"").'" id="cha_zzim_'.$idx.'"><span>찜하기</span></button>';
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
						echo $html;
					?>
								</ul>
								<div class="rew_cha_more" id="template_more" style="display:none">
									<button><span>more</span></button>
								</div>
							</div>
						</div>
					</div>
					<?
					echo "|".number_format($total_count)."|".$page_count."|".$gp;
					exit;
				}else{?>
							<div class="tdw_list_none">
								<strong><span><?=$thema_list_content?></span></strong>
							</div>
						</div>
						</div>
					</div>
				<?php
				}
	exit;
}



//챌린지 템플릿 리스트(전체/임시저장챌린지/숨길챌린지)
if($mode == "challenges_template_list_check"){

	//페이지
	$gp = $_POST['gp'];

	//정렬기준
	$rank = $_POST['rank'];
	$rank = preg_replace("/[^0-9]/", "", $rank);

	//테마번호
	$thema_idx = $_POST['thema_idx'];
	if($thema_idx){
		$thema_idx = preg_replace("/[^0-9]/", "", $thema_idx);
	}else{
		$thema_idx = "";
	}

	$cate = $_POST['cate'];

	//내가 찜한 챌린지
	$zzim = $_POST['zzim'];
	$zzim = preg_replace("/[^0-9]/", "", $zzim);

	$temp = $_POST['temp'];

	//수정권한
	$temp_auth = $_POST['temp_auth'];

	//검색어
	$search = $_POST['search'];

	//챌린지 체크박스(전체:all, 임시저장챌린지:1, 숨김챌린지:2)
	// $viewchk = $_POST['viewchk'];
	$viewchk_all = $_POST['viewchk_all'];
	$viewchk_save = $_POST['viewchk_save']; //temp_flag
	$viewchk_hide = $_POST['viewchk_hide']; //view_flag
	$viewchk = $_POST['viewchk']; //0
	//정렬
	if($rank){
		switch($rank){

			//조회수
			case "1":
				$orderby = " order by a.pageview desc";
				break;

			//찜많은순
	  		case "2":
				$orderby = " order by a.zzim desc";
				//$orderby = "order by CASE WHEN a.chllday > 0 THEN a.chllday END DESC, CASE WHEN a.chllday < 0 THEN a.chllday end desc";
				break;

			//최신등록순
	  		case "3":
				$orderby = " order by a.idx desc";
				break;
			default :
				$orderby = " order by a.idx desc";
				break;
		}
	}else{
		$orderby = " order by a.idx desc";
	}

	$where = " where a.state='0' and a.coaching_chk='0' and a.template = '1'";

	if($viewchk == "0"){
			$where = $where .= " and (a.temp_flag = '0' and a.view_flag = '0') ";
	}else{
		if($viewchk_all){
			$where = $where .= " ";
		}else{
			if($viewchk_save){
				$where = $where .= " and a.temp_flag = '1'";
			} 
		
			if($viewchk_hide){
				$where = $where .= " and a.view_flag = '1'";
			}
		}
	}

	//챌린지 테마정보
	$thema_info_title = challenges_thema_info();

	//테마 리스트 정보
	$thema_list_info = challenges_thema_list_info();


	//찜하기
	if($viewchk != 1){
		if($zzim){

			$join = " left join work_challenges_thema_zzim_list as b on(a.idx=b.challenges_idx) ";
			$join_where = " and b.state='0' and b.email='".$user_id."'";

		}
	}


	//테마번호
	// if($viewchk != 1){
	// 	if($thema_idx){
	// 		$where = $where .= " and (select thema_idx from work_challenges_thema_list where state='0' and thema_idx='".$thema_idx."' and a.idx=challenges_idx limit 1)='".$thema_idx."'";
	// 		$thema_list_title = $thema_info_title[$thema_idx];
	// 	}else{
	// 		$thema_list_title = "전체";
	// 	}
	// }

	//검색어
	if($search){
		// $search = urlencode($search);
		$where = $where .= " and (a.title like '%".$search."%' or a.keyword like '%".$search."%')";
	}

	//템플릿 리스트 갯수
	$sql = "select count(1) cnt from ( select a.idx from work_challenges as a ".$join. $where. $join_where." ) as a";
	$sql = "select count(1) as cnt from work_challenges as a ".$join. $where. $join_where;

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
		$startnum = 0;
	}else{
		$startnum = ($gp - 1) * $pagesize;
	}

	//페이징 갯수
	if ( ($total_count % $pagesize) > 0 ){
		$page_count = floor($total_count/$pagesize)+1;
	}else{
		$page_count = floor($total_count/$pagesize);
	}
	?>

	<?
	//챌린지 템플릿 리스트
	$sql = "select * from (select a.idx, a.state, a.cate, a.title, a.companyno, a.email, a.keyword, a.pageview, a.temp_flag, a.view_flag";
	$sql = $sql .= ", (select count(1) from work_challenges_thema_zzim_list where a.idx=challenges_idx and state='0' and companyno='".$companyno."' and email='".$user_id."') as zzim";
	if ($thema_list_info['thema_idx']){
		$sql = $sql .= ",";
	}
	
	for($i=0; $i<count($thema_list_info['thema_idx']); $i++){
		$thema_idx_loop = $thema_list_info['thema_idx'][$i];
		
		if ($thema_idx_loop != end($thema_list_info['thema_idx'])){
			$field = "themaidx{$thema_idx_loop},";
		}else{
			$field = "themaidx{$thema_idx_loop}";
		}
		$sql = $sql .=" (select thema_idx from work_challenges_thema_list where state='0' and thema_idx='".$thema_idx_loop."' and a.idx=challenges_idx limit 1) as ".$field."";
	}

	$sql = $sql .= " from work_challenges as a ".$join. $where . $join_where." ";
	//$sql = $sql .= " ) as a where r_num between ". $startnum ." and " .$endnum."";
	$sql = $sql .= " ) as a ";
	$sql = $sql .= "".$orderby."";
	if($startnum==1){
		$startnum=0;
	}
	$sql = $sql .= " limit ".$startnum.", ".$pagesize." ";
	echo "#sql:::".$sql."|";
	// echo "\n\n";
	
	$chall_info = selectAllQuery($sql);
	

	//테마정보
	$sql = "select challenges_idx, thema_idx from work_challenges_thema_list where state='0' order by idx desc";
	$chall_thema_list_info = selectAllQuery($sql);
	for($i=0; $i<count($chall_thema_list_info['challenges_idx']); $i++){
		$ch_idx = $chall_thema_list_info['challenges_idx'][$i];
		$ch_thema_idx = $chall_thema_list_info['thema_idx'][$i];
		$thema_list_info_title[$ch_idx][] = $ch_thema_idx;
	}

	
	/*print "####";
	print "<pre>";
	print_r($thema_list_info_title);
	print "</pre>";*/

	//카테고리정보
	$category = challenges_category();

	if($chall_info['idx']){


		/*print "<pre>";
		print_r($chall_info);
		print "</pre>";*/

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

			/*for($j=0; $j<count($thema_list_info['thema_idx']); $j++){
				$themaidx = $thema_list_info['thema_idx'][$j];
				$thema_idx_list = $chall_info['themaidx'.$themaidx][$i];
				$thema_title_list[$idx][] = $thema_info_title[$thema_idx_list];
			}*/

			$title = urldecode($title);

			if($startnum > 1 && $i==0){
				$offset = " offset0";
			}else{
				$offset ="";
			}

			/*print "#####";
			print "<pre>";
			print_r($thema_title_list);
			print "</pre>";*/


			$html = $html .= '<li class="sli2'.($chllday<0?" cha_dend":"").' category_0'.$cate.''.$offset.'" value="'.$chall_info['idx'][$i].'">';
			$html = $html .= '<button class="cha_jjim'.($zzim>0?" on":"").'" id="cha_zzim_'.$idx.'"><span>찜하기</span></button>';
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

		echo $html."|".number_format($total_count)."|".$page_count."|".$gp;
		exit;
	}else{?>
		<div class="tdw_list_none">
			<strong><span>등록된 챌린지 테마가 없습니다.</span></strong>
		</div>
	<?php
	}
	?>
	<?
	exit;
}


//챌린지 템플릿 더보기
if($mode == "challenges_template_list_more"){

	print "<pre>";
	print_r($_POST);
	print "</pre>";

	//페이지
	$gp = $_POST['gp'];

	//카테고리
	$cate = $_POST['cate'];
	
	//정렬기준
	$rank = $_POST['rank'];
	$rank = preg_replace("/[^0-9]/", "", $rank);

	//테마번호
	$thema_idx = $_POST['thema_idx'];
	if($thema_idx){
		$thema_idx = preg_replace("/[^0-9]/", "", $thema_idx);
	}else{
		$thema_idx = "";
	}

	//내가 찜한 챌린지
	$zzim = $_POST['zzim'];
	$zzim = preg_replace("/[^0-9]/", "", $zzim);

	//수정권한
	$temp_auth = $_POST['temp_auth'];

	//검색어
	$search = $_POST['search'];

	//챌린지 체크박스(전체:all, 임시저장챌린지:1, 숨김챌린지:2)
	$viewchk = $_POST['viewchk'];

	//정렬
	if($rank){
		switch($rank){
			//조회수
			case "1":
				$orderby = " order by a.pageview asc";
				break;

			//찜많은순
	  		case "2":
				$orderby = " order by a.zzim asc";
				//$orderby = "order by CASE WHEN a.chllday > 0 THEN a.chllday END DESC, CASE WHEN a.chllday < 0 THEN a.chllday end desc";
				break;

			//최신등록순
	  		case "3":
				$orderby = " order by a.idx asc";
				break;

			default :
				$orderby = " order by a.idx asc";
				break;
		}
	}else{
		$orderby = " order by a.idx asc";
	}

	//챌린지 체크박스(전체:all, 임시저장챌린지:1, 숨김챌린지:2)
	// $viewchk = $_POST['viewchk'];
	$viewchk_all = $_POST['viewchk_all'];
	$viewchk_save = $_POST['viewchk_save']; //temp_flag
	$viewchk_hide = $_POST['viewchk_hide']; //view_flag
	$viewchk = $_POST['viewchk']; //0

	$where = " where a.state='0' and a.coaching_chk='0' and a.template = '1'";

	//전체
	// if($viewchk){

		
	// 	if($viewchk == "all"){

	// 	//임시저장챌린지
	// 	}else{

	// 		$where .= " and ( ";

	// 		if($viewchk == "1"){
	// 			$tab_where = " OR ";
	// 			$where_chk[] = "(a.temp_flag='1')";
	// 			//숨김 챌린지
	// 		}else if($viewchk == "2"){
	// 			$tab_where = " OR ";
	// 			$where_chk[] = "(a.view_flag='1')";
	// 		}

	// 		for($i=0; $i<count($where_chk); $i++){
					
	// 			if($where_chk[$i] == end($where_chk)){
	// 				$where_loop = "";
	// 			}
				
	// 			$where .= $where_chk[$i] .$where_loop;
	// 		}
	// 		$where .= " ) ";
	// 	}
	// }

	if($viewchk == "0"){
		$where = $where .= " and (a.temp_flag = '0' and a.view_flag = '0') ";
	}else{
		if($viewchk_all){
			$where = $where .= " ";
		}else{
			if($viewchk_save){
				$where = $where .= " and a.temp_flag = '1'";
			}
		
			if($viewchk_hide){
				$where = $where .= " and a.view_flag = '1'";
			}
		}
	}

	//챌린지 테마정보
	$thema_info_title = challenges_thema_info();

	//테마 리스트 정보
	$thema_list_info = challenges_thema_list_info();


	//찜하기
	if($zzim){
		//$where .= " and b.state=0 and b.email='".$user_id."'";

		$join = " left join work_challenges_thema_zzim_list as b on(a.idx=b.challenges_idx) ";
		$join_where = " and b.state='0' and b.email='".$user_id."'";

	}else{
		$join = " ";
		$join_where = " ";
	}


	//테마번호
	if($thema_idx){
		$where .= " and (select thema_idx from work_challenges_thema_list where state='0' and thema_idx='".$thema_idx."' and a.idx=challenges_idx limit 1)='".$thema_idx."'";
		$thema_list_title = $thema_info_title[$thema_idx];
	}else{
		$thema_list_title = "전체";
	}


	//검색어
	if($search){
		$search = urlencode($search);
		$where .= " and (a.title like '%".$search."%' or a.keyword like '%".$search."%')";
	}


	//템플릿 리스트 갯수
	$sql = "select count(1) cnt from ( select a.idx from work_challenges as a ".$join. $where. $join_where." ) as a";
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
		$startnum = 0;
	}else{
		$startnum = ($gp - 1) * $pagesize;
	}

	//페이징 갯수
	if ( ($total_count % $pagesize) > 0 ){
		$page_count = floor($total_count/$pagesize)+1;
	}else{
		$page_count = floor($total_count/$pagesize);
	}



	//챌린지 템플릿 리스트
	$sql = "select * from (select a.idx, a.state, a.cate, a.title, a.companyno, a.email, a.keyword, a.pageview, a.temp_flag, a.view_flag";
	$sql = $sql .= ", (select count(1) from work_challenges_thema_zzim_list where a.idx=challenges_idx and state='0' and companyno='".$companyno."' and email='".$user_id."') as zzim";
	if ($thema_list_info['thema_idx']){
		$sql = $sql .= ",";
	}
	
	for($i=0; $i<count($thema_list_info['thema_idx']); $i++){
		$thema_idx_loop = $thema_list_info['thema_idx'][$i];
		
		if ($thema_idx_loop != end($thema_list_info['thema_idx'])){
			$field = "themaidx{$thema_idx_loop},";
		}else{
			$field = "themaidx{$thema_idx_loop}";
		}
		$sql = $sql .=" (select thema_idx from work_challenges_thema_list where state='0' and thema_idx='".$thema_idx_loop."' and a.idx=challenges_idx limit 1) as ".$field."";
	}

	$sql = $sql .= " from work_challenges as a ".$join. $where . $join_where." ";
	//$sql = $sql .= " ) as a where r_num between ". $startnum ." and " .$endnum."";
	$sql = $sql .= " ) as a";
	$sql = $sql .= "".$orderby."";
	
	$sql = $sql .= " limit ".$startnum." , ".$pagesize." ";
	echo $sql;
	echo "\n\n";

	$chall_info = selectAllQuery($sql);
	
	//카테고리정보
	$category = challenges_category();


	//테마정보
	$sql = "select challenges_idx, thema_idx from work_challenges_thema_list where state='0'";
	// if($cate){
	// 	$sql = $sql .= " and thema_idx";
	// }
	$sql = $sql .= " order by idx desc";
	$chall_thema_list_info = selectAllQuery($sql);
	for($i=0; $i<count($chall_thema_list_info['challenges_idx']); $i++){
		$ch_idx = $chall_thema_list_info['challenges_idx'][$i];
		$ch_thema_idx = $chall_thema_list_info['thema_idx'][$i];
		$thema_list_info_title[$ch_idx][] = $ch_thema_idx;
	}
	


	if($chall_info['idx']){
		/*print "<pre>";
		print_r($chall_info);
		print "</pre>";*/

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


			/*for($j=0; $j<count($thema_list_info['thema_idx']); $j++){
				$themaidx = $thema_list_info['thema_idx'][$j];
				$thema_idx_list = $chall_info['themaidx'.$themaidx][$i];
				$thema_title_list[$idx][] = $thema_info_title[$thema_idx_list];
			}*/

			$title = urldecode($title);

			if($startnum > 1 && $i==0){
				$offset = " offset0";
			}else{
				$offset ="";
			}

			//print_r($thema_title_list);

			$html = $html .= '<li class="sli2'.($chllday<0?" cha_dend":"").' category_0'.$cate.''.$offset.'" value="'.$chall_info['idx'][$i].'">';
			$html = $html .= '<button class="cha_jjim'.($zzim>0?" on":"").'" id="cha_zzim_'.$idx.'"><span>찜하기</span></button>';
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

		echo $html."|".number_format($total_count)."|".$page_count."|".$gp;
		exit;
	}else{?>
		<div class="tdw_list_none">
			<strong><span>등록된 챌린지 테마가 없습니다.</span></strong>
		</div>|0|0|0
	<?
	}
	exit;
}




//템플릿 찜하기
if($mode == "thema_zzim"){

	$idx = $_POST['val'];
	$zzim = $_POST['zzim'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$zzim = preg_replace("/[^0-9]/", "", $zzim);

	if($idx){
		$sql = "select idx, title from work_challenges where state='0' and idx='".$idx."'";

		echo "SSS".$sql;

		$chall_info = selectQuery($sql);	

		if($chall_info['idx']){
			$title = $chall_info['title'];
			$sql = "select idx from work_challenges_thema_zzim_list where state='0' and challenges_idx='".$idx."' and companyno='".$companyno."' and email='".$user_id."'";
			$zzim_info = selectQuery($sql);	

			if($zzim_info['idx']){
				//찜하기해제
				
				if($zzim=='1'){
					$sql = "update work_challenges_thema_zzim_list set state='9', editdate=".DBDATE." where idx='".$zzim_info['idx']."'";
					$up = updateQuery($sql);
					if($up){
						echo "complete";
						exit;
					}
				}
			}else{

				//찜하기설정
				if($zzim == '0'){
					$sql = "insert into work_challenges_thema_zzim_list(challenges_idx,companyno,email,name,ip)";
					$sql = $sql .=" values('".$idx."','".$companyno."','".$user_id."','".$user_name."','".LIP."')";
					$insert_idx = insertIdxQuery($sql);
					if($insert_idx){
						echo "complete";
						exit;
					}
				}
			}
		}
	}
}


//테마 내가 찜한 챌린지
if($mode == "challenges_thema_zzim_list_all"){

	$idx = $_POST['val'];
	$zzim = $_POST['zzim'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$zzim = preg_replace("/[^0-9]/", "", $zzim);

	if($zzim){


		//챌린지 테마정보
		$thema_info_title = challenges_thema_info();

		//테마 리스트 정보
		$thema_list_info = challenges_thema_list_info();


		//전체 카운터
		$sql = "select count(1) cnt from ( select a.idx from work_challenges as a left join work_challenges_thema_zzim_list as b on(a.idx=b.challenges_idx) where a.state='0' and b.state='0' and b.email='".$user_id."') as c";

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
		?>

		<div class="rew_cha_list_func">
			<div class="rew_cha_list_func_in">
				<div class="rew_cha_count">
					<span class="thema_title" id="thema_title">내가 찜한 챌린지</span>
						<?if($template_auth){?>
							<!-- <div class="thema_title_regi" id="thema_title_edit" style="display:none;">
								<input type="text" value="<?=$thema_list_title?>" class="input_thema_title" id="input_thema_title"/>
								<div class="btn_thema_title">
									<button class="btn_thema_submit" id="btn_thema_submit">확인</button>
									<button class="btn_thema_cancel" id="btn_thema_cancel">취소</button>
								</div>
							</div> -->
						<?}?>
						<strong><?=$total_count?></strong>
						<input type="hidden" id="pageno" value="<?=$gp?>">
						<input type="text" id="page_count" value="<?=$page_count?>">
						<input type="hidden" id="chall_type">
						<input type="hidden" id="chall_cate">
						<input type="hidden" id="thema_zzim" value="<?=$zzim?>">
						<input type="hidden" id="thema_idx" value="<?=$thema_idx?>">
				</div>

				<div class="rew_cha_sort" style="right:240px" id="template_sort">
					<div class="rew_cha_sort_in">
						<button class="btn_sort_on" id="btn_sort_on" value="1"><span>조회수 순</span></button>
						<ul>
							<li><button value="1"><span>조회수 순</span></button></li>
							<li><button value="2"><span>찜많은 순</span></button></li>
							<li><button value="3"><span>최근 등록 순</span></button></li>
						</ul>
					</div>
				</div>
				<div class="rew_cha_search" style="right:10px" id="rew_cha_search">
					<div class="rew_cha_search_box">
						<input type="text" class="input_search" id="input_search_thema" placeholder="키워드 검색">
						<button id="input_search_thema_btn"><span>검색</span></button>
					</div>
				</div>
				
				<?if($template_auth){?>
					<div class="rew_cha_chk_tab" id="rew_cha_chk_tab">
						<ul>
							<li>
								<div class="chk_tab">
									<input type="checkbox" name="cha_template_tab" id="cha_template_tab_all" checked="">
									<label for="cha_template_tab_all">전체</label>
								</div>
							</li>
							<li>
								<div class="chk_tab">
									<input type="checkbox" name="cha_template_tab" id="cha_chk_tab_save" checked="">
									<label for="cha_chk_tab_save">임시저장 챌린지</label>
								</div>
							</li>
							<li>
								<div class="chk_tab">
									<input type="checkbox" name="cha_template_tab" id="cha_chk_tab_hide" checked="">
									<label for="cha_chk_tab_hide">숨긴 챌린지</label>
								</div>
							</li>
						</ul>
					</div>
				<?}?>
			</div>
		</div>

		<div class="rew_conts_scroll_04" id="rew_conts_scroll_04_list">
			<div class="rew_cha_list" id="rew_cha_list">
				<div class="rew_cha_list_in">
					<ul class="rew_cha_list_ul" id="template_list">

		<?php


		$sql = "select * from (select a.idx , a.cate, a.title, a.pageview, b.state, b.email, a.keyword";
		$sql = $sql .= ", (select count(1) from work_challenges_thema_zzim_list where a.idx=challenges_idx and state='0') as zzim";
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
			$sql = $sql .=" (select thema_idx from work_challenges_thema_list where state='0' and thema_idx='".$thema_idx."' and a.idx=challenges_idx limit 1) as ".$field."";
		}

		$sql = $sql .=" from work_challenges as a left join work_challenges_thema_zzim_list as b on(a.idx=b.challenges_idx) where a.state=0 and b.state=0 and b.companyno='".$companyno."' and b.email='".$user_id."'";
		//$sql = $sql .= " ) as a where r_num between ". $startnum ." and " .$endnum."";
		$sql = $sql .= " ) as a ";
		$sql = $sql .= "".$orderby."";
		if($startnum==1){
			$startnum = 0;
		}
		$sql = $sql .= " limit ".$startnum.", ".$pagesize."";

		echo $sql;
		echo "\n\n";

		$zzim_info = selectAllQuery($sql);

		//카테고리정보
		$category = challenges_category();

		if($zzim_info['idx']){
			for($i=0; $i<count($zzim_info['idx']); $i++){
				$idx = $zzim_info['idx'][$i];
				$state = $zzim_info['state'][$i];
				$cate = $zzim_info['cate'][$i];
				$title = $zzim_info['title'][$i];
				$zzim = $zzim_info['zzim'][$i];
				$temp_flag = $zzim_info['temp_flag'][$i];
				$pageview = $zzim_info['pageview'][$i];
				$view_flag = $zzim_info['view_flag'][$i];
				$keyword = $zzim_info['keyword'][$i];
				$title = urldecode($title);

				if($startnum > 1 && $i==0){
					$offset = " offset0";
				}else{
					$offset ="";
				}
		
				$html = $html .= '<li class="sli2'.($chllday<0?" cha_dend":"").' category_0'.$cate.''.$offset.'" value="'.$idx.'">';
				$html = $html .= '<button class="cha_jjim'.($zzim>0?" on":"").'" id="cha_zzim_'.$idx.'"><span>찜하기</span></button>';
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

			echo $html."|".number_format($total_count)."|".$page_count."|".$gp;
			exit;
		}else{?>

			<div class="tdw_list_none">
				<strong><span>찜한 챌린지가 없습니다.</span></strong>
			</div>

		<?php
		}
		?>

				</ul>
			</div>
		</div>
	</div>

	<?
	}
	exit;
}


//테마 제목 변경
if($mode == "thema_title_edit"){

	$val = $_POST['val'];
	$thema_idx = $_POST['thema_idx'];
	
	$thema_idx = preg_replace("/[^0-9]/", "", $thema_idx);
	if($thema_idx){
	
		$sql = "select idx from work_challenges_thema where state='0' and idx='".$thema_idx."'";
		$thema_info = selectQuery($sql);
		if($thema_info['idx']){

			$sql = "update work_challenges_thema set title='".$val."', editdate=".DBDATE." where idx='".$thema_info['idx']."'";
			$up = updateQuery($sql);
			if($up){

				//회원별 테마 제목 변경
				$sql = "select idx from work_challenges_thema_user_list where state='0' and thema_idx='".$thema_info['idx']."'";
				$thema_user_info = selectQuery($sql);
				if($thema_user_info['idx']){
					$sql = "update work_challenges_thema_user_list set title='".$val."', editdate=".DBDATE." where state='0' and thema_idx='".$thema_info['idx']."'";
					$up = updateQuery($sql);
				}

				echo "complete";
				exit;
			}
		}
	}
	exit;
}
?>

