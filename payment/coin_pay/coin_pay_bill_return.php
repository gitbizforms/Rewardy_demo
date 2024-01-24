<?php
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
$home_pay = str_replace( basename($home_dir) , "" , $home_dir );
include $home_pay . "inc_lude/conf_mysqli.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;
date_default_timezone_set('Asia/Seoul');
$now_time = date("Ymd");
        require_once('libs/INIStdPayUtil.php');
        require_once('libs/HttpClient.php');
        require_once('libs/properties.php');
 
        $util = new INIStdPayUtil();
        $prop = new properties();

        try {
 
            //#############################
            // 인증결과 파라미터 수신
            //#############################

            if (strcmp("0000", $_REQUEST["resultCode"]) == 0) {
 
                //############################################
                // 1.전문 필드 값 설정(***가맹점 개발수정***)
                //############################################
 
                $mid        = $_REQUEST["mid"];
                $signKey 	= "SU5JTElURV9UUklQTEVERVNfS0VZU1RS";
                $timestamp  = $util->getTimestamp();
                $charset    = "UTF-8";
                $format     = "JSON";
                $authToken  = $_REQUEST["authToken"]; 
                $authUrl    = $_REQUEST["authUrl"];
                $netCancel  = $_REQUEST["netCancelUrl"];        
                $merchantData = $_REQUEST["merchantData"];

                //##########################################################################
				// 승인요청 API url (authUrl) 리스트 는 properties 에 세팅하여 사용합니다.
				// idc_name 으로 수신 받은 센터 네임을 properties 에서 include 하여 승인요청하시면 됩니다.
				//##########################################################################
                $idc_name 	= $_REQUEST["idc_name"];
                $authUrl    = $prop->getAuthUrl($idc_name);

                if (strcmp($authUrl, $_REQUEST["authUrl"]) == 0) {
 
                    //#####################
                    // 2.signature 생성
                    //#####################
                    $signParam["authToken"] = $authToken;   // 필수
                    $signParam["timestamp"] = $timestamp;   // 필수
                    // signature 데이터 생성 (모듈에서 자동으로 signParam을 알파벳 순으로 정렬후 NVP 방식으로 나열해 hash)
                    $signature = $util->makeSignature($signParam);

                    $veriParam["authToken"] = $authToken;   // 필수
                    $veriParam["signKey"]   = $signKey;     // 필수
                    $veriParam["timestamp"] = $timestamp;   // 필수
                    // verification 데이터 생성 (모듈에서 자동으로 signParam을 알파벳 순으로 정렬후 NVP 방식으로 나열해 hash)
                    $verification = $util->makeSignature($veriParam);
    
    
                    //#####################
                    // 3.API 요청 전문 생성
                    //#####################
                    $authMap["mid"]          = $mid;            // 필수
                    $authMap["authToken"]    = $authToken;      // 필수
                    $authMap["signature"]    = $signature;      // 필수
                    $authMap["verification"] = $verification;   // 필수
                    $authMap["timestamp"]    = $timestamp;      // 필수
                    $authMap["charset"]      = $charset;        // default=UTF-8
                    $authMap["format"]       = $format;         // default=XML
                    
                    try {
    
                        $httpUtil = new HttpClient();
    
                        //#####################
                        // 4.API 통신 시작
                        //#####################
    
                        $authResultString = "";
                        if ($httpUtil->processHTTP($authUrl, $authMap)) {
                            $authResultString = $httpUtil->body;
    
                        } else {
                            echo "Http Connect Error\n";
                            echo $httpUtil->errormsg;
    
                            throw new Exception("Http Connect Error");
                        }
    
                        //############################################################
                        //5.API 통신결과 처리(***가맹점 개발수정***)
                        //############################################################
                        
                        $resultMap = json_decode($authResultString, true);

                        $dataList = explode("&", $merchantData);
                        $datetime = DateTime::createFromFormat('Ymd', $resultMap['applDate']);
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

                        //0: 결제 타입 , 1: 신청 숫자 2: 회사이름 3: 사업자등록번호
                        $code = name_random_time(10);

                        $sql = "select idx, company, company_num from work_company where idx = '".$companyno."' and state ='0'";
                        $check_com = selectQuery($sql);
                            
                        if($check_com){
                            $sql = "select idx from payment_bill where idx = '".$check_com['idx']."' and ";
                            $bill_check = selectQuery($sql);
                            if(!$bill_check){
                            //컴파니 넘버는 수정해야함!!!
                                $sql = "insert into payment_bill(state,companyno,payMethod,resultCode,resultMsg,tid,mid,MOID,payMethodDetail,
                                        TotPrice,goodName,applDate,applTime,CARD_BillKey,CARD_Num,CARD_Code,CARD_CorpFlag,CARD_CheckFlag,CARD_BankCode,
                                        HPP_Billkey,HPP_Num,buyerName,buyerEmail,buyerTel,custEmail,payDevice) 
                                        values('0','".$check_com['idx']."', '".$resultMap['payMethod']."','".$resultMap['resultCode']."','".$resultMap['resultMsg']."','".$resultMap['tid']."','".$resultMap['mid']."','".$resultMap['MOID']."','".$resultMap['payMethodDetail']."',
                                        '".$resultMap['TotPrice']."','".$resultMap['goodName']."','".$resultMap['applDate']."','".$resultMap['applTime']."','".$resultMap['CARD_BillKey']."','".$resultMap['CARD_Num']."','".$resultMap['CARD_Code']."',
                                        '".$resultMap['CARD_CorpFlag']."','".$resultMap['CARD_CheckFlag']."','".$resultMap['CARD_BankCode']."','".$resultMap['HPP_Billkey']."','".$resultMap['HPP_Num']."',
                                        '".$resultMap['buyerName']."','".$resultMap['buyerEmail']."','".$resultMap['buyerTel']."','".$resultMap['custEmail']."','".$resultMap['payDevice']."')";
                                        $pay_in = insertIdxQuery($sql);
                                if($pay_in){
                                    $sql = "select idx,state,CARD_BillKey,HPP_BillKey,goodName,buyerName,buyerEmail,TotPrice,applDate,companyno from payment_bill where companyno = '".$check_com['idx']."' and state = '0'";
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

                                    $sql = "insert into payment_log(state,companyno,payMethod,resultCode,resultMsg,tid,mid,MOID,payMethodDetail,
                                    TotPrice,goodName,applDate,applTime,buyerName,buyerEmail,payDevice) 
                                    values('0','".$resultMap['idx']."', 'Billing','".$resultMap['resultCode']."','".$resultMap['resultMsg']."','".$resultMap['tid']."','".$resultMap['mid']."','".$resultMap['MOID']."','".$resultMap['payMethodDetail']."',
                                    '".$resultMap['TotPrice']."','".$resultMap['goodName']."','".$resultMap['applDate']."','".$resultMap['applTime']."',
                                    '".$resultMap['buyerName']."','".$resultMap['buyerEmail']."','".$resultMap['payDevice']."')";
                                    $pay_log = insertIdxQuery($sql);

                                    $sql = "update payment_bill set state = '1' where company = '".$check_com['idx']."'";
                                    $pay_up = updateQuery($sql);
                                }   
                            }   
                        }
                    } catch (Exception $e) {
                        //    $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
                        //####################################
                        // 실패시 처리(***가맹점 개발수정***)
                        //####################################
                        //---- db 저장 실패시 등 예외처리----//
                        $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
                        echo $s;
    
                        //#####################
                        // 망취소 API
                        //#####################
    
                        $netcancelResultString = ""; // 망취소 요청 API url(고정, 임의 세팅 금지)
                        $netCancel    = $prop->getNetCancel($idc_name);
                        
                        if (strcmp($netCancel, $_REQUEST["netCancelUrl"]) == 0) {

                            if ($httpUtil->processHTTP($netCancel, $authMap)) {
                                $netcancelResultString = $httpUtil->body;
                            } else {
                                echo "Http Connect Error\n";
                                echo $httpUtil->errormsg;
        
                                throw new Exception("Http Connect Error");
                            }
        
                            echo "<br/>## 망취소 API 결과 ##<br/>";
                            
                            /*##XML output##*/
                            //$netcancelResultString = str_replace("<", "&lt;", $$netcancelResultString);
                            //$netcancelResultString = str_replace(">", "&gt;", $$netcancelResultString);
        
                            // 취소 결과 확인
                            echo "<p>". $netcancelResultString . "</p>";
                        }
                    }

                } else {
                    echo "authUrl check Fail\n";
                }

            } else {
 
            }

        } catch (Exception $e) {
            $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
            echo $s;
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
		
		<script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>
        <script type="text/javascript">
            function paybtn() {
                INIStdPay.pay('SendPayForm_id');
            }
        </script>
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

                        <?php 
                            if (strcmp($idc_name, $authUrl) == -1) {
                                echo "
                                <label class='col-10 col-sm-2 input param' style='border:none;'></label>
                                <label class='col-10 col-sm-9 reinput'> authUrl check Fail (인증까지만 진행됨, 아래 인증 결과) </label>";
                            }
                        ?>

                        <label class="col-10 col-sm-2 gap-2 input param" style="border:none;">resultCode</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo @(in_array($resultMap["resultCode"] , $resultMap) ? $resultMap["resultCode"] : $_REQUEST["resultCode"] ) ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">resultMsg</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo @(in_array($resultMap["resultMsg"] , $resultMap) ? $resultMap["resultMsg"] : $_REQUEST["resultMsg"] ) ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">tid</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo @(in_array($resultMap["tid"] , $resultMap) ? $resultMap["tid"] : "null" ) ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">CARD_BillKey</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo @(in_array($resultMap["CARD_BillKey"] , $resultMap) ? $resultMap["CARD_BillKey"] : "null" ) ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">MOID</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo @(in_array($resultMap["MOID"] , $resultMap) ? $resultMap["MOID"] : "null" ) ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">TotPrice</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo @(in_array($resultMap["TotPrice"] , $resultMap) ? $resultMap["TotPrice"] : "null" ) ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">goodName</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo @(in_array($resultMap["goodName"] , $resultMap) ? $resultMap["goodName"] : "null" ) ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">applDate</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo @(in_array($resultMap["applDate"] , $resultMap) ? $resultMap["applDate"] : "null" ) ?>
                        </label>
						
						<label class="col-10 col-sm-2 input param" style="border:none;">applTime</label>
                        <label class="col-10 col-sm-9 reinput">
                            <?php echo @(in_array($resultMap["applTime"] , $resultMap) ? $resultMap["applTime"] : "null" ) ?>
                        </label>
 
                    </div>
                </form>
				
				<button onclick="location.href='bill_pay_pop.php'" class="btn_solid_pri col-6 mx-auto btn_lg" style="margin-top:50px">돌아가기</button>
					
                </div>
            </section>
			
        </main>
		
    </body>
</html>