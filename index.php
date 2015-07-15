<html>
<head>
	<title>Supernova SRP</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
	<script src="https://code.jquery.com/jquery-1.11.3.min.js" type="text/javascript"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="sources/js/jquery.loadTemplate-1.4.5.min.js"></script>
	<script src="sources/js/jquery.number.min.js"></script>
	
	<script type="text/html" id="template-li">
		<div class="avatar-block" class="left">
			<img data-src="PilotPicture" width="64px" height="64px">
		</div>
		<div class="pilot-info-block">
			<h4><a href="#" class="showDetailsPilot" data-content="PilotName"></a></h4>
			<p>Total Deaths: <span data-content="PilotLosses">0</span></p>
		</div>
		<div class="buttons-block">
			<button type="button" class="btn btn-default"><i class="glyphicon glyphicon-envelope"></i></button>
		</div>
		<div class="isk-block">
			<strong>ISK <span data-content="IskTotalLosses">999999999.99</span></strong>
		</div>
		<div style="clear:both;"></div>
		
		<div class="losses-details">
			<ul class="print-losses" style="list-style:none; padding:0px; margin:0px;">

			</ul>
		</div>
	</script>
	
	<script type="text/html" id="template-lossDetail">
		<div class="avatar-block">
			<a data-src="LossZKillBoardLink" target="_blank"><img style="-webkit-border-radius: 5px; border-radius: 5px;" data-src="shipType"></a>
		</div>
		<div class="pilot-info-block">
			<h5><strong>Ship:</strong> Kestrel</h5>
			<h5><strong>Location:</strong> Jita (<strong><span style="color:#48F0C0;">0.9</span></strong>)</h5>
		</div>
		<div style="clear:both;"></div>
	</script>
	
	<script type="text/javascript">
		$(document).ready(function() { 
			
			$.get("api.php",{'interval' : 7}, function(data) { 
				
				var obj = $.parseJSON(data);
				//alert(data);
				$.each(obj, function(key, value) {
					$('.dropPilots').append('<li><a href="#">'+key+'</a></li>');
					
					var html = $('<li class="list-group-item"></li>').loadTemplate($('#template-li'), {
						PilotName : obj[key][0].name,
						PilotPicture : 'https://image.eveonline.com/Character/'+obj[key][0].id+'_64.jpg',
						PilotLosses : obj[key][0].lossCount,
						IskTotalLosses : $.number(obj[key][0].lossSum,2,',','.')
					});
					
					$.each(obj[key][0].killinfo, function(key, value) { 
						
						
						var losshtml = $('<li style="border-bottom:1px #ccc solid; padding:10px;"></li>').loadTemplate($('#template-lossDetail'), {
							LossZKillBoardLink: 'https://zkillboard.com/kill/'+value.killID+'/',
							shipType: 'https://image.eveonline.com/Type/'+value.shipTypeID+'_64.png'
						});
						
						$(html).find('.print-losses').append(losshtml);
						
					});
					
					
					$('.loss-list').append(html)			
					
				});
				
				//Depois de popular a informação no dropdown, ele irá tratar os dados
				$('.dropPilots > li').click(function() { 
					$('.pilotButton').append($(this).html());
				});
			});
			
		});
	</script>
</head>
<body>
	
	<nav class="navbar navbar-default">
		<div class="container">
			<div class="navbar-header">
				<button class="navbar-toggle" data-target=".navbar-collapse" data-toggle="collapse" type="button" data-target=".navbar-ex1-collapse">
						<span class="sr-only">
							Toggle navigation
						</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/"><i class="glyphicon glyphicon-paperclip"></i> Supernova SRP</a></div>
			<nav class="collapse navbar-collapse navbar-ex1-collapse" role="navigtion">
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
				  <button type="button" class="pilotButton btn btn-default">Select Pilot</button>
				  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				  </button>
				  <ul class="dropPilots dropdown-menu"></ul>
				</div>
			
				<div class="btn-group">			
				  <button type="button" class="btn btn-default">Interval</button>
				  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				  </button>
				  <ul class="dropdown-menu">
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
			
			.losses-details { border-top:1px #ccc solid; margin-top:15px; }

			</style>
			
			<ul class="loss-list list-group"></ul>
		</div>
		
	</div>
	
</body>
</html>