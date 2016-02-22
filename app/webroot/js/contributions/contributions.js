var tiles = new Array();

$(document).ready(function() {

  var contribution_data = {};

  // Add click handler to contribution button
  $('#contribution-add-submit').click(function(event) {

    event.preventDefault();

    switch($('#app_type_design_task_element').val()) {

      case 'name':
        document.getElementById("temp").innerHTML = $('#contribution-input').val();
        contribution_data.name = $("#temp").text();
        break;
        
      case 'logo':
        contribution_data.url = $('#contribution-input').val();
        break;
        
      case 'colour':
        contribution_data.primary = $('#primary-picker span').attr('title');
        contribution_data.pin = $('#pin-picker span').attr('title');
        contribution_data.star = $('#star-picker span').attr('title');
        break;
        
      case 'options':
        document.getElementById("temp").innerHTML = $('#contribution-input').val();
        contribution_data.name = $("#temp").text();
        break;
      }

      post_contribution(contribution_data);
      
      return false;
  });

  add_contribution_delete_handler();

  $('.tile').on({
      mouseenter: function () {
          $(this).find('.contribution-flag').show();
          $(this).find('.contribution-flag').css('cursor', 'pointer');
      },
      mouseleave: function () {
          $(this).find('.contribution-flag').hide();
      }
  });

  $('.contribution-flag').click(function(){
      var contribution_id = $(this).attr('data-contributionid');
      $('#contribution-flag-modal').modal('show');
      $('#contribution-flag-modal').attr('data-contribution_id', contribution_id);
      // console.log($(this).attr('data-contributionid'));
  });

  $('#contribution-flag-modal-submit').click(function(){
      if (!$("input[name='contribution_report_radio']:checked").val()) {
        $('#contribution-flag-modal-errors').show();
        return false;
      }
      else {

        var data = {};
        data.id = $('#contribution-flag-modal').attr('data-contribution_id');
        data.report_type_id = $("input[name='contribution_report_radio']:checked").val();

        $.ajax({
           type: "POST",
           url: base_url + 'contributions/report',
           data: data,
           success: function(data)
           {
               var data = $.parseJSON(data);
               console.log(data);
               if (data.meta.success)
               {

               }
               else
               {

               }

               $('#contribution-flag-modal').modal('hide');
           },
           error: function(error)
           {
              $('#contribution-form-errors-list').append('<li>There was a problem, please try again later</li>');
           }
        });

      }
  });
});

// Post contribution to endpoint
function post_contribution(contribution_data)
{
    var data = {'data[Contribution][movement_design_task_id]': $('#movement_design_task_id').val(), 'data[Contribution][contribution_type_id]': $('#contribution_type_id').val(), 'data[Contribution][data]': JSON.stringify(contribution_data)};

    if ($('#app_type_design_task_element').val() != 'logo') {

      $.ajax({
           type: "POST",
           url: base_url + 'contributions/add',
           data: data,
           success: function(data)
           {
               var data = $.parseJSON(data);

               if (data.meta.success)
               {
                  $('#contribution-form-errors').hide();
                  var elem = $(data.response);
                  $('#contributions_wrapper').masonry().append(elem).masonry('appended', elem).masonry();
                  $('#contribution-input').val('');
                  add_vote_handler();
                  add_contribution_delete_handler();
                  $('html, body').animate({
                      scrollTop: $(".tile:last-child").offset().top - 100
                  }, 800);
               }
               else
               {
                  $('#contribution-form-errors-list').empty();
                  $('#contribution-form-errors').show();
                  for(var key in data.errors)
                  {
                    $('#contribution-form-errors-list').append('<li>' + data.errors[key] + '</li>');
                  }
               }
           },
           error: function(error)
           {
              $('#contribution-form-errors-list').append('<li>There was a problem, please try again later</li>');
           }
      });
    }
}

function add_contribution_delete_handler() {

  $('.contribution-delete-button').unbind('click');

  $('.contribution-delete-button').click(function() {

    if (window.confirm(lang.designPhase.confirmDelete)) {

      var contribution_id = $(this).attr('data-contributionid');

      $.ajax({
           type: "POST",
           url: base_url + 'contributions/delete/' + contribution_id,
           success: function(data)
           {
               var data = $.parseJSON(data);

               console.log(data);

               if(data.meta.success)
               {
                  // remove contribution
                  $('#contributions_wrapper').masonry('remove', $('#contribution-' + contribution_id)).masonry('layout');
               }
           },
           error: function(error)
           {
              console.log(error);
           }
      });

    }

  });

};