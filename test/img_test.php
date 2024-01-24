<?
exit;
//http://jcgift.co.kr/upload/image/%EB%A0%88%EB%85%B8%EB%A7%88%EB%82%A8%EC%84%B13%EC%A1%B115020500%20%EB%B3%B5%EC%82%AC(1).jpg

//"http://jcgift.co.kr/upload/image/%EB%A0%88%EB%85%B8%EB%A7%88%EB%82%A8%EC%84%B13%EC%A1%B115020500%20%EB%B3%B5%EC%82%AC(1).jpg"

$res = file_get_contents('http://jcgift.co.kr/upload/image/레노마남성3족15020500 복사(1).jpg');

print "<pre>";
print_r($res);
print "</pre>";
?>