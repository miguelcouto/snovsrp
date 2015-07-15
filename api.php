<?php
//Força a página a prevenir o cache
header("Cache-Control: no-cache, must-revalidate"); 	// HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 		// Date in the past

//include("sources/classes/class.http.php");

//API do zKillboard 
$urlzKillboard = "https://zkillboard.com/api/losses/corporationID/1275978870/";
$killData = array();


//Cria o intervalo de datas que será utilizado para retornar um determinado grupo de kills
$date = new DateTime();
$startingDate = $date->getTimestamp();
$date->sub(new DateInterval('P'.$_REQUEST['interval'].'D'));
$endingDate = $date->getTimestamp();

//Inicia o cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlzKillboard); 
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

$response = curl_exec($ch);
$info = curl_getinfo($ch);
if ($response === false) 
{
	echo "error: ".curl_error($ch);
}
else
{
	$json = json_decode($response,true);
	
	$killLog = array();
	
	foreach ($json as $a) 
	{
		$dateKill = new DateTime($a['killTime']);
		if ($dateKill->getTimestamp() <= $startingDate && $dateKill->getTimestamp() >= $endingDate) 
		{
			$killInfo = array(
				'id' => $a['victim']['characterID'],
				'name' => $a['victim']['characterName'],
				//'hash' => $a['zkb']['hash'],
				'killID' => $a['killID'],
				'systemID' => $a['solarSystemID'],
				'time' => $a['killTime'],
				'shipTypeID' => $a['victim']['shipTypeID'],
				'killvalue' => $a['zkb']['totalValue'],
			);
			
			//Adiciona a variável
			$killLog[$a['victim']['characterName']][] = $killInfo;
		}
	}
	
	foreach($killLog as $name => $value) 
	{
		$lossSum = 0;
		$lossCount = 0;
		//Realiza a soma das losses
		foreach($value as $x) 
		{
			$lossSum = $lossSum + $x['killvalue'];
			$lossCount++;
		}
		
		$killArranged = array(
			'name' => $name,
			'id' => $value[0]['id'],
			'lossSum' => $lossSum,
			'lossCount' => $lossCount,
			'killinfo' => $value
		); 
		
		$killData[$name][] = $killArranged;
	}
	
	
}
curl_close($ch);

//Executa as opções de filtragem
if (isset($_REQUEST['pilot']))
{
	//print_r($killData[$_REQUEST['pilot']]);
	echo json_encode($killData[$_REQUEST['pilot']]);
} else {
	echo json_encode($killData);
}

?>