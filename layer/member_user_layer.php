<div class="layer_user" id="layer_user" style = "display:none;">
	<div class="layer_deam"></div>
	<div class="layer_user_in">
		<div class="layer_user_box none" id="layer_test_01">
			<div class="layer_user_search">
				<div class="layer_user_search_desc">
					<?if($get_dirname=='party'){?>
						<strong>참여자 설정</strong>
					<?}else if($get_dirname=='todaywork'){?>
						<strong>업무 받을 사람 선택</strong>
					<?}else if($get_dirname=='challenge' || $get_dirname=='live'){?>
						<strong>참여자설정</strong>
					<?}?>
					<span id="usercnt">전체 <?=$member_total_cnt?>명</span>
				</div>
				<div class="layer_user_search_box">
					<input type="text" class="input_search" placeholder="이름, 부서명을 검색" id="input_todaywork_search"/>
					<button id="input_todaywork_search_btn"><span>검색</span></button>
				</div>
			</div>
			<div class="layer_user_slc_list">
				<div class="layer_user_slc_list_in" id="layer_user_slc_list_in">
					<ul>
						<?if($get_dirname=='challenge'){
							$sql = "select a.idx, a.challenges_idx, a.email, a.name, (select idx from work_member where email = a.email and state = '0') as user_idx, b.file_path, b.file_name from work_challenges_user as a, work_member_profile_img as b where a.email = b.email and a.state = '0' and b.state = '0' and a.challenges_idx = '".$chall_idx."' order by idx desc";
							$chk_arr = selectAllQuery($sql);
								for($j=0;$j<count($chk_arr['idx']); $j++){?>
								<li id="user_<?=$chk_arr['user_idx'][$j]?>">
									<div class="user_img" style="background-image:url('https://rewardy.co.kr<?=$chk_arr['file_path'][$j].$chk_arr['file_name'][$j]?>')"></div>
									<div class="user_name">
										<strong><?=$chk_arr['name'][$j]?></strong>
									</div>
									<button class="user_slc_del" value="<?=$chk_arr['user_idx'][$j]?>" title="삭제"><span>삭제</span></button>
								</li>
							<?}?>
						<?}?>
					</ul>
				</div>
			</div>
			<div class="layer_user_info">
				<ul>
				<?php
					$memberData = [];
					// 회원 정보를 정리합니다.
						for ($i = 0; $i < count($member_list_info['idx']); $i++) {
							$member_idx = $member_list_info['idx'][$i];
							$member_uid = $member_list_info['email'][$i];
							$member_name = $member_list_info['name'][$i];
							$partno = $member_list_info['partno'][$i];
							$profile_type = $member_list_info['profile_type'][$i];
							$profile_img_idx = $member_list_info['profile_img_idx'][$i];
							$profile_img =  'https://rewardy.co.kr'.$member_list_info['file_path'][$i].$member_list_info['file_name'][$i];
							

							$memberData[$partno][] = [
								'idx' => $member_idx,
								'uid' => $member_uid,
								'name' => $member_name,
								'profile' => $profile_type,
								'image' => $profile_img,
							];
						}
					// 부서 정보와 회원 정보를 출력합니다.
					foreach ($part_info['partno'] as $i => $partno) {
						$part_cnt = count($memberData[$partno]);
					?>
					<li>
						<dl class="on">
							<dt>
								<button class="btn_team_slc" id="btn_team_slc_<?= $partno ?>"><span><?= $part_info['part'][$i] ?> <?= $part_cnt ?></span></button>
								<button class="btn_team_toggle" id="btn_team_toggle"><span>열고닫기</span></button>
							</dt>
							<?php foreach ($memberData[$partno] as $j => $member) {
								$sql = "select challenges_idx, email from work_challenges_user where email = '".$member['uid']."' and challenges_idx = '".$chall_idx."' and state = '0'";
								$chk_user = selectQuery($sql);
							?>
								<dd id="udd_<?= $member['idx'] ?>">
									<button value="<?= $member['idx'] ?>" id="team_<?= $partno ?>" class="<?=$chk_user?'on':''?>">
									<?php echo $member['image']?>
										<div class="user_img" style="background-image:url('<?=$member['profile'] >= '0'?$member['image']:"/html/images/pre/img_prof_default.png"?>');" id="profile_character_img"></div>
										<div class="user_name" value="<?= $member['uid'] ?>">
											<strong><?= $member['name'] ?></strong>
											<span><?= $part_info['part'][$i] ?></span>
										</div>
									</button>
								</dd>
							<?php } ?>
						</dl>
					</li>
				<?php } ?>

				</ul>
			</div>
		</div>

		<div class="layer_user_btn">
			<button class="layer_user_all_slc" id="layer_user_all_slc"><span>전체선택</span></button>
			<button class="layer_user_cancel" id="layer_user_cancel"><span>취소</span></button>
			<?if($get_dirname=='challenge'){?>
				<button class="layer_user_submit<?=$chk_arr?' on':''?>" id="layer_challenges_user"><span>설정하기</span></button>
			<?}else{?>
				<button class="layer_user_submit" id="layer_todaywork_user"><span>설정하기</span></button>
			<?}?>
		</div>
	</div>
</div>