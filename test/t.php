<a href="#" id="requestPermission">권한요청</a>
<a href="#" id="showNotification">알림표시</a>

<script>
	var requestPermission = document.querySelector('#requestPermission')
	var showNotification = document.querySelector('#showNotification')

	// 알림 권한 요청
	requestPermission.addEventListener('click', () => {
	if (!window.Notification) {
		alert('지원하지 않음')
	} else {
		Notification.requestPermission().then(p => {
		if (p == 'denied') {
			alert('알림을 허용하지 않았습니다.')
		} else if (p == 'granted') {
			alert('알림을 허용했습니다.')
		}
		})
	}
	})

	// 알림 표시
	showNotification.addEventListener('click', () => {
	if (Notification.permission!=='default') {
		let notify = new Notification('알림이 왔습니다.', {
		'body': '안녕하세요. \n알림을 성공적으로 수신했습니다.',
		'icon': 'https://tistory3.daumcdn.net/tistory/2979840/attach/6e5d2d16ab6a49628dfe1f4c164e38a0',
		'tag': '메시지'
		})
		notify.onclick = function(){
		alert(this.tag)
		}
	} else {
		alert('알림을 허용해 주세요.')
		}
	})
</script>