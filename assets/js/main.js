function always_show($fields)
{
    return true;
}
jQuery(document).ready(function($){
    $('.filter').on('click', function(){
        get_response('get_all_projects', $(this).data('slug'))
    });
    /* Demo purposes only */
$(".hover").mouseleave(
  function() {
    $(this).removeClass("hover");
  }
);
});
function get_response(action, values){
    $.ajax({
        url: ajax_obj.ajaxurl,
        method: 'POST',
        data:{
            action: action,
            value: values,
            wpnonce: ajax_obj.wpnonce,
        },
        success: (res) => $('#result').html(res.data)
    });
}