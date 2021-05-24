SELECT
    r.event_id,
    e.name event_name,
    e.icon_wca_revert,
    r.round_number,
    r.result_type,
    r.result,
    p.id person_id,
    p.name person_name,
    p_cy.iso2 person_country_iso2,
    p_cy.name person_country_name,
    p_ct.name person_continent_name,
    cn.id competition_id,
    cn.name competition_name,
    c_cy.iso2 competition_country_iso2,
    c_cy.name competition_country_name
FROM `records` r
    JOIN `persons` p on p.id = r.person
    JOIN `countries` p_cy on p_cy.id = p.country_id 
    JOIN `continents` p_ct on p_ct.id = p_cy.continent_id 
    JOIN `competitions` cn on cn.id = r.competition_id
    JOIN `countries` c_cy on c_cy.id = cn.country_id 
    JOIN events e on e.id=r.event_id
WHERE '@:region:' = r.region