UPDATE rounds 
JOIN 
    (SELECT t2.competition_id, t2.event_id, t2.round_number
    FROM (SELECT competition_id,event_id, max(round_number) round_number FROM rounds 
        WHERE competition_id='@:competition:'
        GROUP BY competition_id, event_id) t1 
    JOIN rounds t2 ON t2.competition_id = t1.competition_id
        AND t2.event_id = t1.event_id
        AND t2.round_number = t1.round_number) final_round ON
final_round.competition_id = rounds.competition_id
AND final_round.event_id = rounds.event_id
AND final_round.round_number = rounds.round_number
SET final = 1 