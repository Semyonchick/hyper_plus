define(function () {
    $(function () {
        // Vars
        var client = {name: ''}, map = {
            name: 'Имя клиента',
            surname: 'Фамилия клиента',
            email: 'email'
        };

        // Events
        $('.search__box-input').on('change autocompleteselect', function () {
            var text = $('.search__box-input').val().match(/^(?:.+:\s)?([^\s]+\s)?(.+?)(?:\s\(\s?(.+)\s?\))?$/);
            if (text) {
                client.name = text[2];
                client.surname = text[1];
                client.email = text[3];
                insertClientValues();
            }
        });
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
    });
});