<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";
	include $home_dir . "/challenge/challenges_header.php";

	$chall_idx = $_GET['idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);
	if($chall_idx){

		if(@in_array($user_id , $edit_user_arr)){
			$sql = "select idx, attend_type, cate, email, name, coin, coin_not, title, sdate, edate, attend, day_type, holiday_chk, attend_chk, coin_maxchk, template, keyword from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chall_idx."' limit 1";
		}else{
			$sql = "select idx, attend_type, cate, email, name, coin, coin_not, title, sdate, edate, attend, day_type, holiday_chk, attend_chk, coin_maxchk, template, keyword from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chall_idx."' and email='".$user_id."' limit 1";
		}
		$ch_info = selectQuery($sql);
		if($ch_info['idx']){
			
			
			$attend_type = $ch_info['attend_type'];

			//챌린지형태: 1:메시지형
			if($attend_type == "1"){
				$attend_type1 = "checked";
			}else if($attend_type == "2"){
				$attend_type2 = "checked";
			}else if($attend_type == "3"){
				$attend_type3 = "checked";
			}
			
			$cate = $ch_info['cate'];
			$ch_title = $ch_info['title'];
			$ch_title = urldecode($ch_title);

			//if(strpos($ch_title, "<br />") !== false) {
				$ch_title = strip_tags($ch_title);
			//}


			//관리권한일경우
			//템플릿으로 변경처리
			$template = $ch_info['template'];
			if($template == 0 ){
				$template = $template_auth=="1"?"1":"0";
			}
			
			//$contents = urldecode($ch_info['contents']);
			//챌린지내용
			$sql = "select idx, contents from work_contents where state='0' and companyno='".$companyno."' and work_idx='".$ch_info['idx']."'";
			$contents_info = selectQuery($sql);
			if($contents_info['idx']){
				$contents =  $contents_info['contents'];
				$contents = addslashes($contents);
				$contents = preg_replace("/\r\n|\r|\n/",'',$contents);

				// $contents = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i",'',$contents);
				

			}


			//등록한 파일첨부
			$sql = "select idx, num, file_path, file_name, file_real_name from work_filesinfo_file where state='0' and companyno='".$companyno."' and work_idx='".$ch_info['idx']."'";
			$file_info = selectAllQuery($sql);

			//등록한 예시 이미지
			$sql = "select idx, num, file_path, file_name, file_real_name, file_ori_path, file_ori_name from work_filesinfo_img where state='0' and companyno='".$companyno."' and work_idx='".$ch_info['idx']."'";
			$img_info = selectAllQuery($sql);

			//참여자횟수설정
			$attend = $ch_info['attend'];

			//기간내
			$day_type = $ch_info['day_type'];
			
			//공휴일제외(체크:1)
			$holiday_chk = $ch_info['holiday_chk'];

			//참여자설정
			$attend_chk = $ch_info['attend_chk'];

			//키워드
			$keyword = $ch_info['keyword'];

			//참여코인
			//$coin = $ch_info['coin'];

			//날짜
			if($ch_info['sdate'] || $ch_info['edate']){
				$date1 = $ch_info['sdate'];
				$date2 = $ch_info['edate'];

				//날짜차이 계산
				$datetime1 = new DateTime($date1);
				$datetime2 = new DateTime($date2);
				$interval = $datetime1->diff($datetime2);
				$datediff = $interval->format('%a');

				if($attend > 1){
					$datediff_text = "최대 ".$attend."번까지 참여가능. 1일 1회 한정";
					$datediff_text = "1일 1회 참여할 수 있어요.";
				}



			}else{
				$date1 = date("Y-m-d", time() );
				$date2 = date("Y-m-d", strtotime("+1 month", time()));


				//날짜차이 계산
				$datetime1 = new DateTime($date1);
				$datetime2 = new DateTime($date2);
				$interval = $datetime1->diff($datetime2);
				$datediff = $interval->format('%a');

				if($datediff > 1){
					//$datediff_text = "최대 ".$datediff."번까지 참여가능. 1일 1회 한정";
					$datediff_text = "1일 1회 참여할 수 있어요.";
				}
			}

		}else{
			alertMove("수정 권한이 없습니다.","/challenge/index.php");
			exit;
		}
	}


	//챌린지카테고리
	$sql = "select idx, name from work_category where state='0' order by rank asc";
	$cate_info = selectAllQuery($sql);
	if($cate_info){
		$cate_title = @array_combine($cate_info['idx'], $cate_info['name']);
	}

	//echo (strtotime($date2) - strtotime($date1)) / 86400;


	//챌린지 인기 리스트
	$sql = "select * from (";
	$sql = $sql .=" select a.idx, a.state, a.cate, a.title, a.coin, a.sdate, a.edate, DATEDIFF(a.edate, a.sdate) as chllday, a.pageview,";
	$sql = $sql .="	(SELECT count(idx) FROM work_challenges_user WHERE state=0 and challenges_idx = a.idx) AS chamyeo,";
	$sql = $sql .="	(SELECT count(idx) FROM work_challenges_com WHERE challenges_idx = a.idx) AS challenge";
	$sql = $sql .="	from work_challenges as a left join work_challenges_com as b on(a.idx=b.challenges_idx)";
	$sql = $sql .="	where a.state='0' and a.companyno='".$companyno."' and a.edate > DATE_FORMAT(".DBDATE.", '%Y-%m-%d')";
	$sql = $sql .="	group by a.idx, a.state, cate, title, coin , b.challenges_idx , sdate, edate, DATEDIFF(a.edate, a.sdate), a.pageview";
	$sql = $sql .="	) as a order by a.pageview desc limit 3";
	$chall_top3_info = selectAllQuery($sql);


/*
$headers = apache_request_headers();
foreach ($headers as $header => $value) {
  echo "$header: $value <br />";
}
*/

	$sql = "select count(idx) as cnt FROM work_challenges_user WHERE state='0' and companyno='".$companyno."' and challenges_idx = '".$ch_info['idx']."'";
	$chall_user_info = selectAllQuery($sql);
	if($chall_user_info['cnt']){

		if($cate_info['attend_chk'] =='0'){
			$chall_user_text = "전체 ".$chall_user_info['cnt']."명 선택";
		}else if($cate_info['attend_chk'] =='0'){
			$chall_user_text = "전체 ".$member_total_cnt."명 , ".$chall_user_info['cnt']." 선택";
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


	//테마리스트
	$sql = "select idx, challenges_idx, thema_idx from work_challenges_thema_list where state='0' and companyno='".$companyno."' and challenges_idx='".$chall_idx."' order by thema_idx asc";
	$thema_list = selectAllQuery($sql);
	if($thema_list['thema_idx']){
		$chall_thema_chk = @implode(",",$thema_list['thema_idx']);
	}
?>


<script>

	<?if($cate_info['idx']){?>
		var category_title = new Array();
		<?for($i=0; $i<count($cate_info['idx']); $i++){?>
			category_title["<?=$cate_info['idx'][$i]?>"] = '<?=$cate_info['name'][$i]?>';
		<?}?>
	<?}?>

	<?if($member_total_cnt > 0){?>
		var member_total_cnt = '<?=number_format($member_total_cnt)?>';
	<?}?>

	<?if($datediff>1){?>
		var datediff = '<?=$datediff?>';
	<?}?>
</script>


<!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"> -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<link href="/editor/summernote/summernote-lite.css<?php echo VER;?>" rel="stylesheet">
<script src="/editor/summernote/summernote-lite.js<?php echo VER;?>"></script>
<script src="/editor/summernote/lang/summernote-ko-KR.min.js<?php echo VER;?>"></script>
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

	.note-editable p {
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
		['insert',			[ 'link', 'picture' ,'video'] ],
		['hr',				[ 'hr' ]]
	];

	var maximumImageFileSize = 5242880;

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
		fontNames : fontList,
		maximumImageFileSize : maximumImageFileSize
		
		//callbacks : { //여기 부분이 이미지를 첨부하는 부분
		//	onImageUpload : function(files, editor,	welEditable) {
		//		for (var i = files.length - 1; i >= 0; i--) 
		//		{
		//			//uploadSummernoteImageFile(files[i],	this);
		//		}
		//	},
		//	onMediaDelete : function(target) {
		//		//console.log(target[0]);
		//		//deleteFile(target[0].src);
		//	}
		//}
	};

	//var text_null = '챌린지 내용을 입력하세요.';
	//var text_null ='<p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px;"><font color="#9c9c94">챌린지 내용을 작성해주세요.</font></span><font color="#9c9c94"><br><br><br></font></p><p><span style="font-size: 14px;"></span><br></p><font color="#9c9c94"><hr></font><span style="font-size: 14px;"></span><span style="font-size: 20px; color: rgb(156, 156, 148); font-family: &quot;Noto Sans KR&quot;;">✔️ </span><b style="font-size: 20px; color: rgb(156, 156, 148);"><span style="font-family: &quot;Noto Sans KR&quot;;">챌린지 참여방법</span></b><font color="#9c9c94"><p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px;">1. </span><font color="#9c9c94" style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px;">챌린지 참여방법을 작성해주세요.</font><br></p><p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; line-height: 25px;">2. </span></p><p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; line-height: 25px;">3. </span><br></p><p><br></p><hr><p><span style="font-size: 14px;"></span><span style="font-size: 20px; font-family: &quot;Noto Sans KR&quot;;">📌 </span><b style="font-size: 20px;"><span style="font-family: &quot;Noto Sans KR&quot;;">챌린지 유의사항</span></b></p><p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px;">1. </span><font color="#9c9c94" style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px;">챌린지 유의사항을 입력해주세요.</font><br></p><p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; line-height: 25px;">2. </span></p><p><span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; line-height: 25px;">3. </span><br></p></font><p></p>';
	$('#chall_contents').summernote(setting);
	//$('#chall_contents').summernote({maximumImageFileSize: 5242880});

	$('#chall_contents').summernote('fontName', 'Noto Sans KR');
	$('#chall_contents').summernote('fontSize', '16');

	<?if($contents){?>
		$('#chall_contents').summernote('code', '<?=$contents?>');
	<?}?>


	<?if($file_info['idx'][0]){?>
		//$("#file_01").val('<?=$file_info['idx'][0]?>');
		//$("#file_desc_01").text('<div class="file_desc"><span><?=$file_info['file_real_name'][0]?></span><button id="file_del_0">삭제</button></div>');
		//$(".file_desc").show();
	<?}?>

	<?if($cate){?>
		$("#cate_title").val('<?=$cate?>');
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
														<input type="hidden" id="chall_idx" value="<?=$chall_idx?>">
														<input type="hidden" id="chall_user_cnt" value="<?=$member_total_cnt?>">
														<input type="hidden" id="thema_info_cnt" value="<?=$thema_info_cnt?>">
														<input type="hidden" id="chall_search_chk">
														<input type="hidden" id="chall_thema_chk" value="<?=$chall_thema_chk?>">
														<input type="hidden" id="chall_template" value="<?=$template?>">
														<?if($template_auth){?>
														<input type="hidden" id="chall_auth" value="<?=$template_auth?>">
														<?}?>
													</div>
													<ul>
														<li>
															<div class="qna">
																<div class="rdo_box">
																	<input type="radio" name="write_type" id="write_type_01" class="rdo_input" <?=$attend_type1?>/>
																	<label for="write_type_01" class="rdo_label">메시지형<span class="qna_q">?</span></label>
																</div>
																<div class="qna_a">
																	<span>메시지형 챌린지<br />챌린지 완료 후 메시지를 남기는 형태</span>
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
														<?if($template_auth == true || $template_use_auth == true){?>
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
																	<?if($img_info['idx'][0]){?>
																		<div class="file_desc">
																			<span><img src="<?=$img_info['file_ori_path'][0]?>/<?=$img_info['file_ori_name'][0]?>" alt="" /></span>
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
																	<?if($img_info['idx'][1]){?>
																		<div class="file_desc">
																			<span><img src="<?=$img_info['file_ori_path'][1]?>/<?=$img_info['file_ori_name'][1]?>" alt="" /></span>
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
																<?if($img_info['idx'][2]){?>
																		<div class="file_desc">
																			<span><img src="<?=$img_info['file_ori_path'][2]?>/<?=$img_info['file_ori_name'][2]?>" alt="" /></span>
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
															<input type="text" class="input_cha_date_l" id="sdate" value="<?=$date1?>" />
															<span>~</span>
															<input type="text" class="input_cha_date_r" id="edate" value="<?=$date2?>" />
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
															<button class="btn_count_toggle <?=$day_type=='0'?"btn_on":"btn_off"?>" id="ch_once"><span>한번</span></button>
															<button class="btn_count_toggle <?=$day_type=='1'?"btn_on":"btn_off"?>" id="ch_daily"><span>매일</span></button>
														</div>
														<div class="count_area_l">
															<input type="text" class="input_count" value="<?=$attend?>"/>
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
															<span class="title_desc" id="select_user_cnt"><?=$chall_user_text?></span>
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
															<input type="text" class="input_coin" value="<?=$ch_info['coin']?>" />
															<button class="btn_coin_plus"><span>더하기</span></button>
														</div>
														<div class="coin_area_r">
															<button class="btn_coin_chk btn_chk_off" id="max_coin_ico"><span>최대 사용</span></button>
															<span class="btn_chk_txt">(1인당 최대 사용 코인 : <strong id="maxcoin1">1,100</strong>코인)</span>
														</div>

														<?if($user_id=='sadary0@nate.com' || $user_id=='eyson@bizforms.co.kr'){?>
															<div class="coin_area_r">
															<button class="btn_coin_chk<?=($ch_info['coin_not']=='1'?" btn_chk_on":" btn_chk_off")?>" id="not_coin_ico"><span>코인 사용 안함</span></button>
															<span class="btn_chk_txt"></span>
														</div>
														<?}?>

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
													<button class="btn_black btn_next_step_03" id="ed_com"><span>수정완료</span></button>
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
															<strong>챌린지 수정이 완료 되었습니다.</strong>
															<a href="/challenge/index.php"><span>챌린지 목록보기</span></a>
															<a href="#"><span>작성한 글보기</span></a>
														</div>
														
													</div>
												</div>
											</div>
										</div>
										<div class="rew_cha_popular">
											<div class="title_area">
												<strong class="title_main">지금 제일 인기 많은 챌린지</strong>
												<a href="/challenge/index.php" class="title_more"><span>더보기</span></a>
											</div>
											<ul class="rew_cha_list_ul">

												<?for($i=0; $i<count($chall_top3_info['idx']); $i++){
													
													$chall_top3_title = $chall_top3_info['title'][$i];
													$chall_top3_title = urldecode($chall_top3_title);
													$chall_top3_coin = number_format($chall_top3_info['coin'][$i]);

													$chall_top3_chamyeo = number_format($chall_top3_info['chamyeo'][$i]);
													$chall_top3_challenge = number_format($chall_top3_info['challenge'][$i]);
													$chall_top3_chllday = $chall_top3_info['chllday'][$i];

													?>
													<li class="category_0<?=$chall_top3_info['cate'][$i]?>">
														<a href="/challenge/view.php?idx=<?=$chall_top3_info['idx'][$i]?>">
															<div class="cha_box">
																<div class="cha_box_t">
																	<span class="cha_cate"><?=$cate_title[$chall_top3_info['cate'][$i]]?></span>
																	<span class="cha_title"><?=$chall_top3_title?></span>
																	<span class="cha_coin"><strong><?=$chall_top3_coin?></strong>코인</span>
																</div>
																<div class="cha_box_b">
																	<span class="cha_member"><?=$chall_top3_chamyeo?>/<?=$chall_top3_challenge?> 명 도전중</span>
																	<span class="cha_dday">D - <?=$chall_top3_chllday?></span>
																</div>
															</div>
														</a>
													</li>
												<?}?>
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

								if(@in_array($thema_info['idx'][$i] , $thema_list['thema_idx'])){
									$btn_tdw_list_chk_class =" on";
								}else{
									$btn_tdw_list_chk_class ="";
								}
							?>
								<li>
									<div class="tdw_list_box">
										<div class="tdw_list_chk">
											<button class="btn_tdw_list_chk<?=$btn_tdw_list_chk_class?>" id="btn_tdw_list_thema_chk" value="<?=$thema_idx?>"><span>완료체크</span></button>
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
				<button class="layer_user_submit<?=$chall_thema_chk?" on":""?>" id="thema_select_btn"><span>선택하기</span></button>
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
