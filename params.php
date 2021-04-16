<?php
if(!empty($_GET  ['from'])) {
$Start =    $_GET  ['from'];
}
echo " c $Start </td>";

if(!empty($_GET  ['to'])) {
$End =    $_GET  ['to'];
}
echo "<td> по $End </td>";
if(!empty($_GET ['login'])) {
 // Если логин не пустой отбор по логину
$Login =  $_GET  ['login'];
$qw_login ="WHERE anyconnect.username ='$Login'";
echo "<td> Логин $Login </td>";}
else
{
$qw_login="";
echo "<td> Все Логины </td>";
}


if(!empty($_GET ['extip'])) {
 // Если External IP не пустой отбор по extip
$ExtIp =  $_GET  ['extip'];
echo " Внешний IP $ExtIp";  // var_dump($_GET  ['extip']);
$qw_extip="WHERE anyconnect.realipaddr ='$ExtIp'";
}
else
{
$qw_extip="";
}
?>