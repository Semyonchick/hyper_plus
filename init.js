requirejs.config({
    baseUrl: 'https://smartsam.ru/hyper_plus/lib',
    urlArgs: "v=201724111211"
});

requirejs(['datepicker', 'backup', 'crmfields', 'noanswer', 'bitrix']);

console.log('hyper_plus init');