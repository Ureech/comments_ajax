var icms = icms || {};

icms.newcomm = (function ($) {

    var _this = this;
    this.is_moderation_list = false;
    this.urls;
    this.target;

    this.init = function (urls, target) {
        this.urls = urls;
        this.target = target;
    };

    this.onDocumentReady = function () {
        $('body').on('click', function (e) {
            if (e.target.classList.contains('users_likers')) {
                return;
            } else {
                $('.users_likers').removeClass('on');
            }
        });
    }


    this.Html = function (cc, subject, controller, id, user_id, number = 0) {

        $.post('/comments/ajax_html', { number: number, subject: subject, controller: controller, id: id, user_id: user_id }, function (result) {

            if (result == null || typeof (result) === 'undefined' || result.error) {
                _this.error(result.message);
                return;
            }
            var data = JSON.parse(result);
            $('#ajax_html').html(data.html);
        });

    }

    this.otherList = function (cc, id, user_id) {
        var subject = this.target.subject;
        var controller = this.target.controller;
        var max = this.target.count;

        $('.other_list').hide(); $('.other_list2').hide();

        var a = '<a class="btn btn-info other_list2"  href="javascript:;" onclick="icms.newcomm.otherList(' + cc + ',' + id + ',' + user_id + ')">Ещё - ' + cc + '</a>';

        number = $('#comments_list').attr('data-page');

        $.post('/comments/ajax_html', {
            number: number,
            subject: subject,
            controller: controller,
            id: id,
            user_id: user_id
        }, function (result) {

            if (result == null || typeof (result) === 'undefined' || result.error) {
                _this.error(result.message);
                return;
            }
            var data = JSON.parse(result);
            var n = Number(cc) + Number(data.num);
            var s = document.querySelector('#comments_list');
            var b = $(data.html)[4];
            $('#ajax_html').append(b);
            var nb = $(s).attr('data-page', n);
            if ((number + cc) > max) {
                $('#ajax_html').after(a);
            }
        });
    }


    this.addLike = function (id, user_id, author, user_name) {

        user_id = user_id ? user_id : '';
        user_name = user_name ? user_name : '';
        var count = $('.icon#' + id + ' .count_likes');
        count_num = count[0].innerHTML;

        $.post(this.urls.like, { id: id, user_id: user_id, author: author, user_name: user_name}, function (result) {
            error = (JSON.parse(result)).error;
            if (error) {
                (JSON.parse(result)).message
                return;
            }

            var new_count = count_num * 1 + 1;
            count[0].innerHTML = new_count;
            $('.icon#' + id).addClass('red');
        });

    }

    this.allList = function (id) {

        $.post(this.urls.like, { id: id, all: 1 }, function (result) {

            if (result == null || typeof (result) === 'undefined' || result.error) {
                _this.error(result.message);
                return;
            }

            $('.users_likers').html(result);
            $('.users_likers').addClass('on');

        });

    }

    this.setRating = function (units, id, ctype, user_id) {


        $.post(this.urls.score, { id: id, units: units, ctype: ctype, user_id: user_id }, function (result) {

            if (result == null || typeof (result) === 'undefined' || result.error) {
                _this.error(result.message);
                return;
            }
            location.reload();
        });

    }

    return this;

}).call(icms.actions || {}, jQuery);

/*
function otherList(cc, subject, controller, id, user_id, number = 3) {
        
    number = $('#comments_list').attr('data-page');

    $.post('/comments/ajax_html', {
        number: number,
        subject: subject,
        controller: controller,
        id: id,
        user_id: user_id
    }, function(result) {

        if (result == null || typeof(result) === 'undefined' || result.error) {
            _this.error(result.message);
            return;
        }
        var data = JSON.parse(result);
        var n = Number(cc) + Number(data.num);
        var s = document.querySelector('#comments_list');
        var b = $(data.html)[4];
        $('#ajax_html').append(b);
        var nb = $(s).attr('data-page', n);
    });
}
*/