<? 
  if (isset($params['match'])) {
    $baza = '/match/'.$params['match'];
  }
  include_once "turnir_head.php";
?>

<div class="content flex-container">
		
<?

  $recordteam = $db->Execute("select * from v9ky_team where turnir='".$turnir."' order by name asc");



while (!$recordteam->EOF) {?>

                        <a class="flex-blocks" href="<?=$site_url?>/<?=$tournament?>/team/id/<?=$recordteam->fields[id]?>">
				<span><?=$recordteam->fields[name]?></span>
				<img src="<?=$team_logo_path?><?=$recordteam->fields[pict]?>" width=165 alt="">
			</a>

<?  $recordteam->MoveNext();
}?>

			
		</div>
	</div>
</article>

<?
  include_once "footer.php";
?>