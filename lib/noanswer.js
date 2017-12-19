define(function () {
    $(function () {
        // Vars
        var url = 'https://smartsam.ru/hyper_plus/ajax/noanswer.php',
            button = $('<a class="btn btn-default btn-sm" title="Поле нет ответа"><span class="glyphicon glyphicon-ban-circle"></span></a>'),
            submitButton = $('<button class="btn btn-xs btn-success" type="submit">Сохранить</button>'),
            list = $('<ul class="dropdown-menu dropdown-menu-right" role="menu"></ul>'),
            wrapper = button.wrap('<div class="dropdown"></div>').parent(),
            text = $('<p class="text-muted"></p>'),
            scriptData = {};

        list.append('<li><form style="padding: 0 5px" onsubmit="return false">' +
            '<textarea name="text" class="form-control" rows="5" style="width: 300px;margin-bottom: 5px" placeholder="Текст если нет ответа"></textarea>' +
            '</form></li>').find('form').append(submitButton);
        wrapper.css({
            right: 88,
            position: 'absolute'
        }).appendTo('.container .js_scripts_list_box_wrap .script-buttons').append(list);

        // Events
        $(document).on('click', '.unexpected_answer', insertText);

        button.click(function () {
            wrapper.toggleClass('open');
            if (scriptData['id'] && wrapper.hasClass('open')) {
                $.ajax({
                    url: url,
                    dataType: 'json',
                    method: 'get',
                    cache: true,
                    data: {id: scriptData.id},
                    success: function (edit) {
                        list.find('textarea').val(edit.result);
                    }
                });
            }
        });
        submitButton.click(function () {
            if (scriptData['id']) {
                $.post(url, {id: scriptData['id'], text: this.form.text.value});
                wrapper.removeClass('open');
            }
        });

        $(document).ajaxSuccess(function (ajax, response) {
            if (response.responseJSON) {
                var result = response.responseJSON.response;
                if (result && result['id']) scriptData = result;
            }
        });

        // Listener

        // Functions
        function insertText() {
            var id = scriptData.id || hyperscript.scriptContainer.val();
            $.ajax({
                url: url,
                dataType: 'json',
                method: 'get',
                cache: true,
                data: {id: id},
                success: function (edit) {
                    if (edit.result) {
                        text.text(edit.result);
                        $('.unexpected_answer_box .text-muted').remove();
                        $('.unexpected_answer_box').append(text);
                    }
                }
            });
        }
    });
});