<?
$newUrl = "https://rewardy.co.kr/challenge/template_write.php";
header("Location: $newUrl");
exit;	
//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";
	include $home_dir . "/challenges/challenges_header.php";
	
	//날짜기한
	$date1 = date("Y-m-d", time() );
	$date2 = date("Y-m-d", strtotime("+6 days", time()));

	//날짜차이 계산
	$datetime1 = new DateTime($date1);
	$datetime2 = new DateTime($date2);
	$interval = $datetime1->diff($datetime2);
	$datediff = $interval->format('%a') + 1;
	
	if($datetime1 == $datetime2){
		$datediff = 1;
	}

	if($datediff > 1){
		//$datediff_text = "최대 ".$datediff."번까지 참여가능. 1일 1회 한정";
		$datediff_text = "1일 1회 참여할 수 있어요.";
	}
	//echo (strtotime($date2) - strtotime($date1)) / 86400;

	//일반등급 : 1:관리, 5:일반
	$highlevel = "5";


	//챌린지카테고리
	$sql = "select idx, name from work_category where state='0' order by rank asc";
	$cate_info = selectAllQuery($sql);
	if($cate_info){
		$cate_title = @array_combine($cate_info['idx'], $cate_info['name']);
	}

	//챌린지정보가 있을경우
	$chall_idx = $_GET['idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);
	if($chall_idx){

		$sql = "select idx, attend_type, cate, email, name, coin, title, template, sdate, edate, attend, day_type, holiday_chk, attend_chk, coin_maxchk, keyword from work_challenges where state='0' and idx='".$chall_idx."' limit 1";
		$ch_info = selectQuery($sql);

		if($ch_info['idx']){
			
			//챌린지형태: 1:메시지형, 
			$attend_type = $ch_info['attend_type'];
			if($attend_type == "1"){
				$attend_type1 = "checked";
			}else if($attend_type == "2"){
				$attend_type2 = "checked";
			}else if($attend_type == "3"){
				$attend_type3 = "checked";
			}

			$template = $ch_info['template'];
			$title = urldecode($ch_info['title']);
			$ch_title = strip_tags($title);
			
			//$contents = urldecode($ch_info['contents']);
			//챌린지내용
			$sql = "select idx, contents from work_contents where state='0' and work_idx='".$ch_info['idx']."'";
			$contents_info = selectQuery($sql);
			if($contents_info['idx']){
				$contents =  $contents_info['contents'];
				$contents = preg_replace('/\r\n|\r|\n/','',$contents);
				$contents = addslashes($contents);
			}


			$sql = "select idx, num, file_path, file_name, file_real_name from work_filesinfo_file where state='0' and work_idx='".$ch_info['idx']."'";
			$file_info = selectAllQuery($sql);

			$sql = "select idx, num, resize, file_path, file_name, file_ori_path, file_ori_name, file_real_name from work_filesinfo_img where state='0' and work_idx='".$ch_info['idx']."'";
			$img_info = selectAllQuery($sql);

			for($i=0; $i<count($img_info['idx']); $i++){

				$resize = $img_info['resize'][$i];
				$file_path = $img_info['file_path'][$i];
				$file_name = $img_info['file_name'][$i];

				$file_ori_path = $img_info['file_ori_path'][$i];
				$file_ori_name = $img_info['file_ori_name'][$i];

				if($resize == 0){
					$img_info_img[] = $file_path . $file_name;
				}else{
					$img_info_img[] = $file_ori_path . $file_ori_name;
				}
			}

			//참여자횟수설정
			$attend = $ch_info['attend'];

			//기간내
			$day_type = $ch_info['day_type'];
			
			//공휴일제외(체크:1)
			$holiday_chk = $ch_info['holiday_chk'];

			//참여자설정
			$attend_chk = $ch_info['attend_chk'];

			//참여코인
			$coin = $ch_info['coin'];

			//키워드
			$keyword = $ch_info['keyword'];

			//날짜
			if($ch_info['sdate'] || $ch_info['edate']){
				//$date1 = $ch_info['sdate'];
				//$date2 = $ch_info['edate'];

				$date1 = date("Y-m-d", time() );
				$date2 = date("Y-m-d", strtotime("+6 days", time()));

				//날짜차이 계산
				$datetime1 = new DateTime($date1);
				$datetime2 = new DateTime($date2);
				$interval = $datetime1->diff($datetime2);
				$datediff = $interval->format('%a') + 1;
				if($datetime1 == $datetime2){
					$datediff = 1;
				}

				if($attend > 1){
					$datediff_text = "최대 ".$attend."번까지 참여가능. 1일 1회 한정";
				}
			}else{
				$date1 = date("Y-m-d", time() );
				$date2 = date("Y-m-d", strtotime("+6 days", time()));


				//날짜차이 계산
				$datetime1 = new DateTime($date1);
				$datetime2 = new DateTime($date2);
				$interval = $datetime1->diff($datetime2);
				$datediff = $interval->format('%a') + 1;
				if($datetime1 == $datetime2){
					$datediff = 1;
				}
				if($datediff > 1){
					$datediff_text = "최대 ".$datediff."번까지 참여가능. 1일 1회 한정";
				}
			}
		}

		//템플릿정보가 아닌경우 데이터초기화처리
		if($template == "0"){
			unset($ch_info);
		}
	}


	//테마정보
	$sql = "select idx, title from work_challenges_thema where state='0' and companyno='".$companyno."' order by sort asc";
	$thema_info = selectAllQuery($sql);
	if($thema_info['idx']){
		$thema_info_cnt = number_format(count($thema_info['idx']));
	}else{
		$thema_info_cnt = 0;
	}

?>

<script>
	<?
	//카테고리설정
	if($cate_info['idx']){?>
		var category_title = new Array();
		<?for($i=0; $i<count($cate_info['idx']); $i++){?>
			category_title["<?=$cate_info['idx'][$i]?>"] = '<?=$cate_info['name'][$i]?>';
		<?}?>
	<?}?>

	<?if($member_total_cnt > 0){?>
		var member_total_cnt = '<?=number_format($member_total_cnt)?>';
	<?}?>

	<?if($datediff > 1){?>
		var datediff = '<?=$datediff?>';
	<?}?>
</script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="/editor/summernote/summernote-lite.css<?php echo VER;?>" rel="stylesheet">
<script src="/editor/summernote/summernote-lite.js<?php echo VER;?>"></script>
<script src="/editor/summernote/lang/summernote-ko-KR.min.js<?php echo VER;?>"></script>
<link href="/css/datepicker.css<?php echo VER;?>" rel="stylesheet" type="text/css">
<script src="/js/datepicker.js<?php echo VER;?>"></script>
<script src="/js/datepicker.kr.js<?php echo VER;?>"></script>
<style>
	
	@import url(//fonts.googleapis.com/earlyaccess/nanumgothic.css);
	.nanumgothic * {
		font-family: 'Nanum Gothic';
	}

	.img-box { border:1px solid; padding:10px; width:200px;height:120px; }

	.remove_img_preview {
		position:relative;
		top:-25px;
		right:5px;
		background:black;
		color:white;
		border-radius:50px;
		font-size:0.9em;
		padding: 0 0.3em 0;
		text-align:center;
		cursor:pointer;
	}

	.thumb {
		width: 100%;
		height: 100%;
		margin: 0.2em -0.7em 0 0;
	}

	.note-editable p{
		margin: 0;
		font-size: 16px;
		font-family: "Noto Sans KR";
	}

	.note-editable span{
		margin: 0;
		font-size: 16px;
		font-family: "Noto Sans KR";
	}

	.note-editable hr {
		border: 1px solid #c1c1c1;
	}
	
</style>

<script>

$(document).ready(function(){

	var fontList = ['맑은 고딕','굴림체','돋움체','바탕체','궁서체','Nanum Gothic','Noto Sans KR','Courier New','Arial Black','Arial','Tahoma'];
	var fontSizes = [ '8', '9', '10', '11', '12', '13', '14','16', '18', '20', '22', '24', '28', '30', '36', '50', '72'];
	var toolbar = 
		[['fontname', 		[ 'fontname' ] ],
		['fontsize',		[ 'fontsize' ] ],
		['style',			[ 'bold', 'italic', 'underline', 'strikethrough' , 'forecolor', 'backcolor', 'paragraph' ,'clear'] ],
		['height',			[ 'height']],
		['insert',			[ 'link', 'picture' ,'video'] ],
		['hr',				[ 'hr' ]]
	];

	var setting = {
		//placeholder: '챌린지 내용을 입력해주세요.',
		width: 760,
		height : 600,
		minHeight : null,
		maxHeight : null,
		focus : true,
		lang : 'ko-KR',
		toolbar : toolbar,
		fontSizes : fontSizes,
		fontNames : fontList
	};

	
	$('#chall_contents').summernote(setting);
	$('#chall_contents').summernote('fontName', 'Noto Sans KR');
	$('#chall_contents').summernote('fontSize', '12');
	
	<?if($contents){?>
		$('#chall_contents').summernote('code', '<?=$contents?>');
	<?}?>

	<?if($ch_info['cate']){?>
		$("#cate_title").val('<?=$ch_info['cate']?>');
	<?}?>
});
</script>

<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">
						<!-- <div class="rew_header">
							<div class="rew_header_in">
								<div class="rew_header_notice">
									<span></span>
								</div>
							</div>
						</div> -->
						<?print_r($cate);?>
						<div class="rew_conts_scroll_05">

							<div class="rew_3_div">
								<div class="rew_3_div_in">
									<!-- 작성하기 -->
									<div class="rew_cha_step_01">
										<div class="rew_cha_write">
											<div class="rew_cha_write_in">
												<div class="rew_cha_write_tabs">
													<div class="rew_cha_write_tabs_in">
														<div class="tabs_off">
															<img src="/html/images/pre/bg_write_tab.png" alt="챌린지 작성하기 단계" />
														</div>
														<div class="tabs_on">
															<img src="/html/images/pre/bg_write_tab_on.png" alt="작성하기, 설정하기, 작성완료" />
														</div>
													</div>
												</div>

												<div class="rew_cha_write_type">
													<div class="title_area">
														<strong class="title_main">챌린지 참여 형태를 선택해주세요.</strong>
														<input type="hidden" id="pageno" value="<?=$gp?>">
														<input type="hidden" id="page_count" value="<?=$page_count?>">
														<input type="hidden" id="chall_idx">
														<input type="hidden" id="chall_user_cnt" value="<?=$member_total_cnt?>">
														<input type="hidden" id="chall_search_chk">
													</div>
													<ul>
														<li>
															<div class="qna">
																<div class="rdo_box">
																	<input type="radio" name="write_type" id="write_type_01" class="rdo_input" <?=$attend_type1?>/>
																	<label for="write_type_01" class="rdo_label">메세지형<span class="qna_q">?</span></label>
																</div>
																<div class="qna_a">
																	<span>메세지형 챌린지<br />챌린지 완료 후 메시지를 남기는 형태</span>
																</div>
															</div>
														</li>
														<li>
															<div class="qna">
																<div class="rdo_box">
																	<input type="radio" name="write_type" id="write_type_02" class="rdo_input" <?=$attend_type2?>/>
																	<label for="write_type_02" class="rdo_label">파일첨부형<span class="qna_q">?</span></label>
																</div>
																<div class="qna_a">
																	<span>파일첨부형 챌린지<br />파일을 첨부하고 챌린지를 완료하는 형태</span>
																</div>
															</div>
														</li>
														<li>
															<div class="qna">
																<div class="rdo_box">
																	<input type="radio" name="write_type" id="write_type_03" class="rdo_input" <?=$attend_type3?>/>
																	<label for="write_type_03" class="rdo_label">혼합형<span class="qna_q">?</span></label>
																</div>
																<div class="qna_a">
																	<span>혼합형 챌린지<br />챌린지 완료 후 메시지, 파일을 첨부하는 형태.</span>
																</div>
															</div>
														</li>
													</ul>
												</div>
												<div class="rew_cha_write_top">
													<div class="title_area">
														<strong class="title_main">챌린지를 소개하세요.</strong>
														<?if($template_auth == true){?>
															<button class="btn_thema" id="btn_thema"><span>테마선택</span></button>
														<?}?>
													</div>
													<div class="rew_cha_write_cate">
														<div class="rew_cha_write_cate_in">
															<button class="btn_sort_on"><span id="cate_title" value="<?=$ch_info['cate']?>"><?=$cate_title[$ch_info['cate']]?></span></button>
															<ul>
																<? for($i=0; $i<count($cate_info['idx']); $i++){ ?>
																	<li><button value="<?=$cate_info['idx'][$i]?>"><span><?=$cate_info['name'][$i]?></span></button></li>
																<?}?>
															</ul>
														</div>
														<div class="rew_cha_write_cate_keyword">
															<input type="text" class="input_cha_keyword" placeholder="키워드" id="write_keyword" value="<?=$keyword?>" maxlength="20"/>
														</div>
													</div>
													<div class="rew_cha_write_title">
														<!-- <input type="text" class="input_cha_title" id="write_title" placeholder="챌린지 제목을 입력해주세요." style="ime-mode:active" value="<?=$title?>"/> -->
														<textarea class="input_cha_title" id="write_title" placeholder="챌린지 제목을 입력해주세요.        (줄바꿈 포함)" style="ime-mode:active"><?=$ch_title?></textarea>
													</div>
												</div>
												<div class="rew_cha_write_editor">
													<div class="rew_cha_write_editor_in" id="chall_contents">
														에디터영역
													</div>
												</div>
												<div class="rew_cha_write_file">
													<div class="title_area">
														<div class="qna">
															<span class="title_sub">(선택) </span>
															<strong class="title_main">참여자에게 필요한 파일을 첨부하세요.</strong>
															<span class="qna_q">?</span>
															<div class="qna_a">
																<span>챌린지 설명에 필요한 문서 등 파일을 첨부하세요.</span>
															</div>
														</div>
													</div>
													<ul>
														<li>
															<div class="file_box">
																<input type="file" id="file_01" class="input_file" />
																<label for="file_01" class="label_file"><span>파일첨부</span></label>
																<div id="file_desc_01">
																	<?if($file_info['file_real_name'][0]){?>
																		<div class="file_desc"><span><?=$file_info['file_real_name'][0]?></span><button id="file_del_01">삭제</button></div>
																	<?}?>
																</div>
															</div>
														</li>
														<li>
															<div class="file_box">
																<input type="file" id="file_02" class="input_file" />
																<label for="file_02" class="label_file"><span>파일첨부</span></label>
																<div id="file_desc_02">
																	<?if($file_info['file_real_name'][1]){?>
																		<div class="file_desc"><span><?=$file_info['file_real_name'][1]?></span><button id="file_del_02">삭제</button></div>
																	<?}?>
																</div>
															</div>
														</li>
														<li>
															<div class="file_box">
																<input type="file" id="file_03" class="input_file" />
																<label for="file_03" class="label_file"><span>파일첨부</span></label>
																<div id="file_desc_03">
																	<?if($file_info['file_real_name'][2]){?>
																		<div class="file_desc"><span><?=$file_info['file_real_name'][2]?></span><button id="file_del_03">삭제</button></div>
																	<?}?>
																</div>
															</div>
														</li>
													</ul>
												</div>
												<div class="rew_cha_write_img">
													<div class="title_area">
														<div class="qna">
															<span class="title_sub">(선택) </span>
															<strong class="title_main">인증샷 예시를 등록하세요.</strong>
															<span class="qna_q">?</span>
															<div class="qna_a">
																<span>챌린지 완료를 위해 등록할 인증샷 예시를 첨부하세요.</span>
															</div>
														</div>
													</div>
													<ul>
														<li>
															<div class="file_box">
																<input type="file" id="file_04" class="input_file" />
																<label for="file_04" class="label_file"><span>이미지첨부</span></label>
																<div id="file_desc_04">
																	<?if($img_info_img[0]){?>
																		<div class="file_desc">
																			<span><img src="<?=$img_info_img[0]?>" alt="" /></span>
																			<button id="file_del_04">삭제</button>
																		</div>
																	<?}?>
																</div>
															</div>
														</li>
														<li>
															<div class="file_box">
																<input type="file" id="file_05" class="input_file" />
																<label for="file_05" class="label_file"><span>이미지첨부</span></label>
																<div id="file_desc_05">
																	<?if($img_info_img[1]){?>
																		<div class="file_desc">
																			<span><img src="<?=$img_info_img[1]?>" alt="" /></span>
																			<button id="file_del_05">삭제</button>
																		</div>
																	<?}?>
																</div>
															</div>
														</li>
														<li>
															<div class="file_box">
																<input type="file" id="file_06" class="input_file" />
																<label for="file_06" class="label_file"><span>이미지첨부</span></label>
																<div id="file_desc_06">
																	<?if($img_info_img[2]){?>
																		<div class="file_desc">
																			<span><img src="<?=$img_info_img[2]?>" alt="" /></span>
																			<button id="file_del_06">삭제</button>
																		</div>
																	<?}?>
																</div>
															</div>
														</li>
													</ul>
												</div>
												<div class="rew_cha_write_btn">
													<button class="btn_gray"><span>임시저장</span></button>
													<button class="btn_black btn_next_step_02"><span>다음</span></button>
												</div>
											</div>
										</div>
									</div>
									<!-- //작성하기 -->
									<div class="rew_cha_step_02">
										<div class="rew_cha_write">
											<div class="rew_cha_write_in">
												<div class="rew_cha_write_tabs">
													<div class="rew_cha_write_tabs_in">
														<div class="tabs_off">
															<img src="/html/images/pre/bg_write_tab.png" alt="챌린지 작성하기 단계" />
														</div>
														<div class="tabs_on">
															<img src="/html/images/pre/bg_write_tab_on.png" alt="작성하기, 설정하기, 작성완료" />
														</div>
													</div>
												</div>

												<div class="rew_cha_setting_date">
													<div class="title_area">
														<div class="qna">
															<strong class="title_main">기간설정</strong>
															<span class="qna_q">?</span>
															<span class="title_desc">최대 한 달까지 설정할 수 있어요.</span>
														</div>
													</div>
													<div class="rew_cha_setting_date_area">
														<div class="date_area_l">
															<input type="text" class="input_cha_date_l" id="sdate" value="<?=$date1?>" autocomplete="off"/>
															<span>~</span>
															<input type="text" class="input_cha_date_r" id="edate" value="<?=$date2?>" autocomplete="off"/>
														</div>
													</div>
												</div>

												<div class="rew_cha_setting_count">
													<div class="title_area">
														<div class="qna">
															<strong class="title_main">참여 횟수 설정</strong>
															<span class="qna_q">?</span>
															<span class="title_desc"><?=$datediff_text?></span>
															<input type="hidden" id="chall_user_chk">
														</div>
													</div>
													<div class="rew_cha_setting_count_area rew_cha_setting_count_area_02">

														<div class="count_area_r">
															<button class="btn_count_toggle btn_off" id="ch_once"><span>한번</span></button>
															<button class="btn_count_toggle btn_on" id="ch_daily"><span>매일</span></button>
														</div>
														<div class="count_area_l">
															<input type="text" class="input_count" />
														</div>
													</div>
												</div>

												<div class="rew_cha_setting_user">
													<div class="title_area">
														<div class="qna">
															<strong class="title_main">참여자 설정</strong>
															<span class="qna_q">?</span>
															<div class="qna_a">
																<span>챌린지에 참여할 구성원을 선택해 주세요.</span>
															</div>
															<span class="title_desc" id="select_user_cnt">전체 <?=$member_total_cnt?>명 선택</span>
														</div>
													</div>
													<div class="rew_cha_setting_user_area">
														<button class="btn_user_toggle<?=$attend_chk=='0'?" btn_on":" btn_off"?>"><span>전체</span></button>
														<button class="btn_user_toggle<?=$attend_chk=='1'?" btn_on":" btn_off"?>" id="open_layer_user"><span>일부</span></button>
													</div>
												</div>

												<div class="rew_cha_setting_coin">
													<div class="title_area">
														<div class="qna">
															<strong class="title_main">보상코인 설정</strong>
															<span class="qna_q">?</span>
															<span class="title_desc">100코인 단위로 사용할 수 있어요.</span>
														</div>
													</div>
													<div class="rew_cha_setting_coin_area">
														<div class="coin_area_l">
															<button class="btn_coin_minus coin_limit"><span>빼기</span></button>
															<input type="text" class="input_coin" value="100" />
															<button class="btn_coin_plus"><span>더하기</span></button>
														</div>
														<div class="coin_area_r">
															<button class="btn_coin_chk btn_chk_off" id="max_coin_ico"><span>최대 사용</span></button>
															<span class="btn_chk_txt">(1인당 최대 사용 코인 : <strong id="maxcoin1">1,100</strong>코인)</span>
														</div>
													</div>
													<div class="rew_cha_setting_coin_calc">
														<div class="calc_01">
															<span>사용 가능 코인</span>
															<strong id="common_coin"><?=$common_coin?></strong>
														</div>
														<div class="calc_02">
															<span>지급 예상 코인</span>
															<strong>99,000</strong>
														</div>
														<div class="calc_03">
															<span>남은 보유 코인</span>
															<strong>1,000</strong>
														</div>
													</div>
												</div>

												<div class="rew_cha_write_btn">
													<button class="btn_white btn_prev_step_01"><span>이전</span></button>
													<button class="btn_gray"><span>임시저장</span></button>
													<button class="btn_black btn_next_step_03" id="wr_com"><span>작성완료</span></button>
												</div>
											</div>
										</div>
									</div>
									<div class="rew_cha_step_03">
										<div class="rew_cha_write">
											<div class="rew_cha_write_in">
												<div class="rew_cha_write_tabs">
													<div class="rew_cha_write_tabs_in">
														<div class="tabs_off">
															<img src="/html/images/pre/bg_write_tab.png" alt="챌린지 작성하기 단계" />
														</div>
														<div class="tabs_on">
															<img src="/html/images/pre/bg_write_tab_on.png" alt="작성하기, 설정하기, 작성완료" />
														</div>
													</div>
												</div>

												<div class="rew_cha_comple_area">
													<div class="rew_cha_comple_area_in">
														<div class="rew_cha_comple_box">
															<strong>챌린지 작성이 완료되었습니다.</strong>
															<a href="/challenges/index.php"><span>챌린지 목록보기</span></a>
															<a href="#"><span>작성한 글보기</span></a>
														</div>
														
													</div>
												</div>
											</div>
										</div>
										<div class="rew_cha_popular">
											<div class="title_area">
												<strong class="title_main">지금 제일 인기 많은 챌린지</strong>
												<a href="/challenges/view.php" class="title_more"><span>더보기</span></a>
											</div>
											<ul class="rew_cha_list_ul">
												<li class="category_01">
													<a href="/challenges/view.php">
														<div class="cha_box">
															<div class="cha_box_t">
																<span class="cha_cate">업무</span>
																<span class="cha_title">윈도우 업데이트 점검한다면</span>
																<span class="cha_coin"><strong>500</strong>코인</span>
															</div>
															<div class="cha_box_b">
																<span class="cha_member">12/20 명 도전중</span>
																<span class="cha_dday">D - 20</span>
															</div>
														</div>
													</a>
												</li>
												<li class="category_02">
													<a href="/challenges/view.php">
														<div class="cha_box">
															<div class="cha_box_t">
																<span class="cha_cate">생활</span>
																<span class="cha_title">책 읽고 독서메모를 남긴다면</span>
																<span class="cha_coin"><strong>1,500</strong>코인</span>
															</div>
															<div class="cha_box_b">
																<span class="cha_member">7/20 명 도전중</span>
																<span class="cha_dday">D - 10</span>
															</div>
														</div>
													</a>
												</li>
												<li class="category_05">
													<a href="/challenges/view.php">
														<div class="cha_box">
															<div class="cha_box_t">
																<span class="cha_cate">신입사원</span>
																<span class="cha_title">보고서 작성법을 배우면</span>
																<span class="cha_coin"><strong>1,000</strong>코인</span>
															</div>
															<div class="cha_box_b">
																<span class="cha_member">1/1 명 도전중</span>
																<span class="cha_dday">D - 30</span>
															</div>
														</div>
													</a>
												</li>
												<li class="category_02">
													<a href="/challenges/view.php">
														<div class="cha_box">
															<div class="cha_box_t">
																<span class="cha_cate">생활</span>
																<span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span>
																<span class="cha_coin"><strong>500</strong>코인</span>
															</div>
															<div class="cha_box_b">
																<span class="cha_member">12/20 명 도전중</span>
																<span class="cha_dday">D - 60</span>
															</div>
														</div>
													</a>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>
	
	<?php
		//사용자 레이어(오늘업무(보고,공유,요청),라이브, 챌린지-참여자설정,파티구성원)
		include $home_dir . "/layer/member_user_layer.php";
	?>


	<div class="layer_thema" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_user_in">
			<div class="layer_user_box">
				<div class="layer_user_search">
					<div class="layer_user_search_desc">
						<strong>테마 선택</strong>
						<?if( $thema_info['idx'] ){?>
						<span id="thema_list_cnt">전체 <?=$thema_info_cnt?>개</span>
						<?}?>
					</div>
					<div class="layer_user_search_box">
						<input type="text" class="input_search" placeholder="테마명을 검색" id="input_thema_search"/>
						<button id="input_thema_search_btn"><span>검색</span></button>
					</div>
					<div class="layer_user_add_box">
						<button id="thema_add"><span>추가</span></button>
					</div>
				</div>
				<div class="layer_thema_list tdw_list">
					<ul id="thema_list_add">

						<?
						if($thema_info['idx']){?>
							<?for($i=0; $i<count($thema_info['idx']); $i++){
								$thema_idx = $thema_info['idx'][$i];
								$thema_title = $thema_info['title'][$i];
							?>
								<li>
									<div class="tdw_list_box">
										<div class="tdw_list_chk">
											<button class="btn_tdw_list_chk" id="btn_tdw_list_thema_chk" value="<?=$thema_idx?>"><span>완료체크</span></button>
										</div>
										<div class="tdw_list_desc" id="tdw_list_desc_thema">
											<p id="tdw_list_desc_thema_<?=$thema_idx?>"><?=$thema_title?></p>
											<button class="btn_list_del" id="btn_list_thema_del" value="<?=$thema_idx?>"><span>삭제</span></button>
											<div class="tdw_list_regi" id="tdw_list_regi_thema_<?=$thema_idx?>">
												<textarea name="" class="textarea_regi" id="textarea_regi_thema_<?=$thema_idx?>"><?=$thema_title?></textarea>
												<div class="btn_regi_box">
													<button class="btn_regi_submit" id="btn_regi_thema_submit" value="<?=$thema_idx?>"><span>확인</span></button>
													<button class="btn_regi_cancel" id="btn_regi_thema_cancel" value="<?=$thema_idx?>"><span>취소</span></button>
												</div>
											</div>
										</div>
									</div>
								</li>
							<?}?>
						<?}else{?>
							<li>
								<div class="layer_user_no">
									<strong>등록된 테마가 없습니다.</strong>
								</div>
							</li>
						<?}?>
					</ul>
				</div>
			</div>
			<div class="layer_user_btn">
				<button class="layer_user_cancel"><span>취소</span></button>
				<button class="layer_user_submit" id="thema_select_btn"><span>선택하기</span></button>
			</div>
		</div>
	</div>

	<div class="rew_q">
		<a href="01.html" target="_blank">(구)버전</a>
		<a href="002.html" target="_blank">(신)버전</a>
		<a href="0001.html" target="_blank">(리뉴얼)버전</a>
	</div>
	
</div>
<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/Rewardy"; /* 페이지 이름 지정 Contents Path */
</script>

</body>


</html>
