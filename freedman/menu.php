<? 
  if (!defined('READFILE')){exit('Wrong way to file');}

  //название турнира из символьного названия
  $rec_ru = $db->Execute("select * from v9ky_turnir where name='".$tournament."'");
  if ($rec_ru->fields['ru']) {$turnir_ru = $rec_ru->fields['ru'];} else $turnir_ru = '';
  if ($rec_ru->fields['id']) {$turnir = $rec_ru->fields['id'];} else $turnir = 0;

  if (file_exists("reglamenty/".$gorod_en->fields['name_en'].".pdf")) {
          $reglamfile = $gorod_en->fields['name_en'].".pdf";
        } else {
          $reglamfile = "Ukraine.pdf";
        }

?>

<section class="navigation">
  <div class="swiper1 swiper1-nav">
    <div class="swiper1-wrapper swiper-wrapper-nav navigation__wrap">
      <div class="swiper1-slide swiper1-slide-nav navigation__wrap-item">
        <div class="navigation__item"><a href="<?=$site_url?>">головна</a></div>
      </div>
      <div class="swiper1-slide swiper1-slide-nav">
        <div class="swiper-slide navigation__item"><a href="<?=$url;?>/teams/">команди</a></div>
      </div>
      <div class="swiper1-slide swiper1-slide-nav">
        <div class="swiper-slide navigation__item"><a href="<?=$url;?>/calendar">календар</a></div>
      </div>
      <div class="swiper1-slide swiper1-slide-nav">
        <div class="swiper-slide navigation__item"><a href="<?=$url;?>/transfer">трансфери</a></div>
      </div>
      <div class="swiper1-slide swiper1-slide-nav">
        <div class="swiper-slide navigation__item"><a href="<?=$reglament_path?><?=$reglamfile?>">регламент</a></div>
      </div>
      <div class="swiper1-slide swiper1-slide-nav">
        <div class="swiper-slide navigation__item"><a href="<?=$url?>/live">live</a></div>
      </div>
      <div class="swiper1-slide swiper1-slide-nav">
        <div class="navigation__item">
          <a class="broadcast" href="<?=$url?>/onlines">
            <p href="#">online</p>
          </a>
        </div>
      </div>
  <div class="swiper1-slide swiper1-slide-nav">
        <div class="swiper-slide navigation__item"><a href="<?=$url?>/contacts/">контакти</a></div>
      </div>
    </div>
  
    <div class="swiper-bg-scroll"></div>
    <div class="swiper-scrollbar-nav"></div>
  </div>
</section>
