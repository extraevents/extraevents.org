select 
p1.id person1_id,
p2.id person2_id,
p3.id person3_id,
p4.id person4_id,
p1.name person1_name,
p2.name person2_name,
p3.name person3_name,
p4.name person4_name,
c1.name person1_country_name,
c2.name person2_country_name,
c3.name person3_country_name,
c4.name person4_country_name,
c1.iso2 person1_country_iso2,
c2.iso2 person2_country_iso2,
c3.iso2 person3_country_iso2,
c4.iso2 person4_country_iso2,
e.id event_id,
e.name event_name,
e.icon_wca_revert,
rd.round_number
from rounds rd
join results r on r.competition_id=rd.competition_id and r.event_id=rd.event_id and r.round_number=rd.round_number
join events e on e.id=r.event_id
left outer join persons p1 on p1.id=r.person1
left outer join persons p2 on p2.id=r.person2
left outer join persons p3 on p3.id=r.person3
left outer join persons p4 on p4.id=r.person4
left outer join countries c1 on c1.id=p1.country_id
left outer join countries c2 on c2.id=p2.country_id
left outer join countries c3 on c3.id=p3.country_id
left outer join countries c4 on c4.id=p4.country_id
where rd.competition_id='@competition_id' and r.team_complete = 1 and rd.round_number = 1
