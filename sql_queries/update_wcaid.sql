DELETE FROM persons 
WHERE id = '@:wca_id:'
;
UPDATE persons 
SET id = '@:wca_id:'
WHERE id='@:ee_id:'
;
UPDATE ranks 
SET person = '@:wca_id:'
WHERE person='@:ee_id:'
;
UPDATE records 
SET person = '@:wca_id:'
WHERE person='@:ee_id:'
;
UPDATE results 
SET person1 = '@:wca_id:'
WHERE person1='@:ee_id:'
;
UPDATE results 
SET person2 = '@:wca_id:'
WHERE person2='@:ee_id:'
;
UPDATE results 
SET person3 = '@:wca_id:'
WHERE person3='@:ee_id:'
;
UPDATE results 
SET person4 = '@:wca_id:'
WHERE person4='@:ee_id:'
