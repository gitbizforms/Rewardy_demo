;
(function($) {


    var pageName = "";
    var tempPageName = window.location.href;
    var strPageName = tempPageName.split("/");
    pageName = strPageName[strPageName.length - 1].split("?")[0];

    //if (pageName == "list.php") {
    //    var dateformat = "yyyy. mm. dd";
    //} else {
    var dateformat = "yyyy-mm-dd";
    //}


    $.fn.datepicker.language['kr'] = {
        days: ["일", "월", "화", "수", "목", "금", "토"],
        daysShort: ["일", "월", "화", "수", "목", "금", "토"],
        daysMin: ["일", "월", "화", "수", "목", "금", "토"],
        months: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
        monthsShort: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
        today: '오늘로 입력',
        clear: 'Clear',
        dateFormat: dateformat,
        timeFormat: 'hh:ii aa',
        firstDay: 0
    };
})(jQuery);