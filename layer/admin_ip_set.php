<div class="t_layer admin_ip_set" style="display:none;">
        <div class="tl_deam"></div>
        <div class="lay_rec">
            <div class="rec_tit">
                <h2>출근도장 IP 추가</h2>
            </div>
            <div class="rec_input">
                <div class="rec_input_in">
                    <div class="ip_plus">
                        <input type="number" placeholder="000" class="coin_total" id="ip1">
                        <em>.</em>
                        <input type="number" placeholder="000" class="coin_total" id="ip2">
                        <em>.</em>
                        <input type="number" placeholder="000" class="coin_total" id="ip3">
                        <em>.</em>
                        <input type="number" placeholder="000" class="coin_total" id="ip4">
                        <button id="chul_ip_submit"><span>추가</span></button>
                    </div>
                    <div class="time_ip_list">
                        <? for($i=0; $i<$ip_length; $i++){?>
                            <span id="ip_idx_<?=$company_ip['idx'][$i]?>"><?=$company_ip['ip'][$i]?><button class="ip_delete" value="<?=$company_ip['idx'][$i]?>"><span>닫기</span></button></span>
                        <?}?>
                    </div>
                </div>
            </div>
            <div class="bottom_btn_ip">
                <div class="close_btn" id="ip_cancel"><button><span>취소</span></button></div>
            </div>
        </div>
    </div>