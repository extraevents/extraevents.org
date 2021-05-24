UPDATE competitions 
SET 
    country_id = '@:country:',
    name = '@:name:',
    city = '@:city:',
    start_date = '@:start_date:',
    end_date = '@:end_date:'
WHERE id = '@:id:' 