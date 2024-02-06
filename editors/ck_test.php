<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8"/>
<title>CKEditor 4 설치하기</title>
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