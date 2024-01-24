<?
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
$home_pay = str_replace( basename($home_dir) , "" , $home_dir );
include $home_pay . "inc_lude/conf_mysqli.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;
include_once($home_pay . "PHPMailer/libphp-phpmailer/PHPMailerAutoload.php");

if($_REQUEST['resultcode'] == '00'){
    $dataList = explode("&", $_REQUEST['merchantreserved']);
    $datetime = DateTime::createFromFormat('Ymd', $_REQUEST['pgauthdate']);
    $formatted_date = $datetime->format('Y-m-d');
    // 만약 값이 넘어온다는 가정하에
    if($dataList[0] == 1){
        $next_day = date('Y-m-d', strtotime($formatted_date . ' +1 month'));
    }
    // else if($merchantData == 2){
    //     $next_day = date('Y-m-d', strtotime($formatted_date . ' +6 month'));
    // }else if ($merchantData == 3){
    //     $next_day = date('Y-m-d', strtotime($formatted_date . ' +12 month'));
    // }

    // 0: 결제 타입 , 1: 신청 숫자 2: 회사이름 3: 결제 금액 4: 결제자 5: 결제이메일 6:사업자번호 7: 부서이름
    $code = name_random_time(10);

    $sql = "select company, company_num from work_company company = '".urldecode($dataList[2])."' and company_num = '".$dataList[6]."'";
    $check_com = selectQuery($sql);
    if(!$check_com){
        $sql = "insert into work_company(company,code,people,payment_type,pay_stime,pay_etime)
                values('".urldecode($dataList[2])."', '".$code."', '".$dataList[1]."','".$dataList[0]."','".$formatted_date."', '".$next_day."')";

        $first_com = insertIdxQuery($sql);

        $sql = "select idx from payment_bill where idx = '".$first_com['idx']."'";
        $bill_check = selectQuery($sql);
        if(!$bill_check){
        //컴파니 넘버는 수정해야함!!!
            $sql = "insert into payment_bill(state,companyno,payMethod,resultCode,resultMsg,tid,mid,MOID,payMethodDetail,
                    TotPrice,goodName,applDate,applTime,CARD_BillKey,CARD_Num,CARD_Code,CARD_CorpFlag,CARD_CheckFlag,CARD_BankCode,
                    HPP_Billkey,HPP_Num,buyerName,buyerEmail,buyerTel,custEmail,payDevice) 
                    values('0','".$first_com['idx']."', 'Auth','".$_REQUEST['resultcode']."','".$_REQUEST['resultmsg']."','".$_REQUEST['tid']."','".$_REQUEST['mid']."','".$_REQUEST['authkey']."','BILL_CARD',
                    '".$dataList[3]."','멤버십 가입','".$_REQUEST['pgauthdate']."','".$_REQUEST['pgauthtime']."','".$_REQUEST['billkey']."','".$_REQUEST['cardno']."','".$_REQUEST['cardcd']."',
                    '".$_REQUEST['cardkind']."','".$_REQUEST['CheckFlag']."','".$_REQUEST['cardcd']."','','',
                    '".urldecode($dataList[4])."','".$dataList[5]."','','".$dataList[5]."','MOBILE')";
    
                    $pay_in = insertIdxQuery($sql);
            if($pay_in){
                $sql = "select idx,state,CARD_BillKey,HPP_BillKey,goodName,buyerName,buyerEmail,TotPrice,applDate,companyno from payment_bill where companyno = '".$first_com['idx']."' and state = '0'";
                $bill_sql = selectAllQuery($sql);

                for($i = 0; $i < count($bill_sql['idx']); $i ++){
                    if($bill_sql['state'][$i] == '0'){
                        if($bill_sql['CARD_BillKey'][$i]){
                            $paymethod = "CARD";
                            $billkey = $bill_sql['CARD_BillKey'][$i];
                        }else if($bill_sql['HPP_BillKey'][$i]){
                            $paymethod = "HPP";
                            $billkey = $bill_sql['HPP_BillKey'][$i];
                        }
                        //step1. 요청을 위한 파라미터 설정
                            $key = "rKnPljRn5m6J9Mzz";
                            $iv = "W2KLNKra6Wxc1P==";
                            $mid = "INIBillTst";
                            $type = "billing";
                            $timestamp = date("YmdHis");
                            $clientIp = "192.0.0.0";
                            $goodName = $bill_sql['goodName'][$i];
                            $buyerName = $bill_sql['buyerName'][$i];
                            $buyerEmail = $bill_sql['buyerEmail'][$i];
                            $price = $bill_sql['TotPrice'][$i];
                            $postdata = array();
                            $postdata["mid"] = $mid;
                            $postdata["type"] = $type;
                            $postdata["paymethod"] = $paymethod;
                            $postdata["timestamp"] = $timestamp;
                            $postdata["clientIp"] = $clientIp;

                            // //// Data 상세
                            $detail = array();
                            $detail["url"] = "www.inicis.com";
                            $detail["moid"] = $mid."_".$timestamp;
                            $detail["goodName"] = $goodName;
                            $detail["buyerName"] = $buyerName;
                            $detail["buyerEmail"] = $buyerEmail;
                            $detail["buyerTel"] = "01012345678";
                            $detail["price"] = $price;
                            $detail["billKey"] = $billkey;
                            $detail["authentification"] = "00";
                            $detail["cardQuota"] = "00";
                            $detail["quotaInterest"] = "0";

                            $postdata["data"] = $detail;

                            $details = str_replace('\\/', '/', json_encode($detail, JSON_UNESCAPED_UNICODE));

                            //// Hash Encryption
                            $plainTxt = $key.$mid.$type.$timestamp.$details;
                            $hashData = hash("sha512", $plainTxt);

                            $postdata["hashData"] = $hashData;

                            // echo "plainTxt : ".$plainTxt."<br/><br/>";
                            // echo "hashData : ".$hashData."<br/><br/>"; 


                            $post_data = json_encode($postdata, JSON_UNESCAPED_UNICODE);

                            // echo "**** 요청전문 **** <br/>" ; 
                            // echo str_replace(',', ',<br>', $post_data)."<br/><br/>" ; 
                            
                            //step2. 요청전문 POST 전송
                            
                            $url = "https://iniapi.inicis.com/v2/pg/billing";
                            
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            
                            $response = curl_exec($ch);
                            curl_close($ch);
                            
                            
                            //step3. 결과출력
                            
                            // echo "**** 응답전문 **** <br/>" ;
                            // echo str_replace(',', ',<br>', $response)."<br><br>";
                    }
                }

                // 0: 결제 타입 , 1: 신청 숫자 2: 회사이름 3: 결제 금액 4: 결제자 5: 결제이메일 6:사업자번호
                $sql = "insert into payment_log(state,companyno,payMethod,resultCode,resultMsg,tid,mid,MOID,payMethodDetail,
                                    TotPrice,goodName,applDate,applTime,buyerName,buyerEmail,payDevice) 
                                    values('0','".$first_com['idx']."', 'Billing','".$_REQUEST['resultcode']."','".$_REQUEST['resultmsg']."','".$_REQUEST['tid']."','".$_REQUEST['mid']."','".$_REQUEST['authkey']."','BILL_CARD',
                                    '".$dataList[3]."','멤버십 가입','".$_REQUEST['pgauthdate']."','".$_REQUEST['pgauthtime']."',
                                    '".urldecode($dataList[4])."','".$dataList[5]."','MOBILE')";
                                    $pay_log = insertIdxQuery($sql);

                $sql = "update payment_bill set state = '1' where company = '".$first_com['idx']."'";
                $pay_up = updateQuery($sql);
                if($pay_up){

                    //메일 보내기
                    //데이터저장
                    $sql = "insert into work_member(state, email, company, companyno, highlevel)";
                    $sql = $sql ." values('1','".$dataList[5]."','".urldecode($dataList[2])."','".$first_com['idx']."', '".urldecode($dataList[7])."' , '0')";
                    $insert_idx = insertIdxQuery($sql);
                    if($insert_idx){
                        //발신자명
                        $send_name = "리워디";
                        //발신자이메일주소
                        $send_email = "manager@rewardy.co.kr";
                        //smtp 메일계정
                        $smtp_email = "devmaster@bizforms.co.kr";

                        $title = "리워디 인증 이메일입니다.";
                        $secret = "send_email=".$send_email."&to_email=".$dataList[5]."&sendno=".$insert_idx;
                        $encrypted = Encrypt($secret);


                        //include str_replace( basename(__DIR__) , "", __DIR__ ) ."layer/mail_send_about.php";
                        //$contents = $mail_html;
                        
                        //$contents = "안녕하세요.\n\n".$send_name."에서 발송되었습니다.\n\n아래 링크를 클릭하여 인증 바랍니다.\n\n<span style=\"color: red\"><a href='".$home_url."/team/?".$encrypted."' target=\"_blank\">사용자 인증</a></span>";
                        $location_url = $home_url."/team/?".$encrypted;
                        
                        include $home_pay."layer/mail_send_join_auth.php";
                        $contents = $mail_html;

                        //발신자이름, 발신자이메일, 수신자이메일, 메일제목, 메일내용
                        $result = mailer($send_name, $smtp_email, $dataList[5], $title, $contents);

                        //발송결과
                        if($result == '1'){
                            echo "ok";
                            $state = "1";
                        }else{
                            echo "fail";
                            $state = "2";
                        }

                        //메일전송 결과 업데이트, 메일발송횟수, 메일발송IP, 메일발송일자
                        $sql = "update work_member set state='".$state."', send_mail_cnt = send_mail_cnt + 1 , sender_ip='".LIP."', mail_send_regdate=".DBDATE." where idx='".$insert_idx."'";
                        $res = updateQuery($sql);
                        exit;
                    }
                }
            }   
        }   
    }
}
?>
<!DOCTYPE html>
<html lang="ko">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>KG이니시스 결제샘플</title>
        <link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
    </head>

    <body class="wrap">

        <!-- 본문 -->
        <main class="col-8 cont" id="bill-01">
            <!-- 페이지타이틀 -->
            <section class="mb-5">
                <div class="tit">
                    <h2>빌링(정기과금)</h2>
                    <p>KG이니시스 결제창을 호출하여 정기결제를 위한 billkey 발급 서비스</p>
                </div>
            </section>
            <!-- //페이지타이틀 -->


            <!-- 카드CONTENTS -->
            <section class="menu_cont mb-5">
                <div class="card">
                    <div class="card_tit">
                        <h3>PC 빌링키발급</h3>
                    </div>

                    <!-- 유의사항 -->
                    <div class="card_desc">
                        <h4>※ 유의사항</h4>
                        <ul>
                            <li>테스트MID 결제시 실 승인되며, 당일 자정(24:00) 이전에 자동으로 취소처리 됩니다.</li>
							<li>가상계좌 채번 후 입금할 경우 자동환불되지 않사오니, 가맹점관리자 내 "입금통보테스트" 메뉴를 이용부탁드립니다.<br>(실 입금하신 경우 별도로 환불요청해주셔야 합니다.)</li>
							<li>국민카드 정책상 테스트 결제가 불가하여 오류가 발생될 수 있습니다. 국민, 카카오뱅크 외 다른 카드로 테스트결제 부탁드립니다.</li>
                        </ul>
                    </div>
                    <!-- //유의사항 -->


                    <form name="" id="result" method="post" class="mt-5">
                    <div class="row g-3 justify-content-between" style="--bs-gutter-x:0rem;">
 
                        <label class="col-10 col-sm-2 gap-2 input param" style="border:none;">resultcode</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo $_REQUEST["resultcode"] ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">resultmsg</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo $_REQUEST["resultmsg"] ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">tid</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo $_REQUEST["tid"] ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">billkey</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo $_REQUEST["billkey"] ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">orderid</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo $_REQUEST["orderid"] ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">pgauthdate</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo $_REQUEST["pgauthdate"] ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">pgauthtime</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo $_REQUEST["pgauthtime"] ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">cardcd</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo $_REQUEST["cardcd"] ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">cardno</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo $_REQUEST["cardno"] ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">cardkind</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo $_REQUEST["cardkind"] ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">CheckFlag</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo $_REQUEST["CheckFlag"] ?>
                        </label>
 
                    </div>
                </form>

				
				<button onclick="location.href='INIbill_mo_req.php'" class="btn_solid_pri col-6 mx-auto btn_lg" style="margin-top:50px">돌아가기</button>
					
                </div>
            </section>
			
        </main>
		
    </body>
</html>