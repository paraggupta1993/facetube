<?php
session_start();
$con = 0;
function check_login(){					
	if(!isset($_SESSION['user'])){
		header('Location:index.php');
		die();
	}

}
function fb_sort( $dArr , $column){
	foreach($dArr as $key => $value){
	  $name[$key] = $dArr['name'];
	}
	array_multisort($name,SORT_ASC,$dArr);
	return $dArr;
}
function fb_generate_list( $arr, $id ,$opt_id , $opt_value , $isnull=false , $sort=false){
	if($sort){
		$arr = fb_sort( $arr , $opt_value);
	}
	$st = "<select multiple='multiple' size='10' name='" . $id . "[]' id='" . $id . "'>";
	if($isnull) $st .= "<option value='' >--Please select--</option>";
	foreach ($arr as $row){
		$st .= "<option value='".$row[$opt_id]."' id='".$row[$opt_id]."'>".$row[$opt_value]."</option>";
	}
	$st .= "</select>";
	return $st;

	

}
function generate_list($q,$x,$isnull = true,$default= false){
	$r = execute($q);
	$i=0;
	$rowcount = mysql_num_rows($r);
	$st = "<select name='" . $x . "' id='" . $x . "'>";
	if($isnull) $st .= "<option value='' >--Please select--</option>";
	while($row = mysql_fetch_array($r,MYSQL_NUM)){
		//if($default!=false && $row[0]==$default){
		//$st .= "<option value='".$row[0]."' id='".$row[0]."' selected>".$row[0]."</option>";
		    
		//  }
	//	else{
		$st .= "<option value='".$row[0]."' id='".$row[0]."'>".$row[0]."</option>";
	//	}
	}
	$st .= "</select>";
	return $st;
}
function generate_timeslot($myid,$default=false){
	$st = "<select name='". $myid . "' id='". $myid."' >";
	$st .= "<option value='' id='fg'>Please select</option>";
	for($i=0;$i<24;$i++){
		$dig=0;
		$k = $i;
		while($k>0){
			$k/=10;
			$dig++;
		}
		$j = $i.'';
		if($i<10){
			$j = '0' . $j;
		}
		$j1 = $j.":00:00";
		$j2 = $j.":30:00";
		$st .= "<option value='" . $j1 . "' name='" . $j1 . "' id='" .$j1."'>".$j1."</option>";
		$st .= "<option value='" . $j2 . "' name='" . $j2 . "' id='" .$j2."'>".$j2."</option>";

	}
		$st .= "<option value='23:59:59' name='23:59:59' id='23:59:59'>23:59:59</option>";

	$st .= "</select>";
	return $st;
}
function execute($q){
	GLOBAL $con;

	if($con == 0 ){
		dbconnect();
	}
	$r = mysql_query($q,$con);
	if(!$r){
		die("Cannot execute the query".$q);
	}
	else{
		return $r;
	}
}
function ex($q){
	GLOBAL $con;

	if($con == 0 ){
		dbconnect();
	}
	return  mysql_query($q,$con);
}

function dbconnect(){
	GLOBAL $con;
	$con = mysql_connect('localhost','root','paragrocks');
	if(!$con){
		die("no connection to database");
	}
	else {
		$q = "use smartfbing_phpfogapp_com";
		$rv = mysql_query( $q , $con);
		if(!$rv){
			die("no database!");
		}
	}
}

function escape($x){
	$x = stripslashes($x);
	$x = mysql_real_escape_string($x);
	return ($x);
}
function make_array($a){
	$all = array();
	while($row = mysql_fetch_array($a))
	{
		$all[] = $row[0];
	}
	return $all;
}
function collision($roomId , $date_s , $date_e , $time_s, $time_e , $Repeat_Type ){  // all parameters are in TIME type. not in string
	
	$query = "SELECT * FROM Booking WHERE
		roomId = $roomId
		AND NOT (Start_Date >'" . date('Y-m-d',$date_e) . "' OR Booking.End_Date < '" . date('Y-m-d',$date_s) . "') AND NOT (Booking.Start_Time >='" . date('H:i:s',$time_e). "' OR Booking.End_Time <='" . date('H:i:s',$time_s). "') ORDER BY Start_Date ,Start_Time;";
//	echo "</br>$query</br>";
//	echo "echoing from essential.php int the collision funtion </br>";
	$result = execute($query);
	$result1 = array();
	$counter = 0;
	while( $row = mysql_fetch_assoc($result) ){
	//	echo "$row" . "$counter" . $row['Repeat_Type'] . "$Repeat_Type" . "<br/>";
		$counter++;
		if( $row['Repeat_Type'] == 'DAILY' &&  $Repeat_Type=='DAILY'){  // collision will alwaz happen ....
			//echo "</br >inside of if condition </br>";
			$result1[] =  $row;
		}
		else if ($row['Repeat_Type']=='DAILY' && $Repeat_Type=='WEEKLY' ){ 
			$row_incrdate = $date_s;
			While(!($row_incrdate <= strtotime($row['End_Date']) && $row_incrdate >= strtotime($row['Start_Date'])) && $row_incrdate <= $date_e)
			{
				//echo date("Y-m-d",$row_incrdate);
				$row_incrdate = strtotime("+7 day",$row_incrdate);
				//echo date("Y-m-d",$row_incrdate);

			}
			if(!($row_incrdate > $date_e)){
				$result1[] = $row;
			}
		}
		else if ($row['Repeat_Type']=='WEEKLY' && $Repeat_Type=='DAILY' ){
			$row_incrdate = strtotime($row['Start_Date']);
			While(!($row_incrdate <= $date_e && $row_incrdate >= $date_s) && $row_incrdate <= strtotime($row['End_Date']))
			{
				//echo date("Y-m-d",$row_incrdate);
				$row_incrdate = strtotime("+7 day",$row_incrdate);
				//echo date("Y-m-d",$row_incrdate);

			}
			if(!($row_incrdate > strtotime($row['End_Date']))){
				$result1[] = $row;
			}

		}
		else if ($row['Repeat_Type']=='WEEKLY' && $Repeat_Type=='WEEKLY' ){
			$row_incrdate = strtotime($row['Start_Date']);
			$collide = 0;
			while($row_incrdate <= strtotime($row['End_Date'])){
				if($row_incrdate == $date_s){ // starting date collides with one of the days 
					$collide = 1;
					break;
				}
				$row_incrdate = strtotime("+7 day" , $row_incrdate );
			}
			if(!$collide){
				$row_incrdate = $date_s ; 
				while( $row_incrdate <= $date_e ){
					if( $row_incrdate == strtotime($row['Start_Time'])){ //starting time collides with one of the days.
						$collide = 1;
						break;
					}
					$row_incrdate = strtotime("+7 day" , $row_incrdate );
				}
			}
			if($collide){ // appending to the result array.
				$result1[] = $row;
			}
		}
	}
	
	return $result1;

}
function delete_mail($booking_id)
{
        $query = "select U.email, U.name, R.roomName, B.Start_Date, B.End_Date, B.Start_Time, B.End_Time, B.Repeat_Type, booking_id from User as U, Room as R, Booking as B where U.userId = B.user and R.roomId = B.roomId and B.booking_id = ".$booking_id.";";
        $r = execute($query);
        $val = mysql_fetch_assoc($r);
        $msg = "Dear ".$val['U.name']."Your Booking for room ".$val['R.roomName']." from the date ".$val['B.Start_Date']." till ".$val['B.End_Date']." for the time period ".$val['B.Start_Time']." till ".$val['B.End_Time']." has been cancelled/rejected. This is an automatically generated mail";
        $to = $val['U.email'];
        $sub = "Booking Cancelled Notification";
        $header = "From: roomreservation@iiit.ac.in";
        $ret = mail($to, $sub, $msg, $header);

	//deleting the booking 
							$del_query = "delete from Booking where booking_id =$booking_id";
							execute($del_query);
        return ret;
}
function confirmation_mail($booking_id){
        $query = "select U.email, U.name, R.roomName, B.Start_Date, B.End_Date, B.Start_Time, B.End_Time, B.Repeat_Type, booking_id from User as U, Room as R, Booking as B where U.userId = B.user and R.roomId = B.roomId and B.booking_id = ".$booking_id.";";
        $r = execute($query);
        $val = mysql_fetch_assoc($r);
        $msg = "Dear ".$val['U.name']."Your Booking for room ".$val['R.roomName']." from the date ".$val['B.Start_Date']." till ".$val['B.End_Date']." for the time period ".$val['B.Start_Time']." till ".$val['B.End_Time']." has been confirmed. This is an automatically generated mail";
        $to = $val['U.email'];
        $sub = "Booking Confirmation Notification";
        $header = "From: roomreservation@iiit.ac.in";
        $ret = mail($to, $sub, $msg, $header);
}

function paginate($file,$myquery,$start=0,$lim=10)
{
//	$lim = 10;
	$query1 = $myquery.";";
	$result=execute($myquery);
	$num = mysql_num_rows($result);
	if($num == 0)
	{
		return $result;
	}
	$back = $start - $lim;
	$next = $start + $lim;
	$query2= $myquery." limit ".$start.",".$lim.";";
	$result2=execute($query2);
	echo mysql_error();
	$index = 1;
	if($num > $lim){		// display links only if records are enuf.
		if($back >=0){
			echo"<a href='".$file."?st=".$back."'>PREV</a>";
		}

		for($i=0;$i<$num;$i=$i+$lim){
			if($i != $start){

			echo" <a href='".$file."?st=".$i."'>";
			echo ' '.$index;
			echo "</a>";
			}
			else{
				echo ' ';
				echo $index;
			}
			$index = $index + 1;
		}
		if($next < $num){
			echo" <a href='".$file."?st=".$next."'> NEXT</a>";
		}
	}
	return $result2;
}
?>

