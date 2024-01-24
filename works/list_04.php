<?php

	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir  . "inc_lude/header_04.php";
?>

    <!-- include summernote css/js-->
	<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
$(document).ready(function(){

	var fontList = ['맑은 고딕','나눔 고딕','굴림','돋움','바탕','궁서','Noto Sans KR' /*, 'Arial', 'Arial Black', 'Noto Sans KR', 'Tahoma', 'Courier New'*/];

	
	$('#contents').summernote({
		placeholder: '할일을 입력해주세요.',
		lang: "ko-KR", // 한글 설정
		width: 690,
		height: 400,
		maxHeight: 400,
		fontNames: fontList,
		toolbar: [
            //[groupName, [list of button]]
            //['fontname', ['fontname']],
			['font', ['fontname','fontsize','fontsizeunit']],
            ['style', ['bold', 'italic' /*, 'underline', 'strikethrough' , 'clear'*/ ]],
            ['color', ['forecolor', 'color']],
            ['table', ['table']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['link', 'picture'/*,'video'*/ ]],
            /*['view', ['fullscreen', 'help']],*/
            ['his', ['undo', 'redo']]
        ]
	});
});

</script>



<textarea name="contents" id="contents" class="input_01"></textarea>

<br>

<button id="write_btn">등록하기</button>


<br><Br>

<div class="tc_tab_function">
	<div class="tc_tab">
		<ul>
			<li><button class="tab_day<?=$tab_day?>"><span>일일</span></button></li>
			<li><button class="tab_week<?=$tab_week?>"><span>주간</span></button></li>
			<?/*<li><button class="tab_month <?=$tab_month?>" value="month"><span>한달</span></button></li>*/?>
			<input type="hidden" name="tabval" value="<?=$dtab?>">
		</ul>
	</div>
</div>


<div class="tc_index_list">

</div>