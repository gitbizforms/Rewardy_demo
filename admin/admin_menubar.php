<?php

$this_url = explode("/",$_SERVER['REQUEST_URI']);
$url = $this_url[2];

$comcoin_arr = ['comcoin_account.php','comcoin.php'];
$member_arr = ['member_list.php','member_add.php'];
$member_coin = ['comcoin_mem.php','comcoin_mem_list.php'];

?>
<div class="rew_member_tab">
    <div class="rew_member_tab_in">
        <ul>
            <li class="comlogo <?=$url=='admin_setting.php'?' on':''?>"><a href="#"><span>초기 설정</span></a></li>
            <li class="member_list <?=in_array($url,$member_arr)?' on':''?>"><a href="#"><span>멤버관리</span></a></li>
            <li class="comcoin <?=in_array($url,$comcoin_arr)?' on':''?>"><a href="#"><span>공용코인 관리</span></a></li>
            <li class="comcoin_member <?=in_array($url,$member_coin)?' on':''?>"><a href="#"><span>멤버별 공용코인</span></a></li>
            <li class="comcoin_out_page <?=$url=='comcoin_out.php'?' on':''?>"><a href="#"><span>코인출금 신청내역</span></a></li>
        </ul>
    </div>
</div>