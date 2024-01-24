<?
//header페이지
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "../inc_lude/header_about.php";

?>

<link rel="stylesheet" type="text/css" href="../../../html/css/sub_05.css" />
<?

$sql = "select count(idx) as cnt from bro_sample where state = '1'";
$history_info_cnt = selectQuery($sql);
if($history_info_cnt['cnt']){ 
	$total_count = $history_info_cnt['cnt'];
}

$code = "all";
	
$url = "sample_list";
$string = "&page=".$url;

$p = $_POST['p']?$_POST['p']:$_GET['p'];

if (!$p){
	$p = 1;
}

// $pagingsize = 5;					//페이징 사이즈
$pagesize = 12;						//페이지 출력갯수
$startnum = 0;						//페이지 시작번호
$endnum = $p * $pagesize;			//페이지 끝번호

//시작번호
if ($p == 1){
  $startnum = 0;  
}else{
  $startnum = ($p - 1) * $pagesize;
}

$sql = "select idx, category, service, email, title, contents, title_color, regdate, state, editdate, (select name from work_member where email = bro_sample.email and state = '0') as name from bro_sample where state = '1' order by idx desc limit ".$startnum.",".$pagesize;	
$query = selectAllQuery($sql);

$chall_arr = ['work'=>'업무','challenge'=>'챌린지','party'=>'파티','insight'=>'인사이트','live'=>'라이브','reward'=>'리워드'];
?>
<div class="rew_sub_05">
        <div class="rew_sub_05_in">
          <!-- banner -->
          <div class="sub_05_banner">
            <div class="sub_05_banner_in">
              <div class="banner_text">
                <h2>리워디로 우리회사의 문화가 달라집니다.</h2>
                <span>리워디를 통해서 회사가 얼마나 더 좋아질 수 있는지<br>
                  아래의 활용사례들을 통해서 알아보세요~</span>
              </div>
            </div>
          </div>

          <div class="sub_05_cont">
            <div class="sub_05_cont_in">
              <!-- tab_btn -->
              <div class="tab_btn">
                <ul>
                  <li class="on btn_all"><button><span>전체</span></button></li>
                  <li class="btn_work" value="1"><button><span>업무</span></button></li>
                  <li class="btn_edu" value="2"><button><span>교육</span></button></li>
                  <li class="btn_life" value="3"><button><span>생활/문화</span></button></li>
                  <li class="btn_hea" value="4"><button><span>건강</span></button></li>
                  <li class="btn_event" value="5"><button><span>행사</span></button></li>
                </ul>
              </div>
              <!-- cont_top -->
              <div class="cont_tit">
                <div class="cont_tit_txt">
                  <span>총 <strong><?=$total_count?></strong>건</span>
                </div>
                <div class="cont_search">
                  <input type="text" class="input_search" name="cont_search" placeholder="검색">
                  <button><span>검색</span></button>
                </div>
              </div>
              <!-- cont_list -->
              <div class="cont_main">
                <div class="cont_main_in">
                  <ul>
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
                    <?}
                    if(!$query['idx']){?>
                      <span>등록된 활용사례가 없습니다.</span>
                    <?}?>
                  </ul>
                  <div class="cont_more">
                    <input type="hidden" id="sample_page" value="<?=$p?>">                      
                    <button id="sample_more"><span>더보기</span></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
<?
//footer 페이지
include $home_dir . "/inc_lude/footer_about.php";
?>

