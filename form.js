$(function(){
  $('.postRadio').click(function(){
    if($('.postRadio').is(':checked')){
      $('.exchangeForm').attr('action', 'currPost.php');
      $('.code').removeClass('hide');
      $('.name').addClass('hide');
      $('.rate').removeClass('hide');
      $('.country').addClass('hide');
      console.log('checked');
    }else{
      $('.code').removeClass('hide');
      $('.name').removeClass('hide');
      $('.rate').removeClass('hide');
      $('.country').removeClass('hide');
      console.log('unchecked');
    }
  });

  $('.putRadio').click(function(){
    if($('.putRadio').is(':checked')){
      $('.exchangeForm').attr('action', 'currPut.php');
      $('.code').removeClass('hide');
      $('.name').removeClass('hide');
      $('.rate').removeClass('hide');
      $('.country').removeClass('hide');
      console.log('checked');
    }else{
      $('.code').removeClass('hide');
      $('.name').removeClass('hide');
      $('.rate').removeClass('hide');
      $('.country').removeClass('hide');
    }
  });

  $('.delRadio').click(function(){
    if($('.delRadio').is(':checked')){
      $('.exchangeForm').attr('action', 'currDel.php');
      $('.code').removeClass('hide');
      $('.name').addClass('hide');
      $('.rate').addClass('hide');
      $('.country').addClass('hide');
      console.log('checked');
    }else{
      $('.code').removeClass('hide');
      $('.name').removeClass('hide');
      $('.rate').removeClass('hide');
      $('.country').removeClass('hide');
      console.log('unchecked');
    }
  });

  // $('.exchangeForm').submit(function(e){
  //   e.preventDefault();
  //   var file = $('.exchangeForm').attr('action');
  //   $.get(file, function(data){
  //     $('.responseBox').html(data);
  //   });
  // });
});
