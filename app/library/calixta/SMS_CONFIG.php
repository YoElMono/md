<?php
// - Este archivo es su licencia para acceder al servicio remoto de envío de SMS, queda bajo su responsabilidad el uso que le de al mismo y queda estrictamente prohibida su distribución y/o comercialización.
//Estos son los parámetros de configuración, y deberán ser establecidos conforme las instrucciones del personal técnico de Auronix.

define('HOST','www.calixtaondemand.com');
define('PORT',80);
define('TIMEOUT',40);
define('CLIENTE',43130);
define('PASSWORD','13c36a0ab2469b4e2fac0ee8b1bea9636af6094a7a7791ba9f5cba2d25d05d76');
define('USER','eduardo.lopez@witenconsulting.com');

function checkValidSession(){
	//Esta función debe devolver TRUE cuando la sesión actual es válida para envío de SMS, y FALSE en cuanquier otro caso.
	return true;
}
?>
