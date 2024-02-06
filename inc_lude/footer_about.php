					<div class="section09 fp-auto-height" id="section09">
						<div class="section09_in">
							<div class="section09_footer">
								<ul>
									<li><span>(주)비즈폼</span> | <span>서울특별시 강남구 역삼로 204 (역삼동) 604호</span> |
									<span>부산광역시 해운대구 해운대해변로 257 (우동, 하버타운) 1601호</span> | 
									<span>대표이사 : 이선규</span>
									</li>
									<li>
										<span>사업자등록번호 : 605-81-38178</span> | <span>통신판매업 신고번호 : 제2015-부산해운-0582호</span> |
										<span>이메일 : rewardy@rewardy.co.kr</span>
									</li>
								</ul>
								<p>Copyright (c) 2000-2022 by bizforms.co.kr All rights reserved.</p>
							</div>
							<div class="footer_list">
								<a class ="service" href="http://demo.rewardy.co.kr/about/service.php"><span>이용약관</span></a>
								<a class ="p_guide" href="http://demo.rewardy.co.kr/about/privacy_guide.php"><span>개인정보취급방침</span></a>
							</div>
						</div>
					</div>
				</div>
				<?
				//index페이지 단독
				if (basename($_SERVER['PHP_SELF']) == "index.php") {
				?>
			</div>
		</div>

		<div class="quick_nav">
			<div class="quick_nav_inner">
				<ul>
					<li class="quick_nav_go_01"><button><strong></strong></button><span>리워디</span></li>
					<li class="quick_nav_go_02"><button><strong></strong></button><span>오늘업무</span></li>
					<li class="quick_nav_go_03"><button><strong></strong></button><span>챌린지</span></li>
					<li class="quick_nav_go_04"><button><strong></strong></button><span>LIVE</span></li>
					<li class="quick_nav_go_05"><button><strong></strong></button><span>보상</span></li>
					<li class="quick_nav_go_06"><button><strong></strong></button><span>역량</span></li>
					<li class="quick_nav_go_10"><button><strong></strong></button><span>타임라인</span></li>
					<li class="quick_nav_go_11"><button><strong></strong></button><span>실시간 업무</span></li>
					<li class="quick_nav_go_12"><button><strong></strong></button><span>파티</span></li>
					<li class="quick_nav_go_13"><button><strong></strong></button><span>인사이트</span></li>
					<li class="quick_nav_go_07"><button><strong></strong></button><span>UI</span></li>
				</ul>
			</div>
		</div>
	<?}?>
<?
//공통 레이어 인클루드
include $home_dir . "/inc_lude/login_layer_about.php";
?>


	<!-- Step 1) Load D3.js -->
	<script src="https://d3js.org/d3.v6.min.js"></script>
	<!-- Step 2) Load billboard.js with style -->
	<script src="/about/js/billboard.js"></script>
	<!-- Load with base style -->
	<link rel="stylesheet" href="/about/css/billboard.css">
	<script type="text/javascript">
		$(document).ready(function(){

		});
	</script>
</div>

<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/Rewardy"; /* 페이지 이름 지정 Contents Path */
</script>

</body>


</html>
