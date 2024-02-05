$(function () {

  //로그인버튼
  $(document).on("click", ".demo_loginbtn", function(){

    // console.log($(this).parent().attr("value"));
    var demoId = $(this).parent().attr("value");

    var fdata = new FormData();
    fdata.append("mode", "demo_login");
    fdata.append("id", demoId);
    // fdata.append("pwd", '0000');

    // if ($("input[name='chk_login']").is(":checked") == true) {
    //   fdata.append("chk_login", true);
    // }

    // if ($("input[name='id_save']").is(":checked") == true) {
    //   fdata.append("id_save", true);
    // }

    // if ($("#loginbtn").hasClass("ra_btn_login_mo")) {
    //   fdata.append("mobile", "1");
    // }

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/login_ok.php",
      success: function (data) {
        console.log(data);
        if (data == "use_ok" || data == "m_use_ok") {
          if (window.location.pathname == "/myinfo/index.php") {
            location.replace("/team/");
          } else {
            // location.reload();
            location.replace("/team/index.php");
          }
          return false;
        } else if (data == "ad_ok" || data == "m_ad_ok") {
          //location.replace("/admin/pay.php");
          //location.href = "/admin/member_list.php";
          if (
            window.location.pathname == "/myinfo/index.php" ||
            window.location.pathname == "/index.php"
          ) {
            location.replace("/team/");
          } else {
            location.reload();
          }
          return false;
        }
      },
    });
});

});