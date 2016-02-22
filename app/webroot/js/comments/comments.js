var comments_sorted_newest_to_oldest = true;

$(document).ready(function() {

  $(".comment-created-ago").timeago();
  
  $('#comment-form-errors').hide();

	// override submit event
	// post data to comments controller
	$('#comment_add_form').submit(function(event){        
        event.preventDefault();
  });

  $('#comment-form-textarea').keyup(function (e) {
      if (e.keyCode == 13 && $('#comment-form-textarea').is(':focus')) {
          // Do something
          postComment($('#comment-form-textarea').val(), false);
      }
  });

  $('#discussion_comment_submit_btn').click(function(){
    $('#comment-form-textarea').val();
    postComment($('#comment-form-textarea').val(), false);
  });

  $('#comment-sort-oldest').click(function(){
    if(!comments_sorted_newest_to_oldest)
    {
      var comments_container = $('#comments-container');
      var comments = comments_container.children('.comment');
      comments_container.append(comments.get().reverse());
      comments_sorted_newest_to_oldest = true;
      $('#comment-sort-oldest').hide();
      $('#comment-sort-newest').show();
    }
  });

  $('#comment-sort-newest').click(function(){
    if(comments_sorted_newest_to_oldest)
    {
      var comments_container = $('#comments-container');
      var comments = comments_container.children('.comment');
      comments_container.append(comments.get().reverse());
      comments_sorted_newest_to_oldest = false;
      $('#comment-sort-newest').hide();
      $('#comment-sort-oldest').show();
    }
  });

  $(document).on('click', '.comment-reply-btn', function(e){
    var reply_box = $(e.target).next();
    $(reply_box).toggle();
  });

  $(document).on('click', '.comment-reply-submit', function(e){
    var in_reply_to_comment_id = $(this).data('inReplyToCommentId');
    var comment = $(e.target).parent().find('input[type=text]').val();
    postComment(comment, in_reply_to_comment_id);
  });

  $(document).on('submit', '.reply-form', function(e){
    e.preventDefault();
  });

  $(document).on('keyup', '.reply-form-input', function(e){
    if (e.keyCode == 13) {
          var in_reply_to_comment_id = $(this).data('inReplyToCommentId');
          var comment = $(e.target).parent().find('input[type=text]').val();
          postComment(comment, in_reply_to_comment_id);
    }
  });

  $(document).on('click', '.fa-arrow-up', function(e){
    var comment_id = $(this).data('commentid');
    postCommentVote(comment_id, 1);
  });

  $(document).on('click', '.fa-arrow-down', function(e){
    var comment_id = $(this).data('commentid');
    postCommentVote(comment_id, 0);
  });

  add_comment_delete_handler();
});

function postCommentVote(comment_id, up) {
  var data = {'data[CommentVote][comment_id]': comment_id, 'data[CommentVote][up]': up};

  $.ajax({
       type: "POST",
       url: base_url + 'comments/vote',
       data: data,
       success: function(data)
       {
           var data = $.parseJSON(data);
           // console.log(data); // show response from the php script.
           // // show errors if there are any
           // // add new comment to comments block
           // // 
           if(data.meta.success)
           {
              var comment_score = parseInt($('#comment-score-' + data.response.comment_id).text());

              if(data.response.up == 1)
              {
                $('#vote-down-' + data.response.comment_id).removeClass('comment-vote-negative');
                $('#vote-up-' + data.response.comment_id).addClass('comment-vote-positive');
                comment_score++;
              }
              else
              {
                $('#vote-up-' + data.response.comment_id).removeClass('comment-vote-positive');
                $('#vote-down-' + data.response.comment_id).addClass('comment-vote-negative');
                comment_score--;
              }

              $('#comment-score-' + data.response.comment_id).text(comment_score);
           }
           else
           {
              $('#comment-form-errors-list').empty();
              $('#comment-form-errors').show();
              for(var key in data.errors)
              {
                $('#comment-form-errors-list').append('<li>' + data.errors[key] + '</li>');
              }
           }
       },
       error: function(error)
       {
          console.log(error);
          $('#comment-form-errors-list').empty();
          $('#comment-form-errors').show();
          $('#comment-form-errors-list').append('<li>You must be logged in to perform this action</li>');
       }
  });
}

function postComment (text, in_reply_to_comment_id) {
  
  var data = {'data[Comment][text]': text, 'data[Comment][parent_id]': parent_id, 'data[Comment][comment_type_id]': comment_type_id, 'data[Comment][in_reply_to_comment_id]': in_reply_to_comment_id};

  // console.log(data);

  $.ajax({
       type: "POST",
       url: base_url + 'comments/add',
       data: data,
       success: function(data)
       {
           var data = $.parseJSON(data);
           // console.log(data); // show response from the php script.
           // show errors if there are any
           // add new comment to comments block
           // 
           if(data.meta.success)
           {
              // if the comment is a reply then append to parent
              if(data.meta.is_reply)
              {
                $('#nocomments-row').hide();
                $('#comment-form-errors').hide();
                $(data.response).hide().appendTo('#comment-replies-' + data.meta.parent_id).slideDown(300);
                $('#reply-form-textarea-' + data.meta.parent_id).val("");
                $(".comment-created-ago").timeago();
              }
              else
              {
                $('#nocomments-row').hide();
                $('#comment-form-errors').hide();
                $(data.response).hide().prependTo('#comments-container').slideDown(300);
                $(".comment-created-ago").timeago();
                $('#comment-form-textarea').val('');
              }
              
              add_comment_delete_handler();
           }
           else
           {
              $('#comment-form-errors-list').empty();
              $('#comment-form-errors').show();
              for(var key in data.errors)
              {
                $('#comment-form-errors-list').append('<li>' + data.errors[key] + '</li>');
              }
           }
       },
       error: function(error)
       {
          console.log(error);
          $('#comment-form-errors-list').empty();
          $('#comment-form-errors').show();
          $('#comment-form-errors-list').append('<li>You must be logged in to perform this action</li>');
       }
  });

  // get latest comments on timer loop needs adding

  add_comment_delete_handler();

}

function add_comment_delete_handler() {

  $('.comment-delete-button').unbind('click');

  $('.comment-delete-button').click(function() {


    if (window.confirm("Delete comment?")) {

      var comment_id = $(this).attr('data-commentid');

      $.ajax({
           type: "POST",
           url: base_url + 'comments/delete/' + comment_id,
           success: function(data)
           {
               var data = $.parseJSON(data);

               // console.log(data);

               if(data.meta.success)
               {
                  // remove comment
                  $('#comment-' + comment_id).slideUp(300);
               }
               else
               {
               }
           },
           error: function(error)
           {
              console.log(error);
           }
      });

    }

  });

}