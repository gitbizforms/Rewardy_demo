<!-- 1026 검색 -->
<div class="search_layer" id="search_layer" style="display:none;">
	<div class="sl_deam"></div>
	<div class="sl_in">
		<div class="sl_box">
			<div class="sl_box_in">
				<div class="sl_close" id="sl_close">
					<button><span>닫기</span></button>
				</div>
				<div class="sl_top">
					<strong>오늘업무 검색</strong>
				</div>
				<div class="sl_list">
					<ul>
						<li>
							<div class="sl_date_area">
								<div class="date_area_l" id="date_area_l">
									<input type="text" class="input_cha_date_l" id="input_cha_date_l" value="<?=$month_first_day?>">
									<span>~</span>
									<input type="text" class="input_cha_date_r" id="input_cha_date_r" value="<?=$month_last_day?>">
								</div>
							</div>
						</li>
						<li>
							<div class="sl_div">
								<div class="sl_slc" id="sl_slc">
									<button class="btn_sort_on" id="btn_sort_on"><span>전체</span></button>
									<ul>
										<li><button value="all"><span>전체</span></button></li>
										<li><button value="works"><span>나의업무</span></button></li>
										<li><button value="report"><span>보고</span></button></li>
										<li><button value="req"><span>요청</span></button></li>
										<li><button value="share"><span>공유</span></button></li>
										<li><button value="file"><span>첨부파일</span></button></li>
										<li><button value="memo"><span>메모</span></button></li>
									</ul>
								</div>
								<div class="tc_input">
									<input type="text" id="sl1" name="sl1" class="input_001" placeholder="검색어를 입력하세요">
									<label for="sl1" class="label_001">
										<strong class="label_tit">검색어를 입력하세요</strong>
									</label>
								</div>
							</div>
						</li>
					</ul>
				</div>
				<div class="sl_btn">
					<button id="sl_btn"><span>검색</span></button>
				</div>
			</div>
		</div>
	</div>
</div>