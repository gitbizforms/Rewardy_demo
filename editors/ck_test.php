<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8"/>
<title>CKEditor 4 설치하기</title>

<?
 $html ='<!--//Mong9 Editor//--><!--//m9_font_family(//fonts.googleapis.com/earlyaccess/jejugothic.css,)//-->
    <div class="m9-grid-block">
    <div class="m9-grid-1">
    <div class="m9-column-1 _not-copy">
    <div class="float-left m-display-block m-float-none m-width-100 m-text-align-center" data-m9-m-style="max-width:100%" style="max-width:50%"><img alt="" alt_no="1" class="m9-fullimg m9-img-size-2" data-m9-m-style="width:auto" src="https://rewardy.co.kr/editors/ckeditor/plugins/mong9-editor/source/img/example/example108.jpg" style="width:255px" /></div>
    
    <div class="display-block overflow-hidden m9-padding-height-1 m9-padding-width-3 m-overflow-visible m-m9-padding-0 m-m9-padding-height-1">
    <div>
    <h3 class="m9-margin-0 m9-padding-height-1 m9-font-family-5 text-align-left m-m9-padding-height-0 m-m9-padding-bottom-1 m-text-align-left m9-f-xxxlarge" data-m9-m-style="font-size:1.5em;" style="letter-spacing: 1px; font-family: &quot;Jeju Gothic&quot;;">조직 구조 파악</h3>
    
    <div class="m9-f-size" style="color: rgb(0, 0, 0); letter-spacing: 0px; font-family: &quot;Jeju Gothic&quot;;">회사에는 다양한 부서와 팀이 존재하죠. 신입사원이 입사 후 팀과 부서를 한눈에&nbsp; 파악하기에 어려움이 있는데요<br class="_mong9" />
    <br class="_mong9" />
    라이브 페이지에서 사내 부서와 팀별 직원들을 파악<span style="color:#000000">할</span> 수 있어 보고 및 공유가 필요한 협업 시에 따로 사원정보를 찾아 볼 필요없이 간편하게 가능하죠!</div>
    </div>
    </div>
    </div>
    </div>
    </div>
    
    <div class="m9-grid-block">
    <div class="m9-grid-1">
    <div class="m9-column-1 _not-copy">
    <div class="float-right m-display-block  m-float-none m-width-100  m-text-align-center" data-m9-m-style="max-width:100%" style="max-width:50%"><img alt="" alt_no="1" class="m9-fullimg m9-img-size-2" data-m9-m-style="width:auto" src="https://rewardy.co.kr/editors/ckeditor/plugins/mong9-editor/source/img/example/example109.jpg" /></div>
    
    <div class="display-block overflow-hidden m9-padding-height-1 m9-padding-width-3 m-overflow-visible m-m9-padding-0 m-m9-padding-height-1">
    <div>
    <h3 class="m9-margin-0 m9-padding-height-1 m9-font-family-5 text-align-left m-m9-padding-height-0 m-m9-padding-bottom-1 m-text-align-left m9-f-xxlarge" data-m9-m-style="font-size:1.5em;" style="letter-spacing: 1px; font-family: Gulim;">소개 챌린지</h3>
    
    <div class="m9-f-small" style="color:#b0b0b0;letter-spacing: 0">새로 입사한 신입사원을 회사구성원이 다같이 있는 자리에서 소개하는 것은 쉽지 않습니다.<br class="_mong9" />
    <br class="_mong9" />
    연차나 외근으로 자리에 없거나<br class="_mong9" />
    업무교류는 많지만 멀리 떨어져 있어 보기 힘든 부서는 어렵죠.<br class="_mong9" />
    <br class="_mong9" />
    간단한 소개 챌린지로 새로 입사한 사원을 소개하고 다같이 환영하는건 어떨까요?</div>
    </div>
    </div>
    </div>
    </div>
    </div>
    ';

    require_once('https://localhost:8080/editors/ckeditor/plugins/mong9-editor/includes/functions/content-filter.php');
	$html = Mong9_Html_Convert($html);
?>

<script src="https://code.jquery.com/jquery.min.js"></script>
<script>

if (!M9_SET) { var M9_SET = {}; }
M9_SET['mong9_editor_use'] = '1'; // Mong9 에디터 사용
M9_SET['mong9_url'] = 'https://rewardy.co.kr/editors/ckeditor/plugins/mong9-editor/'; // 몽9 에디터 주소
// M9_SET['mong9_url'] = 'http://localhost:8090/editors/ckeditor/plugins/mong9-editor/'; // 몽9 에디터 주소
</script>


<script src="https://rewardy.co.kr/editors/ckeditor/plugins/mong9-editor/source/js/mong9.js"></script>

<link rel="stylesheet" href="https://rewardy.co.kr/editors/ckeditor/plugins/mong9-editor/source/etc/bootstrap-icons/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://rewardy.co.kr/editors/ckeditor/plugins/mong9-editor/source/css/mong9-base.css">
<link rel="stylesheet" href="https://rewardy.co.kr/editors/ckeditor/plugins/mong9-editor/source/css/mong9.css">
<link rel="stylesheet" href="https://rewardy.co.kr/editors/ckeditor/plugins/mong9-editor/source/css/mong9-m.css" media="all and (max-width: 768px)">
<link rel="stylesheet" href="https://rewardy.co.kr/editors/ckeditor/plugins/mong9-editor/source/css/mong9-e.css" media="all and (max-width: 576px)">



</head>
    <textarea name="content" id="content"></textarea>
    <div><?=$html?></div>
</body>

<!-- 에디터 페이지에 삽입할 소스 -->

<script src="https://rewardy.co.kr/editors/ckeditor/ckeditor.js"></script>
<script src="https://rewardy.co.kr/editors/ckeditor/plugins/mong9-editor/source/js/mong9-connect.js"></script>
<script type="text/javascript">
//<![CDATA[
    CKEDITOR.replace('content');

    // 1. 언어 선택(필요시)
	//CKEDITOR.config.language = 'ko';

    // 3. Mong9Editor : Allow HTML, etc
    CKEDITOR.config.allowedContent = {
        $1: {
            // Use the ability to specify elements as an object.
            elements: CKEDITOR.dtd,
            attributes: true,
            styles: true,
            classes: true
        }
    };

    // 4. Mong9Editor : Disallow <style>, <script>, on events(onload,onmouse..)
    CKEDITOR.config.disallowedContent = 'style;script; *[on*]';

    // 5. 몽9 에디터 버튼 삽입
</script>

</html>