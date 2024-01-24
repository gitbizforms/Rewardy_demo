<!-- include libraries(jQuery, bootstrap) -->

<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- 로컬 파일 summernote css/js -->
<!-- <script src="../4.surmmernote/lib/summernote-bs4.js"></script> -->
<!-- <link rel="stylesheet" href="../4.surmmernote/lib/summernote-bs4.css"> -->

<!-- CDN 파일 summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<!-- CDN 한글화 -->
<script src=" https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/lang/summernote-ko-KR.min.js"></script>

<!-- 로컬 파일 한글화 -->
<!-- <script src="../4.surmmernote/lib/lang/summernote-ko-KR.min.js"></script> -->
<!-- <script src="../4.surmmernote/lib/lang/summernote-ko-KR.js"></script> -->


<div class="jumbotron p-1">
	<h1 class="display-4">SummerNote 사용해보기</h1>
	
	<!-- <p class="lead">Subtitle</p> -->

	<hr class="my-4">
		<div class="container-fluid">

			<!-- summernote을 직접적으로 사용할 요소 -->

		<div id="summernote">
			<p>Hello Summernote</p>
		</div>
	</div>
	
	<!-- 수정 시작 버튼 -->

	<button id="edit" class="btn btn-primary" onclick="edit()" type="button">수정</button>

	<!-- 수정 완료 버튼 -->

	<button id="save" class="btn btn-primary" onclick="save()" type="button">수정 종료</button>
</div>


<script>
	$(document).ready(function () {
		console.log($.summernote.options); // 실행시 언어 설정을 한글로 설정
		$.summernote.options.lang = 'ko-KR';
		$.summernote.options.airMode = false;
	});

	var a = $('#summernote'); // 수정버튼
	
	var edit = function () {
		a.summernote({ focus: true });
	}; // 수정 종료
	
	var save = function () {
		var markup = a.summernote('code');
		a.summernote('destroy');
	};

</script>
