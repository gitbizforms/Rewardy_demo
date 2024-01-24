<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";

?>


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

						<div class="rew_member_tab">
							<div class="rew_member_tab_in">
								<ul>
									<li class="member_list"><a href="#"><span>멤버관리</span></a></li>
                  <? if($user_id == 'suyong9851@nate.com'){?>
									<li class="comlogo on"><a href="#"><span>홈페이지 로고 관리</span></a></li>
									<? }?>
                  <li class="comcoin"><a href="#"><span>공용코인 관리</span></a></li>
									<li class="comcoin_member"><a href="#"><span>멤버별 공용코인</span></a></li>
									<li class="comcoin_out_page"><a href="#"><span>코인출금 신청내역</span></a></li>
								</ul>
							</div>
						</div>
					
					
					<div class="rew_conts_scroll_01">
						<div class="tdw_logo_area_in">
							<!-- 파일 썸네일 표시 영역 -->
							<div id="thumbnail-container"></div>

							<div class = "logo_file_box">
								<input type="file" id="files" class="logo_input_file" multiple>
								<label for="files" class="logo_label_file" id="logo_label_file" ><span>첨부 파일</span></label>
							</div>
						</div>

							
						<div class = "logo_btn" value="<?php echo $companyno?>">
							<button><span>등록하기</span></button>
						</div>
					</div>
				</div>
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>


	<?php
		//로딩 페이지
		include $home_dir . "loading.php";
	?>	
	<script type="text/javascript">

		$(document).ready(function(){
		window.onbeforeunload = function () { $('.rewardy_loading_01').css('display', 'block'); }
		$(window).load(function () {          //페이지가 로드 되면 로딩 화면을 없애주는 것
            $('.rewardy_loading_01').css('display', 'none');
        	});
		});
		window.onpageshow = function(event) {
			if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
				$('.rewardy_loading_01').css('display', 'none');
			}
		}


		var format_ext = new Array(
    "asp",
    "php",
    "jsp",
    "xml",
    "html",
    "htm",
    "aspx",
    "exe",
    "exec",
    "java",
    "js",
    "class",
    "as",
    "pl",
    "mm",
    "o",
    "c",
    "h",
    "m",
    "cc",
    "cpp",
    "hpp",
    "cxx",
    "hxx",
    "lib",
    "lbr",
    "ini",
    "py",
    "pyc",
    "pyo",
    "bak",
    "$$$",
    "swp",
    "sym",
    "sys",
    "cfg",
    "chk",
    "log",
    "lo"
  );


 

 

  $(document).on("click", ".logo_btn", function(){
    var fdata = new FormData();
    var company = $(".logo_btn").attr("value");
    console.log(company);
	var fileobj = $("input[id='files']")[0].files; 
    if (fileobj) {
      if (fileListArr.length > 0) {
		fdata.append("files[]", $("input[id='files")[0].files[0]);
      }
    }

    fdata.append("mode", "logo_upload"); 
    fdata.append("company", company); 
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,  
      url: "/inc/member_process.php",
      success: function (data) {
        if (data) {
          // work_list() 활성화
        console.log(data);
        // 파일 업로드 완료 후, fileListArr 초기화
        }
      },
    });

  });

  $("input[id='files']").change(function () {
    var file_box_cnt = $(".tdw_logo_area_in #logo_file_desc").length;
    var file_obj = $(this)[0].files; //파일정보
    var file_name = file_obj[0]['name'];
    var ext = file_name.split(".").pop().toLowerCase();
    var maxSize = 100 * 1024 * 1024;
    
    //if (!(new RegExp(format_ext, 'i')).test(file_name)) {
    if ($.inArray(ext, format_ext) > 0 ) {
      alert("첨부할 수 없는 파일입니다.\n파일명 : " + file_name + "");
      return false;
    } else {
      fileListArr.push(file_obj);
      $(".tdw_logo_area_in #thumbnail-container").after(
      '<div class="logo_file_desc" id="logo_file_desc"><span>' +
        file_name +
        '</span><button id="work_file_del">삭제</button></div>'
      );

      // 파일 미리보기 생성
      var reader = new FileReader();
      reader.onload = function (e) {
        var thumbnailContainer = document.getElementById("thumbnail-container");
        var img = document.createElement("img");
        img.src = e.target.result;
        img.className = "thumbnail";
        thumbnailContainer.appendChild(img);
      };
      reader.readAsDataURL(file_obj[0]);
    }

    if (fileListArr.length > 0) {
    fileListArr.reverse();
    }
  });

 //파일첨부가 있을경우
 if ($("input[id='files']").length > 0) {
  //최대파일갯수
    var max_file_cnt = 3;
    //var fileListArr = Array.from($("input[id='files']")[0].files);
    var fileListArr = new Array();
  }
	</script>
</div>
	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->

</body>


</html>
