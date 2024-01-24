<?
//header페이지
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "../inc_lude/header_about.php";

$sql = "select idx, title, contents from bro_faq where state = '1' order by idx desc";
$query = selectAllQuery($sql);

?>
<div class="rb_main fp-notransition">
	<div class="rb_main_sub_img">
		<div class="cs_wrap">
			<div class="cs_in">
				<div class="cs_slogan">
					<strong>리워디 고객센터에서 <br />실시간으로 상담받으세요.</strong>
					<span>[업무시간] 오전 9:30 ~ 12:30 / 오후 13:30 ~ 18:30</span>
				</div>
				<div class="cs_boxs">
					<div class="cs_box">
						<span>전화문의</span>
						<strong>1899-6844</strong>
						<button class="btn_layer_reserv">전화 전 클릭</button>
					</div>
					<div class="cs_box">
						<span>이메일 문의</span>
						<strong>rewardy@rewardy.co.kr</strong>
						<button class="btn_layer_ask">문의하기</button>
					</div>
				</div>
			</div>
		</div>
		<div class="help_wrap">
			<div class="help_in">
				<strong>도움이 필요하신가요? 최선을 다해 도와드리겠습니다.</strong>
				<div class="help_boxs">
					<div class="help_box">
						<button class="btn_layer_reserv"><span>전화상담 예약</span></button>
					</div>
					<div class="help_box">
						<!-- <button><span>원격지원 요청</span></button> -->
					</div>
					<div class="help_box">
						<button class="btn_layer_ask"><span>이메일 문의</span></button>
					</div>
				</div>
			</div>
		</div>
		<div class="faq_wrap">
			<div class="faq_header">
				<strong>무엇을 도와드릴까요?</strong>
			</div>

			<div class="faq_mid">
				<ul class="faq_list">
					<? for($i=0;$i<count($query['idx']);$i++){?>
						<li>
							<div class="faq_q">
								<div class="faq_q_txt">
									<p><?=$query['title'][$i]?></p>
								</div>
							</div>
							<div class="faq_a">
								<div class="faq_a_txt">
									<?=$query['contents'][$i]?>
								</div>
							</div>
						</li>
					<?}?>
				</ul>
			</div>

			<div class="faq_bottom">
				<div class="faq_btn">
					<button onclick="location.href='./question.php'"><span>더 보기</span></button>
				</div>
			</div>
		</div>
	</div>
	<?
	//footer 페이지
	include $home_dir . "/inc_lude/footer_about.php";
	?>
