<?
//지각, 오늘업무, 퇴근 페널티 레이어
//각 페널티 2회 이상일때 레이어 출력 됩니다.
/*
페널티 내역 조회
penalty_info1 : 지각 페널티 등록내역 
penalty_info2 : 오늘업무 페널티 등록내역 
penalty_info3 : 퇴근 페널티 등록내역 

*/


if($user_id=='sadary0@nate.com'){

	//echo "mem_work_penalty : ". $mem_work_penalty = '';
	//echo "<Br>";
	//echo "login_cnt_check[".$user_id."] ==> " . $login_cnt_check[$user_id] = 2;
	//echo "mem_quit_penalty :: " .$mem_quit_penalty = true;
	//$login_cnt_check[$user_id] = 2;
	//$penalty_info1 = "";
	//$mem_work_penalty  = "2";
	//$mem_quit_penalty  = "2";
}

if ($plt0['cnt'] > 1){

	//오늘업무에서 레이어 제외 처리
	if (get_dirname() != 'todaywork'){	?>
		<!-- 페널티 -->
		<div class="penalty_first"<?=($penalty_info1 || $_COOKIE['pf_close_01']=='1')?" style='display:none';":""?> id="penalty_first_01">
			<div class="pf_deam"></div>
			<div class="pf_in">
				<div class="pf_box">
					<span><strong>[긴급]</strong>지각 페널티 카드가 발동했습니다.</span>
					<button id="penalty_bt_01">미션 수행하기</button>
				</div>
			</div>

			<div class="pf_bottom">
				<div class="pf_chk">
					<input type="checkbox" name="no_more" id="more_01">
					<label for="more_01">더 이상 보지 않기</label>
				</div>
				<div class="pf_close">
					<button id="pf_close_01"><span>닫기</span></button>
				</div>
			</div>

		</div>
	<?}?>

	<div class="penalty_layer" style="display: none;" id="penalty_layer_01">
		<div class="pl_deam"></div>
		<div class="pl_in">
			<div class="pl_box">
				<div class="pl_box_in">
					<div class="pl_close" id="pl_close_01">
						<button><span>닫기</span></button>
					</div>
					<div class="pl_top">
						<strong>지각 페널티 카드</strong>
					</div>
					<div class="pl_area">
						<div class="pl_user">
							<div class="user_img" style="background-image:url(<?=$member_row_info['profile_img_src']?>);"></div>
							<div class="user_name">
								<strong><?=$member_row_info['name']?></strong>
								<?=$member_row_info['part']?>
							</div>
						</div>

						<div class="pl_left">
							<p>지각 페널티 카드는 1주일 내 지각 2회 이상 시 자동으로 발동됩니다.</p>
							<p>종료 시간 내 팀장님 혹은 대표님과 10분간 면담을 진행합니다.<br>
							첨부된 수료증을 다운로드 받아 면담자에게 사인을 받습니다.<br>
							<strong>수료증 이미지를 찍어 업로드</strong>하면 미션 완료!</p>
							<p>미션 실패 시에는 관리자에게 알림이 보내집니다.</p>
							<p>📌 오늘업무에 <strong>[연차]</strong> 등록 시 해당일은 페널티가 적용되지 않습니다.</p>
							<div class="pl_file">
								<div class="file_box" id="file_box_01" value="325">
									<div class="file_desc">
										<span>수료증.hwp</span>
										<strong>다운로드</strong>
									</div>
								</div>
							</div>
						</div>
						<div class="pl_right">
							<div class="pl_right_01">
								<div class="pl_img">
									<div class="pl_img_on" id="pl_preview_01">
										<img src="<?=$penalty_img_src?>" alt="" />
									</div>
									<div class="pl_img_comp" id="pl_img_comp_01">
										<img src="<?=$penalty_img_src?"/html/images/pre/img_comp.png":""?>" alt="COMPLETE" <?=$penalty_img_src?"":"style='display:none;'"?> />
									</div>
								</div>
							</div>
							<div class="pl_right_02">
								<div class="pl_time">
									<span>남은 시간</span>
									<strong>07:50</strong>
								</div>
								<div class="pl_upload">
									<div class="file_box" id="pl_img_btn">
										<input type="file" class="input_file" id="pl_file_01" name="pl_file_01">
										<label for="pl_file_01" class="label_file"><span>이미지 올리기</span></label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="layer_cha_image" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_cha_image_in">
			<div class="layer_cha_image_box">
				<div class="layer_cha_image_box_in">
					<img src="/html/images/pre/20210927_01.jpg" alt="" id="layer_penalty_img"/>
				</div>
			</div>
		</div>
	</div>
<?
}
?>


	<?
	//오늘업무 페널티카드
	if($mem_work_penalty){

		//오늘업무에서 레이어 제외 처리
		if (get_dirname() != 'todaywork'){	?>
			<!-- 페널티 -->
			<div class="penalty_first"<?=($penalty_info2 || $_COOKIE['pf_close_02']=='1')?" style='display:none';":""?> id="penalty_first_02">
				<div class="pf_deam"></div>
				<div class="pf_in">
					<div class="pf_box">
						<span><strong>[긴급]</strong>오늘업무 페널티 카드가 발동했습니다.</span>
						<button id="penalty_bt_02">미션 수행하기</button>
					</div>
				</div>

				<div class="pf_bottom">
					<div class="pf_chk">
						<input type="checkbox" name="no_more" id="more_02">
						<label for="more_02">더 이상 보지 않기</label>
					</div>
					<div class="pf_close">
						<button id="pf_close_02"><span>닫기</span></button>
					</div>
				</div>
			</div>
		<?}?>

		<div class="penalty_layer" style="display: none;" id="penalty_layer_02">
			<div class="pl_deam"></div>
			<div class="pl_in">
				<div class="pl_box">
					<div class="pl_box_in">
						<div class="pl_close" id="pl_close_02">
							<button><span>닫기</span></button>
						</div>
						<div class="pl_top">
							<strong>오늘업무 페널티 카드</strong>
						</div>
						<div class="pl_area">
							<div class="pl_user">
								<div class="user_img" style="background-image:url(<?=$member_row_info['profile_img_src']?>);"></div>
								<div class="user_name">
									<strong><?=$member_row_info['name']?></strong>
									<?=$member_row_info['part']?>
								</div>
							</div>
							<div class="pl_left">
							<p>오늘업무 페널티 카드는 오늘업무를 2회 이상 작성하지 않으면 자동<br>
							발동됩니다.</p>
							<p><strong>리워디 내 오늘업무를 작성한 뒤 화면을 캡쳐해 이미지를 올려주세요.</strong><br> 
							미션 실패 시에는 관리자에게 알림이 보내집니다</p>
							<p>📌 오늘업무에 <strong>[연차]</strong> 등록 시 해당일은 페널티가 적용되지 않습니다.</p>
								<div class="pl_file">
									<div class="file_box" id="file_box_02" value="326">
										<div class="file_desc">
											<span>오늘업무 페널티 미션 등록 예시.jpg</span>
											<strong>다운로드</strong>
										</div>
									</div>
								</div>
							</div>
							<div class="pl_right">
								<div class="pl_right_01">
									<div class="pl_img">
										<div class="pl_img_on" id="pl_preview_02">
											<img src="<?=$penalty_img_src?>" alt="" />
										</div>
										<div class="pl_img_comp" id="pl_img_comp_02">
											<img src="<?=$penalty_img_src?"/html/images/pre/img_comp.png":""?>" alt="COMPLETE" <?=$penalty_img_src?"":"style='display:none;'"?>>
										</div>
									</div>
								</div>
								<div class="pl_right_02">
									<div class="pl_time">
										<span>남은 시간</span>
										<strong>07:50</strong>
									</div>
									<div class="pl_upload">
										<div class="file_box">
											<input type="file" class="input_file" id="pl_file_02" name="pl_file_02">
											<label for="pl_file_02" class="label_file"><span>이미지 올리기</span></label>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?}?>


	<?
	//퇴근 페널티
	if($mem_quit_penalty){

		
		if (date("H") >= ATTEND_STIME){

			//오늘업무에서 레이어 제외 처리
			if (get_dirname() != 'todaywork'){	?>
				<!-- 페널티 -->
				<div class="penalty_first"<?=($penalty_info3 || $_COOKIE['pf_close_03']=='1')?" style='display:none';":""?> id="penalty_first_03">
					<div class="pf_deam"></div>
					<div class="pf_in">
						<div class="pf_box">
							<span><strong>[긴급]</strong>퇴근 페널티 카드가 발동했습니다.</span>
							<button id="penalty_bt_03">미션 수행하기</button>
						</div>
					</div>

					<div class="pf_bottom">
						<div class="pf_chk">
							<input type="checkbox" name="no_more" id="more_03">
							<label for="more_03">더 이상 보지 않기</label>
						</div>
						<div class="pf_close">
							<button id="pf_close_03"><span>닫기</span></button>
						</div>
					</div>
				</div>
			<?}?>

			<div class="penalty_layer" style="display: none;" id="penalty_layer_03">
				<div class="pl_deam"></div>
				<div class="pl_in">
					<div class="pl_box">
						<div class="pl_box_in">
							<div class="pl_close" id="pl_close_03">
								<button><span>닫기</span></button>
							</div>
							<div class="pl_top">
								<strong>퇴근 페널티 카드</strong>
							</div>
							<div class="pl_area">
								<div class="pl_user">
									<div class="user_img" style="background-image:url(<?=$member_row_info['profile_img_src']?>);"></div>
									<div class="user_name">
									<strong><?=$member_row_info['name']?></strong>
										<?=$member_row_info['part']?>
									</div>
								</div>
								<div class="pl_left">
									<p>퇴근 페널티 카드는 2회 이상 퇴근을 기록하지 않으면 자동 발동됩니다.</p>
									<p><strong>퇴근 시간을 표시하고 해당 화면을 캡쳐해 이미지를 올려주세요.</strong><br> 
									미션 실패 시에는 관리자에게 알림이 보내집니다.</p>
									<p>📌 오늘업무에 <strong>[연차]</strong> 등록 시 해당일은 페널티가 적용되지 않습니다.</p>
									<div class="pl_file">
										<div class="file_box" id="file_box_03" value="327">
											<div class="file_desc">
												<span>퇴근 페널티 미션 등록 예시.jpg</span>
												<strong>다운로드</strong>
											</div>
										</div>
									</div>
								</div>
								<div class="pl_right">
									<div class="pl_right_01">
										<div class="pl_img">
											<div class="pl_img_on" id="pl_preview_03">
											<img src="<?=$penalty_img_src?>" alt="" />
											</div>
											<div class="pl_img_comp" id="pl_img_comp_03">
												<img src="<?=$penalty_img_src?"/html/images/pre/img_comp.png":""?>" alt="COMPLETE" <?=$penalty_img_src?"":"style='display:none;'"?>>
											</div>
										</div>
									</div>
									<div class="pl_right_02">
										<div class="pl_time">
											<span>남은 시간</span>
											<strong>07:50</strong>
										</div>
										<div class="pl_upload">
											<div class="file_box">
												<input type="file" class="input_file" id="pl_file_03" name="pl_file_03">
												<label for="pl_file_03" class="label_file"><span>이미지 올리기</span></label>
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?}?>
	<?}?>
