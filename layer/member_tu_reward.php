<!-- 보상하기 -->
<div class="layer_reward" id="layer_reward" style="display:none;">
	<div class="lr_deam"></div>
	<div class="lr_in">
		<div class="lr_box">
			<div class="lr_box_in">
				<div class="lr_close" id="lr_close">
					<button><span>닫기</span></button>
				</div>
				<div class="lr_top">
					<strong>열심히 하는 동료에게 보상은 언제나 옳습니다!</strong>
				</div>
				<div class="lr_area">
					<ul>
						<?
						for($i=0; $i<count($coin_reward_info['idx']); $i++){
							$reward_info_idx = $coin_reward_info['idx'][$i];
							$reward_info_coin = $coin_reward_info['coin'][$i];
							$reward_info_icon = $coin_reward_info['icon'][$i];
							$reward_info_memo = $coin_reward_info['memo'][$i];
							$reward_info_icon = urldecode($reward_info_icon);
						?>
						<li>
							<button class="btn_lr_0<?=($i+1)?>" value="<?=$reward_info_idx?>">
								<div class="lr_txt">
									<span><?=$reward_info_icon?></span>
									<strong><?=$reward_info_memo?></strong>
								</div>
								<div class="lr_coin">
									<em><?=number_format($reward_info_coin)?></em>
								</div>
							</button>
						</li>
						<?}?>
					</ul>
				</div>
				<div class="lr_bottom">
					<input type="hidden" id="lr_uid"/>
					<input type="hidden" id="lr_val"/>
					<input type="hidden" id="lr_work_idx"/>
					<input type="text" class="lr_input" placeholder="보상할 코인을 입력하세요." id="lr_input"/>
					<input type="text" class="lr_input_text" placeholder="메시지를 입력하세요." id="lr_input_text">
					<button class="lr_btn tuto tuto_03_02" id="lr_btn"><span>보상하기</span></button>
					<p>(현재보유 공용코인 : <strong><?=$common_coin?></strong>코인)</p>
				</div>
			</div>
		</div>
	</div>
</div>