$(document).ready(function() {
    add_vote_handler();
});


function add_vote_handler() {
    $('.up-vote-button').unbind('click');

    $('.down-vote-button').unbind('click');

    $('.up-vote-button').click(function() {

        var contribution_id = $(this).attr("data-contribution");
        var data = {'data[vote_up]': 1, 'data[contribution_id]': contribution_id};

        $.ajax({
             type: "POST",
             url: '../../../votes/vote',
             data: data,
             success: function(data) {
                 var data = $.parseJSON(data);
                 console.log(data);
                 if (data.meta.success) {
                    $('#vote-count-' + contribution_id).html(data.response);

                    if($('#vote-down-' + contribution_id).css('color') == "rgb(255, 0, 0)")
                    {
                        $('#vote-down-' + contribution_id).css('color', '#CCC');
                        $('#vote-up-' + contribution_id).css('color', '#CCC');
                    }
                    else
                    {
                        $('#vote-up-' + contribution_id).css('color', '#5BCA5B');
                        $('#vote-down-' + contribution_id).css('color', '#CCC');
                    }

                 } else {
                    alert(data.errors);
                 }
             },
             error: function(error) {
                console.log(error);
              }
        });
    });

    $('.down-vote-button').click(function() {

        var contribution_id = $(this).attr("data-contribution");
        var data = {'data[vote_up]': 0, 'data[contribution_id]': contribution_id};
        
        $.ajax({
             type: "POST",
             url: '../../../votes/vote',
             data: data,
             success: function(data) {
                 var data = $.parseJSON(data);
                 console.log(data);
                 if (data.meta.success) {
                    $('#vote-count-' + contribution_id).html(data.response);

                    if($('#vote-up-' + contribution_id).css('color') == "rgb(70, 136, 71)")
                    {
                        $('#vote-down-' + contribution_id).css('color', '#CCC');
                        $('#vote-up-' + contribution_id).css('color', '#CCC');
                    }
                    else
                    {
                        $('#vote-up-' + contribution_id).css('color', '#CCC');
                        $('#vote-down-' + contribution_id).css('color', '#FF0000');
                    }

                 } else {
                    alert(data.errors);
                 }
             },
             error: function(error) {
                console.log(error);
              }
        });
    });
}