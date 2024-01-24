<?php
require_once('pay_libs/INIStdPayUtil.php');
   
?>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>KG이니시스 결제샘플</title>
        <link rel="stylesheet" href="/html/css/common.css">
        <link rel="stylesheet" href="/html/css/coin_rec_pop.css">
		<!--테스트 JS--><script language="javascript" type="text/javascript" src="https://stgstdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>
		<!--운영 JS> <script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script> -->
       
        <script>
            $(document).ready(function () {
                    var currentValue = 0;
                    $('.coin_plus button').click(function(){
                        console.log($(this).attr("value"));
                        var amountText = $(this).find('span').text();
                        console.log(amountText);
                        var amount = parseInt(amountText.replace(/\D/g, ''));
                        var amountPrice = amount * 10000;
                        currentValue += amountPrice;
                        $(".coin_total").val(number_format(currentValue) + "코인");
                        chargePrice = currentValue * 0.05
                        $(".total_coin").attr('placeholder', number_format(chargePrice));

                    });
                    $('.coin_last').click(function(){
                        currentValue = 0;
                        $(".coin_total").val(currentValue  + "원");
                        $(".total_coin").attr('placeholder', 0);
                    });
                    function number_format(num){
                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g,',');
                    }

                    $(".billing_coin").click(function(){
                        $(this).toggleClass("on");
                    });
                    

                    $(".submit_btn button").click(function(){
                        var chPriceValue = $(".coin_total").val();
                        var payPrice = parseInt(chPriceValue.replace(/,/g, ''), 10);
                        // console.log(payPrice);
                        // on 일경우 빌링, off일 경우 일반 결제
                        if($(".billing_coin").hasClass("on") == true){
                            var fdata = new FormData();
                            fdata.append("price", payPrice);
                            $.ajax({
                                    type: "POST",
                                    data: fdata,
                                    contentType: false,
                                    processData: false,
                                    url: "/payment/coin_pay/coin_pay_bill_process.php",
                                    success: function (data) {
                                        var tdata = data.split("|");
                                        result = tdata[1];
                                        // console.log(tdata);
                                        $("#SendPayForm_id").html(result);
                                        paybtn();
                                        },
                                    });
                        }else{
                            var fdata = new FormData();
                            fdata.append("price", payPrice);
                            $.ajax({
                                    type: "POST",
                                    data: fdata,
                                    contentType: false,
                                    processData: false,
                                    url: "/payment/coin_pay/coin_pay_process.php",
                                    success: function (data) {
                                        var tdata = data.split("|");
                                        result = tdata[1];
                                        // console.log(data);
                                        $("#SendPayForm_id").html(result);
                                        paybtn();
                                        },
                                    });
                        }
                    })
                    
                });
        </script>
        <script>
            function paybtn() {
                INIStdPay.pay('SendPayForm_id');
            }
        </script>
    </head>
    <body>
    <div class="t_layer rew_rec" style = "display:none;">
        <div class="tl_deam"></div>
        <div class="lay_rec">
        <div class="rec_tit">
            <h2>공용코인 충전</h2>
        </div>
        <div class="rec_input">
            <div class="rec_input_in">
            <div class="rec_coin">
                <div class="coin_btn">
                <div class="coin_plus">
                    <button value = "10000"><span>+1만</span></button>
                    <button value = "30000"><span>+3만</span></button>
                    <button value = "50000"><span>+5만</span></button>
                    <button value = "100000"><span>+10만</span></button>
                </div>
                <button class="coin_last"><span>초기화</span></button>
                </div>
                <input type="text" placeholder="충전할 코인 금액을 입력하세요." class = "coin_total" readonly>
            </div>
            <div class="rec_sec rec_total on">
                <p>총 결제금액</p>
                <input type="text" name="total_coin" class="total_coin" placeholder="0" readonly>
                <label for="total_coin">충전 수수료 (5%)</label>
                <span>원</span>
            </div>
            <div class="rec_sec rec_pay">
                <p>결제수단</p>
                <div class="rec_pay_list">
                <button><span>신용카드</span></button>
                </div>
                <button class ="billing_coin" style= "display:none;"><span>매월 자동 반복</span></button>
            </div>
            </div>
        </div>
        <div class="submit_btn"><button><span>충전하기</span></button></div>
        </div>
    </div>
<form name="" id="SendPayForm_id" method="post" class="mt-5">
   
</form> 
