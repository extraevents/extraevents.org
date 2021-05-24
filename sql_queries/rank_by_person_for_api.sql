SELECT 
    e.id event_id,
    r.result_type,
    r.result,
    r.world_rank,
    r.continent_rank,
    r.country_rank
FROM events e
    JOIN ranks r ON r.event_id = e.id 
WHERE r.person='@:person:'        
order by e.id, r.result_type 