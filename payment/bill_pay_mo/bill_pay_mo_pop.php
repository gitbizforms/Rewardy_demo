<?php
require_once('libs/INIStdPayUtil.php');
$SignatureUtil = new INIStdPayUtil();
$timestamp = $SignatureUtil->getTimestamp(); 

$merchantKey = "b09LVzhuTGZVaEY1WmJoQnZzdXpRdz09"; // 이니라이트키

$dateVal = date("YmdHis");

$mid="INIBillTst";
$orderid= "DemoTest_". $timestamp ;


$hashData = hash("sha256",(string)$mid.(string)$orderid.(string)$dateVal.(string)$merchantKey);

?>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>KG이니시스 결제샘플</title>
        <link rel="stylesheet" href="/html/css/pay_pop.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
            integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script>
            $(document).ready(function () {
            // 팝업 다음&이전
            $('.rew_layer_pay_01 .submit_btn').click(function () {
                if ($('.company').val() === '' || $('.company_num').val() === '' || $('.name').val() === '' || $(
                    '.department').val() === '' || $('.email').val() === '') {
                    alert('내용을 입력해주세요.');
                    $('.rew_layer_pay_02').hide();
                    $('.rew_layer_pay_01').show();
                } else if($('.com_ok').hasClass("on") == false || $('.email_ok').hasClass("on") == false){
                    alert('중복확인을 해주세요.');
                }else {
                    $('.rew_layer_pay_01').hide();
                    $('.rew_layer_pay_02').show();
                    var dep = $('.department').val();
                    var userEmail = $('.email').val();
                    var userName = $('.name').val();
                    var com = $('.company').val();
                    var companyNum = $('.company_num').val();
                    $('.buyername').val(userName);
                    $('.buyeremail').val(userEmail);
                }
            });
            $('.move_before').click(function () {
                $('.rew_layer_pay_02').hide();
                $('.rew_layer_pay_01').show();
            });

            // 사업자 등록번호
            $('.company_num').keydown(function (event) {
                $('.com_ok').hide();
                $('.com_ok').removeClass("on");
                $('.com_num_check').show();
                $('.com_num_check').addClass("on");
                let key = event.charCode || event.keyCode || 0;
                $text = $(this);
                if (key !== 8 && key !== 9) {
                if ($text.val().length === 3) {
                    $text.val($text.val() + '-');
                }
                if ($text.val().length === 6) {
                    $text.val($text.val() + '-');
                }
                }
                return (key == 8 || key == 9 || key == 46 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105));
            })
            $('.email').keydown(function (event) {
                $('.email_ok').hide();
                $('.email_ok').removeClass("on");
                $('.com_email_check').show();
                $('.com_email_check').addClass("on");
            })
            // 숫자 감소
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
            $(document).on('click', '.com_num_check', function(){
                var com_num = $('.company_num').val();
                var fdata = new FormData();
                fdata.append('mode', 'num_check');
                fdata.append('com_num', com_num);
                if(com_num.length < 12){
                    alert("사업자번호 10자리를 채워주세요.");
                }else if(com_num.length == 12){
                     $.ajax({
                        type: "POST",
                        data: fdata,
                        contentType: false,
                        processData: false,
                        url: "/inc/pay_process.php",
                        success: function (data) {
                                if(data == "complete"){
                                    alert("사용할 수 있는 사업자 번호입니다.");
                                    $('.com_ok').show();
                                    $('.com_ok').addClass("on");
                                    $('.com_num_check').hide();
                                    $('.com_num_check').removeClass("on");
                                }else{
                                    alert("존재하는 사업자 번호입니다. 다시 확인해주시길 바랍니다.");
                                }
                            }
                        });
                }
            });
            $(document).on('click', '.com_email_check', function(){
                var com_email = $('.email').val();
                var regExp = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

                if(com_email.match(regExp) == null){
                    alert("메일 형식이 아닙니다. 형식에 맞게 작성해주세요.");
                }else{
                    var fdata = new FormData();
                    fdata.append('mode', 'email_check');
                    fdata.append('com_email', com_email);
                    
                    $.ajax({
                        type: "POST",
                        data: fdata,
                        contentType: false,
                        processData: false,
                        url: "/inc/pay_process.php",
                            success: function (data) {
                                console.log(data);
                                if(data == "complete"){
                                    alert("사용할 수 있는 이메일입니다.");
                                    $('.email_ok').show();
                                    $('.email_ok').addClass("on");
                                    $('.com_email_check').hide();
                                    $('.com_email_check').removeClass("on");
                                }else{
                                    alert("존재하는 이메일입니다. 다시 확인해주시길 바랍니다.");
                                }
                            }
                        });
                }
                
            });
            $('.result_btn').click(function () {
                // console.log(parseInt($('.cont_num').text()));
                $('.cont_num').val(parseInt($('.cont_num').text()));
                
                var dep = $('.department').val();
                var userEmail = $('.email').val();
                var userName = $('.name').val();
                var com = $('.company').val();
                var companyNum = $('.company_num').val();
                var person = $('.cont_num').val();
                var fdata = new FormData();
                fdata.append('person', person);
                fdata.append('dep', dep);
                fdata.append('userEmail', userEmail);
                fdata.append('userName', userName);
                fdata.append('company_num', companyNum);
                fdata.append('com', com);
              
                $.ajax({
                        type: "POST",
                        data: fdata,
                        contentType: false,
                        processData: false,
                        url: "/payment/bill_pay_mo/bill_pay_mo_process.php",
                        success: function (data) {
                            var tdata = data.split("|");
                            result = tdata[1];
                            // console.log(tdata);
                            $("#mobileweb").html(result);
                            on_pay();
                            },
                        });
                   });

                function number_format(num){
                  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g,',');
                }
                });
        </script>
		<script language="javascript"> 
            function on_pay() { 
             form = document.mobileweb; 
             form.action = "https://inilite.inicis.com/inibill/inibill_card.jsp";
             form.target = "_self";
             form.submit(); 
            }  
        </script> 
    </head>

    <body>
        <div class="t_layer rew_layer_pay_01">
            <div class="tl_deam"></div>
            <div class="lay_pay">
            <div class="pay_tit">
                <h2>멤버십 가입하기</h2>
                <p>리워디 멤버십 가입을 환영합니다. <br>
                아래의 내용을 입력해 주세요.</p>
            </div>
            <div class="pay_input">
                <div class="pay_input_in">
                <input type="text" placeholder="회사명" class="company">
                <div class="com_num pay_check">
                    <input type="text" placeholder="사업자번호를 입력해주세요." maxlength="12" class="company_num">
                    <button class = "com_num_check"><span>중복확인</span></button>
                    <button class="ok com_ok" style="display: none; pointer-events: none;"><span>확인완료</span></button>
                </div>
                <input type="text" placeholder="담당자명" class="name">
                <input type="text" placeholder="부서명" class="department">
                <div class="com_email pay_check">
                    <input type="text" placeholder="이메일을 입력해주세요." class="email">
                    <button class="com_email_check"><span>중복확인</span></button>
                    <button class="ok email_ok" style="display: none; pointer-events: none;"><span>확인완료</span></button>
                </div>
                </div>
            </div>
            <div class="submit_btn"><button><span>다음 (1/2)</span></button></div>
            </div>
        </div>

        <div class="t_layer rew_layer_pay_02" style="display: none;">
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
    
    <form name="mobileweb" id = "mobileweb" method="post" class="mt-5">
    </form>
				
