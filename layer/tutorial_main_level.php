<?
	$member_info = member_row_info($user_id);
	$tuto_flag = $member_info['t_flag'];	
?>

<div class="tuto_phase" style="display:none;">
	<div class="tuto_phase_deam"></div>
	<div class="tuto_phase_in">
		<div class="tuto_phase_tit">
			<strong>튜토리얼로 레벨업!</strong>
			<span>단계별로 튜토리얼을 통해서 역량점수와 좋아요를 받으세요~</span>
		</div>
		<div class="tuto_phase_list">
			<div class="tuto_phase_box phase_01 <?=$tuto_flag>=1?'tuto_clear':''?> <?=$tuto_flag==0?'tuto_on':''?>">
				<p>1</p>
				<button>
					<dl>
						<dt>오늘업무</dt>
						<dd>
							<span>역량</span>
							<strong>2</strong>
						</dd>
						<dd>
							<span>좋아요</span>
							<strong>1</strong>
						</dd>
					</dl>
					<em>도전하기</em>
				</button>
			</div>
			<div class="tuto_phase_box phase_02 <?=$tuto_flag>=2?'tuto_clear':''?> <?=$tuto_flag==1?'tuto_on':''?>">
				<p>2</p>
				<button>
					<dl>
						<dt>좋아요</dt>
						<dd>
							<span>좋아요</span>
							<strong>2</strong>
						</dd>
						<dd>
							<span>역량</span>
							<strong>1</strong>
						</dd>
					</dl>
					<em>도전하기</em>
				</button>
			</div>
			<div class="tuto_phase_box phase_03 <?=$tuto_flag>=3?'tuto_clear':''?> <?=$tuto_flag==2?'tuto_on':''?>">
				<p>3</p>
				<button>
					<dl>
						<dt>코인 보상</dt>
						<dd>
							<span>좋아요</span>
							<strong>1</strong>
						</dd>
						<dd>
							<span>역량</span>
							<strong>1</strong>
						</dd>
					</dl>
					<em>도전하기</em>
				</button>
			</div>
			<div class="tuto_phase_box phase_06 <?=$tuto_flag>=6?'tuto_clear':''?> <?=$tuto_flag==5?'tuto_on':''?>">
				<p>6</p>
				<button>
					<dl>
						<dt>메인</dt>
						<dd>
							<span>좋아요</span>
							<strong>2</strong>
						</dd>
						<dd>
							<span>역량</span>
							<strong>2</strong>
						</dd>
					</dl>
					<em>도전하기</em>
				</button>
			</div>
			<div class="tuto_phase_box phase_05 <?=$tuto_flag>=5?'tuto_clear':''?> <?=$tuto_flag==4?'tuto_on':''?>">
				<p>5</p>
				<button>
					<dl>
						<dt>챌린지 도전</dt>
						<dd>
							<span>역량</span>
							<strong>2</strong>
						</dd>
						<dd>
							<span>좋아요</span>
							<strong>1</strong>
						</dd>
					</dl>
					<em>도전하기</em>
				</button>
			</div>
			<div class="tuto_phase_box phase_04 <?=$tuto_flag>=4?'tuto_clear':''?> <?=$tuto_flag==3?'tuto_on':''?>">
				<p>4</p>
				<button>
					<dl>
						<dt>파티 체험</dt>
						<dd>
							<span>역량</span>
							<strong>1</strong>
						</dd>
						<dd>
							<span>좋아요</span>
							<strong>1</strong>
						</dd>
					</dl>
					<em>도전하기</em>
				</button>
			</div>
		</div>
		<div class="tuto_phase_pause">
			<button>다음에 이어하기</button>
		</div>
	</div>
</div>