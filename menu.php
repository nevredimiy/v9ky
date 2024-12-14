<? 
  if (!defined('READFILE')){exit('Wrong way to file');}

  //название турнира из символьного названия
  $rec_ru = $db->Execute("select * from v9ky_turnir where name='".$tournament."'");
  if ($rec_ru->fields[ru]) {$turnir_ru = $rec_ru->fields[ru];} else $turnir_ru = '';
  if ($rec_ru->fields[id]) {$turnir = $rec_ru->fields[id];} else $turnir = 0;

  if (file_exists("reglamenty/".$gorod_en->fields[name_en].".pdf")) {
          $reglamfile = $gorod_en->fields[name_en].".pdf";
        } else {
          $reglamfile = "Ukraine.pdf";
        }

?>
<?php if (!isset($_GET['foo'])): ?>

	<div class="menu-line">
		<div class="main-menu box-widht" id="menu">
			<ul>
				<li><a href="<?=$site_url?>">головна</a></li>
				<li><a href="<?=$url;?>/teams/">команди</a></li>
				<li><a href="<?=$url;?>/calendar">календар</a></li>
				<li><a href="<?=$url;?>/transfer">трансфери</a></li>
				<? if($nenadacss<>1){ ?>				
					<li><a href="<?=$reglament_path?><?=$reglamfile?>">регламент</a></li>
				<? } ?>			
				<li><a href="<?=$url?>/arhiv">архів</a></li>
				<? if ($gorod_en->fields[id]==2){ ?> 
					<li><a href="<?=$url?>/live">live</a></li> 
				<?}?>
				<li><a href="<?=$url?>/onlines">online</a></li>
				<!--   <li><a href="<?=$url?>/rating">Рейтинги</a></li>-->
				<li><a href="<?=$url?>/contacts">контакти</a></li>
			</ul>
		</div>
	</div>
	<div class="mobile-menu">
		<div class="icon-close">
			<img src="https://v9ky.in.ua/img/close-btn.png">
	    </div>
		<ul>
			<li><a href="<?=$site_url?>">головна</a></li>
			<li><a href="<?=$url;?>/teams/">команди</a></li>
			<li><a href="<?=$url;?>/calendar">календар</a></li>
			<li><a href="<?=$url;?>/transfer">трансфери</a></li>
			<? if($nenadacss<>1){ ?>				
				<li><a href="<?=$reglament_path?><?=$reglamfile?>">регламент</a></li>
			<? } ?>		
			<li><a href="<?=$url?>/arhiv">архів</a></li>
			<li><a href="<?=$url?>/live">live</a></li>
			<li><a href="<?=$url?>/onlines">online</a></li>
			<!--   <li><a href="<?=$url?>/rating">Рейтинги</a></li>-->
			<li><a href="<?=$url?>/contacts/">контакти</a></li>
		</ul>
	</div>
	<div class="icon-menu">
		<i class="fa fa-bars" aria-hidden="true"></i>
	</div>

<?php else: ?>
	
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
<?php endif ?>