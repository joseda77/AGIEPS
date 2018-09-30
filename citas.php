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

$agi->exec("AGI","googletts.agi,\"Bienvenido al sistema de  reservas de citas de la EPS de la universidad de antióquia\",es");
$agi->exec("AGI","googletts.agi,\"ingrese su identificación para continuar\",es");
$id = $agi->get_data("beep",5000,10);
$valor = $id['result'];
$result = mysql_query("SELECT *  FROM persona WHERE id = ".$valor, $link);
$row = mysql_fetch_array($result);
$opt = "0";

if($row['id']==$valor){
	$agi->exec("AGI","googletts.agi,\"bienvenido ". $row['nombre'] ." al sistema de citas de la eps universidad de antióquia\",es");	
}
else{
	$agi->exec("AGI","googletts.agi,\"el numero de identificación ". $id["result"] ." no se encuentra en el sistema\",es");
	sleep(0,3);
	$agi->exec("AGI","googletts.agi,\"por favor comuniquese con la universidad de antióquia, hasta luego.\",es");	      	      
	$opt = null;
}

if($opt != null){
	do{
		$opt = null;
		$agi->exec("AGI","googletts.agi,\"Menú principal\",es");
		$agi->exec("AGI","googletts.agi,\"marque uno para solicitar una cita\",es");	
		sleep(1);
		$agi->exec("AGI","googletts.agi,\"marque dos para verificar la información de la cita\",es");
		sleep(1);
		$agi->exec("AGI","googletts.agi,\"marque tres para colgar la llamada\",es");

		$numero = $agi->get_data("beep",5000,1);
		$opt = $numero["result"];
		$agi->exec("AGI","googletts.agi,\"el numero marcado fue". $opt ."\",es");
		switch($opt){
			case "1":
				$agi->exec("AGI","googletts.agi,\"seleccione el numero del especialita para continuar \",es");
				$opc = null;
				$agi->exec("AGI","googletts.agi,\"marque uno para María\",es");	
				sleep(1);
				$agi->exec("AGI","googletts.agi,\"marque dos para Hernán\",es");
				sleep(1);
				$agi->exec("AGI","googletts.agi,\"marque tres para Angela\",es");
				sleep(1);
				$agi->exec("AGI","googletts.agi,\"marque cero para devolverse al menú anterior\",es");
				$numero = $agi->get_data("beep",5000,1);
				$opc = $numero["result"];
				switch ($opc) {
					case "1":
						$espec = 1;
						break;
					case "2":
						$espec = 2;
						break;
					case "3":
						$espec = 3;
						break;
					case "4":
						$opc = null;
						break;
				}
				
				do{
					$agi ->exec("AGI","googletts.agi,\"ingrese el dia de la cita \",es");
					$numero3 = $agi->get_data("beep",5000,2);
					$dia = $numero3["result"];
				}while($dia>31);
				do{
					$agi->exec("AGI","googletts.agi,\"ingrese la hora de la cita en formato de 24 horas\",es");
					$numero4 = $agi->get_data("beep",5000,2);
					$hora = $numero4["result"];
				}while($hora>24);
				
				do {
					$agi->exec("AGI","googletts.agi,\"ingrese los minutos de la cita\",es");
					$numero5 = $agi->get_data("beep",5000,2);
					$minutos = $numero5["result"];
				} while ($minutos>59);		
				$fecha= "2018-09-$dia $hora:$minutos:00";
				$id_persona= $row['id'];
				$agi->exec("AGI","googletts.agi,\"la fecha seleccionada es". $fecha ." la identificación de la persona es ". $id_persona ."\",es");
				$query = "INSERT INTO citas (id_persona,fecha_hora,id_especialista,tipo,lugar) VALUES ( $id_persona, '$fecha' , $espec , 'Medicina general', 'centro');";
				$retval = mysql_query( $query, $link );
				if ($retval==1){
					mysql_close($link);
					  $agi->exec("AGI","googletts.agi,\"La cita ha sido asignada correctamente \",es");
				}else{
					$agi->exec("AGI","googletts.agi,\"Error al programar la cita, por favor intentelo mas tarde \",es");
					$opt=null;
				}
				break;

			case "2":
				$agi->exec("AGI","googletts.agi,\"Usted tiene las siguientes citas agendadas\",es");
				$id_persona= $row['id'];
				$result = mysql_query("SELECT *  FROM citas WHERE id_persona = ".$id_persona, $link);
				while ($rows = mysql_fetch_array($result)) {
					$agi->exec("AGI","googletts.agi,\"Cita con codigo". $rows["id_cita"] ."\",es");
				}
				$agi->exec("AGI","googletts.agi,\"Por favor ingrese el codigo de la cita\",es");
				$cod = $agi->get_data("beep",5000,10);
				$agi->exec("AGI","googletts.agi,\"el numero marcado fue". $cod["result"] ."\",es");
				$codigo = $cod["result"];
				$result = mysql_query("SELECT *  FROM citas WHERE id_cita = ".$codigo, $link);
				while ($row3 = mysql_fetch_array($result)){
					$agi->exec("AGI","googletts.agi,\"La información de su cita es \",es");
					$agi->exec("AGI","googletts.agi,\"El día de agendamiento de su cita es ". $row3['fecha_hora'] ."\",es");
					$especialista = $row3['id_especialista'];								
					$result2 = mysql_query("SELECT nombre  FROM especialista WHERE id = ".$especialista, $link);
					$row2 = mysql_fetch_array($result2);					
					$agi->exec("AGI","googletts.agi,\"el nombre del especialista es ". $row2['nombre'] ."\",es");
					$agi->exec("AGI","googletts.agi,\"la cita es de tipo ". $row3['tipo'] ."\",es");
					$agi->exec("AGI","googletts.agi,\" y el lugar de su cita es ". $row3['lugar'] ."\",es");
					sleep(1);
				}
				break;
			case "3":
				$agi->exec("AGI","googletts.agi,\"gracias por utilizar el sistema de audio respuesta de los cursos de la universidad de antioquia\",es");
				$opt = null;
				break;
		}
	}while($opt != null);
}
$agi->exec("AGI","googletts.agi,\"fin de la llamada\",es");
?>
