SELECT
    distinct
    c.id,
    c.city,
    c.start_date,
    c.end_date,
    c.name,
    countries.name country_name,
    countries.iso2 country_iso2,
    c.status,
    GROUP_CONCAT(distinct CONCAT(e.id,',',e.name,',',e.icon_wca_revert) ORDER BY e.name SEPARATOR ';') events
FROM competitions c 
LEFT OUTER JOIN countries 
    ON countries.id=c.country_id 
LEFT OUTER JOIN rounds r on r.competition_id=c.id and r.round_number = 1  
LEFT OUTER JOIN results res 
    on res.competition_id=r.competition_id 
    and res.event_id=r.event_id 
    and res.round_number=r.round_number 
LEFT OUTER JOIN events e on e.id=res.event_id
LEFT OUTER JOIN organizers o on o.competition_id=c.id
WHERE '@:person:' in (res.person1,res.person2,res.person3,res.person4) or '@:person:'=o.person
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