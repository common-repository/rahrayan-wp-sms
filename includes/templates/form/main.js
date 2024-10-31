jQuery(document).ready(function ($) {
    function content_check(id, focus) {
        if ($(id).val() != '' && $(id).val() != null) {
            $(id).removeClass('rahrayan_invalid');
            return true;
        } else {
            $(id).addClass('rahrayan_invalid');
            if (focus)
                $(id).focus();
            return false;
        }
    }

    function replaceNumbers(string) {
        string = string.toString();

        var arabicNumbers = ["١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩", "٠"],
            persianNumbers = ["۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "۰"],
            englishNumbers = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"];

        for (var i = 0, numbersLen = arabicNumbers.length; i < numbersLen; i++) {
            string = string.replace(new RegExp(arabicNumbers[i], "g"), englishNumbers[i]);
        }

        for (var i = 0, numbersLen = persianNumbers.length; i < numbersLen; i++) {
            string = string.replace(new RegExp(persianNumbers[i], "g"), englishNumbers[i]);
        }
        return string;
    }


    function tell_check(id, focus) {
        var re = /^09([0-9]{9})$/;
        if (re.test(replaceNumbers($(id).val()))) {
            $(id).removeClass('rahrayan_invalid');
            return true;
        } else {
            $(id).addClass('rahrayan_invalid');
            if (focus)
                $(id).focus();
            return false;
        }
    }

    $('.rahrayan_fname,.rahrayan_lname,.rahrayan_code').keyup(function () {
        content_check($(this), false);
    });
    $('.rahrayan_fname,.rahrayan_lname,.rahrayan_gender,.rahrayan_group,.rahrayan_code').focusout(function () {
        content_check($(this), false);
    });
    $('.rahrayan_gender,.rahrayan_group').change(function () {
        content_check($(this), false);
    });
    $('.rahrayan_mobile').keyup(function () {
        tell_check($(this), false);
    });
    $('.rahrayan_mobile').focusout(function () {
        tell_check($(this), false);
    });
    var mpcode = 0;
    $('#rahrayan').submit(function () {
        if (!$(this).hasClass('working')) {
            if (mpcode == 0) {
                content_check($('.rahrayan_fname'), false);
                content_check($('.rahrayan_lname'), false);
                tell_check($('.rahrayan_mobile'), false);
                content_check($('.rahrayan_gender'), false);
                content_check($('.rahrayan_group'), false);
                if (content_check($('.rahrayan_fname'), true)) {
                    if (content_check($('.rahrayan_lname'), true)) {
                        if (tell_check($('.rahrayan_mobile'), true)) {
                            if (content_check($('.rahrayan_gender'), true)) {
                                if (content_check($('.rahrayan_group'), true)) {
                                    $(this).addClass('working');
                                    $('#submit_rahrayan').val('لطفا صبر کنید...');
                                    $.ajax({
                                        type: "POST",
                                        url: "",
                                        data: {
                                            name: $('.rahrayan_fname').val(),
                                            lname: $('.rahrayan_lname').val(),
                                            mobile: replaceNumbers($('.rahrayan_mobile').val()),
                                            gender: $('.rahrayan_gender').val(),
                                            group: $('.rahrayan_group').val(),
                                            mpadn: 'true'
                                        }
                                    }).done(function (data) {
                                        var ok = false;
                                        if (data == 'remove/code') {
                                            mpcode = 1;
                                            $('#mpcode').empty().append('کد تایید که به موبایل شما ارسال شده است را جهت تایید لغو عضویت وارد کنید.');
                                            $('.mpnew').slideUp();
                                            $('.mpcode').slideDown();
                                            $('#rahrayan').removeClass('working');
                                            ok = true;
                                        }
                                        if (data == 'add/code') {
                                            mpcode = 1;
                                            $('#mpcode').empty().append('کد تایید که به موبایل شما ارسال شده است را جهت تایید عضویت وارد کنید.');
                                            $('.mpnew').slideUp();
                                            $('.mpcode').slideDown();
                                            $('#rahrayan').removeClass('working');
                                            ok = true;
                                        }
                                        if (data == 'added') {
                                            mpreset();
                                            $('#submit_rahrayan').val('عضویت شما انجام شد. با تشکر');
                                            $('#rahrayan').removeClass('working');
                                            ok = true;
                                        }
                                        if (data == 'deleted') {
                                            mpreset();
                                            $('#submit_rahrayan').val('لغو عضویت شما انجام شد. با تشکر');
                                            $('#rahrayan').removeClass('working');
                                            ok = true;
                                        }
                                        if (data == '' || !ok) {
                                            $('#submit_rahrayan').val('اشتراک یا لغو اشتراک');
                                            alert('مشکلی پیش آمد. مجددا تلاش کنید.' + ' (' + data + ')');
                                            $('#rahrayan').removeClass('working');
                                        }
                                    }).fail(function (data) {
                                        $('#submit_rahrayan').val('اشتراک یا لغو اشتراک');
                                        alert('مشکلی پیش آمد. مجددا تلاش کنید.');
                                        alert('مشکلی پیش آمد. مجددا تلاش کنید.' + ' (' + data + ')');
                                    });
                                }
                            }
                        }
                    }
                }
            } else {
                if (mpcode != 2) {
                    if (content_check($('.rahrayan_code'), true)) {
                        $(this).addClass('working');
                        $('#submitcmp').val('لطفا صبر کنید...');
                        $.ajax({
                            type: "POST",
                            url: "",
                            data: {
                                name: $('.rahrayan_fname').val(),
                                lname: $('.rahrayan_lname').val(),
                                mobile: replaceNumbers($('.rahrayan_mobile').val()),
                                gender: $('.rahrayan_gender').val(),
                                group: $('.rahrayan_group').val(),
                                code: $('.rahrayan_code').val(),
                                mpadn: 'true'
                            }
                        }).done(function (data) {
                            var ok = false;
                            if (data == 'added') {
                                $('.rahrayan_code').val('');
                                $('#submitcmp').val('عضویت شما انجام شد. با تشکر »بازگشت');
                                mpcode = 2;
                                $('#rahrayan').removeClass('working');
                                ok = true;
                            }
                            if (data == 'deleted') {
                                $('.rahrayan_code').val('');
                                $('#submitcmp').val('لغو عضویت شما انجام شد. »بازگشت');
                                mpcode = 2;
                                $('#rahrayan').removeClass('working');
                                ok = true;
                            }
                            if (data == 'incorrect') {
                                $('.rahrayan_code').val('');
                                content_check($('.rahrayan_code'), true);
                                $('#submitcmp').val('کد وارد شده معتبر نیست.');
                                $('#rahrayan').removeClass('working');
                                ok = true;
                            }
                            if (data == '' || !ok) {
                                $('#submitcmp').val('ارسال کد تایید');
                                $('#rahrayan').removeClass('working');
                                alert('مشکلی پیش آمد. مجددا تلاش کنید.' + ' (' + data + ')');
                            }
                        }).fail(function (data) {
                            $('#submitcmp').val('ارسال کد تایید');
                            $('#rahrayan').removeClass('working');
                            alert('مشکلی پیش آمد. مجددا تلاش کنید.' + ' (' + data + ')');
                        });
                    }
                }
            }
        }
        return false;
    });
    $('#submitcmp').click(function () {
        if (mpcode == 2) {
            mpreset();
            $('.mpnew').slideDown();
            $('.mpcode').slideUp();
        }
    })

    function mpreset() {
        mpcode = 0;
        $('.rahrayan_fname').val('');
        $('.rahrayan_lname').val('');
        $('.rahrayan_mobile').val('');
        $('.rahrayan_gender').val('');
        $('.rahrayan_group').val('');
        $('#submit_rahrayan').val('اشتراک یا لغو اشتراک');
        $('#submitcmp').val('ارسال کد تایید');
    }

});
