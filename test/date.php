<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";

	

	$wdate = $_GET['wdate'];

	
	//echo "\t\t";
	//echo $wdate;

	//$sql = "select idx, email from work_member where state=0 and idx not in (46,47,50) order by idx asc";
	$sql = "select idx, email from work_member where state=0 and idx in (48) order by idx asc";
	$mem_info = selectAllQuery($sql);

	exit;
	for($i=0; $i<count($mem_info['idx']); $i++){
		$email = $mem_info['email'][$i];
		work_com_reward_wday($email, '2022-11-01');
		work_com_reward_wday($email, '2022-11-02');
		work_com_reward_wday($email, '2022-11-03');
		work_like_reward_wday($email, '2022-11-01');
		work_like_reward_wday($email, '2022-11-02');
		work_like_reward_wday($email, '2022-11-03');
	}

	//work_like_reward('qohse@nate.com', $wdate);


?>