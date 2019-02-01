<?php
// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('Start_Time','Connect_Time','Disconnect_Time','Called_Number','ANI','In_Trunk','Out_Trunk','Duration','Cause_Code','Fractional_Call_Duration','Source_IP','Destination_IP','In_Cost','Out_Cost'));

// fetch the data

###################################################DB Connection##############################################
$link = mysql_connect("162.222.23.214:3306",'ageis','@geis2016');
if (!$link) {
	die('Could not connect switch1: ' . mysql_error());
}
$db_select=mysql_select_db("Nextone",$link);
if (!$db_select){
   die('Cannot connect to database'); 
}

$call_source_regid = $_GET['call_source_regid'];
$call_dest_regid = $_GET['call_dest_regid'];
$country = $_GET['country'];
$destination = $_GET['destination'];

$rs = ("SELECT 
 start_time1 AS START_TIME,
 connecttime AS CONNECT_TIME,
 disconnecttime AS DISCONNECT_TIME,
 
billable_number AS CALLED_NUMBER,
 ani AS ANI,
 call_source_regid AS IN_TRUNK,
 call_dest_regid AS OUT_TRUNK,
 
call_duration2 AS Duration,
 isdn_cause_code AS CAUSE_CODE,
 call_duration_fractional AS FRACTIONAL_CALL_DURATION,
 
call_source AS SOURCE_IP , 
 call_dest AS DESTINATION_IP,
 
 IngressCost AS INGRESS_COST,
 
 EgressCost AS EGRESS_COST
from Nextone.NextoneCDR, 
Nextone_cdr_config.IngressRateTable I

WHERE
    I.IngressRateTableId=IngressRateId AND 
    call_source_regid LIKE '%".$call_source_regid."%'
        AND call_dest_regid LIKE '%".$call_dest_regid."%'
        AND I.Country LIKE '%".$country."%'
		AND I.Destination LIKE '%".$destination."%' AND (IngressCost-EgressCost) < 0
        AND call_duration2 > 0;");

$rows = mysql_query($rs);

// loop over the rows, outputting them
while ($row = mysql_fetch_row($rows)) fputcsv($output, $row);


?>


