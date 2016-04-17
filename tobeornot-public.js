;(function($){
    $(function(){

        // если есть voter,значит можно голосовать
        if ( $('.tobeornot_voter').length ) {

            // если не голосовал
            if (!localStorage.getItem('tobeornot-' + ajax_params.post_id)) {
                $('.tobeornot_voter').addClass('active');
                $('#tobeornot-true').on( 'click', function(){
                    $('.tobeornot_voter--button_true').addClass('selected');
                    $('.tobeornot_voter--button_false').removeClass('selected');
                    localStorage.setItem('tobeornot-' + ajax_params.post_id, true);
                });
                $('#tobeornot-false').on( 'click', function(){
                    $('.tobeornot_voter--button_false').addClass('selected');
                    $('.tobeornot_voter--button_true').removeClass('selected');
                    localStorage.setItem('tobeornot-' + ajax_params.post_id, false);
                });
            } else {    //если голосовал, заблокировать
                $('.tobeornot_voter').addClass('inactive');
                if ( localStorage.getItem('tobeornot-' +ajax_params.post_id) === 'true') {
                    $('.tobeornot_voter--button_true').addClass('selected');
                } else {
                    $('.tobeornot_voter--button_false').addClass('selected');
                }
            }
        }

    });
})(jQuery);
