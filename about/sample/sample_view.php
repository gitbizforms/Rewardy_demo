<?
//header페이지
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "../inc_lude/header_about.php";
?>

<link rel="stylesheet" type="text/css" href="../../../html/css/sub_05.css" />
<link rel="stylesheet" type="text/css" href="../../../html/css/sub_05_01.css" />
<?

if($_POST['idx']){
	$idx = $_POST['idx'];
}else{
	$idx = $_GET['idx'];
}

$sql = "select idx, state, contents, editdate, title, title_color from bro_sample where idx = '".$idx."' ";
$page = selectQuery($sql);

$sql = "select idx, state, title from bro_sample where idx < '".$idx."' and state = '1' order by idx desc limit 0,1";
$prev = selectQuery($sql);
if(!$prev['title']){
  $prev_title = "이전글이 없습니다.";
}else{
  $prev_title = $prev['title'];
}

$sql = "select idx, state, title from bro_sample where idx > '".$idx."' and state = '1' order by idx asc limit 0,1";
$next = selectQuery($sql);
if(!$next['title']){
  $next_title = "다음글이 없습니다.";
}else{
  $next_title = $next['title'];
}
if($page['idx']){
	$ch_contents =  $page['contents'];
}

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


    <div class="sub_05_01_cont">
      <div class="sub_05_01_cont_in">
        <!-- cont_tit -->
        <div class="cont_arrow">
          <div class="cont_arrow_in">
              <div class="slide_prev">
                <button class="notice_<?=$prev['idx']?>"><span>이전글 <strong><?=$prev_title?></strong></span></button>
              </div>
              <div class="slide_next">
                <button class="notice_<?=$next['idx']?>"><span><strong><?=$next_title?></strong>다음글</span></button>
              </div>
          </div>
        </div>
        <div class="cont_main">
          <div class="cont_main_in">
            <div class="cont_tit">
              <h3><?=$page['title']?></h3>
              <!-- <div class="cont_tit_bottom">
                <span>챌린지</span>
                <button><span>공유하기</span></button>
              </div> -->
            </div>
            <!-- cont -->
            <div class="cont_sec">
              <div class="cont_sec_in">
                <?=$page['contents']?>
              </div>
            </div>
          </div>
          <div class="cont_arrow">
            <div class="cont_arrow_in">
              <div class="slide_prev">
                <button class="notice_<?=$prev['idx']?>"><span>이전글 <strong><?=$prev_title?></strong></span></button>
              </div>
              <div class="slide_next">
                <button class="notice_<?=$next['idx']?>"><span><strong><?=$next_title?></strong>다음글</span></button>
              </div>
            </div>
          </div>
          <div class="page_list">
            <button><span>목록</span></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  //이전글, 다음글
  $(document).on("click","button[class^=notice_]", function(){
				idx = $(this).attr("class");
				no = idx.replace("notice_", "");
				if(!no){
					return false;
				}
				location.href = "sample_view.php?idx="+no;
			});
</script>
<?
//footer 페이지
include $home_dir . "/inc_lude/footer_about.php";
?>

