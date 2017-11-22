define(function () {
    var change = false;
    // var bxPlace = document.getElementById('b24placement');
    // if (bxPlace && bxPlace.dataset['options']) {
    //     var params = JSON.parse(bxPlace.dataset.options), scriptList = document.getElementById('js_script_list');
    // }
    // localStorage.current_script = params.script;
    $(function () {
        var bx24Data = $('#b24placement'),
            info = BX24.placement.info() || bx24Data.data(),
            scriptList = document.getElementById('js_script_list');

        bx24Data.data('type', 'CALL_CARD');

        if (info.options && info.options.script) {
            $(document).ajaxSuccess(function () {
                if(scriptList.value && !change)
                    window.hyperscript.scriptContainer.val(info.options.script);
                if (window['hyperscript'] && scriptList.value && !change) {
                    var interval = setInterval(function () {
                        if ($.app.script.view.is_call_in && window.hyperscript.scriptContainer.val() == info.options.script) {
                            clearInterval(interval);
                            setTimeout(function(){
                                change = true;
                            }, 5000);
                            return;
                        }
                        window.hyperscript.ready();
                        setTimeout(function(){
                            window.hyperscript.scriptContainer.change();
                            window.hyperscript.searchInput.change();
                        }, 1000);
                    }, 10);
                }
            }).ajaxSuccess(function (event, response, params) {
                if (params.url === '/api/bitrix/save_log') {
                    console.log(params.data);

                    var f = $('<form>').attr('action', bx24Data.options.back).attr('target', '_parent');
                    f.appendTo(document.body);
                    f.submit();
                }
            });
        }
    });


});