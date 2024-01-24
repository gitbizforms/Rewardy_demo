<?php

	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir  . "inc_lude/header_01.php";
?>

	<!--<link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">-->
    <!--<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>-->
	<!--<link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote.css" rel="stylesheet">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote.js"></script>
	<script src="http://192.168.0.248/js/summernote/lang/summernote-ko-KR.js"></script>
	<script src="http://192.168.0.248/js/summernote/lang/summernote-ko-KR.js"></script>-->



	<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">

	<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

	<!-- CDN 한글화 -->
	<script src=" https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/lang/summernote-ko-KR.min.js"></script>

<script>


$(document).ready(function(){

	var fontList = ['맑은 고딕','나눔 고딕','굴림','돋움','바탕','궁서','Noto Sans KR' /*, 'Arial', 'Arial Black', 'Noto Sans KR', 'Tahoma', 'Courier New'*/];

	
	$('#contents').summernote({
		placeholder: '할일을 입력해주세요.',
		lang: "ko-KR", // 한글 설정
		width: 690,
		height: 400,
		maxHeight: 400,
		fontNames: fontList,
		toolbar: [
            //[groupName, [list of button]]
            //['fontname', ['fontname']],
			['font', ['fontname','fontsize','fontsizeunit']],
            ['style', ['bold', 'italic' /*, 'underline', 'strikethrough' , 'clear'*/ ]],
            ['color', ['forecolor', 'color']],
            ['table', ['table']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['link', 'picture'/*,'video'*/ ]],
            /*['view', ['fullscreen', 'help']],*/
            ['his', ['undo', 'redo']]
        ]
	});
});

</script>

<div class="todaywork_wrap">
	<div class="t_in">
		<!-- header -->
		<?php

			//top페이지
			include $home_dir . "inc_lude/top.php";

			$part_type = $_POST['type']?$_POST['type']:$_GET['type'];
			$part_flag = $user_part;
			$wdate = $_GET['wdate'];

			//나의업무/전체/팀별업무별 조건
			if($part_type == "all_works"){
				$works_title = "전체업무";
				$where = "";
			}
			else if($part_type == "team_works"){
				$works_title = "팀별업무";
				$where = "and a.part_flag='".$part_flag."'";
			}
			else{
				$works_title = "나의업무";
				$where = "and a.email='".$user_id."'";
			}

			if($wdate){
				$newdate = $wdate;
				$towdate = str_replace("-",".",$wdate);
			}else{
				$newdate = date("Y-m-d", time());
				$towdate = date("Y.m.d", time());
			}


			//이전날
			$yesterday = date("Y-m-d", strtotime($newdate." -1 day"));

			//다음날
			$tomorrowday = date("Y-m-d", strtotime($newdate." +1 day"));


			if ($_SERVER['QUERY_STRING']){
				$qry = $_SERVER['QUERY_STRING'];
				parse_str($qry);
			}

			$dtab = $_GET['dtab']?$_GET['dtab'] : $_POST['dtab'];


			$tab_day="";
			$tab_week="";
			$tab_month="";
			if($dtab == "" || $dtab=="day"){
				$tab_day = " on";
			}else if($dtab=="week"){
				$tab_week = " on";
			}else if($dtab == "month"){
				$tab_month = " on";
			}

			//요청회원
			$highlevel = 5;
			$sql = "select idx, name, part from work_member where state='0' and highlevel='".$highlevel."' and companyno='".$companyno."' and email != '".$user_id."' order by idx asc";
			$mem_req = selectAllQuery($sql);

?>

		<div class="t_contents">
			<div class="tc_in">
				<div class="tc_page">
					<div class="tc_page_in">
						<div class="tc_box_07">
							<div class="tc_box_07_in">

								<div class="tc_index">
									<div class="tc_index_my">

										<div class="tc_index_tit">
											<strong>오늘업무</strong>
											<div class="tc_index_tit_date">
												<button class="btn_yesterday" onclick="wdate_link('<?=$yesterday?>');"><span>&lt;</span></button>
												<input name="works_today" id="works_today" value="<?=$towdate?>">

												<button class="btn_tomorrow" onclick="wdate_link('<?=$tomorrowday?>');"><span>&gt;</span></button>
											</div>
										</div>

										<div class="tc_index_tab">
											<div class="tc_index_tab_in">
												<ul>
													<li><button class="tab_work on"><span>오늘할일</span></button></li>
													<li><button class="tab_date"><span>업무예약</span></button></li>
													<li><button class="tab_request"><span>업무요청</span></button></li>
													<li><button class="tab_goal"><span>목표</span></button></li>
												</ul>
											</div>
										</div>


										<?php
											//오늘할일
										?>
										<div class="tc_index_box" id="tab_work">
											<div class="tc_box_add">
												<div class="tc_input">
													<textarea name="contents" id="contents" class="input_01"></textarea>
													<!--<label for="add01" class="label_01">
														<strong class="label_tit">할일을 입력해주세요</strong>
													</label>-->
												</div>
											</div>
											<div class="tc_add_btn">
												<button id="write_btn"><span>등록하기</span></button>
											</div>
										</div>


										<?php
											//업무예약
										?>
										<div class="tc_index_box" id="tab_date">
											<div class="tc_box_add">
												<div class="tc_input">
													<textarea name="wdate_contents" id="add02" class="input_01"></textarea>
													<label for="add02" class="label_01">
														<strong class="label_tit">일정 내용을 입력해주세요</strong>
													</label>
												</div>
											</div>
											<div class="tc_add_timer">
												<div class="tc_add_timer_in">
													<div class="tc_add_timer_box">
														<div class="tc_add_timer_date">
															<i class="far fa-calendar-alt"></i>
															<input type="text" id="date_02" name="" class="input_01" value="<?=$newdate?>" />
														</div>
													</div>
												</div>
											</div>
											<div class="tc_add_btn">
												<button id="date_write"><span>등록하기</span></button>
											</div>
										</div>


										<?php
											//요청
										?>

										<div class="tc_index_box" id="tab_request">
											<div class="tc_box_add">
												<div class="tc_input">
													<textarea name="req_contents" id="add03" class="input_01"></textarea>
													<label for="add03" class="label_01">
														<strong class="label_tit">업무요청 내용을 입력해주세요</strong>
													</label>
												</div>
											</div>
											<div class="tc_add_user_list">
												<ul>
												<li>
													<input type="checkbox" name="chkall" id="chkall" value="">
													<label for="chkall">전체선택</label>
												</li>

												<?php
													for($i=0; $i<count($mem_req['idx']); $i++){?>
													<li>
														<input type="checkbox" name="chk" id="chk<?=$i?>" value="<?=$mem_req['idx'][$i]?>">
														<label for="chk<?=$i?>"><?=$mem_req['name'][$i]?><span>(<?=$mem_req['part'][$i]?>)</span></label>
													</li>
												<?}?>

												</ul>
											</div>
											<input type="hidden" id="date_03" name="" class="input_01" value="<?=$newdate?>" />
											<?/*
											<div class="tc_add_timer">
												<div class="tc_add_timer_in">
													<div class="tc_add_timer_box">
														<div class="tc_add_timer_date">
															<i class="far fa-calendar-alt"></i>
															<input type="text" id="date_03" name="" class="input_01" value="<?=TODATE?>" />
														</div>
													</div>
												</div>
											</div>
											*/?>
											<div class="tc_add_btn">
												<button id="req_write"><span>요청하기</span></button>
											</div>
										</div>


										<?php
											//목표
										?>
										<div class="tc_index_box" id="tab_goal">
											<div class="tc_box_add">
												<div class="tc_box_area">
													<div class="tc_input">
														<textarea name="goal1" id="goal1" class="input_01"></textarea>
														<label for="goal1" class="label_01">
															<strong class="label_tit">목표를 입력해주세요</strong>
														</label>
													</div>
												</div>
												<div class="tc_box_area">
													<div class="tc_input">
														<textarea name="goal2" id="goal2" class="input_01"></textarea>
														<label for="goal2" class="label_01">
															<strong class="label_tit">핵심결과를 입력해주세요</strong>
														</label>
													</div>
												</div>

												<div class="tc_add_timer">
													<div class="tc_add_timer_in">
														<div class="tc_add_timer_box">
															<div class="tc_add_timer_date">
																<i class="far fa-calendar-alt"></i>
																<input type="text" id="date_04" name="date_04" class="input_01" value="<?=$newdate?>" />
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="tc_add_btn">
												<button id="goal_write"><span>등록하기</span></button>
											</div>
										</div>


										<div class="tc_tab_function">
											<div class="tc_tab">
												<ul>
													<li><button class="tab_day<?=$tab_day?>"><span>일일</span></button></li>
													<li><button class="tab_week<?=$tab_week?>"><span>주간</span></button></li>
													<?/*<li><button class="tab_month <?=$tab_month?>" value="month"><span>한달</span></button></li>*/?>
													<input type="hidden" name="tabval" value="<?=$dtab?>">
												</ul>
											</div>


										</div>

										<div class="tc_index_list">

										</div>

										<div class="tc_index_list_week">
											
										</div>

										<div class="tc_index_list_month">
											<div class="tc_index_middle_month">
												<ul>
													<li class="tc_month_none">
														<div class="tc_chk_month">
															<span></span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li class="tc_month_none">
														<div class="tc_chk_month">
															<span></span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li class="tc_month_none">
														<div class="tc_chk_month">
															<span></span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li class="tc_month_none">
														<div class="tc_chk_month">
															<span></span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li class="tc_month_none">
														<div class="tc_chk_month">
															<span></span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>1</span>
														</div>
														<div class="tc_desc_month">
															<ul>
																<li><span>오늘일 실시간 작업</span></li>
																<li><span>오늘일 실시간 작업</span></li>
																<li><span>오늘일 실시간 작업</span></li>
																<li><span>오늘일 실시간 작업</span></li>
															</ul>
														</div>
													</li>
													<li class="tc_month_sat">
														<div class="tc_chk_month">
															<span>2</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>

													<li class="tc_month_sun">
														<div class="tc_chk_month">
															<span>3</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>4</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>5</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>6</span>
														</div>
														<div class="tc_desc_month">
															<ul>
																<li><span>오늘일 실시간 작업</span></li>
																<li><span>오늘일 실시간 작업</span></li>
																<li><span>오늘일 실시간 작업</span></li>
															</ul>
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>7</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>8</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li class="tc_month_sat">
														<div class="tc_chk_month">
															<span>9</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>

													<li class="tc_month_sun">
														<div class="tc_chk_month">
															<span>10</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>11</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>12</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>13</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>14</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>15</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li class="tc_month_sat">
														<div class="tc_chk_month">
															<span>16</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>

													<li class="tc_month_sun">
														<div class="tc_chk_month">
															<span>17</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>18</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>19</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>20</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>21</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>22</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li class="tc_month_sat">
														<div class="tc_chk_month">
															<span>23</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>

													<li class="tc_month_sun">
														<div class="tc_chk_month">
															<span>24</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>25</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>26</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>27</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>28</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li>
														<div class="tc_chk_month">
															<span>29</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>
													<li class="tc_month_sat">
														<div class="tc_chk_month">
															<span>30</span>
														</div>
														<div class="tc_desc_month">
														</div>
													</li>

												</ul>
											</div>
										</div>

									</div>

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

