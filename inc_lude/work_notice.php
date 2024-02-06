<?
	//크롬브라우저에서 알림관련 Notification API 
	//사용안함
	exit;
	$work_report_info = false;
	$work_req_info = false;
	$work_share_info = false;

	//보고받은 업무
	$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and work_flag='1' and email='".$user_id."' and workdate='".TODATE."'";
	$work_report_info = selectQuery($sql);

	//요청받은 업무
	$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and work_flag='3' and email='".$user_id."' and workdate='".TODATE."'";
	$work_req_info = selectQuery($sql);

	//공유받은 업무
	$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and share_flag='2' and email='".$user_id."' and workdate='".TODATE."'";
	$work_share_info = selectQuery($sql);
	
?>

<script>

	var icon = "http://demo.rewardy.co.kr/favicon.ico";
	var link = "http://demo.rewardy.co.kr/todaywork/index.php";
	var title = '리워디';
	var time = new Date().getTime();

	window.onload = function () {
		if (window.Notification) {
			Notification.requestPermission();
		}
	}

	/*function calculate() {
		//console.log(1);
		//setTimeout(function () {
		//	notify();
		//}, 5000);

		setInterval(function() {
			//console.log("start");

			var htime = new Date().getTime();
			htime = parseInt(htime)
			time = parseInt(time);

			//console.log( htime - time);
			if( htime - time >= 60000 ){
			//	console.log("GGGGG");
			}else{
			//	console.log(htime - time);
			}


			if (new Date().getTime() - time >= 60000 ) {
			//if ( htime - time >= 60000 ) {
			//	console.log("on");
				//window.location.reload(true);
				notify();
			}
		}, 5000);
	}*/

	function notify() {

		if (Notification.permission !== 'granted') {
			alert('브라우저 알림 허용을 설정해 주세요.');
		}else{

			<?if($work_report_info == true || $work_req_info == true || $work_share_info == true){?>

				var body = "";
				<?if($work_report_info == true){?>
					var body = '보고업무가 작성되었습니다.';
				<?}else if($work_req_info == true){?>
					var body = '요청업무가 작성되었습니다.';
				<?}else if($work_share_info == true){?>
					var body = '공유업무가 작성되었습니다.';
				<?}?>

				var notification = new Notification(title , {
					icon: icon,
					body: body,
				});

				notification.onclick = function () {
					window.open(link);
				};
			<?}?>
		}
	}
</script>