#!/usr/bin/php -q
<?php
set_time_limit(0);
$param_error_log = '/tmp/errores.log';
$param_debug_on = 1;

require('phpagi.php');
require("definiciones.inc");
$link = mysql_connect(MAQUINA, USUARIO,CLAVE);
mysql_select_db(DB, $link);

$agi = new AGI();
$agi->answer();

$agi->exec("AGI","googletts.agi,\"Bienvenido al sistema de  reservas de citas de la EPS de la universidad de antioquia\",es");
$agi->exec("AGI","googletts.agi,\"ingrese su identificación para continuar\",es");
$id = $agi->get_data("beep",5000,1);
$valor = $id['result'];
$result = mysql_query("SELECT *  FROM persona WHERE id = ".$valor, $link);
$row = mysql_fetch_array($result);
$agi->exec("AGI","googletts.agi,\" su identificación es ". $row['id'] ."\",es");
$opt = "0";

if($row['id']==$valor){
	$agi->exec("AGI","googletts.agi,\"bienvenido ". $row['nombre'] ." al sistema de audio respuesta de admisiones de la eps\",es");	
}
else{
	$agi->exec("AGI","googletts.agi,\"el numero de identificación ". $celuda["result"] ." no es valido\",es");
	sleep(0,3);
	$agi->exec("AGI","googletts.agi,\"por favor vuelva a intentarlo mas tarde\",es");	      	      
	$opt = null;
}
/*
if($opt != null){
	do{
		$opt = null;
		$agi->exec("AGI","googletts.agi,\"marque uno para solicitar una cita\",es");	
		sleep(1);
		$agi->exec("AGI","googletts.agi,\"marque dos para verificar la información de la cita\",es");
		sleep(1);
		$agi->exec("AGI","googletts.agi,\"marque tres para colgar la llamada\",es");

		$numero = $agi->get_data("beep",5000,1);
		$opt = $numero["result"];
		switch($opt){
			case "1":
				$agi->exec("AGI","googletts.agi,\"seleccione el numero del especialita para continuar \",es");
				$result = mysql_query("SELECT * FROM citas WHERE id = ".$id['result'], $link);
				while ($row = mysql_fetch_array($result)){
					if($row['estado']=="aprovado"){
						$agi->exec("AGI","googletts.agi,\"Felicidades usted fue ". $row['estado'] ."\",es"); 
					}else{
						if($row['estado']=="rechazado"){
							$agi->exec("AGI","googletts.agi,\"lo sentimos usted fue ". $row['estado'] ."\",es"); 
						}else{
							$agi->exec("AGI","googletts.agi,\"Su examen esta ". $row['estado'] ."\",es"); 
						}	
					}
					sleep(1);
				}
				do {
					$opc = null;
					$agi->exec("AGI","googletts.agi,\"marque uno para Maria\",es");	
					sleep(1);
					$agi->exec("AGI","googletts.agi,\"marque dos para Hernan\",es");
					sleep(1);
					$agi->exec("AGI","googletts.agi,\"marque tres para Angela\",es");
					sleep(1);
					$agi->exec("AGI","googletts.agi,\"marque cero para devolverse al menú anterior\",es");
					$numero = $agi->get_data("beep",5000,1);
					$opc = $numero["result"];
					switch ($opc) {
						$espec = null;
						case '1':
							$espec = 1;
							break;
						case '2':
							$espec = 2;
						break;
						case '3':
							$espec = 3;
						break;						
						default:
							$opc = null;
						break;
					}
					$agi = ->exec("AGI","googletts.agi,\"ingrese el dia de la cita \",es");
					$numero3 = $agi->get_data("beep",5000,1);
					$dia = $numero3["result"];
					$agi = ->exec("AGI","googletts.agi,\"ingrese la hora de la cita\",es");
					$numero4 = $agi->get_data("beep",5000,1);
					$hora = $numero4["result"];
					$agi = ->exec("AGI","googletts.agi,\"ingrese los minutos de la cita\",es");
					$numero5 = $agi->get_data("beep",5000,1);
					$minutos = $numero5["result"];
				} while ($opc <= 10);
				$fecha= "2018-09-$dia $hora:$minutos:00";
				$id_persona= $row['id'];
				$query = "INSERT INTO citas VALUES ('$id_persona','$fecha','$espec', 'Medicina general', 'centro')";
				if (!mysql_query($query, $link)){
					die('Error: ' . mysql_error());
				  }else{
					mysql_close($link);
				  }
				  $agi->exec("AGI","googletts.agi,\"La cita ha sido asignada correctamente \",es");
				break;

			case "2":
				$result = mysql_query("SELECT * FROM citas WHERE id = ".$id['result'], $link);
				while ($row = mysql_fetch_array($result)){
					$agi->exec("AGI","googletts.agi,\"La información de su cita es \",es");
					$agi->exec("AGI","googletts.agi,\"El día de agendamiento de su cita es ". $row['fecha_hora'] ."\",es");
					$row2 = $row['id_especialista'];
					$result2 = mysql_query("SELECT * FROM especialista WHERE id = ".$row2, $link);
					$agi->exec("AGI","googletts.agi,\"con el especialista ". $row['hora'] ."\",es");
					$agi->exec("AGI","googletts.agi,\"la cita es de tipo ". $row['tipo'] ."\",es");
					$agi->exec("AGI","googletts.agi,\" y el lugar de su cita es ". $row['lugar'] ."\",es");
					sleep(1);
				}
				break;
			case "3":
				$agi->exec("AGI","googletts.agi,\"gracias por utilizar el sistema de audio respuesta de los cursos de la universidad de antioquia\",es");
				$opt = null;

				break;
		}
	}while($opt != null);
}*/
$agi->exec("AGI","googletts.agi,\"fin de la llamada\",es");
?>
