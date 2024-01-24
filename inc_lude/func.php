<?
	/*쿼리문, 쿼리문 출력 : 1 실행*/
	function Query($query="", $debug="0"){
		global $conn;

		//$query = "select * from product where idx='31s118' and id='ssssiseehss' and view_flag='0' ";
		//전체URL확인
		if($_SERVER['SERVER_PROTOCOL']){
			$arr_protocol =  explode("/", $_SERVER['SERVER_PROTOCOL']);
			$protocol = strtolower($arr_protocol[0]);
			$protocol_url = ConvertStr($protocol."://".$_SERVER['HTTP_HOST']);
		}else{
			$protocol_url = "";
		}

		$query_sub = "/*".$_SERVER['REMOTE_ADDR']."|".$protocol_url . $_SERVER['PHP_SELF']."*/ ";

		//전체 쿼리 출력
		if($debug == 1){
			echo $query_sub . $query;
		}


		//쿼리 조건절 체크
		$condition = @end(explode('where',strtolower($query)));

		//문자열을 배열로 정의
		parse_str( str_replace(' and ','&',$condition) , $arr_field);

		/* 숫자형 때문에 작성했던 내용
		foreach ($arr_field as $key => $val){
			//number, idx, view_flag : 숫자만 사용
			$nstr = array(">","<","/","=","\\","+");
			$key = str_replace( $nstr , "" , $key );


			if ( in_array($key, array('number','idx','view_flag') )){

				//order by절 제거
				$val2 = current(explode(' ',strtolower($val)));

				//따옴표('' , "")를 제거
				$replace_val = trim(str_replace( array("'" , "\"") , "", $val2));

				//숫자가 아닌경우
				//	echo "replace_val " .$replace_val;
				if( is_numeric($replace_val) == false){
					//echo "999";
					Queryfail($query ,$debug);
				}
			}
			//id : 한글이 포함인 경우
			elseif( in_array( $key , array('id') )){
				if(preg_match("/[\xE0-\xFF][\x80-\xFF][\x80-\xFF]/", $val)){
					Queryfail($query, $debug);
				}
			}
		}
		*/

		if( strtolower(substr($query, 0, 6))=='insert'){
			/*
			$condition_first = current(explode('values', strtolower($query)));
			$condition_end = end(explode('values', strtolower($query)));

			$condition_first = str_replace(')', '', end(explode('(', strtolower($condition_first))));
			$condition_first = explode(",", $condition_first);

			$condition_end = str_replace( array("', '") ,"','",$condition_end);
			$condition_end = substr(ltrim($condition_end) ,1);
			$condition_end = substr($condition_end,0, -1);
			$condition_end = explode(",", $condition_end);
			*/
			//echo count($condition_first) . " = " . count($condition_end);


			//parse_str( str_replace(' and ','&',$condition) , $arr___con);


			/*
			print "<pre>";
			print_r($condition_end);
			print "</pre>";
			*/
			//exit;

		}

		$result = sqlsrv_query($conn, $query);
		if(!$result){
			Queryfail($query, $debug);
		}
		return $result;
	}

	//전체 셀렉트 쿼리
	function selectAllQuery($query="", $debug="0"){
		$List = array();
		$result = Query($query, $debug);
		while( $row = @sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))
		{
			foreach ($row as $key => $value) {
				$List[$key][] = $value;
			}
		}
		return $List;
	}

	//단일 셀렉트 쿼리
	function selectQuery($query="", $debug="0"){
		$result = Query($query, $debug);
		$result = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);		//row번호 -> SQLSRV_FETCH_NUMERIC 사용
		return $result;
	}

	//카운터 구하는 쿼리
	function countQuery($query="", $debug="0"){
		$data = sqlsrv_fetch_array(Query($query, $debug));
		$return = $data[0];
		return $return;
	}

	//인서트 쿼리
	function insertQuery($query="", $debug="0"){
		global $conn;

		$result = Query($query, $debug);

		//쿼리실행된 row수 리턴, 없을경우 0
		$row = sqlsrv_rows_affected($result);
		if($row){
			//커밋처리
			sqlsrv_commit($conn);
			return $row;
		}else{
			//롤백처리
			sqlsrv_rollback($conn);
			print "<pre> rollback ";
			print_r( sqlsrv_errors() );
			print "</pre>";
			return 0;

		}
	}

	//인서트 쿼리 : 쿼리실행된 ID값 반환
	function insertIdxQuery($query="", $debug="0"){
		global $conn;
		$result = Query($query, $debug);

		//쿼리실행된 row수 리턴, 없을경우 0
		$rows_affected = sqlsrv_rows_affected($result);
		if($rows_affected == 1){
			sqlsrv_commit($conn);
			$sql = "select @@IDENTITY as insert_id";
			$data = sqlsrv_fetch_array(Query($sql));
			return $data['insert_id'];
		}else{

			//오류났을때
			if( $rows_affected === false){
				sqlsrv_rollback($conn);
				die( print_r( sqlsrv_errors(), true));
			//쿼리 영향받은 행수가 확인 할수 없을경우 : -1
			}elseif( $rows_affected == -1) {
				//echo "No information available.<br />";
				return $rows_affected;
			}else{
				//정상적으로 성공 : 1 반환
				return $rows_affected;
			}
		}
	}

	//업데이트 쿼리
	function updateQuery($query="", $debug="0"){
		global $conn;

		$result = Query($query, $debug);
		//쿼리실행된 row수 리턴, 없을경우 0
		$rows_affected = sqlsrv_rows_affected($result);

		//오류났을때
		if( $rows_affected === false){
			sqlsrv_rollback($conn);
			die( print_r( sqlsrv_errors(), true));
		//쿼리 영향받은 행수가 확인 할수 없을경우 : -1
		}elseif( $rows_affected == -1) {
			sqlsrv_rollback($conn);
			//echo "No information available.<br />";
			return $rows_affected;
		}else{
			sqlsrv_commit($conn);
			//정상적으로 성공 : 1 반환
			return $rows_affected;
		}
	}



	//문자열 자르기
	function cuttingStr($str, $divpnt) {
		$retArray = array();
		if ( strlen($str) <= $divpnt ) {
		return $str;
		}
		for ( $i=0, $substring="", $hanStart=false; $i < $divpnt; $i++ ) {
		$char=substr($str,$i,1);

		if ( ord($char) > 127 ) { // toggle
		if ( $hanStart ) $hanStart = false;
		else $hanStart = true;
		}

		if ( $i >= ($divpnt -1) ) {
			if ( ord($char) <= 127 || !$hanStart) $substring .= $char;
			else $substring = substr($substring,0,$i--);
			break;
			}
			$substring .= $char;
		}


		return $substring . "...";
	}

	function curString122($str, $len, $addStr="...")
	{
		if(strlen($str)>$len)
		{
			for($i=0; $i<$len; $i++) if(ord($str[$i])>127) $i++;
			$str=substr($str,0,$i);
			$str = $str.$addStr;
		}
		return $str;
	}



function CutString($str, $len, $checkmb=false, $tail='...') {
	preg_match_all('/[\xEA-\xED][\x80-\xFF]{2}|./', $str, $match);
	$m = $match[0];
	$slen = strlen($str);
	$tlen = strlen($tail);
	$mlen = count($m);

	if ($slen <= $len) return $str;
	if (!$checkmb && $mlen <= $len) return $str;

	$ret = array();
	$count = 0;

	for ($i=0; $i < $len; $i++) {
		$count += ($checkmb && strlen($m[$i]) > 1)?2:1;
		if ($count + $tlen > $len) break;
			$ret[] = $m[$i];
		}
		return join('', $ret).$tail;
	}

	//문자열 자르기
	function cutString11($str,$maxlen, $suffix = "..")
	{
		if($maxlen<=0) return $str;
		if(eregi("\[re\]",$str)) $len=$len+4;
		if($maxlen >= strlen($str)) return $str;

		$klen = $maxlen - 1;
		while(ord($str[$klen]) & 0x100) $klen--;

		return substr($str, 0, $maxlen - (($maxlen + $klen + 1) % 2)).$suffix;
	}


	//메세지
	function alert($msg){
		echo "<script>alert('$msg      \\n');</script>";
	}

	function alertMove($msg , $url)
	{
		print "<script>alert('$msg       \\n');</script>";
		if(!$url) {
			print "<script>history.go(-1);</script>";
		}
		else {
			print "<script>location.href='$url';</script>";
		}
	}



	/*접속 기기체크*/
	function device_chk() {
		$mobileArray = array("iphone" , "lgtelecom" , "skt" , "mobile" , "samsung" , "nokia" , "blackberry" , "android" , "sony" , "phone");
		$checkCount = 0;
		for($num = 0; $num < sizeof($mobileArray); $num++) {
			if(preg_match("/$mobileArray[$num]/", strtolower($_SERVER['HTTP_USER_AGENT']))) {
				$checkCount++;
				break;
			}
		}
		return ($checkCount >= 1) ? "m" : "p";
	}

	/*로그인 여부 체크*/
	function login_chk() {
		if (!empty($_COOKIE['sub_id']) or !empty($_COOKIE['sub_sns'])) {
			$login_flag = true;
		} else {
			$login_flag = false;
		}

		return $login_flag;
	}

	/*현재 페이지*/
	function now_url($page=""){
		if($_SERVER['SERVER_PROTOCOL']){
			$arr_protocol =  explode("/", $_SERVER['SERVER_PROTOCOL']);
			$protocol = strtolower($arr_protocol[0]);
			$protocol_url = ConvertStr($protocol."://".$_SERVER['HTTP_HOST']);
		}else{
			$protocol_url = "";
		}

		if($page == "page"){
			return $_SERVER['REQUEST_URI'];
		}else{
			return $protocol_url.$_SERVER['REQUEST_URI'];
		}
	}

	//현재 파일명
	function get_filename($name) {
		$file_name = basename($_SERVER['PHP_SELF']);

		$tmp = explode(".",$file_name);
		if(is_array($tmp)){
			$file_name = $tmp[0];
		}else{
			$file_name = $file_name;
		}

		return $file_name;
	}

	Function ConvertStr($str) {
		$str = trim($str);
		$str = str_replace("'","&#39;",$str);
		$str = str_replace("`","&#96;",$str);
		$str = str_replace("<","&lt;",$str);
		$str = str_replace(">","&gt;",$str);
		$str = str_replace("%","&#37",$str);
		$str = str_replace('&quot;',"&#39;",$str);
		$str = str_replace('"',"&quot;",$str);
		$str = str_replace("--","­­",$str);
		$str = str_replace("exec ", "ｅxec ",$str);
		$str = str_replace("select ", "ｓelect ",$str);
		$str = str_replace("insert ", "ｉnsert ",$str);
		$str = str_replace("update ", "ｕpdate ",$str);
		$str = str_replace("delete ", "ｄelete ",$str);
		$str = str_replace("union ", "ｕnion ",$str);
		$str = str_replace("<script", "<ｓcript",$str);
		$str = str_replace("&","&amp;",$str);
		return $str;
	}

	Function ConvertStrView($str){
		$str = trim($str);
		$str = str_replace(chr(13),"<br/>",$str);
		$str = str_replace("''","'",$str);
		$str = str_replace("&#39;","\'",$str);
		$str = str_replace("&#96;","`",$str);
		$str = str_replace("&amp;","&",$str);
		$str = str_replace("&lt;","<",$str);
		$str = str_replace("&gt;",">",$str);
		$str = str_replace("&#37","%",$str);
		$str = str_replace("&quot;",'"',$str);
		$str = str_replace("­­","--",$str);
		$str = str_replace("ｅxec ","exec ",$str);
		$str = str_replace("ｓelect ","select ",$str);
		$str = str_replace("ｉnsert ","insert ",$str);
		$str = str_replace("ｕpdate ","update ",$str);
		$str = str_replace("ｄelete ","delete ",$str);
		$str = str_replace("ｕnion ","union ",$str);
		$str = str_replace("<ｓcript","<script",$str);
		return $str;
	}


	//글내용(textarea) 엔터값 그대로 출력
	function textarea_replace($str){
		$result = nl2br(str_replace(" "," ", $str));
		return $result;
	}


	function Query_Insert($table="", $value, $debug="0"){
		global $conn;

		//필드
		$field = "";

		//필드=데이터
		$fields = "";

		//데이터
		$values = "";

			//데이터값이 여러개경우
			if($value > 1){

				//마지막원소 확인하기
				end($value); $last_key = key($value);
				foreach($value as $key => $val){
					$field .= $key;
					$fields .= $key ."=".  "'$val'";
					$values .= "'$val'";

					if( $key != $last_key ){
						$fields .= ', ';
						$field .= ', ';
						$values .= ', ';
					}
				}

			}else{

			}

		/*IF EXISTS(
			SELECT $str FROM $table
			WHERE 1=1
		)
		BEGIN
			SELECT 99 as cnt
		END

		ELSE
		BEGIN";
		*/

		$sql ="insert into $table ($field) values ($values)";
		if($debug == 1){
			print "<pre>";
			echo "query : ". $sql;
			print "</pre>";
		}else{
			//print "<pre>";
			//echo "query : ". $sql;
			//print "</pre>";
			//exit;
		}

		$result = sqlsrv_query($conn, $sql);
		if($result){
			sqlsrv_commit($conn);
		}else{
			sqlsrv_rollback($conn);
		}
		return $result;
	}

	//랜덤난수발생
	function nano_random($cnt='5'){
		$length = $cnt;
		$token = "";
		//$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		//$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet = "0123456789";
		$max = strlen($codeAlphabet);

		for ($i=0; $i < $length; $i++){
			$token .= $codeAlphabet[random_int(0, $max-1)];
		}
		return $token;
	}

	//랜덤난수발생2
	function name_random($cnt='10'){
		$length = $cnt;
		$token = "";
		$codeAlphabet  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet .= "0123456789";
		$max = strlen($codeAlphabet);

		for ($i=0; $i < $length; $i++){
			$token .= $codeAlphabet[random_int(0, $max-1)];
		}
		return $token;
	}

	//랜덤난수발생2 + 현재시간
	function name_random_time($cnt='10'){
		$length = $cnt;
		$token = "";
		$codeAlphabet  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet .= "0123456789";
		$max = strlen($codeAlphabet);

		for ($i=0; $i < $length; $i++){
			$token .= $codeAlphabet[random_int(0, $max-1)];
		}

		$token = $token.time();
		return $token;
	}

	//숫자를 한글로 표기
	function number_hangul($number){
		$num = array('', '일', '이', '삼', '사', '오', '육', '칠', '팔', '구');
		$unit4 = array('', '만', '억', '조', '경');
		$unit1 = array('', '십', '백', '천');
		$res = array();

		$number = str_replace(',','',$number);
		$split4 = str_split(strrev((string)$number),4);

		for($i=0; $i<count($split4); $i++){
			$temp = array();
			$split1 = str_split((string)$split4[$i], 1);
			for($j=0;$j<count($split1);$j++){
				$u = (int)$split1[$j];
				if($u > 0) {
					$temp[] = $num[$u].$unit1[$j];
				}
			}
			if(count($temp) > 0){
				$res[] = implode('', array_reverse($temp)).$unit4[$i];
			}
		}
		return implode('', array_reverse($res));
	}

	//배열안에 배열이 있는지 체크
	function array_to_in_array($arr1, $arr2){
		if(is_array($arr2)){
			if(is_array($arr1)){
				foreach($arr1 as $k=>$v){
					if(in_array($v, $arr2)){
						return true;
					}else{
						continue;
					}
				}
				return false;
			}else{
				return in_array($arr1,$arr2);
			}
		}else{
			return false;
		}
	}

	function Queryfail($query , $debug=""){

		global $chkMobile;

		$arr_val = array();
		//페이지에러코드
		$arr_val['state'] = '0';
		$arr_val['code'] = 'query_fail';

		$qry = @end(explode("*/ " , $query));

		//$arr_val['query'] = ConvertStr($qry);
		$arr_val['query'] = replace_text($qry);

		//select, insert, update
		$arr_val['kind'] = strtolower(substr($qry, 0, 6));

		//프로토콜
		if($_SERVER['SERVER_PROTOCOL']){
			$arr_protocol = explode("/", $_SERVER['SERVER_PROTOCOL']);
			$protocol = strtolower($arr_protocol[0]);
			$arr_val['host'] = ConvertStr($protocol."://".$_SERVER['HTTP_HOST']);
		}else{
			$arr_val['host'] = "";
		}

		if($_SERVER['QUERY_STRING']){

			if(strstr($_SERVER['QUERY_STRING'], "404;")){
				$qstring = explode("404;", ConvertStr($_SERVER['QUERY_STRING']));
				$arr_val['refer'] = $qstring[1];
				$arr_val['page'] = substr($qstring[1], 0, 254);
			}else{

				//$qstring = current(explode("?", ConvertStr($_SERVER['QUERY_STRING'])));
				$arr_val['refer'] = "-";
				$arr_val['page'] = $_SERVER['PHP_SELF'];
			}
		}else{
			$arr_val['refer'] = $_SERVER['QUERY_STRING'];
			$arr_val['page'] = now_url('page');
		}

		//에이전트
		if($_SERVER['HTTP_USER_AGENT']){
			$arr_val['ua'] = ConvertStr($_SERVER['HTTP_USER_AGENT']);
		}else{
			$arr_val['ua'] = "";
		}

		//현재아이피
		$arr_val['ip'] = LIP;

		//페이지 레퍼러
		if(isset($_SERVER['HTTP_REFERER'])){
		//	$arr_val['err_refer_page'] = $_SERVER['HTTP_REFERER'];
		}else{
		//	$arr_val['err_refer_page'] = "";
		}

		//PC:0 ,모바일:1
		$arr_val['site_flag'] = ($chkMobile == 0) ? $chkMobile : $chkMobile;

		//서버아이피
		$arr_val['host_ip'] = $_SERVER['LOCAL_ADDR'];
		$arr_val['message'] ='-';

		/*
		print "<pre>";
		print_r($arr_val);
		print "</pre>";
		*/

		//쿼리 인서트시킴
		write_log($arr_val['page']);
		write_log($arr_val['query']);

		//$res = Query_Insert("biz_page_error_dbconnect", $arr_val , $debug);
		$res = Query_Insert("work_page_error_list", $arr_val , $debug);
		exit;
	}


	/*
	<div class="sw_paging">
		<div class="sw_paging_in">
			<button class="paging_prev"><span>앞으로 10페이지 이동</span></button>
			<button class="paging_num on"><span>1</span></button>
			<button class="paging_num"><span>2</span></button>
			<button class="paging_num"><span>3</span></button>
			<button class="paging_next"><span>뒤로 10페이지 이동</span></button>
		</div>
	</div>
	*/

	//'pagingSize : 페이징 사이즈 ex)pagingSize=4면 1 2 3 4 까지 보여줌
	//	'total_cnt	: 전체 게시물 수
	//	'pageSize		: 페이지 당 보여질 게시물 수

	function pageing($pagingSize, $total_cnt, $pageSize, $string=""){
		global $user_id;
		//	$pagingSize = 5;
		//	$pageSize = "5";

		/*
		print "<pre>";
		print_r($_SERVER);
		print "</pre>";
		*/

		$end_number = "";
		$page_count = ""; //만들어질 페이지 갯수

		if ( ($total_cnt % $pageSize) > 0 ){
			$page_count = floor($total_cnt/$pageSize)+1;
		}else{
			$page_count = floor($total_cnt/$pageSize);
		}

		$p = $_POST['p']?$_POST['p']:$_GET['p'];
		if (!$p){
			$p = 1;
		}

		if($string){
			parse_str($string, $output);
		}

		//페이징 결정
		$url = $output['page'];


		///5,81, 10
		if($page_url){
			$target_page = $page_url;
		}else{
			$target_page = $_SERVER['PHP_SELF'];
		}

		$querystring = "&";
		/*
		print "<pre>";
		print_r($_SERVER['QUERY_STRING']);
		print "</pre>";
		*/
		if($p){
			$querystring = $_SERVER['QUERY_STRING'];
		}else{
			$querystring = $querystring . $_SERVER['QUERY_STRING'];
		}

//		// 페이징 이동 시 (넘버값, gp) 있을시 제거


		if ( strpos($querystring, "number=") > 0 || strpos($querystring, "p=") > 0 ){

			if (strpos($querystring, "&") > 0 ){

				$chk_number_s	= explode("&", $querystring);
				$chk_number_c	= count($chk_number_s);
				$chk_number_no	= "";

				for($i = 0; $i<$chk_number_c; $i++){
					if (strpos($chk_number_s[$i], "number") > 0 || strpos($chk_number_s[$i], "gp") > 0 ){
					}else{
						if($chk_number_s[$i]){
							$chk_number_no	= $chk_number_no . "&" . $chk_number_s[$i];
						}
					}
				}

				$querystring		= "&" .$chk_number_no;
			}else{
				$querystring		= "";
			}
		}else{
			$querystring		= "";
		}

		if($p < $pagingSize){
			$start_number="1";
			if($page_count < $pagingSize){
				$end_number = $page_count;
			}else{
				$end_number = $pagingSize;
			}
		}else if( ($p % $pagingSize) > 0){
			$end_number = floor(1+$p/$pagingSize) * $pagingSize;
			$start_number = $end_number - $pagingSize+1;
		}else{
			$end_number = floor($p/$pagingSize) * $pagingSize;
			$start_number = $end_number - $pagingSize+1;
		}

		if( $end_number > $page_count){
			$end_number = $page_count;

		}

		$search = $_POST['search'];
		$str = "&page=".$output['page']."&sdate=".$output['sdate']."&edate=".$output['edate']."&nday=".$output['nday']."&type=".$output['type']."&kind=".$output['kind']."&this_class=".$output['this_class']."&sort_kind=".$output['sort_kind'];

		$pageing = "";
		$page_url_a = "javascript:list_pageing('".$url."','".$str."','1')";
		$pageing .= "<button class=\"paging_first\" onclick=\"".$page_url_a."\" title=\"첫페이지로 이동\">첫페이지로 이동</button>";

		$page_url_a = "javascript:list_pageing('".$url."','".$str."','".($start_number-1)."')";
		//이전페이지
		if($start_number > $pagingSize){
			$pageing .= "<button class=\"paging_prev\" onclick=\"".$page_url_a."\" title=\"이전 10페이지 이동\"><span>이전 10페이지 이동</span></button>";
		}else{
			$pageing .= "<button class=\"paging_prev\" title=\"이전 10페이지 이동\"><span>이전 10페이지 이동</span></button>";
		}

		for($i = $start_number; $i <= $end_number; $i++){

			$page_url_a = "javascript:list_pageing('".$url."','".$str."', '".$i."')";
			if($i == $p){
				$pageing .= "<button class=\"paging_num on\"><span>". $i ."</span></button>";
			}else{
				$pageing .= "<button class=\"paging_num\" onclick=\"".$page_url_a."\"><span>". $i ."</span></button>";
			}
		}


		$page_url_a = "javascript:list_pageing('".$url."','".$str."', '".($end_number+1)."')";

		if($end_number < $page_count){
			$pageing .= "<button class=\"paging_next\" onclick=\"".$page_url_a."\" title=\"다음 10페이지 이동\"><span>다음 10페이지 이동</span></button>";
		}else{
			$pageing .= "<button class=\"paging_next\" title=\"다음 10페이지 이동\"><span>다음 10페이지 이동</span></button>";
		}

		$page_url_a = "javascript:list_pageing('".$url."','".$str."', '".$page_count."')";
		$pageing .= "<button class=\"paging_last\" onclick=\"".$page_url_a."\" title=\"마지막 페이지로 이동\">마지막 페이지로 이동</button>";

		return $pageing;
	}


	//휴대폰번호 자동하이픈
	function add_hyphen($tel)
	{
		$tel = preg_replace("/[^0-9]/", "", $tel);    // 숫자 이외 제거
		if (substr($tel,0,2)=='02'){
			return preg_replace("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
		}else if (strlen($tel)=='8' && (substr($tel,0,2)=='15' || substr($tel,0,2)=='16' || substr($tel,0,2)=='18')){
			// 지능망 번호이면
			return preg_replace("/([0-9]{4})([0-9]{4})$/", "\\1-\\2", $tel);
		}else{
			return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
		}
	}

	//아이피
	function change_ip(){
		if($_SERVER['HTTP_X_FORWARDED_FOR'] <> ""){
			$common_server_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$common_server_ip = $_SERVER['REMOTE_ADDR'];
		}

		if(strpos($common_server_ip,',') > 0){
			$common_server_ip = trim(common_server_ip);
			$split_server_ip = explode(',',$common_server_ip);

			if(is_array($split_server_ip)){
				if(!empty($split_server_ip[0])){
					$common_server_ip = $split_server_ip[0];
				}
			}
			$change_ip = trim($common_server_ip);
			return $change_ip;
		}
	}


	//현재디렉토리명
	function get_dirname(){
		$dir = getcwd();			// 현재 디렉토리명을 반환하는 PHP 함수이다.
		$temp = explode("\\", $dir);
		$dirname = $temp[sizeof($temp)-1];

		return $dirname;
	}



	//암호화
	function Encrypt($str)
	{
//		$secret_key = "123456789";
//		$secret_iv = "#@$%^&*()_+=-";
		$secret_key = "492136857";
		$secret_iv = "^#+=*$-%)&@(_";

		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $secret_iv), 0, 32);

		return str_replace("=", "", base64_encode(
			openssl_encrypt($str, "AES-256-CBC", $key, 0, $iv))
		);
	}

	//복호화
	function Decrypt($str)
	{
		$secret_key = "492136857";
		$secret_iv = "^#+=*$-%)&@(_";

		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $secret_iv), 0, 32);

		return openssl_decrypt(
				base64_decode($str), "AES-256-CBC", $key, 0, $iv
		);
	}


	//보상입력처리 -- 사용안함
	function work_coininfo_add($info){
		global $conn , $user_id;

		//등록된 업무글 번호
		if($info['work_idx']){
			$work_idx = preg_replace("/[^0-9]/", "", $info['work_idx']);
		}

		if($info['code'] && $info['user'] && $info['name'] && $info['coin'] && $info['memo']){

			$sql = "insert into work_coininfo(state, code, work_idx, email, name, coin, memo, workdate, ip) values(";
			$sql = $sql .= "'0', '".$info['code']."', '".$work_idx."', '".$info['user']."', '".$info['name']."', '".$info['coin']."', '".$info['memo']."','".TODATE."','".LIP."')";
			$res_info_idx = insertQuery($sql);
			if($res_info_idx){
				$sql = "update work_member set coin = coin + '".$info['coin']."' where state='0' and email='".$info['user']."'";
				$res = updateQuery($sql);
				if($res){
					echo "complete";
					exit;
				}
			}
		}
	}




	//역량지표 가산점
	//역량 평가 지표(work, 0001, 아이디, 게시물idx), 역량지표 등록 처리
	//work_cp_reward_add("cp", $act, $user_id, $idx, $reward_cp_info['idx']);
	function work_cp_reward_add($work, $mode, $uid="", $idx="", $cp_idx=""){
		global $conn,$companyno, $user_id, $user_name;

		//회원정보 체크
		$mem_user_info = member_row_info($uid);
		if($mem_user_info){

			$mem_user_id = $mem_user_info['email'];
			$mem_user_name = $mem_user_info['name'];

			//역량평가 지표
			$sql = "select idx, limit, type1, type2, type3, type4, type5, type6, score1, score2, score3, score4, score5, score6 from work_cp_reward where state='0' and service='".$work."' and act='".$mode."'";
			$cp_info = selectQuery($sql);
			if($cp_info['idx']){

				//오늘업무
				$sql = "select idx, state from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$idx."' and email='".$mem_user_id."' and workdate='".TODATE."'";
				$work_info = selectQuery($sql);


				//아이디별 역량평가 내역
				$sql = "select count(1) as cnt from work_cp_reward_list where state='0' and service='".$work."' and act='".$mode."' and companyno='".$companyno."' and email='".$mem_user_id."' and workdate='".TODATE."'";
				$cp_list_info = selectQuery($sql);

				if($cp_info['idx']){

					$qstring = "";
					$qstring_val = "";
					$cp_type = "";
					if($cp_info['score1']){
						$qstring .= ",type1";
						$qstring_val .= ",'".($cp_info['score1']>0?$cp_info['score1']:$cp_info['type1'])."'";

						$cp_kind[] = '1';
						$cp_type[] = 'type1';
					}

					if($cp_info['score2']){
						$qstring .= ",type2";
						$qstring_val .= ",'".($cp_info['score2']>0?$cp_info['score2']:$cp_info['type2'])."'";

						$cp_kind[] = '2';
						$cp_type[] = 'type2';
					}

					if($cp_info['score3']){
						$qstring .= ",type3";
						$qstring_val .= ",'".($cp_info['score3']>0?$cp_info['score3']:$cp_info['type3'])."'";

						$cp_kind[] = '3';
						$cp_type[] = 'type3';
					}

					if($cp_info['score4']){
						$qstring .= ",type4";
						$qstring_val .= ",'".($cp_info['score4']>0?$cp_info['score4']:$cp_info['type4'])."'";

						$cp_kind[] = '4';
						$cp_type[] = 'type4';
					}

					if($cp_info['score5']){
						$qstring .= ",type5";
						$qstring_val .= ",'".($cp_info['score5']>0?$cp_info['score5']:$cp_info['type5'])."'";

						$cp_kind[] = '5';
						$cp_type[] = 'type5';
					}

					if($cp_info['score6']){
						$qstring .= ",type6";
						$qstring_val .= ",'".($cp_info['score6']>0?$cp_info['score6']:$cp_info['type6'])."'";

						$cp_kind[] = '6';
						$cp_type[] = 'type6';
					}


					//역량지표 데이터가 없고, 횟수제한 없을때
					if(!$cp_list_info['idx'] && $cp_info['limit'] == null){
						$sql = "insert into work_cp_reward_list(companyno,email,name,work_idx,link_idx,service,act,ip,workdate".$qstring.") values";
						$sql = $sql .= " ('".$companyno."','".$mem_user_id."','".$mem_user_name."','".$idx."','".$cp_idx."','".$work."','".$mode."','".LIP."','".TODATE."'".$qstring_val.")";
						$insert_idx = insertIdxQuery($sql);
					}else if ($cp_list_info['cnt'] < $cp_info['limit']){

						$sql = "insert into work_cp_reward_list(companyno,email,name,work_idx,link_idx,service,act,ip,workdate".$qstring.") values";
						$sql = $sql .= " ('".$companyno."','".$mem_user_id."','".$mem_user_name."','".$idx."','".$cp_idx."','".$work."','".$mode."','".LIP."','".TODATE."'".$qstring_val.")";
						$insert_idx = insertIdxQuery($sql);
					}

				}
			}
		}
	}



	//work_cp_reward("work", "0001", $user_id, $res_idx);

	//work_cp_reward("like","0003", $send_info['email'], $insert_idx);

	//역량 평가 지표(work, 0001, 아이디, 게시물idx), 역량지표 등록 처리, 업무구분
	//function work_cp_reward($work, $mode, $uid="", $idx="", $work_idx="", $work_type=""){

	//"work", "0004", $user_id, $work_mem_email, $res_idx, $insert_idx

	function work_cp_reward($work, $mode, $uid, $idx, $send_id="", $work_idx=""){
		global $conn, $companyno;
		//오늘업무 작성
		//write_log("\r\n");
		//write_log($work . " , " . $mode. " , " .$uid. " , " .$idx);

		//write_log("역량지표입력");
		//write_log("========\n");

		//write_log($work .",". $mode .",". $uid .",". $idx .",". $work_idx .",". $work_type);
		//echo $work .",". $mode .",". $uid .",". $idx .",". $work_idx .",". $work_type;
		//echo "\n\n";
		//"like","0007", $send_info['email'], $insert_idx

		//등록한 유저정보
		$sql = "select idx, email, name from work_member where state='0' and companyno='".$companyno."' and email='".$uid."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){
			//회원이름
			$user_id = $mem_info['email'];
			$user_name = $mem_info['name'];

			//일일업무
			$sql = "select idx, state, work_idx from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$idx."'";
			$work_info = selectQuery($sql);

			//받은 업무 idx
			$work_idx = $work_info['idx'];

			//역량평가 지표
			$sql = "select idx, limit, type1, type2, type3, type4, type5, type6, score1, score2, score3, score4, score5, score6 from work_cp_reward where state='0' and service='".$work."' and act='".$mode."'";
			$cp_info = selectQuery($sql);

			$qstring = "";
			$qstring_val = "";
			$cp_type = "";
			if($cp_info['type1']){
				$qstring .= ",type1";
				$qstring_val .= ",'".($cp_info['score1']>0?$cp_info['score1']:$cp_info['type1'])."'";

				$cp_kind[] = '1';
				$cp_type[] = 'type1';
			}

			if($cp_info['type2']){
				$qstring .= ",type2";
				$qstring_val .= ",'".($cp_info['score2']>0?$cp_info['score2']:$cp_info['type2'])."'";

				$cp_kind[] = '2';
				$cp_type[] = 'type2';
			}

			if($cp_info['type3']){
				$qstring .= ",type3";
				$qstring_val .= ",'".($cp_info['score3']>0?$cp_info['score3']:$cp_info['type3'])."'";

				$cp_kind[] = '3';
				$cp_type[] = 'type3';
			}

			if($cp_info['type4']){
				$qstring .= ",type4";
				$qstring_val .= ",'".($cp_info['score4']>0?$cp_info['score4']:$cp_info['type4'])."'";

				$cp_kind[] = '4';
				$cp_type[] = 'type4';
			}

			if($cp_info['type5']){
				$qstring .= ",type5";
				$qstring_val .= ",'".($cp_info['score5']>0?$cp_info['score5']:$cp_info['type5'])."'";

				$cp_kind[] = '5';
				$cp_type[] = 'type5';
			}

			if($cp_info['type6']){
				$qstring .= ",type6";
				$qstring_val .= ",'".($cp_info['score6']>0?$cp_info['score6']:$cp_info['type6'])."'";

				$cp_kind[] = '6';
				$cp_type[] = 'type6';
			}


			//아이디별 역량평가 내역
			$sql = "select count(1) as cnt from work_cp_reward_list where state='0' and service='".$work."' and act='".$mode."' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
			$cp_list_info = selectQuery($sql);

			//역량지표 데이터가 없고, 횟수제한 없을때
			if(!$cp_list_info['idx'] && $cp_info['limit'] == null){

				$sql = "insert into work_cp_reward_list(companyno,email,name,work_idx,link_idx,service,act,ip,workdate".$qstring.") values";
				$sql = $sql .= " ('".$companyno."','".$user_id."','".$user_name."','".$idx."','".$work_idx."','".$work."','".$mode."','".LIP."','".TODATE."'".$qstring_val.")";
				$insert_idx = insertIdxQuery($sql);
				if($insert_idx){

					work_cp_reward_plus($cp_kind, $cp_type, $idx, $insert_idx, $work);
					//if($user_id=='sadary0@nate.com' || $user_id=='adsb12@nate.com'){
					//}else{
						//협업,성실,실행 획득시 성장 +1추가
						//if($cp_kind){
						//	work_cp_reward_month($cp_kind, $cp_type, $idx, $insert_idx);
						//}
					//}
				}

				//역략평가 내역 입력(제한 수보다 적을때만 입력)
			}else if ($cp_list_info['cnt'] < $cp_info['limit']){

				$sql = "insert into work_cp_reward_list(companyno,email,name,work_idx,link_idx,service,act,ip,workdate".$qstring.") values";
				$sql = $sql .= " ('".$companyno."','".$user_id."','".$user_name."','".$idx."','".$work_idx."','".$work."','".$mode."','".LIP."','".TODATE."'".$qstring_val.")";
				$insert_idx = insertIdxQuery($sql);

				if($insert_idx){

					work_cp_reward_plus($cp_kind, $cp_type, $idx, $insert_idx, $work);


					//협업,성실,실행 획득시 성장 +1추가
					//if($cp_kind){
					//	work_cp_reward_month($cp_kind, $cp_type, $idx, $insert_idx);
					//}
						// if($user_id == 'adsb12@nate.com'){

						// 레이어 띄우기 (신규)
						$sql = "select memo from work_cp_reward_layer where state = 0 and companyno = '".$companyno."' and service = '".$work."' and act = '".$mode."'";
						$layer_up = selectQuery($sql);

							if($layer_up){
								$layer_sh = $layer_up[memo];
								return $layer_sh;
							}
						// }
						// else if($work == 'work' && $mode =='0001'){
						// 	return "type6";
						// }
				}
			}




		}
	}


	//역량지표 협업,성실,실행 == 성장 +1 올림
	//역량(1:지식, 2:성과, 3:성장, 4:협업, 5:성실, 6:실행), 필드(type1), 오늘업무 idx값, work_cp_reward_list테이블 insert_idx번호
	//work_cp_reward_month('4', 'type4', '8274')
	function work_cp_reward_month($kind, $type, $idx, $link_idx=""){
		global $conn, $user_id, $user_name, $companyno, $month_first_day, $month_last_day, $chkMobile;

		//구분(0:사이트, 1:모바일)
		$type_flag = ($chkMobile)?1:0;

		//데이터 삽입
		$sql = "select top 1 idx, type1, type2, type3, type4, type5, type6 from work_cp_reward_month where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate between '".$month_first_day."' and '".$month_last_day."' order by idx asc";
		$reward_info = selectQuery($sql);
		if($reward_info['idx']){
			$reward_info_idx = $reward_info['idx'];
			//kind == 1:에너지, 2:성과, 3:성장, 4:협업, 5:성실, 6:실행
			for($i=0; $i<count($kind); $i++){

				$kind_no = $kind[$i];
				$cp_kind = "type".$kind_no;

				if($reward_info[$cp_kind] == null){
					//역량, idx
					$reward_idx = reward_month_update($kind_no, $reward_info_idx);
					if($reward_idx){
						//로그저장
						insert_reward_month_log($kind_no, $reward_idx, $reward_info_idx);
					}
				}else{

					//데이터삽입
					$reward_idx = reward_month_insert($kind_no);
					if($reward_idx){
						//로그저장
						insert_reward_month_log($kind_no, $reward_idx, $reward_info_idx);
					}
				}
			}
		}else{

			for($i=0; $i<count($kind); $i++){

				$kind_no = $kind[$i];
				$cp_kind = "type".$kind_no;

				//데이터삽입
				$reward_idx = reward_month_insert($kind_no);
				if($reward_idx){
					//로그저장
					insert_reward_month_log($kind_no, $reward_idx, $reward_info_idx);
				}
			}
		}

		//역량지표 체크하기
		$sql = "select top 1 idx, type1, type2, type3, type4, type5, type5, type6 from work_cp_reward_month where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate between '".$month_first_day."' and '".$month_last_day."' order by idx asc";
		$reward_info = selectQuery($sql);

		if($reward_info['idx']){
			$type1 = empty($reward_info['type1'])?0:$reward_info['type1'];
			$type2 = empty($reward_info['type2'])?0:$reward_info['type2'];
			$type3 = empty($reward_info['type3'])?0:$reward_info['type3'];
			$type4 = empty($reward_info['type4'])?0:$reward_info['type4'];
			$type5 = empty($reward_info['type5'])?0:$reward_info['type5'];
			$type6 = empty($reward_info['type6'])?0:$reward_info['type6'];

			$sql = "select idx, act, limit, score1, score2, score3, score4, score5, score6 from work_cp_reward where state='0' and service='cp' and type1='".$type1."' and type2='".$type2."' and type3='".$type3."' and type4='".$type4."' and type5='".$type5."' and type6='".$type6."'";
			$reward_cp_info = selectQuery($sql);
			if($reward_cp_info['idx']){

				$act = $reward_cp_info['act'];
				$sql = "update work_cp_reward_month set state='1', editdate=getdate() where companyno='".$companyno."' and idx='".$reward_info['idx']."'";
				$up_idx = updateQuery($sql);
				if($up_idx){
					work_cp_reward_add("cp", $act, $user_id, $idx, $reward_cp_info['idx']);
				}
			}
		}
	}


	//가산점
	function work_cp_reward_plus($kind, $type, $idx, $link_idx="", $plus_type=""){
		global $conn, $user_id, $user_name, $companyno, $month_first_day, $month_last_day, $chkMobile;

		//구분(0:사이트, 1:모바일)
		$type_flag = ($chkMobile)?1:0;

		//출근
		if($plus_type == 'login'){

			$sql = "select top 1 idx, email, name from work_member_login where state='0' and companyno='".$companyno."' and workdate='".TODATE."' order by idx asc";
			$login_top_info = selectQuery($sql);
			//내역이 있을때
			if($login_top_info['idx']){

				//접속한아이디와 로그인한 아이디가 같을때
				if($user_id == $login_top_info['email']){
					work_cp_reward_add("cp", "0005", $login_top_info['email'], $login_top_info['idx']);

					//출근1등(메인 좋아요 지표)
					main_like_cp_login();
				}
			}

		//좋아요받음
		}else if($plus_type == 'like'){

			$sql = "select top 1 idx, email, name from work_todaywork_like where state='0' and companyno='".$companyno."' and idx='".$idx."' order by idx asc";
			$like_top_info = selectQuery($sql);
			if($like_top_info['idx']){
				work_cp_reward_add("cp", "0006", $like_top_info['email'], $like_top_info['idx']);
			}
		}else if($plus_type == "reward"){

			//보상받는 사용자
			$send_userid = $link_idx;
			work_cp_reward_add("cp", "0007", $send_userid, $idx);

		}else{
			//데이터 체크
			$sql = "select top 1 idx, type1, type2, type3, type4, type5, type6 from work_cp_reward_month where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate between '".$month_first_day."' and '".$month_last_day."' order by idx asc";
			$reward_info = selectQuery($sql);
			if($reward_info['idx']){
				$reward_info_idx = $reward_info['idx'];
				//kind == 1:에너지, 2:성과, 3:성장, 4:협업, 5:성실, 6:ㅈ실행
				for($i=0; $i<count($kind); $i++){

					$kind_no = $kind[$i];
					$cp_kind = "type".$kind_no;

					if($reward_info[$cp_kind] == null){
						//역량, idx
						$reward_idx = reward_month_update($kind_no, $reward_info_idx);
						if($reward_idx){
							//로그저장
							insert_reward_month_log($kind_no, $reward_idx, $reward_info_idx);
						}
					}else{

						if($cp_kind){
							$que = " and ".$cp_kind." is null";
							$sql = "select top 1 idx, type1, type2, type3, type4, type5, type6 from work_cp_reward_month where state='0' and companyno='".$companyno."' and email='".$user_id."'";
							$sql = $sql .= "".$que." and workdate between '".$month_first_day."' and '".$month_last_day."' order by idx asc";
							$reward_info = selectQuery($sql);
							if($reward_info['idx']){
								$reward_info_idx = $reward_info['idx'];
								$reward_idx = reward_month_update($kind_no, $reward_info_idx);
							}else{

								$reward_idx = reward_month_insert($kind_no, $reward_info);
								if($reward_idx){
									//로그저장
									insert_reward_month_log($kind_no, $reward_idx, $reward_info_idx);
								}
							}
						}
					}
				}
			}else{

				for($i=0; $i<count($kind); $i++){

					$kind_no = $kind[$i];
					$cp_kind = "type".$kind_no;

					//데이터삽입
					$reward_idx = reward_month_insert($kind_no);
					if($reward_idx){
						//로그저장
						insert_reward_month_log($kind_no, $reward_idx, $reward_info_idx);
					}
				}
			}

			//역량지표 체크하기
			$sql = "select top 1 idx, type1, type2, type3, type4, type5, type6 from work_cp_reward_month where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate between '".$month_first_day."' and '".$month_last_day."' order by idx asc";
			$reward_info = selectQuery($sql);

			if($reward_info['idx']){

				$type1 = empty($reward_info['type1'])?0:$reward_info['type1'];
				$type2 = empty($reward_info['type2'])?0:$reward_info['type2'];
				$type3 = empty($reward_info['type3'])?0:$reward_info['type3'];
				$type4 = empty($reward_info['type4'])?0:$reward_info['type4'];
				$type5 = empty($reward_info['type5'])?0:$reward_info['type5'];
				$type6 = empty($reward_info['type6'])?0:$reward_info['type6'];


				$sql = "select idx, act, limit, score1, score2, score3, score4, score5, score6 from work_cp_reward where state='0' and service='cp' and type1='".$type1."' and type2='".$type2."' and type3='".$type3."' and type4='".$type4."' and type5='".$type5."' and type6='".$type6."'";
				$reward_cp_info = selectQuery($sql);




				if($user_id=='sadary0@nate.com' || $user_id=='adsb12@nate.com'){
					//echo $sql;
					//echo "\n\n";
				}
				if($reward_cp_info['idx']){

					$act = $reward_cp_info['act'];
					$sql = "update work_cp_reward_month set state='1', editdate=getdate() where companyno='".$companyno."' and idx='".$reward_info['idx']."'";
					if($user_id=='sadary0@nate.com' || $user_id=='adsb12@nate.com'){
						//echo $sql;
						//echo "\n\n";
					}

					$up_idx = updateQuery($sql);
					if($up_idx){
						work_cp_reward_add("cp", $act, $user_id, $idx, $reward_cp_info['idx']);
					}
				}
			}
		}

	}



	//내역저장
	function reward_month_insert_all($kind, $cp_idx){

		global $conn, $user_id, $user_name, $companyno, $month_first_day, $month_last_day, $chkMobile;

		//구분(0:사이트, 1:모바일)
		$type_flag = ($chkMobile)?1:0;

		$que = "";
		$que_values = "";
		if($kind == '4'){
			$que = ", type4";
			$que_values = ",'1'";
			$memo = "협업 +1 획득";
		}

		if($kind == '5'){
			$que = ", type5";
			$que_values = ",'1'";
			$memo = "성실 +1 획득";
		}

		if($kind == '6'){
			$que = ", type6";
			$que_values = ",'1'";
			$memo = "실행 +1 획득";
		}

		$sql = "insert into work_cp_reward_month(companyno, type_flag, email, name".$que.",ip, workdate)";
		$sql = $sql .= " values('".$companyno."','".$type_flag."','".$user_id."','".$user_name."'".$que_values.",'".LIP."','".TODATE."')";
		$insert_idx = insertIdxQuery($sql);
		if($insert_idx){
			$sql = "insert into work_cp_reward_month_log(companyno, type_flag, work_idx, cp_idx".$que.", email, name, memo, workdate, ip)";
			$sql = $sql .= " values('".$companyno."','".$type_flag."','".$insert_idx."','".$cp_idx."'".$que_values.", '".$user_id."','".$user_name."','".$memo."','".TODATE."','".LIP."')";
			insertQuery($sql);
		}
	}


	//역량인서트
	function reward_month_insert($kind, $cp_info=""){
		global $conn, $user_id, $user_name, $companyno, $chkMobile;

		//구분(0:사이트, 1:모바일)
		$type_flag = ($chkMobile)?1:0;

		//역량지표(에너지)
		if($kind == '1'){
			$que = ", type1";
			$que_values = ",'1'";
		}

		//역량지표(성과)
		if($kind == '2'){
			$que = ", type2";
			$que_values = ",'1'";
		}

		//역량지표(성장)
		if($kind == '3'){
			$que = ", type3";
			$que_values = ",'1'";
		}

		//역량지표(협업)
		if($kind == '4'){
			$que = ", type4";
			$que_values = ",'1'";
		}

		//역량지표(성실)
		if($kind == '5'){
			$que = ", type5";
			$que_values = ",'1'";
		}

		//역량지표(실행)
		if($kind == '6'){
			$que = ", type6";
			$que_values = ",'1'";
		}

		//업데이트처리
		if($cp_info){

			//모든 역략이 채워진 경우 업데이트 처리
			if($cp_info['type1'] && $cp_info['type2'] && $cp_info['type3'] && $cp_info['type4'] && $cp_info['type5'] && $cp_info['type6']){
				$sql = "select idx from work_cp_reward_month where state='0' and idx='".$cp_idx."'";
				$cp_month_info = selectQuery($sql);
				if($cp_month_info['idx']){
					$sql = "update work_cp_reward_month set state='1' where state='0' and idx='".$cp_month_info['idx']."'";
					updateQuery($sql);
				}
			}
		}

		$sql = "insert into work_cp_reward_month(companyno, type_flag, email, name".$que.", ip, workdate)";
		$sql = $sql .= " values('".$companyno."','".$type_flag."','".$user_id."','".$user_name."'".$que_values.",'".LIP."','".TODATE."')";
		$insert_idx = insertIdxQuery($sql);
		if($insert_idx){
			return $insert_idx;
		}
	}



	//역량업데이트
	function reward_month_update($kind, $cp_idx){
		global $conn, $user_id, $user_name, $companyno, $chkMobile;

		//구분(0:사이트, 1:모바일)
		$type_flag = ($chkMobile)?1:0;
		$que = "";

		//역량지표(에너지)
		if($kind == '1'){
			$que = " type1='1'";
		}

		//역량지표(성과)
		if($kind == '2'){
			$que = " type2='1'";
		}

		//역량지표(성장)
		if($kind == '3'){
			$que = " type3='1'";
		}

		//역량지표(협업)
		if($kind == '4'){
			$que = " type4='1'";
		}

		//역량지표(성실)
		if($kind == '5'){
			$que = " type5='1'";
		}

		//역량지표(실행)
		if($kind == '6'){
			$que = " type6='1'";
		}

		if($que){
			$sql = "update work_cp_reward_month set ".$que." where idx='".$cp_idx."'";
			$res = updateQuery($sql);
			if($res){
				//$sql = "select idx from work_cp_reward_month where state='0' and service='cp'";
				return $cp_idx;
			}
		}
	}


	//로그(work_cp_reward_month_log)내역 저장
	function insert_reward_month_log($kind, $insert_idx , $cp_idx ){
		global $conn, $user_id, $user_name, $companyno, $month_first_day, $month_last_day, $chkMobile;

		//구분(0:사이트, 1:모바일)
		$type_flag = ($chkMobile)?1:0;
		$memo = "";
		$que = "";
		$que_values = "";

		if($kind == '4'){
			$memo = "협업 +1 획득";
			$que = ", type4";
			$que_values = ",'1'";
		}

		if($kind == '5'){
			$memo = "성실 +1 획득";
			$que = ", type5";
			$que_values = ",'1'";
		}

		if($kind == '6'){
			$memo = "실행 +1 획득";
			$que = ", type6";
			$que_values = ",'1'";
		}

		$sql = "insert into work_cp_reward_month_log(companyno, type_flag, work_idx, cp_idx".$que.", email, name, memo, workdate, ip)";
		$sql = $sql .= " values('".$companyno."','".$type_flag."','".$insert_idx."', '".$cp_idx."'".$que_values.",'".$user_id."','".$user_name."','".$memo."','".TODATE."','".LIP."')";
		//write_log($sql);
		insertQuery($sql);

	}



	//역량 평가 지표 해제 처리(work, 0001, 게시물idx)
	function work_cp_unreward($work, $mode, $idx=""){
		global $conn, $user_id, $user_name;
		//if(in_array($user_id , array('sadary0@nate.com','eyson@bizforms.co.kr'))){
			if($work){
				$sql = "select idx, state from work_todaywork where state='0' and idx='".$idx."' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
				$work_info = selectQuery($sql);
				if($work_info['idx']){

					//등록된 업무
					$sql = "select idx, work_idx from work_cp_reward_list where state='0' and companyno='".$companyno."' and service='".$work."' and act='".$mode."' and work_idx='".$idx."' and email='".$user_id."' and workdate='".TODATE."'";
					$cp_list_info = selectQuery($sql);
					if($cp_list_info['idx']){

						//역량지표 삭제
						work_cp_delreward_month($cp_list_info['work_idx']);

						$sql = "update work_cp_reward_list set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$cp_list_info['idx']."'";
						$res = updateQuery($sql);
					}
				}
			}
		//}
	}


	//역량 평가 지표 삭제 처리(work, 0001, 게시물idx, 삭제번호)
	function work_cp_delreward($mode, $idx){
		global $conn, $user_id, $user_name, $companyno;

		if($mode){
			$sql = "select idx, state from work_todaywork where idx='".$idx."' and companyno='".$companyno."' and email='".$user_id."'";
			$work_info = selectQuery($sql);

			if($work_info['idx']){

				//역량지표 삭제
				work_cp_delreward_month($idx);

				//if($link_idx){
				//	$sql = "select idx, work_idx from work_cp_reward_list where state='0' and companyno='".$companyno."' and service='".$work."' and act='".$mode."' and work_idx='".$idx."' and link_idx='".$link_idx."' and email='".$user_id."'";
				//}else{
				$sql = "select idx, work_idx from work_cp_reward_list where state='0' and companyno='".$companyno."' and service='".$mode."' and work_idx='".$idx."' and email='".$user_id."'";
				//}
				$cp_list_info = selectQuery($sql);
				if($cp_list_info['idx']){

					//역량지표 점수 모두 삭제처리
					$sql = "update work_cp_reward_list set state='9', editdate=".DBDATE." where work_idx='".$cp_list_info['work_idx']."'";
					$res = updateQuery($sql);
				}
			}
		}
	}


	//역량 평가 지표 삭제 처리
	function work_cp_delreward_month($idx){
		global $conn, $user_id, $user_name, $companyno;

		if($idx){

			//오늘업무 조회
			$sql = "select idx, state from work_todaywork where idx='".$idx."' and companyno='".$companyno."' and email='".$user_id."'";
			$work_info = selectQuery($sql);
			if($work_info['idx']){

				//역량지표 데이터 조회
				$sql = "select idx from work_cp_reward_list where state='0' and companyno='".$companyno."' and work_idx='".$work_info['idx']."'";
				$cp_reward_list = selectQuery($sql);
				if($cp_reward_list['idx']){

					//역량지표 데이터 로그 조회
					$sql = "select idx,work_idx,type1,type2,type3,type4,type5,type6 from work_cp_reward_month_log where state='0' and companyno='".$companyno."' and cp_idx='".$cp_reward_list['idx']."' and email='".$user_id."'";
					$log_info = selectQuery($sql);
					if($log_info['work_idx']){

						if($log_info['type1']){
							$whereis = " and type1='1'";
							$updateis = " type1=null";

						}else if($log_info['type2']){
							$whereis = " and type2='1'";
							$updateis = " type2=null";

						}else if($log_info['type3']){
							$whereis = " and type3='1'";
							$updateis = " type3=null";

						}else if($log_info['type4']){
							$whereis = " and type4='1'";
							$updateis = " type4=null";

						}else if($log_info['type5']){
							$whereis = " and type5='1'";
							$updateis = " type5=null";

						}else if($log_info['type6']){
							$whereis = " and type6='1'";
							$updateis = " type6=null";
						}

						//삭제처리-업데이트
						if($whereis && $updateis){
							$sql = "select idx from work_cp_reward_month where idx='".$log_info['work_idx']."' and companyno='".$companyno."'".$whereis."";
							$reward_month_info = selectQuery($sql);
							if($reward_month_info['idx']){
								$sql = "update work_cp_reward_month set ".$updateis.", editdate=getdate() where idx='".$reward_month_info['idx']."'";
								$up = updateQuery($sql);
								if($up){
									$sql = "update work_cp_reward_month_log set state='9', ".$updateis.", editdate=getdate() where work_idx='".$reward_month_info['idx']."'";
									$up = updateQuery($sql);
								}
							}
						}
					}
				}
			}
		}
	}

	//보상시스템
	function coin_add($mode, $idx=""){
		global $conn , $user_id;

		$code = "";
		$coin = "";
		$memo = "";

		if($mode && $user_id){
			$sql = "select idx, code, kind, coin, memo from work_coin_reward where state='0' and kind='".$mode."' order by idx asc";
			$reward_info = selectQuery($sql);

			if($reward_info['idx']){
				//보상코인
				$coin = $reward_info['coin'];

				//보상코드값
				$code = $reward_info['code'];

				//보상메모
				$memo = $reward_info['memo'];

				//주별(월~금)
				$week_info = week_day();

				//코드, 아이디, 코인, 적립내역
				$works_info = array();
				$works_info['code'] = $code;
				$works_info['user'] = $user_id;
				$works_info['coin'] = $coin;
				$works_info['memo'] = $memo;
				$works_info['work_idx'] = $idx;

				//보상 내역 체크
				if(in_array($code , array("200","201","202"))){

					//200 = 업무시작 30분 이내에 오늘업무 등록
					//201 = 퇴근 전 오늘업무 완료여부 체크
					//203 = 업무요청 1건 이상 받아서 처리완료
					$sql = "select count(idx) as cnt from work_coininfo where state='0' and companyno='".$companyno."' and code='".$code."' and email='".$user_id."'";
					$sql = $sql .= " and convert(char(10) , regdate, 120) = convert(char(10), getdate(), 120)";
					$coin_info = selectQuery($sql);

				}else if($code == "203"){

					//주간일정 미리 설정하기
					$sql = "select idx from work_coininfo where state='0' and companyno='".$companyno."' and code='".$code."' and email='".$user_id."'";
					$sql = $sql .= " and convert(varchar(10) , regdate, 120) between '".$week_info['month']."' and '".$week_info['friday']."'";
					$coin_info = selectQuery($sql);

				}else if($code == "204"){

					//목표 설정 후 달성완료
					$sql = "select idx from work_coininfo where state='0' and companyno='".$companyno."' and code='".$code."' and email='".$user_id."'";
					$sql = $sql .= " and convert(varchar(10), regdate, 120) = convert(varchar(10), getdate(), 120)";
					$coin_info = selectQuery($sql);

				}else{

					//최초 가입 후 로그인 시 웰컴코인
					//최초 1회 오늘업무 등록
					//최초 1회 오늘업무 완료
					//최초 1회 업무요청 등록
					//최초 1회 업무요청 완료

					$sql = "select idx from work_coininfo where state='0' and companyno='".$companyno."' and code='".$code."' and email='".$user_id."' ";
					$coin_info = selectQuery($sql);
				}


				//회원정보
				$sql = "select idx, name, login_count from work_member where state='0' companyno='".$companyno."' and email = '".$user_id."'";
				$mem_info = selectQuery($sql);
				//$mem_info['login_count'] = '1';

				//이름
				$works_info['name'] = $mem_info['name'];

				//최초 가입 후 로그인 시 웰컴코인
				if($mode == "login"){
					if($mem_info['idx'] && $mem_info['login_count'] == '1'){
						//보상이력이 없는 경우
						if(!$coin_info['idx']){
							work_coininfo_add($works_info);
						}
					}
				}else if($mode == "works_write"){

					//최초 1회 오늘업무 등록
					$sql = "select count(1) as cnt from work_todaywork where state='0' and companyno='".$companyno."' and email='".$user_id."'";
					$work_info = selectQuery($sql);
					$work_info['cnt'] =1;
					if($work_info['cnt'] == '1'){
						if(!$coin_info['idx']){
							work_coininfo_add($works_info);
						}
					}
				}else if($mode == "works_complete"){

					//최초 1회 오늘업무 완료
					$sql = "select count(1) as cnt from work_todaywork where state='1' and companyno='".$companyno."' and email='".$user_id."'";
					$work_info = selectQuery($sql);
					if($work_info['cnt'] == '1'){
						if(!$coin_info['idx']){
							work_coininfo_add($works_info);
						}
					}
				}else if($mode == "works_req_write"){

					//최초 1회 업무요청 등록
					$sql = "select count(1) as cnt from work_todaywork where state='0' and companyno='".$companyno."' and work_flag='2' and email='".$user_id."'";
					$work_info = selectQuery($sql);
					if($work_info['cnt'] == '1'){
						if(!$coin_info['idx']){
							work_coininfo_add($works_info);
						}
					}
				}else if($mode == "works_req_complete"){

					//==최초 1회 업무요청 완료
					//
					$sql = "select count(1) as cnt from work_todaywork where state='1' and companyno='".$companyno."' and work_flag='2' and email='".$user_id."'";
					$work_info = selectQuery($sql);
					if(!$work_info['cnt']){
						if(!$coin_info['idx']){
							work_coininfo_add($works_info);
						}
					}
				}else if($mode == "works_goal"){

					//최초 1회 목표설정 및 달성
					$sql = "select count(1) as cnt from work_todaywork where state in ('0','1') and companyno='".$companyno."' and work_flag='3' and email='".$user_id."'";
					$work_info = selectQuery($sql);

					if($work_info['cnt'] == '1'){
						if(!$coin_info['idx']){
							work_coininfo_add($works_info);
						}
					}
				}else if($mode == "works_30min"){

					//업무시작 30분 이내에 오늘업무 등록 (일일 3회)
					$sql = "select count(1) as cnt from work_todaywork where state='0' and companyno='".$companyno."' and email='".$user_id."'";
					$sql = $sql .= " and convert(char(10) , regdate, 120)=convert(char(10) ,getdate(), 120)";
					$sql = $sql .= " and DATEDIFF(MI, getdate(), CONVERT(VARCHAR, GETDATE(), 23) + ' 09:30:00') >= '0' and DATEDIFF(MI, getdate(), CONVERT(VARCHAR, GETDATE(), 23) + ' 09:30:00') <= 30";
					$work_info = selectQuery($sql);
					if($work_info['cnt'] <= '3'){
						if($coin_info['cnt'] <= '3'){
							work_coininfo_add($works_info);
						}
					}
				}else if($mode == "works_quit"){

					//퇴근 전 오늘업무 완료여부 체크 (일일 3회)
					$sql = "select count(1) as cnt from work_todaywork where state='1' and companyno='".$companyno."' and email='".$user_id."'";
					$sql = $sql .= " and convert(char(10) , regdate, 120)=convert(char(10) ,getdate(), 120)";
					$sql = $sql .= " and regdate < CONVERT(VARCHAR, GETDATE(), 23) + ' 18:00:00'";
					$work_info = selectQuery($sql);
					if($work_info['cnt'] <= '3'){
						if($coin_info['cnt'] <= '3'){
							work_coininfo_add($works_info);
						}
					}
				}else if($mode == "works_req_recomplete"){
					//삭제 또는 완료 취소하기
					$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and email='".$user_id."' and idx='".$idx."'";
					$work_info = selectQuery($sql);
					if($work_info['idx']){
						work_coininfo_del($work_info['idx']);
					}

				}else if($mode == "works_req_complete1"){

					//업무요청 1건 이상 받아서 처리완료 (일일 3회)
					$sql = "select count(a.idx) as cnt from work_todaywork as a left join work_req_write as b on (a.idx = b.work_idx)";
					$sql = $sql .= " where a.state='1' and companyno='".$companyno."' and work_flag='2' and convert(varchar(10), workdate, 120) = convert(varchar(10), getdate(), 120)";
					$sql = $sql .= " and b.email='".$user_id."'";
					$work_info = selectQuery($sql);
					if($work_info['cnt'] <= '3'){
						if($coin_info['cnt'] <= '3'){
							work_coininfo_add($works_info);
						}
					}
				}else if($mode == "works_week"){

					//주간일정 미리 설정하기
					$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and convert(varchar(10) , workdate, 120) between '".$week_info['month']."' and '".$week_info['friday']."'";
					$sql = $sql .= " and email='".$user_id."'";
					$work_info = selectQuery($sql);
					if(!$work_info['idx']){
						if(!$coin_info['idx']){
							work_coininfo_add($works_info);
						}
					}
				}else if($mode == "works_goal_complete"){

					//목표 설정 후 달성완료
					$sql = "select count(idx) as cnt from work_todaywork where state='1' and companyno='".$companyno."' and work_flag='3' and convert(varchar(10), workdate, 120) = convert(varchar(10), getdate(), 120)";
					$sql = $sql .= " and email='".$user_id."' and idx='".$idx."'";
					$work_info = selectQuery($sql);
					if($work_info['cnt'] == '1'){
						if(!$coin_info['idx']){
							work_coininfo_add($works_info);
						}
					}
				}

			}
		}

	}


	//보상코인 회수(번호, 상태값)
	function coin_del($idx, $state){

		global $conn , $user_id;
		if($idx){
			$sql = "select idx, work_flag from work_todaywork where state in ('0', '9') and companyno='".$companyno."' and idx='".$idx."' and convert(varchar(10), workdate, 120) = convert(varchar(10), getdate(), 120)";
			$sql = $sql .= " and email='".$user_id."'";
			$work_info = selectQuery($sql);

			if($work_info['idx']){


				//업무요청
				if($work_info['work_flag'] == '2'){
					$sql = "select idx, coin, email from work_coininfo where companyno='".$companyno."' and work_idx='".$idx."'";
					$coin_info = selectQuery($sql);
					if($coin_info['idx']){
						$sql = "update work_coininfo set state='9' where companyno='".$companyno."' and idx='".$coin_info['idx']."'";
						$res = updateQuery($sql);
						if($res){
							if($coin_info['coin'] > 0){
								$sql = "select idx, coin from work_member companyno='".$companyno."' and where state='0' and email='".$coin_info['email']."'";
								$mem_info = selectQuery($sql);
								if($mem_info['idx']){
									$sql = "update work_member set coin = coin -'".$coin_info['coin']."' where state='0' and companyno='".$companyno."' and email='".$mem_info['email']."'";
									$res = updateQuery($sql);
								}
							}
						}
					}

				}else{

					//등록된 업무
					if($state=='0'){
						$wcode = "101";
					}else if($state=='1'){
						//완료된 업무
						$wcode = "102";
					}else if($state =='9'){
						//삭제된 업무
						//$wcode = "102";
					}

					$sql = "select idx, coin from work_coininfo where companyno='".$companyno."' and code='".$wcode."' and work_idx='".$idx."' and email='".$user_id."'";
					$coin_info = selectQuery($sql);
					if($coin_info['idx']){
						$sql = "update work_coininfo set state='9' where companyno='".$companyno."' and idx='".$coin_info['idx']."'";
						$res = updateQuery($sql);
						if($res){
							$sql = "update work_member set coin = coin-'".$coin_info['coin']."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
							$res = updateQuery($sql);
						}
					}
				}
			}
		}
	}


	//코인확인
	function email_coin($email){
		global $conn, $companyno;
		if($email){
			$sql = "select idx, coin from work_member where state='0' and companyno='".$companyno."' and email='".$email."'";
			$coin_info = selectQuery($sql);
			if($coin_info['idx'] && $coin_info['coin'] > 0){
				return $coin_info['coin'];
			}else{
				return '0';
			}
		}
		exit;
	}


	//업무요청 : 업무요청수, 업무요청완료수
	function work_req_info($idx){
		global $companyno, $user_id;

		$work_req_info= array();

		//오늘업무 조회
		$sql = "select idx, work_flag, work_idx from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$idx."'";
		$work_info =  selectQuery($sql);
		if($work_info['idx']){

			$work_flag = $work_info['work_flag'];
			$work_idx = $work_info['work_idx'];

			//요청업무
			//if($work_flag=='3'){
				//요청업무
				$sql = "select count(idx) as cnt from work_todaywork where state!='9' and companyno='".$companyno."' and work_idx='".$work_idx."'";
				$req_info =  selectQuery($sql);

				//요청한 업무완료갯수
				$sql = "select count(idx) as cnt from work_todaywork where state='1' and companyno='".$companyno."' and work_idx='".$work_idx."'";
				$com_info =  selectQuery($sql);
			//}

			$work_req_info['idx'] = $work_info['idx'];
			$work_req_info['work_idx'] = $work_info['work_idx'];
			$work_req_info['req_tot'] = $req_info['cnt'];
			$work_req_info['req_com'] = $com_info['cnt'];
		}
		return $work_req_info;
	}



	//주별 확인(월 ~ 금)
	function week_day($w=""){
		if($w){
			$time = strtotime($w);
		}else{
			$time = time();
		}
		$week = date("w", $time);

		if($week == 0){
			$monthday = date('Y-m-d', strtotime("-6 day", $time));
			$friday = date('Y-m-d', strtotime("-2 day", $time));
			$sunday = date('Y-m-d', $time);
		}else if($week == 1){
			$monthday = date('Y-m-d', $time);
			$friday = date('Y-m-d', strtotime("+4 day", $time));
			$sunday = date('Y-m-d', strtotime("+6 day", $time));
		}else if($week == 2){
			$monthday = date('Y-m-d', strtotime("-1 day", $time));
			$friday = date('Y-m-d', strtotime("+3 day", $time));
			$sunday = date('Y-m-d', strtotime("+5 day", $time));
		}else if($week == 3){
			$monthday = date('Y-m-d', strtotime("-2 day", $time));
			$friday = date('Y-m-d', strtotime("+2 day", $time));
			$sunday = date('Y-m-d', strtotime("+4 day", $time));
		}else if($week == 4){
			$monthday = date('Y-m-d', strtotime("-3 day", $time));
			$friday = date('Y-m-d', strtotime("+1 day", $time));
			$sunday = date('Y-m-d', strtotime("+3 day", $time));

			//echo "|| ". $monthday . "=========>> " . $sunday;


		}else if($week == 5){
			$monthday = date('Y-m-d', strtotime("-4 day", $time));
			$friday = date('Y-m-d', $time);
			$sunday = date('Y-m-d', strtotime("+2 day", $time));
		}else if($week == 6){
			$monthday = date('Y-m-d', strtotime("-5 day", $time));
			$friday = date('Y-m-d', strtotime("-1 day", $time));
			$sunday = date('Y-m-d', strtotime("+1 day", $time));
		}

		$info = array();
		$info['month'] = $monthday;
		$info['friday'] = $friday;
		$info['sunday'] = $sunday;
		return $info;
	}


	//파일업로드
	//임시파일, 업로드파일
	function file_upload_send( $tmpfile ,  $uploadfile ){
		if($tmpfile && $uploadfile){
			if(move_uploaded_file($tmpfile, $uploadfile)){
				/*echo "<img src ={$_FILES['file']['name']}> <p>";
				echo "1. file name : {$_FILES['file']['name']}<br />";
				echo "2. file type : {$_FILES['file']['type']}<br />";
				echo "3. file size : {$_FILES['file']['size']} byte <br />";
				echo "4. temporary file size : {$_FILES['file']['size']}<br />";
				*/
				$upfiles = true;
			} else {
				$upfiles = false;
			}
			return $upfiles;
		}
	}



	//JPG 이미지 만드는 함수
	function fn_imagejpeg($image, $upload_file, $new_file_width, $new_file_height, $width, $height, $new_quality) {
		$tmpCreation = imagecreatetruecolor($new_file_width, $new_file_height);

		imagecopyresampled($tmpCreation, $image, 0, 0, 0, 0, $new_file_width, $new_file_height, $width, $height);
		imagejpeg($tmpCreation, $upload_file, $new_quality);

		// 원본 이미지 리소스 종료
		$ret = imagedestroy($tmpCreation);
		return $ret;
	}


	//배열 키값 리셋처리 함수
	function array_key_reset($arr){

		$j = 0;
		foreach($arr as $key=>$val)
		{
			unset($arr[$key]);
			$new_key = $j;
			$arr[$new_key] = $val;
			$j++;
		}

		return $arr;
	}


	function mssql_escape($data) {
		if(is_numeric($data)){
			return $data;
		}

		$unpacked = unpack('H*hex', $data);
		return '0x' . $unpacked['hex'];
	}


	//파일 용량 체크
	function filesize_check($size) {
		if(!$size) return "0 Byte";
		if($size < 1024) {
			return "$size Byte";
		} elseif($size >= 1024 && $size < 1024 * 1024) {
			return sprintf("%0.1f",$size / 1024)." KB";
		} elseif($size >= 1024 * 1024 && $size < 1024 * 1024 * 1024) {
			return sprintf("%0.1f",$size / 1024 / 1024)." MB";
		} else {
			return sprintf("%0.1f",$size / 1024 / 1024 / 1024)." GB";
		}
	}


	//프로필 사진, 프로필 케릭터사진
	function profile_img_info($user_id){
		global $companyno;
		//global $conn;
		//프로필 캐릭터 사진
		$sql = "select idx, file_path, file_name from work_member_character_img where state='0' order by idx asc";
		$character_img_info = selectAllQuery($sql);
		if($character_img_info['idx']){
			for($i=0; $i<count($character_img_info['idx']); $i++){
				$file_path = $character_img_info['file_path'][$i];
				$file_name = $character_img_info['file_name'][$i];
				$profile_character_info[$character_img_info['idx'][$i]] = $file_path.$file_name;
			}
		}

		//프로필 사진
		$sql = "select idx, file_path, file_name from work_member_profile_img where state='0' and companyno='".$companyno."' order by idx asc";
		$profile_img_list = selectAllQuery($sql);
		if($profile_img_list['idx']){
			for($i=0; $i<count($profile_img_list['idx']); $i++){
				$file_path = $profile_img_list['file_path'][$i];
				$file_name = $profile_img_list['file_name'][$i];
				$profile_img_list_info[$profile_img_list['idx'][$i]] = $file_path.$file_name;
			}
		}


		//회원정보에서 데이터 추출
		$sql = "select idx, profile_type, profile_img_idx from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$member_info = selectQuery($sql);
		if($member_info['idx']){
			$profile_type = $member_info['profile_type'];
			$profile_img_idx = $member_info['profile_img_idx'];

			//프로필 정보가 없는경우
			if($profile_img_idx == null){
				$profile_img_idx = 5;
			}

			//케릭터 선택
			if($profile_type == '0'){
				$profile_main_img_src = $profile_character_info[$profile_img_idx];

			//프로필 사진 선택
			}else if($profile_type == '1'){
				$profile_main_img_src = $profile_img_list_info[$profile_img_idx];
			}

			if($profile_main_img_src){
				return $profile_main_img_src;
			}
		}
	}


	//회원정보 - 아이디
	function member_row_info($user_id){
		global $companyno;

		if($user_id){
			//회원정보
			$sql = "select idx, company, companyno, email, name, part, partno, highlevel, coin, comcoin, profile_type, profile_img_idx, live_1, live_2, live_3, live_4, convert(char(5), live_1_regdate, 8) as live_1_time, convert(char(5), live_4_regdate, 8) as live_4_time from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
			$member_info = selectQuery($sql);
			if($member_info['idx']){

				if($member_info['live_1_time']){
					$tmp_live_1_time = explode(":", $member_info['live_1_time']);
					if($tmp_live_1_time){
						$member_info['live_1_time'] = (int)$tmp_live_1_time[0].":" .$tmp_live_1_time[1] ."";
					}
				}

				$live_4_time = $member_info['live_4_time'];
				if($member_info['live_4_time']){
					$tmp_live_4_time = explode(":", $member_info['live_4_time']);
					if($tmp_live_4_time){
						$member_info['live_4_time'] = (int)$tmp_live_4_time[0].":" .$tmp_live_4_time[1] ."";
					}
				}

				//코인
				$member_info['coin'] = number_format($member_info['coin']);

				//프로필 사진
				$member_info['profile_img_src'] = profile_img_info($member_info['email']);
			}else{
				$member_info['coin'] = 0;
			}

			return $member_info;
		}
	}


	//회원정보 - idx번호
	function member_rowidx_info($idx){
		global $companyno;

		if($idx){
			//회원정보
			$sql = "select idx, company, companyno, email, name, part, partno, highlevel, coin, profile_type, profile_img_idx, live_1, live_2, live_3, live_4, convert(char(5), live_1_regdate, 8) as live_1_time, convert(char(5), live_4_regdate, 8) as live_4_time from work_member where state='0' and companyno='".$companyno."' and idx='".$idx."'";
			$member_info = selectQuery($sql);
			if($member_info['idx']){

				if($member_info['live_1_time']){
					$tmp_live_1_time = explode(":", $member_info['live_1_time']);
					if($tmp_live_1_time){
						$member_info['live_1_time'] = (int)$tmp_live_1_time[0].":" .$tmp_live_1_time[1] ."";
					}
				}

				$live_4_time = $member_info['live_4_time'];
				if($member_info['live_4_time']){
					$tmp_live_4_time = explode(":", $member_info['live_4_time']);
					if($tmp_live_4_time){
						$member_info['live_4_time'] = (int)$tmp_live_4_time[0].":" .$tmp_live_4_time[1] ."";
					}
				}

				//코인
				$member_info['coin'] = number_format($member_info['coin']);

				//프로필 사진
				$member_info['profile_img_src'] = profile_img_info($member_info['email']);
			}else{
				$member_info['coin'] = 0;
			}

			return $member_info;
		}
	}


	//회원정보 리스트(일반권한)
	function member_list_info(){
		global $companyno;

		//일반회원
		$highlevel = '5';
		$sql = "select idx, email, name, part, partno, highlevel from work_member where state='0' and highlevel='".$highlevel."' and companyno='".$companyno."'";
		$mem_info = selectAllQuery($sql);
		return $mem_info;
	}

	//회원정보 리스트(관리권한 제외)
	function member_alist_info(){
		global $companyno;
		$highlevel = '1';
		$sql = "select idx, companyno, email, name, part, partno, highlevel from work_member where state='0' and highlevel!='".$highlevel."' group by  idx, companyno, email, name, part, partno, highlevel order by idx asc";
		$mem_info = selectAllQuery($sql);
		return $mem_info;
	}

	//회원정보 리스트(회사별전체)
	function member_clist_info(){
		global $companyno;
		$highlevel = '1';
		$sql = "select idx, email, name, part, partno, highlevel from work_member where state='0' and companyno='".$companyno."' order by idx asc";
		$mem_info = selectAllQuery($sql);
		return $mem_info;
	}


	//챌린지카테고리
	function challenges_category(){
		global $user_id;

		$sql = "select idx, act, name from work_category where state='0' order by rank asc";
		$cate_info = selectAllQuery($sql);
		//for($i=0; $i<count($cate_info['idx']); $i++){
		//	$chall_category[$cate_info['idx'][$i]] = $cate_info['name'][$i];
		//}


		$chall_category['category'] = @array_combine($cate_info['idx'], $cate_info['name']);
		$chall_category['act'] = @array_combine($cate_info['idx'], $cate_info['act']);
		$chall_category['idx'] = @array_combine($cate_info['act'], $cate_info['idx']);
		return $chall_category;
	}


	//로그인 로그 저장
	function member_login_log(){
		global $user_id, $user_name, $companyno, $highlevel, $chkMobile;

		//구분(0:사이트, 1:모바일)
		$type_flag = ($chkMobile)?1:0;

		//관리자권한은 제외
		//하루 한번만 저장
		if($highlevel !='1'){
			$sql = "select idx from work_member_login where state='0' and email='".$user_id."' and companyno='".$companyno."' and convert(char(10), regdate, 120)='".TODATE."'";
			$mem_info = selectQuery($sql);

			//오늘업무 체크
			member_todaywork_over();

			//퇴근체크
			member_logoff_over();


			//현재시간
			$atime = date("Y-m-d H:i:s" , time());
			$att_year = date("Y", TODAYTIME);
			$att_month = date("m", TODAYTIME);
			$att_day = date("d", TODAYTIME);

			//출근20분전
			$atime_att_pre = mktime('08', '40', '00', $att_month, $att_day, $att_year);
			//$atime_att = date("Y-m-d H:i:s", $atime_att_pre);

			//출근 09:00분전
			$atime_att_time = mktime('09', '00', '00', $att_month, $att_day, $att_year);
			//$atime_att = date("Y-m-d H:i:s", $atime_att_time);

			//현재시간
			$ATTEND_DATE = date("Y-m-d H:i:s", TODAYTIME);
			$attime = strtotime($ATTEND_DATE);

			if(!$mem_info['idx']){
				//아이디, 이름, 회사번호, 사이트(0)/모바일(1), 등록날짜(년-월-일), 등록시간(시:분), 아이피
				$sql = "insert into work_member_login(email, name, companyno, type_flag, workdate, worktime, ip) values('".$user_id."','".$user_name."', '".$companyno."', '".$type_flag."', '".TODATE."', convert(char(5), getdate(), 8), '".LIP."')";
				$insert_idx = insertIdxQuery($sql);

				//출근시간 <= 출근시간 20분전
				if($attime <= $atime_att_pre){
					//역량지표(출근시간 20분 이전)
					work_cp_reward("main", "0002", $user_id, $insert_idx);
				}

				//출근시간 <= 정시시간 09:00분전
				if($attime <= $atime_att_time){
					//역량지표(출근시간 09:00분 이전)
					work_cp_reward("main", "0003", $user_id, $insert_idx);
				}

				//타임라인(출근)
				work_data_log('0','1', $insert_idx, $user_id, $user_name);

				//출근시간 이후에 접속한 로그저장
				member_login_over();

				//출근1등 체크
				work_cp_reward_plus("cp", "0005", $insert_idx, "", "login");

			}
		}
	}


	//출근시간 이후에 접속한 로그저장
	function member_login_over(){
		global $user_id, $user_name, $companyno, $chkMobile;

		//구분(0:사이트, 1:모바일)
		$type_flag = ($chkMobile)?1:0;

		//지각한 로그기록
		$member_log_info = member_login_log_chk();
		if($member_log_info){
			$extmp = @explode(":" , $member_log_info['regtime']);
			if($extmp){

				//접속한 시간
				$ex_hours = $extmp[0];

				//접속한 분
				$ex_minutes = $extmp[1];

				//출근시간(09:00) 이후 접속한 내역 저장
				if($ex_hours >= ATTEND_STIME && $ex_minutes >= '01'){
					/*$sql = "select idx from work_member_login_after where state='0' and email='".$user_id."' and convert(char(10), regdate, 120) = '".TODATE."'";
					$after_info = selectQuery($sql);
					if(!$after_info['idx']){
						$sql = "insert into work_member_login_after(email, name, companyno, type_flag, ip) values('".$user_id."','".$user_name."','".$companyno."','".$type_flag."','".LIP."')";
						insertQuery($sql);
						//insertIdxQuery($sql);
					}*/

					//지각 페널티 저장: 페널티정보, 지각(0)
					$penalty_log_info = penalty_log_save($member_log_info, 0);
				}
			}
		}
	}

	function dateTimeViewer($time){
		$timeLater = time() - $time;
		//if($timeLater < 60) {
		//	return "방금 전";
		//}else
		if($timeLater < 60*60) {
			return floor($timeLater / 20)."분 전";
		}
		//else if($timeLater < 60*60*24) return floor($timeLater / (60*60))."시간 전";
		//else if($timeLater < 60*60*24*30) return floor($timeLater / (60*60*24))."일 전";
		//else return floor($timeLater / (60*60*24*30))."달 전";
	}



	//오늘 업무 내역 체크
	function member_todaywork_over(){
		global $user_id, $user_name;
		$member_todaywork_info = todaywork_chk();

		if($member_todaywork_info){
			//오늘업무 페널티 저장: 페널티정보, 오늘업무(1)
			$penalty_log_info = penalty_log_save($member_todaywork_info, 1);
		}
	}

	//퇴근 시간 내역 체크
	function member_logoff_over(){
		global $user_id, $user_name;

		$member_logoff_info = logoff_chk();
		if($member_logoff_info){
			//퇴근 페널티 저장: 페널티정보, 지각(2)
			$penalty_log_info = penalty_log_save($member_logoff_info, 2);
		}
	}


	//로그인 로그 확인
	function member_login_log_chk(){
		global $companyno ,$user_id;

		$result = array();

		//회원 로그인 기록 확인
		$sql = "select idx, email, name, workdate, worktime as regtime,convert(varchar(20), regdate, 120) as reg from work_member_login where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
		$mem_login_info = selectQuery($sql);

		//일정체크
		$decide = todaywork_decide_chk();
		if($mem_login_info['idx']){
			$idx = $mem_login_info['idx'];
			$email = $mem_login_info['email'];
			$name = $mem_login_info['name'];
			$wdate = $mem_login_info['workdate'];
			$regtime = $mem_login_info['regtime'];
			$reg = $mem_login_info['reg'];

			//일정이 있을경우 예외처리
			if($wdate == $decide[$email]){
				//데이터 비우기
				unset($result);
			}else{
				$result['idx'] = $idx;
				$result['id'] = $email;
				$result['name'] = $name;
				$result['wdate'] = $wdate;
				$result['regtime'] = $regtime;
				$result['reg'] = $reg;
			}
			return $result;
		}
	}


	//퇴근 로그 저장
	function member_logoff_log(){
		global $user_id, $user_name, $companyno, $highlevel, $chkMobile;

		//구분(0:사이트, 1:모바일)
		$type_flag = ($chkMobile)?1:0;

		//관리자권한은 제외
		if($highlevel !='1'){
			$sql = "select idx from work_member_logoff where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
			$mem_info = selectQuery($sql);
			if(!$mem_info['idx']){

				//아이디, 이름, 회사번호, 아이피
				$sql = "insert into work_member_logoff(state, email, name, companyno, type_flag, workdate, ip) values('0','".$user_id."','".$user_name."','".$companyno."','".$type_flag."','".TODATE."','".LIP."')";
				$insert_idx = insertIdxQuery($sql);

				//타임라인(퇴근)
				work_data_log('0','9', $insert_idx, $user_id, $user_name);

				//역량지표(퇴근 체크)
				work_cp_reward("main", "0007", $user_id, $insert_idx);


				//출근기록
				$member_login_time = member_login_log_chk();
				if($member_login_time){

					$ex_time = explode(":", $member_login_time['regtime']);
					if($ex_time){
						$ex_hours = (int)$ex_time[0];
						$ex_minutes = (int)$ex_time[1];
						//$login_time = $time_s .":". $time_e;
					}

					//정시 출근 체크(근태기록)
					if($ex_hours <= ATTEND_STIME && $ex_minutes <= '00'){
						work_cp_reward("work", "0009", $user_id, $insert_idx);
					}
				}
			}
		}
	}


	//지각 횟수(월~금) 체크
	function member_attend_list(){
		global $user_id, $companyno;

		//한주(월~금)
		//현재 날짜기준으로 한 주 동안 정시 출근시간(09:00) 2회이상 체크
		//연차:1, 반차:2, 출장:5 제외처리
		$sql = "select count(1) as cnt, email";
		$sql = $sql .= " from work_member_login where state='0' and convert(char(10), regdate, 120) >= CONVERT(VARCHAR(10), CONVERT(DATETIME, '".TODATE."')-DATEPART(DW, '".TODATE."')+2, 121)";
		$sql = $sql .= " and convert(char(10), regdate, 120) <= CONVERT(VARCHAR(10), CONVERT(DATETIME, '".TODATE."')-DATEPART(DW, '".TODATE."')+6, 121)";
		$sql = $sql .= " and companyno='".$companyno."' and convert(char(5), regdate, 8) > '".ATTEND_TIME."'";
		$sql = $sql .= " and email not in(select email from work_todaywork where state='0' and workdate='".TODATE."' and decide_flag in ('1','2','5'))";
		$sql = $sql .= " group by email";
		$login_attend_info = selectAllQuery($sql);
		$attend_member = array();
		for($i=0; $i<count($login_attend_info['email']); $i++){
			$mem_id = $login_attend_info['email'][$i];
			$mem_cnt = $login_attend_info['cnt'][$i];
			$attend_member['cnt'][$mem_id] = $mem_cnt;
		}


		//현재 날짜기준으로 한 주 동안 지각한 날짜 가져오기
		$sql = "select email , convert(char(10), regdate, 120) as wdate";
		$sql = $sql .= " from work_member_login where state='0' and convert(char(10), regdate, 120) >= CONVERT(VARCHAR(10), CONVERT(DATETIME, '".TODATE."')-DATEPART(DW, '".TODATE."')+2, 121)";
		$sql = $sql .= " and convert(char(10), regdate, 120) <= CONVERT(VARCHAR(10), CONVERT(DATETIME, '".TODATE."')-DATEPART(DW, '".TODATE."')+6, 121)";
		$sql = $sql .= " and companyno='".$companyno."' and convert(char(5), regdate, 8) > '".ATTEND_TIME."'";
		$sql = $sql .= " and email not in(select email from work_todaywork where state='0' and workdate='".TODATE."' and decide_flag in ('1','2','5'))";
		$login_wdate_info = selectAllQuery($sql);
		for($i=0; $i<count($login_wdate_info['email']); $i++){
			$mem_id = $login_wdate_info['email'][$i];
			$mem_wdate = $login_wdate_info['wdate'][$i];
			$attend_member['date'][$mem_id][] = $mem_wdate;
		}

		return $attend_member;
	}


	//업무횟수 (월~금) 체크
	function member_work_list(){
		global $user_id, $companyno, $user_level;

		//주별 확인(월 ~ 금)
		$wdate = week_day(TODATE);

		//어제날짜
		$ywdate = strtotime(TODATE);
		$ystdate = date("Y-m-d",strtotime("-1 day", $ywdate));
		//$user_id = "fpqldhtk3@nate.com";

		if($user_id && $wdate){
			$sql = "select * from (select a.workdate as workdate, count(c.idx) as cnt";
			$sql = $sql .= " from (select workdate from work_todaywork where workdate between '".$wdate['month']."' and '".$wdate['friday']."' group by workdate) a";
			$sql = $sql .= " left outer join work_todaywork c on (c.workdate=a.workdate and work_idx is null and decide_flag not in ('1','2','5') and email='".$user_id."')";
			$sql = $sql .= " where a.workdate BETWEEN '".$wdate['month']."' and '".$ystdate."'";
			$sql = $sql .= " group by a.workdate) c";
			$work_info = selectAllQuery($sql);
			$list_cnt = 0;
			for($i=0; $i<count($work_info['workdate']); $i++){
				$workdate =$work_info['workdate'][$i];
				$cnt = $work_info['cnt'][$i];
				$member_work_list['wdate'][$workdate] = $cnt;
				if($cnt == 0){
					$list_cnt++;
				}
			}

			//관리 권한 제외
			if($user_level!='1'){
				$member_work_list['cnt'] = $list_cnt;
			}
		}
		return $member_work_list;
	}


	//페널티 완료 내역 조회
	function penalty_complete($v, $id="" , $date=""){
		global $user_id, $companyno;

		$wdate = TODATE;

		//아이디가 있는경우
		if($id){
			$uid = $id;
		}else{
			$uid = $user_id;
		}

		//날짜가 있는경우
		if($date){
			$wdate = $date;
		}

		$sql = "select top 1 idx, file_ori_path, file_ori_name from work_filesinfo_img_penalty where state='0' and companyno='".$companyno."' and kind='".$v."' and email='".$uid."' and workdate='".$wdate."'";
		$penalty_info = selectQuery($sql);
		if($penalty_info['idx']){
			$penalty_info_file_ori_path = $penalty_info['file_ori_path'];
			$penalty_info_file_ori_name = $penalty_info['file_ori_name'];
			$penalty_img_src = $penalty_info_file_ori_path . $penalty_info_file_ori_name;
		}
		return $penalty_img_src;
	}

	//오늘업무 횟수
	function member_work_list_2(){
		//삭제되지 않은 업무:state!=9, 일반업무:work_flag=2
		//일정 연차:1, 반차:2, 출장:5 제외처리
		//전날

		//주별 확인(월 ~ 금)
		$wdate = week_day(TODATE);

		$ywdate = strtotime(TODATE);
		$ystdate = date("Y-m-d",strtotime("-1 day", $ywdate));

		$sql = "select * from(select email, a.workdate as workdate, count(c.idx) as cnt";
		$sql = $sql .= " from (select workdate from work_todaywork where workdate between '".$wdate['month']."' and '".$wdate['friday']."' group by workdate) a";
		$sql = $sql .= " left outer join work_todaywork c on (c.workdate=a.workdate and state!='9' and work_flag='2' and decide_flag not in ('1','2','5'))";
		$sql = $sql .= " where a.workdate BETWEEN '".$wdate['month']."' and '".$ystdate."'";
		$sql = $sql .= " group by a.workdate, email ) c";
		$work_info = selectAllQuery($sql);
		$member_work_member = array();
		for($i=0; $i<count($work_info['email']); $i++){
			$mem_id = $work_info['email'][$i];
			$mem_cnt = $work_info['cnt'][$i];
			$mem_date = $work_info['workdate'][$i];

			$member_work_member['cnt'][$mem_date][$mem_id] = $mem_cnt;
			$member_work_member['date'][] = $mem_date;

			//$member_work_member[$mem_id][$mem_date] = $mem_cnt;
			//$member_work_member['date'][$mem_id][] = $mem_date;
		}

		$member_work_member['date'] = @array_unique($member_work_member['date']);
		$member_work_member['date'] = @array_key_reset($member_work_member['date']);

		return $member_work_member;
	}




	//페널티 체크 : login
	function member_login_info_check(){
		//global $user_id, $user_name, $companyno, $highlevel, $type_flag;

		//관리자 권한 조회
		//$sql = "select idx, email, name, part, partno, companyno from work_member where state='0' and highlevel='0'";
		//$mem_auth_info = selectAllQuery($sql);

		//어제날짜
		//TODATE -1;
		//$ystday = date('Y-m-d', strtotime('-1 day'));

		//업무내역
		$wdate = week_day(TODATE);
		$monthday = $wdate['month'];
		$sunday = $wdate['sunday'];

		//$sql = "select count(1) , email from work_todaywork where state!='9' and notice_flag='0' and workdate between '".$monthday."' and '".$sunday."' group by email";
		//$work_info = selectAllQuery($sql);

		//$penalty_info = work_penalty_info();

		/*print "<pre>";
		print_r($penalty_info);
		print "</pre>";*/
		return $penalty_info;
	}

	//패널티 알림등록
	function penalty_info_notice(){
		global $chkMobile;

		//구분(0:사이트, 1:모바일)
		$type_flag = ($chkMobile)?1:0;

		//페널티 내역
		$penalty_info = work_penalty_info();

		/*
		print "<pre>";
		print_r($penalty_info);
		print "</pre>";
		*/

		//관리자 권한 조회
		//$sql = "select idx, email, name, part, partno, companyno from work_member where state='0' and highlevel='0'";
		$sql = "select idx, email, name, part, partno, companyno from work_member where state='0' and idx=47";
		$mem_auth_info = selectAllQuery($sql);

		//지각 알림
		$notice_flag = '2';
		for($i=0; $i<count($penalty_info['attend']['idx']); $i++){

			$idx = $penalty_info['attend']['idx'][$i];
			$id = $penalty_info['attend']['id'][$i];
			$name = $penalty_info['attend']['name'][$i];
			$cnt = $penalty_info['attend']['cnt'][$i];
			$wdate = $penalty_info['attend']['wdate'][$i];


			//지각 1회 이상일때 관리자에서 알림 전송
			if($cnt > 1){
				$contents = $name ."님이 지각페널티 카드를 완료하지 않았습니다.";
				for($j=0; $j<count($mem_auth_info['idx']); $j++){

					$mem_manager_id = $mem_auth_info['email'][$j];
					$mem_manager_name = $mem_auth_info['name'][$j];
					$mem_manager_part = $mem_auth_info['part'][$j];
					$mem_manager_partno = $mem_auth_info['partno'][$j];

					//날짜 차이
					$date_diff = (strtotime(TODATE) - strtotime($wdate)) / 86400;

					//echo $name."(". $wdate .") " . $date_diff;
					//echo "\n";
					if ($date_diff <= 1){
						$sql = "select idx from work_todaywork where work_idx='".$idx."' and notice_flag='".$notice_flag."' and workdate='".TODATE."' and email='".$mem_manager_id."'";
						$info = selectQuery($sql);

						if (!$info['idx']){
							$sql = "insert into work_todaywork(highlevel, work_flag, part_flag, type_flag, notice_flag, work_idx, email, name, part, contents, ip, workdate) values(";
							$sql = $sql .= "'0', '2', '".$mem_manager_partno."', '".$type_flag."', '".$notice_flag."', '".$idx."','".$mem_manager_id."', '".$mem_manager_name."', '".$mem_manager_part."','".$contents."', '".LIP."','".TODATE."')";
							$insert_idx = insertQuery($sql);
						}
					}
				}
			}
		}


		//오늘업무 알림
		/*$notice_flag = '3';
		for($i=0; $i<count($penalty_info['work']['wdate']); $i++){
			$workdate = $penalty_info['work']['wdate'][$i];
			$workcnt = $penalty_info['work']['cnt'][$i];

			if($workcnt > 1){
				for($j=0; $j<count($mem_auth_info['idx']); $j++){

				}
			}
		}*/


		if($insert_idx){
			echo "complete";
			exit;
		}
	}


	//정시 출근시간(09:00) 2회이상 일경우
	function member_coaching_chk(){
		global $user_id;

		//현재 날짜기준으로 한 주 동안 정시 출근시간(09:00) 2회이상 체크
		//지각 횟수 리턴
		$sql = "select count(1) as cnt ";
		$sql = $sql .= " from work_member_login where state='0' and convert(char(10), regdate, 120) >= CONVERT(VARCHAR(10), CONVERT(DATETIME, '".TODATE."')-DATEPART(DW, '".TODATE."')+2, 121)";
		$sql = $sql .= " and convert(char(10), regdate, 120) <= CONVERT(VARCHAR(10), CONVERT(DATETIME, '".TODATE."')-DATEPART(DW, '".TODATE."')+6, 121)";
		$sql = $sql .= " and convert(char(5), regdate, 8) > '".ATTEND_TIME."' and email='".$user_id."'";
		$login_attend_info = selectQuery($sql);
		if($login_attend_info['cnt'] >= ATTEND_CNT){
			return $login_attend_info['cnt'];
		}
	}



	//지각 페널티 체크
	function attend_penalty_info(){

		//아이디, 회원권한
		global $user_id, $user_level;;

		//회원 전체목록
		$member_list_info = member_list_info();

		//일반회원 지각 횟수(월~금) 체크
		$member_attend_list = member_attend_list();

		/*print "<pre>";
		print_r($member_attend_list);
		print "</pre>";*/

		//주별 확인(월 ~ 금)
		$wdate = week_day(TODATE);

		//지각 페널티
		$kind = '0';
		//지각한 회원 내역 중에
		//지각 페널티 카드 작성 안한 회원
		//지각 kind=2
		if($wdate){
			$sql = "select idx, email, CONVERT(varchar(10), regdate, 120) as wdate from work_filesinfo_img_penalty where state='0' and companyno='".$companyno."' and kind='".$kind."'";
			$sql = $sql .=" and CONVERT(varchar(10), regdate , 120) between '".$wdate['month']."' and '".$wdate['friday']."'";
			$pl_info = selectAllQuery($sql);
			$pl_list = @array_combine($pl_info['email'], $pl_info['wdate']);
			$result = array();
			for($i=0; $i<count($member_list_info['idx']); $i++){
				$mem_idx = $member_list_info['idx'][$i];
				$mem_id = $member_list_info['email'][$i];
				$mem_name = $member_list_info['name'][$i];

				//아이디별 지각 횟수
				if ($member_attend_list['date'][$mem_id]){

					//갯수
					$cnt = count($member_attend_list['date'][$mem_id]);
					for($j=0; $j<$cnt; $j++){
						$pl_date = $member_attend_list['date'][$mem_id][$j];

						//페널티 작성 내역 없는 회원
						if ($pl_list[$mem_id] != $pl_date){
							$result['idx'][] = $mem_idx;
							$result['id'][] = $mem_id;
							$result['name'][] = $mem_name;
							$result['cnt'][] = $member_attend_list['cnt'][$mem_id];
							$result['wdate'][] = $pl_date;
						}
					}
				}
			}
		}

		//관리권한 제외
		if($user_level!='1'){
			return $result;
		}
	}



	//퇴근 페널티 카드
	function member_quit_list(){

		//아이디, 회사구분코드, 회원권한
		global $user_id, $companyno, $user_level;

		//주별 확인(월 ~ 금)
		$wdate = week_day(TODATE);

		//어제날짜
		$ywdate = strtotime(TODATE);
		$ystdate = date("Y-m-d",strtotime("-1 day", $ywdate));

		if($user_id && $wdate){
			$sql = "select * from (select  a.workdate as workdate, count(c.idx) as cnt from ";
			$sql = $sql .= " (select workdate from work_todaywork where workdate between '".$wdate['month']."' and '".$wdate['friday']."' group by workdate) a";
			$sql = $sql .= " left outer join work_member_logoff c on ( convert(char(10), c.regdate, 120)=a.workdate and email='".$user_id."')";
			$sql = $sql .= " where a.workdate BETWEEN '".$wdate['month']."' and '".$ystdate."'";
			$sql = $sql .= " group by a.workdate ) c";
			$list_info = selectAllQuery($sql);
			$list_cnt = 0;
			for($i=0; $i<count($list_info['workdate']); $i++){
				$workdate = $list_info['workdate'][$i];
				$cnt = $list_info['cnt'][$i];

				$member_quit_list['wdate'][$workdate] = $cnt;
				if($cnt == 0){
					$list_cnt++;
				}
			}

			//관리 권한 제외
			if($user_level!='1'){
				$member_quit_list['cnt'] = $list_cnt;
			}

			return $member_quit_list;
		}
	}





	//페널티 내역 체크
	function work_penalty_info(){
		global $user_id;

		//회원 전체목록
		$member_list_info = member_list_info();

		//일반회원 전체 중 지각 횟수(월~금) 체크
		$member_attend_list = member_attend_list();


/*		print "<pre>";
		print_r($member_attend_list);
		print "</pre>";
*/
		//주별 확인(월 ~ 금)
		$wdate = week_day(TODATE);

		//지각한 회원 내역 중에
		//지각 페널티 카드 작성 안한 회원
		//업무일지 kind=1
		$sql = "select idx, email, CONVERT(varchar(10), regdate, 120) as wdate from work_filesinfo_img_penalty where state='0' and companyno='".$companyno."' and kind='1'";
		$sql = $sql .=" and CONVERT(varchar(10), regdate , 120) between '".$wdate['month']."' and '".$wdate['friday']."'";
		$pl_info = selectAllQuery($sql);
		$pl_list = @array_combine($pl_info['email'], $pl_info['wdate']);
		$result = array();
		for($i=0; $i<count($member_list_info['idx']); $i++){
			$mem_idx = $member_list_info['idx'][$i];
			$mem_id = $member_list_info['email'][$i];
			$mem_name = $member_list_info['name'][$i];

			//아이디별 지각 횟수
			if ($member_attend_list['date'][$mem_id]){

				//갯수
				$cnt = count($member_attend_list['date'][$mem_id]);
				for($j=0; $j<$cnt; $j++){
					$pl_date = $member_attend_list['date'][$mem_id][$j];

					//페널티 작성 내역 없는 회원
					if ($pl_list[$mem_id] != $pl_date){
						$result['attend']['idx'][] = $mem_idx;
						$result['attend']['id'][] = $mem_id;
						$result['attend']['name'][] = $mem_name;
						$result['attend']['cnt'][] = $member_attend_list['cnt'][$mem_id];
						$result['attend']['wdate'][] = $pl_date;
					}
				}
			}
		}



		//$member_work_list = member_work_list();

		/*print "<pre>";
		print_r($member_work_list);
		print "</pre>";
		*/

		$ywdate = strtotime(TODATE);
		$ystdate = date("Y-m-d",strtotime("-1 day", $ywdate));

		//echo $date_diff = (strtotime($ystdate) - strtotime($wdate['month'])) / 86400;


		for($i=0; $i<count($member_list_info['idx']); $i++){

			$mem_idx = $member_list_info['idx'][$i];
			$mem_id = $member_list_info['email'][$i];
			$mem_name = $member_list_info['name'][$i];

			for($j=0; $j<count($member_work_list['date']); $j++){

				$wdate = $member_work_list['date'][$j];
				$cnt = $member_work_list['cnt'][$wdate][$mem_id];
				if(!$cnt){
					//echo $mem_id ."\t". $wdate ."\t". $cnt;
					//echo "\n";
					$work_mem_list['work'][$wdate][] = $mem_id;
					$work_mem_list['date'][$mem_id][] = $wdate;
				}

			}
		}


/*		print "<pre>";
		print_r($work_mem_list);
		print "</pre>";
*/


		//오늘업무 내역 중에
		//오늘업무 페널티 카드 작성 안한 회원
		//오늘업무 kind=1
		$sql = "select idx, email, CONVERT(varchar(10), regdate, 120) as wdate from work_filesinfo_img_penalty where state='0' and companyno='".$companyno."' and kind='1'";
		$sql = $sql .=" and CONVERT(varchar(10), regdate , 120) between '".$wdate['month']."' and '".$wdate['friday']."'";
		$pl_info = selectAllQuery($sql);
		$pl_list = @array_combine($pl_info['email'], $pl_info['wdate']);


		for($i=0; $i<count($member_work_list['date']); $i++){
			$wdate = $member_work_list['date'][$i];


			for($j=0; $j<count($work_mem_list['work']); $j++){
				$mem_id = $work_mem_list['work'][$wdate][$j];

				$pl_date = $work_mem_list['date'][$mem_id][$j];

				if ($pl_list[$mem_id] != $pl_date){

				//	echo $mem_id;
				//	echo "\n";
				}

			}
		}

		for($i=0; $i<count($work_info['workdate']); $i++){
			$workdate = $work_info['workdate'][$i];
			$workemail = $work_info['email'][$i];
			$workcnt = $work_info['cnt'][$i];

			//아이디별 오늘업무 횟수
			if ($workcnt == 0){
				//페널티 작성 내역 없는 회원
				if ($pl_list[$workemail] != $workdate){
					$result['work']['wdate'][] = $workdate;
					$result['work']['id'][] = $workemail;
					$result['work']['cnt'][] = $workcnt;
				}
			}
		}
		return $result;
	}



	//페널티 로그 저장
	function penalty_log_save($data, $kind){
		global $companyno ,$user_id, $user_name;

		//지각:kind=0, 오늘업무:kind=1, 퇴근:kind=2
		if($kind=='0'){
			$sql = "select idx from work_penalty_list_log where state='0' and kind_flag='".$kind."' and companyno='".$companyno."' and email='".$data['id']."' and workdate='".TODATE."'";
			$pl_info = selectQuery($sql);
			if(!$pl_info['idx']){
				$sql = "insert into work_penalty_list_log(email, name, companyno, kind_flag, attend_time, login_idx, workdate, ip)";
				$sql = $sql .= " values('".$data['id']."','".$data['name']."','".$companyno."','".$kind."','".$data['regtime']."','".$data['idx']."','".TODATE."', '".LIP."')";
				$insert_idx = insertIdxQuery($sql);
			}
		}else{

			//오늘업무, 퇴근
			$sql = "select idx from work_penalty_list_log where state='0' and kind_flag='".$kind."' and companyno='".$companyno."' and email='".$data['id']."' and workdate='".$data['wdate']."'";
			$pl_info = selectQuery($sql);
			if(!$pl_info['idx']){
				$sql = "insert into work_penalty_list_log(email, name, companyno, kind_flag, workdate, ip)";
				$sql = $sql .= " values('".$data['id']."','".$data['name']."','".$companyno."','".$kind."','".$data['wdate']."','".LIP."')";
				$insert_idx = insertIdxQuery($sql);
			}
		}

		return $insert_idx;
	}


	//페널티 정보 조회
	//월~금 페널티 횟수
	function penalty_info_check($kind , $id=""){
		global $companyno ,$user_id, $user_name;
		$week_day = week_day(TODATE);
		//지각:kind=0
		//오늘업무: kind=1, 퇴근:kind=2

		if($id){
		//	$user_id = $id;
		}

		//echo "id :: ". $id;

		//지각페널티
		if($kind=='0'){

			//지각페널티 내역에서 오늘날짜 조회
			$sql = "select idx, email, name from work_penalty_list_log where state='0' and kind_flag='".$kind."' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
			$pl_info = selectAllQuery($sql);

			//한주동안 페널티 카운터수
			$sql = "select count(1) as cnt from work_penalty_list_log where state='0' and kind_flag='".$kind."' and companyno='".$companyno."' and email='".$user_id."' and workdate between '".$week_day['month']."' and '".$week_day['friday']."'";
			$penalty_info = selectQuery($sql);

			if($penalty_info['cnt']){
				$penalty_cnt = $penalty_info['cnt'];
			}else{
				$penalty_cnt = 1;
			}

			if($pl_info['idx']){
				$sql = "select idx from work_penalty_list where state='0' and kind_flag='".$kind."' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
				$info = selectQuery($sql);

				if(!$info['idx']){
					$sql = "insert into work_penalty_list(email, name, companyno, kind_flag, penalty_cnt, workdate, closedate, ip)";
					$sql = $sql .=" values('".$user_id."','".$user_name."','".$companyno."','".$kind."','".$penalty_cnt."','".TODATE."',".DBDATE.",'".LIP."')";
					$insert_idx = insertIdxQuery($sql);
				}

				return $pl_info;
			}

		}else{

			$sql = "select idx, email, name from work_penalty_list_log where state='0' and kind_flag='".$kind."' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
			$pl_info = selectAllQuery($sql);
			if($pl_info['idx']){
				$sql = "select idx from work_penalty_list where state='0' and kind_flag='".$kind."' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
				$info = selectQuery($sql);
				if(!$info['idx']){
					$sql = "insert into work_penalty_list(email, name, companyno, kind_flag, penalty_cnt, workdate, closedate, ip)";
					$sql = $sql .=" values('".$user_id."','".$user_name."','".$companyno."','".$kind."','".$penalty_cnt."','".TODATE."',".DBDATE.",'".LIP."')";
					$insert_idx = insertIdxQuery($sql);
				}
				return $pl_info;
			}
		}
	}


	//페널티 정보 아이디, 날짜 조회
	//월~금 페널티 횟수
	function penalty_info_check_date($kind , $id="", $date=""){
		global $companyno ,$user_id, $user_name;
		//지각:kind=0
		//오늘업무: kind=1, 퇴근:kind=2

		if($id){
			$id = $id;
		}else{
			$id = $user_id;
		}

		if($date){
			$wdate = $date;
		}

		$sql = "select idx, email, name, penalty_cnt from work_penalty_list where state='0' and kind_flag='".$kind."' and companyno='".$companyno."' and email='".$id."' and workdate='".$wdate."'";
		$pl_info = selectQuery($sql);
		if($pl_info){
			return $pl_info;
		}
	}


	function penalty_input(){


	}


	//페널티 정보 조회
	//월~금 페널티 횟수
	function penalty_info_all_check($kind){
		global $companyno ,$user_id, $user_name;
		$wdate = week_day(TODATE);
		//지각:kind=0
		//오늘업무: kind=1, 퇴근:kind=2
		if($kind=='0'){
			$sql = "select idx, email, name from work_penalty_list_log where state='0' and kind_flag='".$kind."' and companyno='".$companyno."' and convert(char(10), regdate, 120)='".TODATE."'";
			$pl_info = selectAllQuery($sql);
			for($i=0; $i<count($pl_info['idx']); $i++){
				$idx = $pl_info['idx'][$i];
				$id = $pl_info['email'][$i];
				$result['idx'][] = $idx;
				$result['id'][] = $id;
			}
			return $result;

		}else{

			$sql = "select idx, email, name from work_penalty_list_log where state='0' and kind_flag='".$kind."' and companyno='".$companyno."' and convert(char(10), regdate, 120)='".TODATE."'";
			$pl_info = selectAllQuery($sql);
			for($i=0; $i<count($pl_info['idx']); $i++){
				$idx = $pl_info['idx'][$i];
				$id = $pl_info['email'][$i];
				$result['idx'][] = $idx;
				$result['id'][] = $id;
			}
			return $result;
		}
	}


	//페널티 횟수 체크(일체크)
	function penalty_info_cnt(){
		global $companyno;

		//주별 확인(월 ~ 금)
		$wdate = week_day(TODATE);

		//$sql = "select email, kind_flag from work_penalty_list where state='0' and penalty_cnt>'1' and companyno='".$companyno."' and workdate between '".$wdate['month']."' and '".$wdate['friday']."' group by kind_flag, email";
		$sql = "select email, kind_flag from work_penalty_list where state='0' and companyno='".$companyno."' and workdate='".TODATE."'";
		$penalty_info = selectAllQuery($sql);
		for($i=0; $i<count($penalty_info['email']); $i++){
			$penalty_email = $penalty_info['email'][$i];
			$penalty_kind_flag = $penalty_info['kind_flag'][$i];
			$penalty_info_cnt[$penalty_email]++;
		}

		return $penalty_info_cnt;
	}


	//오늘 업무 체크
	function todaywork_chk(){
		global $user_id, $user_name, $companyno;

		//주별 확인(월 ~ 금)
		$wdate = week_day(TODATE);

		//어제날짜
		$ywdate = strtotime(TODATE);
		$ystdate = date("Y-m-d",strtotime("-1 day", $ywdate));

		$result = array();
		$holiday_chk = holiday_chk();

		//휴무일 아닌경우
		if(!$holiday_chk){

			//월요일 이후 화요일부터 체크
			if (TODATE > $wdate['month']){
				$sql = "select count(1) as cnt from work_todaywork where state!='9' and work_flag='2' and email='".$user_id."' and workdate='".$ystdate."'";
				$work_info = selectQuery($sql);
				if(!$work_info['cnt']){
					$result['id'] = $user_id;
					$result['name'] = $user_name;
					$result['date'] = $ystdate;
				}
				return $result;
			}
		}
	}



	//퇴근 로그 기록 체크
	function logoff_chk(){
		global $user_id, $user_name, $companyno;

		//주별 확인(월 ~ 금)
		$wdate = week_day(TODATE);

		//어제날짜
		$ywdate = strtotime(TODATE);
		$ystdate = date("Y-m-d",strtotime("-1 day", $ywdate));
		//$ystdate = "2022-04-19";

		$result = array();
		$holiday_chk = holiday_chk();

		//휴무일 아닌경우
		if(!$holiday_chk){

			//월요일 이후 화요일부터 체크
			if (TODATE > $wdate['month']){
				$sql = "select idx from work_member_logoff where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".$ystdate."'";
				$logoff_info = selectQuery($sql);

				if(!$logoff_info['idx']){
					$result['id'] = $user_id;
					$result['name'] = $user_name;
					$result['wdate'] = $ystdate;
				}
				return $result;
			}
		}
	}

	//휴무일 체크
	function holiday_chk(){
		$sql = "select idx from work_holiday where state='0' and holiday='".TODATE."'";
		$info = selectQuery($sql);
		if($info['idx']){
			return true;
		}else{
			return false;
		}
	}

	//페널티 오늘업무 일정 체크
	function todaywork_decide_chk(){
		global $user_id;

		$decide_day = array();
		//삭제안된 오늘업무중 일정(연차,반차,외출,출장) 체크
		$sql = "select idx, email, name, workdate from work_todaywork where state!='9' and decide_flag in(1,2,3,5) and email='".$user_id."' and workdate='".TODATE."'";
		$work_decide_info = selectQuery($sql);
		if($work_decide_info['idx']){
			$email = $work_decide_info['email'];
			$name = $work_decide_info['name'];
			$workdate = $work_decide_info['workdate'];
			$decide_day[$email] = $workdate;
		}

		return $decide_day;
	}



	//페널티 출근 체크
	function penalty_login_check(){
		global $companyno ,$user_id;

		//$user_id = "fpqldhtk3@nate.com";
		if(!$user_id){
			return false;
		}

		//최고관리자
		$highlevel = '1';

		//어제날짜
		$ywdate = strtotime(TODATE);
		$ystdate = date("Y-m-d",strtotime("-1 day", $ywdate));
		//$ystdate = date("Y-m-d",strtotime("-4 day", $ywdate));

		//주별 확인(월 ~ 금)
		$wdate = week_day(TODATE);

		if($user_id=='sadary0@nate.com'){
		//	$user_id='sun@bizforms.co.kr';
		//	$user_id ='aeiou3143@nate.com';
		}

		//회원전체 인원수
		$sql = "select COUNT(1) cnt from work_member where state='0' and companyno='".$companyno."' and highlevel!='".$highlevel."'";
		$mem_data = selectQuery($sql);
		if($mem_data['cnt']){
			$mem_cnt = $mem_data['cnt'];
		}

		//회원전체 20%의 인원(round 반올림)
		//$percent = round($mem_cnt * 0.2);

		//어제 로그인한 회원 내역
		//$sql = "select count(idx) as cnt from work_member_login where state='0' and convert(char(10), regdate, 120) ='".$ystdate."'";
		//$mlogin_data = selectQuery($sql);
		//if($mlogin_data['cnt']){

			//로그인한 회원이 전체인원의 20% 이상일때 정상근무로 인정
			//if((int)$mlogin_data['cnt'] > (int)$percent){

				//[조건]
				//삭제업무X, 업무가 연차:1, 반차:2, 외출:3, 출장:5 제외, 월~금 체크
				//일정 여부 확인
				$deciad_day = array();
				$sql = "select email, name, workdate from work_todaywork where state!='9' and decide_flag in(1,2,3,5) and email='".$user_id."' and workdate between '".$wdate['month']."' and '".$wdate['friday']."'";
				$work_decide_info = selectAllQuery($sql);
				for($i=0; $i<count($work_decide_info['email']); $i++){
					$work_email = $work_decide_info['email'][$i];
					$work_name = $work_decide_info['name'][$i];
					$work_workdate = $work_decide_info['workdate'][$i];
					$deciad_day[$work_email] = $work_workdate;
				}

				//로그인 내역
				$mem_login_attend = array();
				//$sql = "select email, name, convert(char(10), regdate, 120) as reg from work_member_login where state='0' and email='".$user_id."' and convert(char(10), regdate , 120) between '".$wdate['month']."' and '".$wdate['friday']."' and convert(char(5), regdate, 8) > '".ATTEND_TIME."'";
				$sql = "select email, name, convert(char(10), regdate, 120) as reg from work_member_login where state='0' and email='".$user_id."' and convert(char(10), regdate , 120) between '".$wdate['month']."' and '".$wdate['friday']."' and convert(char(5), regdate, 8) > '08:00' and idx=656";
				$mem_loging_info = selectAllQuery($sql);
				for($i=0; $i<count($mem_loging_info['email']); $i++){
					$login_email = $mem_loging_info['email'][$i];
					$login_name = $mem_loging_info['name'][$i];
					$login_reg = $mem_loging_info['reg'][$i];
					$mem_login_attend['id'][] = $login_email;
					$mem_login_attend['reg'][] = $login_reg;
				}


				/*print "<pre>";
				print_r($mem_login_attend);
				print "</pre>";*/


				//페널티 등록된내역 확인 아이디, 지각
				//$mem_penalty = penalty_kind_info($user_id, 0);
				//$mem_penalty = penalty_kind_info('sadary0@nate.com', 2);

				//$mem_penalty['reg'][0] = '2022-04-20';
				//지각 전체 횟수
				$penalty = array();
				$penalty_cnt = count($mem_login_attend['id']);
				for($i=0; $i<$penalty_cnt; $i++){

					$id = $mem_login_attend['id'][$i];
					$reg = $mem_login_attend['reg'][$i];

					//echo $reg ." == " . $mem_penalty['reg'][$i];
					//echo "<Br>";
					//일정이 있는경우, 페널티 등록한 경우에는 횟수 차감
					//if($reg == $deciad_day[$id] || in_array($reg, $mem_penalty['reg'])){
					if($reg == $deciad_day[$id]){
						$penalty_cnt--;
					}

					//데이터가 하나일때
					if ($penalty_cnt == 1){
						if($reg == TODATE){
							$penalty['today'] = 'true';
						}else{
							$penalty['today'] = 'false';
						}
					}
				}

				$penalty['cnt'] = $penalty_cnt;
				return $penalty;
			//}
		//}
	}


	function penalty_kind_info($id, $kind){
		//주별 확인(월 ~ 금)
		$wdate = week_day(TODATE);

		$mem_penalty = array();
		$sql = "select idx, email, kind, file_path, file_name, file_real_name, CONVERT(varchar(10), regdate, 120) as reg from work_filesinfo_img_penalty where state='0' and kind='".$kind."' and companyno='".$companyno."' and email='".$id."' and convert(char(10), regdate , 120) between '".$wdate['month']."' and '".$wdate['friday']."'";
		$penalty_info = selectAllQuery($sql);
		for($i=0; $i<count($penalty_info['email']); $i++){
			$penalty_email = $penalty_info['email'][$i];
			$penalty_reg = $penalty_info['reg'][$i];
			$mem_penalty['id'][] = $penalty_email;
			$mem_penalty['reg'][] = $penalty_reg;
		}
		return $mem_penalty;
	}



	//챌린지 테마 정보
	function challenges_thema_info(){
		global $companyno ,$user_level;

		$sql = "select idx, title from work_challenges_thema where state='0'";

		/*
		if($user_level != '1'){
			$sql = $sql .= " and companyno='".$companyno."'";
		}
		*/

		$thema_info = selectAllQuery($sql);
		for($i=0; $i<count($thema_info['idx']); $i++){
			$thema_info_title[$thema_info['idx'][$i]] = $thema_info['title'][$i];
		}


		return $thema_info_title;
	}

	//챌린지 테마리스트 정보
	function challenges_thema_list_info(){
		global $user_id, $companyno;

		//사용자의 테마 정보가 있을경우 각 계정별로 설정리스트, 없을경우는 관리자가 설정한 테마 정보 출력
		//일반 사용자의 테마 정보
		$sql = "select thema_idx,title from work_challenges_thema_user_list where state='0' and email='".$user_id."' order by sort asc";
		$thema_list_info = selectAllQuery($sql);
		if(!$thema_list_info['thema_idx']){
			//관리자가 설정한 테마리스트
			$sql = "select idx as thema_idx,title,recom from work_challenges_thema where state='0' order by sort asc";
			$thema_list_info = selectAllQuery($sql);
		}

		return $thema_list_info;
	}


	//오늘업무 마지막 작성시간(분단위)
	function todaywork_last_time(){
		global $user_id, $companyno;
		$sql = "select top 1 datediff(MI, regdate , getdate()) as regtime from work_todaywork where state='0' and companyno='".$companyno."' and notice_flag='0' and share_flag!='2' and work_flag>'1' and email='".$user_id."' and workdate='".TODATE."' order by idx desc";
		$work_info = selectQuery($sql);
		return $work_info;
	}

	//메모 마지막 작성시간(분단위)
	function todaymemo_last_time(){
		global $user_id, $companyno;
		//$sql = "select top 1 datediff(MI, regdate , getdate()) as regtime from work_todaywork_comment where state='0' and email='".$user_id."' and convert(char(10), regdate, 120)='".TODATE."' order by idx desc";

		$sql = "select top 1 case when editdate is not null then datediff(MI, editdate , getdate()) else  datediff(MI, regdate , getdate()) end as regtime";
		$sql = $sql .= " from work_todaywork_comment where state='0' and companyno='".$companyno."' and email='".$user_id."' and convert(char(10), regdate, 120)='".TODATE."' order by idx desc";
		$work_info = selectQuery($sql);
		return $work_info;
	}


	//오늘업무 요청 받은업무 작성시간(분단위)
	function workreq_last_time(){
		global $user_id, $companyno;
		$sql = "select top 1 case when editdate is not null then datediff(MI, editdate , getdate()) else  datediff(MI, regdate , getdate()) end as regtime";
		$sql = $sql .= " from work_todaywork where state='0' and companyno='".$companyno."' and work_flag='3' and email='".$user_id."' and workdate='".TODATE."' order by idx desc";
		$work_info = selectQuery($sql);
		return $work_info;
	}

	//공유받은 업무 작성시간(분단위)
	function workshare_last_time(){
		global $user_id, $companyno;
		$sql = "select top 1 case when editdate is not null then datediff(MI, editdate , getdate()) else  datediff(MI, regdate , getdate()) end as regtime";
		$sql = $sql .= " from work_todaywork where state='0' and companyno='".$companyno."' and share_flag>'0' and email='".$user_id."' and workdate='".TODATE."'";
		$work_info = selectQuery($sql);
		return $work_info;
	}


	//업무 데이터 로그 저장(타임라인)
	//상태값, 코드, 연결키값, 아이디, 이름, 받는사람아이디, 받는사람이름, 오늘업무 idx키값
	function work_data_log($state="0", $code, $work_idx="", $uid="", $uname="", $tid="", $tname="", $link_idx=""){
		global $companyno, $user_id, $chkMobile;

		$type_flag = ($chkMobile)?1:0;

		//데이터 로그 코드 조회
		$sql = "select idx, memo, kind_flag from work_data_code where state='0' and idx='".$code."'";
		$info = selectQuery($sql);
		if($info['idx']){
			$memo = $info['memo'];
			$kind_flag = $info['kind_flag'];

			//삭제처리
			if($state == '9'){

				//$sql = "update work_data_log set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and email='".$uid."' and work_idx='".$work_idx."'";
				if($link_idx){
					$sql = "update work_data_log set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and link_idx='".$link_idx."'";
				}else{
					$sql = "update work_data_log set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."'";
				}

				$up = updateQuery($sql);

				if($up){
					//요청받은 데이터 삭제처리
					$sql = "update work_data_log set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and code='6' and work_idx='".$work_idx."'";
					$up = updateQuery($sql);
				}

			}else{


				//기본:0, 1:보냄,받음 여부
				//보냄, 받음인경우 사용자까지 체크함

				if($kind_flag=='0'){
					$sql = "select idx from work_data_log where state='0' and companyno='".$companyno."' and code='".$code."' and work_idx='".$work_idx."' and email='".$uid."'";
				}else if($kind_flag=='1'){
					$sql = "select idx from work_data_log where state='0' and companyno='".$companyno."' and code='".$code."' and work_idx='".$work_idx."' and email='".$uid."' and send_email='".$user_id."'";
				}


				//오늘업무 작성일때는 내용삭제처리
				if($code == '2'){
					$tid = "";
					$tname = "";
				}

				$info = selectQuery($sql);
				if(!$info['idx']){

					//코인 보상받음,역량 코인 보상,좋아요 코인 보상
					if(in_array($code , array('21','24','25'))){
						//코인 받은 내역 조회
						$sql = "select idx, coin from work_coininfo where state='0' and companyno='".$companyno."' and email='".$uid."' and idx='".$work_idx."' ";
						$work_coin_info = selectQuery($sql);
						if($work_coin_info['idx'] && $work_coin_info['coin']){
							$data_coin = $work_coin_info['coin'];
						}

						// 코인보상 메모 제외(김정훈)
						if(in_array($code , array('24','25'))){
							//코인지급
							//이달의역량보상, 이달의좋아요보상
							$yesterday = date('Y-m-d', strtotime(" -2 day"));
							$yester_ex = explode("-", $yesterday);
							if($yester_ex){
								//어제날짜 년,월,일
								$yester_year = $yester_ex[0];
								$yester_month = $yester_ex[1];
								$yester_day = $yester_ex[2];
								$curMonth = (int)$yester_month;
								$memo = $curMonth . "월 ". $memo;
							}
						}
					}

					//등록처리
					$sql = "insert into work_data_log(state,code,work_idx,link_idx,companyno,email,name,send_email,send_name,coin,memo,type_flag,ip,workdate) values(";
					$sql = $sql .= "'".$state."','".$code."','".$work_idx."','".$link_idx."','".$companyno."','".$uid."','".$uname."','".$tid."','".$tname."','".$data_coin."','".$memo."','".$type_flag."','".LIP."','".TODATE."')";
					$res_info_idx = insertQuery($sql);
				}
			}
		}
	}



	//업무 데이터 로그 저장(타임라인)
	//상태값, 코드, 연결키값, 아이디, 이름, 받는사람아이디, 받는사람이름, 오늘업무 idx키값
	function work_data_multi_log($state="0", $code, $work_idx="", $uid="", $uname="", $tid="", $tname="", $cnt=""){
		global $companyno, $user_id, $chkMobile;

		$type_flag = ($chkMobile)?1:0;

		//데이터 로그 코드 조회
		$sql = "select idx, memo, kind_flag from work_data_code where state='0' and idx='".$code."'";
		$info = selectQuery($sql);
		if($info['idx']){
			$memo = $info['memo'];
			$kind_flag = $info['kind_flag'];

			//삭제처리
			if($state == '9'){

				//$sql = "update work_data_log set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and email='".$uid."' and work_idx='".$work_idx."'";
				if($link_idx){
					$sql = "update work_data_log set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and link_idx='".$link_idx."'";
				}else{
					$sql = "update work_data_log set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."'";
				}

				$up = updateQuery($sql);

				if($up){
					//요청받은 데이터 삭제처리
					$sql = "update work_data_log set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and code='6' and work_idx='".$work_idx."'";
					$up = updateQuery($sql);
				}

			}else{


				//기본:0, 1:보냄,받음 여부
				//보냄, 받음인경우 사용자까지 체크함

				if($kind_flag=='0'){
					$sql = "select idx from work_data_log where state='0' and companyno='".$companyno."' and code='".$code."' and work_idx='".$work_idx."' and email='".$uid."'";
				}else if($kind_flag=='1'){
					$sql = "select idx from work_data_log where state='0' and companyno='".$companyno."' and code='".$code."' and work_idx='".$work_idx."' and email='".$uid."' and send_email='".$user_id."'";
				}

				//오늘업무 작성일때는 내용삭제처리
				if($code == '2'){
					$tid = "";
					$tname = "";
				}

				$info = selectQuery($sql);
				if(!$info['idx']){

					//코인 보상받음,역량 코인 보상,좋아요 코인 보상
					if(in_array($code , array('21','24','25'))){
						//코인 받은 내역 조회
						$sql = "select idx, coin from work_coininfo where state='0' and companyno='".$companyno."' and email='".$uid."' and idx='".$work_idx."' ";
						$work_coin_info = selectQuery($sql);
						if($work_coin_info['idx'] && $work_coin_info['coin']){
							$data_coin = $work_coin_info['coin'];
						}

						//코인지급
						//이달의역량보상, 이달의좋아요보상
						$yesterday = date('Y-m-d', strtotime(" -2 day"));
						$yester_ex = explode("-", $yesterday);
						if($yester_ex){
							//어제날짜 년,월,일
							$yester_year = $yester_ex[0];
							$yester_month = $yester_ex[1];
							$yester_day = $yester_ex[2];
							$curMonth = (int)$yester_month;
							$memo = $curMonth . "월 ". $memo;
						}
					}


					//받는사용자가 있을경우
					if($cnt > 0){
						$memo = "+".$cnt."명 ".$memo;
					}

					//등록처리
					$sql = "insert into work_data_log(state,code,work_idx,link_idx,companyno,email,name,send_email,send_name,coin,memo,type_flag,ip,workdate) values(";
					$sql = $sql .= "'".$state."','".$code."','".$work_idx."','".$link_idx."','".$companyno."','".$uid."','".$uname."','".$tid."','".$tname."','".$data_coin."','".$memo."','".$type_flag."','".LIP."','".TODATE."')";
					$res_info_idx = insertQuery($sql);
				}
			}
		}
	}


	//역량지표계산
	function reward_cp_type_info($type_idx, $type){

		//1:에너지, 2:성과, 3:성장, 4:협업, 5:성실, 6:실행
		//$max_array = array("1"=>"20", "2"=>"20", "3"=>"80", "4"=>"80", "5"=>"160", "6"=>"140");
		//$max_array = array("1"=>"125", "2"=>"279", "3"=>"154", "4"=>"227", "5"=>"147", "6"=>"198");

		global $user_id, $max_array;

		//$max_array = array("1"=>"94", "2"=>"88", "3"=>"116", "4"=>"171", "5"=>"110", "6"=>"149");

		//최대값(conf.php 설정값)
		$max_no = $max_array[$type];

		//최소값
		$minx_no = '25';

		$result['cp_ori'] = $type_idx;

		//최대점수가 넘을경우 최대수로 사용
		if($type_idx > $max_no ){
			$type_idx = $max_no;
		}

		//비율계산
		$cp_type = @round($type_idx/$max_no * 100);

		//소수점 2자리 계산
		$cp_type_sp = @round($type_idx/$max_no * 5, 2);

		if(!$cp_type_sp){
			$cp_type_sp = "0.0";
		}

		//실제그래프값
		$cp_graph = $cp_type * 0.75 + $minx_no;

		//최소점수 일때
		if($cp_graph < $minx_no){
			$cp_graph = $minx_no;
		}

		//소수점 1자리 표현
		$cp_type_sp = sprintf('%0.1f', $cp_type_sp);

		$result['cp_type'] = $cp_type;
		$result['cp_type_sp'] = $cp_type_sp;
		$result['cp_graph'] = $cp_graph;

		return $result;

	}



	//역량지표평가등급
	function cp_rate_info($data){

		if($data >= 71 && $data <= 100){
			$result = "S";
		}else if($data >= 51 && $data <= 70){
			$result = "A";
		}else if($data >= 31 && $data <= 50){
			$result = "B";
		}else if($data >= 11 && $data <= 30){
			$result = "C";
		}else if($data >= 5 && $data <= 10){
			$result = "D";
		}else{
			$result = "E";
		}

		return $result;
	}


	function work_reward_like(){

		//역량지표 좋아요
		//성과를 칭찬하기, 열렬히 응원하기, 전문가로 인정하기, 잘해서 칭찬하기, 적극성 인정하기, 도움에 감사하기
		$sql = "select idx, act_title from work_cp_reward where state='0' and service='like' and idx > 20 order by idx asc";
		$work_reward_info = selectAllQuery($sql);
		for($i=0; $i<count($work_reward_info['idx']); $i++){
			$work_reward_info_title = $work_reward_info['act_title'][$i];
			$work_reward_title[$i] = $work_reward_info_title;
		}
		return $work_reward_title;
	}




	//strpos 배열로 사용
	function strposa($haystack, $needles=array(), $offset=0) {
        $chr = array();
        foreach($needles as $needle) {
                $res = strpos($haystack, $needle, $offset);
                if ($res !== false) $chr[$needle] = $res;
        }
        if(empty($chr)) return false;
        return min($chr);
	}



	//홑따옴표 치환하기
	function replace_text($contents){

		if(strpos($contents, "'") !== false) {
			$contents = str_replace("'", "''", $contents);
		}
		return $contents;
	}



	//메일발송
	function mailer($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="")
	{

		if ($type != 1) $content = nl2br($content);
		// type : text=0, html=1, text+html=2
		$mail = new PHPMailer(true); // defaults to using php "mail()"
		$mail->IsSMTP();
		// $mail->SMTPDebug = 2;
		$mail->SMTPSecure = "ssl";
		$mail->SMTPAuth = true;
		$mail->Host = "smtp.mailplug.co.kr";
		$mail->Port = 465;
		$mail->Username = "devmaster@bizforms.co.kr";
		$mail->Password = "MailBizdev@!";
		$mail->CharSet = 'UTF-8';
		$mail->From = $fmail;
		$mail->FromName = $fname;
		$mail->Subject = $subject;
		$mail->AltBody = ""; // optional, comment out and test

		$auth_str = 'abcdefghijkmnopqrstuvwxyz23456789';
		$email_numbers = substr(str_shuffle($auth_str),-6); // 중복 없는 6자리 문자열

		//if (!preg_match("/^[A-Z0-9._-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z.]{2,6}$/i",$to)) alert ("이메일 주소가 올바른지 확인하세요.");
		//$content="안녕하세요.\n"."### 운영자 입니다.\n"."아래 인증 코드를 복사하여 가입 창 E-mail Check란에 넣어주십시오.\n\n"."<b>E-mail Check: <span style=\"color: red\">$email_numbers</span></b>\n\n"."E-mail Check를 타이핑하기 힘들때는 마우스로 코드를 더블클릭 후 Ctrl-C 를 눌러서 복사한후,\n"."E-mail Check란에서 Ctrl-V를 눌러서 붙여 넣기 하시면됩니다.";
		///$content="안녕하세요.\\n\\n인증메일이 발송되었습니다.\\n\\n <span style=\"color: red\"><a href='http://www.todaywork.co.kr/admin/user.php' target=\"_blank\">사용자 인증</a></span>으로 이동해 주세요.";


		$mail->msgHTML($content);
		$mail->addAddress($to);
		if ($cc){
			$mail->addCC($cc);
		}

		if ($bcc){
			$mail->addBCC($bcc);
		}
		if ($file != "") {
			foreach ($file as $f) {
				$mail->addAttachment($f['path'], $f['name']);
			}
		}
		if ( $mail->send() ){
			return true;
		}else{
			return false;
		}
	}



	//파일처리
	//파일정보, 서비스, 번호
	function work_upload_files($file, $kind, $idx){

		global $user_id, $companyno, $upload_path, $upload_path_ori, $dir_file_path, $work_save_dir, $work_save_dir_img, $work_save_dir_img_ori, $comfolder;

		//echo "파일처리 시작 ==> ";
		//echo $file ." , ".  $kind. " , " .$idx;
		//echo "\n";

		//파일정보, 게시물번호
		if($file && $kind && $idx){

			$res = array();
			$num = 0;
			//파일이 몇번째 인지 체크
			$sql = "select max(num) as num from work_filesinfo_todaywork where state='0' and companyno='".$companyno."' and email='".$user_id."' and work_idx='".$idx."'";
			$list_info = selectQuery($sql);
			if($list_info['num']){
				$num = $list_info['num'];
			}

			//파일갯수
			$file_cnt = count($file['files']['name']);
			$cnt=1;
			for($i=0; $i<$file_cnt; $i++){

				//파일명
				$file_name = $file['files']['name'][$i];

				//파일타입
				$file_type = $file['files']['type'][$i];

				//파일위치
				$file_tmp_name = $file['files']['tmp_name'][$i];

				//파일사이즈
				$file_size = $file['files']['size'][$i];


				//랜덤번호
				$rand_id = name_random();

				//파일 확장자
				$ext = array_pop(explode(".", strtolower($file_name)));

				//변경되는 파일명
				list($microtime,$timestamp) = explode(' ',microtime());
				$time = $timestamp.substr($microtime, 2, 3);
				$datetime = date("YmdHis", $timestamp).substr($microtime, 2, 3);

				//업로드 파일명
				$renamefile = "{$datetime}_{$rand_id}_{$kind}_{$idx}.{$ext}";

				//년도
				$dir_year = date("Y", TODAYTIME);

				//월
				$dir_month = date("m", TODAYTIME);

				//회사별 폴더명
				$comfolder = $comfolder;

				//오늘업무
				if($kind == "work"){

					//이미지 업로드 디렉토리 -/data/(회사폴더명)/work/img/년/월/
					$upload_path_img = $dir_file_path."/".$work_save_dir_img."/".$dir_year."/".$dir_month."/";
					$upload_path_img = str_replace($work_save_dir_img , "data/".$companyno."/".$comfolder."/"."work/img" , $upload_path_img);

					//이미지 오리지널 업로드 디렉토리 -/data/(회사폴더명)/work/img/년/월/
					$upload_path_img_ori = $dir_file_path."/".$work_save_dir_img_ori."/".$dir_year."/".$dir_month."/";
					$upload_path_img_ori = str_replace($work_save_dir_img_ori , "data/".$companyno."/".$comfolder."/"."work/img_ori" , $upload_path_img_ori);

					//첨부파일 업로드 디렉토리 -/data/(회사폴더명)/work/img/년/월/
					$upload_path_file = $dir_file_path."/".$work_save_dir."/".$dir_year."/".$dir_month."/";
					$upload_path_file = str_replace($work_save_dir , "data/".$companyno."/".$comfolder."/"."work/files" , $upload_path_file);

				}else if($kind == "challenge"){

				}else if($kind == "penalty"){

				}else if($kind == "penalty"){

				}

				//업로드될 파일경로/파일명
				$upload_files_img = $upload_path_img.$renamefile;

				//원본 업로드될 파일경로/파일명
				$upload_files_img_ori = $upload_path_img_ori.$renamefile;

				//업로드될 첨부 이미지파일경로
				$upload_img_path = $upload_path_img;

				//업로드될 첨부파일경로
				$upload_file_path = $upload_path_file;

				//업로드될 첨부파일경로/파일명
				$upload_files = $upload_path_file.$renamefile;

				//디렉토리 없는 경우 권한 부여 및 생성
				//업로드 이미지
				if ( !is_dir ( $upload_path_img ) ){
					mkdir( $upload_path_img , 0777, true);
				}

				//업로드 이미지 오리지널
				if ( !is_dir ( $upload_path_img_ori ) ){
					mkdir( $upload_path_img_ori , 0777, true);
				}

				//업로드 첨부파일
				if ( !is_dir ( $upload_path_file ) ){
					mkdir( $upload_path_file , 0777, true);
				}

				/*	//파일정보
					Array
					(
						[0] => 400
						[1] => 400
						[2] => 3
						[3] => width="400" height="400"
						[bits] => 8
						[mime] => image/png
					)
				*/

				/*
				echo "확장자 :: " . $ext;
				echo "\n\n";

				echo "첨부파일 업로드";
				echo "\n\n";
				echo "upload_files ==> " . $upload_files;
				*/

				//이미지 허용 확장자
				$img_ext_array = array("gif", "png", "jpg", "jpeg", "bmp", "heic");
				if(in_array($ext, $img_ext_array)){
					$res['format'][] = "img";

					list($width, $height) = getimagesize($file_tmp_name);
					$res['file_width'][] = $width;
					$res['file_height'][] = $height;

					//첨부파일 업로드
					$res['result'][] = file_upload_send( $file_tmp_name, $upload_files_img );

					//첨부파일 이미지 경로
					$upload_file_path = str_replace($dir_file_path , "" , $upload_img_path);

				}else{

					$res['format'][] = "file";
					//첨부파일 업로드
					$res['result'][] = file_upload_send( $file_tmp_name, $upload_files );

					//첨부파일 경로
					$upload_file_path = str_replace($dir_file_path , "" , $upload_file_path);
				}

				//echo "업로드 결과 :: " .$res['result'] . " :: ";
				//echo "\n";
				//echo "파일정보 :: " .$upload_path_file . " :: ";
				//echo "\n";


				//첨부파일 순서
				//$res['num'][] = $i;
				$res['num'][] = $num+$cnt;

				//첨부파일의 오늘업무 idx
				$res['work_idx'][] = $idx;

				//첨부파일 업로드 경로
				$res['file_path'][] = $upload_file_path;

				//첨부파일 이름
				$res['file_name'][] = $renamefile;

				//첨부파일 원본이름
				$res['file_real_name'][] = $file_name;

				//첨부파일 확장자
				$res['file_type'][] = $ext;

				//첨부파일 사이즈
				$res['file_size'][] = $file_size;

				$cnt++;
			}

			if($res){
				return $res;
			}else{
				return false;
			}

		}
	}


	//파일 애니메이션 체크
	function is_ani($file_tmp_name) {
		if(!($fh = @fopen($file_tmp_name, 'rb')))
			return false;
		$count = 0;
		//an animated gif contains multiple "frames", with each frame having a
		//header made up of:
		// * a static 4-byte sequence (\x00\x21\xF9\x04)
		// * 4 variable bytes
		// * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)

		// We read through the file til we reach the end of the file, or we've found
		// at least 2 frame headers
		while(!feof($fh) && $count < 2) {
			$chunk = fread($fh, 1024 * 100); //read 100kb at a time
			$count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
	   }

		fclose($fh);
		return $count > 1;
	}


	//php실행속도
	function php_timer()
	{
		static $arr_timer;

		if(!isset($arr_timer)) {
			$arr_timer = explode(" ", microtime());
		}
		else {
			$arr_timer2 = explode(" ", microtime());
			$result = ($arr_timer2[1] - $arr_timer[1]) + ($arr_timer2[0] - $arr_timer[0]);
			$result = sprintf("%.4f",$result);

			return $result;
		}

		return false;
	}

	function get_time() {
		$t=explode(' ',microtime());
		return (float)$t[0]+(float)$t[1];
	}


	//회사정보
	function company_info(){
		global $companyno;

		$sql = "select idx, comcoin from work_company where state='0' and idx='".$companyno."'";
		$company_info = selectQuery($sql);
		if($company_info){
			return $company_info;
		}
	}


	//부서정보
	function team_info($partname){
		global $companyno;

		$sql = "select idx from work_team where state='0' and companyno='".$companyno."' and partname='".$partname."'";
		$team_info = selectQuery($sql);
		if($team_info['idx']){
			return $team_info['idx'];
		}
	}


	//파일 로그 남기기(query 저장)
	function write_log($log){
		global $dir_file_path;

		$logPathDir = $dir_file_path."/log/query";	//로그위치 지정
		$filePath = $logPathDir."/".date("Y")."/".date("n");

		//년도
		$dir_year = date("Y", TODAYTIME);

		//월
		$dir_month = date("m", TODAYTIME);

		if(!is_dir($logPathDir."/".$dir_year)){
			mkdir($logPathDir."/".$dir_year, 0777, true);
		}

		if(!is_dir($logPathDir."/".$dir_year."/".$dir_month)){
			mkdir(($logPathDir."/".$dir_year."/".$dir_month), 0777, true);
		}

		$log_file = fopen($logPathDir."/".$dir_year."/".$dir_month."/".date("Ymd").".txt", "a");
		fwrite($log_file, $log."\r\n");
		fclose($log_file);
	}


	//파일 로그 남기기(폴더지정)
	function write_log_dir($log, $dir){
		global $dir_file_path;

		$logPathDir = $dir_file_path."/log//".$dir;	//로그위치 지정
		$filePath = $logPathDir."/".date("Y")."/".date("n");

		//년도
		$dir_year = date("Y", TODAYTIME);

		//월
		$dir_month = date("m", TODAYTIME);

		if(!is_dir($logPathDir."/".$dir_year)){
			mkdir($logPathDir."/".$dir_year, 0777, true);
		}

		if(!is_dir($logPathDir."/".$dir_year."/".$dir_month)){
			mkdir(($logPathDir."/".$dir_year."/".$dir_month), 0777, true);
		}

		$log_file = fopen($logPathDir."/".$dir_year."/".$dir_month."/".date("Ymd").".txt", "a");
		fwrite($log_file, $log."\r\n");
		fclose($log_file);
	}


	//ai 메모작성
	//$work_idx, $insert_idx, $user_id, $send_info['email'], $jf_idx
	//업무번호, 좋아요idx, 보낸사람id, 받은사람id, 역량(1~6), like테이블 idx
	function memo_ai_write($work_idx, $insert_idx, $user_id, $send_userid, $like_idx, $type="",$jl_comment,$like_com_idx=""){
		global $companyno, $chkMobile;

		//구분(0:사이트, 1:모바일)
		$type_flag = ($chkMobile)?1:0;

		//AI등록으로 1로 지정
		$cmt_flag = "1";

		//업무번호, 좋아요idx, 좋아요구분
		if($work_idx){
			//작성자정보
			$mem_user_info = member_row_info($user_id);

			//받은사람 정보
			$mem_send_user_info = member_row_info($send_userid);

			//요청업무(type=req)

			if($type=="req"){
				$comment = "".$mem_user_info['name']."님께서 요청하신 업무를 완료하였습니다.";
			}else{
				//좋아요 선택하면
				if($like_idx){
					$sql = "select idx, rank, name from work_category where state='0' and idx='".$like_idx."'";
					$like_info = selectQuery($sql);
					if($like_info['idx']){
						$like_name = $like_info['name'];
						$like_rank = $like_info['idx'];

						if(in_array($like_rank , array(5,6))){
							$like_text = "를";
						}else{
							$like_text = "을";
						}
					}

					$insert_idx = $work_idx;
					$mem_memo_info = "-";
					// $comment = "".$mem_user_info['name']."님이 ".$mem_send_user_info['name']."님의 ''".$like_name."''".$like_text." 좋아합니다.";
					$comment = $jl_comment;
				}
			}

			$sql = "select ai_like_idx from work_todaywork_comment where link_idx = '".$insert_idx."' and work_idx != link_idx";
			$sql = $sql." and work_idx != '".$insert_idx."' order by ai_like_idx desc";
			$ai_like_idx = selectQuery($sql);

			if($ai_like_idx['ai_like_idx'] != 0){
				$ai_like_cnt = $ai_like_idx['ai_like_idx'] + 1;
			}else{
				$ai_like_cnt = 1;
			}


				$sql = "insert into work_todaywork_comment(cmt_flag, link_idx, work_idx, companyno, email, name, part, partno, comment, type_flag,ip, like_email, ai_like_idx, like_idx,workdate) values";
				$sql = $sql .= "('".$cmt_flag."','".$insert_idx."','".$work_idx."','".$companyno."', null, null, null, null,'".$comment."','".$type_flag."','".LIP."','".$user_id."','".$ai_like_cnt."','".$like_com_idx."','".TODATE."')";
				$res_idx = insertIdxQuery($sql);

				if($res_idx){

					$sql_ai = "select ai_like_idx,like_email from work_todaywork_comment where state !=9 and link_idx = '".$insert_idx."' order by regdate desc";
					$com_idx_ai = selectAllQuery($sql_ai);


						if($com_idx_ai){
							for($z=0; $z<count($com_idx_ai['ai_like_idx']); $z++){
								$ai_like_cnt = $com_idx_ai['ai_like_idx'][$z];
								$ai_like_email = $com_idx_ai['like_email'][$z];

								$sql_kr = "select com_idx from work_todaywork_like where state !=9 and work_idx = '".$insert_idx."'";
								$sql_kr = $sql_kr." and email = '".$ai_like_email."'order by regdate desc";

								$com_kr = selectQuery($sql_kr);

								if($com_kr){
									$sql2 = "update top(1) work_todaywork_like set ai_like_idx = '".$ai_like_cnt."'";
									$sql2 = $sql2." where work_idx = '".$insert_idx."' and email = '".$ai_like_email."' and state != 9";
									$sql2 = $sql2." and com_idx is NULL";
									$update_com_like = updateQuery($sql2);
								}
							}

							$sql1 = "select a.idx as idx, a.like_idx as like_idx from work_todaywork_comment a join work_todaywork_like b on a.like_idx = b.idx";
							$sql1 = $sql1." where a.link_idx = a.work_idx and a.state !=9 and a.link_idx = '".$insert_idx."'";
							$com_idx1 = selectAllQuery($sql1);

							if($com_idx1){
								for($lc=0; $lc<count($com_idx1['idx']); $lc++){
									$com_com_idx = $com_idx1['idx'][$lc];
									$com_like_idx = $com_idx1['like_idx'][$lc];

									$sql3 = "update top(1) work_todaywork_like set com_idx = '".$com_com_idx."'";
									$sql3 = $sql3." where idx = '".$com_like_idx."' and state != 9";
									$sql3 = $sql3." and com_idx is NULL";

									$update_com_like = updateQuery($sql3);
								}
							}
						}
					}

			}
		}



	//ai 메모삭제
	function memo_ai_del($work_idx){
		global $companyno,$user_id;

		if($work_idx){
			$sql = "select idx from work_todaywork_comment where state='0' and companyno='".$companyno."' and cmt_flag='1' and work_idx='".$work_idx."'";

			/*echo "##########\n\n";
			echo $sql;
			echo "\n\n";
			*/
			$ai_memo_info = selectQuery($sql);
			if($ai_memo_info['idx']){
				$sql = "update work_todaywork_comment set state='9', editdate=getdate() where idx='".$ai_memo_info['idx']."'";

				if($work_idx == '53242'){
					echo "\n\n";
					echo 'sql::::'.$sql;
					echo "\n\n";
				}

				updateQuery($sql);
			}
		}
	}


	//업무구분, 작성자 체크
	function work_todaywork_type($idx){
		global $companyno;

		$sql = "select idx, email from work_todaywork where companyno='".$companyno."' and idx='".$idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			return $work_info;
		}
	}


	//데이터 회수 처리(삭제시 회수)
	function work_data_callback($work_idx){
		global $companyno;

		$sql = "select idx from work_todaywork_like where state='0' and companyno='".$companyno."' and email='".$user_id."' and work_idx='".$work_idx."'";
		echo $sql;
		echo "\n";
		$wokr_like_info = selectQuery($sql);
		if($work_like_info['idx']){
			$sql = "update work_todaywork_like set state='9', editdate=".DBDATE." where idx='".$work_like_info['idx']."'";
			echo $sql;
			echo "\n";
			$up = updateQuery($sql);
			if($up){
				$sql = "select idx from work_data_log where companyno='".$companyno."' and idx='".$work_like_info['idx']."'";
				$work_data_info = selectQuery($sql);

				echo $sql;
				echo "\n";
				if($work_data_info['idx']){
					$sql = "update work_data_log set state='9', editdate=".DBDATE." where work_idx='".$work_data_info['idx']."'";
					echo $sql;
					echo "\n";
					$up = updateQuery($sql);
				}
			}

			$sql = "select idx from work_";
		}
	}



	//읽음표시
	//work_read_check($user_id, "day", $sel_wdate, "");
	function work_read_check($user_id, $day_type, $sdate, $edate){
		global $companyno, $user_id;

		if($idx==null){

			if(strpos($sdate, ".") !== false) {
				$sdate = str_replace(".", "-", $sdate);
			}

			if(strpos($edate, ".") !== false) {
				$edate = str_replace(".", "-", $edate);
			}

			//일일업무
			if($day_type=="day"){
				$where = " and workdate='".$sdate."'";
			//주간업무
			}else if($day_type=="week"){
				$where = " and workdate between '".$sdate."' and '".$edate."'";
			}

			$sql = "select idx, work_flag, share_flag, work_idx, read_flag from work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."'".$where."";
			$work_info = selectAllQuery($sql);

			for($i=0; $i<count($work_info['idx']); $i++){
				$idx = $work_info['idx'][$i];
				$work_flag = $work_info['work_flag'][$i];
				$work_idx = $work_info['work_idx'][$i];
				$share_flag = $work_info['share_flag'][$i];
				$read_flag = $work_info['read_flag'][$i];

				//요청업무
				if($work_flag=='3' && $work_idx){
					$sql = "select idx from work_todaywork_user where state!='9' and read_flag='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and email='".$user_id."'";
					$work_user_info = selectQuery($sql);
					if($work_user_info['idx']){
						$sql = "update work_todaywork_user set read_flag='1', readdate=".DBDATE." where idx='".$work_user_info['idx']."'";
						$up_req = updateQuery($sql);
					}
				}

				//보고업무
				if($work_flag=='1' && $work_idx){
					$sql = "select idx from work_todaywork_report where state!='9' and read_flag='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and email='".$user_id."'";
					$work_user_info = selectQuery($sql);
					if($work_user_info['idx']){
						$sql = "update work_todaywork_report set read_flag='1', readdate=".DBDATE." where idx='".$work_user_info['idx']."'";
						$up_report = updateQuery($sql);
					}
				}

				//공유업무 받은업무가 있으면
				if($share_flag=='2'){
					$sql = "select idx from work_todaywork_share where state!='9' and read_flag='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and email='".$user_id."'";
					$work_user_info = selectQuery($sql);
					if($work_user_info['idx']){
						$sql = "update work_todaywork_share set read_flag='1', readdate=".DBDATE." where idx='".$work_user_info['idx']."'";
						$up_share = updateQuery($sql);
					}
				}

				//업무 읽음표기
				if($read_flag=='0' && $work_idx){
					$sql = "update work_todaywork set read_flag='1', readdate=".DBDATE." where idx='".$idx."'";
					$up = updateQuery($sql);
				}

				//if($up_req || $up_report || $up_share){
				//	$sql = "update work_todaywork set read_flag='1', readdate=".DBDATE." where idx='".$idx."'";
				//	$up = updateQuery($sql);
				//}


			}

		}else{

			//게시물별로 업데이트 처리
			$sql = "select idx, work_flag, work_idx from work_todaywork where state!='9' and idx='".$idx."'";
			$work_info = selectQuery($sql);
			if($work_info['idx']){
				$idx = $work_info['idx'];
				$work_flag = $work_info['work_flag'];
				$work_idx = $work_info['work_idx'];

				//요청업무
				$sql = "select idx from work_todaywork_user where state!='9' and read_flag='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and email='".$user_id."'";
				$work_user_info = selectQuery($sql);
				if($work_user_info['idx']){
					$sql = "update work_todaywork_user set read_flag='1', readdate=".DBDATE." where idx='".$work_user_info['idx']."'";
				//	echo $sql;
				//	echo "\n\n";
					//$up = updateQuery($sql);
				}
			}

		}
	}


	//좋아요 체크(업무번호, 보낸사람아이디)
	function like_check($idx ,$send_userid){
		global $companyno;

		$sql = "select idx from work_todaywork_like where state='0' and companyno='".$companyno."' and work_idx='".$idx."' and send_email='".$send_userid."'";
		$like_info = selectQuery($sql);
		if($like_info['idx']){
			return true;
		}else{
			return false;
		}
		exit;
	}




	//역량 할당코인_매일갱신
	function work_com_reward_wday($user_id, $wdate){

		global $companyno, $user_name, $month_first_day, $month_last_day, $max_array;


		$member_info_row = member_row_info($user_id);
		$user_name = $member_info_row['name'];

		//어제 날짜
		//$wdate = date("Y-m-d", strtotime($day." -1 day"));

		//$yday = $date;
		//echo $yday;


		//획득역량점수
		$sql = "select sum(type1) + sum(type2) +sum(type3) + sum(type4) + sum(type5) + sum(type6) as com from work_cp_reward_list";
		$sql = $sql .= " where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".$wdate."'";
		$work_reward_info = selectQuery($sql);

		if($work_reward_info['com']){
			//역량점수
			$reward_com_int = $work_reward_info['com'];
		}


		//역량합계
		$max_array_sum = array_sum($max_array);

		$work_com_int = $max_array_sum;

		//50%계산
		$reward_com_jumsu = @round($work_com_int * 0.5);

		//회원 전체수 : 최고관리자 제외
		$sql = "select count(1) as cnt from work_member where state='0' and companyno='".$companyno."' and highlevel!='1'";
		$mem_auth_info = selectQuery($sql);
		if($mem_auth_info){
			$member_auth_cnt = $mem_auth_info['cnt'];
		}

		//역량 할당된 코인
		//역량 할당 코인(200000), 좋아요 할당코딩(100000)
		$sql = "select cp_coin  from work_com_rule where state='0' and companyno='".$companyno."'";
		$rule_info = selectQuery($sql);
		if($rule_info){
			$cp_coin = $rule_info['cp_coin'];
		}


		//역량 할당된 금액 / 회원전체수
		//역량 개당점수: 10000
		$reward_com_price = @round($cp_coin / $member_auth_cnt);

		//종아요 개당 : 500
		//$reward_like_price = @round($like_coin / $member_auth_cnt);


		//역량 개당 코인점수
		$work_com_gaedang = @round($reward_com_price / $reward_com_jumsu, 2);

		//개당점수
		$total_com_coin = @round($reward_com_int * $work_com_gaedang);

		//달성목표치
		$cp_per = round($reward_com_int / $reward_com_jumsu * 100);


		//$date_tmp = explode("-", TODATE);
		//$month = $date_tmp[0]."-". $date_tmp[1];

		//오늘날짜의 획득한 코인
		$sql = "select idx, cp, coin from work_com_reward where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".$wdate."'";
		$info = selectQuery($sql);
		if(!$info['idx']){
			$sql = "insert into work_com_reward(companyno, email, name, cp, coin, ip, workdate) values(";
			$sql = $sql .= "'".$companyno."','".$user_id."', '".$user_name."', '".$reward_com_int."', '".$total_com_coin."', '".LIP."', '".$wdate."')";
			$insert_idx = insertIdxQuery($sql);
		}else{

			//역량점수
			$info_cp = $info['cp'];

			//업데이트 기준
			//역량점수와 추가역량점수가 다르고, 추가역역량점수가 큰경우
			if($info_cp != $reward_com_int && $reward_com_int > $info_cp){
				$plus_coin = $total_com_coin - $info['coin'];
				$sql = "update work_com_reward set plus_cp='".$reward_com_int."', plus_coin='".$plus_coin."', editdate=getdate() where idx='".$info['idx']."'";
				updateQuery($sql);
			}
		}

		if($cp_coin){
			$result['cp_coin'] = $cp_coin;
			$result['cp_per'] = $cp_per;
			return $result;
		}
	}



	//좋아요 할당코인
	function work_like_reward_wday($user_id, $wdate){

		global $companyno, $user_name, $month_first_day, $month_last_day;


		$member_info_row = member_row_info($user_id);
		$user_name = $member_info_row['name'];

		//획득한 좋아요 점수
		$sql = "select count(1) cnt from work_todaywork_like where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".$wdate."'";
		$like_month_info = selectQuery($sql);
		if($like_month_info['cnt']){
			//좋아요 50%에 해당하는 점수
			$reward_like_int = $like_month_info['cnt'];
		}

		$work_like_int = 180;
		$reward_like_jumsu = @round($work_like_int * 0.3);

		//회원수 : 최고관리자 제외
		$sql = "select count(1) as cnt from work_member where state='0' and companyno='".$companyno."' and highlevel!='1'";
		$mem_auth_info = selectQuery($sql);
		if($mem_auth_info){
			$member_auth_cnt = $mem_auth_info['cnt'];
		}


		//좋아요 할당코딩(100000)
		$sql = "select cp_coin, like_coin from work_com_rule where state='0' and companyno='".$companyno."'";
		$rule_info = selectQuery($sql);
		if($rule_info){
			$like_coin = $rule_info['like_coin'];
		}

		//종아요 개당 : 500
		$reward_like_price = @round($like_coin / $member_auth_cnt);

		//역량 개당 코인점수
		$work_com_gaedang = @round($reward_like_price / $reward_like_jumsu, 2);
		$total_like_coin = @round($reward_like_int * $work_com_gaedang);


		//달성목표치
		$like_per = round($reward_like_int / $reward_like_jumsu * 100);


		//오늘날짜의 획득한 코인
		$sql = "select idx, cp, coin from work_like_reward where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".$wdate."'";
		$info = selectQuery($sql);
		if(!$info['idx']){
			$sql = "insert into work_like_reward(companyno, email, name, cp, coin, ip, workdate) values(";
			$sql = $sql .= "'".$companyno."','".$user_id."', '".$user_name."', '".$reward_like_int."', '".$total_like_coin."', '".LIP."', '".$wdate."')";
			$insert_idx = insertIdxQuery($sql);
		}else{

			//역량점수
			$info_like_cnt = $info['cp'];

			//업데이트 기준
			//역량점수와 추가역량점수가 다르고, 추가역역량점수가 큰경우
			if($info_like_cnt != $reward_like_int && $reward_like_int > $info_like_cnt){

				$plus_coin = $total_like_coin - $info['coin'];
				$sql = "update work_like_reward set plus_cp='".$reward_like_int."', plus_coin='".$plus_coin."', editdate=getdate() where idx='".$info['idx']."'";
				updateQuery($sql);
			}
		}

		if($like_coin){
			$result['like_coin'] = $like_coin;
			$result['like_per'] = $like_per;
			return $result;
		}
	}


	//역량 할당코인_매일갱신
	function work_com_reward_day($user_id){

		global $companyno, $user_name, $month_first_day, $month_last_day, $max_array;

		//어제 날짜
		//$yday = date("Y-m-d", strtotime($day." -1 day"));
		//$yday = TODATE;
		//echo $yday;

		//획득역량점수
		$sql = "select sum(type1) + sum(type2) +sum(type3) + sum(type4) + sum(type5) + sum(type6) as com from work_cp_reward_list";
		$sql = $sql .= " where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
		$work_reward_info = selectQuery($sql);

		if($work_reward_info['com']){
			//역량점수
			$reward_com_int = $work_reward_info['com'];
		}


		//역량합계
		$max_array_sum = array_sum($max_array);

		$work_com_int = $max_array_sum;

		//50%계산
		$reward_com_jumsu = @round($work_com_int * 0.5);

		//회원 전체수 : 최고관리자 제외
		$sql = "select count(1) as cnt from work_member where state='0' and companyno='".$companyno."' and highlevel!='1'";
		$mem_auth_info = selectQuery($sql);
		if($mem_auth_info){
			$member_auth_cnt = $mem_auth_info['cnt'];
		}

		//역량 할당된 코인
		//역량 할당 코인(200000), 좋아요 할당코딩(100000)
		$sql = "select cp_coin  from work_com_rule where state='0' and companyno='".$companyno."'";
		$rule_info = selectQuery($sql);
		if($rule_info){
			$cp_coin = $rule_info['cp_coin'];
		}


		//역량 할당된 금액 / 회원전체수
		//역량 개당점수: 10000
		$reward_com_price = @round($cp_coin / $member_auth_cnt);

		//종아요 개당 : 500
		//$reward_like_price = @round($like_coin / $member_auth_cnt);


		//역량 개당 코인점수
		$work_com_gaedang = @round($reward_com_price / $reward_com_jumsu, 2);

		//개당점수
		$total_com_coin = @round($reward_com_int * $work_com_gaedang);

		//달성목표치
		$cp_per = round($reward_com_int / $reward_com_jumsu * 100);


		//$date_tmp = explode("-", TODATE);
		//$month = $date_tmp[0]."-". $date_tmp[1];

		//오늘날짜의 획득한 코인
		$sql = "select idx, cp, coin from work_com_reward where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
		$info = selectQuery($sql);
		if(!$info['idx']){
			$sql = "insert into work_com_reward(companyno, email, name, cp, coin, ip, workdate) values(";
			$sql = $sql .= "'".$companyno."','".$user_id."', '".$user_name."', '".$reward_com_int."', '".$total_com_coin."', '".LIP."', '".TODATE."')";
			$insert_idx = insertIdxQuery($sql);
		}else{

			//역량점수
			$info_cp = $info['cp'];

			//업데이트 기준
			//역량점수와 추가역량점수가 다르고, 추가역역량점수가 큰경우
			if($info_cp != $reward_com_int && $reward_com_int > $info_cp){
				$plus_coin = $total_com_coin - $info['coin'];
				$sql = "update work_com_reward set plus_cp='".$reward_com_int."', plus_coin='".$plus_coin."', editdate=getdate() where idx='".$info['idx']."'";
				updateQuery($sql);
			}
		}

		if($cp_coin){
			$result['cp_coin'] = $cp_coin;
			$result['cp_per'] = $cp_per;
			return $result;
		}
	}


	//좋아요 할당코인
	function work_like_reward_day($user_id){

		global $companyno, $user_name, $month_first_day, $month_last_day;

		//획득한 좋아요 점수
		$sql = "select count(1) cnt from work_todaywork_like where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
		$like_month_info = selectQuery($sql);
		if($like_month_info['cnt']){
			//좋아요 50%에 해당하는 점수
			$reward_like_int = $like_month_info['cnt'];
		}

		$work_like_int = 180;
		$reward_like_jumsu = @round($work_like_int * 0.3);

		//회원수 : 최고관리자 제외
		$sql = "select count(1) as cnt from work_member where state='0' and companyno='".$companyno."' and highlevel!='1'";
		$mem_auth_info = selectQuery($sql);
		if($mem_auth_info){
			$member_auth_cnt = $mem_auth_info['cnt'];
		}


		//좋아요 할당코딩(100000)
		$sql = "select cp_coin, like_coin from work_com_rule where state='0' and companyno='".$companyno."'";
		$rule_info = selectQuery($sql);
		if($rule_info){
			$like_coin = $rule_info['like_coin'];
		}

		//종아요 개당 : 500
		$reward_like_price = @round($like_coin / $member_auth_cnt);

		//역량 개당 코인점수
		$work_com_gaedang = @round($reward_like_price / $reward_like_jumsu, 2);
		$total_like_coin = @round($reward_like_int * $work_com_gaedang);


		//달성목표치
		$like_per = round($reward_like_int / $reward_like_jumsu * 100);


		//오늘날짜의 획득한 코인
		$sql = "select idx, cp, coin from work_like_reward where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
		$info = selectQuery($sql);
		if(!$info['idx']){
			$sql = "insert into work_like_reward(companyno, email, name, cp, coin, ip, workdate) values(";
			$sql = $sql .= "'".$companyno."','".$user_id."', '".$user_name."', '".$reward_like_int."', '".$total_like_coin."', '".LIP."', '".TODATE."')";
			$insert_idx = insertIdxQuery($sql);
		}else{

			//역량점수
			$info_like_cnt = $info['cp'];

			//업데이트 기준
			//역량점수와 추가역량점수가 다르고, 추가역역량점수가 큰경우
			if($info_like_cnt != $reward_like_int && $reward_like_int > $info_like_cnt){

				$plus_coin = $total_like_coin - $info['coin'];
				$sql = "update work_like_reward set plus_cp='".$reward_like_int."', plus_coin='".$plus_coin."', editdate=getdate() where idx='".$info['idx']."'";
				updateQuery($sql);
			}
		}

		if($like_coin){
			$result['like_coin'] = $like_coin;
			$result['like_per'] = $like_per;
			return $result;
		}

	}



	//메인 좋아요지표(출근1등)
	function main_like_cp_login(){

		global $user_id, $companyno, $user_name, $month_first_day, $month_last_day;

		//오늘날짜
		$day = TODATE;

		//어제 날짜
		$yday = date("Y-m-d", strtotime($day." -1 day"));

		//횟수제한
		$num = 2;

		//출근 1등 조회 및 데이터 삽입
		$sql = "select top 1 idx, email, name from work_member_login where state='0' and companyno='".$companyno."' and workdate='".TODATE."' order by idx asc";
		$attend_info = selectQuery($sql);
		if($attend_info['idx']){
			$attend_email = $attend_info['email'];
			$attend_name = $attend_info['name'];

			$main_type = main_like_reward($companyno, 'main_like', '0001');
			$sql = "select idx from work_main_like where state='0' and companyno='".$companyno."' and kind='login' and type='".$main_type['act']."' and email='".$attend_email."' and workdate='".TODATE."'";
			$data_info = selectQuery($sql);
			if(!$data_info['idx']){
				$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
				$sql = $sql .= "'".$companyno."', '".$attend_email."', '".$attend_name."', 'login', '".$main_type['act']."', '1', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
				$insert_idx = insertIdxQuery($sql);
				if($insert_idx){
					main_member_like();
				}
			}
		}


	}

	//불꽃업무중, 보고업무 최다(현재기준, 실행:type6), 공유업무 최다(현재기준, 에너지:type1), 요청완료 최다(현재기준, 협업:type4)
	function main_like_cp_works($kind){

		global $user_id, $companyno;

		//오늘날짜
		$day = TODATE;

		//어제 날짜
		$yday = date("Y-m-d", strtotime($day." -1 day"));

		//횟수제한
		$num = 2;
		//공유횟수
		$share_num = 1;
		//보고횟수
		$report_num = 1;
		//요청완료횟수
		$work_cnum = 1;

		if($kind == 'works'){

			//불꽃 업무중(오늘업무 최다 작성 1등)
			/*$sql = "select top 1 count(1) as cnt, email, name, workdate from work_todaywork where state!='9' and companyno='".$companyno."' and workdate='".TODATE."'";
			$sql = $sql .= " and notice_flag='0' and decide_flag='0' and repeat_flag=0";
			$sql = $sql .= " group by email, name, workdate order by count(1) desc";*/

			$sql = "select top 1 idx, email, name, work_cnt from work_todaywork_realtime where state='0'";
			$sql = $sql .= " and companyno='".$companyno."' and work_flag='works' and kind_flag='0' and workdate='".TODATE."' order by work_cnt desc";
			$work_info = selectQuery($sql);
			if($work_info['email']){
				$work_email = $work_info['email'];
				$work_name = $work_info['name'];
				$work_cnt = $work_info['work_cnt'];

				$main_type = main_like_reward($companyno, 'main_like', '0006');
				$sql = "select idx, email from work_main_like where state='0' and companyno='".$companyno."' and kind='works' and type='".$main_type['act']."' and workdate='".TODATE."'";
				$data_info = selectAllQuery($sql);
				if($data_info['idx']){

					//데이터 삭제 처리
					for($i=0; $i<count($data_info['idx']); $i++){
						$sql = "update work_main_like set state='9', editdate=".DBDATE." where idx='".$data_info['idx'][$i]."'";
						$res = updateQuery($sql);
						if($res){
							$sql = "update work_todaywork_main_like set state='9', editdate=".DBDATE." where state='0' and work_idx='".$data_info['idx'][$i]."'";
							$res = updateQuery($sql);
						}
					}

					//데이터 삽입
					$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
					$sql = $sql .= "'".$companyno."', '".$work_email."', '".$work_name."', 'works', '".$main_type['act']."', '".$work_cnt."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
					$insert_idx = insertIdxQuery($sql);
					if($insert_idx){
						main_member_like_add($insert_idx,'works');
					}


				}else{
					$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
					$sql = $sql .= "'".$companyno."', '".$work_email."', '".$work_name."', 'works', '".$main_type['act']."', '".$work_cnt."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
					$insert_idx = insertIdxQuery($sql);
					if($insert_idx){
						main_member_like_add($insert_idx,'works');
					}
				}
			}
		}else if($kind == 'report'){

			//보고업무 등록시 실시간 반영(1회이상)

			$sql = "select top 1 companyno, email, name, workdate from work_todaywork where state!='9' and work_flag='1'";
			$sql = $sql .=" and work_idx is null and companyno='".$companyno."' and workdate='".TODATE."'";
			$sql = $sql .= " order by regdate desc";
			$report_info = selectQuery($sql);

			if($report_info['email']){
				$report_companyno = $report_info['companyno'];
				$report_email = $report_info['email'];
				$report_name = $report_info['name'];

				$sql = "select count(1) as cnt from work_todaywork where state!='9'";
				$sql = $sql .=" and companyno='".$report_companyno."' and work_flag='1' and email='".$report_email."' and workdate='".TODATE."'";
				$report_cnt_info = selectQuery($sql);

				$report_cnt = $report_cnt_info['cnt'];

				if($report_cnt >= $report_num){
					$main_type = main_like_reward($report_companyno, 'main_like', '0004');
					$sql = "select idx from work_main_like where state='0' and companyno='".$report_companyno."' and kind='report' and type='".$main_type['act']."' and workdate='".TODATE."'";
					$data_info = selectAllQuery($sql);
					if($data_info['idx']){
						//데이터 삭제 처리
						for($i=0; $i<count($data_info['idx']); $i++){
							$sql = "update work_main_like set state='9', editdate=".DBDATE." where idx='".$data_info['idx'][$i]."'";
							$res = updateQuery($sql);
							if($res){
								$sql = "update work_todaywork_main_like set state='9', editdate=".DBDATE." where state='0' and work_idx='".$data_info['idx'][$i]."'";
								$res = updateQuery($sql);
							}
						}

						//데이터 삽입
						$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
						$sql = $sql .= "'".$report_companyno."', '".$report_email."', '".$report_name."', 'report', '".$main_type['act']."', '".$report_cnt."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);
						if($insert_idx){
							main_member_like_add($insert_idx,'report');
						}


					}else{
						$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
						$sql = $sql .= "'".$report_companyno."', '".$report_email."', '".$report_name."', 'report', '".$main_type['act']."', '".$report_cnt."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);
						if($insert_idx){
							main_member_like_add($insert_idx,'report');
						}
					}
				}
			}
		}else if($kind == 'share'){

			//공유업무 등록시 실시간 반영(1회이상)

			$sql = "select top 1 companyno, email, name, workdate from work_todaywork where state!='9'";
			$sql = $sql .=" and companyno='".$companyno."' and share_flag='1' and workdate='".TODATE."'";
			$sql = $sql .= " order by regdate desc";
			$share_info = selectQuery($sql);

			if($share_info['email']){
				$share_companyno = $share_info['companyno'];
				$share_email = $share_info['email'];
				$share_name = $share_info['name'];

				$sql = "select count(1) as cnt from work_todaywork where state!='9'";
				$sql = $sql .=" and companyno='".$share_companyno."' and share_flag='1' and email='".$share_email."' and workdate='".TODATE."'";
				$share_cnt_info = selectQuery($sql);

				$share_cnt = $share_cnt_info['cnt'];

				if($share_cnt >= $share_num){
					$main_type = main_like_reward($share_companyno, 'main_like', '0003');
					$sql = "select idx from work_main_like where state='0' and companyno='".$share_companyno."' and kind='share' and type='".$main_type['act']."' and workdate='".TODATE."'";

					$data_info = selectAllQuery($sql);
					if($data_info['idx']){

						//데이터 삭제 처리
						for($i=0; $i<count($data_info['idx']); $i++){
							$sql = "update work_main_like set state='9', editdate=".DBDATE." where idx='".$data_info['idx'][$i]."'";
							$res = updateQuery($sql);
							if($res){
								$sql = "update work_todaywork_main_like set state='9', editdate=".DBDATE." where state='0' and work_idx='".$data_info['idx'][$i]."'";
								$res = updateQuery($sql);
							}
						}

						//데이터 삽입
						$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
						$sql = $sql .= "'".$share_companyno."', '".$share_email."', '".$share_name."', 'share', '".$main_type['act']."', '".$share_cnt."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);
						if($insert_idx){
							main_member_like_add($insert_idx,'share');
						}


					}else{
						$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
						$sql = $sql .= "'".$share_companyno."', '".$share_email."', '".$share_name."', 'share', '".$main_type['act']."', '".$share_cnt."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);
						if($insert_idx){
							main_member_like_add($insert_idx,'share');
						}
					}
				}
			}
		}else if($kind == 'works_complete'){

			//요청완료(오늘요청 1회이상 반영)

			$sql ="select top 1 companyno, email, name, workdate from work_todaywork where state=1 and work_flag='3'";
			$sql = $sql .=" and workdate='".TODATE."' and work_idx is not null order by regdate desc";
			$work_c_info = selectQuery($sql);

			if($work_c_info['email']){
				$work_c_companyno = $work_c_info['companyno'];
				$work_c_email = $work_c_info['email'];
				$work_c_name = $work_c_info['name'];

				$sql = "select count(1) as cnt from work_todaywork where state=1 and work_flag='3' and companyno='".$work_c_companyno."'";
				$sql = $sql .= " and email='".$work_c_email."' and workdate='".TODATE."' and work_idx is not null";
				$work_c_cnt_info = selectQuery($sql);

				$work_c_cnt = $work_c_cnt_info['cnt'];

				if($work_c_cnt >= $work_cnum){
					$main_type = main_like_reward($work_c_companyno, 'main_like', '0013');
					$sql = "select idx,email from work_main_like where companyno='".$work_c_companyno."' and state = 0 and kind='works_complete'";
					$sql = $sql .=" and type='".$main_type['act']."' and workdate = '".TODATE."'";

					$data_info = selectAllQuery($sql);

					if($data_info['idx']){
						//데이터 삭제 처리
						for($i=0; $i<count($data_info['idx']); $i++){
							$sql = "update work_main_like set state='9', editdate=".DBDATE." where idx='".$data_info['idx'][$i]."'";
							$res = updateQuery($sql);

							if($res){
								$sql = "update work_todaywork_main_like set state='9', editdate=".DBDATE." where state='0' and work_idx='".$data_info['idx'][$i]."'";
								$res = updateQuery($sql);
							}

							//데이터 삽입
							$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
							$sql = $sql .= "'".$work_c_companyno."', '".$work_c_email."', '".$work_c_name."', 'works_complete', '".$main_type['act']."', '".$work_c_cnt."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";

							$insert_idx = insertIdxQuery($sql);
							if($insert_idx){
								main_member_like_add($insert_idx,'works_complete');
							}
						}
					}else{
						//데이터 삽입
						$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
						$sql = $sql .= "'".$work_c_companyno."', '".$work_c_email."', '".$work_c_name."', 'works_complete', '".$main_type['act']."', '".$work_c_cnt."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);

						if($insert_idx){
							main_member_like_add($insert_idx,'works_complete');
						}
					}
				}
			}
		}
	}

	//메인 좋아요 지표
	function main_like_cp(){

		global $user_id, $companyno, $user_name, $month_first_day, $month_last_day;

		//if($user_id=='sadary0@nate.com'){

			//오늘날짜
			$day = TODATE;

			//어제 날짜
			$yday = date("Y-m-d", strtotime($day." -1 day"));

			//일자를 숫자로 표현 > 0:일요일, 1:월요일, 2:화요일, 3:수요일, 4:목요일, 5:금요일, 6:토요일
			$yoil_int = date('w', strtotime($yday));

			//전날이 일요일이면 금요일 데이터 추출
			if($yoil_int == '0'){
				$yday = date("Y-m-d", strtotime($yday." -2 day"));

			//전날이 토요일이면 금요일 데이터 추출
			}else if($yoil_int == '6'){
				$yday = date("Y-m-d", strtotime($yday." -1 day"));
			}

			//횟수제한
			$num = 2;
			//메모횟수
			$memo_num = 2;
			//공유횟수
			//$share_num = 1;
			//보고횟수
			//$report_num = 1;
			//좋아요보내기 횟수
			$like_num = 3;

			//에너지 type1, //성과 type2, //성장 type3, //협업 type4, //성실 type5, //실행 type6
			//성과 급등중(전일기준, 성과:type2)
			/*
			$sql = "select top 1 a.companyno, sum(type2) as type2, (select sum(type2) from work_cp_reward_list where state='0' and type2='1' and workdate between '".$month_first_day."' and '".$month_last_day."' and email=a.email and companyno=a.companyno) as cnt_total";
			$sql = $sql .= ", email, name from work_cp_reward_list as a where a.state='0'";
			$sql = $sql .= " and a.type2='1' and a.workdate='".$yday."'";
			$sql = $sql .= " group by a.companyno, a.email, a.name, a.workdate";
			$sql = $sql .= " order by sum(type2) desc, (select sum(type2) from work_cp_reward_list where state='0' and type2='1' and workdate between '".$month_first_day."' and '".$month_last_day."' and email=a.email and companyno=a.companyno) desc";
			write_log_dir($sql , "update");
			$cp_reward_info = selectAllQuery($sql);

			if($cp_reward_info['email']){

				for($i=0; $i<count($cp_reward_info['email']); $i++){
					$cp_reward_companyno = $cp_reward_info['companyno'][$i];
					$cp_reward_email = $cp_reward_info['email'][$i];
					$cp_reward_name = $cp_reward_info['name'][$i];
					$cp_reward_cnt = $cp_reward_info['type2'][$i];
					$cp_reward_cnt_total = $cp_reward_info['cnt_total'][$i];

					$main_type = main_like_reward($cp_reward_companyno, 'main_like', '0012');
					$sql = "select idx from work_main_like where state='0' and companyno='".$cp_reward_companyno."' and kind='cp' and type='".$main_type['act']."' and email='".$cp_reward_email."' and workdate='".TODATE."'";
					$data_info = selectQuery($sql);
					if(!$data_info['idx']){
						$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, cnt_total, memo, ip, workdate) values(";
						$sql = $sql .= "'".$cp_reward_companyno."', '".$cp_reward_email."', '".$cp_reward_name."', 'cp', '".$main_type['act']."', '".$cp_reward_cnt."', '".$cp_reward_cnt_total."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);
					}
				}
			}
			*/

			//협업 급등중(전일기준, 협업:type4)
			/*
			$sql = "select top 1 a.companyno, sum(type4) as type4, (select sum(type4) from work_cp_reward_list where state='0' and type4='1' and workdate between '".$month_first_day."' and '".$month_last_day."' and email=a.email and companyno=a.companyno) as cnt_total";
			$sql = $sql .= ", email, name from work_cp_reward_list as a where a.state='0'";
			$sql = $sql .= " and a.type4='1' and a.workdate='".$yday."'";
			$sql = $sql .= " group by a.companyno, a.email, a.name, a.workdate";
			$sql = $sql .= " order by sum(type4) desc, (select sum(type4) from work_cp_reward_list where state='0' and type4='1' and workdate between '".$month_first_day."' and '".$month_last_day."' and email=a.email and companyno=a.companyno) desc";
			write_log_dir($sql , "update");
			$cp_reward_info = selectAllQuery($sql);
			if($cp_reward_info['email']){
				for($i=0; $i<count($cp_reward_info['email']); $i++){
					$cp_reward_companyno = $cp_reward_info['companyno'][$i];
					$cp_reward_email = $cp_reward_info['email'][$i];
					$cp_reward_name = $cp_reward_info['name'][$i];
					$cp_reward_cnt = $cp_reward_info['type4'][$i];
					$cp_reward_cnt_total = $cp_reward_info['cnt_total'][$i];

					$main_type = main_like_reward($cp_reward_companyno, 'main_like', '0011');
					$sql = "select idx from work_main_like where state='0' and companyno='".$cp_reward_companyno."' and kind='cp' and type='".$main_type['act']."' and email='".$cp_reward_email."' and workdate='".TODATE."'";
					$data_info = selectQuery($sql);
					if(!$data_info['idx']){
						$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, cnt_total, memo, ip, workdate) values(";
						$sql = $sql .= "'".$cp_reward_companyno."', '".$cp_reward_email."', '".$cp_reward_name."', 'cp', '".$main_type['act']."', '".$cp_reward_cnt."', '".$cp_reward_cnt_total."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);
					}
				}
			}
			*/

			//실행 급등중(전일기준, 실행:type6)
			/*
			$sql = "select top 1 a.companyno, sum(type6) as type6, (select sum(type6) from work_cp_reward_list where state='0' and type6='1' and workdate between '".$month_first_day."' and '".$month_last_day."' and email=a.email and companyno=a.companyno) as cnt_total";
			$sql = $sql .= ", email, name from work_cp_reward_list as a where a.state='0'";
			$sql = $sql .= " and a.type6='1' and a.workdate='".$yday."'";
			$sql = $sql .= " group by a.companyno, a.email, a.name, a.workdate";
			$sql = $sql .= " order by sum(type6) desc, (select sum(type6) from work_cp_reward_list where state='0' and type6='1' and workdate between '".$month_first_day."' and '".$month_last_day."' and email=a.email and companyno=a.companyno) desc";
			write_log_dir($sql , "update");
			$cp_reward_info = selectAllQuery($sql);
			if($cp_reward_info['email']){
				for($i=0; $i<count($cp_reward_info['email']); $i++){
					$cp_reward_companyno = $cp_reward_info['companyno'][$i];
					$cp_reward_email = $cp_reward_info['email'][$i];
					$cp_reward_name = $cp_reward_info['name'][$i];
					$cp_reward_cnt = $cp_reward_info['type6'][$i];
					$cp_reward_cnt_total = $cp_reward_info['cnt_total'][$i];

					$main_type = main_like_reward($cp_reward_companyno, 'main_like', '0010');
					$sql = "select idx from work_main_like where state='0' and companyno='".$cp_reward_companyno."' and kind='cp' and type='".$main_type['act']."' and email='".$cp_reward_email."' and workdate='".TODATE."'";
					$data_info = selectQuery($sql);
					if(!$data_info['idx']){
						$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, cnt_total, memo, ip, workdate) values(";
						$sql = $sql .= "'".$cp_reward_companyno."', '".$cp_reward_email."', '".$cp_reward_name."', 'cp', '".$main_type['act']."', '".$cp_reward_cnt."', '".$cp_reward_cnt_total."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);
					}
				}
			}
			*/

			/*
			//성실 급등중(전일기준, 실행:type5)
			$sql = "select top 1 a.companyno, sum(type5) as type5, (select sum(type5) from work_cp_reward_list where state='0' and type5='1' and workdate between '".$month_first_day."' and '".$month_last_day."' and email=a.email and companyno=a.companyno) as cnt_total";
			$sql = $sql .= ", email, name from work_cp_reward_list as a where a.state='0'";
			$sql = $sql .= " and a.type5='1' and a.workdate='".$yday."'";
			$sql = $sql .= " group by a.companyno, a.email, a.name, a.workdate";
			$sql = $sql .= " order by sum(type5) desc, (select sum(type5) from work_cp_reward_list where state='0' and type5='1' and workdate between '".$month_first_day."' and '".$month_last_day."' and email=a.email and companyno=a.companyno) desc";
			write_log_dir($sql , "update");
			$cp_reward_info = selectAllQuery($sql);
			if($cp_reward_info['email']){
				for($i=0; $i<count($cp_reward_info['email']); $i++){
					$cp_reward_companyno = $cp_reward_info['companyno'][$i];
					$cp_reward_email = $cp_reward_info['email'][$i];
					$cp_reward_name = $cp_reward_info['name'][$i];
					$cp_reward_cnt = $cp_reward_info['type5'][$i];
					$cp_reward_cnt_total = $cp_reward_info['cnt_total'][$i];

					$main_type = main_like_reward($cp_reward_companyno, 'main_like', '0009');
					$sql = "select idx from work_main_like where state='0' and companyno='".$cp_reward_companyno."' and kind='cp' and type='".$main_type['act']."' and email='".$cp_reward_email."' and workdate='".TODATE."'";
					$data_info = selectQuery($sql);
					if(!$data_info['idx']){
						$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, cnt_total, memo, ip, workdate) values(";
						$sql = $sql .= "'".$cp_reward_companyno."', '".$cp_reward_email."', '".$cp_reward_name."', 'cp', '".$main_type['act']."', '".$cp_reward_cnt."', '".$cp_reward_cnt_total."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);
					}
				}
			}
			*/

			//성장 급등중(전일기준, 실행:type3)
			/*
			$sql = "select top 1 a.companyno, sum(type3) as type3, (select sum(type3) from work_cp_reward_list where state='0' and type3='1' and workdate between '".$month_first_day."' and '".$month_last_day."' and email=a.email and companyno=a.companyno) as cnt_total";
			$sql = $sql .= ", email, name from work_cp_reward_list as a where a.state='0'";
			$sql = $sql .= " and a.type3='1' and a.workdate='".$yday."'";
			$sql = $sql .= " group by a.companyno, a.email, a.name, a.workdate";
			$sql = $sql .= " order by sum(type3) desc, (select sum(type3) from work_cp_reward_list where state='0' and type3='1' and workdate between '".$month_first_day."' and '".$month_last_day."' and email=a.email and companyno=a.companyno) desc";
			write_log_dir($sql , "update");
			$cp_reward_info = selectAllQuery($sql);
			if($cp_reward_info['email']){
				for($i=0; $i<count($cp_reward_info['email']); $i++){
					$cp_reward_companyno = $cp_reward_info['companyno'][$i];
					$cp_reward_email = $cp_reward_info['email'][$i];
					$cp_reward_name = $cp_reward_info['name'][$i];
					$cp_reward_cnt = $cp_reward_info['type3'][$i];
					$cp_reward_cnt_total = $cp_reward_info['cnt_total'][$i];
					$main_type = main_like_reward($cp_reward_companyno, 'main_like', '0008');
					$sql = "select idx from work_main_like where state='0' and companyno='".$cp_reward_companyno."' and kind='cp' and type='".$main_type['act']."' and email='".$cp_reward_email."' and workdate='".TODATE."'";
					$data_info = selectQuery($sql);
					if(!$data_info['idx']){
						$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, cnt_total, memo, ip, workdate) values(";
						$sql = $sql .= "'".$cp_reward_companyno."', '".$cp_reward_email."', '".$cp_reward_name."', 'cp', '".$main_type['act']."', '".$cp_reward_cnt."', '".$cp_reward_cnt_total."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);
					}
				}
			}
			*/


			//에너지 급등중(전일기준, 에너지:type1)
			/*
			$sql = "select top 1 a.companyno, sum(type1) as type1, (select sum(type1) from work_cp_reward_list where state='0' and type1='1' and workdate between '".$month_first_day."' and '".$month_last_day."' and email=a.email and companyno=a.companyno) as cnt_total";
			$sql = $sql .= ", email, name from work_cp_reward_list as a where a.state='0'";
			$sql = $sql .= " and a.type1='1' and a.workdate='".$yday."'";
			$sql = $sql .= " group by a.companyno, a.email, a.name, a.workdate";
			$sql = $sql .= " order by sum(type1) desc, (select sum(type1) from work_cp_reward_list where state='0' and type1='1' and workdate between '".$month_first_day."' and '".$month_last_day."' and email=a.email and companyno=a.companyno) desc";
			write_log_dir($sql , "update");
			$cp_reward_info = selectAllQuery($sql);
			if($cp_reward_info['email']){
				for($i=0; $i<count($cp_reward_info['email']); $i++){
					$cp_reward_companyno = $cp_reward_info['companyno'][$i];
					$cp_reward_email = $cp_reward_info['email'][$i];
					$cp_reward_name = $cp_reward_info['name'][$i];
					$cp_reward_cnt = $cp_reward_info['type1'][$i];
					$cp_reward_cnt_total = $cp_reward_info['cnt_total'][$i];

					$main_type = main_like_reward($cp_reward_companyno, 'main_like', '0007');
					$sql = "select idx from work_main_like where state='0' and companyno='".$cp_reward_companyno."' and kind='cp' and type='".$main_type['act']."' and email='".$cp_reward_email."' and workdate='".TODATE."'";
					$data_info = selectQuery($sql);
					if(!$data_info['idx']){
						$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, cnt_total, memo, ip, workdate) values(";
						$sql = $sql .= "'".$cp_reward_companyno."', '".$cp_reward_email."', '".$cp_reward_name."', 'cp', '".$main_type['act']."', '".$cp_reward_cnt."', '".$cp_reward_cnt_total."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);
					}
				}
			}
			*/

			//좋아요 보내기 1등(전일기준, 에너지:type1)
			$sql = "select top 1 companyno, count(1) as cnt, send_email, send_name, workdate from work_todaywork_like where state='0' and workdate='".$yday."'";
			$sql = $sql .= " group by companyno, send_email, send_name, workdate having(count(1) > ".$like_num.") order by count(1) desc";
			write_log_dir($sql , "update");
			$like_info = selectQuery($sql);
			if($like_info['send_email']){
				$like_send_companyno = $like_info['companyno'];
				$like_send_email = $like_info['send_email'];
				$like_send_name = $like_info['send_name'];
				$like_cnt = $like_info['cnt'];

				$main_type = main_like_reward($like_send_companyno, 'main_like', '0005');
				$sql = "select idx from work_main_like where state='0' and companyno='".$like_send_companyno."' and kind='like' and type='".$main_type['act']."' and email='".$like_send_email."' and workdate='".TODATE."'";
				$data_info = selectQuery($sql);
				if(!$data_info['idx']){
					$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
					$sql = $sql .= "'".$like_send_companyno."', '".$like_send_email."', '".$like_send_name."', 'like', '".$main_type['act']."', '".$like_cnt."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
					$insert_idx = insertIdxQuery($sql);
				}
			}

			//보고업무 최다(전일기준, 실행:type6)
			/*
			$sql = "select top 1 companyno, count(1) as cnt, email, name, workdate from work_todaywork where state!='9' and work_flag='1' and work_idx is null and workdate='".$yday."'";
			$sql = $sql .= " group by companyno, email, name, workdate having(count(1) > ".$report_num.") order by count(1) desc";
			write_log_dir($sql , "update");
			$report_info = selectQuery($sql);
			if($report_info['email']){
				$report_companyno = $report_info['companyno'];
				$report_email = $report_info['email'];
				$report_name = $report_info['name'];
				$report_cnt = $report_info['cnt'];

				$main_type = main_like_reward($report_companyno, 'main_like', '0004');
				$sql = "select idx from work_main_like where state='0' and companyno='".$report_companyno."' and kind='report' and type='".$main_type['act']."' and email='".$report_email."' and workdate='".TODATE."'";
				$data_info = selectQuery($sql);
				if(!$data_info['idx']){
					$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
					$sql = $sql .= "'".$report_companyno."', '".$report_email."', '".$report_name."', 'report', '".$main_type['act']."', '".$report_cnt."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
					$insert_idx = insertIdxQuery($sql);
				}
			}
			*/

			//공유최다(전일기준, 에너지:type1)
			/*
			$sql = "select top 1 companyno, count(1) as cnt, email, name, workdate from work_todaywork where state!='9' and share_flag='1' and workdate='".$yday."'";
			$sql = $sql .= " group by companyno, email, name, workdate having(count(1) > ".$share_num.") order by count(1) desc";
			write_log_dir($sql , "update");
			$share_info = selectQuery($sql);
			if($share_info['email']){
				$share_companyno = $share_info['companyno'];
				$share_email = $share_info['email'];
				$share_name = $share_info['name'];
				$share_cnt = $share_info['cnt'];

				$main_type = main_like_reward($share_companyno, 'main_like', '0003');
				$sql = "select idx from work_main_like where state='0' and companyno='".$share_companyno."' and kind='share' and type='".$main_type['act']."' and email='".$share_email."' and workdate='".TODATE."'";
				$data_info = selectQuery($sql);
				if(!$data_info['idx']){
					$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
					$sql = $sql .= "'".$share_companyno."', '".$share_email."', '".$share_name."', 'share', '".$main_type['act']."', '".$share_cnt."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
					$insert_idx = insertIdxQuery($sql);
				}
			}
			*/

			//메모최다(전일기준, 협업:type4)
			$sql = "select top 1 a.companyno, count(1) as cnt, b.email, b.name, b.workdate from work_todaywork as a left join work_todaywork_comment as b on(a.email=b.email) where";
			$sql = $sql .= " a.state!=9 and a.idx=b.work_idx and a.work_idx is not null and a.idx!=a.work_idx";
			$sql = $sql .= " and b.workdate='".$yday."'";
			$sql = $sql .= " group by a.companyno, b.email, b.name, b.workdate having(count(1) > ".$memo_num.") order by count(1) desc";
			write_log_dir($sql , "update");
			$memo_info = selectQuery($sql);
			if($memo_info['email']){
				$memo_companyno = $memo_info['companyno'];
				$memo_email = $memo_info['email'];
				$memo_name = $memo_info['name'];
				$memo_cnt = $memo_info['cnt'];

				$main_type = main_like_reward($memo_companyno, 'main_like', '0002');
				$sql = "select idx from work_main_like where state='0' and companyno='".$memo_companyno."' and kind='memo' and type='".$main_type['act']."' and email='".$memo_email."' and workdate='".TODATE."'";
				$data_info = selectQuery($sql);
				if(!$data_info['idx']){
					$sql = "insert into work_main_like(companyno, email, name, kind, type, cnt, memo, ip, workdate) values(";
					$sql = $sql .= "'".$memo_companyno."', '".$memo_email."', '".$memo_name."', 'memo', '".$main_type['act']."', '".$memo_cnt."', '".$main_type['act_title']."', '".LIP."', '".TODATE."')";
					$insert_idx = insertIdxQuery($sql);
				}
			}

			main_member_like();

		//}
	}

	//좋아요 보내기 회원별로 등록
	function main_member_like(){

		global $companyno;
		//회원전체 목록
		$member_list_info = member_alist_info();

		if(!$companyno){
			$companyno = 1;
		}
		//메인 좋아요 지표
		$sql = "select idx, email from work_main_like where state='0' and companyno='".$companyno."' and workdate='".TODATE."'";
		$main_like_info = selectAllQuery($sql);
		for($i=0; $i<count($main_like_info['idx']); $i++){

			$main_like_info_idx = $main_like_info['idx'][$i];

			if($member_list_info['email']){
				for($j=0; $j<count($member_list_info['email']); $j++){
					$mem_companyno = $member_list_info['companyno'][$j];
					$mem_id = $member_list_info['email'][$j];
					$mem_name = $member_list_info['name'][$j];

					$sql = "select idx, email, name from work_todaywork_main_like where state='0' and companyno='".$mem_companyno."' and work_idx='".$main_like_info_idx."' and email='".$mem_id."' and workdate='".TODATE."'";
					$main_like_row = selectQuery($sql);
					if(!$main_like_row['idx']){

						$sql = "insert into work_todaywork_main_like(work_idx, companyno, email, name, ip, workdate)";
						$sql = $sql .= " values('".$main_like_info_idx."', '".$mem_companyno."', '".$mem_id."', '".$mem_name."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);
					}
				}
			}
		}
	}


	//추가로 등록된 업무에 대한 사용자별 등록
	function main_member_like_add($idx,$kind){

		if($idx){
			global $companyno, $user_id;
			//회원전체 목록
			$member_list_info = member_alist_info();

			if(!$companyno){
				$companyno = 1;
			}

			//메인 좋아요 불꽃업무 지표
			$sql = "select idx from work_todaywork_main_like where state='0' and companyno='".$companyno."' and workdate='".TODATE."'";
			$sql = $sql." and work_idx = '".$idx."'";
			$main_like_work_info = selectAllQuery($sql);
			if(!$main_like_work_info['idx']){
				$sql = "select idx, email from work_main_like where state='0' and companyno='".$companyno."' and idx='".$idx."' and workdate='".TODATE."'";
				$main_like_info = selectAllQuery($sql);
				for($i=0; $i<count($main_like_info['idx']); $i++){

					$main_like_info_idx = $main_like_info['idx'][$i];

					if($member_list_info['email']){
						for($j=0; $j<count($member_list_info['email']); $j++){
							$mem_companyno = $member_list_info['companyno'][$j];
							$mem_id = $member_list_info['email'][$j];
							$mem_name = $member_list_info['name'][$j];

							$sql = "select idx, email, name from work_todaywork_main_like where state='0' and companyno='".$mem_companyno."' and work_idx='".$main_like_info_idx."' and email='".$mem_id."' and workdate='".TODATE."'";
							$main_like_row = selectQuery($sql);
							if(!$main_like_row['idx']){

								$sql = "insert into work_todaywork_main_like(work_idx, companyno, email, name, ip, workdate)";
								$sql = $sql .= " values('".$main_like_info_idx."', '".$mem_companyno."', '".$mem_id."', '".$mem_name."', '".LIP."', '".TODATE."')";
								$insert_idx = insertIdxQuery($sql);
							}
						}
					}
				}
			}
		}
	}


	//에너지 type1, //성과 type2, //성장 type3, //협업 type4, //성실 type5, //실행 type6
	//메인 좋아요 -> 역량적용
	function main_like_reward($companyno, $service, $act){

		$sql = "select idx, act_title, type1, type2, type3, type4, type5, type6 from work_cp_reward where state='0' and companyno='".$companyno."' and service='".$service."' and act='".$act."'";
		$info = selectQuery($sql);
		if($info['idx']){
			$act_title = $info['act_title'];

			if ($info['type1']){
				$return['act'] = "0001";
				$return['act_title'] = $act_title;
			}else if ($info['type2']){
				$return['act'] = "0002";
				$return['act_title'] = $act_title;
			}else if ($info['type3']){
				$return['act'] = "0003";
				$return['act_title'] = $act_title;
			}else if ($info['type4']){
				$return['act'] = "0004";
				$return['act_title'] = $act_title;
			}else if ($info['type5']){
				$return['act'] = "0005";
				$return['act_title'] = $act_title;
			}else if ($info['type6']){
				$return['act'] = "0006";
				$return['act_title'] = $act_title;
			}


			return $return;
		}
	}


	//검색어 하이라이트 처리
	function keywordHightlight($search, $word) {
		if($search){
			$high_light = "<strong style='background-color:#FFFD42;'>".$search."</strong>";
			$str = str_replace($search, $high_light, $word);
			return $str; // 결과 값을 리턴해준다.
		}
	}

	//실시간 업무수 체크 , 업무종류(오늘업무, 보고,공유,요청), 구분(보냄:1,받음:1), 아이디, 이름
	function works_realtime($work_flag, $type, $kind_flag, $uid, $uname, $workdate){
		global $companyno;

		$sql = "select idx from work_todaywork_realtime where state='0' and companyno='".$companyno."' and work_flag='".$work_flag."' and kind_flag='".$kind_flag."' and email='".$uid."' and workdate='".$workdate."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){

			//추가
			if($type =='add'){
				$sql = "update work_todaywork_realtime set work_cnt=work_cnt + 1 where idx='".$work_info['idx']."'";
				$res = updateQuery($sql);
			//삭제
			}else if($type =='del'){
				$sql = "update work_todaywork_realtime set work_cnt=case when work_cnt>0 then work_cnt-1 else 0 end where idx='".$work_info['idx']."'";
				$res = updateQuery($sql);
			}
		}else{
			$sql = "insert into work_todaywork_realtime(companyno, email, name, work_flag, kind_flag, work_cnt, workdate)";
			$sql = $sql .=" values('".$companyno."', '".$uid."', '".$uname."', '".$work_flag."', '".$kind_flag."', '1', '".$workdate."')";
			insertIdxQuery($sql);
		}
	}
?>
