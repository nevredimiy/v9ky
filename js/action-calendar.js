
$(document).ready(function () {

    $('.calendar-of-matches__grid-container').on('click', '[data-tur]:not([data-anons])', function (e) {
        e.preventDefault();

        var newUrl = $(this).attr('href'); // Получаем URL из атрибута href  

        // Обновляем адресную строку  
        window.history.pushState({ path: newUrl }, '', newUrl);

        let tur = $(this).attr('data-tur');
        let turnir = $(this).attr('data-turnir');
        let lastTur = $(this).attr('data-lasttur');

        if (tur) {
            $.ajax({
                type: "post",
                url: "../freedman/actions/actionCalendar.php",
                data: JSON.stringify({ tur: tur, turnir: turnir, lasttur: lastTur }),
                success: function (response) {
                    $(".calendar-of-matches__grid-container").html(response);

                    swipersLeagues = new Swiper(".swiper-month-controls", {
                        slidesPerView: 'auto',
                        spaceBetween: 20,
                        scrollbar: {
                            el: '.swiper-scrollbar',
                            hide: false,
                            draggable: true,
                        },
                    });

                    swipersLeagues = new Swiper(".swiper-matches", {
                        slidesPerView: 'auto',
                        spaceBetween: 20,
                        scrollbar: {
                            el: '.swiper-scrollbar',
                            hide: false,
                            draggable: true,
                        },
                    });

                },
                error: function (xhr, status, error) {
                    console.error('Ошибка AJAX:', error); // Логируем ошибку
                    alert('Ошибка при загрузке данных. Попробуйте позже.');
                }
            });
        }
    });

    $('[data-anons]').each(function () {
        if ($(this).attr('href') !== undefined) {

            
            $('.calendar-of-matches__grid-container').on('click', '[data-tur]', function (e) {
                console.log('i here');
                $('[data-tur]').css({'background': ''});
                $(this).css({'background': 'red'});
                e.preventDefault();
                e.stopPropagation(); // Останавливаем всплытие события


                let matchId = $(this).data('anons');
                let tur = $(this).data('tur');
                let turnir = $(this).data('turnir');

                // Имя параметра и новое значение  
                var paramName = 'anons';       // Замените на нужное имя параметра  

                // Получаем текущий URL  
                var currentUrl = window.location.href;

                var urlObj = new URL(currentUrl);

                // Проверяем наличие параметра  
                if (urlObj.searchParams.has(paramName)) {
                    // Если параметр найден, изменяем его значение  
                    urlObj.searchParams.set(paramName, matchId);
                } else {
                    // Если параметра нет, добавляем его с новым значением  
                    urlObj.searchParams.append(paramName, matchId);
                }

                if (matchId) {
                    $.ajax({
                        type: "post",
                        url: "../freedman/actions/actionAnons.php",
                        data: JSON.stringify({ match_id: matchId, tur: tur, turnir: turnir }),
                        success: function (response) {
                            $('.green-zone').html(response);
                            // console.log(response);

                        }, // Привязываем контекст к функции 
                        error: function (xhr, status, error) {
                            console.error('Ошибка AJAX:', error); // Логируем ошибку
                            alert('Ошибка при загрузке данных. Попробуйте позже.');
                        }
                    });
                }

            });
        }
    });


});