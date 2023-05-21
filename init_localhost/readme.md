- **Create** databases on localhost:8889
  - l_extraevents
  - l_ee_helper
  - l_wca
  - l_ee_export
- **Exec** init_localhost/db_extraevents_init.sql in DB l_extraevents
- **Exec** init_localhost/db_helper_init.sql in DB l_ee_helper
- **Exec** init_localhost/db_export_init.sql in DB l_ee_export
- **Exec** https://www.worldcubeassociation.org/export/results/WCA_export.sql.zip in DB l_wca
- **Copy** init_localhost/localhost.yml in .config_key/localhost.yml
- **Run** CRON functions in url http://<localhost_main_url>/cron
- **Look** at the errors in .files_tmp/errors/localhost

Example <localhost_main_url> - http://localhost:8882/ee_localhost
