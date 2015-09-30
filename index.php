<?php

include ('class.mysql.php');
include ('conn.php');
include ('class.zkillboard.php');

$zKill = new zKillboard(1275978870);
$killData = $zKill->get(20);

?>
<html>
<head>
	<title>Supernova SRP System</title>

	<link href='http://fonts.googleapis.com/css?family=Droid+Sans+Mono' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Bree+Serif' rel='stylesheet' type='text/css'>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" href="sources/css/green.css">

	<script src="https://code.jquery.com/jquery-1.11.3.min.js" type="text/javascript"></script>
	<script src="sources/js/icheck.min.js"></script>
	<style>
		* { padding:0px; margin:0px; }
		body { background:#0F0F0F; width:100%; height:100%; }
		a { color:#FFD633; text-decoration: none; }
		a:hover { color:#FFE16B; }
		ul.pilotTable { list-style: none; border:1px #1C1C1C solid; -webkit-border-radius: 5px; border-radius: 5px; }
		ul.pilotTable li { padding:10px; background:#141414; border-top:1px #3b3b3b dashed !important;  color:#dedede !important; }
		ul.pilotTable li .avatar-block { width:64px;  float:left; }
		ul.pilotTable li .avatar-block img { -webkit-border-radius: 100px; border-radius: 100px; }
		ul.pilotTable li .pilot-info-block { float:left; height:64px; width:250px; margin-left:15px; }
		ul.pilotTable li .pilot-info-block h4 { font-family: 'Roboto', sans-serif; font-size:22px; margin:0px; padding:10px 0px 5px 0px; }
		ul.pilotTable li .pilot-info-block p { font-family: 'Roboto', sans-serif; font-size:12px; }
		ul.pilotTable li .buttons-block { float:left; margin-left:15px; padding-top:15px; }
		ul.pilotTable li .buttons-block i { font-size:22px; }
		ul.pilotTable li a.hotbutton { display:inline-block; background:#FFD633; padding:5px; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color:#000 !important; }
		ul.pilotTable li a.hotbutton:hover { background: #FFE787; }
		ul.pilotTable li .isk-block { float:right; font-size:20px; padding-top:15px; font-family: 'Droid Sans Mono'; color:#87E9FF; }
		ul.pilotTable li .losses-details { border-top:1px #3B3B3B solid; margin-top:15px; /* max-height:300px; overflow: auto; */ }
		ul.print-losses { list-style: none; }
		ul.print-losses li { padding: 10px; border-top:1px #3b3b3b solid !important; font-family: 'Roboto', sans-serif; }

		ul.print-losses li .type-avatar { width:64px; float:left; }
		ul.print-losses li .type-avatar img { -webkit-border-radius: 5px; border-radius: 5px; }
		ul.print-losses li .kill-info { float:left; width:320px; margin-left:5px; }
		ul.print-losses li .kill-info p { font-size:13px; padding:0px; margin:0px; }
		ul.print-losses li .red-box { background:#802F2F; color:#fff; font-size:12px; line-height:16px; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; padding:2px 6px 2px 6px; font-weight:bold; }
		ul.print-losses li .isk-box { float:left; width:150px; margin-left:5px; padding-top:20px; font-size:13px; }
		ul.print-losses li .isk-box .isk-text { color:#45FFE0; font-weight:bold; }
		ul.print-losses li .checkbox-box { float:right; width:120px; margin-left:5px;padding-top:20px; text-align: right; }



		ul.lateral-menu { list-style: none; font-family: 'Roboto', sans-serif; border-top:1px #262626 solid; }
		ul.lateral-menu li a { display: block; padding:10px; border-bottom:1px #262626 dashed; font-size:14px; color:#fff; -webkit-transition: all 500ms ease; -moz-transition: all 500ms ease; -ms-transition: all 500ms ease; -o-transition: all 500ms ease; transition: all 500ms ease; }
		ul.lateral-menu li a:hover { background:#FFD633; color:#000; }
	</style>
	<script>
		$(document).ready(function() {
			$('input[type=checkbox]').iCheck({
				checkboxClass: 'icheckbox_flat-green',
				radioClass: 'iradio_flat-green'
			});

			$('.losses-details').slideUp(0);

			$('.pilotTable').find(".show-loss").click(function() {
				var obj = $(this).parent().parent().find('.losses-details');
				//Fecha tudo
				$('.losses-details').slideUp(500);
				//Abre apenas o que foi selecionado
				if ($(obj).is(":visible")) {
					$(obj).slideUp(500);
				}
				else 
				{
					$(obj).slideDown(500);
				}
			});

			$('.pilotTable').find(".remove-player").click(function() { 
				var obj = $(this).parent().parent();
				$(obj).slideUp(500);
			});

			$('.payinsurance').click(function() { 
				var obj = $(this).parent().parent().find('input[name=valuepaid]');
				var elementIBtn = $(this)

				$.post("actions.php", 
				{ 
					action:"pay", 
					value: $(obj).val(), 
					datekill: $(this).attr('kill-date'), 
					pilotid: $(this).parent().attr('pilot-id'), 
					killid: $(this).attr('kill-id'),
					pilotName: $(this).parent().attr('pilot-name'),
					shipType: $(this).parent().attr('ship-type'), 
					shipName: $(this).parent().attr('ship-name'),
				}
				, function(data) {
					//Informa com um verde de que foi pago
					$(elementIBtn).parent().parent().css('background', '#1C2624');
					//Remove os botões
					$(elementIBtn).hide(500);
					$(elementIBtn).parent().find('.nopay').hide(500);
				});
			});

			$('.nopay').click(function() { 
				$(this).parent().parent().css('background', '#261C1C');
				//Remove os botões
				$(this).hide(500);
			});
		}) 
	</script>
</head>
<body>

	<div style="display:table; table-layout: fixed; width:100%; height:100%;">
		<div style="background:#1A1A1A; border-right:1px #262626 solid; width:250px; display: table-cell; height:100%; vertical-align:top;">
			<div style="color:#fff; text-align:center; padding:30px 0px 30px 0px;">
				<i class="material-icons" style="font-size:150px; color:#FFD633;">star</i>
				<p style="font-family: 'Bree Serif', serif; font-size:30px;">SNOV SRP</p>
			</div>
			<ul class="lateral-menu">
				<li><a href="index.php">Loss Report</a></li>
				<li><a href="index.php">Payouts Report</a></li>
			</ul>
			<?php
			$resultIsk = $mysqli->query("select sum(value_payed) as total_payed from eve_payouts");
			?>
			<div style="border-top:2px #616161 solid; position:fixed; bottom:0; width:250px; padding:10px 0px 10px 0px;">
				<div style="padding:5px;">
					<div style="color:#474747; font-family: 'Roboto', sans-serif; font-size:12px;">Total payouts:</div>
					<div style="color:#FFD633; font-family: 'Droid Sans Mono'; font-size:18px; text-align:right;">ISK <?php echo number_format($resultIsk[0]['total_payed'], 2, ',', '.'); ?></div>
				</div>
			</div>
		</div>

		<div style="display: table-cell; width:100%; height:100px; vertical-align:top;">
			<div style="padding:40px;">
				<div style="background:#FFD633; -webkit-border-radius: 5px 5px 0 0; border-radius: 5px 5px 0 0; padding:5px;">
					<form method="post" action="index.php">
						<span style="font-family: 'Roboto', sans-serif; font-size:12px;">Report Date:</span>
						<select style="padding:5px; border:1px #FFD633 solid; -webkit-border-radius: 3px; border-radius: 3px;" name="daysreport">
							<option value="1">1 Day</option>
							<option value="5">5 Days</option>
							<option value="7">7 Days</option>
							<option value="10">10 Days</option>
							<option value="15">15 Days</option>
							<option value="20">20 Days</option>
							<option value="25">25 Days</option>
							<option value="30">30 Days</option>
						</select>
						<input type="submit" name="send" value="Show Report" />
					</form>
				</div>
				<ul class="pilotTable">
					<?php
					foreach ($killData as $key => $value) 
					{

					$resultIsk = $mysqli->query("select sum(value_payed) as total_payed from eve_payouts where id_pilot = '".$value[0]['id']."';");

					echo '
						<li>
							<div class="avatar-block">
								<a href="https://zkillboard.com/character/' . $value[0]['id'] . '" target="_blank">
									<img src="https://image.eveonline.com/Character/' . $value[0]['id'] . '_64.jpg" width="64px" height="64px">
								</a>
							</div>

							<div class="pilot-info-block">
								<h4><a href="javascript:CCPEVE.showInfo(1377, ' . $value[0]['id'] . ')" class="showDetailsPilot">' . $value[0]['name'] . '</a></h4>
								<p>Total Deaths: ' . $value[0]['lossCount'] . '</p>
							</div>

							<div class="buttons-block">
								<a href="javascript: CCPEVE.sendMail(' . $value[0]['id'] . ')" class="hotbutton"><i class="material-icons">mail</i></a>
								<!--<a href="#" class="hotbutton remove-player"><i class="material-icons">clear</i></a>-->
								<a href="#" class="hotbutton show-loss"><i class="material-icons">inbox</i></a>
							</div>
							
							';
							if ($resultIsk[0]['total_payed'] > 0) {
								echo '
								<div class="isk-block">
									<strong>ISK ' . number_format($resultIsk[0]['total_payed'], 2, ',', '.') . '</strong>
								</div>
								';
							}
							echo '

							<div style="clear:both;"></div>

							<div class="losses-details">
								<ul class="print-losses">
								';

								foreach ($value[0]['killinfo'] as $v) 
								{
									//Registra como data a kill
									$lossDate = new DateTime();
									$lossDate->setTimestamp(strtotime($v['time']));

									//Verifica se a loss é correspondente a um pod
									$isPod = ((int)$v['shipTypeID'] == 670 ? true : false);
									//Verifica se a loss é em HighSec
									$isHighSec = (floatval($v['system']['sec']) > 0.4 ? true : false);
									//Verifica se a loss é de mais de 50 milhões
									$isHighValue = (floatval($v['killvalue']) > 50000000 ? true : false);

									//Verifica se essa loss já foi paga pelo sistema
									$result = $mysqli->query("select * from eve_payouts where id_zkillboard = '".$v['killID']."' and id_pilot = '".$value[0]['id']."'");

									echo '
									<li'.(count($result) > 0 ? ' style="background:#1C2624;"' : '').'>
										<div class="type-avatar">
											<a href="https://zkillboard.com/kill/' . $v['killID'] . '/" target="_blank">
												<img src="https://image.eveonline.com/Type/' . $v['shipTypeID'] . '_64.png">
											</a>
										</div>

										<div class="kill-info">
											<p><strong>Ship:</strong> <a href="javascript:CCPEVE.showPreview(' . $v['shipTypeID'] . ')">' . $v['shipName'] . '</a></p>
											<p>
												<strong>Location:</strong> <span style="color:#A3A3A3;">' . $v['system']['name'] . '/' . $v['system']['region'] . ' (<strong>' . $v['system']['sec'] . '</strong>)</span>
											</p>
											<p><strong>Date:</strong> <span style="color:#A3A3A3;">' . $lossDate->format('d/m/Y H:i:s') . ' (' . $zKill->time_elapsed_string($lossDate->gettimestamp()) . ')</span></p>
											
											' . ( $isPod ? '<span class="red-box">Pod Loss</span>' : '') . '
											' . ( floatval($v['system']['sec']) > 0.4 ? '<span class="red-box">High-Sec</span>' : '') . '
											' . ( floatval($v['killvalue']) > 50000000 ? '<span class="red-box">High-Value</span>' : '') . '
										</div>

										<div class="isk-box">
											<span class="isk-text">ISK ' . number_format($v['killvalue'], 2, ',', '.') . '</span>
											
										</div>

										<div class="isk-box" style="padding-top:7px;">
											<span class="isk-text">Value Paid:</span>
											<input type="textbox" name="valuepaid" style="background:#121212; border:1px #1A1A1A solid; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; padding:5px; color:#8F8F8F;" value="' . (count($result) > 0 ? $result[0]['value_payed'] : ( floatval($v['killvalue']) > 50000000 ? 50000000 : $v['killvalue'])) . '" style="padding:3px;" /> 
										</div>

										';
										if (count($result) == 0) 
										{
											echo '
											<div class="checkbox-box" pilot-id="' . $value[0]['id'] . '" pilot-name="' . $v['name'] . '" ship-type="' . $v['shipTypeID'] . '" ship-name="' . $v['shipName'] . '">
												<a href="#" kill-id="' . $v['killID'] . '" kill-date="' . $lossDate->format('Y-m-d H:i:s') . '" class="payinsurance hotbutton"><i class="material-icons">thumb_up</i></a>
												<a href="#" class="nopay hotbutton"><i class="material-icons">thumb_down</i></a>
												<!--<input type="checkbox"' . (!$isPod && !$isHighSec && !$isHighValue ? ' checked' : '') . '>-->
											</div>
											';
										}
										echo '
										<div style="clear:both;"></div>
									</li>
									';
								}
								echo '
								</ul>
							</div>
						</li>
					';
					}	
					?>
				</ul>

			</div>
		</div>
	</div>

	

</body>
</html>