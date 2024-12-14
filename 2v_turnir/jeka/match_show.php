<?
  //показать все матчи в таблицу
  define('READFILE', true);
  require_once ("menu.php");
  echo "<center><br>Матчи ";
  //require_once('config.php');
  $db->debug = false;
  //удаление выбранного матча
  if (isset($_GET['id']))
  {
  	 $id_to_delete=intval($_GET['id']);
	 echo"Удален матч №".$id_to_delete;
	 $db->Execute("delete from v9ky_match where id='".$id_to_delete."'");
	 
	 $recordS = $db->Execute("select turnir from v9ky_match where id='".$id_to_delete."'");
	 $recordu["updatet"] = gmdate('Y-m-d H:i:s');
	 $recordu["upd_teams_match_stat"] = 1;
	 $recordu["upd_table"] = 1;
     $db->AutoExecute('v9ky_turnir',$recordu,'UPDATE', 'id = '.$recordS->fields[turnir].'');
   }

session_start(); 
//if (!isset($_SESSION['page'])) $_SESSION['page']=1;

$recordTurnir = $db->Execute("select * from v9ky_turnir where active>0");
if (!isset($_SESSION['turnir'])) $_SESSION['turnir']=$recordTurnir->fields[id];

if (isset($_GET['turnir'])) {$turnir=1*($_GET['turnir']); $_SESSION['turnir']=$turnir;} else $turnir=$_SESSION['turnir'];
if (isset($_GET['tur'])) {$tur=1*($_GET['tur']); $tur_str = "and tur=".$tur;} else $tur_str = "";
   // количество записей, выводимых на странице
//  $per_page=30;
  // получаем номер страницы
//  if (isset($_GET['page'])) {$page=($_GET['page']); $_SESSION['page']=$page;} else $page=$_SESSION['page'];
  // вычисляем первый оператор для LIMIT
//  $start=abs($page*$per_page)-$per_page;

  
  echo "<select name='turnir' size=1 onchange='document.location=this.options[this.selectedIndex].value'>";
	 while (!$recordTurnir->EOF)
	 {
		 if ($recordTurnir->fields[id]==$turnir) print "<option selected>".$recordTurnir->fields[name]."  ".$recordTurnir->fields[season]."</option> \n";
		 else print "<option value='?turnir=".$recordTurnir->fields[id]."'>".$recordTurnir->fields[name]."  ".$recordTurnir->fields[season]."</option> \n";
		 $recordTurnir->MoveNext();
	 }
	 echo"</select><br>";

  $recordm = $db->Execute("select tur from v9ky_match where turnir=".$turnir." group by tur order by tur");
  while (!$recordm->EOF)
	 {
           echo "<a href='match_show.php?tur=".$recordm->fields[tur]."' > Тур".$recordm->fields[tur]."</a> ";
           $recordm->MoveNext();
	 }

  $recordSet = $db->Execute("select * from v9ky_match where turnir=".$turnir." ".$tur_str." order by date desc");
//  $recordSet1 = $db->Execute("select count(*) from v9ky_match where turnir in(select id from v9ky_turnir where active>0)");

//  $total_rows=$recordSet1->fields[0];
//  $num_pages=ceil($total_rows/$per_page);

//if ($id_to_select>0) {$id_to_selectt="&selid=".$id_to_select;}else{$id_to_selectt="";}
//for($i=1;$i<=$num_pages;$i++) {
//  if ($i == $page) {
//    echo $i." ";
//  } else {
//    echo "<a href='".$_SERVER['PHP_SELF']."?page=".$i.$id_to_selectt."'>".$i."</a> ";
//  }
//}

  echo "<center><table cellspacing='2' border='1' cellpadding='5'><tr><td>ID</td><td>Дата</td><td>Поле</td><td>Тур</td><td>Команды</td>
  <td>Турнир</td><td>Состояние</td>";
if ($recorduser->fields[permition]=="admin") echo"<td>Голы</td><td>Жол</td><td>Жол/Кр</td><td>Кр</td><td><abbr title='Асист'>Ас</abbr></td><td><abbr title='Арбитр1 зеленый если указан'>А1</abbr></td><td><abbr title='Арбитр2 зеленый если указан'>А2</abbr></td><td><abbr title='Корекція ціни за матч команди1'>₴1</abbr></td><td><abbr title='Корекція ціни за матч команди2'>₴2</abbr></td>";
  if ($recorduser->fields[permition]=="admin") echo"<td></td>";
  if (($recorduser->fields[permition]=="admin")||($recorduser->fields[permition]=="video")||($recorduser->fields[permition]=="kicks")) echo"<td>Трансл</td><td>Флешка</td>";
  if (($recorduser->fields[permition]=="admin")||($recorduser->fields[permition]=="zhyrnalist")) echo"<td>Статьи</td>";
  if (($recorduser->fields[permition]=="admin")||($recorduser->fields[permition]=="photo")) echo"<td>Фото</td>";
  if($recorduser->fields[permition]=="admin") echo"<td><a href='match_update.php'>+</a></td><td> </td>";
  echo"</tr>";
  while (!$recordSet->EOF) {
    $recordSetf = $db->Execute("select name from v9ky_fields where id='".$recordSet->fields[field]."'");
    $recordSet2 = $db->Execute("select name from v9ky_team where id='".$recordSet->fields[3]."'");
    $recordSet3 = $db->Execute("select name from v9ky_team where id='".$recordSet->fields[4]."'");
    $recordSet4 = $db->Execute("select id, name, season from v9ky_turnir where id='".$recordSet->fields[5]."'");
    print "<tr><td>".$recordSet->fields[0]."</td><td>".$recordSet->fields[1]."</td><td>".$recordSetf->fields[name]."</td>
	<td><a href='post_game_update.php?matcid=".$recordSet4->fields[id]."&tur=".$recordSet->fields[tur]."' >".$recordSet->fields[tur]."</a></td>
	<td><center>".$recordSet2->fields[name]." vs ".$recordSet3->fields[name]."</center></td>
	<td>".$recordSet4->fields[name]."</td>
	<td>";
	switch ($recordSet->fields[6]) {
    case 0:
        echo "Активен";
        break;
    case 1:
        echo "Завершен";
        break;
    case 2:
        echo "Live";
        break;
    case 3:
        echo "Отменен";
        break;
}

	echo "</td>";
	if ($recorduser->fields[permition]=="admin") echo"<td><a href='gol_update.php?matcid=".$recordSet->fields[0]."' ><img src='picts/ball.jpg' height='22' /></a></td>";

	if ($recorduser->fields[permition]=="admin") echo"<td><a href='yellow_update.php?matcid=".$recordSet->fields[0]."' ><img src='picts/yel.jpg' height='22' /></a></td>";
  if ($recorduser->fields[permition]=="admin") echo"<td><a href='yellow_red_update.php?matcid=".$recordSet->fields[0]."' ><img src='picts/yellow-red-icon.svg' height='22' /></a></td>";

	if ($recorduser->fields[permition]=="admin") echo"<td><a href='red_update.php?matcid=".$recordSet->fields[0]."' ><img src='picts/red.jpg'  height='22' /></a></td>";

        if ($recorduser->fields[permition]=="admin") echo"<td><a href='asist_update.php?matcid=".$recordSet->fields[0]."' ><img src='picts/asist.png' height='22' /></a></td>";

        if ($recorduser->fields[permition]=="admin"){ echo"<td ";
		if ($recordSet->fields[refery1]!=="0") echo "bgcolor='#a3fDa6'";
		echo "></td>";
                echo"<td ";
		if ($recordSet->fields[refery2]!=="0") echo "bgcolor='#a3fDa6'";
		echo "></td>";
        }
        echo"<td>".$recordSet->fields[correction_price_team1]."</td><td>".$recordSet->fields[correction_price_team2]."</td>";
	if ($recorduser->fields[permition]=="admin") echo"<td><a href='sostav_update.php?matcid=".$recordSet->fields[0]."' >Состав</a></td>";

	if (($recorduser->fields[permition]=="admin")||($recorduser->fields[permition]=="video")||($recorduser->fields[permition]=="kicks")){ echo"<td ";
		if ($recordSet->fields[video]!=="") echo "bgcolor='#a3fDa6'";
		echo "><a href='video_update.php?matcid=".$recordSet->fields[0]."' ><img src='picts/video.jpg' /></a></td>";}
        if (($recorduser->fields[permition]=="admin")||($recorduser->fields[permition]=="video")||($recorduser->fields[permition]=="kicks")){ echo"<td ";
		if ($recordSet->fields[videohiden]!=="") echo "bgcolor='#a3fDa6'";
		echo "><a href='video_update.php?matcid=".$recordSet->fields[0]."' ><img src='picts/video.jpg' /></a>HD</td>";}

	if (($recorduser->fields[permition]=="admin")||($recorduser->fields[permition]=="zhyrnalist")) echo"<td><form action='statia_update.php' method='POST' ><input type='hidden' name='matcid' value='".$recordSet->fields[0]."'>
	<input type='submit' value='Изм'></form></td>";

	  if (($recorduser->fields[permition]=="admin")||($recorduser->fields[permition]=="photo")) {echo"<td ";
                
                $dir = "../photo/".$recordSet->fields[0]."/";
                
                //$files = scandir($dir); 
                if (file_exists($dir)) {
                
                //if ($files !== false) {
                  echo "bgcolor='#a3fDa6'";
                }
		echo "><a href='phot_update.php?matcid=".$recordSet->fields[0]."' ><img src='picts/camera.png' height='22' /></a></td>";}

	if ($recorduser->fields[permition]=="admin") echo"<td><a href='match_update.php?id=".$recordSet->fields[0]."'>Edit</a></td>";


    $txt1 = "Удалить матч с ID=".$recordSet->fields[id]."?";
    if ($recorduser->fields[permition]=="admin"){
      echo "<td><a href='match_show.php?id=".$recordSet->fields[id]."' "; 
?>
onclick='return confirm("<? echo $txt1; ?>")' >
<?
    echo"Delete</a></td>";}

    echo"</tr> \n";
    $recordSet->MoveNext();
}
echo"</table><br></center>";

?>