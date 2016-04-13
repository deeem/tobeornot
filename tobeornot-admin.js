;(function($){
    $(function(){

        function getFormattedDate( date ) {
            var day = date.getDate();
            if (day < 10) day = '0' + day;
            var month = date.getMonth() + 1;
            if (month < 10) month = '0' + month;
            var year = date.getFullYear();
            var hour = date.getHours();
            if (hour < 10) hour = '0' + hour;
            var minute = date.getMinutes();
            if (minute < 10) minute = '0' + minute;

            return day + '.' + month + '.' + year + ' ' + hour + ':' + minute;
        }

        $('#tobeornot_date').datetimepicker({
            dateFormat: 'dd.mm.yy',
            timeFormat: 'HH:mm',
            // Current date plus 10 hours
            defaultValue: getFormattedDate( new Date( Date.now() + 1000 * 60 * 60 * 10 ) )
        });

    });
})(jQuery);
