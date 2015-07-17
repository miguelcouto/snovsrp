<?php
//Força a página a prevenir o cache
header("Cache-Control: no-cache, must-revalidate"); 	// HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 		// Date in the past

//Inicialmente o sistema irá carregar os sistemas diretamente do arquivo JSON, não utilizo banco de dados
//para evitar a carga desnecessária de dados, e também por complicações na conexão utilizando o HEROKU
//Preferi utilizar um arquivo JSON para armazenar os dados dos sistemas do EVE que são estáticos
$systems = file_get_contents("data/systems.json");
$sysJson = json_decode($systems, true);

//Agora o sistema retorna todos os tipos de itens
$invs = file_get_contents("data/types.json");
$typJson = json_decode($invs, true);

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
				'system' => array(
					'id' => $a['solarSystemID'],
					'region' => $sysJson[$a['solarSystemID']]['region'],
					'name' => $sysJson[$a['solarSystemID']]['system'],
					'sec' => number_format($sysJson[$a['solarSystemID']]['security'], 1)
				),
				'time' => $a['killTime'],
				'shipTypeID' => $a['victim']['shipTypeID'],
				'shipName' => $typJson[$a['victim']['shipTypeID']],
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