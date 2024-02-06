<?php

$this_url = explode("/",$_SERVER['REQUEST_URI']);
$url = $this_url[2];

$comcoin_arr = ['comcoin_account.php','comcoin.php'];
$member_arr = ['member_list.php','member_add.php'];
$member_coin = ['comcoin_mem.php','comcoin_mem_list.php'];

?>
<script type="text/javascript">
   $(document).ready(function(){
        $(".admin_depth_01 strong.admin_depth_01_link").mouseenter(function(){
            $(this).next(".admin_depth_02_area").show();
        });
        $(".admin_depth_01").mouseleave(function(){
            $(this).find(".admin_depth_02_area").hide();
        });
    });
</script>
    <div class="rb_nav_admin_depth rew_member_tab_in">
        <ul class="admin_depth_01_area">
            <li class="comlogo admin_depth_01 <?=$url=='admin_setting.php'?' on':''?>">
            <a href = "#" class="admin_depth_01_link"><span>환경설정</span></a>
            </li>
            <li class="member_list admin_depth_01 <?=in_array($url,$member_arr)?' on':''?>">
            <a href = "#" class="admin_depth_01_link"><span>멤버관리</span></a>
            </li>
            <li class="comcoin admin_depth_01 <?=in_array($url,$comcoin_arr)?' on':''?>">
            <a href = "#" class="admin_depth_01_link"><span>공용코인 관리</span></a>
            </li>
            <li class="comcoin_member admin_depth_01 <?=in_array($url,$member_coin)?' on':''?>">
            <a href = "#" class="admin_depth_01_link"><span>멤버별 공용코인</span></a>
            </li>
            <li class="comcoin_out_page admin_depth_01 <?=$url=='comcoin_out.php'?' on':''?>">
                <a href = "#" class="admin_depth_01_link"><span>출금신청 내역</span></a>
            </li>
            <li class="comcoin_pay_page admin_depth_01 <?=$url=='comcoin_pay.php'?' on':''?>">
                <a href = "#" class="admin_depth_01_link"><span>결제 내역</span></a>
            </li>
        </ul>
        </div>