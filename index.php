    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   <head>
  <!-- CSS  -->
  <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>

    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon">
    <meta charset="utf-8">
    <title>Статистика VPN</title>
 </head> 
<?php
$before_conn = microtime(true);
date('Y-m-d\TH:i:s');
echo "<div align=center>";
include 'buttons.php'; //Верхние кнопки

// Соединение, выбор базы данных
$dbconn = pg_connect("host=localhost port=5442 dbname=netflow user=netflow password=netflow")
    or die('Не удалось соединиться: ' . pg_last_error());


$today = date("Y-m-d"); $yestoday  = date("Y-m-d", strtotime("-2 day")); 
$fday_lmonth=date('Y-m-d', strtotime('first day of last month'));
$lday_lmonth=date('Y-m-d', strtotime('last day of last month'));

$Start=$yestoday; $End=$today;
//$Start=$fday_lmonth; $End=$lday_lmonth;

$Login =''; $ExtIp='';

echo '<form id="inputform" method="GET" >
 <table border="0" width=60%>
  <tr> 
    <td>Отчет по Cisco VPN сессиям:</td>
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

    $y=1;
    $depart_old="";
    $fio_old="";
    $date_old="";
    $ddt_old="";
    $f_value="";
    $dd_time=0;
    $blockdata="";

//Заполнение таблицы
while ($line = pg_fetch_object($result)) 
{ //Выводим Строки
	if ($line->depart=="") //Подразделение
	{$line->depart="Подразделение не определено";}
	 if ($depart_old<>$line->depart)
	{ //Start Depart
          echo "</table></br><table border=0 width=60%><tr style='font-weight:bold'><td align=center>Подразделение: ".strtoupper($line->domain)."  $line->depart</td></tr></table>";
	  $depart_old=$line->depart;
	if ($line->fio=="") 
	    {$line->fio="ФИО не определено";}
	if ($fio_old<>$line->fio)
	{ //ФИО
         echo "</table></br><table border=0 width=60%><tr style='font-weight:bold'><td align=left>1 $line->fio</td></tr></table>"; 
         $blockdata="";
	 $date_old=0; 
	 $ddt_old=0; 
	 $dd_time=0;
	 $fio_old=$line->fio;
	 if ($date_old<>$line->datesa) 
         { //Дата
           $dd_time=0; 
           $date_old=$line->datesa;
           $dd_time +=strtotime($line->length); 
           $datebegin=date('G:i', strtotime($line->datebegin));
           $dateend=date('G:i', strtotime($line->dateend));
           $blockdata=$blockdata."<tr>\t\t<td> $line->length</td>\n";
           $blockdata=$blockdata."\t\t<td> $datebegin</td>\n";
           $blockdata=$blockdata."\t\t<td> $dateend</td>\n";
           $blockdata=$blockdata."\t\t<td> $line->sessionbegin</td>\n";
           $blockdata=$blockdata."\t\t<td> $line->sessionend</td>\n";
           $blockdata=$blockdata."\t\t<td> $line->login</td>\n";
           $blockdata=$blockdata."\t\t<td> $line->externalip</td>\n";
           $blockdata=$blockdata."\t</tr>\n";
           include 'tbl_header.php';
           echo " $blockdata </table>";
         } //END DATE
	else {echo "Debug data: old:".$date_old." cur:".$line->datesa." ".$line->fio.date('H:i',$dd_time)." </br>"; }
      } //End FIO
else {echo "fio7";}
//     $dd_time=0;
    } //END Depart
    else { //Одно подразделение, одно ФИО, один день
           $blockdata="";
           $dd_time +=strtotime($line->length); 
           $datebegin=date('G:i', strtotime($line->datebegin));
           $dateend=date('G:i', strtotime($line->dateend));
           $blockdata=$blockdata."\t\t<td width=14%> $line->length</td>\n";
           $blockdata=$blockdata."\t\t<td width=14%> $datebegin</td>\n";
           $blockdata=$blockdata."\t\t<td width=14%> $dateend</td>\n";
           $blockdata=$blockdata."\t\t<td width=14%> $line->sessionbegin</td>\n";
           $blockdata=$blockdata."\t\t<td width=14%> $line->sessionend</td>\n";
           $blockdata=$blockdata."\t\t<td width=14%> $line->login</td>\n";
           $blockdata=$blockdata."\t\t<td width=14%> $line->externalip</td>\n";
           $blockdata=$blockdata."\t</tr>\n";
           $ddt_old=$dd_time;
	  if ($fio_old<>$line->fio)
	{ //ФИО повтор
           $fio_old=$line->fio;
//           echo "<table border=0 width=60%><tr><td align=left width=100% style='font-weight:bold'> $line->datesa 2 Общее время ".date('H:i',$dd_time)."</td></tr></tr></table>\n\n";
           include 'tbl_header.php';
 	  if ($date_old<>$line->datesa) 
          { //Дата повтор
           $date_old=$line->datesa;
           echo "</table></br><table border=0 width=60%><tr style='font-weight:bold'><td align=left> $line->fio</td></tr></table>"; 
           echo "<table cellspacing='0' border='1' cellpadding='0' width=60%>";
          }//END Дата повтор
          else { echo "<table border=0 width=60%><tr><td align=left width=100% style='font-weight:bold'>$line->datesa $line->fio (3) Общее время ".date('H:i',$dd_time)."</td></tr></tr></table>\n\n"; }
         }//END ФИО повтор
           echo "<table cellspacing='0' border='1' cellpadding='0' width=60%> <tr>";
           echo $blockdata."</table>";

	}//END ELSE
//  echo "Debug2: old:".$date_old." cur:".$line->datesa." ".$line->fio." ".date('H:i',$dd_time)." </br>" ;
} //END Выводим Строки

// Очистка результата
pg_free_result($result);

// Закрытие соединения
pg_close($dbconn);

$after_conn = microtime(true);
echo("Время запроса ".($after_conn - $before_conn))."</div>" ; 

?>

 