<?
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "inc_lude/conf.php";
include DBCON;
include FUNC;

$sql = "select * from work_challenges order by idx desc";
$rs = selectAllquery($sql);
if($rs){
	for($i=0; $i<count($rs['idx']); $i++){
		$idx = $rs['idx'][$i];
		$comment = urldecode($rs['comment'][$i]);
	
		echo "idx:".$idx;
		echo "<br>";
		echo "comment".$comment;
		echo "<br>";
	}
}	

?>
