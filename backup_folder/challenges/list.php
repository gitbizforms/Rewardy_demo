<?php
$newUrl = "https://rewardy.co.kr/challenge/list.php";
header("Location: $newUrl");
exit;
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir  . "inc_lude/header.php";
	include $home_dir . "/challenges/challenges_header.php";
?>

    <div class="todaywork_wrap">
        <div class="t_in">
            <!-- header -->
            <?php
				//top페이지
				include $home_dir . "inc_lude/top.php";


				/*$sql = "select idx , state, sdate, edate, DATEDIFF(DD,sdate, edate ) as dd ,(case when convert(varchar(10), getdate(), 120)  > edate then '종료' else '진행중' end ) as kk 
				from work_challenges where state=0 order by dd asc";
				*/


				/*
				select a.idx, a.cate, a.title, a.coin, DATEDIFF(DD, a.sdate, a.edate ) as chllday, a.sdate, a.edate,
				(SELECT count(idx) FROM work_challenges_user WHERE state=0 and challenges_idx = a.idx) AS chamyeo,
				(SELECT count(idx) FROM work_challenges_com WHERE challenges_idx = a.idx) AS challenge
				from work_challenges as a left join work_challenges_com as b on(a.idx=b.challenges_idx) 
				where a.state=0 and a.edate > convert(varchar(10), getdate(), 120)
				group by a.idx, cate, title, coin , b.challenges_idx , DATEDIFF(DD,a.sdate, a.edate ), sdate, edate
				order by a.idx desc
				*/

				$sql = "select idx, cate, email, name, title, title_emoji, emoji, sdate, edate, contents, action1, action2, type from work_challenges where state!='9' and view_flag='0'";
				$sql = $sql .= " and ( sdate >= convert(varchar(10), getdate(), 120) or convert(varchar(10), getdate(), 120) <= edate ) order by idx desc";
				$res = selectAllQuery($sql);

				$sql = "select challenges_idx , convert(varchar(10), regdate, 120) as wdate from work_challenges_com where state!='9' and email='".$user_id."' group by challenges_idx, convert(varchar(10), regdate, 120)";
				$chall_com = selectAllQuery($sql);
				for($i=0; $i<count($chall_com['challenges_idx']); $i++){
					$chall_data_one[$i] = $chall_com['challenges_idx'][$i];
					$chall_data_daily[$chall_com['wdate'][$i]][] = $chall_com['challenges_idx'][$i];
				}
				

				//챌린지 카테고리
				$sql = "select idx, name from work_category where state='0' and type='2' order by idx asc";
				$cate_info = selectAllQuery($sql);
				for($i=0; $i<count($cate_info['idx']); $i++){
					$chall_cate[$cate_info['idx'][$i]] = $cate_info['name'][$i];
				}

				/*
				print "<pre>";
				print_r($chall_data_one);

				print_r($chall_data_daily);
				print "</pre>";
				*/
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
									
									<div class="tc_box_btn">
                                        <a href="/challenges/write.php">챌린지 생성</a>
                                    </div>

                                    <div class="tc_chall_list">
                                        <div class="tc_chall_list_in">
                                            <ul>

											<?for($i=0; $i<count($res['idx']); $i++){

												$idx = $res['idx'][$i];
												$cate = $res['cate'][$i];
												$title = $res['title'][$i];
												$title_emoji = $res['title_emoji'][$i];
												$name = $res['name'][$i];
												$sdate = $res['sdate'][$i];
												$edate = $res['edate'][$i];
												$contents = $res['contents'][$i];
												$action1 = $res['action1'][$i];
												$action2 = $res['action2'][$i];
												$type = $res['type'][$i];
												$emoji = $res['emoji'][$i];

												if (TODATE < $sdate){
													$ready_class = " class=\"tc_ready\"";
													$ready_title = "<strong>[준비중]</strong>";
												}else{
													$ready_class = "";
													$ready_title = "";
												}

												if($type == '1'){
													$chall_idx = $chall_check[TODATE];
												}else{
													$chall_idx = $idx;
												}

												if($emoji == 1){
													$title = urldecode($title_emoji);
												}

												if($chall_cate[$cate]){
													$category = "[".$chall_cate[$cate]."]";
												}

											?>
												<li <?=$ready_class?>>
													<div class="tc_desc">
														<a href="/challenges/view.php?idx=<?=$idx?>"><?=$ready_title?><?=$category?> <?=$title?></a>
														<?if($user_id == $res['email'][$i]){?>
															
															<?//if(!$chall_com['idx']){?>
															<div class="tc_function">
																<button class="tc_function_open"><span>·<br>·<br>·</span></button>
																<div class="tc_function_list">
																	<button id="chall_edit_<?=$idx?>" value="<?=$idx?>"><span>수정하기</span></button>
																	<button id="chall_del" value="<?=$idx?>"><span>삭제하기</span></button>
																</div>
															</div>

															<?//}?>
														<?}?>
													</div>

														<?if($type == '0'){

															if(in_array($idx, $chall_data_one)){?>
																<div class="tc_clear"><span>CLEAR</span></div>
															<?}?>

														<?}else if($type == '1'){

															if(in_array($idx, $chall_data_daily[TODATE])){?>
																<div class="tc_clear"><span>CLEAR</span></div>
															<?}?>
														<?}?>
													</li>
												<?}?>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="tc_box_btn">
                                        <a href="/challenges/write.php">챌린지 생성</a>
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