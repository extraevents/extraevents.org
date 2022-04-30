SELECT
    c.id,
    c.city,
    c.start_date,
    c.end_date,
    c.contact,
    c.name,
    countries.name country_name,
    countries.iso2 country_iso2,
    UNIX_TIMESTAMP(c.registration_close) registration_close,
    c.registration_close > current_timestamp registration_open,
    c.status,
    c.nonwca
FROM competitions c 
LEFT OUTER JOIN countries on countries.id=c.country_id 
WHERE lower(c.id) = lower('@:id:')