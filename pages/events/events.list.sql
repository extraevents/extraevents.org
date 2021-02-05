SELECT 
	id, name, long_inspection, person_count, 
	CASE 
		WHEN scramble_tnoodle_format='pdf' THEN scramble_tnoodle_events 
		ELSE '[]' 
	END scramble_tnoodle_events,
	scramble_training,
	icon_wca_revert
FROM events
ORDER BY name