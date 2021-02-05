SELECT
    p.id,
    p.name,
    t.contacts,
    t.is_leader,
    t.description,
    p.country_id,
    c.iso2 country_iso2,
    c.name country_name
FROM team t 
	JOIN persons p ON t.id = p.id
	JOIN countries c on c.id = p.country_id
ORDER BY p.name