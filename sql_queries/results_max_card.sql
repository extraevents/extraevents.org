SELECT 
    max(card_id) card,
    competition_id competition, 
    event_id event,
    round_number round
FROM `results`
WHERE card_id is not null
GROUP BY event_id, competition_id, round_number