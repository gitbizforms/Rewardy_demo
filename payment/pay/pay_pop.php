<?php
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
$home_pay = str_replace( basename($home_dir) , "" , $home_dir );
// include $home_pay . "/inc_lude/header.php";
// var_dump($home_pay . "/inc_lude/header.php");
			// 가맹점 주문번호(가맹점에서 직접 설정)
require_once('libs/INIStdPayUtil.php');
   
?>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>KG이니시스 결제샘플</title>
        <link rel="stylesheet" href="/html/css/common.css">
        <link rel="stylesheet" href="/html/css/pay_pop.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
            integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<!--테스트 JS--><script language="javascript" type="text/javascript" src="https://stgstdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>
		<!--운영 JS> <script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script> -->
       
        <script>
            $(document).ready(function () {

                $('.cont_sub button').click(function (e) {
                e.preventDefault();
                let stat = $('.cont_num span').text();
                let num = parseInt(stat, 10);
                num--;
                if (num <= 0) {
                alert('더 이상 인원을 줄일 수 없습니다.')
                return;
                }
                $('.cont_num span').text(num + '명');
                $('.pay').text(number_format(num * 3000))
            })
            // 숫자 증가
            $('.cont_plus button').click(function (e) {
                e.preventDefault();
                let stat = $('.cont_num span').text();
                let num = parseInt(stat, 10);
                num++;
                $('.cont_num span').text(num + '명');
                $('.pay').text(number_format(num * 3000))

            })
            function number_format(num){
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g,',');
            }
            // 팝업 다음&이전
            $('.result_btn').click(function () {
                // console.log(parseInt($('.cont_num').text()));
                $('.cont_num').val(parseInt($('.cont_num').text()));
                
                var person = $('.cont_num').val();
                var fdata = new FormData();
                fdata.append('person', person);
                $.ajax({
                        type: "POST",
                        data: fdata,
                        contentType: false,
                        processData: false,
                        url: "pay_process.php",
                        success: function (data) {
                            var tdata = data.split("|");
                            result = tdata[1];
                            // console.log(tdata);
                            $("#SendPayForm_id").html(result);
                               paybtn();
                            },
                        });
                   });
                });
        </script>
        <script>
            function paybtn() {
                INIStdPay.pay('SendPayForm_id');
            }
        </script>
    </head>
    <body>
    <div class="t_layer rew_layer_pay_02">
            <div class="tl_deam"></div>
            <div class="lay_pay">
            <div class="pay_tit">
                <div class="pay_tit_in">
                <h2>멤버십 가입하기</h2>
                </div>
            </div>
            <div class="pay_02_input">
                <div class="pay_02_input_in">
                <div class="pay_cont">
                    <p>우리 회사는 총 몇명의 구성원이<br>리워디 서비스를 사용하나요?</p>
                    <div class="pay_count">
                    <div class="cont_sub"><button><span>빼기</span></button></div>
                    <div class="cont_num" value=""><span>10명</span></div>
                    <div class="cont_plus"><button><span>더하기</span></button></div>
                    </div>
                    <span class="pay_text">※멤버십 결제는 매월 추가/삭제된 멤버수에 따라서 금액이 달라지며,<br>매월 12일에 자동으로 결제됩니다.</span>
                </div>
                <div class="pay_cash">
                   <p>총 결제금액</p>
                    <div><span class="pay">30,000</span><em>원</em></div>
                </div>
                </div>
            </div>
           
            <div class="submit_btn result_btn"><button><span>멤버십 결제하기 (2/2)</span></button></div>
            <div class="move_before"><button><span>이전으로</span></button></div>
            </div>
        </div>
    </body>
<form name="" id="SendPayForm_id" method="post" class="mt-5">
   
</form> 
