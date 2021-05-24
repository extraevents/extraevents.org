INSERT INTO persons (id,country_id, name)
VALUES ('@:id:', '@:country:', '@:name:') 
   
ON DUPLICATE KEY
UPDATE country_id = '@:country:', name='@:name:'