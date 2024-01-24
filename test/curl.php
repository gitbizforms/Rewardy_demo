<?
//phpinfo();

	//테스트용 API키
	//$API_KEY = "694c261e13b8672ecd64ce824262c469809f0d1175";
	//$SECRET = "a4e20c864a2f3f1c48663a";
	
	//베먼클럽 API키
	$API_KEY = "724beda158460f062e0388b1597a1728f531b55be4";
	$SECRET = "82147d3be238f4563475db";

	//회원조회
	$url ='https://api.imweb.me/v2/member/members';

	//인증
	//$url = 'https://api.imweb.me/v2/auth';
	
	//$headers = array();
	//curl_setopt($ch, CURLOPT_HEADER, true);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
		'key'=> "'.$API_KEY.'",
		'secret'=> "'.$SECRET.'",
	]));
	$res = curl_exec($ch);
	curl_close($ch);


	print "<pre>";
	print_r($res);
	print "</pre>";
?>
