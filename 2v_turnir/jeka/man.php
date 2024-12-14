<?PHP
define('READFILE', true);
require_once ("menu.php");
require_once('config.php');
require_once('ajax_forms/PHPLiveX.php');


class Validation {
    
	public $text1 = "<tr><td>ID</td><td>Фото</td><td>Команда</td><td>Фамилия</td><td>Имя</td><td>Отчество</td><td>Дата рождения</td><td>Амплуа</td><td>Тел '380681111111'</td><td>Швидк<br>(20м),мс*10</td><td>Фізика<br>(14м),раз</td><td></td><td></td></tr>
	                 <tr><td></td><td></td><td></td>";
	
	public function sel_all($str){
		global $db;
		
               

        $recordS1 = $db->Execute("select * from v9ky_man WHERE name1 LIKE '".$str."%' ORDER BY name1, name2, name3");


	while (!$recordS1->EOF) {
          $record_from_team = $db->Execute("select name, (select name from v9ky_turnir where id=a.turnir) as namet from v9ky_team a where id in (select team from v9ky_player WHERE man=".$recordS1->fields[id].") and turnir in (select id from v9ky_turnir where active=1)");
            $test1 .= "<tr><td>".$recordS1->fields[0]."</td><td>";

			$test1 .= "<a href='man_foto.php?manid=".$recordS1->fields[0]."' ><img src='picts/camera.png' height='22' /></a></td><td>";
                        $b = 0;
                        while (!$record_from_team->EOF) {
                          if ($b>0) $test1 .= "<br>";
                          $b++;
                          $test1 .= $record_from_team->fields[namet];
                          $test1 .= " - ";
                          $test1 .= $record_from_team->fields[name];
                          $record_from_team->MoveNext();
                        }
                        $test1 .= "</td><td><input type='text' id='name1".$recordS1->fields[0]."' name='name1' size='20' value='".($recordS1->fields['name1'])."'></td>
	             <td><input type='text' id='name2".$recordS1->fields[0]."' name='name2' size='20' value='".($recordS1->fields['name2'])."'></td>
	             <td><input type='text' id='name3".$recordS1->fields[0]."' name='name3' size='20' value='".($recordS1->fields['name3'])."'></td>";

	             $age = date_create($recordS1->fields['age']);

	        $test1 .= "<td><input type='date' id='age".$recordS1->fields[0]."' name='age' size='60' value='".date_format($age, 'Y-m-d')."'></td>";
                $test1 .= "<td><select id='amplua".$recordS1->fields[0]."' name='amplua' size=1>";
	 if ($recordS1->fields['amplua']==0) $ifselected="selected"; else $ifselected="";
	 $test1 .= "<option value='0' ".$ifselected.">-</option> \n";
	 if ($recordS1->fields['amplua']==1) $ifselected="selected"; else $ifselected="";
	 $test1 .= "<option value='1' ".$ifselected.">НАП</option> \n";
         if ($recordS1->fields['amplua']==2) $ifselected="selected"; else $ifselected="";
	 $test1 .= "<option value='2' ".$ifselected.">ЗАХ</option> \n";
         if ($recordS1->fields['amplua']==3) $ifselected="selected"; else $ifselected="";
	 $test1 .= "<option value='3' ".$ifselected.">ВРТ</option> \n";
         if ($recordS1->fields['amplua']==2) $ifselected="selected"; else $ifselected="";
	 $test1 .= "<option value='4' ".$ifselected.">Тренер</option> \n";
         if ($recordS1->fields['amplua']==3) $ifselected="selected"; else $ifselected="";
	 $test1 .= "<option value='5' ".$ifselected.">Менеджер</option> \n
	  </select></td>";
                $test1 .= "<td><input type='text' id='tel".$recordS1->fields[0]."' name='tel' size='20' value='".$recordS1->fields['tel']."'></td>";
                $test1 .= "<td><input type='number' min='100' id='speed".$recordS1->fields[0]."' name='speed' size='2' value='".$recordS1->fields['speed']."'></td>";
                $test1 .= "<td><input type='number' min='0' max='100' id='fizika".$recordS1->fields[0]."' name='fizika' size='2' value='".$recordS1->fields['fizika']."'></td>
	             <td><input type='submit' value=' Изменить ' onclick='validupdate(".$recordS1->fields[0].");'></td>
                 <td><input type='submit' value=' Удалить ' onclick='validdel(".$recordS1->fields[0].");'></td>";
            $test1 .= "</tr> \n";
            $recordS1->MoveNext();
        }
		return $test1;
		
	}	
	
	public function del_man($idnum, $str){
		global $db;
		$idnum = 1*$idnum;
		$db->Execute("delete from v9ky_man where id='".$idnum."'");
                $db->Execute("delete from v9ky_man_face where man='".$idnum."'");
		$test = $this -> text1;


        $test .= "<td><input type='text' id='name1' name='name1' size='20' value='".$str."' onchange='findman(this.value);'></td>
        	<td><input type='text' id='name2' name='name2' size='20' value=''></td>
        	<td><input type='text' id='name3' name='name3' size='20' value=''></td>";

        $age =date_create("1900-01-01");
        $test .= "<td><input type='date' id='age' name='age' size='60' value='".date_format($age, 'Y-m-d')."'></td>
                 <td><select id='amplua' size=1>
	<option value='0' ".$ifselected.">-</option> \n
	<option value='1' ".$ifselected.">НАП</option> \n
        <option value='2' ".$ifselected.">ЗАХ</option> \n
        <option value='3' ".$ifselected.">ВРТ</option> \n
        <option value='4' ".$ifselected.">Тренер</option> \n
        <option value='5' ".$ifselected.">Менеджер</option> \n
      </select></td>
  <td><input type='text' id='tel' name='tel' size='20' value=''></td>
  <td><input type='text' min='100' id='speed' name='speed' size='2' value=''></td>
  <td><input type='text' min='0' max='100' id='fizika' name='fizika' size='2' value=''></td>
	        <td><input type='button' value='Создать' onclick='validate();'></td>
            <td>".$errmsg."</td></tr> \n";

		$test .= $this->sel_all($str); 

        return $test;
	}

    

    public function find_man($str){
	

		$test = $this -> text1;

        $test .= "<td><input type='text' id='name1' name='name1' size='20' value='".$str."' onchange='findman(this.value);'></td>
        	<td><input type='text' id='name2' name='name2' size='20' value='".$name2."'></td>
        	<td><input type='text' id='name3' name='name3' size='20' value='".$name3."'></td>";

        $age =date_create("1900-01-01");
        $test .= "<td><input type='date' id='age' name='age' size='60' value='".date_format($age, 'Y-m-d')."'></td>
                   <td><select id='amplua' size=1>
	<option value='0' ".$ifselected.">-</option> \n
	<option value='1' ".$ifselected.">НАП</option> \n
        <option value='2' ".$ifselected.">ЗАХ</option> \n
        <option value='3' ".$ifselected.">ВРТ</option> \n
        <option value='4' ".$ifselected.">Тренер</option> \n
        <option value='5' ".$ifselected.">Менеджер</option> \n
      </select></td>
  <td><input type='text' id='tel' name='tel' size='20' value=''></td>
  <td><input type='number' min='100' id='speed' name='speed' size='2' value=''></td>
  <td><input type='number' min='0' max='100' id='fizika' name='fizika' size='2' value=''></td>
	        <td><input type='button' value='Создать' onclick='validate();'></td>
            <td></td></tr> \n";

		$test .= $this->sel_all($str); 

        return $test;
    }

    public function create_man($idnum, $name1, $name2, $name3, $age, $str, $amplua, $tel, $speed, $fizika){
		global $db;
		$errmsg="";

		$name1=filter_string($name1);
		$name2=filter_string($name2);
		$name3=filter_string($name3);
		$age=filter_string($age);
                $tel=filter_string($tel);
                $amplua = 1*$amplua;
                $speed = 1*$speed;
                $fizika = 1*$fizika;

		$idnum = 1*$idnum;

		$recordS = $db->Execute("select COUNT(*) as kol from v9ky_man where name1='".$name1."' and name2='".$name2."' and name3='".$name3."' and not id='".$idnum."' ");
		if (($recordS->fields['kol']==0)&&($name1<>"")&&($name2<>"")) {

	       $record["name1"] = $name1;
	       $record["name2"] = $name2;
	       $record["name3"] = $name3;
               $record["age"] = $age;
               $record["amplua"] = $amplua;
	       $record["speed"] = $speed;
	       $record["tel"] = $tel;
               $record["fizika"] = $fizika;

			if ($idnum<>0){$db->AutoExecute('v9ky_man',$record,'UPDATE', 'id = '.$idnum.'');}else
			{$db->AutoExecute('v9ky_man',$record,'INSERT');}

		   $name1 = $str; $name2 = ""; $name3 = "";
		}else $errmsg = "<font color='red'>".$idnum."Такое имя уже<br>есть в базе!</font>";


		$test = $this -> text1;

        $test .= "<td><input type='text' id='name1' name='name1' size='20' value='".$name1."' onchange='findman(this.value);'></td>
        	<td><input type='text' id='name2' name='name2' size='20' value='".$name2."'></td>
        	<td><input type='text' id='name3' name='name3' size='20' value='".$name3."'></td>";

        $age =date_create("1900-01-01");
        $test .= "<td><input type='date' id='age' name='age' size='60' value='".date_format($age, 'Y-m-d')."'></td>
                 <td><select id='amplua' size=1>
	<option value='0' ".$ifselected.">-</option> \n
	<option value='1' ".$ifselected.">НАП</option> \n
        <option value='2' ".$ifselected.">ЗАХ</option> \n
        <option value='3' ".$ifselected.">ВРТ</option> \n
        <option value='4' ".$ifselected.">Тренер</option> \n
        <option value='5' ".$ifselected.">Менеджер</option> \n
      </select></td>
  <td><input type='text' id='tel' name='tel' size='20' value=''></td>
  <td><input type='number' min='100' id='speed' name='speed' size='2' value=''></td>
  <td><input type='number' min='0' max='100' id='fizika' name='fizika' size='2' value=''></td>
	        <td><input type='button' value='Создать' onclick='validate();'></td>
            <td>".$errmsg."</td></tr> \n";

		$test .= $this->sel_all($str); 

        return $test;
    }
}

$ajax = new PHPLiveX();

$myClass = new Validation();
$ajax->AjaxifyObjectMethods(array("myClass" => array("create_man", "del_man", "find_man")));
// If validateEmail was a static function, you wouldn't need to create an object:
// $ajax->AjaxifyClassMethods(array("Validation" => array("validateEmail")));

$ajax->Run(); // Must be called inside the 'html' or 'body' tags

?>

<script type="text/javascript">
function validupdate(idnum){
    val1 = document.getElementById("name1"+idnum).value;
	val2 = document.getElementById("name2"+idnum).value;
	val3 = document.getElementById("name3"+idnum).value;
	val4 = document.getElementById("age"+idnum).value;
        val9 = document.getElementById("name1").value;
        val5 = document.getElementById("amplua"+idnum).value;
        val6 = document.getElementById("tel"+idnum).value;
        val7 = document.getElementById("speed"+idnum).value;
        val8 = document.getElementById("fizika"+idnum).value;
    myClass.create_man(idnum, val1, val2, val3, val4, val9, val5, val6, val7, val8, {

        "onFinish": function(response){
            var msg = document.getElementById("msg");
            msg.innerHTML = response;

        }
    });
}

function validdel(idnum){
    val5 = document.getElementById("name1").value;
    myClass.del_man(idnum, val5, {

        "onFinish": function(response){
            var msg = document.getElementById("msg");
            msg.innerHTML = response;

        }
    });
}

function validate(){
    val1 = document.getElementById("name1").value;
	val2 = document.getElementById("name2").value;
	val3 = document.getElementById("name3").value;
	val4 = document.getElementById("age").value;
        val5 = document.getElementById("amplua").value;
        val6 = document.getElementById("tel").value;
        val7 = document.getElementById("speed").value;
        val8 = document.getElementById("fizika").value;
    myClass.create_man(0, val1, val2, val3, val4, val1, val5, val6, val7, val8, {

        "onFinish": function(response){
            var msg = document.getElementById("msg");
            msg.innerHTML = response;

        }
    });
}

function findman(str){
    
    myClass.find_man(str, {

        "onFinish": function(response){
            var msg = document.getElementById("msg");
            msg.innerHTML = response;

        }
    });
}
</script>


<center>
<h3>Люди</h3>
Старых людей не удалять (на них висит статистика), у новых пишите отчество так как много однофамильцев и тёзок!!!<br>
Футболёрам можно закидывать новые личные фотографии, не удаляя старые. На сайте будет отображаться последняя закинутая фотка
<?
  
  echo "<table id='msg' cellspacing='0' bordercolor='silver' border='1' cellpadding='3'><tr><td>ID</td><td>Фото</td><td>Команда</td><td>Фамилия</td><td>Имя</td><td>Отчество</td>
    <td>Дата рождения</td><td>Амплуа</td><td>Тел '380681111111'</td><td>Швидк<br>(20м),мс*10</td><td>Фізика<br>(14м),раз</td><td></td><td></td></tr>";

  print "<tr><td></td><td></td><td></td>
	<td><input type='text' id='name1' name='name1' size='20' value='' onchange='findman(this.value);'></td>
	<td><input type='text' id='name2' name='name2' size='20' value=''></td>
	<td><input type='text' id='name3' name='name3' size='20' value=''></td>";

  $age =date_create("1900-01-01");
  echo "<td><input type='date' id='age' name='age' size='60' value='".date_format($age, 'Y-m-d')."'></td>
  <td><select id='amplua' size=1>
	<option value='0' ".$ifselected.">-</option> \n
	<option value='1' ".$ifselected.">НАП</option> \n
        <option value='2' ".$ifselected.">ЗАХ</option> \n
        <option value='3' ".$ifselected.">ВРТ</option> \n
        <option value='4' ".$ifselected.">Тренер</option> \n
        <option value='5' ".$ifselected.">Менеджер</option> \n
      </select></td>
  <td><input type='text' id='tel' name='tel' size='20' value=''></td>
  <td><input type='number' min='100' id='speed' name='speed' size='2' value=''></td>
  <td><input type='number' min='0' max='100' id='fizika' name='fizika' size='2' value=''></td>
	  <input type='hidden' name='red' value='0'>
	  <td><input type='button' value='Создать' onclick='validate();'></td>
      <td></td></tr> \n ";


// echo $myClass -> sel_all();
  
  
  echo"</table>";
?>
<br><br>
</center>
</body>
</html>