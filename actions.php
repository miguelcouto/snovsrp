<?php
include ('class.mysql.php');
include ('conn.php');

if (isset($_POST['action']) && $_POST['action'] == 'pay') 
{

	DB::insert('eve_payouts', array(
	  'id_zkillboard' 	=> $_POST['killid'],
	  'id_pilot' 		=> $_POST['pilotid'],
	  'pilot_name'		=> $_POST['pilotName'],
	  'ship_type'		=> $_POST['shipType'],
	  'ship_name'		=> $_POST['shipName'],
	  'value_payed' 	=> $_POST['value'],
	  'dt_kill' 		=> $_POST['datekill']
	));

	$output = array('message' => true);
	echo json_encode($output);
}

?>