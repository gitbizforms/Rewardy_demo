<?

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "inc_lude/conf.php";

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<link href="/editor/summernote/summernote-lite.css<?php echo VER;?>" rel="stylesheet">
<script src="/editor/summernote/summernote-lite.js<?php echo VER;?>"></script>
<script src="/editor/summernote/lang/summernote-ko-KR.min.js<?php echo VER;?>"></script>

<style>
	
	@import url(//fonts.googleapis.com/earlyaccess/nanumgothic.css);
	.nanumgothic * {
		font-family: 'Nanum Gothic';
	}

	.img-box { border:1px solid; padding:10px; width:200px;height:120px; }

	.remove_img_preview {
		position:relative;
		top:-25px;
		right:5px;
		background:black;
		color:white;
		border-radius:50px;
		font-size:0.9em;
		padding: 0 0.3em 0;
		text-align:center;
		cursor:pointer;
	}

	.thumb {
		width: 100%;
		height: 100%;
		margin: 0.2em -0.7em 0 0;
	}

	.note-editable p {
		margin: 0;
	}

	.note-editable hr {
		border: 1px solid #c1c1c1;
	}
</style>
<script>
var text_null ='<p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px;"><font color="#9c9c94">챌린지 내용을 작성해주세요.</font></span><font color="#9c9c94"><br></font></p><p><span style="font-size: 12px;">&#xFEFF;</span><br></p><font color="#9c9c94"><hr></font><span style="font-size: 12px;">&#xFEFF;</span><span style="font-size: 20px; color: rgb(156, 156, 148); font-family: &quot;Noto Sans KR&quot;;">✔️ </span><b style="font-size: 20px; color: rgb(156, 156, 148);"><span style="font-family: &quot;Noto Sans KR&quot;;">챌린지 참여방법</span></b><font color="#9c9c94"><p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px;">1. </span><font color="#9c9c94" style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px;">챌린지 참여방법을 작성해주세요.</font><br></p><p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; line-height: 25px;">2. </span></p><p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; line-height: 25px;">3. </span><br></p><p><br></p><hr><p><span style="font-size: 12px;">&#xFEFF;</span><span style="font-size: 20px; font-family: &quot;Noto Sans KR&quot;;">📌 </span><b style="font-size: 20px;"><span style="font-family: &quot;Noto Sans KR&quot;;">챌린지 유의사항</span></b></p><p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px;">1. </span><font color="#9c9c94" style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px;">챌린지 유의사항을 입력해주세요.</font><br></p><p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; line-height: 25px;">2. </span></p><p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; line-height: 25px;">3. </span><br></p></font><p></p>';

$(document).ready(function(){

	var fontList = ['맑은 고딕','굴림체','돋움체','바탕체','궁서체','Nanum Gothic','Noto Sans KR','Courier New','Arial Black','Arial','Tahoma'];
	var fontSizes = [ '8', '9', '10', '11', '12', '13', '14','16', '18', '20', '22', '24', '28', '30', '36', '50', '72'];
	var toolbar = 
		[['fontname', 		[ 'fontname' ] ],
		['fontsize',		[ 'fontsize' ] ],
		['style',			[ 'bold', 'italic', 'underline', 'strikethrough' , 'forecolor', 'backcolor', 'paragraph' ,'clear'] ],
		['height',			[ 'height']],
		['insert',			[ 'link', 'picture' ,'video'] ],
		['hr',				[ 'hr' ]]
	];

	var setting = {
		//placeholder: '챌린지 내용을 입력해주세요.',
		width: 760,
		height : 600,
		minHeight : null,
		maxHeight : null,
		focus : true,
		lang : 'ko-KR',
		toolbar : toolbar,
		fontSizes : fontSizes,
		fontNames : fontList
		
		/*callbacks : { //여기 부분이 이미지를 첨부하는 부분
			onImageUpload : function(files, editor,	welEditable) {
				for (var i = files.length - 1; i >= 0; i--) 
				{
					//uploadSummernoteImageFile(files[i],	this);
				}
			},
			onMediaDelete : function(target) {
				//console.log(target[0]);
				//deleteFile(target[0].src);
			}
		}*/
	};

	//var text_null = '챌린지 내용을 입력하세요.';
	$('#chall_contents').summernote(setting);
	$('#chall_contents').summernote('fontName', '맑은 고딕');
	$('#chall_contents').summernote('fontSize', '12');
	$('#chall_contents').summernote('code', text_null);

	/*$(document).on("propertychange change keyup paste click", $("textarea[id='chall_contents']") , function() {
		var contents = $('#chall_contents').summernote('code');
		if( contents.indexOf("챌린지 내용을 입력하세요.") > -1) {
			//$('#chall_contents').code('');
			//$('#chall_contents').summernote('editor.insertText', ' ');
			$("#chall_contents").summernote("code", "");
		}else{
		}
	});*/


	/*$('#chall_contents').summernote({
		height: 450,   			//set editable area's height
		lang: "ko-KR", 			// 한글 설정
		codemirror: { 			// codemirror options
			theme: 'monokai'
		}
	});*/
});

</script>
<div id="chall_contents"></div>