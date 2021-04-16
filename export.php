<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   <head>
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon">
    <meta charset="utf-8">
  <title>Статистика VPN</title>
  <style type="text/css">
table {
  //font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
  border-collapse: collapse;
  color: #686461;
}
caption {
  padding: 5px;
  color: white;
  background: #8FD4C1;
  font-size: 12px;
  text-align: left;
  font-weight: bold;
}
th {
  border-bottom: 3px solid #B9B29F;
  padding: 5px;
  text-align: left;
}
td {
  padding: 5px;
}
tr:nth-child(odd) {
  background: white;
}
tr:nth-child(even) {
  background: #E8E6D1;
}
  </style>
 </head> 

<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//$before_conn = microtime(true);


// Соединение, выбор базы данных
$dbconn = pg_connect("host=localhost port=5442 dbname=netflow user=netflow password=netflow")
    or die('Не удалось соединиться: ' . pg_last_error());
//$after_conn = microtime(true);
//echo($after_conn - $before_conn); 

// Выполнение SQL-запроса
//$query = 'SELECT * FROM anyconnect limit 1000';
//#FROM generate_series('2021-03-31'::date,'2021-04-01'::date,'1 day') s(a)

$today = date("Y-m-d");
$yestoday  = date("Y-m-d", strtotime("yesterday"));
$fday_lmonth=date('Y-m-d', strtotime('first day of last month'));
$lday_lmonth=date('Y-m-d', strtotime('last day of last month'));

//$Start=$yestoday; $End=$today;
$Start=$fday_lmonth; $End=$lday_lmonth;


$Login ='';
$ExtIp='';
//echo "Сегодня $today, Вчера $ystd,"; 
echo 
'
 <table border="0">
  <tr> 
    <td>Выборка VPN сессий:</td>
  </tr>
    <tr>
	 <td>Выбран период </td>
    <td>
';

include 'params.php';

//echo 'from='.$_GET[from].'to='.$End;

include 'query.php';

$result = pg_query($query) or die('Ошибка запроса: ' . pg_last_error());
    echo "<td></td><td></td>
  </tr> 
 </table>
</form>";
// Вывод результатов в HTML
//var_dump($query);
header('Content-Type: application/vnd.ms-excel; charset=utf-8;'); 
header('Content-disposition: attachment; filename=VPN_statreport.xls'); 
header("Content-Transfer-Encoding: binary ");

//Шапка
echo "<table cellspacing='0' border='1' cellpadding='0' >\n";
echo "<tr style='font-weight:bold'>
	<td>Дата</td>
	<td>Подразделение</td>
	<td>Фамилия Имя Отчество</td>
	<td>Домен</td>
	<td>Продолжительность</td>
	<td>ДатаНачала</td>
	<td>ДатаОкончания</td>
	<td>ДатаНачалаСессии</td>
	<td>ДатаОкончанияСессии</td>
	<td>Логин</td>
	<td>Профиль</td>
	<td>Внешний IP</td>
	<td>Внутренний IP</td>
	<td>Страна</td>
	<td>Город</td>
	<td>Сессия</td>
    </tr>";
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

?>