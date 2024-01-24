<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "/inc_lude/back_header.php";

	//오늘날짜
    $today = TODATE;

    if($_POST['idx']){
        $idx = $_POST['idx'];
    }else{
        $idx = $_GET['idx'];
    }

	$sql = "select idx, state, contents, title from bro_sample where idx = '".$idx."' ";
    $page = selectQuery($sql);

    if($page['idx']){
        $ch_title=  $page['title'];
        $ch_contents =  $page['contents'];
        $btn_name = "활용사례 수정";
    }else{
        $btn_name = "활용사례 등록";
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

<script src="../editors/ckeditor/plugins/mong9-editor/source/js/mong9.js"></script>

<link rel="stylesheet" href="../editors/ckeditor/plugins/mong9-editor/source/etc/bootstrap-icons/bootstrap-icons.min.css">
<link rel="stylesheet" href="../editors/ckeditor/plugins/mong9-editor/source/css/mong9-base.css">
<link rel="stylesheet" href="../editors/ckeditor/plugins/mong9-editor/source/css/mong9.css">
<link rel="stylesheet" href="../editors/ckeditor/plugins/mong9-editor/source/css/mong9-m.css" media="all and (max-width: 768px)">
<link rel="stylesheet" href="../editors/ckeditor/plugins/mong9-editor/source/css/mong9-e.css" media="all and (max-width: 576px)">
    </head>
    <body class="sb-nav-fixed">
		<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
			<?php include "admin_top.php"; ?>
		</nav>
		<div id="layoutSidenav">
            <?php include "admin_sidebar.php"; ?>
            	<div id="layoutSidenav_content">
                    <div class="container-fluid px-4" id="page-screen">
						<h1 class="mt-4">브로슈어 활용사례</h1>
                        <div class="card mb-4">
                            <input type="hidden" id="backwrite_type" value="bro_sample_write">
                            <? if($idx){?>
                                <input type="hidden" id="notice_up" value="<?=$idx?>">
                            <?}?>
                            <div class="card-body">
                                <div class="container">
                                    <div class="row">
                                        <div class="mb-3 col-md-4">
                                            <!-- <label for="notice_title" class="form-label">활용사례 제목</label> -->
                                            <input type="text" class="form-control" id="notice_title" placeholder="활용사례 타이틀을 입력해주세요." value="<?=$ch_title?>">
                                        </div>
                                        <div class="mb-3 col-md-2 ms-2">
                                            <select class="form-select border" aria-label="Default select example" id="title_color">
                                                <option selected>타이틀 카드 색상</option>
                                                <option value="red">Red</option>
                                                <option value="blue">Blue</option>
                                                <option value="orange">Orange</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 col-md-2 ms-2">
                                            <select class="form-select border" aria-label="Default select example" id="category">
                                                <option selected>활용사례 유형</option>
                                                <option value="1">업무</option>
                                                <option value="2">교육</option>
                                                <option value="3">생활/문화</option>
                                                <option value="4">건강</option>
                                                <option value="5">행사</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 col-md-2 ms-2">
                                            <select class="form-select border" aria-label="Default select example" id="sample_service">
                                                <option selected>서비스 종류</option>
                                                <option value="work">업무</option>
                                                <option value="challenge">챌린지</option>
                                                <option value="party">파티</option>
                                                <option value="insight">인사이트</option>
                                                <option value="live">라이브</option>
                                                <option value="reward">리워드</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
								<div class="rew_member_sub_func_tab my-1">
                                    <textarea name="content" id="content_notice"><?=$ch_contents?></textarea>
                                    <button type="button" class="btn btn-dark mt-3" id="notice_btn"><?=$btn_name?></button>
								</div>
                            </div>
                        </div>
                    </div>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Bizform & Rewardy</div>
                            <div>
                                <!-- <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a> -->
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
    <script src="../editors/ckeditor/ckeditor.js"></script>
    <script src="../editors/ckeditor/plugins/mong9-editor/source/js/mong9-connect.js"></script>
    <script type="text/javascript">
        //<![CDATA[
            CKEDITOR.replace('content');

            // 1. 언어 선택(필요시)
            //CKEDITOR.config.language = 'ko';

            // 3. Mong9Editor : Allow HTML, etc
            CKEDITOR.config.allowedContent = {
                $1: {
                    // Use the ability to specify elements as an object.
                    elements: CKEDITOR.dtd,
                    attributes: true,
                    styles: true,
                    classes: true
                }
            };

            // 4. Mong9Editor : Disallow <style>, <script>, on events(onload,onmouse..)
            CKEDITOR.config.disallowedContent = 'style;script; *[on*]';
            
            // 5. 몽9 에디터 버튼 삽입
    </script>
</html>
 