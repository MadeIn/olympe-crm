<?
	 //setcookie("GestionCookieUsers","", time() - 3600, "/", $HTTP_SERVER_VARS["HTTP_HOST"], 0);
	 session_unset();
   	 //session_destroy();
	 header("Location:/index.php");
?>