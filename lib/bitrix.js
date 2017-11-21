define(function () {
    var change = false;
    // var bxPlace = document.getElementById('b24placement');
    // if (bxPlace && bxPlace.dataset['options']) {
    //     var params = JSON.parse(bxPlace.dataset.options), scriptList = document.getElementById('js_script_list');
    // }
    // localStorage.current_script = params.script;
    $(function () {
        var info = BX24.placement.info(), scriptList = document.getElementById('js_script_list');
        $('#b24placement').data('type', 'CALL_CARD');
        if (info.options.script) {
            $(document).ajaxComplete(function () {
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
                    }, 10);
                }
            });
        }
    });


});