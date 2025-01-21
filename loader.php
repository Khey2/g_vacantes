<?php
	if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

	// Database Connection
	include('connection.php');
	
	// Calendar Class
	include('calendar.extension.php');
	include('calendar.php');
	
	// Embed Class
	include('embed.php');
	
	// Formater Class
	include('formater.php');
	
	// Google Maps
	include('maps.class.php');
	
	// Form Parser
	include('formParser.class.php');
	
	$form = new FormParser();
	$form->json = 'includes/form.json';
		
	$cf = $_GET;
	
	// Search
	if(isset($_POST['search']) && strlen($_POST['search']) !== 0)
	{
		$_POST['search'] = str_replace('&', '&amp;', $_POST['search']);
		$_POST['search'] = addcslashes($_POST['search'], "%_");
		$_SESSION['search'] = $_POST['search'];
		$_SESSION['condition'] = "user_id = '".calendar::db_escape($_SESSION['calend_user'])."'" . " AND title LIKE '%".calendar::db_escape($_POST['search'])."%' OR " . "user_id = '".calendar::db_escape($_SESSION['kt_login_id'])."'" . " AND description LIKE '%".calendar::db_escape($_POST['search'])."%'";
	} elseif(isset($_POST['search']) && strlen($_POST['search']) == '') {
		unset($_SESSION['condition']);
		unset($_SESSION['search']);
	}
	
	// Starts the Calendar Class @params 'DB Server', 'DB Username', 'DB Password', 'DB Name', 'Table Name', [$condition]
	if(isset($_POST['filter']) && !strlen($_POST['filter']) !== 0)
	{
		$_POST['filter'] = str_replace('&', '&amp;', $_POST['filter']);
		$filter = $_POST['filter'];
		$_SESSION['filter'] = $filter;
		if($filter == 'all-fields')
		{
			$_SESSION['condition'] = "user_id = '".$_SESSION['calend_user']."'";
		} else {
			$_SESSION['condition'] = "category = '".$filter."'" . " AND user_id = '".$_SESSION['calend_user']."'";	
		}
		$calendar = new calendar(DB_HOST, DB_USERNAME, DB_PASSWORD, DATABASE, TABLE, $_SESSION['condition'], $cf); 
	} elseif(isset($_SESSION['condition']) && strlen($_SESSION['condition']) !== 0) {
		$calendar = new calendar(DB_HOST, DB_USERNAME, DB_PASSWORD, DATABASE, TABLE, $_SESSION['condition'], $cf);
	} else {
		if(basename($_SERVER['PHP_SELF']) !== 'index.php')
		{
			$_SESSION['condition'] = "user_id = '".$_SESSION['calend_user']."'";
			$calendar = new calendar(DB_HOST, DB_USERNAME, DB_PASSWORD, DATABASE, TABLE, $_SESSION['condition'], $cf);	
		} else {
			$calendar = new calendar(DB_HOST, DB_USERNAME, DB_PASSWORD, DATABASE, TABLE, false, $cf);		
		}			
	}
	
	$calendar->uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
		
	// Set Categories
	if(isset($categories))
	{
		$calendar->categories = $categories;
	} else {
		$calendar->categories = array('General');	
	}
	
?>