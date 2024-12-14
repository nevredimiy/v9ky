<? 
  include_once "turnir_head.php";


  $cachefile = 'jeka_cashe/assist/cached-'.$tournament.'.html';
$cachetime = 900;
// Обслуживается из файла кеша, если время запроса меньше $cachetime
$golupdate = $db->Execute("select CONVERT_TZ( updatet, @@session.time_zone, '+00:00' ) as updatet1, active from v9ky_turnir where id='".$turnir."'");

if ((file_exists($cachefile) && ((strtotime($golupdate->fields[updatet1]) < (filemtime($cachefile) + 5000))))) {
    echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
    include($cachefile);
} else {ob_start(); // Запуск буфера вывода
?>
		<div class="content">
			<div class="content-asistent">
				<p>АСИСТЕНТИ</p>
				<table>
				  <tr class="tabl_head">
				    <td>№</td>
					<td></td>
					<td>Матчі</td>
					<td>ГП</td>
					<td>ГП/М</td>
					<td>КОМАНДА</td>				    
				  </tr>
<? 
  $record_pagestat["ip"] = $_SERVER['REMOTE_ADDR'];
  //$record_pagestat["ip_forwarded"] = $_SERVER['HTTP_X_FORWARDED_FOR'];
  $record_pagestat["agent"] = $_SERVER['HTTP_USER_AGENT'];
$record_pagestat["page"] = "assist";
  $db->AutoExecute('page_stat',$record_pagestat,'INSERT');

$recordgol = $db->Execute("select (select pict from v9ky_team where id=(select team from v9ky_player where id=a.player)) as pict, (select name from v9ky_team where id=(select team from v9ky_player where id=a.player)) as team, (select pict from v9ky_man_face where man=(select man from v9ky_player where id=a.player) order by data desc limit 1)as face, (select concat(name1, ' ', name2) from v9ky_man where id=(select man from v9ky_player where id=a.player)) as fio, count(*) as gols, (select count(*) from v9ky_sostav where player=a.player) as matches, (count(*)/(select count(*) from v9ky_sostav where player=a.player)) as seredn from v9ky_asist a where team=(select team from v9ky_player where id=a.player) and matc in (select id from v9ky_match where turnir='".$turnir."') group by player order by count(*) desc, seredn desc");
$i = 0; $g = -5; $gm = -5;
while (!$recordgol->EOF) {
  if (($g <> $recordgol->fields[gols])or($gm <> round($recordgol->fields[seredn], 3))) $i = $i + 1;
  $g = $recordgol->fields[gols];
  $gm = round($recordgol->fields[seredn], 3);
?>
				  <tr>
				    <td><?=$i?></td>
					<td><img src="<?=$player_face_path?><?=$recordgol->fields[face]?>"><p><?=$recordgol->fields[fio]?></p></td>
					<td><?=$recordgol->fields[matches]?></td>
					<td><?=$recordgol->fields[gols]?></td>
					<td><?=round($recordgol->fields[seredn], 3)?></td>
					<td><img src="<?=$team_logo_path?><?=$recordgol->fields[pict]?>"><p><?=$recordgol->fields[team]?></p></td>				    
				  </tr>
<? $recordgol->MoveNext(); }?>
				 
				</table>
				
			</div>
		</div>
	</div>
</article>

<?
// Кешируем содержание в файл
$cached = fopen($cachefile, 'w');
fwrite($cached, ob_get_contents());
fclose($cached);
ob_end_flush(); // Отправялем вывод в браузер
}

  include_once "footer.php";
?>