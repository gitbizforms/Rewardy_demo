<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">리워디 백오피스</div>
                <a class="nav-link collapsed" id="collapse_service" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    6대 서비스
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?=$url=="backcoin_list"?'text-light':''?>" href="coin_list.php">코인</a>
                        <a class="nav-link <?=$url=="backlike_list"?'text-light':''?>" href="like_list.php">좋아요</a>
                        <a class="nav-link <?=$url=="backchall_list"?'text-light':''?>" href="challenge_list.php">챌린지</a>
                        <a class="nav-link <?=$url=="backchall_user"?'text-light':''?>" href="challenge_user_list.php">챌린지참여</a>
                        <a class="nav-link <?=$url=="backparty_list"?'text-light':''?>" href="project_list.php">파티</a>
                        <a class="nav-link <?=$url=="backwork_list"?'text-light':''?>" href="todaywork_list.php">오늘업무</a>
                        <a class="nav-link <?=$url=="backalarm_list"?'text-light':''?>" href="alarm_list.php">알림 리스트</a>
                    </nav>
                </div>
                <a class="nav-link collapsed" id="collapse_log" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayoutsLog" aria-expanded="false" aria-controls="collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-gears"></i></div>
                    리워디 로그
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseLayoutsLog" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?=$url=="backlog_list"?'text-light':''?>" href="data_log.php">데이터 로그</a>
                        <a class="nav-link <?=$url=="backcp_list"?'text-light':''?>" href="cp_reward_list.php">역량성장 리스트</a>
                        <a class="nav-link <?=$url=="backin_list"?'text-light':''?>" href="incount.php">출근기록</a>
                        <a class="nav-link <?=$url=="backcomm_list"?'text-light':''?>" href="commute_list.php">퇴근/퇴근소감</a>
                        <a class="nav-link <?=$url=="backpenalty_list"?'text-light':''?>" href="penalty_list.php">페널티/경고</a>
                    </nav>
                </div>
                <a class="nav-link collapsed" id="collapse_total" href="#" data-bs-toggle="collapse" data-bs-target="#collapseInfo" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-circle-info"></i></div>
                    정보
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseInfo" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                        <a class="nav-link <?=$url=="backuser_list"?'text-light':''?>" href="user_list.php">회원 정보</a>
                        <a class="nav-link collapsed <?=$url=="backcomp_list"?'text-light':''?>" href="company_list.php" >기업별 정보</a>
                    </nav>
                </div>
                <a class="nav-link collapsed" id="collapse_total" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-chart-pie"></i></div>
                    누계 차트
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                        <a class="nav-link collapsed <?=$url=="backwork_total"?'text-light':''?>" href="work_total.php" >업무 누계차트</a>
                        <a class="nav-link collapsed <?=$url=="backlike_total"?'text-light':''?>" href="like_total.php" >좋아요 누계차트</a>
                        <a class="nav-link collapsed <?=$url=="backerror_log"?'text-light':''?>" href="error_list.php" >에러 리스트</a>
                    </nav>
                </div>
                <div class="sb-sidenav-menu-heading">부가기능</div>
                <!-- <a class="nav-link" href="charts.html"><div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>Charts</a> -->
                <a class="nav-link" href="user_auth.php"><div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>서비스별 권한 부여</a>
                <a class="nav-link" href="/challenges/challenge_template.php"><div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>챌린지 템플릿 페이지 </a>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in</div>
            BackOffice_Rewardy
        </div>
    </nav>
</div>