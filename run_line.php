<?
  $linematch = new match(); //класс матча
  $recline = $db->CacheExecute(1000, "select gols1, gols2, team1, team2 from v9ky_match a where turnir=".$turnir." and canseled=1 and YEARWEEK(date, 1)=(select YEARWEEK(date, 1) from v9ky_match where turnir in (select id from v9ky_turnir where city=(select city from v9ky_turnir where id='".$turnir."')) and canseled=1 group by YEARWEEK(date, 1) ORDER BY YEARWEEK(date, 1) desc limit 1)  ORDER BY date aSC");
  
?>
	<div class="run-line">
		<marquee behavior="scroll" direction="left" style="display: block; width: 1255px; margin: 0 auto">
<?
  while (!$recline->EOF) {
	$recfieldsteam1 = $db->Execute("select name from v9ky_team where id=".$recline->fields['team1']);
    $recfieldsteam2 = $db->Execute("select name from v9ky_team where id=".$recline->fields['team2']);
    $goly1 = $recline->fields['gols1'];
    $goly2 = $recline->fields['gols2'];
    if ($goly1<>"-") $score = $goly1.":".$goly2; else $score = "VS";
?>
			<span><?=$recfieldsteam1->fields['name']?> <?=$score?>  <?=$recfieldsteam2->fields['name']?></span>
  

<? $recline->MoveNext(); }?> 
		</marquee>
	</div>