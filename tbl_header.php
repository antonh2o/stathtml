<?php
echo "<table cellspacing='0' border='1' cellpadding='0' width=60%>\n";
if (!empty($_GET["debug"]))
{
echo "    <tr style='font-weight:bold'>
     <td>(0) Подразделение</td>
    </tr><tr>
     <td>(1) Фамилия Имя Отчество</td>
     <td>(2)Дата</td>
     <td>(3) Продолжительность</td>
     <td>(4) Время Начала</td>
     <td>(5) Время Окончания</td>
     <td>(6) Дата Начала Сессии</td>
     <td>(7) Дата Окончания Сессии</td>
     <td>(8) Логин</td>
     <td>(9) Домен</td>
     <td>(10) Внешний IP</td>
     <td>(11) Локальный IP</td>
     <td>(12) Город</td>
    </tr>
";
}
else
echo "    <tr style='font-weight:bold'>
     <td width=12%>Продолжительность</td>
     <td width=12%>Время Начала</td>
     <td width=12%>Время Завершения</td>
     <td width=12%>Начало Сессии</td>
     <td width=12%>Завершение Сессии</td>
     <td width=12%>Логин</td>
     <td width=12%>Внешний IP</td>
    </tr>
";

?>

