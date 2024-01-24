<?php
exit;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>datepicker demo</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.12.4.js"></script>
  <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
 
<div id="datepicker">&nbsp;</div>
 
<script>

	$("#datepicker").click(function() {
		$( "#datepicker" ).datepicker();
	});
</script>
 
</body>
</html>



<?exit;?>
<html>
<body>
<?

/*--------------------
Title : 초간단 달력
Author : Cho Sung O
---------------------*/
?>

<?

$YYYY = $_GET['YYYY'];
$MM = $_GET['MM'];


if ($YYYY =="") {
$YYYY = date("Y"); 
}
IF ($MM =="") {

$MM = date("m"); 

}

if($MM == 13) {

$MM = 1;
$YYYY++;

}

if($MM == 0) {

$MM = 12;
$YYYY--;

}

$before = $MM - 1;
$after = $MM + 1;

$firstday_weeknum = date("w", mktime(0, 0, 0, $MM, 1, $YYYY)); 
$lastday = date("t", mktime(0, 0, 0, $MM, 1, $YYYY)); 

if($MM == 2) { 
    if(($YYYY % 4) == 0 && ($YYYY % 100) != 0 || ($YYYY % 400) == 0) { $lastday = 29; }
}

$td1 = "<TD width='80' align='center'><font size='2' align='center'><b>";
$td2 = "</b></font></TD><TD width='80' height='80' align='center'><font size='2' align='center'><b>";
$td3 = "</b></font></TD>\n";
?>
<link rel="stylesheet" href="inet_style.css">

<body>
<table width=600><tr>
<td align=center><a href="<?=$PHP_SELF?>?MM=<?=$before?>&YYYY=<?=$YYYY?>">이전달<<</a> <font color="deepink"><?echo "$YYYY";?>년<?echo "$MM";?>월 </font> <a href="<?=$PHP_SELF?>?MM=<?=$after?>&YYYY=<?=$YYYY?>">>>다음달</a>
</td></tr>
</table>
<?
echo("<table border=1 cellspacing=0 cellpadding=2 bordercolorlight=#CCCCCC bordercolordark=#FFFFFF bgcolor=#FFFFFF><TR>\n");
echo($td1."<font color='red'>日</font>".$td2."月".$td2."火".$td2."水".$td2."木".$td2."金".$td2."<font color='green'>土</font>".$td3);
echo("</TR><TR>");

$week = 0;
for ($i=0; $i < $firstday_weeknum; $i++) { echo("<TD>&nbsp;</TD>"); $week++; }
	for($d=1; $d <= $lastday; $d++)
	{



		if ($week == 7) { echo("</TR></TR>"); $week = 0; }
		$day = (date("j") == $d)? "<font color='deepink'><b>".$d."</b></font>":$d;


		echo("<TD wdith='80' height='80' align='center'><font size=2>".$day."</font><br>$son<br>$row[0]</TD>\n");

		$week++;
	}

for ($i=$week; $i < 7; $i++) { echo("<TD>&nbsp;</TD>\n"); }
echo("</TR>\n");
echo("</TABLE><br>\n"); 


?>

</body>
</html>

