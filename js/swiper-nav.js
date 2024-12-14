const swiperNav = document.querySelector('.swiper-nav');
const slidesWrapNav = swiperNav.querySelector('.swiper-wrapper');
const slidesNav = swiperNav.querySelectorAll('.swiper-slide-nav');

let totalWidthNav = 0;
for (let i = 0; i < slidesNav.length; i++) {
  const slide = slidesNav[i];
  totalWidthNav += slide.offsetWidth;
}

new Swiper(swiperNav, {
  direction: 'horizontal', // Горизонтальная прокрутка
  slidesPerView: 'auto',    // Автоматическое количество видимых слайдов
  spaceBetween: 0,         // Расстояние между слайдами
  freeMode: true,           // Свободный режим, чтобы слайды прокручивались не только по фиксированным размерам
  scrollbar: {
    el: '.swiper-scrollbar-nav',   // Добавляем ползунок
    hide: false,                // Ползунок не скрывается
    draggable: true,
  },
  breakpoints: {
    768: {
      slidesPerView: 'auto',
      scrollbar: {
        hide: true,
        draggable: false
      }
    },
  },
});

