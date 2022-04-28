$(document).ready(function () {
  // jQuery methods go here...
  // console.log("hello");

  $("#create").click(function (e) {
    e.preventDefault();
    console.log("hello");
    createAddition();
  });

  $("#createvariation").click(function (e) {
    e.preventDefault();
    console.log("hello");
    createvariation();
  });
});

let c = 0;
function createAddition() {
  str =
    '<div class="addi"><div>  field label <input type="text" name="atname' +
    c +
    '" required="required">\
</div><div> field value <input type="text" name="atvalue' +
    c +
    '" required="required"></div> <button  class="deletebtn">delete</button></div>';

  $("#additional").append(str);
  c++;
  $("#max").val(c);
}

let v = 0;
function createvariation() {
  str1 ='<div><button class="add">+</button><input type="hidden" class="variationNo" value="' + v+'">\
  <div class="m-3">\
 label <input type="text" name="attriname'+v+'0" >\
 value <input type="text" name="attrival'+v+'0" >\
  </div>\
  <input type="hidden" class="attributecont" name="attricount'+v+'" value="0" >\
</div>'
  

  $("#variation").append(str1);
  v++;
  $("#maxvari").val(v);
}

$("#additional").on("click", ".deletebtn", function (e) {
  e.preventDefault();
  // $("#max").val(c);
  $(this).parent().remove();
  // console.log("yyuturt")
  c--;
  //  let val= $("#max").val();
  $("#max").val(c);
  //  console.log(val);
});
//**************************delete variation******************* */
$("#deletevariation").click(function (e) {
  e.preventDefault();
  $("#variation").children().last().remove();
  v--;
  $("#maxvari").val(v);
});

$("#variation").on("click", ".add", function (e) {
  // console.log(e.parent())
  e.preventDefault();
  let val=$(this).parent().find(".attributecont").val()
  let variNo=$(this).parent().find(".variationNo").val()
  console.log(variNo);
  val++;
  $(this).parent().append('<div class="m-3" >\
 label <input type="text" name="attriname'+variNo+''+val+'" >\
 value <input type="text" name="attrival'+variNo+''+val+'" >\
  </div>')

 
  $(this).parent().find(".attributecont").val(val)
});



//****************************************ajax******************************** */

// $("#form").on("change", "#name", function (e) {
  // e.preventDefault();
  // console.log($(this).val());

  // let id = $(this).data("pid");

  // console.log("fdsds");
  // $.ajax({
  //   url:"/one/update",
  //   method:"POST",

  //   data: {
  //     id: id,

  //   },
  //       dataType: "JSON"
  //   }).done(function (data) {
  //       // let newdata = JSON.parse(data);

  //       console.log(data);

  //       populate(data);

  // });
// });

// function getval(e){
//   console.log("erwqe")
// }


