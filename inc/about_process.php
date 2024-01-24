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
include_once($home_dir."/PHPMailer/libphp-phpmailer/PHPMailerAutoload.php");

$mode = $_POST["mode"];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
	// $companyno = $_COOKIE['companyno'];
}

//오늘날짜
$today = TODATE;

//전월날짜
$timestamp = strtotime("-1 weeks");
$last_week = date("Y-m-d", $timestamp);

//기업별 리스트
if($mode == "notice_list"){
	$url = "notice_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$sql = "select count(idx) as cnt from bro_notice where state = '1'";
    $history_info_cnt = selectQuery($sql);
    if($history_info_cnt['cnt']){ 
        $total_count = $history_info_cnt['cnt'];
    }

        $code = "all";

        
    $p = $_POST['p']?$_POST['p']:$_GET['p'];
    if (!$p){
        $p = 1;
    }

    $pagingsize = 5;					//페이징 사이즈
    $pagesize = 5;						//페이지 출력갯수
    $startnum = 0;						//페이지 시작번호
    $endnum = $p * $pagesize;			//페이지 끝번호

    //시작번호
    if ($p == 1){
        $startnum = 0;
    }else{
    $startnum = ($p - 1) * $pagesize;
    }

    $sql = "select idx, email, title, regdate, state, editdate, (select name from work_member where email = bro_notice.email and state = '0') as name from bro_notice where state = '1' order by idx desc limit ". $startnum.",".$pagesize;	
    $query = selectAllQuery($sql);

	for($i=0;$i<count($query['idx']);$i++){
        $idx = $query['idx'][$i] ?>
        <li class="notice_<?=$idx?>">
            <div class="notice_desc">
                <div class="notice_num">
                    <span><?=$i+$startnum+1?></span>
                </div>
                <div class="notice_title">
                    <a href="/about/customer/notice_list.php?idx=<?=$idx?>"><span><?=$query['title'][$i]?></span></a>
                </div>
                <div class="notice_date">
                    <span><?=$query['editdate'][$i]?></span>
                </div>
            </div>
        </li>
    <?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
		<?}
	?>|
	<input type="hidden" id="page_num" value="<?=$p?>">
	<input type="hidden" value="<?=$search?>" id="bro_search">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$kind?>" id="kind">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<? echo "|".$tclass."|notice|";
}

// FAQs(자주묻는 질문) 리스트
if($mode == "faq_list"){
	$url = "faq_list";
	$string = "&page=".$url;

    $search = $_POST['search'];

    $where = "";
    if($search){
        $where = $where .= " and title like '%".$search."%' ";
    }
	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$sql = "select count(idx) as cnt from bro_faq where state = '1'";
    $history_info_cnt = selectQuery($sql);
    if($history_info_cnt['cnt']){ 
        $total_count = $history_info_cnt['cnt'];
    }

        $code = "all";

        
    $p = $_POST['p']?$_POST['p']:$_GET['p'];
    if (!$p){
        $p = 1;
    }

    $pagingsize = 5;					//페이징 사이즈
    $pagesize = 5;						//페이지 출력갯수
    $startnum = 0;						//페이지 시작번호
    $endnum = $p * $pagesize;			//페이지 끝번호

    //시작번호
    if ($p == 1){
        $startnum = 0;
    }else{
    $startnum = ($p - 1) * $pagesize;
    }

    $sql = "select idx, email, title, contents, regdate, state, editdate, (select name from work_member where email = bro_faq.email and state = '0') as name from bro_faq where state = '1' ".$where." order by idx desc limit ". $startnum.",".$pagesize;	
    $query = selectAllQuery($sql);
    ?>
        <?for($i=0;$i<count($query['idx']);$i++){
            $idx = $query['idx'][$i] ?>
            <li>
                <div class="faq_q">
                    <div class="faq_q_txt">
                        <span><?=$query['title'][$i]?></span>
                    </div>
                </div>
                <div class="faq_a">
                    <div class="faq_a_txt">
                        <?=$query['contents'][$i]?>
                    </div>
                </div>
            </li>
        <?}
            if(count($query['idx'])==0){?>
                <tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
            <?}?>|
        <!-- <input type="hidden" id="page_num" value="<?=$p?>">
        <input type="hidden" value="<?=$search?>" id="bro_search">
        <input type="hidden" value="<?=$tclass?>" id="tclass">
        <input type="hidden" value="<?=$kind?>" id="kind">| -->
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<? echo "|".$tclass."|faq|";
}

if($mode == "sample_list"){
    $cate = $_POST['cate'];
    $search = $_POST['search'];

    $where = "";

    if($cate){
        $where = $where .= "and category = '".$cate."' ";
    }

    if($search){
        $where = $where .= "and title like '%".$search."%' ";
    }

    $page = 1;

    $sql = "select idx, service, title from bro_sample where state = '1' ".$where." order by idx desc limit 0,12";
    $query = selectAllQuery($sql);

    $chall_arr = ['work'=>'업무','challenge'=>'챌린지','party'=>'파티','insight'=>'인사이트','live'=>'라이브','reward'=>'리워드']; 
    
    if($cate){?>
    <input type="hidden" id="sample_cate" value="<?=$cate?>">
    <?} for($i=0; $i<count($query['idx']); $i++){
            if(array_key_exists($query['service'][$i],$chall_arr)){
            $service = $chall_arr[$query['service'][$i]];
            }?>
            <li class="char_hea">
            <input type="hidden" value="<?=$query['idx'][$i]?>" id="sam_li_idx">
            <a href="#">
                <h3><?=$query['title'][$i]?></h3>
                <span><?=$service?></span>
            </a>
            </li>
        <?}
         if(!$query['idx']){?>
            <span>등록된 활용사례가 없습니다.</span>
          <?}?>
        
    <? echo "|success|".$page;
    exit;
    }

    if($mode == "sample_more"){
        $cate = $_POST['cate'];
        $search = $_POST['search'];

        $where = "";

        if($cate){
            $where = $where .= "and category = '".$cate."' ";
        }

        if($search){
            $where = $where .= "and title like '%".$search."%' ";
        }

        $p = $_POST['p'];
        if($p){
            $startnum = $p * 12;
            $limit = " limit ".$startnum.",12";
            $p = $p + 1;
        }
        $sql = "select idx, service, title from bro_sample where state = '1' ".$where." order by idx desc".$limit;
        $query = selectAllQuery($sql);
    
        $chall_arr = ['work'=>'업무','challenge'=>'챌린지','party'=>'파티','insight'=>'인사이트','live'=>'라이브','reward'=>'리워드'];
        ?>
            <!-- <input type="hidden" id="sample_search" value="<?=$search?>"> -->
            <? for($i=0; $i<count($query['idx']); $i++){
                if(array_key_exists($query['service'][$i],$chall_arr)){
                $service = $chall_arr[$query['service'][$i]];
                }?>
                <li class="char_hea">
                <input type="hidden" value="<?=$query['idx'][$i]?>" id="sam_li_idx">
                <a href="#">
                    <h3><?=$query['title'][$i]?></h3>
                    <span><?=$service?></span>
                </a>
                </li>
            <?}?>
                
        <? echo "|success|".$p;
        exit;
    }

?>