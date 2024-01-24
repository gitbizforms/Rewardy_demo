<?php
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- datetimepicker 스타일 적용 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">
    <title>Document</title>
</head>

<body>
    
    <div>
        시작일자 <input type="text" id="startDate" autocomplete="off">
    </div>
    
    <div>
        종료일자 <input type="text" id="endDate" autocomplete="off">
    </div>

</body>


<!-- 제이쿼리 import -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>


<!-- datetimepicker import -->
<script type="text/javascript" src="/js/jquery.datetimepicker.full.min.js"></script>

<script type="text/javascript">

    $(document).ready(function() {
        fn_egov_init_date()
    })

    function fn_egov_init_date(){
        var $startDate = $('#startDate');
        var $endDate = $('#endDate');
        $startDate.datetimepicker({
            timepicker: false,
            lang: 'ko',
            format: 'Y-m-d',
            scrollMonth: false,
            scrollInput: false,
            onShow: function (ct) {
                this.setOptions({
                    maxDate: $endDate.val() ? $endDate.val() : false
                })
            },
        });

        $endDate.datetimepicker({
            timepicker: false,
            lang: 'ko',
            format: 'Y-m-d',
            scrollMonth: false,
            scrollInput: false,
            onShow: function (ct) {
                this.setOptions({
                    minDate: $startDate.val() ? $startDate.val() : false
                })
            }
        });

    }
</script>
</html>