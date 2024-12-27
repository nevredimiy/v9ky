const swipersTable = document.querySelector('.swiper-table');

if (swipersTable) {
  new Swiper('.swiper-table', {
    // enabled: Boolean(window.innerWidth < 1260),
    slidesPerView: 'auto',
    spaceBetween: 5,
    speed: 1000,
    allowTouchMove: true,
    scrollbar: {
      el: '.swiper-scrollbar-table',
      hide:false
    },
    breakpoint: {
      840: {
        scroollbar: {
          hide: true
        }
      }
    }
  
  });
} 