<?php
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
$home_pay = str_replace( basename($home_dir) , "" , $home_dir );
// include $home_pay . "/inc_lude/header.php";
// var_dump($home_pay . "/inc_lude/header.php");
$type_flag = ($chkMobile)?1:0;	

require_once('libs/INIStdPayUtil.php');
$SignatureUtil = new INIStdPayUtil();
$timestamp = $SignatureUtil->getTimestamp(); 

$merchantKey = "b09LVzhuTGZVaEY1WmJoQnZzdXpRdz09"; // 이니라이트키

$dateVal = date("YmdHis");

$mid="INIBillTst";
$orderid= "DemoTest_". $timestamp ;


$hashData = hash("sha256",(string)$mid.(string)$orderid.(string)$dateVal.(string)$merchantKey);

$com = $_POST['com'];
$com_text = urlencode($com);
$dep = $_POST['dep'];
$dep_text = urlencode($dep);
$userEmail = $_POST['userEmail'];
$userName = $_POST['userName'];
$companyNum = $_POST['company_num'];
$com_user = urlencode($userName);
$people = $_POST['person'];
$people_price = 3000;
   							
$price = $people * $people_price; // 상품가격(특수기호 제외, 가맹점에서 직접 설정)

?>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>KG이니시스 결제샘플</title>
        <link rel="stylesheet" href="/html/css/common.css">
        <link rel="stylesheet" href="/html/css/pay_pop.css">
        <link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
            integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<!--테스트 JS--><script language="javascript" type="text/javascript" src="https://stgstdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>
		<!--운영 JS> <script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script> -->
        <script language="javascript"> 
            function on_pay() { 
             form = document.mobileweb; 
             form.action = "https://inilite.inicis.com/inibill/inibill_card.jsp";
             form.target = "_self";
             form.submit(); 
            }  
        </script> 
    </head>
    <?php
       echo "|";
    ?>
    <form name="mobileweb" id = "mobileweb" method="post" class="mt-5">
        <input type="hidden" name="authtype" value="D">
        <input type="text" name="mid" value="<?php echo $mid ?>">
        <input type="text" name="orderid" value="<?php echo $orderid ?>">
        <input type="hidden" name="price" class = "price" value="<?php echo $price ?>">
        <input type="text" name="timestamp" value="<?php echo $dateVal ?>">
        <input type="text" name="hashdata" value="<?php echo $hashData ?>">
        <input type="hidden" name="goodname" value="멤버십 가입">
        <input type="hidden" name="buyername" class= "buyername" value="<?php echo $userName?>">
        <input type="hidden" name="buyertel" value="01012345678">
        <input type="hidden" name="buyeremail" class= "buyeremail" value="<?php echo $userEmail?>">
        <input type="text" name="merchantreserved" value="<?php echo "1&".$people."&".$com_text."&".$price."&".$com_user."&".$userEmail."&".$companyNum."&".$dep_text?>">
        <input type="hidden" name="returnurl" value="http://demo.rewardy.co.kr/payment/bill_pay_mo/bill_pay_mo_pop_return.php">
        <input type="text" name="carduse" value="">
    </form> 