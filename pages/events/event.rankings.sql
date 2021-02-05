
SELECT 
    CASE
            WHEN '@region' = 'world' THEN r.world_rank
            WHEN '@region' = r.country_id THEN r.country_rank
            WHEN '@region' = r.continent_id THEN r.continent_rank
    END
    rank,
    r.result,
    r.attempt1,
    r.attempt2,
    r.attempt3,
    r.attempt4,
    r.attempt5,
    p.id person_id,
    p.name person_name,
    cn.id competition_id,
    cn.name competition_name,
    c_cy.iso2 competition_country_iso2,
    c_cy.name competition_country_name,
    p_cy.iso2 person_country_iso2,
    p_cy.name person_country_name,
    r.round_number
FROM (SELECT * FROM ranks 
        WHERE event_id = '@event_id' 
        AND result_type = '@result_type'
        AND ('@region' = 'world' or '@region' in (country_id, continent_id)))r
    JOIN persons p ON p.id=r.person
    JOIN countries p_cy on p_cy.id=r.country_id
    JOIN competitions cn ON cn.id=r.competition_id
    JOIN countries c_cy on c_cy.id=cn.country_id
ORDER by 1, p.name