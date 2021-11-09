select 
    * 
from competition_status 
where competition='@:competition:'
order by timestamp desc