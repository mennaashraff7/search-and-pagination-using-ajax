$(document).ready(function(){
    //on click on the page number
    fetch(1);
    $(document).on('click', '.page-link', function(){
      var page = $(this).data('page_number');
      var query = $('#input_field').val();
      fetch(page, query);
    });
    // on writing in the search field
    $('#input_field').keyup(function(){
      var query = $('#input_field').val();
      fetch(1, query);
    });

//fetch data function (ajax request)
    function fetch(page, query = '')
    {
      $.ajax({
        url:"process.php",
        method:"POST",
        data:{page:page, query:query},
        success:function(data)
        {
          $('#live_search').html(data);
        }
      });
    }
  });






