SELECT
    p.id,
    p.name,
    cy.name country_name,
    cy.iso2 country_iso2
    FROM `persons` p 
    LEFT OUTER JOIN `countries` cy ON cy.id = p.country_id
    WHERE LOWER(p.id) = LOWER('@:id:')