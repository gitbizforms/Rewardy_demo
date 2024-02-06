<!-- <div class="layer_withdraw" style="display:none;">
	<div class="layer_deam"></div>
	<div class="layer_withdraw_in">
		<div class="layer_withdraw_box">
			<div class="layer_withdraw_top">
				<div class="layer_withdraw_qna">
					<div class="qna">
						<strong class="layer_withdraw_qna_tit">내가 획득한 코인</strong>
						<span class="qna_q">?</span>
						<div class="qna_a">
							<span>내가 획득한 코인</span>
						</div>
					</div>
				</div>
				<div class="layer_withdraw_coin" id="layer_withdraw_coin">
					<strong><span><?=$reward_user_info['coin']?></span></strong>
				</div>
			</div>
			<div class="layer_withdraw_mid">
				<div class="layer_withdraw_btns" id="layer_withdraw_btns">
					<ul>
						<li><button value="10000"><span>+ 1만</span></button></li>
						<li><button value="30000"><span>+ 3만</span></button></li>
						<li><button value="50000"><span>+ 5만</span></button></li>
						<li><button value=""><span>MAX</span></button></li>
					</ul>
					<button class="btn_coin_reset" id="btn_coin_reset"><span>초기화</span></button>
				</div>
				<div class="layer_withdraw_input">
					<input type="text" class="input_withdraw" placeholder="출금할 금액을 입력하세요." id="withdraw_coin"/>
				</div> -->

				<!-- <div class="withdraw_tax">
					<span>출금예상수수료 : 0원</span>
				</div> -->
					
				<!-- <div class="layer_withdraw_info">
					<div class="layer_withdraw_bank" id="layer_withdraw_bank">
						<div class="layer_withdraw_bank_in">
							<button class="btn_bank_on" id="btn_bank_on"><span>은행</span></button>
							<ul>
								<?for($i=0;$i<count($bank_info['idx']); $i++){?>
									<li><button value="<?=$bank_info['idx'][$i]?>"><span><?=$bank_info['name'][$i]?></span></button></li>
								<?}?>
							</ul>
						</div>
					</div>
					<input type="text" class="input_bank_num" placeholder="계좌번호" id="input_bank_num"/>
					<input type="text" class="input_bank_user" placeholder="예금주" id="input_bank_user"/>
				</div>
				<div class="layer_withdraw_desc">
					<ul>
						<li>출금 신청한 코인은 돌아오는 주 화요일에 일괄 지급됩니다.</li>
						<li>작성해 주신 계좌로 입금되며, 오기입으로 인해 잘못 송금된 코인에 대해서는 책임지지 않습니다.</li>
					</ul>
				</div>
				<div class="layer_withdraw_btn">
					<button class="btn_withdraw_off" id="btn_withdraw_off"><span>취소</span></button>
					<button class="btn_withdraw_on" id="btn_withdraw_on"><span>출금 신청하기</span></button>
				</div>
			</div>
		</div>
	</div>
</div> -->

<div class="t_layer rew_out" id="layer_withdraw">
    <div class="tl_deam"></div>
		<div class="lay_out">
		<div class="out_tit">
			<h2>출금 신청</h2>
		</div>
		<div class="out_input">
			<div class="out_input_in">
				<div class="total_coin" id="layer_withdraw_coin">
					<span>내가 획득한 코인</span>
					<p><?=$reward_user_info['coin']?></p>
				</div>
				<div class="out_coin">
					<div class="coin_btn">
					<div class="coin_plus" id="layer_withdraw_btns">
						<button value="10000"><span>+1만</span></button>
						<button value="30000"><span>+3만</span></button>
						<button value="50000"><span>+5만</span></button>
						<button value="100000"><span>+10만</span></button>
						<button value=""><span>MAX</span></button>
					</div>
					<button class="coin_last" id="btn_coin_reset"><span>초기화</span></button>
					</div>
					<input type="text" placeholder="출금할 코인 금액을 입력하세요." id="withdraw_coin">
				</div>
			</div>
			<div class="coin_out_txt">
				<p>
					· 출금신청은 10,000코인 단위로 가능합니다. <br>
					· 출금 신청한 코인은 우리회사의 관리팀에서 정산해 드립니다. <br>
					· 회사의 정책에 따라서 급여에 포함되거나 별도로 지급될 수 있습니다. <br>
					· 출금 신청한 내역의 진행상황은 보상/코인 메뉴에서 확인이 가능합니다. <br>
					· 출금신청에 대한 상세한 문의는 우리회사의 리워디 서비스 관리자에게 문의하시기 바랍니다.
				</p>
			</div>
		</div>
		<div class="bottom_btn">
			<div class="close_btn" id="btn_withdraw_off"><button><span>취소</span></button></div>
			<div class="submit_btn" id="btn_withdraw_on"><button><span>출금 신청하기</span></button></div>
		</div>
	</div>
</div>