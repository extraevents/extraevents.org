select 
    * 
from competition_status 
where send_notification is null and status_old <> status_new
order by timestamp