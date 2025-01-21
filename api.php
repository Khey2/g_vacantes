<?php

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Credentials: true ");
	header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
	header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");
	
	$API = false;
	
	// sample key (you can create a proper API system for this)
	$key = 'k40d801kfafvui0680f3hl';
	
	if($API)
	{
		if(isset($_GET['key']) && $_GET['key'] == $key)
		{
			if(isset($_GET['action']))
			{
				include('includes/loader.php');
				switch($_GET['action'])
				{
					case "event_description":
						if(isset($_GET['id']))
						{
							$id = intval($_GET['id']);
						} else {
							echo 'Nothing found.';
						}
					break;
					
					case "insert":
						
					break;
					
					case "update":
						
					break;
					
					case "delete":
						
					break;
				}
			} else {
				echo 'Welcome, the API feature still under development.';	
			}
		}
	}
	
?>