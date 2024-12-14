const swiperDisqualification = document.querySelector('.swiper-disqualification');

if(swiperDisqualification) {
  const slidesDisqualification = swiperDisqualification.querySelectorAll('.swiper-slide-disqualification');
  let totalWidthDisqualification = 0;


  
  for (let i = 0; i < slidesDisqualification.length; i++) {
    const slide = slidesDisqualification[i];
  
    // console.log(i, slide.offsetWidth, slide);
    
    totalWidthDisqualification += slide.offsetWidth;
  }
  
  totalWidthDisqualification += slidesDisqualification.length * 10;

  new Swiper(swiperDisqualification, {
    enabled: Boolean(window.innerWidth < totalWidthDisqualification),
    slidesPerView: 'auto',
    spaceBetween: 10,
    speed: 400,
    scrollbar: {
      el: '.swiper-scrollbar',
      dragSize: 70,
    },
  });

  if (window.innerWidth >= totalWidthDisqualification) {
    // console.log('style');
    const swiperWrapper = swiperDisqualification.querySelector('.swiper-wrapper');
    swiperDisqualification.style.display = 'flex';
    swiperDisqualification.style.justifyContent = 'center';
    swiperDisqualification.style.alignItems = 'center';
    swiperDisqualification.style.gap = '10px';
    swiperWrapper.style.maxWidth = '1440px';
    swiperWrapper.style.width = 'fit-content';
    // swiperWrapper.style.backgroundColor = 'red';
  }
  
}




