<?php

	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir  . "inc_lude/header_challenges.php";
?>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="/editor/summernote/summernote-lite.css<?php echo VER;?>" rel="stylesheet">
<script src="/editor/summernote/summernote.js<?php echo VER;?>"></script>
<script src="/editor/summernote/lang/summernote-ko-KR.min.js<?php echo VER;?>"></script>

<style>
	@import url(//fonts.googleapis.com/earlyaccess/nanumgothic.css);
	.nanumgothic * {
		font-family: 'Nanum Gothic', sans-serif;
	}
</style>

<script>
	$(document).ready(function(){

		var fontList = ['맑은 고딕','굴림체','돋움체','바탕체','궁서체','Nanum Gothic','Noto Sans KR','Courier New','Arial Black','Arial','Tahoma'];
		var fontSizes = [ '8', '9', '10', '11', '12', '14','16', '18', '20', '22', '24', '28', '30', '36', '50', '72'];
		var toolbar = 
			[['fontname', 	[ 'fontname' ] ],
			['fontsize',	[ 'fontsize' ] ],
			['style',		[ 'bold', 'italic'] ],
			['color',		[ 'forecolor', 'color'] ],
			['table',		[ 'table' ] ],
			['para',		[ 'ul', 'ol', 'paragraph'] ],
			['insert',		['link', 'picture'] ],
			['his',			['undo', 'redo'] ],
			//['view', [ 'codeview' ] ]
		];

		$('#chall_contents').summernote({
			placeholder: '챌린지 내용을 입력해주세요.',
			width: 760,
			height : 800,
			minHeight : null,
			maxHeight : null,
			focus : true,
			lang : 'ko-KR',
			toolbar : toolbar,
			fontSizes : fontSizes,
			fontNames : fontList
		});

	
		var contents = $('#chall_contents').summernote('code');
		var text_null = '챌린지 내용을 입력하세요.';

	//	$('#chall_contents').summernote(setting);
		if( contents.length < 15 ){
			$('#chall_contents').summernote('editor.insertText', text_null);
		}

		$('#chall_contents').summernote('fontName', '맑은 고딕');
		$('#chall_contents').summernote('fontSize', '12');

		$(document).on("propertychange change keyup paste click", $("textarea[id='chall_contents']") , function() {
			if( contents.length < 15 ){
				if( contents.indexOf("챌린지 내용을 입력하세요.") > -1) {
					$("#chall_contents").summernote("code", "");
				}
			}
		});

		if(!$("#h4").val()){
			$(".tc_chall_coin .tc_input").addClass("on now_focus");
			$("#h4").val("1,000");
		}
	});
</script>

    <div class="todaywork_wrap">
        <div class="t_in">
            <!-- header -->
            <?php

				//top페이지
				include $home_dir . "inc_lude/top.php";

				$idx = $_GET['idx'];
				$idx = preg_replace("/[^0-9]/", "", $idx);

				$sql = "select idx, email, coin, title, title_emoji, emoji, sdate, edate, type, convert(varchar(max) , contents) as contents, files_name, action1, action2, outputchk from work_challenges where idx='".$idx."'";
				$res = selectQuery($sql);
				if($user_id != $res['email']){
					alertMove("권한이 없습니다.");
					exit;
				}

				if($res['idx']){

					$chall_idx = $res['idx'];
					$chall_title = $res['title'];
					$title_emoji = $res['title_emoji'];
					$chall_coin = $res['coin'];
					$date1 = $res['sdate'];
					$date2 = $res['edate'];
					$chall_type = $res['type'];
					$chall_contents = $res['contents'];
					$chall_action1 = $res['action1'];
					$chall_action2 = $res['action2'];
					$chall_outputchk = $res['outputchk'];
					$emoji = $res['emoji'];
					$files_name = $res['files_name'];

					if($chall_outputchk == 1){
						$text_chk = "checked=checked";
					}

					//$chall_contents = urldecode($chall_contents);

					if($chall_type == '0'){
						$chall_class_one = "class=\"on\"";
					}else if($chall_type == '1'){
						$chall_class_day = "class=\"on\"";
					}

					if($emoji == 1){
						$chall_title = urldecode($title_emoji);
					}


					$sql = "select idx, contents from work_contents where work_idx='".$res['idx']."'";
					$contents_info = selectQuery($sql);

					if($contents_info['idx']){
						$chall_contents = urldecode($contents_info['contents']);
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

                                    <div class="tc_chall_write">
                                        <ul>
                                            <li>
                                                <div class="tc_box_area">
                                                    <div class="tc_input now_focus">
                                                        <textarea name="" id="h1" class="input_01"><?=$chall_title?></textarea>
                                                        <label for="h1" class="label_01">
														<strong class="label_tit">챌린지명</strong>
													</label>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="tc_date_area">
                                                    <div class="tc_date_calendar">
                                                        <i class="far fa-calendar-alt"></i>
                                                    </div>
                                                    <div class="tc_date_before">
                                                        <input type="text" id="date1" name="date1" class="input_01" value="<?=$date1?>" />
                                                    </div>
                                                    <div class="tc_date_by">
                                                        <span>~</span>
                                                    </div>
                                                    <div class="tc_date_after">
                                                        <input type="text" id="date2" name="date2" class="input_01" value="<?=$date2?>" />
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="tc_chall_slc">
                                                    <em>기간 내</em>
                                                    <button <?=$chall_class_one?> id="chall_one"><span>한번만</span></button>
                                                    <button <?=$chall_class_day?> id="chall_day"><span>매일</span></button>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="tc_chall_edit">
                                                    <span><textarea name="" id="chall_contents" class="" class="input_01"><?=$chall_contents?></textarea></span>
                                                </div>
                                            </li>
                                            <!---<li>
                                                <div class="tc_chall_project">
                                                    <em>행동지침</em>
                                                    <div class="tc_box_area">
                                                        <div class="tc_input now_focus">
                                                            <textarea name="" id="action1" class="input_01"><?=$chall_action1?></textarea>
                                                            <label for="action1" class="label_01">
															<strong class="label_tit">1. 행동지침 입력</strong>
														</label>
                                                        </div>
                                                    </div>
                                                    <div class="tc_box_area">
                                                        <div class="tc_input now_focus">
                                                            <textarea name="" id="action2" class="input_01"><?=$chall_action2?></textarea>
                                                            <label for="action2" class="label_01">
															<strong class="label_tit">2. 행동지침 입력</strong>
														</label>
                                                        </div>
                                                    </div>
                                                    <?/*<button class="tc_chall_plus"><i class="fas fa-plus-circle"></i><span>행동지침 추가</span></button>*/?>
                                                </div>
                                            </li>-->
                                            <li>
                                                <div class="tc_chall_coin">
                                                    <em>보상 coin</em>
                                                    <div class="tc_input now_focus">
                                                        <input type="text" id="h4" name="h4" class="input_01" value="<?=$chall_coin?>"/>
                                                        <input type="hidden" id="chall_idx" value="<?=$chall_idx?>"/>
                                                        <label for="h4" class="label_01">
														<strong class="label_tit">보상할 coin 입력</strong>
													</label>
                                                        <span>coin</span>
                                                    </div>
                                                </div>
                                            </li>


											<li>
                                                <div class="tc_chall_coin">
                                                    <em>첨부파일</em>
                                                    
													<div class="tc_box_btns_upload">
														<input type="file" id="files" name="files" value="<?=$files_name?>"/>
														
													</div>
                                                	
                                                </div>
                                            </li>

											<li>
												<div class="tc_chall_chk">
													<input type="checkbox" name="chk01" id="chk01" <?=$text_chk?>/>
													<label for="chk01">챌린지 결과물을 등록해야 완료됩니다.</label>
												</div>
											</li>
                                        </ul>
                                    </div>

                                    <div class="tc_box_btn">
                                        <a href="javascript:void(0);" id="challenges_edit">챌린지 수정</a>
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