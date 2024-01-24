<?php

    $home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );


    //윈도우서버용 php-mssql 사용, 도메인 : https://rewardy.co.kr

    //리눅스 환경 변수 : /inc_lude/conf_mysqli.php
    //리눅스 환경 함수 : /inc_lude/func_mysqli.php

    include $home_dir . "inc_lude/conf_mysqli.php";
    include DBCON_MYSQLI;
    include FUNC_MYSQLI;

    //디렉토리 추출
    $get_dirname = str_replace(NAS_HOME_DIR,"", get_dirname());

    // 챌린지 종료일 스케줄러 -> 종료일 00:00시를 기준으로 배포한 코인 외에 남은 코인 회수
    $regtime = now();

    $sql = "insert into scheduler_test set test_info = '테스트1차', regtime = '".$regtime."'";
    $query = insertQuery($sql);

    // echo "스케줄러가 정상적으로 작동 중입니다";

    exit;
?>