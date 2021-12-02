SELECT 
    r.competition_id,
    c.name competition_name,
    e.id event_id,
    e.name event_name,
    e.icon_wca_revert,
    r.round_number,
    e.person_count,
    e.scramble,
    e.scramble_tnoodle_format,
    rt.name round_format,
    rt.id round_id,
    f.name format_name,
    f.id format_id,
    f.sort_by format_sort_by,
    f.sort_by_second format_sort_by_second,
    f.solve_count format_solve_count,
    f.extra_count format_extra_count,
    f.cutoff_count format_cutoff_count,
    r.time_limit,
    r.time_limit_cumulative,
    r.cutoff,
    r.competitor_limit,
    r.final,
    r.settings
FROM rounds r
JOIN competitions c on c.id=r.competition_id
JOIN events e on e.id=r.event_id
JOIN round_types rt on rt.id = r.round_type
JOIN formats f on f.id = r.round_format
WHERE competition_id = '@:competition:'
ORDER BY e.name, r.round_number