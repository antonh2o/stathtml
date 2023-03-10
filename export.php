<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   <head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon">
    <meta charset="utf-8">
    <title>Статистика VPN</title>
 </head> 
<?php
$before_conn = microtime(true);
date('Y-m-d\TH:i:s');
echo "<div align=center>";
//include 'buttons.php';
// Соединение, выбор базы данных
$dbconn = pg_connect("host=localhost port=5442 dbname=netflow user=netflow password=netflow")
    or die('Не удалось соединиться: ' . pg_last_error());


$today = date("Y-m-d"); $yestoday  = date("Y-m-d", strtotime("yesterday")); 
$fday_lmonth=date('Y-m-d', strtotime('first day of last month'));
$lday_lmonth=date('Y-m-d', strtotime('last day of last month'));

$Start=$yestoday; $End=$today;
//$Start=$fday_lmonth; $End=$lday_lmonth;

$Login =''; $ExtIp='';

echo '<form id="inputform" method="GET" >
 <table border="0" width=60%>
  <tr> 
    <td>Выборка VPN сессий:</td>
    <td>с <input type="date" id="from" name="from" value="from" onchange="$_GET[from] = this.value;" /></td>
    <td>по <input type="date" id="to" name="to" value="to" onchange="$.GET[to] = this.value;"/></td>
    <td>Логин <input type="string" id="login" name="login"  /></td>
    <td><button>Применить</button></td>
  </tr>
    <tr>
    <td>Выбран период</td>
    <td>
';

include 'params.php';
echo ' </form>';

// Выполнение SQL-запроса
include 'query.php';

$result = pg_query($query) or die('Ошибка запроса: ' . pg_last_error());
header('Content-Type: application/vnd.ms-excel; charset=utf-8;');
header('Content-disposition: attachment; filename=VPN_statreport.xls');
header("Content-Transfer-Encoding: binary ");

echo '
  <td> </form>
<form id="export" method="GET" action="export.php?from=$_GET["from"]&to=$_GET["to"]&login=$_GET["login"]>
<button>Export в Excel</button>
</form>
</td>
  </tr> 
  </table>
</br>
';


// Вывод результатов в HTML
//var_dump($query);

//include 'tbl_header.php';
    $y=1;
     $depart_old="";
    $fio_old="";
    $date_old="";
    $f_value="";
//Заполнение таблицы
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    //echo "\t\t<td>$y</td>\n";
    $i=0;
    foreach ($line as $col_value) {

    switch ($i) 
    {
     case 0: // Подразделение
     {
	if ($col_value=="")
	    {$col_value="Не распознан";}

	if ($depart_old<>$col_value) 
	    {
          echo "</table></br><table border=0 width=60%><tr  style='font-weight:bold'><td align=center> $col_value</td></tr></table><table border=1 width=66%>\n";
		$depart_old=$col_value;

            }
	else
	    {
	     $col_value='';
	    }
     }
        break;
     case 1: //FIO
	{
	if ($col_value=="")
	    {$col_value="Не распознан";}
	    if ($fio_old<>$col_value)
	{
        echo "</table><table border=0 width=60%><tr style='font-weight:bold'><td align=left> $col_value</td></tr></table>\n"; 
	$fio_old=$col_value;
	$date_old='';
	} 
	else 
	    {
	     $col_value=''; //$date_old='';
	    }
	}
        break;
     case 2: //Дата
	{	
	    if ($date_old<>$col_value)
	{
          echo "</table><table border=0 width=60%><tr style='font-weight:bold'><td> $col_value</td></tr></table>\n"; 
	$date_old=$col_value;
	include 'tbl_header.php';
	}
	else
	{
	   //$date_old="";$col_value='';
	}
        break;
	}
     case 3: //Продолжительность
	{	
          echo "\t\t<td> $col_value</td>\n";
        break;
	}
     case 4: //Время начала
	{	
	$col_value=date('G:i', strtotime($col_value));
          echo "\t\t<td> $col_value</td>\n";
        break;
	}
     case 5: //Время окончания
	{	
	$col_value=date('G:i', strtotime($col_value));
          echo "\t\t<td> $col_value</td>\n";
        break;
	}
     default: // условие по умолчанию
     {
      echo "\t\t<td> $col_value</td>\n";
      break;
     }
    }
if (!$col_value=='')
{
 if (!empty($_GET["debug"]))
  {
  //    var_dump(' i='.$i.' y='.$y.'; cvalue='.$col_value.'; dep_old='.$depart_old.' fio_old='.$fio_old);
  //    echo "\t \n";
  //    echo "\t\t<td align=center>[$y]($i) $col_value</td>\n";
  } //else
//    {echo "\t\t<td align=center width=66%> $col_value</td>\n";}
}
     $i=$i+1;
   }
   echo "\t</tr>\n";
   $y=$y+1;
}
echo "</table>\n";

// Очистка результата
pg_free_result($result);

// Закрытие соединения
pg_close($dbconn);

$after_conn = microtime(true);
echo("</div> Время запроса ".($after_conn - $before_conn)); 

?>

