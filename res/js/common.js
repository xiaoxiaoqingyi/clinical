$(function () {

  // 添加用户
  $('.addUser').bind('click', function () {
    $('#addUser').show();
  });

  // 关闭弹窗
  $('.p-box .close').bind('click', function () {
    var $currentPopbox = $(this).parents('.p-box');
    var $currentPopboxForm = $currentPopbox.find('form')
    if($currentPopboxForm.length){
        $currentPopboxForm[0].reset();
    }
    $currentPopbox.hide();
  });

  // 复选框UI
  $('.check-box input').bind('click', function(){
    var checkedLen = $('.check-box input:checked').length;
    if(!checkedLen){
      $('.table-do-batch').addClass('readonly');
    }else{
        $('.table-do-batch').removeClass('readonly');
    }
    var $checkState = $(this).prop('checked');
    var $currentCheckbox = $(this).parents('.check-box');
    if($checkState){
      $currentCheckbox.addClass('active');
    }else{
      $currentCheckbox.removeClass('active');
    }
  })
});