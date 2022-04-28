$(document).ready(function () {
  // jQuery methods go here...
  console.log("hello");
});

$("#form").on("change", "#name", function (e) {
  e.preventDefault();

  // let id = $(this).data("pid");

  // console.log($(this).val());
  let id = $(this).val();
  console.log(id);
  $.ajax({
    url: "/admin/one/getdatabyid",
    method: "POST",

    data: {
      id: id,
    },
      // dataType: "JSON"
  }).done(function (data) {
    let newdata = JSON.parse(data);

    console.log(newdata.variation);
    // console.log((data))

    populate(newdata.variation);
  });
});

function populate(data) {
  let variationstr = '<select id="variname"  name="variname">';
  // console.log(data.length);
  for (let i = 0; i < data.length; i++) {
    let vari = "";
    for (const key in data[i]) {
      vari += key + "-" + data[i][key];
    }

    variationstr += '<option value="' + vari + '">' + vari + "</option>";
  }
  console.log(variationstr);

  $("#dwop2").html(variationstr + "</select>");
}

// $("#table").on("change", ".status", function (e) {

//   // console.log("hello world")

//   console.log($(".status option:selected").text());
//   // console.log($(".status option:selected").val());
//   // console.log($(this).closest("#orderId").val());

// })

function fun() {
  console.log("hello fun");
}
$("body").on("change", "#date", function (e) {
  console.log("hello fun");
  // $('select#dropDownId option:selected').val();
 if($("#date").val()==="custom"){
   
  console.log("custom")
 let optn= '<input type="date" name="stdate" placeholder="start date">\
  <input type="date" name="endate" placeholder="end date">'

  $("#filterForm").append(optn);
 }


  
});
