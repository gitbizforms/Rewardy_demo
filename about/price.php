<?
//header페이지
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "/inc_lude/header_about.php";
// $type_flag = ($chkMobile)?1:0;	
?>
<div class="rb_main fp-notransition">
	<div class="rb_price">
		<div class="rb_price_in">
			<div class="rb_price_top">
				<p>
					<strong>가격정책</strong>
					<span>회사 규모에 맞게 결제 후 이용하시면 됩니다. <br />무료 체험으로 리워디를 먼저 경험해 보세요.</span>
				</p>
				<a href="./0000.php" target="_blank"><span>무료 체험</span></a>
			</div>
			<div class="rb_price_bottom">
				<div class="price_area">
					<div class="price_money">
						<strong>최초 가입비 100,000원 / 구성원 1명당 3,000원 </strong><span>(1개월 기준)</span>
						<button class ="price_btn">결제하기</button>
					</div>
					<div class="price_desc">
						<dl>
							<dt><span>📝</span>오늘업무</dt>
							<dd>- 오늘할일 작성</dd>
							<dd>- 일정예약 기능</dd>
							<dd>- 업무요청 기능</dd>
							<dd>- 업무공유 기능</dd>
							<dd>- 피드백 기능</dd>
						</dl>
						<dl>
							<dt><span>📢</span>LIVE</dt>
							<dd>- 나의 업무현황 공유</dd>
							<dd>- 일정 공유 기능</dd>
							<dd>- 프로젝트 생성 기능</dd>
							<dd>- 근태관리 기능</dd>
							<dd>- 역량평가 리포트</dd>
							<dd>- 평가(좋아요) 기능</dd>
						</dl>
						<dl>
							<dt><span>💰</span>보상</dt>
							<dd>- 코인 적립, 출금</dd>
							<dd>- 코인 충전 기능</dd>
							<dd>- 코인 보상 기능</dd>
							<dd>- 코인 사용 기능</dd>
						</dl>
						<dl>
							<dt><span>🏆</span>챌린지</dt>
							<dd>- 챌린지 테마 샘플</dd>
							<dd>- 챌린지 생성 기능</dd>
							<dd>- 코인 보상 지급 기능</dd>
						</dl>
						<dl>
							<dt><span>💡</span>기타</dt>
							<dd>- 개인별 업무 타임라인</dd>
							<dd>- 근태, 업무 관련 페널티 카드</dd>
							<dd>- 개인별 성과 노출 기능</dd>
						</dl>
					</div>
				</div>
				<div class="price_ep">
					<dl>
						<dt>Enterprise (엔터프라이즈)</dt>
						<dd>30인 이상 기업은 할인을 제공합니다.</dd>
					</dl>
					<button>가격문의</button>
				</div>
			</div>
		</div>
	</div>
<?

//footer 페이지
include $home_dir . "/inc_lude/footer_about.php";
?>
