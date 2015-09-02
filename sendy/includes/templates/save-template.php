<?php include('../functions.php');?>
<?php include('../login/auth.php');?>
<?php 
	//------------------------------------------------------//
	//                      	INIT                       //
	//------------------------------------------------------//
	
	$edit = isset($_GET['edit']) ? $_GET['edit'] : '';
	$template_id = isset($_GET['t']) ? mysqli_real_escape_string($mysqli, $_GET['t']) : '';	
	$template_name = addslashes(mysqli_real_escape_string($mysqli, $_POST['template_name']));
	$html = stripslashes($_POST['html']);
	if(trim($html)=='<html><head></head><body></body></html>') $html = '';
	
	//------------------------------------------------------//
	//                      FUNCTIONS                       //
	//------------------------------------------------------//
	
	if($edit)
	{
		$q = 'UPDATE template SET template_name="'.$template_name.'", html_text="'.addslashes($html).'" WHERE id='.$template_id;
		$r = mysqli_query($mysqli, $q);
		if ($r)
		{
			header('Location: '.get_app_info('path').'/templates?i='.get_app_info('app'));
		}
		else echo 'Unable to save, please try again.';
	}
	else
	{	
		//Insert into campaigns
		$q = 'INSERT INTO template (userID, app, template_name, html_text) VALUES ('.get_app_info('main_userID').', '.get_app_info('app').', "'.$template_name.'", "'.addslashes($html).'")';
		$r = mysqli_query($mysqli, $q);
		if ($r)
		{
			header('Location: '.get_app_info('path').'/templates?i='.get_app_info('app'));
		}
	}
?>