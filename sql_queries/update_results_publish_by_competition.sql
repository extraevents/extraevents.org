UPDATE results 
SET is_publish = true
WHERE competition_id = '@:competition:' 
    AND best > 0
    AND pos > 0 