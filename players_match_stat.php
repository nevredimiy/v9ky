<?
  if (isset($params['match'])) {
    $baza = '/match/'.$params['match'];
  }
 
  include_once "turnir_head.php";
?>

<?
  if (isset($params['match'])) $match=($params['match']); else $match = 1;

	 $cachefile = 'jeka_cashe/players_match_stat/cached-'.$match.'.html';
// Обслуживается из файла кеша, если время запроса меньше $cachetime
$golupdate = $db->Execute("select CONVERT_TZ( updatet, @@session.time_zone, '+00:00' ) as updatet1 from v9ky_match where id='".$match."'");
//echo "<!-- Cached table copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
if ((file_exists($cachefile) && ((strtotime($golupdate->fields[updatet1]) < (filemtime($cachefile) + 2600))))) {
    
    include($cachefile);
} else {ob_start(); // Запуск буфера вывода
  $record_pagestat["ip"] = $_SERVER['REMOTE_ADDR'];
  //$record_pagestat["ip_forwarded"] = $_SERVER['HTTP_X_FORWARDED_FOR'];
  $record_pagestat["agent"] = $_SERVER['HTTP_USER_AGENT'];
  $record_pagestat["page"] = "players_match_stat";
  $record_pagestat["test"] = $match;
  $db->AutoExecute('page_stat',$record_pagestat,'INSERT');
  
  
  $recordmatch = $db->Execute("select team1, team2 from v9ky_match a where id='".$match."'");
  $recfieldsteam1 = $db->Execute("select name, pict from v9ky_team where id=".$recordmatch->fields[team1]);
  $recfieldsteam2 = $db->Execute("select name, pict from v9ky_team where id=".$recordmatch->fields[team2]);
  $recordsostav1 = $db->Execute("select nomer,seyv, seyvmin,vstvor,vtrata, mimo,pasplus,pasminus,otbor,otbormin,obvodkaplus,obvodkaminus,golevoypas,zagostrennia,blok, (select name1 from v9ky_man where id = (select man from v9ky_player where id = a.player)) as name_1, (select substr(name2,1,1) from v9ky_man where id = (select man from v9ky_player where id = a.player)) as name_2, (select name3 from v9ky_man where id = (select man from v9ky_player where id = a.player)) as name_3, (select COUNT(*) from v9ky_asist where matc='".$match."' and player=a.player and team=(select team from v9ky_player where id = a.player)) as asists, (select COUNT(*) from v9ky_gol where matc='".$match."' and player=a.player and team=(select team from v9ky_player where id = a.player)) as gols from v9ky_sostav a where a.matc='".$match."' and a.player in (select id from v9ky_player where team = (select team1 from v9ky_match where id = '".$match."')) order by gols*15+15*golevoypas + 10*zagostrennia +3*pasplus - 3*pasminus -3*vtrata + 7*vstvor + 4*mimo + 5*obvodkaplus - 3*obvodkaminus + 8*otbor - 5*otbormin + 4*blok + 15*seyv - 7*seyvmin desc");
  $recordsostav2 = $db->Execute("select nomer,seyv, seyvmin,vstvor,vtrata, mimo,pasplus,pasminus,otbor,otbormin,obvodkaplus,obvodkaminus,golevoypas,zagostrennia,blok, (select name1 from v9ky_man where id = (select man from v9ky_player where id = a.player)) as name_1, (select substr(name2,1,1) from v9ky_man where id = (select man from v9ky_player where id = a.player)) as name_2, (select name3 from v9ky_man where id = (select man from v9ky_player where id = a.player)) as name_3, (select COUNT(*) from v9ky_asist where matc='".$match."' and player=a.player and team=(select team from v9ky_player where id = a.player)) as asists, (select COUNT(*) from v9ky_gol where matc='".$match."' and player=a.player and team=(select team from v9ky_player where id = a.player)) as gols from v9ky_sostav a where a.matc='".$match."' and a.player in (select id from v9ky_player where team = (select team2 from v9ky_match where id = '".$match."')) order by gols*15+15*golevoypas + 10*zagostrennia +3*pasplus - 3*pasminus -3*vtrata + 7*vstvor + 4*mimo + 5*obvodkaplus - 3*obvodkaminus + 8*otbor - 5*otbormin + 4*blok + 15*seyv - 7*seyvmin desc");  
  
?>

		<div class="content">
			
			
			<section class="margin-block"><!-- пустой блок --></section>
			<h2 class="table-titles"><?=$recfieldsteam1->fields[name]?></h2>
			<table class="first-team">
                                <tr>
					<td></td>
					<td></td>
					<td><font color="green">15</font></td>
					<td><font color="green">10</font></td>
                                        <td><font color="green">10</font></td>
					<td><font color="green">3</font></td>
                                        <td><font color="red">3</font></td>
					<td><font color="red">3</font></td>
					<td><font color="green">7</font></td>
					<td><font color="green">4</font></td>
					<td><font color="green">5</font></td>
					<td><font color="red">3</font></td>
                                        <td><font color="green">8</font></td>
                                        <td><font color="red">5</font></td>
					<td><font color="green">4</font></td>
                                        <td><font color="green">15</font></td>
					<td><font color="red">7</font></td>
                                        <td></td>
				</tr>
				<tr>
					<th><span class="cut-span">№</span></th>
					<th><span class="cut-span">Гравець</span></th>
					<th><font color="green">Г</font></th>
					<th><font color="green">ГП</font></th>
                                        <th><font color="green">ЗП</font></th>
					<th><font color="green">П</font></th>
					<th><font color="red">П</font></th>
                                        <th><font color="red">ВМ</font></th>
					<th><font color="green">У</font></th>
					<th><font color="red">У</font></th>
					<th><font color="green">ОБ</font></th>
					<th><font color="red">ОБ</font></th>
					<th><font color="green">В</font></th>
                                        <th><font color="red">В</font></th>
                                        <th><font color="green">Б</font></th>
					<th><font color="green">С</font></th>
                                        <th><font color="red">С</font></th>
                                        <th><span class="cut-span">Т</font></th>
				</tr>
                                
<?while (!$recordsostav1->EOF) {?>
						
				<tr>
					<td><?=$recordsostav1->fields[nomer]?></td>
					<td><?=$recordsostav1->fields[name_1]?> <?=$recordsostav1->fields[name_2];?></td>
					<td><font color="green"><?=$recordsostav1->fields[gols]?></font></td>
					<td><font color="green"><?=$recordsostav1->fields[asists]?></font></td>
                                        <td><font color="green"><?=$recordsostav1->fields[zagostrennia]?></font></td>
					<td><font color="green"><?=$recordsostav1->fields[pasplus]?></font></td>
                                        <td><font color="red"><?=$recordsostav1->fields[pasminus]?></font></td>
                                        <td><font color="red"><?=$recordsostav1->fields[vtrata]?></font></td>
					<td><font color="green"><?=$recordsostav1->fields[vstvor]?></font></td>
					<td><font color="green"><?=$recordsostav1->fields[mimo]?></font></td>
					<td><font color="green"><?=$recordsostav1->fields[obvodkaplus]?></font></td>
					<td><font color="red"><?=$recordsostav1->fields[obvodkaminus]?></font></td>
					<td><font color="green"><?=$recordsostav1->fields[otbor]?></font></td>
                                        <td><font color="red"><?=$recordsostav1->fields[otbormin]?></font></td>
                                        <td><font color="green"><?=$recordsostav1->fields[blok]?></font></td>
                                        <td><font color="green"><?=$recordsostav1->fields[seyv]?></font></td>
					<td><font color="red"><?=$recordsostav1->fields[seyvmin]?></font></td>
                                        <td><?=(15 * $recordsostav1->fields[gols])+(10 * $recordsostav1->fields[golevoypas])+(10 * $recordsostav1->fields[zagostrennia])+
(3 * $recordsostav1->fields[pasplus])-(3 * $recordsostav1->fields[pasminus])-(3 * $recordsostav1->fields[vtrata])+(7 * $recordsostav1->fields[vstvor])+(4 * $recordsostav1->fields[mimo])
+(5 * $recordsostav1->fields[obvodkaplus])-(3 * $recordsostav1->fields[obvodkaminus])+(8 * $recordsostav1->fields[otbor])-(5 * $recordsostav1->fields[otbormin])+(4 * $recordsostav1->fields[blok])
+(15 * $recordsostav1->fields[seyv])-(7 * $recordsostav1->fields[seyvmin])?></td>
				</tr>
<? $recordsostav1->MoveNext();}?>
				
			</table>

			<h2 class="table-titles"><?=$recfieldsteam2->fields[name]?></h2>
			<table class="second-team">
                                <tr>
					<td></td>
					<td></td>
					<td><font color="green">15</font></td>
					<td><font color="green">10</font></td>
                                        <td><font color="green">10</font></td>
					<td><font color="green">3</font></td>
                                        <td><font color="red">3</font></td>
					<td><font color="red">3</font></td>
					<td><font color="green">7</font></td>
					<td><font color="green">4</font></td>
					<td><font color="green">5</font></td>
					<td><font color="red">3</font></td>
                                        <td><font color="green">8</font></td>
                                        <td><font color="red">5</font></td>
					<td><font color="green">4</font></td>
                                        <td><font color="green">15</font></td>
					<td><font color="red">7</font></td>
                                        <td></td>
				</tr>
				<tr>
					<th><span class="cut-span">№</span></th>
					<th><span class="cut-span">Г<span class="display-words">равець</span></span></th>
					<th><font color="green">Г</font></th>
					<th><font color="green">ГП</font></th>
                                        <th><font color="green">ЗП</font></th>
					<th><font color="green">П</font></th>
					<th><font color="red">П</font></th>
                                        <th><font color="red">ВМ</font></th>
					<th><font color="green">У</font></th>
					<th><font color="red">У</font></th>
					<th><font color="green">ОБ</font></th>
					<th><font color="red">ОБ</font></th>
					<th><font color="green">В</font></th>
                                        <th><font color="red">В</font></th>
                                        <th><font color="green">Б</font></th>
					<th><font color="green">С</font></th>
                                        <th><font color="red">С</font></th>
                                        <th><span class="cut-span">Т</font></th>
				</tr>
                                
<?while (!$recordsostav2->EOF) {?>
				<tr>
					<td><?=$recordsostav2->fields[nomer]?></td>
					<td><?=$recordsostav2->fields[name_1]?> <?=$recordsostav2->fields[name_2];?></td>
					<td><font color="green"><?=$recordsostav2->fields[gols]?></font></td>
					<td><font color="green"><?=$recordsostav2->fields[asists]?></font></td>
                                        <td><font color="green"><?=$recordsostav2->fields[zagostrennia]?></font></td>
					<td><font color="green"><?=$recordsostav2->fields[pasplus]?></font></td>
                                        <td><font color="red"><?=$recordsostav2->fields[pasminus]?></font></td>
                                        <td><font color="red"><?=$recordsostav2->fields[vtrata]?></font></td>
					<td><font color="green"><?=$recordsostav2->fields[vstvor]?></font></td>
					<td><font color="green"><?=$recordsostav2->fields[mimo]?></font></td>
					<td><font color="green"><?=$recordsostav2->fields[obvodkaplus]?></font></td>
					<td><font color="red"><?=$recordsostav2->fields[obvodkaminus]?></font></td>
					<td><font color="green"><?=$recordsostav2->fields[otbor]?></font></td>
                                        <td><font color="red"><?=$recordsostav2->fields[otbormin]?></font></td>
                                        <td><font color="green"><?=$recordsostav2->fields[blok]?></font></td>
                                        <td><font color="green"><?=$recordsostav2->fields[seyv]?></font></td>
					<td><font color="red"><?=$recordsostav2->fields[seyvmin]?></font></td>
                                        <td><?=(15 * $recordsostav2->fields[gols])+(10 * $recordsostav2->fields[golevoypas])+(10 * $recordsostav2->fields[zagostrennia])+
(3 * $recordsostav2->fields[pasplus])-(3 * $recordsostav2->fields[pasminus])-(3 * $recordsostav2->fields[vtrata])+(7 * $recordsostav2->fields[vstvor])+(4 * $recordsostav2->fields[mimo])
+(5 * $recordsostav2->fields[obvodkaplus])-(3 * $recordsostav2->fields[obvodkaminus])+(8 * $recordsostav2->fields[otbor])-(5 * $recordsostav2->fields[otbormin])+(4 * $recordsostav2->fields[blok])
+(15 * $recordsostav2->fields[seyv])-(7 * $recordsostav2->fields[seyvmin])?></td>
				</tr>
<? $recordsostav2->MoveNext();}

$time = microtime(true) - $start;
error_log($_SERVER['REQUEST_URI']." ->Gen in ".$time."sec");

// Кешируем содержание в файл
$cached = fopen($cachefile, 'w');
fwrite($cached, ob_get_contents());
fclose($cached);
ob_end_flush(); // Отправялем вывод в браузер
}
?>
				
			</table>

  
<p><font color="green">Г</font> - Гол</p>
<p><font color="green">ГП</font> - Гольова передача</p>
<p><font color="green">ЗП</font> - Загострюючий пас</p>
<p><font color="green">П</font> - Вдалий пас</p>
<p><font color="red">П</font> - Невдалий пас</p>
<p><font color="red">ВМ</font> - Втрата м'яча</p>
<p><font color="green">У</font> - Удар по воротам</p>
<p><font color="red">У</font> - Удар повз ворота</p>
<p><font color="green">ОБ</font> - Вдала обводка</p>
<p><font color="red">ОБ</font> - Невдала обводка</p>
<p><font color="green">В</font> - Вдалий відбір м'яча</p>
<p><font color="red">В</font> - Невдалий відбір м'яча</p>
<p><font color="green">Б</font> - Блок</p>
<p><font color="green">С</font> - Вдалий сейв голкіпера</p>
<p><font color="red">С</font> - Невдалий сейв голкіпера</p>
<p>T - Сумарний тотал</p>
		</div>
	</div>
</article>
					

<?
  include_once "footer.php";
?>