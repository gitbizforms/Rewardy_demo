<?
//리워디 사용자 비밀번호를 초기화합니다.

$home_dir = str_replace( basename(__DIR__) , "", __DIR__ );
include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";

include DBCON_MYSQLI;
include FUNC_MYSQLI;

$email_str = $_GET['email_str'];
$mode = $_GET['mode'];

if($mode == "passreset"){

	//비밀번호 초기화 번호
	$mb_pass ='0000';

	//KISA측 모듈사용
	$mem_pass =  kisa_encrypt($mb_pass);
	if (LIP != '59.19.241.15'){
		write_log_dir('허용되지 않는 IP 입니다', "pass");
		echo "<script>alert('허용되지 않는 접근입니다.'); window.close(); window.open('about:blank','_self').self.close();</script>";
		exit;
	}

	//사용자 조회하기
	$sql = "select idx from work_member where state='0' and email='".$email_str."'";
	write_log_dir("비밀번호 초기화 실행", "pass");
	write_log_dir($sql, "pass");
	$mb_use_info = selectQuery($sql);
	if($mb_use_info['idx']){
		$sql = "update work_member set password='".$mem_pass."' where idx='".$mb_use_info['idx']."'";
		$up = updateQuery($sql);
		if($up){
			write_log_dir("비밀번호 초기화 완료", "pass");
			write_log_dir($sql, "pass");

			echo "<script>var p='".$mb_pass."';alert('비밀번호가 '+ p +'으로 초기화되었습니다.'); window.close();</script>";
			echo "<script>history.back();</script>";
		}else{

			write_log_dir("비밀번호 초기화 시도", "pass");
			write_log_dir($sql, "pass");

			echo "<script>alert('비밀번호가 초기화되지 않았습니다.\\r\\n비밀번호가 기존 비밀번호와 동일합니다.'); window.close();</script>";
			echo "<script>history.back();</script>";
		}
	}else{

		write_log_dir("비밀번호 초기화 시도실패", "pass");
		echo "<script>alert('입력하신 사용자가 확인 되지않습니다.\\r\\n사용자 아이디를 확인해주세요.'); window.close();</script>";
		echo "<script>history.back();</script>";
	}
	exit;
}
?>