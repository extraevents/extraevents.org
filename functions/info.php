<?php

function build_info() {
    info_persons_event();
    info_competitions_event();
    info_countries_competition_event();
    info_countries_person_event();

    info_persons_country();
    info_competitions_country();
    info_world_records_country();
    info_events_competition_country();
}

function info_persons_event() {
    $rows = db::rows("select count(*) count, e.id event_id, e.name event_name ,e.icon_wca_revert
                    from (select distinct person, event_id from ranks) r
                    join events e on e.id=r.event_id
                    group by e.id, e.name, e.icon_wca_revert
                    order by 1 desc
                    limit 5");
    $id = 'persons_event';
    db::exec("DELETE FROM info WHERE id= '$id'");
    foreach ($rows as $r) {
        db::exec("INSERT INTO info (id, count, event_id, event_name,icon_wca_revert )
                VALUES ('$id', '$r->count', '$r->event_id', '$r->event_name', '$r->icon_wca_revert' )");
    }
}

function info_competitions_event() {
    $rows = db::rows("select count(*) count, e.id event_id, e.name event_name ,e.icon_wca_revert
                    from (select distinct competition_id, event_id from ranks) r
                    join events e on e.id=r.event_id
                    group by e.id, e.name, e.icon_wca_revert
                    order by 1 desc
                    limit 5");
    $id = 'competitions_event';
    db::exec("DELETE FROM info WHERE id= '$id'");
    foreach ($rows as $r) {
        db::exec("INSERT INTO info (id, count, event_id, event_name,icon_wca_revert )
                VALUES ('$id', '$r->count', '$r->event_id', '$r->event_name', '$r->icon_wca_revert' )");
    }
}

function info_countries_competition_event() {
    $rows = db::rows("select count(distinct r.country_id) count, e.id event_id, e.name event_name ,e.icon_wca_revert
                    from events e 
                    join (select distinct r.event_id, cn.country_id from ranks r join competitions cn on cn.id=r.competition_id)r
                    on r.event_id=e.id
                    group by e.id, e.name, e.icon_wca_revert
                    order by 1 desc
                    limit 5");
    $id = 'countries_competition_event';
    db::exec("DELETE FROM info WHERE id= '$id'");
    foreach ($rows as $r) {
        db::exec("INSERT INTO info (id, count, event_id, event_name,icon_wca_revert )
                VALUES ('$id', '$r->count', '$r->event_id', '$r->event_name', '$r->icon_wca_revert' )");
    }
}

function info_countries_person_event() {
    $rows = db::rows("select count(distinct r.country_id) count, e.id event_id, e.name event_name ,e.icon_wca_revert
                    from (select distinct person, event_id, country_id from ranks) r
                    join events e on e.id=r.event_id
                    group by e.id, e.name, e.icon_wca_revert
                    order by 1 desc
                    limit 5");
    $id = 'countries_person_event';
    db::exec("DELETE FROM info WHERE id= '$id'");
    foreach ($rows as $r) {
        db::exec("INSERT INTO info (id, count, event_id, event_name,icon_wca_revert )
                VALUES ('$id', '$r->count', '$r->event_id', '$r->event_name', '$r->icon_wca_revert' )");
    }
}

function info_persons_country() {
    $rows = db::rows("select 
                    count(distinct r.person) count, cy.iso2 country_iso2, cy.name country_name
                    from ranks r
                    join countries cy on cy.id=r.country_id
                    group by cy.iso2, cy.name
                    order by 1 desc
                    limit 5");
    $id = 'persons_country';
    db::exec("DELETE FROM info WHERE id= '$id'");
    foreach ($rows as $r) {
        db::exec("INSERT INTO info (id, count, country_iso2, country_name )
                VALUES ('$id', '$r->count', '$r->country_iso2', '$r->country_name' )");
    }
}

function info_competitions_country() {
    $rows = db::rows("select 
                        count(*) count, cy.iso2 country_iso2, cy.name country_name
                        from competitions c
                        join countries cy on cy.id=c.country_id
                        group by cy.iso2, cy.name
                        order by 1 desc
                        limit 5");
    $id = 'competitions_country';
    db::exec("DELETE FROM info WHERE id= '$id'");
    foreach ($rows as $r) {
        db::exec("INSERT INTO info (id, count, country_iso2, country_name )
                VALUES ('$id', '$r->count', '$r->country_iso2', '$r->country_name' )");
    }
}

function info_world_records_country() {
    $rows = db::rows("select 
                    count(*) count, cy.iso2 country_iso2, cy.name country_name
                    from records r
                    join persons p on p.id=r.person
                    join countries cy on cy.id=p.country_id
                    where r.record_type='world'
                    group by cy.iso2, cy.name
                    order by 1 desc
                    limit 5");
    $id = 'world_records_country';
    db::exec("DELETE FROM info WHERE id= '$id'");
    foreach ($rows as $r) {
        db::exec("INSERT INTO info (id, count, country_iso2, country_name )
                VALUES ('$id', '$r->count', '$r->country_iso2', '$r->country_name' )");
    }
}

function info_events_competition_country() {
    $rows = db::rows("select count(distinct r.event_id) count,cy.iso2 country_iso2, cy.name country_name
                        from `ranks` r
                        join competitions c on r.competition_id=c.id
                        join countries cy on cy.id=c.country_id
                        group by cy.iso2, cy.name
                        order by 1 desc
                        limit 5");
    $id = 'events_competition_country';
    db::exec("DELETE FROM info WHERE id= '$id'");
    foreach ($rows as $r) {
        db::exec("INSERT INTO info (id, count, country_iso2, country_name )
                VALUES ('$id', '$r->count', '$r->country_iso2', '$r->country_name' )");
    }
}
