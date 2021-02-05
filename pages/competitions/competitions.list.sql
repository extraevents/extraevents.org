SELECT
    c.id,
    c.city,
    c.start_date,
    c.end_date,
    c.name,
    countries.name country_name,
    countries.iso2 country_iso2,
    c.status,
    GROUP_CONCAT(CONCAT(e.id,',',e.name,',',e.icon_wca_revert) ORDER BY e.name SEPARATOR ';') events
FROM competitions c 
LEFT OUTER JOIN countries 
    ON countries.id=c.country_id 
LEFT OUTER JOIN rounds r on r.competition_id=c.id and r.round_number=1
LEFT OUTER JOIN events e on e.id=r.event_id
group by 
c.id,
c.city,
c.start_date,
c.end_date,
c.name,
countries.name,
countries.iso2,
c.status
ORDER BY 
    start_date DESC,
    end_date DESC