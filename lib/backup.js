define(function () {
    $(function () {
        var button = $('<a class="btn btn-default btn-sm"><span class="glyphicon glyphicon-floppy-open"></span></a>');
        button.css({
            right:47,
            position:'absolute'
        });
        $('.container .js_scripts_list_box_wrap .script-buttons').append(button);
        $(document).ajaxSuccess(function (data, response) {
            var result = response.responseJSON.response;
            if (result && result['id']) {
                $.ajax({
                    url: 'https://smartsam.ru/hyper_plus/ajax/backup.php',
                    dataType: 'json',
                    method: 'post',
                    data: result,
                    success: function(data){
                        console.log(data);
                    }
                })
            }
        });
    });
});