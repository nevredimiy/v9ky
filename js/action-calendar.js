
$(document).ready(function () {

    $('.calendar-of-matches__grid-container').on('click', '[data-tur]', function (e) {
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
                url: "../freedman/actionCalendar.php",
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
});
