<?php

	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir  . "inc_lude/header.php";

	//챌린지 카테고리
	$sql = "select idx, name from work_category where state='0' and type='2' order by idx asc";
	$cate_info = selectAllQuery($sql);
	for($i=0; $i<count($cate_info['idx']); $i++){
		$chall_cate[$cate_info['idx'][$i]] = $cate_info['name'][$i];
	}
?>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<link href="/editor/summernote/summernote-lite.css<?php echo VER;?>" rel="stylesheet">
<script src="/editor/summernote/summernote.js<?php echo VER;?>"></script>

<div class="todaywork_wrap">
	<div class="t_in">
		<!-- header -->
		<?php
			//top페이지
			include $home_dir . "inc_lude/top.php";
			$idx = $_GET['idx'];
			$idx = preg_replace("/[^0-9]/", "", $idx);
			if($idx){
				$sql = "select idx, state, cate, email, name, coin, sdate, edate, type, title, title_emoji, emoji, contents, action1, action2, outputchk, convert(char(10) , regdate, 120) as reg from work_challenges";
				$sqk = $sql .= " where state='0' and idx='".$idx."'";
				$res = selectQuery($sql);

				if($res['idx']){
					$sql = "select idx, contents from work_contents where work_idx='".$idx."'";
					$contents_info = selectQuery($sql);

					if($contents_info['idx']){
						$contents = urldecode($contents_info['contents']);
					}

					if($res['emoji'] == 1){
						$chall_title = urldecode($res['title_emoji']);
					}else{
						$chall_title = $res['title'];
					}

					//조회수 업데이트
					$sql = "update work_challenges set pageview = CASE WHEN pageview >=0 THEN pageview + 1 ELSE 0 END where idx='".$res['idx']."'";
					updateQuery($sql);


					if($chall_cate[$res['cate']]){
						$category = "[".$chall_cate[$res['cate']]."]";
					}


					$sql = "select idx, comment from work_comment where state='0' and link_idx='".$idx."'";
					$comment_row = selectAllQuery($sql);

				}
			}
		?>

		<div class="t_contents">
			<div class="tc_in">
				<div class="tc_page">
					<div class="tc_page_in">
						<div class="tc_box_09">
							<div class="tc_box_09_in">
								<div class="tc_box_tit">
									<strong>챌린지</strong>
								</div>

								<div class="tc_chall_view">
									<div class="tc_chall_view_in">
										<div class="chall_title">
											<strong><?=$category?> <?=$chall_title?></strong>
										</div>
										<div class="chall_coin">
											<span>coin</span>
											<strong><?=number_format($res['coin'])?></strong>
											<span>challenge</span>
										</div>
										<div class="chall_date">
											<ul>
												<li>
													<span>등록</span>
													<strong><?=$res['name']?></strong>
													<em><?=$res['reg']?></em>
												</li>
												<li>
													<span>기간</span>
													<strong><?=$res['sdate']?> ~ <?=$res['edate']?></strong>
												</li>
											</ul>
										</div>
										<div class="chall_view">
											<?=$contents?>
										</div>
										<!--<div class="chall_project">
											<span>행동지침</span>
											<ul>
												<li>
													<strong>1. <?=$res['action1']?></strong>
												</li>
												<li>
													<strong>2. <?=$res['action2']?></strong>
												</li>
											</ul>
										</div>-->


										<?if($res['outputchk'] == '1'){?>
											<div class="chall_file"><!-- 파일첨부 있을경우만 -->
												<span id="chall_file_txt">결과물로 올린 파일명 : 없음</span>
											</div>
										<?}?>

										<?if($user_id != $res['email']){?>
											<div class="chall_view">
												<strong>댓글 : <input type="text" id="comment" class="input_01" style="width:420px;" placeholder="댓글을 입력해주세요."/></strong>
												<button id="comment_btn" value="<?=$res['idx']?>">참여하기</button>
											</div>
										<?}?>





									</div>
								</div>

								<div class="tc_box_btns">
									<a href="/challenge/list.php">뒤로가기</a>
									<?if($res['outputchk'] == '1'){?>
										<div class="tc_box_btns_upload">
											<input type="file" id="file2" name="file2"/>
											<label for="file2"><span>결과물 올리기</span></label>
										</div>
									<?}?>
									<button class="tc_upload" id="challenges_complete" value="<?=$idx?>"><span>챌린지 완료</span></button>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php

			//footer페이지
			include $home_dir  . "inc_lude/footer.php";

		?>
	</div>
</div>


	<?php
		//login페이지
		include $home_dir  . "inc_lude/login_layer.php";
	?>


	<?php
		//일정페이지
		include $home_dir  . "works/write_date.php";
	?>

	<?php
		//요청페이지
		include $home_dir  . "works/write_req.php";
	?>

	<?php
		//목표페이지
		include $home_dir  . "works/write_goal.php";
	?>


<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/오늘일"; /* 페이지 이름 지정 Contents Path */
</script>

</body>


</html>
