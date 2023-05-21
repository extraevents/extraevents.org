- **Create** databases on localhost:8889
  - l_extraevents
  - l_ee_helper
  - l_wca
  - l_ee_export
- **Exec** in DB l_extraevents init_localhost/db_extraevents_init.sql
- **Exec** in DB l_ee_helper exec init_localhost/db_helper_init.sql
- **Exec** in DB l_ee_export exec init_localhost/db_export_init.sql
- **Exec** in DB l_wca exec https://www.worldcubeassociation.org/export/results/WCA_export.sql.zip
- **Exec** in DB l_extravents exec
"INSERT INTO team (id, description) VALUES ('<your WCA ID>', 'localhost_admin')"
- **Copy** init_localhost/localhost.yml in .config_key/localhost.yml
- **Run** CRON functions in url http://<localhost_main_url>/cron
- **Look**  at the errors in .files_tmp/errors/localhost

Example <localhost_main_url> - http://localhost:8882/ee_localhost
