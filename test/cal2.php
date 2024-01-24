<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
 <head>
  <title> jQuery UI button </title>
  <!-- 
  <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
  -->	

	<link rel="stylesheet" href="http://turfrain.co.kr/TRpackage/asset/js/libs/jquery-ui-1.10.4.custom/themes/base/jquery.ui.all.css">
	<script src="//code.jquery.com/jquery-1.12.1.min.js"></script>	
	<script src="http://turfrain.co.kr/TRpackage/asset/js/libs/jquery-ui-1.10.4.custom/ui/jquery.ui.core.js"></script>
	<script src="http://turfrain.co.kr/TRpackage/asset/js/libs/jquery-ui-1.10.4.custom/ui/jquery.ui.widget.js"></script>
	<script src="http://turfrain.co.kr/TRpackage/asset/js/libs/jquery-ui-1.10.4.custom/ui/jquery.ui.datepicker.js"></script>
	<script src="http://turfrain.co.kr/TRpackage/asset/js/libs/jquery-ui-1.10.4.custom/ui/i18n/jquery.ui.datepicker-ko.js"></script>





	<style type="text/css">
	/* 공통 스타일 */		
	body {	font-size: 62.5%; 	font-family: "Trebuchet MS", "Arial", "Helvetica", "Verdana", "sans-serif"; 	}
	table {	font-size: 1em; }
	.demo-description {	clear: both; padding: 12px; font-size: 1.3em; line-height: 1.4em; }
	.ui-draggable, .ui-droppable {	background-position: top;	}
	
	/* 페이지 스타일*/
	#format { margin-top: 2em; }	
	</style>

	<script type="text/javascript">
	  $(function() {
		
		/* 1. "jquery.ui.datepicker-ko.js" 불러와서 사용하는 방법 */
		//$( "#datepicker1" ).datepicker( $.datepicker.regional[ "ko" ] );

		/* 2. obj 형태로 넣는 방법 */
		var date_language = {
			  closeText: '닫기',
			  prevText: '이전달',
			  nextText: '다음달',
			  currentText: '오늘',
			  monthNames: ['1월','2월','3월','4월','5월','6월',
			  '7월','8월','9월','10월','11월','12월'],
			  monthNamesShort: ['1월','2월','3월','4월','5월','6월',
			  '7월','8월','9월','10월','11월','12월'],
			  dayNames: ['일요일','월요일','화요일','수요일','목요일','금요일','토요일'],
			  dayNamesShort: ['일','월','화','수','목','금','토'],
			  dayNamesMin: ['일','월','화','수','목','금','토'],
			  weekHeader: 'Wk',							//주차
			  dateFormat: 'yy-mm-dd',
			  firstDay: 0,
			  isRTL: false,
			  showMonthAfterYear: true,
			  yearSuffix: '년'};

		$( "#datepicker2" ).datepicker( date_language );

			console.log( $(this).val() );

	  });
	</script>

</head>
	<body>


	<!---<div id="datepicker1">datepicker1</div>-->
	<input type="text" id="datepicker2"></div>

	

	</body>
</html>