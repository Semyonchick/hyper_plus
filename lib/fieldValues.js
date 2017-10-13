define(function () {
    $(function () {
        // Vars
        var client = {name: ''}, map = {
            name: 'Имя клиента',
            email: 'email'
        };

        // Events
        $('.search__box-input').on('change autocompleteselect', function () {
            var text = $('.search__box-input').val().match(/^(?:.+:\s)?(.+?)(?:\s\(\s?(.+)\s?\))?$/);
            if (text) {
                client.name = text[1];
                client.email = text[2];
                insertClientValues();
            }
        });

        // Listener
        $(document).ajaxSuccess(insertClientValues);

        // Functions
        function insertClientValues() {
            for (var key in client)
                if (client[key] && map[key])
                    $('[placeholder="' + map[key] + '"]').val(client[key]);
        }
    });
});