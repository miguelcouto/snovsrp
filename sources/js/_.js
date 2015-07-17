$(document).ready(function() { 			
	$.get("api.php",{'interval' : 7}, function(data) { 
		executeData(data);
	});
	
	$('.dropInterval > li').click(function() { 
		$('.loss-list').empty();
		$.get("api.php",{'interval' : $(this).find('a').html().replace("Day","").replace("s", "").trim()}, function(data) {
			executeData(data);
		});
	});
	
	var executeData = function(data) {
		var obj = $.parseJSON(data);
				$('.dropPilots').empty();
				$('.dropPilots').append('<li><a href="#">All Pilots</a></li>');
				//Percorre todos os pilotos
				$.each(obj, function(key, value) {
					//Cria a lista de pilotos
					$('.dropPilots').append('<li><a href="#">'+key+'</a></li>');
					
					var html = $('<li pilotname="'+obj[key][0].name+'" class="list-group-item" style="background:#222222; border-top:1px #3B3B3B dashed !important; color:#DEDEDE !important;"></li>').loadTemplate($('#template-li'), {
						PilotName : obj[key][0].name,
						pilotzkillboard: 'https://zkillboard.com/character/' + obj[key][0].id,
						PilotPicture : 'https://image.eveonline.com/Character/' + obj[key][0].id + '_64.jpg',
						PilotLosses : obj[key][0].lossCount,
						IskTotalLosses : $.number(obj[key][0].lossSum,2,',','.')
					});
					
					var totalPayed = 0;
					//Percorre todas as kills
					$.each(obj[key][0].killinfo, function(key, value) { 
						
						var losshtml = $('<li style="border-top:1px #303030 solid; padding:10px;"></li>').loadTemplate($('#template-lossDetail'), {
							LossZKillBoardLink: 'https://zkillboard.com/kill/' + value.killID + '/',
							shipType: 'https://image.eveonline.com/Type/' + value.shipTypeID + '_64.png',
							SystemName: value.system.name,
							SecStatus: value.system.sec,
							RegionName: value.system.region,
							ShipName: value.shipName + ' (' + value.shipTypeID + ')',
							IskLoss: 'ISK ' + $.number(value.killvalue,2,',','.'),
							iskLossCheck: value.killvalue
						});
						
						//Realiza algumas verificações
						
						//Primeiramente é necessário saber onde ocorreu a kill do piloto, se foi em HS, LS ou NS, dependendo das operações realizadas
						//pela Corp
						
						if (value.system.sec > 0.4) 
						{
							//Exibe o alerta de kill em HS
							$(losshtml).find('.HighSecKill').show();
							//Desmarca o checkbox em caso de kills em HS
							$(losshtml).find('input[type=checkbox]').prop('checked', false);
							
							//Verifica se a kill ultrapassa o valor de 50 milhões
							if (value.killvalue > 50000000) 
							{
								$(losshtml).find('.HighValueKill').show();
							} 
							else 
							{
								$(losshtml).find('.HighValueKill').hide();
							}
							
						} 
						else 
						{
							//Esconde o alerta de kill em HS
							$(losshtml).find('.HighSecKill').hide();
							//Marca a opção da checkbox em caso de kills em LS, requer avaliações de outras situações
							$(losshtml).find('input[type=checkbox]').prop('checked', true);
							
							//Verifica se a kill do jogador é algo como capsula(670), MTU(33475)
							if (value.shipTypeID == 670 || value.shipTypeID == 33475) 
							{
								$(losshtml).find('.HighValueKill').hide();
								//Desmarca a checkbox
								$(losshtml).find('input[type=checkbox]').prop('checked', false);
								
								//Verifica se a kill ultrapassa o valor de 50 milhões
								if (value.killvalue > 50000000) 
								{
									$(losshtml).find('.HighValueKill').show();
								} 
								else 
								{
									$(losshtml).find('.HighValueKill').hide();
								}
							}
							else 
							{
								//Verifica se a kill ultrapassa o valor de 50 milhões
								if (value.killvalue > 50000000) 
								{
									$(losshtml).find('.HighValueKill').show();
									$(losshtml).find('input[type=checkbox]').prop('checked', false);
								} 
								else 
								{
									$(losshtml).find('.HighValueKill').hide();
									$(losshtml).find('input[type=checkbox]').prop('checked', true);
									
									totalPayed = totalPayed + value.killvalue;
								}
							}
							
						}

						//Demarca os elementos para modificar o estilo CSS das checkbox
						$('input[type=checkbox]').iCheck({
							checkboxClass: 'icheckbox_flat-green',
							radioClass: 'iradio_flat-green'
						});
						
						$(losshtml).find('.icheckbox_flat-green').click(function() { 
							alert("teste");
						});
						
						$('input[type=checkbox]').on('ifChanged', function(event){
							//Aqui é marcado o objeto LI para referência futura
							var liObj = $(this).parent().parent().parent().parent().parent().parent();
							var sumValue = 0;
							$.each($(liObj).find('input[type=checkbox]'), function(index) {
								if ($(this).is(':checked')) {
									//alert($(this).val());
									sumValue = parseFloat(sumValue) + parseFloat($(this).val());
									console.log(sumValue);
								}
							});
							
							//Agora ele imprime o valor diretamente na DIV responsável
							$(liObj).find('#totalToPay').html($.number(sumValue,2,',','.'));
						});
						
						//Adiciona o elemento
						$(html).find('.print-losses').append(losshtml);
								
					});
					
					//Realiza as contas
					$(html).find('#totalToPay').html($.number(totalPayed,2,',','.'));
					//Cria a loss-list completa
					$('.loss-list').append(html);
					
					$(html).find('.losses-details').slideUp(0);
					
					$(html).find('.showDetailsPilot').click(function() {
						if ($(this).parent().parent().parent().find('.losses-details').is(":hidden")) 
						{
							$(this).parent().parent().parent().find('.losses-details').slideDown(500);
						} 
						else 
						{
							$(this).parent().parent().parent().find('.losses-details').slideUp(500);
						}
						
					});
					
				});
				
				//Limpa todos os binds primeiro
				('.dropPilots > li').unbind();
				//Depois de popular a informação no dropdown, ele irá tratar os dados
				$('.dropPilots > li').click(function() { 
					$('.pilotButton').html($(this).find('a').html() + ' <span class="caret"></span>');
					
					if ($(this).find('a').html() != "All Pilots") 
					{
						var pilotToFix = $(this).find('a').html();
						$.each($('.list-group-item'), function(index) { 
							if ($(this).attr('pilotname') != pilotToFix) {
								$(this).slideUp(500);
							} else {
								$(this).slideDown(500);
							}
						});
					} 
					else 
					{
						$.each($('.list-group-item'), function(index) { 
							$(this).slideDown(500);
						});
					}
				});
				

	}
	
});