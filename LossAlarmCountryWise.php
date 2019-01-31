<?php
ob_start(); 
session_start();
include("php_var.php");
$link = mysql_connect($hostname,$user,$password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

$db_select=mysql_select_db($database,$link);
if (!$db_select){
   die('Cannot connect to Nextone_cdr_config'); 
}  
if($_SESSION['admin']=="on")
{

?>

<html>
<head>
<title>Loss Alarm</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="Style/ForReportNew.css" rel="stylesheet" type="text/css" />
<link href="Style/Heading.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
//--------------------------DB CONNECTION----------------------------------------
//-------------------------------------------------------------------------------------
include("numberformat.php");

$country=$_GET["country"];

$str = "";


							$strsqldest="SELECT 
							COUNT(Id),
							ROUND(SUM(call_duration2)/60,2),
							call_source_regid,
							inr.Destination,
							call_dest_regid,
							ANI as ANI,
							billable_number as CalledNumber
							FROM 
							Nextone.NextoneCDR,
							Nextone_cdr_config.IngressRateTable inr
							WHERE
							IngressRateTableId=IngressRateId AND 
							(EgressCost-IngressCost) > 0 AND
							Country like '".$country."%' 
							AND call_source_regid NOT LIKE '%test%'
							GROUP BY call_source_regid,call_dest_regid,Destination
							ORDER BY SUM(call_duration2) DESC;";
						//echo $strsqlwholesale;
						
						$resultdest= mysql_query($strsqldest,$link); 
						if (!$resultdest) {
						   die("query failedstrsqlwholesale4: " . mysql_error());
						}						
						print("<div align='center' class='HeadingReport' style='padding-top:5px;padding-bottom:10px'><label>Details of Loss For
					     <font color='red'>".$country."</font></label></div>");
					
						print("<table align='center' class='ForTableReport' cellpadding='2px' cellspacing='0'>");
						print("<tr class='TableHeader'>
							  <th class='TableHeaderMiddle'>Total</th>
							  <th class='TableHeaderMiddle'>Duration</th>
							  <th class='TableHeaderMiddle'>InTrunk</th>
							  <th class='TableHeaderMiddle'>Destination</th>
							  <th class='TableHeaderMiddle'>OutTrunk</th>
							  <th class='TableHeaderMiddle'>Action</th> 
							  </tr>");				
						$intCount=1;
						$count=1;
						while($rowwholesale=mysql_fetch_row($resultdest))
						{
							$Download = "Download CDR";
							
							if($intCount%2!=0)
							{								
								print("<tr class='FirstRow'>");
								print("<td align='center' class='ForTableDataBorderMiddle'>".numformat($rowwholesale[0])."</td>");
								print("<td align='center' class='ForTableDataBorderMiddle'>".numformat(round($rowwholesale[1],2))."</td>");
								print("<td align='center' class='ForTableDataBorderMiddle'>".($rowwholesale[2])."</td>");
								print("<td align='center' class='ForTableDataBorderMiddle'>".($rowwholesale[3])."</td>");
								print("<td align='center' class='ForTableDataBorderMiddle'>".($rowwholesale[4])."</td>");
								print("<td align='center' class='ForTableDataBorderMiddle'><a target='_blank' 
								href=DownloadLossCDR.php?call_source_regid=".$rowwholesale[2]."&call_dest_regid=".$rowwholesale[4].
								"&country=".$country."&destination=".$rowwholesale[3].">".$Download."</a></td>");
								
								
								print("</tr>");
							}
							else
							{
								print("<tr class='SecondRow'>");
								print("<td align='center' class='ForTableDataBorderMiddle'>".numformat($rowwholesale[0])."</td>");
								print("<td align='center' class='ForTableDataBorderMiddle'>".numformat(round($rowwholesale[1],0))."</td>");
								print("<td align='center' class='ForTableDataBorderMiddle'>".($rowwholesale[2])."</td>");
								print("<td align='center' class='ForTableDataBorderMiddle'>".($rowwholesale[3])."</td>");
								print("<td align='center' class='ForTableDataBorderMiddle'>".($rowwholesale[4])."</td>");
								print("<td align='center' class='ForTableDataBorderMiddle'><a target='_blank' 
								href=DownloadLossCDR.php?call_source_regid=".$rowwholesale[2]."&call_dest_regid=".$rowwholesale[4].
								"&country=".$country."&destination=".$rowwholesale[3].">".$Download."</a></td>");
								print("</tr>");
								
							}
							$intCount=$intCount+1;
							
						}
						
					print("<tr class='TableFooter' align='center'>");
					print("</tr>");
					print("</table>\n");
?>
</body>
</html>

<?php
}
else{
header("Location:logout.php");
	}
?>
