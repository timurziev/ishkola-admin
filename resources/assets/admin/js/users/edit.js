(function ($) {
    $('.select2').select2();

    $('.select-user').select2({
        placeholder: "Введите email или имя пользователя...",
    });

    var inputsTel = document.querySelectorAll('input[type="tel"]');

    Inputmask({
        "mask": "+7 (999) 999-99-99",
        showMaskOnHover: false
    }).mask(inputsTel);

})(jQuery);