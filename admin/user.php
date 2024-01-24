<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir  . "inc_lude/header.php";
?>

<div class="todaywork_wrap">
	<div class="t_in">
		<!-- header -->
		<?php

			//top페이지
			include $home_dir . "inc_lude/top.php";

			//쿼리스트링값을 복호화하여 변수지정
			if($_SERVER['QUERY_STRING']){
				parse_str(Decrypt($_SERVER['QUERY_STRING']));
			}

			if(!$send_email && !$to_email){
				header("location:/");
				exit;
			}

			$sql = "select idx, email, highlevel, name, company from work_member where state='0' and email='".$send_email."'";
			$res = selectQuery($sql);
			if($res['idx']){
				$company = $res['company'];
			}

			//메일수신확인 체크
			if($sendno){
				
				$sql = "select idx from work_sendmail where state='0' and idx='".$sendno."'";
				$res = selectQuery($sql);

				if($res['idx']){
					$sql = "update work_sendmail set state='1', checkdate=".DBDATE." , receive_ip='".LIP."' where idx='".$sendno."'";
					$res = updateQuery($sql);
				}

			}

		?>

		<div class="t_contents">
			<div class="tc_in">
				<div class="tc_join">
					<div class="tc_join_in">
						<div class="tc_box_05">
							<div class="tc_box_05_in">
								<div class="tc_box_tit">
									<strong>사용자 인증</strong>
								</div>
								<div class="tc_box_list">
									<ul>
										<li>
											<div class="tc_input_none">
												<input type="text" id="d1" name="" class="input_01" value="<?=$company?>" disabled="disabled" />
												<label for="d1" class="label_01">
													<strong class="label_tit">회사명</strong>
												</label>
											</div>
										</li>
										<li>
											<div class="tc_input_none">
												<input type="text" id="d2" name="" class="input_01" value="<?=$to_email?>" />
												<label for="d2" class="label_01">
													<strong class="label_tit">이메일</strong>
												</label>
											</div>
										</li>
										<li>
											<div class="tc_input">
												<input type="text" id="d3" name="" class="input_01" />
												<label for="d3" class="label_01">
													<strong class="label_tit">이름</strong>
												</label>
											</div>
										</li>
										<li>
											<div class="tc_input">
												<input type="text" id="d6" name="" class="input_01" />
												<label for="d6" class="label_01">
													<strong class="label_tit">부서명</strong>
												</label>
											</div>
										</li>
										<li>
											<div class="tc_input">
												<input type="password" id="d4" name="" class="input_01" />
												<label for="d4" class="label_01">
													<strong class="label_tit">비밀번호</strong>
												</label>
											</div>
										</li>
										<li>
											<div class="tc_input">
												<input type="password" id="d5" name="" class="input_01" />
												<label for="d5" class="label_01">
													<strong class="label_tit">비밀번호 확인</strong>
												</label>
											</div>
										</li>
									</ul>
								</div>
								<div class="tc_box_btn" id="userchk">
									<a href="javascript:void(0);">완료</a>
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
	
<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/오늘일"; /* 페이지 이름 지정 Contents Path */
</script>

</body>


</html>
