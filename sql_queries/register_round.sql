select 
    count(r.id) register_count,
    rd.competitor_limit competitor_limit
from rounds rd
join results r 
    on r.competition_id = rd.competition_id
    and r.round_number = rd.round_number
    and r.event_id = rd.event_id
    and r.team_complete = 1
where rd.round_number = 1 
	and rd.competition_id='@:competition:' 
	and rd.event_id='@:event:'
group by rd.competitor_limit