SELECT 
    id, 
    competition_id competition, 
    event_id event, 
    round_number round
FROM `results`
WHERE card_id is null
ORDER BY id