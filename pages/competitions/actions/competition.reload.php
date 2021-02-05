<?php

$args = form::required('wcaid');

#print_r(page::get_object('competition'));
#echo 1;
#exit();
#CheckPostIsset('WCA');
#CheckPostNotEmpty('WCA');
#$WCA = $_POST['WCA'];
#RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings', $WCA);
$wca_competition= new wca_competition($args->wcaid);
$wca_competition->reload();
wca_registrations::get($args->wcaid, true);

#if (!$competition) {
#    HeaderExit();
#}

#DataBaseClass::Query("Select ID from `Competition` where `WCA`='$WCA'");
#$delegates = [];
#foreach ($competition->delegates as $delegate) {
#    $delegates[] = $delegate->wca_id;
#

#$Name = DataBaseClass::Escape($competition->name);
#$City = DataBaseClass::Escape($competition->city);
#$Country = DataBaseClass::Escape($competition->country_iso2);
#$StartDate = DataBaseClass::Escape($competition->start_date);
#$EndDate = DataBaseClass::Escape($competition->end_date);
#$WebSite = DataBaseClass::Escape($competition->website);
#$DelegatesWCA = DataBaseClass::Escape(implode(", ", $delegates));

#if (DataBaseClass::rowsCount() > 0) {
#    $ID = DataBaseClass::getRow()['ID'];
#    DataBaseClass::Query("Update `Competition` set "
#            . "`Name`='$Name',"
#            . "`StartDate`='$StartDate',"
#            . "`EndDate`='$EndDate',"
#            . "`City`='$City',"
#            . "`Country`='$Country',"
#            . "`WebSite`='$WebSite',"
#            . "`DelegateWCA`='$DelegatesWCA',"
#            . "`json`='".json_encode($competition)."'"
#            . " where `WCA`='$WCA'");#
#
#}

#header('Location: ' . $_SERVER['HTTP_REFERER']);
#exit();
form::return();

