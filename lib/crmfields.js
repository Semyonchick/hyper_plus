define(function () {
    $(function () {
        // Vars
        var client = {NAME: ''}, map = {
            NAME: 'Имя клиента',
            LAST_NAME: 'Фамилия клиента',
            EMAIL: 'email'
        };

        // Events
        $('.search__box-input').on('input change autocompleteselect', function () {
            var text = $('.search__box-input').val().match(/^(?:.+:\s)?([^\s]+\s)?(.+?)(?:\s\(\s?(.+)\s?\))?$/);
            if (!client.ID && text) {
                client.NAME = text[2];
                client.LAST_NAME = text[1];
                client.EMAIL = text[3];
                insertClientValues();
            }
        }).on('autocompleteselect', insertClientValues);
        $('.js_view_box .set_state[data-state]').click(insertClientValues);

        // Listener
        $(document).ajaxSuccess(insertClientValues);

        // Functions
        function insertClientValues() {
            for (var key in client)
                if (client[key] && map[key]) {
                    var input = $('[placeholder="' + map[key] + '"]')
                    if (!input.val()) input.val(client[key]);
                }
        }

        function loadData() {
            if (hyperscript.entityType && hyperscript.entityId)
                BX24.callMethod('crm.' + hyperscript.entityType.toLowerCase() + '.get',
                    {'ID': hyperscript.entityId},
                    function (result) {
                        if (result.answer.result) {
                            console.log(result.answer.result);
                            for (var i in result.answer.result) {
                                var value = result.answer.result[i];
                                if (!value || !value.length) value = '';
                                else if (value[0].VALUE) value = value[0].VALUE;
                                client[i] = value;
                            }
                            insertClientValues();
                        }
                    }
                );
        }

        var interval = setInterval(function () {
            if (hyperscript.entityType && hyperscript.entityId) {
                clearInterval(interval);
                loadData();
            }
        }, 100);
    });
});