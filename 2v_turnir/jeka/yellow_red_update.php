<?PHP 
define('READFILE', true);
require_once ("menu.php");
require_once('config.php');

$matcid = intval($_GET['matcid'])*1;
$recordS = $db->Execute("select * from v9ky_match where id='".$matcid."'");
$recordSet2 = $db->Execute("select name from v9ky_team where id='".$recordS->fields[3]."'"); 
$recordSet3 = $db->Execute("select name from v9ky_team where id='".$recordS->fields[4]."'");
?>  
<center>
<h2>Желто-красные карточки матча ТЕСТ</h2> 
<? echo "<table border='1' cellpadding='5'><tr><td>".$recordS->fields[date]."</td><td>".$recordSet2->fields[name]."</td>
<td>".$recordSet3->fields[name]."</td></tr></table><br>"; ?>

<?
//удаление выбранной команды
  if (isset($_GET['del'])) 
  {
  	 $delid=intval($_GET['del']);
	 //удаляем картинку
	 
	 echo"Удалена карточка ID".$delid;
	 $db->Execute("delete from v9ky_yellow_red where id='".$delid."'");
     $record_gol_to_matc["upd_teams_match_stat"] = 1;
	 $db->AutoExecute('v9ky_match',$record_gol_to_matc,'UPDATE', 'id = '.$matcid.'');
   } 

if ((!empty($_GET))&&((isset($_GET['red']))||(isset($_GET['id'])))){
  if (isset($_GET['red'])){
    if (isset($_GET['id'])) $team_id=intval($_GET['id'])*1;   
    if (isset($_GET['team'])) $team=intval($_GET['team'])*1; else $team=0;  
    if (isset($_GET['time'])) $time=intval($_GET['time'])*1; else $time=0;
    if (isset($_GET['player'])) $player=intval($_GET['player'])*1; else $player=0;	
	
    $record["matc"] = $matcid;	
    $record["team"] = $team;
	$record["time"] = $time;
	$record["player"] = $player;
    //$recordtime = $db->Execute("SELECT CURRENT_TIMESTAMP");
         $record1["updatet"] = gmdate('Y-m-d H:i:s');
        
	//запись в базу
	if (isset($_GET['red'])) {$redatirovat_or_else=intval($_GET['red']);
        } ELSE {$redatirovat_or_else=0;}	
	if ($redatirovat_or_else==1) 
	  {
    	 $db->AutoExecute('v9ky_yellow_red',$record,'UPDATE', 'id = '.$team_id.'');	
         	 
	  }else {$db->AutoExecute('v9ky_yellow_red',$record,'INSERT'); }  
  }
  $record_gol_to_matc["upd_teams_match_stat"] = 1;
	$db->AutoExecute('v9ky_match',$record_gol_to_matc,'UPDATE', 'id = '.$matcid.'');
  $team=0;
  $time=0;
  $player=0;
  
  if ((isset($_GET['id']))&&(intval($_GET['id'])*1>0)&&(($redatirovat_or_else==1)||(!isset($_GET['red'])))) 
  {
  	 $id_to_update=intval($_GET['id'])*1;
	 $recordSet1 = $db->Execute("select * from v9ky_yellow_red where id='".$id_to_update."'");
  }else {
    $recordSet1 = $db->Execute("select * from v9ky_yellow_red where id=(SELECT LAST_INSERT_ID())");
    $id_to_update=$recordSet1->fields['id'];
  }
  
  $recordS1 = $db->Execute("select * from v9ky_yellow_red where matc='".$matcid."' ORDER BY time");
  echo "<center><table cellspacing='2' border='1' cellpadding='5'><tr><td>ID</td><td>Команда</td><td>Игрок</td>
    <td>Минута</td><td></td><td></td></tr>";
  while (!$recordS1->EOF) {  
    $recordS2 = $db->Execute("select * from v9ky_team where id='".$recordS1->fields[team]."'"); 
	$recordS3 = $db->Execute("select * from v9ky_player where id='".$recordS1->fields[player]."'");
    $recordname = $db->Execute("select name1, name2 from v9ky_man where id=(select man from v9ky_player where id=".$recordS1->fields[player].")"); 
    print "<tr><td>".$recordS1->fields[0]."</td>
	<td>".$recordS2->fields[name]."</td><td>".$recordname->fields[name1]." ".$recordname->fields[name2]."</td>
	<td>".$recordS1->fields[time]."</td>
	<td><a href='yellow_red_update.php?matcid=".$matcid."&id=".$recordS1->fields[0]."'>Edit</a></td>
	<td><a href='yellow_red_update.php?matcid=".$matcid."&del=".$recordS1->fields[0]."'>Delete</a></td>";
    print "</tr> \n";
    $recordS1->MoveNext();
  }
  echo"</table></center>";
  
	 $recordSet1 = $db->Execute("select * from v9ky_yellow_red where id='".$id_to_update."'");
	 echo"<form action='yellow_red_update.php' method='GET' ENCTYPE='multipart/form-data'> 
	 <table><tr><td>Команда</td><td>Игрок</td><td>Минута</td></tr>
	 <tr><td>"; 
	 echo"<select name='team' size=1> ";
	 $recordSet2 = $db->Execute("select * from v9ky_team where id=".$recordS->fields[team1]."");
	 if ($recordSet2->fields[id]==$recordSet1->fields['team']) print "<option value='".$recordSet2->fields[id]."' selected>".$recordSet2->fields[name]."</option> \n";
	 else print "<option value='".$recordSet2->fields[id]."'>".$recordSet2->fields[name]."</option> \n";
		 
	 
	 $recordSet2 = $db->Execute("select * from v9ky_team where id=".$recordS->fields[team2]."");
	 if ($recordSet2->fields[id]==$recordSet1->fields['team']) print "<option value='".$recordSet2->fields[id]."' selected>".$recordSet2->fields[name]."</option> \n";
	 else print "<option value='".$recordSet2->fields[id]."'>".$recordSet2->fields[name]."</option> \n";
		 
	 
	 echo"</select></td> ";
	 echo"<td><select name='player' size=1>";
	 $recordSet5 = $db->Execute("select v9ky_player.id as id,v9ky_player.name1 as name1,v9ky_player.name2 as name2 from v9ky_sostav, v9ky_player where v9ky_sostav.player=v9ky_player.id AND v9ky_sostav.matc='".$matcid."' ORDER BY v9ky_player.team=".$recordS->fields[team2].", v9ky_player.name1");
	 while (!$recordSet5->EOF) 
	 {
             $recordname = $db->Execute("select name1, name2 from v9ky_man where id=(select man from v9ky_player where id=".$recordSet5->fields[id].")");
		 if ($recordSet5->fields[id]==$recordSet1->fields['player']) print "<option value='".$recordSet5->fields[id]."' selected>
		 ".$recordSet5->fields[nomer]." ".$recordname->fields[name1]." ".$recordname->fields[name2]."</option> \n";
		 else print "<option value='".$recordSet5->fields[id]."'>".$recordSet5->fields[nomer]." ".$recordname->fields[name1]." ".$recordname->fields[name2]."</option> \n";
		 $recordSet5->MoveNext();
	 }
	// $recordSet5 = $db->Execute("select * from v9ky_player where team=".$recordS->fields[team2]." ORDER BY nomer");
	 //while (!$recordSet5->EOF) 
	 //{
	//	 if ($recordSet5->fields[id]==$recordSet1->fields['player']) print "<option value='".$recordSet5->fields[id]."' selected>
	//	 ".$recordSet5->fields[nomer]." ".$recordSet5->fields[name1]." ".$recordSet5->fields[name2]."</option> \n";
	//	 else print "<option value='".$recordSet5->fields[id]."'>".$recordSet5->fields[nomer]." ".$recordSet5->fields[name1]." ".$recordSet5->fields[name2]."</option> \n";
	//	 $recordSet5->MoveNext();
	 //}
	 echo"</select></td> ";
	 echo"<td><input type='text' name='time' size='3' value='".($recordSet1->fields['time'])."'></td></tr></table> ";
	     
	 echo"<input type='submit' value='  Изменить  '><input type='radio' name='red' value='1' checked>
	 Внести изменения в карточку ".$recordSet1->fields['id']."<input type='radio' name='red' value='0'>
	 Добавить как новую<input type='hidden' name='id' value='".$id_to_update."'>
	 <input type='hidden' name='matcid' value='".$matcid."'></form> ";
	 if (isset($_GET['red'])){ echo "Желтая карточка: <H2> ".$recordSet1->fields['id']." </H2> изменения приняты";}
}else {
  $recordS1 = $db->Execute("select * from v9ky_yellow_red where matc='".$matcid."' ORDER BY time");
  echo "<center><table cellspacing='2' border='1' cellpadding='5'><tr><td>ID</td><td>Команда</td><td>Игрок</td>
    <td>Минута</td><td></td><td></td></tr>";
  while (!$recordS1->EOF) {
    $recordS2 = $db->Execute("select * from v9ky_team where id='".$recordS1->fields[team]."'"); 
	$recordS3 = $db->Execute("select * from v9ky_player where id='".$recordS1->fields[player]."'");
        $recordname = $db->Execute("select name1, name2 from v9ky_man where id=(select man from v9ky_player where id=".$recordS1->fields[player].")"); 
    print "<tr><td>".$recordS1->fields[0]."</td>
	<td>".$recordS2->fields[name]."</td><td>".$recordname->fields[name1]." ".$recordname->fields[name2]."</td>
	<td>".$recordS1->fields[time]."</td>
	<td><a href='yellow_red_update.php?matcid=".$matcid."&id=".$recordS1->fields[0]."'>Edit</a></td>
	<td><a href='yellow_red_update.php?matcid=".$matcid."&del=".$recordS1->fields[0]."'>Delete</a></td>";
    print "</tr> \n";
    $recordS1->MoveNext();
  }
  echo"</table></center>";
  
  echo "<form action='yellow_red_update.php' method='GET' ENCTYPE='multipart/form-data'> 
     <table><tr><td>Команда</td><td>Игрок</td><td>Минута</td></tr>
     <tr><td>";
	 echo"<select name='team' size=1> ";
	 $recordSet2 = $db->Execute("select * from v9ky_team where id=".$recordS->fields[team1]."");
	 if ($recordSet2->fields[id]==$team) print "<option value='".$recordSet2->fields[id]."' selected>".$recordSet2->fields[name]."</option> \n";
	 else print "<option value='".$recordSet2->fields[id]."'>".$recordSet2->fields[name]."</option> \n";
		 
	 
	 $recordSet2 = $db->Execute("select * from v9ky_team where id=".$recordS->fields[team2]."");
	 if ($recordSet2->fields[id]==$team) print "<option value='".$recordSet2->fields[id]."' selected>".$recordSet2->fields[name]."</option> \n";
	 else print "<option value='".$recordSet2->fields[id]."'>".$recordSet2->fields[name]."</option> \n";
		 
	 echo"</select> </td><td><select name='player' size=1>";
	 $recordSet5 = $db->Execute("select v9ky_player.id as id,v9ky_player.name1 as name1,v9ky_player.name2 as name2 from v9ky_sostav, v9ky_player where v9ky_sostav.player=v9ky_player.id AND v9ky_sostav.matc='".$matcid."' ORDER BY v9ky_player.team=".$recordS->fields[team2].", v9ky_player.name1");
	 while (!$recordSet5->EOF) 
	 {
            $recordname = $db->Execute("select name1, name2 from v9ky_man where id=(select man from v9ky_player where id=".$recordSet5->fields[id].")");
		 if ($recordSet5->fields[id]==$recordSet1->fields['player']) print "<option value='".$recordSet5->fields[id]."' selected>
		 ".$recordname->fields[name1]." ".$recordname->fields[name2]."</option> \n";
		 else print "<option value='".$recordSet5->fields[id]."'>".$recordSet5->fields[nomer]." ".$recordname->fields[name1]." ".$recordname->fields[name2]."</option> \n";
		 $recordSet5->MoveNext();
	 }
	// $recordSet5 = $db->Execute("select * from v9ky_player where team=".$recordS->fields[team2]." ORDER BY nomer");
	 //while (!$recordSet5->EOF) 
	 //{
	//	 if ($recordSet5->fields[id]==$recordSet1->fields['player']) print "<option value='".$recordSet5->fields[id]."' selected>
	//	 ".$recordSet5->fields[nomer]." ".$recordSet5->fields[name1]." ".$recordSet5->fields[name2]."</option> \n";
	//	 else print "<option value='".$recordSet5->fields[id]."'>".$recordSet5->fields[nomer]." ".$recordSet5->fields[name1]." ".$recordSet5->fields[name2]."</option> \n";
	//	 $recordSet5->MoveNext();
	 //}
	 echo"</select>";
	 
	 echo" </td>
	 <td><input type='text' name='time' size='3' ></td></tr></table> ";
  echo "<input type='hidden' name='red' value='0'><input type='hidden' name='matcid' value='".$matcid."'>
<input type='submit' value='  Создать  '></form>  ";
}
?> 
</center>
</body>
</html>