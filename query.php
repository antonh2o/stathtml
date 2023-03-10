<?php
$query = "SELECT
     orgunit as depart,
     displayname as fio,
     (to_char (s.a,'DD.MM.YYYY' )) datesa,
     to_char((CASE WHEN MAX(endtime at time zone 'utc' at time zone 'Etc/GMT-5')>s.a + interval '1 day' - interval '1 second'
             THEN s.a + interval '1 day' - interval '1 second'
             ELSE MAX(endtime at time zone 'utc' at time zone 'Etc/GMT-5') END)
        -(CASE WHEN MIN(starttime at time zone 'utc' at time zone 'Etc/GMT-5')<s.a THEN s.a
              ELSE MIN(starttime at time zone 'utc' at time zone 'Etc/GMT-5') END),'hh24:mi') as length,
    CASE WHEN MIN(starttime at time zone 'utc' at time zone 'Etc/GMT-5')<s.a THEN s.a
        ELSE MIN(starttime at time zone 'utc' at time zone 'Etc/GMT-5') END as datebegin,
    CASE WHEN MAX(endtime at time zone 'utc' at time zone 'Etc/GMT-5')>s.a + interval '1 day' - interval '1 second'
            THEN s.a + interval '1 day' - interval '1 second'
        ELSE MAX  (endtime at time zone 'utc' at time zone 'Etc/GMT-5') END as  dateend,
           to_char (starttime at time zone 'utc' at time zone 'Etc/GMT-5','DD.MM.YYYY hh24:mi') AS sessionbegin,
           to_char (endtime at time zone 'utc' at time zone 'Etc/GMT-5','DD.MM.YYYY hh24:mi') AS sessionend,
    anyconnect.username as login,
    realipaddr as externalip,
    domains.domain AS domain 
FROM generate_series('$Start'::date,'$End'::date,'1 day') s(a)
INNER JOIN anyconnect
    ON s.a BETWEEN date_trunc('day',starttime at time zone 'utc' at time zone 'Etc/GMT-5')
            AND date_trunc('day',endtime at time zone 'utc' at time zone 'Etc/GMT-5') + interval '1 day' - interval '1 second'
LEFT OUTER JOIN user_names on lower(anyconnect.username) = lower(user_names.login)
LEFT JOIN orgunits on user_names.orgunit_id = orgunits.id
LEFT JOIN domains on user_names.domain_id = domains.id
"
.$qw_login.$qw_extip.
"GROUP BY
    orgunit,
    displayname,
    s.a,
    starttime,
    endtime,
    anyconnect.username,
    realipaddr,
    domains.domain
ORDER BY
    orgunit,
    displayname,
    s.a,
    starttime
;";
?>