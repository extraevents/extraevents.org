SELECT 
    e.id event_id,
    e.name event_name,
    e.icon_wca_revert,
    r_single.result single_result,
    r_single.world_rank single_world_rank,
    r_single.continent_rank single_continent_rank,
    r_single.country_rank single_country_rank,
    r_average.result average_result,
    r_average.world_rank average_world_rank,
    r_average.continent_rank average_continent_rank,
    r_average.country_rank average_country_rank
FROM events e
    LEFT OUTER JOIN ranks r_single 
        ON r_single.event_id = e.id 
        AND r_single.result_type='single'
        AND r_single.person='@:person:'
    LEFT OUTER JOIN ranks r_average 
        ON r_average.event_id = e.id 
        AND r_average.result_type='average'
	AND r_average.person='@:person:'
WHERE r_single.event_id is not null or r_average.event_id is not null
order by e.name

