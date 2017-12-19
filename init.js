requirejs.config({
    baseUrl: 'https://smartsam.ru/hyper_plus/lib',
    urlArgs: "v=20171201-1657"
});

requirejs(['datepicker', 'backup', 'crmfields', 'noanswer', 'bitrix']);

console.log('hyper_plus init');