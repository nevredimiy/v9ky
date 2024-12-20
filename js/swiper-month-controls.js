const swiperMonthControls = document.querySelectorAll('.swiper-month-controls');

const GAP_MONTH_CONTROLS = 5;

if (swiperMonthControls && swiperMonthControls.length > 0){

  swiperMonthControls.forEach(el => {
    const slidesMatchZoneControls = el.querySelectorAll('.swiper-slide-month-controls');
    let totalWidthMatchZoneControls = 0;

    for (let i = 0; i < slidesMatchZoneControls.length; i++) {
      const slide = slidesMatchZoneControls[i];

      totalWidthMatchZoneControls += slide.offsetWidth;
    }

    totalWidthMatchZoneControls += slidesMatchZoneControls.length * GAP_MONTH_CONTROLS;


    const swiperWrapper = el.querySelector('.swiper-wrapper-month-controls');


    if (swiperWrapper.offsetWidth >= totalWidthMatchZoneControls) {
      // console.log('style');
      swiperWrapper.style.display = 'flex';
      swiperWrapper.style.justifyContent = 'center';
      swiperWrapper.style.alignItems = 'center';
      swiperWrapper.style.gap = `${GAP_MONTH_CONTROLS}px`;
      return;
    }

    return new Swiper(el, {
      enabled: Boolean(swiperWrapper.clientWidth < totalWidthMatchZoneControls),
      enabled: true,
      slidesPerView: 'auto',
      spaceBetween: GAP_MONTH_CONTROLS,
      speed: 400,
      scrollbar: {
        el: '.swiper-scrollbar',
        dragSize: 70,
      },
    });
  });

}