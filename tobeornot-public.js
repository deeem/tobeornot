;(function($){
    $(function(){

        // если есть voter,значит можно голосовать
        if ( $('.tobeornot_voter').length ) {

            // если не голосовал
            if (!localStorage.getItem('tobeornot-' + tobeornot_ajax.post_id)) {

                $('.tobeornot_voter').addClass('active');

                $('#tobeornot-true').on( 'click', function() {
                    $.post( tobeornot_ajax.ajax_url,
                        {
                            action: 'tobeornot-ajax-public',
                            nonce: tobeornot_ajax.nonce,
                            post_id: tobeornot_ajax.post_id,
                            result: 'true'
                        },
                        function( response ) {
                            $('.tobeornot_voter--button_true').addClass('selected');
                            $('.tobeornot_voter--button_false').removeClass('selected');
                            localStorage.setItem('tobeornot-' + tobeornot_ajax.post_id, true);
                        }
                    );
                });
                $('#tobeornot-false').on( 'click', function() {
                    $.post( tobeornot_ajax.ajax_url,
                        {
                            action: 'tobeornot-ajax-public',
                            nonce: tobeornot_ajax.nonce,
                            post_id: tobeornot_ajax.post_id,
                            result: 'false'
                        },
                        function( response ) {
                            $('.tobeornot_voter--button_false').addClass('selected');
                            $('.tobeornot_voter--button_true').removeClass('selected');
                            localStorage.setItem('tobeornot-' + tobeornot_ajax.post_id, false);
                        }
                    );
                });
            } else {    //если голосовал, заблокировать
                $('.tobeornot_voter').addClass('inactive');
                if ( localStorage.getItem('tobeornot-' + tobeornot_ajax.post_id) === 'true') {
                    $('.tobeornot_voter--button_true').addClass('selected');
                } else {
                    $('.tobeornot_voter--button_false').addClass('selected');
                }
            }
        }

    });
})(jQuery);
