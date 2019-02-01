<?php
class StatusUpload extends AppModel
{
	var $name = "StatusUpload";
	var $useTable = false;
	
	function worklist($database)
	{
		$arrayType = array();
		
		$_SESSION['database'] = $database;
		$strsql = "select WorkId,WorkName,Frequency from Client_Maintainence.Work order by WorkId ASC;";
		$this->changeDataSource($_SESSION['database']);
		$rst = $this->query($strsql);
		
		
		$_SESSION['database'] = $database;
		$strsql2 = "select UserId,UserName from Nextone_cdr_config.Users order by UserId DESC;";
		$this->changeDataSource($_SESSION['database']);
		$rst2 = $this->query($strsql2);
		
		
		$j = 0;
		foreach ($rst as $key => $row)
		{
			$arrayType[$j]['WorkName'] = $row['Work']['WorkName'];
			$arrayType[$j]['Frequency'] = $row['Work']['Frequency'];
			$j++;
		}
		$arrayType[$j]['Users'] = $rst2;
		$array = array("0" => $arrayType , "1" => $rst3);
		return $array;
	}
	
	function insert_data($Date,$WorkName,$Frequency,$UserId,$Status,$database)
	{
		$Date = date('Y-m-d',strtotime($Date));
		
		$_SESSION['database'] = $database;
		
		$sql = "DELETE FROM Client_Maintainence.Aegis_Status WHERE Date='".$Date."' AND WorkId='".$WorkName."'";
		
		$this->changeDataSource($_SESSION['database']);
			
		
		$this->query($sql);
		
		$_SESSION['database'] = $database;

		$sql1 = "INSERT INTO Client_Maintainence.Aegis_Status SET Date='".$Date."', WorkId='".$WorkName."', UserId='".$UserId."', Status='".$Status."'";
		
		//echo $sql1;exit;	
		
		$this->changeDataSource($_SESSION['database']);
		
		
		$this->query($sql1);
		
		if($database != 'server_uscolo_186')
		{
			$_SESSION['database'] = 'server_uscolo_186';
			
			$sql2 = "DELETE FROM Client_Maintainence.Aegis_Status WHERE Date='".$Date."'";
		
		   $this->changeDataSource($_SESSION['database']);
		   
		   $this->query($sql2);
			
		}
		//return "Updated Data";
	}
	
	function find_data($Date,$database)
	{
		$arrayType = array();
		$Date = date('Y-m-d',strtotime($Date));
		
		$_SESSION['database'] = $database;
		
		$sql = "SELECT * FROM Client_Maintainence.Aegis_Status v, Client_Maintainence.Work w WHERE v.WorkId=w.WorkName AND v.Date='".$Date."' ORDER BY w.WorkId ASC;";
		
		$this->changeDataSource($_SESSION['database']);
		
		$rst = $this->query($sql);
		
		//echo "<pre>";
		//print_r($rst); exit;
		$j=0;
		foreach ($rst as $key => $row)
		{
			$arrayType[$j]['Aegis_Status']['Id'] = $row['v']['Id'];
			$arrayType[$j]['Aegis_Status']['Date'] = $row['v']['Date'];
			$arrayType[$j]['Aegis_Status']['WorkId'] = $row['v']['WorkId'];
			$arrayType[$j]['Aegis_Status']['Status'] = $row['v']['Status'];
			$arrayType[$j]['Aegis_Status']['UserId'] = $row['v']['UserId'];
			$j++;
		}
		$array = array("0" => $arrayType);
		return $array;
	}
	
}
?>