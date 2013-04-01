(function() {
    $.ajaxSetup({
        async: true,
        dataType: "json",
        timeout: 5000,
        url: "./services/marker/"
    });
})();