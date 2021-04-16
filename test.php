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


// Соединение, выбор базы данных
$dbconn = pg_connect("host=localhost port=5442 dbname=netflow user=netflow password=netflow")
    or die('Не удалось соединиться: ' . pg_last_error());


$today = date("Y-m-d"); $yestoday  = date("Y-m-d", strtotime("yesterday")); 
$fday_lmonth=date('Y-m-d', strtotime('first day of last month'));
$lday_lmonth=date('Y-m-d', strtotime('last day of last month'));

$Start=$yestoday; $End=$today;
//$Start=$fday_lmonth; $End=$lday_lmonth;

$Login =''; $ExtIp='';

echo '<p>
<a href="http://netmon.ocm.ru:8182" class="design">Онлайн Статистика </a> 
<a href="http://netmon.ocm.ru:8181" class="design">Cisco Syslog </a> 
<a href="https://10.13.3.110:8001" class="design">ESET-ESA Administrator</a> 
</p>';

echo '<form id="inputform" method="GET" >
 <table border="0">
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

include 'tbl_header.php';

//Заполнение таблицы
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
        echo "\t\t<td>$col_value</td>\n";
    }
    echo "\t</tr>\n";
}
echo "</table>\n";

// Очистка результата
pg_free_result($result);

// Закрытие соединения
pg_close($dbconn);

$after_conn = microtime(true);
echo("Время запроса ".($after_conn - $before_conn)); 


?>

