<html>
<head>
	<title>Supernova SRP</title>
	
	<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="sources/css/green.css">
	<script src="https://code.jquery.com/jquery-1.11.3.min.js" type="text/javascript"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="sources/js/jquery.loadTemplate-1.4.5.min.js"></script>
	<script src="sources/js/jquery.number.min.js"></script>
	<script src="sources/js/icheck.min.js"></script>
	<script src="sources/js/_.js"></script>
	
	<style>
		body { background:#0F0F0F; }
		.panel-default { 
			border:2px #1F242B solid !important;
		}
		
		.panel-heading {
			background:#242A33 !important; 
			color:#7D96BD !important;
			border-bottom:0px !important;
		}
	</style>
	
</head>
<body>
	
	<nav class="navbar navbar-inverse">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="/"><i class="glyphicon glyphicon-paperclip"></i> Supernova - SRP System</a></div>
			</nav>
		</div>
		</div>
	</nav>
	
	<div class="container">
		
		<div class="panel panel-default">
			<!-- Default panel contents -->
			<div class="panel-heading">
				Filters: 
				
				<div class="btn-group">
					<button class="pilotButton btn btn-warning dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						Select Pilot <span class="caret"></span>
					</button>
					<ul class="dropPilots dropdown-menu" aria-labelledby="dropdownMenu1"></ul>
				</div>
				
				<div class="btn-group">
					<button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						Select Interval <span class="caret"></span>
					</button>
					<ul class="dropInterval dropdown-menu">
						<li><a href="#">1 Day</a></li>
						<li><a href="#">5 Days</a></li>
						<li><a href="#">7 Days</a></li>
						<li><a href="#">30 Days</a></li>
					</ul>
				</div>
			</div>

			<style>
			.list-group li .avatar-block { width:64px; float:left; }
			.list-group li .avatar-block img { -webkit-border-radius: 100px; border-radius: 100px; }
			.list-group li .pilot-info-block { float:left; height:64px; width:250px; margin-left:15px; }
			.list-group li .pilot-info-block h4 { margin:0px; padding:10px 0px 5px 0px; }
			.list-group li .pilot-info-block p { font-size:12px; }
			
			.list-group li .buttons-block { float:left; margin-left:15px; padding-top:15px; }
			.list-group li .buttons-block i { font-size:22px; }
			
			.list-group li .isk-block { float:right; height:64px; font-size:20px; padding-top:15px; font-family: 'Lato', sans-serif; }
			
			.losses-details { border-top:1px #3B3B3B solid; margin-top:15px; }

			</style>
			
			<ul class="loss-list list-group"></ul>
		</div>
		
	</div>
	
	<script type="text/html" id="template-li">
		<div class="avatar-block" class="left">
			<a data-href="pilotzkillboard" target="_blank"><img data-src="PilotPicture" width="64px" height="64px"></a>
		</div>
		<div class="pilot-info-block">
			<h4><a href="#" class="showDetailsPilot" data-content="PilotName"></a></h4>
			<p>Total Deaths: <span data-content="PilotLosses">0</span></p>
		</div>
		<div class="buttons-block">
			<button type="button" class="btn btn-warning"><i class="glyphicon glyphicon-envelope"></i></button>
		</div>
		<div class="isk-block">
			<strong>ISK <span id="totalToPay" data-content="IskTotalLosses"></span></strong>
		</div>
		<div style="clear:both;"></div>
		
		<div class="losses-details">
			<ul class="print-losses" style="list-style:none; padding:0px; margin:0px;">
				
			</ul>
		</div>
	</script>
	
	<script type="text/html" id="template-lossDetail">
		<div style="width:64px; float:left;">
			<a data-href="LossZKillBoardLink" target="_blank"><img style="-webkit-border-radius: 5px; border-radius: 5px;" data-src="shipType"></a>
		</div>
		<div style="float:left; height:64px; width:450px; margin-left:5px;">
			<p style="font-size:13px; padding:0px; margin:0px;"><strong>Ship:</strong> <span data-content="ShipName"></span></p>
			<p style="font-size:13px; padding:0px; margin:0px;">
				<strong>Location:</strong> 
				<span data-content="SystemName"></span>/<span data-content="RegionName"></span>
				(<strong><span data-content="SecStatus"></span></strong>)
				<span class="HighSecKill" style="background:#802F2F; color:#fff; line-height:16px; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; padding:2px 6px 2px 6px; font-weight:bold;">High-Sec</span>
			</p>
			<p style="font-size:13px; padding:0px; margin:0px;"><a data-href="LossZKillBoardLink" target="_blank">[KILL-LINK]</a></p>
		</div>
		<div style="float:left; height:60px; width:250px; margin-left:5px; padding-top:20px; font-size:13px;">
			<span data-content="IskLoss" style="color:#45FFE0; font-weight:bold;"></span>
			<span class="HighValueKill" style="background:#802F2F; color:#fff; line-height:16px; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; padding:2px 6px 2px 6px; font-weight:bold;">High-Value</span>
		</div>
		<div style="float:right; height:60px; width:20px; margin-left:5px;padding-top:20px;">
			<input type="checkbox" data-value="iskLossCheck" checked>
		</div>
		<div style="clear:both;"></div>
	</script>
	
</body>
</html>