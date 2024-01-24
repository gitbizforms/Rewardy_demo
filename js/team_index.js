$(document).ready(function(){

  $(".ts_sort .btn_sort_on").click(function(){
    $(".ts_sort").addClass("on");
  });
  $(".ts_sort").mouseleave(function(){
    $(".ts_sort").removeClass("on");
  });

  $(document).on("click", ".ts_sort .btn_sort_on", function() {
    $(".ts_sort").addClass("on");
  // Function to attach click event handlers to the buttons inside .ts_sort_in ul li
  $(document).on("click", ".ts_sort_in ul li button", function() {

    var val = $(this).val();
    var teamNo = $(this).parent().find("span").attr("value");
    var now = $(".ts_now").val();
    $(".ts_sort").removeClass("on");
    $(".btn_sort_on").text(val);
    var fdata = new FormData();
    fdata.append("team", val);
    fdata.append("teamNo", teamNo);
    fdata.append("now", now);
    fdata.append("mode", "date_list");

   
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/team_index_process.php",
      success: function (data) {
        console.log(data);
        var html = data;
        $(".ts_in").html(html);

      }
    });
  });
  });

$(document).on("click", ".ts_next", function() {
  var next = $(".ts_now").val();
  var cate = $(".btn_sort_on").text();
  var tsNo = $(".btn_sort_on").val();

  var fdata = new FormData();
  fdata.append("cate", cate);
  fdata.append("next", next);
  fdata.append("ts_no", tsNo);
  fdata.append("mode", "date_list");
  
  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/team_index_process.php",
    success: function (data) {
      console.log(data);
      var html = data;
      $(".ts_in").html(html);

    }
  });
});

  $(document).on("click", ".ts_prev", function(){
    var prev = $(".ts_now").val();
    var cate = $(".btn_sort_on").text();
    var tsNo = $(".btn_sort_on").val();

    var fdata = new FormData();

    fdata.append("cate", cate); 
    fdata.append("prev", prev);
    fdata.append("ts_no", tsNo);
    fdata.append("mode", "date_list");
    $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/team_index_process.php",
        success: function (data) {
            console.log(data);
            var html = data;
            $(".ts_in").html(html);
        }
    });
  });

  $(document).on("click", ".ts_now", function(){
    var tnow = $(".tweek").val();
    var cate = $(".btn_sort_on").text();
    var tsNo = $(".btn_sort_on").val();

    var fdata = new FormData();

    fdata.append("cate", cate); 
    fdata.append("now", tnow);
    fdata.append("ts_no", tsNo);
    fdata.append("mode", "date_list");
    $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/team_index_process.php",
        success: function (data) {
            console.log(data);
            var html = data;
            $(".ts_in").html(html);
        }
    });
  });
});
