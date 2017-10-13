define(function () {
    $(function () {
        var button = $('<a class="btn btn-default btn-sm" title="Резервные копии"><span class="glyphicon glyphicon-floppy-open"></span></a>'),
            list = $('<ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="z4s41nb233fgrgq"></ul>'),
            wrapper = button.wrap('<div class="dropdown"></div>').parent().css({
            right:47,
            position:'absolute'
            }).appendTo('.container .js_scripts_list_box_wrap .script-buttons').append(list),
            scriptData;

        $('#hs_save_script_btn').on('click', save);

        button.click(function () {
            wrapper.toggleClass('open');
            if (scriptData && scriptData.id && wrapper.hasClass('open')) {
                $.ajax({
                    url: 'https://smartsam.ru/hyper_plus/ajax/backup.php',
                    dataType: 'json',
                    method: 'get',
                    cache: true,
                    data: {id: scriptData.id},
                    success: backupsShow
                })
            }
        });

        $(document).ajaxSuccess(function (ajax, response) {
            if(response.responseJSON){
                var result = response.responseJSON.response;
                if (result && result['id']) scriptData = result;
            }
        });

        function save() {
            if (scriptData) {
                $.ajax({
                    url: 'https://smartsam.ru/hyper_plus/ajax/backup.php',
                    dataType: 'json',
                    method: 'post',
                    data: scriptData,
                    success: backupsShow
                })
            }
        }

        function backupsShow(data){
            list.html('');
            data.backups.forEach(function (value) {
                var li = $('<li>').html('<a target="_blank" href="https://smartsam.ru/hyper_plus/ajax/backup.php?id=' + scriptData.id + '&date=' + value + '">' + value).prependTo(list);
                var restore = $('<a><i class="glyphicon glyphicon-upload"></i></a>').css({
                    padding: '4px 7px',
                    position: 'absolute',
                    right: 0
                }).click(function () {
                    $.ajax({
                        url: 'https://smartsam.ru/hyper_plus/ajax/backup.php',
                        dataType: 'json',
                        method: 'get',
                        data: {id: scriptData.id, restore: value},
                        success: function (data) {
                            backupsShow(data);
                            var request = data.result;
                            request['method'] = 'scripts.update';
                            request['csrf_token'] = $('[data-val]').data('val');
                            $.post('https://hyper-script.ru/ajax', request, function(){
                                location.reload();
                            })
                        }
                    })
                });
                li.prepend(restore)
            });
            if (!data.backups || !data.backups.length) save();

        }
    });
});