$(document).ready(function() {
  // const selectToken1 = document.querySelector('#token_1');
  // const selectToken2 = document.querySelector('#token_2');
  // selectToken1.addEventListener('click', function() {
  //   this.querySelector('.token_select').classList.toggle('open');
  // });
  // selectToken2.addEventListener('click', function() {
  //   this.querySelector('.token_select').classList.toggle('open');
  // });

// tok1
  $('#token_1 .custom-options').delegate('.custom-option', 'click', function(e) {
    const { value } = $(this).data();
    const name = $(this).text();
    const $headerElm = $(this).parent().prev('.select__trigger');

    //clear class .selected
    $(this).parent().find('.selected').removeClass('selected');

    //add name&class to header
    $headerElm.find('span').attr('class', '');
    $headerElm.find('span').text(name).addClass(`custom-option ${$(this).attr('class')}`);

    // add class .selected
    $(this).addClass('selected');
  })
  // tok 2
  $('#token_2 .custom-options').delegate('.custom-option', 'click', function(e) {
    const { value } = $(this).data();
    const name = $(this).text();
    const $headerElm = $(this).parent().prev('.select__trigger');

    //clear class .selected
    $(this).parent().find('.selected').removeClass('selected');

    //add name&class to header
    $headerElm.find('span').attr('class', '');
    $headerElm.find('span').text(name).addClass(`custom-option ${$(this).attr('class')}`);

    // add class .selected
    $(this).addClass('selected');
  })
});