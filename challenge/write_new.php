<?

	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir  . "inc_lude/header_challenges.php";

	
	//date('w' , strtotime(날짜) ) 는 0 ~ 6 반환.
	//일요일 - 0  ~ 토요일 -6....

	
	//요청회원
	$highlevel = 5;
	$sql = "select idx, name, part from work_member where state='0' and highlevel='".$highlevel."' and companyno='".$companyno."' and email != '".$user_id."' order by idx asc";
	$mem_req = selectAllQuery($sql);

?>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<link href="/editor/summernote/summernote-lite.css<?php echo VER;?>" rel="stylesheet">
<script src="/editor/summernote/summernote-lite.min.js<?php echo VER;?>"></script>

<script>
//	$(jquery) = jQuery.noConflict();
</script>
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
	}

	.note-editable hr {
		border: 1px solid #c1c1c1;
	}

</style>

<script>

/*	var text_null = "<span style='font-family: Noto Sans KR; font-size: 13px;'>즐거운 회사 생활을 위해 가장 필수적인 요소는 회사의 보안을 점검하는 것!</span>";
	text_null  += "<br />";
	text_null  += "<span style='font-family: Noto Sans KR; font-size: 13px;'>사내 네트워크의 각종 외부 공격으로 막을 수 있는 간단한 방법은 우리 모두가 사용하는 PC를 업데이트 하는 것입니다.</span>";
	text_null  += "<br />";
	text_null  += "<br />";
	text_null  += "<div style='display:; border: 1px solid #A5A5A5;'></div>";
	text_null  += "<br />";
	text_null  += "<span style='font-size: 20px;'>✔️ <b>챌린지 참여방법</b></span>";
	text_null  += "<br />";
	text_null  += "<span style='font-family: Noto Sans KR; font-size: 13px; line-height:25px;'>1. 매월 10일 Windows 업데이트 확인을 해주세요.</span>";
	text_null  += "<br />";
	text_null  += "<span style='font-family: Noto Sans KR; font-size: 13px; line-height:25px;'>2. 정상적인 업데이트를 진행한 후 인증을 해주세요.</span>";
	text_null  += "<br />";
	text_null  += "<span style='font-family: Noto Sans KR; font-size: 13px; line-height:25px;'>3. 최신 상태임을 확인할 수 있는 사진을 업로드해주세요.</span>";
	text_null  += "<br />";
	text_null  += "<br />";
	text_null  += "<div style='display:; border: 1px solid #A5A5A5;'></div>";
	text_null  += "<br />";
	text_null  += "<span style='font-size: 20px;'>📌 <b>챌린지 유의사항</b></span>";
	text_null  += "<br />";
	text_null  += "<span style='font-family: Noto Sans KR; font-size: 13px; line-height:25px;'>- 필수 참여 챌린지입니다.</span>";
	text_null  += "<br />";
	text_null  += "<span style='font-family: Noto Sans KR; font-size: 13px; line-height:25px;'>- 개인이 참여하는 것보다 전체가 함께 참여한다는 것을 잊지 마세요!</span>";
	text_null  += "<br />";
	text_null  += "<span style='font-family: Noto Sans KR; font-size: 13px; line-height:25px;'>- 업데이트 완료 사진은 자신을 나타낼 수 있게 표현해주세요.</span>";
	text_null  += "<br />";
*/

	var text_null ='<p>';
	text_null +='<span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px;"><font color="#9c9c94">챌린지 내용을 작성해주세요.</span>';
	text_null +='<br>';
	text_null +='</p>';
	
	text_null +='<br>';
	text_null +='<br>';
	text_null +='<br>';
	text_null +='<br>';
	
	text_null +='<hr>';
	text_null +='<br>';
	text_null +='<p>';
	text_null +='</p>';
	text_null +='<p>';
	text_null +='<span style="font-size: 20px;">';
	text_null +='<span style="font-family: &quot;Noto Sans KR&quot;;">✔️ </span>';
	text_null +='<b>';
	text_null +='<span style="font-family: &quot;Noto Sans KR&quot;;">챌린지 참여방법</span>';
	text_null +='</b>';
	text_null +='</span>';
	text_null +='<span style="font-size: 12px;">';
	text_null +='<br>';
	text_null +='</span>';
	text_null +='</p>';
	text_null +='<p>';
	text_null +='<span style="font-family: " noto="" sans="" kr";="" font-size:="" 13px;"="">';
	text_null +='<br>';
	text_null +='</span>';
	text_null +='</p>';
	text_null +='<p>';
	text_null +='<span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; font-color=" #cec6ce";"="" noto="" sans="" kr";="" font-size:="" 13px;"="" ;="">1. <font color="#9c9c94">챌린지 참여방법을 작성해주세요.</font></span>';
	text_null +='<br>';
	text_null +='</p>';
	text_null +='<p>';
	text_null +='<span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; line-height: 25px;">2. </span>';
	text_null +='</p>';
	text_null +='<p>';
	text_null +='<span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; line-height: 25px;">3. </span>';
	text_null +='<br>';
	text_null +='</p>';
	text_null +='<p>';
	text_null +='<br>';
	text_null +='</p>';
	text_null +='<hr>';
	text_null +='<p>';
	text_null +='<span style="font-size: 12px;">&#xFEFF;</span>';
	text_null +='<span style="font-size: 20px;">';
	text_null +='<br>';
	text_null +='</span>';
	text_null +='</p>';
	text_null +='<p>';
	text_null +='<span style="font-size: 20px; font-family: &quot;Noto Sans KR&quot;;">📌 </span>';
	text_null +='<b style="font-size: 20px;">';
	text_null +='<span style="font-family: &quot;Noto Sans KR&quot;;">챌린지 유의사항</span>';
	text_null +='</b>';
	text_null +='<br>';
	text_null +='</p>';
	text_null +='<p>';
	text_null +='<span style="font-family: " noto="" sans="" kr";="" font-size:="" 13px;"="">';
	text_null +='<br>';
	text_null +='</span>';
	text_null +='</p>';
	text_null +='<p>';
	text_null +='<span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px;" noto="" sans="" kr";="" font-size:="" 13px;"="">1. <font color="#9c9c94">챌린지 유의사항을 입력해주세요.</font></span>';
	text_null +='<br>';
	text_null +='</p>';
	text_null +='<p>';
	text_null +='<span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; line-height: 25px;">2. </span>';
	text_null +='</p>';
	text_null +='<p>';
	text_null +='<span style="font-family: &quot;Noto Sans KR&quot;; font-size: 14px; line-height: 25px;">3. </span>';
	text_null +='<br>';
	text_null +='</p>';
	text_null +='</div>';



	$(document).ready(function(){

		var fontList = ['맑은 고딕','굴림체','돋움체','바탕체','궁서체','Nanum Gothic','Noto Sans KR','Courier New','Arial Black','Arial','Tahoma'];
		var fontSizes = [ '8', '9', '10', '11', '12', '13', '14','16', '18', '20', '22', '24', '28', '30', '36', '50', '72'];
		var toolbar = 
			[['fontname', 		[ 'fontname' ] ],
			['fontsize',		[ 'fontsize' ] ],
			['style',			[ 'bold', 'italic', 'underline', 'strikethrough' , 'forecolor', 'backcolor', 'paragraph' ,'clear'] ],
			//['color',			[ 'forecolor', 'backcolor'] ],
			['height',			[ 'height']],
			//['para',			[] ],
			['insert',			[ 'link', 'picture' ,'video'] ],
			['hr',				[ 'hr' ]]
			

			//'ul', 'ol',
		/*	['fontsizeunit',	['fontsizeunit']],
			['backcolor',		['backcolor']],
			['superscript',		['superscript']],
			['subscript',		['subscript']],
			['clear',			['clear']],
			['fullscreen',		['fullscreen']],
			['codeview',		['codeview']],

			
			['help',			['help']],


			['video',			['video']],
			['table',			['table']],

			['underline',		['underline']],
			['strikethrough',	['strikethrough']],

			['view', [ 'codeview' ] ]*/
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
			
			/*callbacks : { //여기 부분이 이미지를 첨부하는 부분
				onImageUpload : function(files, editor,	welEditable) {
					for (var i = files.length - 1; i >= 0; i--) 
					{
						//uploadSummernoteImageFile(files[i],	this);
					}
				},
				onMediaDelete : function(target) {
					//console.log(target[0]);
					//deleteFile(target[0].src);
				}
			}*/
		};

		//var text_null = '챌린지 내용을 입력하세요.';
		$('#chall_contents').summernote(setting);
		$('#chall_contents').summernote('fontName', '맑은 고딕');
		$('#chall_contents').summernote('fontSize', '12');
		$('#chall_contents').summernote('code', text_null);

		/*$(document).on("propertychange change keyup paste click", $("textarea[id='chall_contents']") , function() {
			var contents = $('#chall_contents').summernote('code');
			if( contents.indexOf("챌린지 내용을 입력하세요.") > -1) {
				//$('#chall_contents').code('');
				//$('#chall_contents').summernote('editor.insertText', ' ');
				$("#chall_contents").summernote("code", "");
			}else{
			}
		});*/


		/*$('#chall_contents').summernote({
			height: 450,   			//set editable area's height
			lang: "ko-KR", 			// 한글 설정
			codemirror: { 			// codemirror options
				theme: 'monokai'
			}
		});*/


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
                                                <div class="tc_chall_slc">
                                                    <button class="on" id="chall_cate1" value="1"><span>업무</span></button>
                                                    <button id="chall_cate2" value="2"><span>생활</span></button>
                                                    <button id="chall_cate3" value="3"><span>교육</span></button>
                                                    <button id="chall_cate4" value="4"><span>문화</span></button>
                                                    <button id="chall_cate5" value="5"><span>신입사원</span></button>
                                                    <button id="chall_cate6" value="6"><span>기타</span></button>
                                                    
                                                </div>
                                            </li>


                                            <li>
                                                <div class="tc_box_area">
                                                    <div class="tc_input">
                                                        <textarea name="" id="h1" class="input_01"></textarea>
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
                                                        <input type="text" id="date1" name="date1" class="input_01" value="<?=date("Y-m-d", time())?>" />
                                                    </div>
                                                    <div class="tc_date_by">
                                                        <span>~</span>
                                                    </div>
                                                    <div class="tc_date_after">
                                                        <input type="text" id="date2" name="date2" class="input_01" value="<?=date("Y-m-d", time())?>" />
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="tc_chall_slc">
                                                    <em>기간 내</em>
                                                    <button class="on" id="chall_one"><span>한번만</span></button>
                                                    <button id="chall_day"><span>매일</span></button>
													<?for($i=1; $i<8; $i++){
														if ($i == 7) {?>
															<button id="chall_days0" value="0"><span>일</span></button>
														<?}else{?>
                                                    		<button id="chall_days<?=$i?>" value="<?=$i?>"><span><?=$week[$i]?></span></button>
														<?}?>
													<?}?>
                                                </div>
                                            </li>
											
											<li>
                                                <div class="tc_chall_slc">
                                                    <em>참여자</em>
                                                    <button class="on" id="user_all"><span>전체</span></button>
                                                    <button id="user_one"><span>일부</span></button>
                                                </div>
                                            </li>


											<li>
                                                <div class="tc_chall_coin">
                                                    <em>참여횟수</em>
                                                    <div class="tc_input">
                                                        <input type="text" id="h5" name="" class="input_01" value="1"/>
                                                        <label for="h5" class="label_01">
														<strong class="label_tit"></strong>
													</label>
                                                        <span>참여</span>
                                                    </div>
                                                </div>
                                            </li>

											<li>
												<div class="tc_add_user_list" id="add_user_list">
													<ul>
														<li>
															<input type="checkbox" name="chkall" id="chkall" value="">
															<label for="chkall">전체선택</label>
														</li>

														<?for($i=0; $i<count($mem_req['idx']); $i++){?>
															<li>
																<input type="checkbox" name="chk" id="chk<?=$i?>" value="<?=$mem_req['idx'][$i]?>">
																<label for="chk<?=$i?>"><?=$mem_req['name'][$i]?><span>(<?=$mem_req['part'][$i]?>)</span></label>
															</li>
														<?}?>
													</ul>
												</div>
											</li>

                                            <li>
                                                <div class="tc_chall_edit">
                                                    <span><textarea name="" id="chall_contents" class="" class="input_01"></textarea></span>
                                                </div>
                                            </li>

                                            <li>
                                                <div class="tc_chall_coin">
                                                    <em>보상 coin</em>
                                                    <div class="tc_input">
                                                        <input type="text" id="h4" name="" class="input_01" />
                                                        <label for="h4" class="label_01">
														<strong class="label_tit">보상할 coin 입력</strong>
													</label>
                                                        <span>coin</span>
                                                    </div>
                                                </div>
                                            </li>
											
											<!--<li id="div_file">
                                                <div class="tc_chall_coin">
                                                    <em> 첨부파일 +</em>
                                                    
													<div class="tc_box_btns_upload">
														<input type="file" id="files" name="files"/>
													</div>
                                                	
                                                </div>
                                            </li>-->


											<!--<li>
                                                <div class="tc_chall_coin">
                                                    <em id="file_add_bt"> 파일첨부 + </em>
                                                </div>
                                            </li>-->

											<li>
                                                <div class="tc_chall_coin">
                                                    <em id="file_add_bt"> 파일첨부 </em>
                                                </div>
                                            </li>

											<li id="file_add">
												<div class='' id='files_add1'>
													<input type='file' id='files1' name='files' /><span id="delfile1"></span>
												</div>
												<br>
												<div class='' id='files_add2'>
													<input type='file' id='files2' name='files' /><span id="delfile2"></span>
												</div>
												<br>
												<div class='' id='files_add3'>
													<input type='file' id='files3' name='files' /><span id="delfile3"></span>
												</div>
											</li>

											<li>
                                                <div class="tc_chall_coin">
                                                    <em> 사진첨부 </em>
													<div>삭제1</div>
												
													<div id="preview1" style="width:200px; height:200px; border:1px solid #333; margin-right:10px; float: right;"></div>
													<input type="file" id="img_file1" name="file" style="display:none;">
													
													<div>삭제2</div>
													<div id="preview2" style="width:200px; height:200px; border:1px solid #333; margin-right:10px; float: right;"></div>
													<input type="file" id="img_file2" name="file" style="display:none;">

													<div>삭제3</div>
													<div id="preview3" style="width:200px; height:200px; border:1px solid #333; margin-right:10px; float: right;"></div>
													<input type="file" id="img_file3" name="file" style="display:none;">
                                                </div>
                                            </li>

											<li>
												<em id="img_del1"> 삭제1 </em>
												<em id="img_del2"> 삭제2 </em>
												<em id="img_del3"> 삭제3 </em>
                                            </li>


											<?php
											/*<li>
                                                <div class="tc_chall_slc">
                                                    <input type="file" id="file" name="file"/>
                                                </div>
                                            </li>*/
											?>
											<li>
												<div class="tc_chall_chk">
													<input type="checkbox" name="chk01" id="chk01" />
													<label for="chk01">챌린지 결과물을 등록해야 완료됩니다.</label>
												</div>
											</li>
											<li>
                                                <div class="tc_chall_slc">
                                                    <em>게시물 노출여부</em>
                                                    <button class="on" id="list_view"><span>노출</span></button>
                                                    <button id="list_hidden"><span>숨김</span></button>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
									
                                    <div class="tc_box_btn">
                                        <a href="javascript:void(0);" id="challenges_write">챌린지 생성</a>
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

