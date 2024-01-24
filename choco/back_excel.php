<?
	// //DB연결
    // $conn = mysqli_connect("localhost", "root", "root", "todaywork", "3306") or die("db connect fail");
	// $conn = mysqli_connect("10.17.239.90", "todaywork", "work2021%%^^", "todaywork", "3306") or die("db connect fail");
    // include "/inc_lude/func_mysqli.php";
    $home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
    include $home_dir . "/inc_lude/conf_mysqli.php";
	include DBCON_MYSQLI;
	include FUNC_MYSQLI;
    
    header( "Content-type: application/vnd.ms-excel; charset=utf-8");
    header( "Content-Disposition: attachment; filename = BackOffice_Excel.xls" );     //filename = 저장되는 파일명을 설정합니다.
    header( "Content-Description: PHP4 Generated Data" );

    $type = $_GET['type'];
    $tclass = $_GET['tclass'];
    $kind = $_GET['kind'];
    $sdate = $_GET['sdate'];
    $edate = $_GET['edate'];
    $search = $_GET['search'];
    $code = $_GET['code'];
    $p = $_GET['page'];
    $list = $_GET['list'];

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}
	//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$where = "";

    if($type == "backuser_list"){

        if($kind){
            $sort = $kind;
        }else{
            $sort = " idx";
        }

        if($code=='0'){
            $where = $where .= " where state = '0'";
        }else if($code == '1'){
            $where = $where .= " where state = '1'";
        }
        
        if($search){
            $where = $where .= " and (name like '%".$search."%' or company like '%".$search."%' or email like '%".$search."%' )";
        }

        $sql = "select idx, regdate, name, email, company, comcoin, coin, login_count, login_date from work_member".$where." order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
        $query = selectAllQuery($sql);
        
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";

        ?>
        
        <table border='1'>
            <tr>
                <td>idx</td>
                <td>이름</td>
                <td>ID</td>
                <td>회사</td>
                <td>공용코인</td>
                <td>코인</td>
                <td>누적 로그인</td>
                <td>로그인 시간</td>
            </tr>
        <? for($i=0; $i<count($query['idx']); $i++){?>
                <tr>
                <td><?=$query['idx'][$i]?></td>
                <td><?=$query['name'][$i]?></td>
                <td><?=$query['email'][$i]?></td>
                <td><?=$query['company'][$i]?></td>
                <td><?=$query['comcoin'][$i]?></td>
                <td><?=$query['coin'][$i]?></td>
                <td><?=$query['login_count'][$i]?></td>
                <td><?=$query['login_date'][$i]?></td>
            </tr>
        <?}?>
        </table>
    <? }

    if($type == "backpenalty_list"){
        if($kind == "company"){
            $sort = $kind;
        }else{
            $sort = "p.idx";
        }
        
        if($code=="all"){
            $where = $where .= "";
        }else{
            $where = $where .= " and p.".$code. "= '1'";
        }

        if($search){
            $where = $where .= " and (p.email like '%".$search."%' or p.name like '%".$search."%' or wc.company like '%".$search."%')";
        }

        if($sdate && $edate){
            $where = $where .= "and (DATE_FORMAT(p.updatetime, '%Y-%m-%d') >= '".$sdate."' and DATE_FORMAT(p.updatetime, '%Y-%m-%d') <= '".$edate."')";
        }

        $sql = "select p.idx, p.email, p.companyno, p.state, p.name, p.incount, p.outcount, p.work, p.challenge, p.updatetime, wc.company from work_member_penalty as p, work_company as wc where p.companyno = wc.idx ".$where." order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
        $query = selectAllQuery($sql);
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        ?>
        
        <table border='1'>
            <tr>
                <td>회사</td>
                <td>이름</td>
                <td>이메일</td>
                <td>페널티 유형</td>
                <td>5일 경고회수</td>
                <td>경고 시간</td>
                <td>상태</td>
            </tr>
        <?
        for($i=0; $i<count($query['idx']); $i++){
            if($query['incount'][$i]=='1'){
                $penalty_name = '출근 시간 미준수';
                $sql = "select sum(incount) as pen_cnt from work_member_penalty where incount = '1' and email = '".$query['email'][$i]."' and state = '0' ";
            }else if($query['outcount'][$i]=='1'){
                $penalty_name = '퇴근 소감 미작성';
                $sql = "select sum(outcount) as pen_cnt from work_member_penalty where outcount = '1' and email = '".$query['email'][$i]."' and state = '0' ";
            }else if($query['work'][$i]=='1'){
                $penalty_name = '오늘 업무 미작성';
                $sql = "select sum(work) as pen_cnt from work_member_penalty where work = '1' and email = '".$query['email'][$i]."' and state = '0' ";
            }else if($query['challenge'][$i]=='1'){
                $penalty_name = '챌린지 미참여';
                $sql = "select sum(challenge) as pen_cnt from work_member_penalty where challenge = '1' and email = '".$query['email'][$i]."' and state = '0' ";
            }
            $count = selectQuery($sql);
            $pen_count = $count['pen_cnt'];
            if($pen_count < 1){
                $pen_count = 0;
            }
            ?>
            <tr>
                <td><?=$query['company'][$i]?></td>
                <td><?=$query['name'][$i]?></td>
                <td><?=$query['email'][$i]?></td>
                <td><?=$penalty_name?></td>
                <td><?=$pen_count?></td>
                <td><?=$query['updatetime'][$i]?></td>
                <td><?=$query['state'][$i]=='0'?"활성화":"비활성화"?></td>
            </tr>
        <?}?>
        </table>
    <?}

    if($type == "backcomm_list"){
        if($sdate && $edate){
            $where = $where .= " and (wr.workdate >= '".$sdate."' and wr.workdate <= '".$edate."')";
        }

        if($search){
            $where = $where .= " and (wr.email like '%".$search."%' or wr.name like '%".$search."%' or wm.company like '%".$search."%')";
        }

        if($code=="all"){
            $where = $where .= " and wr.work_idx in (1,2,3,4,5,6,7,8,9)";
        }else{
            $where = $where .= " and wr.work_idx in (".$code.")";
        }

        if($kind == "company"){
            $sort = " wm.".$kind;
        }else if($kind != ""){
            $sort = " wr.".$kind;
        }else{
            $sort = " wr.idx";
        }
    
        $sql = "select wr.idx, wr.regdate, wr.email, wr.name, wr.partno, wr.part, wr.work_idx, wr.comment, wm.company from work_todaywork_review as wr, work_company as wm where wr.companyno = wm.idx and wr.state = '0' and wr.comment != '' ".$where;
        $sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
        $query = selectAllQuery($sql);
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        ?>
        <table border='1'>
            <tr>
                <td>퇴근시간</td>
                <td>이름</td>
                <td>회사</td>
                <td>부서</td>
                <td>기분</td>
                <td>코멘트</td>
            </tr>
        <?
        for($i=0; $i<count($query['idx']); $i++){
            $mem_idx = $query['idx'][$i];
            $feelkind = $query['work_idx'][$i];
            $feel_arr = ['1','2','3','4','5','6','7','8','9'];
            $title_arr = ['최고야','뿌듯해','기분좋아','감사해','재밌어','수고했어','무난해','지쳤어','속상해'];
            for($j=0;$j<count($feel_arr);$j++){
                if($feelkind == $feel_arr[$j]){
                    $feel_kind = $title_arr[$j];
                }
            }
            ?>
            <tr>
                <td><?=$query['regdate'][$i]?></td>
                <td><?=$query['name'][$i]."(".$query['email'][$i].")"?></td>
                <td><?=$query['company'][$i]?></td>
                <td><?=$query['part'][$i]?></td>
                <td><?=$feel_kind?></td>
                <td><?=$query['comment'][$i]?></td>
            </tr>
        <?}
            if(count($query['idx'])==0){?>
                <tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
            <?}?>
        </table>
    <?}

?>
