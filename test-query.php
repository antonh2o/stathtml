<?php
$query = "SELECT
     (to_char (s.a,'DD.MM.YYYY' )) Дата,
     orgunit as Подразделение,
    displayname as ФИО,
    domain as Домен,
    to_char((CASE WHEN MAX(endtime at time zone 'utc' at time zone 'Etc/GMT-5')>s.a + interval '1 day' - interval '1 second'
             THEN s.a + interval '1 day' - interval '1 second'
             ELSE MAX(endtime at time zone 'utc' at time zone 'Etc/GMT-5') END)
        -(CASE WHEN MIN(starttime at time zone 'utc' at time zone 'Etc/GMT-5')<s.a THEN s.a
              ELSE MIN(starttime at time zone 'utc' at time zone 'Etc/GMT-5') END),'hh24ч. mi мин') as Продолжтельность,
    CASE WHEN MIN(starttime at time zone 'utc' at time zone 'Etc/GMT-5')<s.a THEN s.a
        ELSE MIN(starttime at time zone 'utc' at time zone 'Etc/GMT-5') END as ДатаНачала,
    CASE WHEN MAX(endtime at time zone 'utc' at time zone 'Etc/GMT-5')>s.a + interval '1 day' - interval '1 second'
            THEN s.a + interval '1 day' - interval '1 second'
        ELSE MAX(endtime at time zone 'utc' at time zone 'Etc/GMT-5') END as  ДатаОкончания,
    starttime at time zone 'utc' at time zone 'Etc/GMT-5' AS ДатаНачалаСессии,
    endtime at time zone 'utc' at time zone 'Etc/GMT-5' AS ДатаОкончанияСессии,
    anyconnect.username as login,
    coords as Профиль,
    realipaddr as ВнешнийАдрес,
    assignedipv4 as ВнутреннийАдрес,
    country as Страна,
    city as Город,
    sessionid
FROM generate_series('$Start'::date,'$End'::date,'1 day') s(a)
INNER JOIN anyconnect
    ON s.a BETWEEN date_trunc('day',starttime at time zone 'utc' at time zone 'Etc/GMT-5')
            AND date_trunc('day',endtime at time zone 'utc' at time zone 'Etc/GMT-5') + interval '1 day' - interval '1 second'
LEFT OUTER JOIN user_names on lower(anyconnect.username) = lower(user_names.login)"
.$qw_login.$qw_extip.
"GROUP BY
    orgunit,
    displayname,
    s.a,
    domain,
    starttime,
    endtime,
    coords,
    realipaddr,
    assignedipv4,
    country,
    city,
    sessionid,
    anyconnect.username
ORDER BY
    displayname,
    s.a,
    starttime
;";
?>