select 
(r.event_id is not null) registred,
e.id event_id,
e.name event_name,
e.icon_wca_revert,
e.person_count,
p1.id person1_id,
p2.id person2_id,
p3.id person3_id,
p4.id person4_id,
p1.name person1_name,
p2.name person2_name,
p3.name person3_name,
p4.name person4_name,
r.team_complete,
r.key,
rd.competitor_limit,
coalesce(r_count.count, 0) registred_count,
rd.settings
from rounds rd
join events e on e.id=rd.event_id
left outer join results r 
	on r.competition_id= rd.competition_id
	and r.event_id= rd.event_id
	and r.round_number= rd.round_number
	and '@:person:' in (r.person1,r.person2,r.person3,r.person4)
left outer join persons p1 on p1.id=r.person1 and p1.id!='@:person:'
left outer join persons p2 on p2.id=r.person2 and p2.id!='@:person:'
left outer join persons p3 on p3.id=r.person3 and p3.id!='@:person:'
left outer join persons p4 on p4.id=r.person4 and p4.id!='@:person:'
left outer join (
	select count(*) count, event_id  
	from results r 
	where competition_id = '@:competition:' 
		and round_number=1
		and team_complete
	group by event_id

)r_count on r_count.event_id = rd.event_id
where rd.round_number=1 and rd.competition_id='@:competition:'
order by e.name