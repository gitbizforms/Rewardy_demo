<?
//header페이지
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "../inc_lude/header_about.php";

if($_POST['idx']){
	$idx = $_POST['idx'];
}else{
	$idx = $_GET['idx'];
}

$sql = "select idx, state, contents, editdate, title from bro_notice where idx = '".$idx."' ";
$page = selectQuery($sql);

$sql = "select idx, state, title from bro_notice where idx < '".$idx."' and state = '1' order by idx desc limit 0,1";
$prev = selectQuery($sql);

$sql = "select idx, state, title from bro_notice where idx > '".$idx."' and state = '1' order by idx asc limit 0,1";
$next = selectQuery($sql);

if($page['idx']){
	//$ch_contents =  urldecode($contents_info['contents']);
	$ch_contents =  $page['contents'];
	//$ch_contents =  rawurldecode($contents_info['contents']);
	// $ch_contents = preg_replace('/\r\n|\r|\n/','',$ch_contents);
	//	$ch_contents = addslashes($ch_contents);
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>RewardyBackoffice</title>
        <script>
            if (!M9_SET) { var M9_SET = {}; }
            M9_SET['mong9_editor_use'] = '1'; // Mong9 에디터 사용
            M9_SET['mong9_url'] = 'https://rewardy.co.kr/editors/ckeditor/plugins/mong9-editor/'; // 몽9 에디터 주소
        </script>

        <script src="../../editors/ckeditor/plugins/mong9-editor/source/js/mong9.js"></script>

        <link rel="stylesheet" href="../../editors/ckeditor/plugins/mong9-editor/source/etc/bootstrap-icons/bootstrap-icons.min.css">
        <link rel="stylesheet" href="../../editors/ckeditor/plugins/mong9-editor/source/css/mong9-base.css"> <!--뷰어에서도 필요-->
        <link rel="stylesheet" href="../../editors/ckeditor/plugins/mong9-editor/source/css/mong9.css"> <!--뷰어에서도 필요-->
        <link rel="stylesheet" href="../../editors/ckeditor/plugins/mong9-editor/source/css/mong9-m.css" media="all and (max-width: 768px)">
        <link rel="stylesheet" href="../../editors/ckeditor/plugins/mong9-editor/source/css/mong9-e.css" media="all and (max-width: 576px)">
    </head>
	<body>
		<div class="rb_main_sub_img">
			<div class="notice_wrap">
				<div class="notice_top">
					<strong>공지사항</strong>
				</div>

				<div class="notice_mid">
					<div class="notice_view">
						<div class="notice_view_title">
							<strong><?=$page['title']?></strong>
							<span><?=$page['editdate']?></span>
						</div>
						<div class="notice_view_desc">
							<?=$ch_contents?>
						</div>
						<div class="notice_view_move">
							<div class="notice_view_prev">
								<button class="notice_<?=$prev['idx']?>">
									<span>이전글</span>
									<strong><?=$prev['title']?$prev['title']:'이전글이 없습니다.'?></strong>
								</button>
							</div>
							<div class="notice_view_next">
								<button class="notice_<?=$next['idx']?>">>
									<strong><?=$next['title']?$next['title']:'다음글이 없습니다.'?></strong>
									<span>다음글</span>
								</button>
							</div>
						</div>
					</div>
				</div>

				<div class="notice_bottom">
					<div class="notice_btn">
						<button onclick="location.href='/about/customer/notice.php'"><span>목록</span></button>
					</div>
				</div>
			</div>
		</div>
	</body>

	<script src="../editors/ckeditor/ckeditor.js"></script>
    <script src="../editors/ckeditor/plugins/mong9-editor/source/js/mong9-connect.js"></script>
    <script type="text/javascript">
			$(document).on("click","button[class^=notice_]", function(){
				idx = $(this).attr("class");
				// alert(idx);
				no = idx.replace("notice_", "");
				if(!no){
					return false;
				}
				location.href = "notice_list.php?idx="+no;
			});
    </script>

	<?
	//footer 페이지
	include $home_dir . "/inc_lude/footer_about.php";
	?>
