;(function($){

    var Tobeornot = {
        init: function( settings ) {
            Tobeornot.voter = settings.voter;
            Tobeornot.tobe = settings.tobe;
            Tobeornot.nottobe = settings.nottobe;
            Tobeornot.bindEvents();

            // если голос не записан в localStorage, сделать голосование активным
            if (!localStorage.getItem('tobeornot-' + tobeornot_ajax.post_id)) {
                Tobeornot.voter.trigger('setActive');
            } else {
                Tobeornot.voter.trigger('setSelected', localStorage.getItem('tobeornot-' + tobeornot_ajax.post_id));
            }
        },
        bindEvents: function() {

            Tobeornot.voter.on('setActive', function() {
                Tobeornot.voter.addClass('active');

                // проголосовал true
                Tobeornot.tobe.one( 'click', function() {
                    $.post( tobeornot_ajax.ajax_url,
                        {
                            action: 'tobeornot-ajax-public',
                            nonce: tobeornot_ajax.nonce,
                            post_id: tobeornot_ajax.post_id,
                            result: 'true'
                        },
                        function( response ) {
                            localStorage.setItem('tobeornot-' + tobeornot_ajax.post_id, 'tobe');
                            Tobeornot.nottobe.off();
                            Tobeornot.voter.trigger('setSelected', 'tobe');
                        }
                    );
                });
                // проголосовал false
                Tobeornot.nottobe.one( 'click', function() {
                    $.post( tobeornot_ajax.ajax_url,
                        {
                            action: 'tobeornot-ajax-public',
                            nonce: tobeornot_ajax.nonce,
                            post_id: tobeornot_ajax.post_id,
                            result: 'false'
                        },
                        function( response ) {
                            localStorage.setItem('tobeornot-' + tobeornot_ajax.post_id, 'nottobe');
                            Tobeornot.tobe.off();
                            Tobeornot.voter.trigger('setSelected', 'nottobe');
                        }
                    );
                });

            });

            Tobeornot.voter.on('setSelected', function( event, param ) {
                Tobeornot.voter.removeClass('active');
                if (param == 'tobe') {
                    Tobeornot.tobe.addClass('selected');
                } else {
                    Tobeornot.nottobe.addClass('selected');
                }
            });

        },
    };

    $(function(){

        Tobeornot.init({
            voter: $('.tobeornot_voter'),
            tobe: $('#tobeornot-true'),
            nottobe: $('#tobeornot-false'),
        });

    });
})(jQuery);
