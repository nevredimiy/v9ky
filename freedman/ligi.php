<!-- TODO: freedman -->
<section class="leagues">
	<div class="leagues__container">
		<div class="leagues__wrap">
			<?
			$recligi = $db->CacheExecute(1000, "select * from v9ky_turnir where city=(select city from v9ky_turnir where id='".$turnir."') and season=(select season from v9ky_turnir where id='".$turnir."') ORDER BY priority ASC");

			$i=0;
			while (!$recligi->EOF) {
			$i=$i+1;
			$strligi = ''; $strligi2 = '';
			$recligi->fields['id']==$turnir ? $strligi = " leagues__item-active" : $strligi = "";
			?>

			<div class="">
				<div class="leagues__item<?=$strligi?>">
				<a href="<?=$site_url?>/<?=$recligi->fields['name']?>/city_news">
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
	</div>
</section>