<?php
class zKillboard 
{

	private $apizKillboard = "https://zkillboard.com/api/losses/corporationID";
	private $corpLossUrl = null;
	private $sysJson = null;
	private $typJson = null;
	private $cachefile = 'zkillboard-cache.json';

	public function __construct($corpID)
	{
		//Grava a url da corp
		$this->corpLossUrl = $this->apizKillboard . '/' . $corpID . '/';

		//Inicialmente o sistema irá carregar os sistemas diretamente do arquivo JSON, não utilizo banco de dados
		//para evitar a carga desnecessária de dados, e também por complicações na conexão utilizando o HEROKU
		//Preferi utilizar um arquivo JSON para armazenar os dados dos sistemas do EVE que são estáticos
		$systems = file_get_contents("data/systems.json");
		$this->sysJson = json_decode($systems, true);

		//Agora o sistema retorna todos os tipos de itens
		$invs = file_get_contents("data/types.json");
		$this->typJson = json_decode($invs, true);
	}

	public function get($days, $cache = true) 
	{
		$jsonObj = array();

		if (file_exists('cache/' . $this->cachefile))
		{
			$today = new DateTime();
			//Checa o cache do sistema
			$dbDate = new DateTime();
			$dbDate->setTimestamp(filectime('cache/' . $this->cachefile));

			//Realiza a diferença total entre as duas datas
			$interval = $dbDate->diff($today);

			//Apenas segura a diferença de dia
			$minDiff = $interval->format("%i");

			if ($minDiff > 30)
			{
				$jsonObj = $this->processKill();
			}
			else 
			{
				$fileContents = file_get_contents('cache/' . $this->cachefile);
				$jsonObj = json_decode($fileContents, true);
			}
		}
		else 
		{
			$jsonObj = $this->processKill();
		}

		return $this->SerializeData($days, $jsonObj);
	}

	private function SerializeData($days, $jsonData) 
	{
		//Cria o intervalo de datas que será utilizado para retornar um determinado grupo de kills
		$date = new DateTime();
		$startingDate = $date->getTimestamp();
		$date->sub(new DateInterval('P' . $days . 'D'));
		$endingDate = $date->getTimestamp();

		$killLog = array();

		foreach ($jsonData as $a) 
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
						'region' => $this->sysJson[$a['solarSystemID']]['region'],
						'name' => $this->sysJson[$a['solarSystemID']]['system'],
						'sec' => number_format($this->sysJson[$a['solarSystemID']]['security'], 1)
					),
					'time' => $a['killTime'],
					'shipTypeID' => $a['victim']['shipTypeID'],
					'shipName' => $this->typJson[$a['victim']['shipTypeID']],
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

		return $killData;
	}

	private function processKill() 
	{
		//Inicia o cURL
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->corpLossUrl); 
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

		$response = curl_exec($ch);
		$info = curl_getinfo($ch);

		if ($response === false) 
		{
			return false;
		} 
		else 
		{
			//Salva o arquivo no cache
			file_put_contents('cache/' . $this->cachefile, $response);
			//Decodifica o json
			$json = json_decode($response,true);
			return $json;
		}
	}

	public function time_elapsed_string($ptime, $textual = true)
	{
	    $etime = strtotime(gmdate('Y-m-d H:i:s')) - $ptime;

	    if ($etime < 1)
	    {
	        return '0 seconds';
	    }

	    $a = array( 365 * 24 * 60 * 60  =>  'year',
	                 30 * 24 * 60 * 60  =>  'month',
	                      24 * 60 * 60  =>  'day',
	                           60 * 60  =>  'hour',
	                                60  =>  'minute',
	                                 1  =>  'second'
	                );
	    $a_plural = array( 'year'   => 'years',
	                       'month'  => 'months',
	                       'day'    => 'days',
	                       'hour'   => 'hours',
	                       'minute' => 'minutes',
	                       'second' => 'seconds'
	                );

	    foreach ($a as $secs => $str)
	    {
	        $d = $etime / $secs;
	        if ($d >= 1)
	        {
	            $r = round($d);
	            if ($textual) {
	            	return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
	        	} else {
	        		return $r;
	        	}
	        }
	    }
	}



}
?>