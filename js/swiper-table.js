const swipersTable = document.querySelector('.swiper-table');

if (swipersTable) {
  new Swiper('.swiper-table', {
    enabled: Boolean(window.innerWidth < 1260),
    slidesPerView: 'auto',
    spaceBetween: 5,
    speed: 1000,
    allowTouchMove: true,
    scrollbar: {
      el: '.swiper-scrollbar',
      dragSize: 70,
    },
  
    on: {
      slideChange: function () {
        console.log('slide changed');
      },
    },
  });
} 