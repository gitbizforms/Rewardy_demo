<div class="layer_party_end" style="display:none;">
		<div class="lpe_deam"></div>
		<div class="lpe_in">
			<div class="lpe_box">
				<div class="lpe_box_in">
					<div class="lpe_top">
						<strong><?=$p_info['title']?></strong>
					</div>
					<div class="lpe_area">
						<div class="lpe_date"><?=date_format($project_start, 'Y-m-d')?> ~ <?=date_format($project_end, 'Y-m-d')?></div>
						<div class="lpe_txt">해당 파티를 종료하며 적립된 코인은 파티 <br />멤버에게 공평하게 분배됩니다.</div>
						<div class="lpe_coin">
							<span>누적코인</span>
							<strong><?=number_format($project_coin)?></strong>
						</div>
						<div class="lpe_member">
							<span>멤버수/예상코인</span>
							<strong><?=$project_user_cnt?>명 / <?=number_format($r_coin)?></strong>
						</div>
					</div>
					<div class="lpe_btn">
						<input type="hidden" id="party_idx" value="<?=$party_idx?>"/>
						<input type="hidden" id="party_e_date" value="<?=date_format($project_end, 'Y-m-d')?>"/>
						<input type="hidden" id="party_r_coin" value="<?=$r_coin?>"/>
						<input type="hidden" id="party_r_name" value="<?=$reward_my_name?>"/>
						<input type="hidden" id="party_close_flag" value="<?=$party_close?>"/>
						<button class="lpe_off" id="lpe_off"><span>취소</span></button>
						<button class="lpe_on" id="lpe_on"><span>파티 종료</span></button>
					</div>
				</div>
			</div>
		</div>
	</div>