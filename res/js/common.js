$(function () {

  // 添加用户
  $('.addUser').bind('click', function () {
    $('#addUser').show();
  });

  // 关闭弹窗
  $('.p-box .close').bind('click', function () {
    var $currentPopbox = $(this).parents('.p-box');
    $currentPopbox.find('form')[0].reset();
    $currentPopbox.hide();
  });

  // 复选框UI
  $('.check-box input').bind('click', function(){
    var $checkState = $(this).prop('checked');
    var $currentCheckbox = $(this).parents('.check-box');
    if($checkState){
      $currentCheckbox.addClass('active');
    }else{
      $currentCheckbox.removeClass('active');
    }
  })
});