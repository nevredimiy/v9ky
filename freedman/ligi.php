<?php 

// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);

//
// Все данные страницы основывауються от переменной turnir, которая береться из tournament. 
// А tournament береться с адресной строки - это название турнира, написаное латиницей
//

// Если переменная tournament пустая, то заполняем ее. 
if (!$tournament) { 
	//Когда tournament пустая это ознчает, что в адресной строке нет названия тура.
	$queryGetTurnirsOfLastSeason = $db->Execute("SELECT * FROM `v9ky_turnir` WHERE `seasons` = (SELECT id FROM `v9ky_seasons` ORDER BY id DESC LIMIT 1)");
	
	$turnirsOfLastSeason = [];
	while(!$queryGetTurnirsOfLastSeason->EOF){
		
		$turnirsOfLastSeason [] = $queryGetTurnirsOfLastSeason->fields;
		
		$queryGetTurnirsOfLastSeason->MoveNext();
	}

	// Идентификатор турнира. Берем первый турнир из массива всех турниров в сезоне
	$turnir = $turnirsOfLastSeason[0]['id'];
	// Название турнира латинице. Берем там же где и turnir
	$tournament = $turnirsOfLastSeason[0]['name'];

} else {
	
	// Получаем переменную turnir исходя от tournament. Все данные страницы основуються от переменной turnir
	$turnirsOfSeason = $db->Execute("select * from v9ky_turnir where name='".$tournament."'");
	$turnirsOfSeason->fields['id'] ? $turnir = $turnirsOfSeason->fields['id'] : $turnir = 0;

}

?>
<!-- TODO: freedman -->
<section class="leagues">
	<div class="leagues__container swiper-leagues">
		<div class="leagues__wrap swiper-wrapper">
			<?
			$recligi = $db->CacheExecute(1000, "select * from v9ky_turnir where city=(select city from v9ky_turnir where id='".$turnir."') and season=(select season from v9ky_turnir where id='".$turnir."') ORDER BY priority ASC");

			$i=0;
			while (!$recligi->EOF) {
			$i=$i+1;
			$strligi = ''; $strligi2 = '';
			$recligi->fields['id']==$turnir ? $strligi = " leagues__item-active" : $strligi = "";
			?>

			<div class="swiper-slide">
				<div class="leagues__item<?=$strligi?>">
				<a href="<?=$site_url?>/<?=$recligi->fields['name']?>?foo=foo">
					<span class="leagues__item-title"><?=$recligi->fields['ru']?></span>
					<div class="leagues__item-location">
						<img src="/css/components/leagues/assets/images/location-icon.svg" alt="location">
						<span>X-park</span>              
					</div>
				</a>
				</div>
			</div>
		
			<?  $recligi->MoveNext();}?>
		</div>
		<div class="swiper-scrollbar-leagues"></div>
	</div>
</section>