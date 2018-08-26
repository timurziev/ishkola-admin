(function ($) {

    //log chart
    var logActivity = {
        options: {
            date: {
                startDate: moment().subtract(6, 'days'),
                endDate: moment(),
                minDate: moment().subtract(60, 'days'),
                maxDate: moment(),
                dateLimit: {
                    days: 60
                },
                showDropdowns: true,
                showWeekNumbers: true,
                timePicker: false,
                timePickerIncrement: 1,
                timePicker12Hour: true,
                ranges: {
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                opens: 'left',
                buttonClasses: ['btn btn-default'],
                applyClass: 'btn-small btn-primary',
                cancelClass: 'btn-small',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                locale: {
                    applyLabel: 'Submit',
                    cancelLabel: 'Clear',
                    fromLabel: 'From',
                    toLabel: 'To',
                    customRangeLabel: 'Custom',
                    daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                    monthNames: [
                        'January',
                        'February',
                        'March',
                        'April',
                        'May',
                        'June',
                        'July',
                        'August',
                        'September',
                        'October',
                        'November',
                        'December'
                    ],
                    firstDay: 1
                }
            },
            chart: {
                series: {
                    lines: {
                        show: false,
                        fill: true
                    },
                    splines: {
                        show: true,
                        tension: 0.4,
                        lineWidth: 1,
                        fill: 0.4
                    },
                    points: {
                        radius: 0,
                        show: true
                    },
                    shadowSize: 2
                },
                grid: {
                    verticalLines: true,
                    hoverable: true,
                    clickable: true,
                    tickColor: "#d5d5d5",
                    borderWidth: 1,
                    color: '#fff'
                },
                colors: ["#B71C1C", "#D32F2F", '#F44336', '#FF5722', '#FF9100', '#4CAF50', '#1976D2', '#90CAF9'],
                xaxis: {
                    tickColor: "rgba(51, 51, 51, 0.06)",
                    mode: "time",
                    tickSize: [1, "day"],
                    //tickLength: 10,
                    axisLabel: "Date",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: 'Verdana, Arial',
                    axisLabelPadding: 10
                },
                yaxis: {
                    ticks: 8,
                    tickColor: "rgba(51, 51, 51, 0.06)",
                },
                tooltip: false
            }
        },
        gteChartData: function ($el, start, end) {
            var self = this;

            $.ajax({
                url: 'admin/dashboard/log-chart',
                data: {start: start, end: end},
                success: function (response) {
                    var data = {};
                    var progress = {all: 0};

                    $.each(response, function (k, v) {
                        data[k] = [];
                        progress[k] = 0;
                        $.each(v, function (date, value) {
                            data[k].push([new Date(date).getTime(), value]);
                            progress.all += value;
                            progress[k] += value;
                        });
                    });

                    $.plot($el,
                        [data.emergency, data.alert, data.critical, data.error, data.warning, data.notice, data.info, data.debug],
                        self.options.chart);


                    $.each(progress, function (k, v) {
                        var $progress = $('.progress-bar.log-' + k);
                        if ($progress.length) {
                            $progress.attr('data-transitiongoal', 100 / progress.all * v).progressbar();
                        }
                    });
                }
            });
        },
        init: function ($el) {
            var self = this;

            $el = $($el);

            var $dateEl = $el.find('.date_piker');
            var $chartEl = $el.find('.chart');

            // $dateEl.daterangepicker(this.options.date, function (start, end) {
            //     $dateEl.find('.date_piker_label').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            // });
            //
            // $dateEl.on('apply.daterangepicker', function (ev, picker) {
            //     self.gteChartData($chartEl, picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
            // });

            self.gteChartData($chartEl, this.options.date.startDate.format('YYYY-MM-DD'), this.options.date.endDate.format('YYYY-MM-DD'));
        }
    };

    logActivity.init($('#log_activity'));


    var registrationUsage = {
        _defaults: {
            type: 'doughnut',
            tooltipFillColor: "rgba(51, 51, 51, 0.55)",
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        "#3498DB",
                        "#3498DB",
                        "#9B59B6",
                        "#E74C3C",
                    ],
                    hoverBackgroundColor: [
                        "#36CAAB",
                        "#49A9EA",
                        "#B370CF",
                        "#E95E4F",
                    ]
                }]
            },
            options: {
                legend: false,
                responsive: false
            }
        },
        init: function ($el) {
            var self = this;
            $el = $($el);

            $.ajax({
                url: 'admin/dashboard/registration-chart',
                success: function (response) {
                    $.each($el.find('.tile_label'), function () {
                        self._defaults.data.labels.push($(this).text());
                    });

                    var count = 0;

                    $.each(response, function () {
                        count += parseInt(this);
                    });

                    $('#registration_usage_from').text(100 / count * parseInt(response.registration_form));
                    $('#registration_usage_google').text(100 / count * parseInt(response.google));
                    $('#registration_usage_facebook').text(100 / count * parseInt(response.facebook));
                    $('#registration_usage_twitter').text(100 / count * parseInt(response.twitter));

                    self._defaults.data.datasets[0].data = [response.registration_form, response.google, response.facebook, response.twitter];

                    new Chart($el.find('.canvasChart'), self._defaults);
                }
            });
        }
    };

    registrationUsage.init($('#registration_usage'));

    //jcarousel
    var jcarousel = $('.jcarousel').jcarousel();

    $('.jcarousel-control-prev')
        .on('jcarouselcontrol:active', function () {
            $(this).removeClass('inactive');
        })
        .on('jcarouselcontrol:inactive', function () {
            $(this).addClass('inactive');
        })
        .jcarouselControl({
            target: '-=1'
        });

    $('.jcarousel-control-next')
        .on('jcarouselcontrol:active', function () {
            $(this).removeClass('inactive');
        })
        .on('jcarouselcontrol:inactive', function () {
            $(this).addClass('inactive');
        })
        .jcarouselControl({
            target: '+=1'
        });

    var url = 'https://photolancer.zone/api/photos',
        param = {
            page: 1,
            perPage: 10,
            sort: [{by: 'rating', type: 'desc'}]
        };

    $.ajax({
        url: url + '?' + $.param(param),
        method: 'GET',
        success: function (response) {
            var html = '<ul>';

            var href = 'https://photolancer.zone/photos';

            $.each(response, function () {
                html += '<li><a href="' + href + '/' + this.slug + '/detail" target="_blank"><img src="' + this.thumbnails.file.photos.small + '" alt="' + this.name + '"/></a></li>';
            });

            html += '</ul>';

            // Append items
            jcarousel
                .html(html);

            // Reload carousel
            jcarousel
                .jcarousel('reload');
        }
    });


    function setPicker(inst) {
        $(inst).datetimepicker({
            locale: 'ru'
        });
    }

    $(function () {
        setPicker('.datetime');
    });

    var count = 1;

    $("#date-time").click(function () {
        event.preventDefault();
        count++;
        var cloned = $('#parent-input').clone();
        if (cloned.length === 0) {
            cloned = $('#edit-parent-input').clone();
            cloned.find('input').attr('name', 'edit-datetimes[]');
        }
        cloned.find('input').attr('id', 'reserve-' + count);
        cloned.insertAfter("#reserve").appendTo(".controls");
        cloned.find('#date-time').remove();
        cloned.find('#remove-date').css('margin-left', '8px').removeClass('hide').attr('class', count);
        var num = cloned.find('#remove-date').attr('class');
        cloned.find('#remove-date').click(function (e) {
            e.preventDefault();
            $('#cloned-input-' + num).remove();
        });
        setPicker('#reserve-' + count);
        cloned.attr('id', 'cloned-input-' + count);
    });

    $(".remove").click(function () {
        event.preventDefault();
        $(this).parent('.datetimes').remove();
    });

    $(function () {
        setCalendar('.calendar');
    });

    $(function () {
        setCalendar('.user-calendar');
    });

    function setCalendar(inst) {
        $(inst).daterangepicker({
            singleDatePicker: true,
            // ranges: {
            //     "Предыдущий месяц": [moment().startOf('month'), moment().endOf('month')],
            //     "Следующий месяц": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            // },
            locale: {
                format: 'DD.MM.YYYY',
                separator: " - ",
                applyLabel: "Подтвердить",
                cancelLabel: "Отмена",
                fromLabel: "От",
                toLabel: "До",
                customRangeLabel: "По умолчанию",
                weekLabel: "Н",
                daysOfWeek: [
                    "Вс",
                    "Пн",
                    "Вт",
                    "Ср",
                    "Чт",
                    "Пт",
                    "Сб"
                ],
                monthNames: [
                    "Январь",
                    "Февраль",
                    "Март",
                    "Апрель",
                    "Май",
                    "Июнь",
                    "Июль",
                    "Август",
                    "Сентябрь",
                    "Октябрь",
                    "Ноябрь",
                    "Декабрь"
                ],
                "firstDay": 1
            }
        }, function (start) {
            if (inst === '.user-calendar') {
                window.location.href = '/user/lessons_table?date=' + start.format('YYYY-MM-DD');
            } else {
                window.location.href = '/admin/lessons_table?date=' + start.format('YYYY-MM-DD');
            }
        });
    }

    $('#calendar').fullCalendar({
        locale: 'ru',
        header: {
            left: 'prev next',
            center: 'title',
            right : 'today month agendaWeek agendaDay',
        },
        views: {
            agendaDay: {
                type: 'agenda',
                duration: { days: 1 },
            },
            agendaWeek: {
                type: 'agenda',
                duration: { days: 7 },
            }
        },
        buttonText: {
            today:    'сегодня',
            month:    'месяц',
            week:     'неделя',
            day:      'день',
        },
        events: function(start, end, timezone, callback) {
            $('#submit').on('submit', function (e) {
                e.preventDefault();
                $('#calendar').fullCalendar('removeEvents');
                var teacher = $('#teacher').val();
                $.ajax({
                    type: 'POST',
                    url: 'http://ishkola-admin:8080/admin/lessons_table/?teacher=' + teacher,
                    dataType: 'json',
                    success: function(data) {
                        callback(data);

                        var events = [];
                        events['events'] = data;

                        $('#calendar').fullCalendar('updateEvents', events);
                    }
                });
            });

            $.ajax({
                url: 'http://ishkola-admin:8080/admin/lessons_table',
                dataType: 'json',
                success: function(data) {
                    callback(data);
                },
            });
        },
        eventClick: function(event) {
            if (event.url) {
                window.open(event.url);
                return false;
            }
        },
        timeFormat: 'H(:mm)',
    });



    $('#user-calendar').fullCalendar({
        locale: 'ru',
        header: {
            left: 'prev next',
            center: 'title',
            right : 'today month agendaWeek agendaDay',
        },
        views: {
            agendaDay: {
                type: 'agenda',
                duration: { days: 1 },
            },
            agendaWeek: {
                type: 'agenda',
                duration: { days: 7 },
            }
        },
        buttonText: {
            today:    'сегодня',
            month:    'месяц',
            week:     'неделя',
            day:      'день',
        },
        events: function(start, end, timezone, callback) {
            $.ajax({
                url: 'http://ishkola-admin:8080/user/lessons_table',
                dataType: 'json',
                success: function(data) {
                    callback(data);
                },
            });
        },
        eventClick: function(event) {
            if (event.url) {
                window.open(event.url);
                return false;
            }
        },
        timeFormat: 'H(:mm)',
    });


    $('a[id^="id"]').click(function(e) {
        e.preventDefault();

        var id = $(this).attr('class');
        var items = $('.items-' + id);
        var records = $('.resources-' + id);
        var resources = $('.resources-' + id);

        items.toggle();

        if (!items.children('.records').data("populated-rec") && items.is(":visible")) {
            ajaxRec();
        }

        if (!items.children('.resources').data("populated-res") && items.is(":visible")) {
            ajaxRes();
        }

        function ajaxRec() {
            $.ajax({
                url: 'http://ishkola-admin:8080/user/records/' + id,
                dataType: "json",
                beforeSend: function () {
                    items.children('.records').children('img').show();
                },
                complete: function () {
                    items.children('.records').children('img').hide();
                    if (!items.children('.records').data("populated-rec")) {
                        items.children('.records').append('<p>Нет записи занятия</p>').data("populated-rec", true);
                    }
                },
                success: function (data) {
                    $.each(data, function (key, value) {
                        items.children('.records').append('<p><a href="' + value.viewLink + '" target="_blank"><b>Посмотреть запись</b></a></p>').data("populated-rec", true);
                    });
                },
            });
        }

        function ajaxRes() {
            $.ajax({
                url: 'http://ishkola-admin:8080/user/resources/' + id,
                dataType: "json",
                beforeSend: function () {
                    items.children('.resources').children('img').show();
                },
                complete: function () {
                    items.children('.resources').children('img').hide();
                    if (!items.children('.resources').data("populated-res")) {
                        items.children('.resources').append('<p>Нет материалов</p>').data("populated-res", true);
                    }
                },
                success: function (data) {
                    $.each(data, function (key, value) {
                        if (!value.access_token) {
                            items.children('.resources').append('<p><a href="' + value.link + '&usrsesid=' + data.session.access_token + '">' + value.fileidname + '</a></p>').data("populated-res", true);
                        }
                    });
                },
            });
        }
    });

    $(".icheckbox_flat-green").click(function () {
        $(this).toggleClass('checked');

        window.onbeforeunload = confirmExit;
        function confirmExit(){
            return false;
        }
    });

    $(".btn-default").click(function () {
        window.onbeforeunload = null;
    });

    $(".schedule_comment").click(function () {
        $(this).children(".comment-pen").hide();
        $(this).children().removeClass('hidden');
    });

    $(".schedule").click(function () {
        $(".schedule-loader").show();
    });


})(jQuery);

