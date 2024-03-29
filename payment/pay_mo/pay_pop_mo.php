    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>KG이니시스 결제샘플</title>
        <link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		
		<script> 
	        function on_pay() { 
	        	myform = document.mobileweb; 
	        	myform.action = "https://mobile.inicis.com/smart/payment/";
	        	myform.target = "_self";
	        	myform.submit(); 
	        }
        </script> 
    </head>

    <body class="wrap">

        <!-- 본문 -->
        <main class="col-8 cont" id="bill-01">
            <!-- 페이지타이틀 -->
            <section class="mb-5">
                <div class="tit">
                    <h2>일반결제</h2>
                    <p>KG이니시스 결제창을 호출하여 다양한 지불수단으로 안전한 결제를 제공하는 서비스</p>
                </div>
            </section>
            <!-- //페이지타이틀 -->


            <!-- 카드CONTENTS -->
            <section class="menu_cont mb-5">
                <div class="card">
                    <div class="card_tit">
                        <h3>모바일 일반결제</h3>
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


                    <form name="mobileweb" id="" method="post" class="mt-5" accept-charset="euc-kr">
                                <input type="text" name="P_INI_PAYMENT" value="CARD">
                                <input type="text" name="P_MID" value="INIpayTest">
                                <input type="text" name="P_OID" value="mobile_test1234">
                                <input type="text" name="P_AMT" value="1000">
                                <input type="text" name="P_GOODS" value="테스트상품">
                                <input type="text" name="P_UNAME" value="테스터">
                                <input type="text" name="P_MOBILE" value="01012345678">
                                <input type="text" name="P_EMAIL" value="test@test.com">
				    		<input type="hidden" name="P_NEXT_URL" value="http://localhost:8090/payment/pay_mo/pay_pop_return_mo.php">
                            <input type="hidden" name="P_CHARSET" value="utf8">
                                <input type="text" name="P_RESERVED" value="below1000=Y&vbank_receipt=Y&centerCd=Y">
                    </form>
				
				    <button onclick="on_pay()" class="btn_solid_pri col-6 mx-auto btn_lg" style="margin-top:50px">결제 요청</button>
					
                </div>
            </section>
			
        </main>
		
    </body>