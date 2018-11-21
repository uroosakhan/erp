<?php
echo '<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">';

  // echo "<link href='$path_to_root/themes/grayblue/all_css/style.css' rel='stylesheet' type='text/css'> \n";
   echo "<link href='$path_to_root/themes/premium/all_css/bootstrap.min.css' rel='stylesheet' type='text/css'> \n";
   
   echo "<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'> \n";
   echo "<link href='https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css' rel='stylesheet' type='text/css'> \n";
   echo "<link href='$path_to_root/themes/premium/all_css/AdminLTE.min.css' rel='stylesheet' type='text/css'> \n";
 
  echo "<link href='$path_to_root/themes/premium/all_css/blue.css' rel='stylesheet' type='text/css'> \n";
   

?>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.12&appId=197955696987063&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<?php

	if (!isset($path_to_root) || isset($_GET['path_to_root']) || isset($_POST['path_to_root']))
		die(_("Restricted access"));
	include_once($path_to_root . "/includes/ui.inc");
	include_once($path_to_root . "/includes/page/header.inc");

	$js = "<script language='JavaScript' type='text/javascript'>
function defaultCompany()
{
	document.forms[0].company_login_name.options[".$_SESSION["wa_current_user"]->company."].selected = true;
}
</script>";
	add_js_file('login.js');
	
	
	// Display demo user name and password within login form if "$allow_demo_mode" is true
	if ($allow_demo_mode == true)
	{
	    $demo_text = _("Login as user: demouser and password: password");
	}
	else
	{
		$demo_text = _("Please login here");
	}
	if (!isset($def_coy))
		$def_coy = 0;
	$def_theme = "default";

	$login_timeout = $_SESSION["wa_current_user"]->last_act;

	$title = $login_timeout ? _('Authorization timeout') : $app_title."Hisaab.pk "._("Accounting Software and ERP Login");
	$encoding = isset($_SESSION['language']->encoding) ? $_SESSION['language']->encoding : "UTF-8";
	$rtl = isset($_SESSION['language']->dir) ? $_SESSION['language']->dir : "ltr";
	$onload = !$login_timeout ? "onload='defaultCompany()'" : "";

	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
	echo "<html dir='$rtl' >\n";
	echo "<head><title>$title</title>\n";
   	echo "<meta http-equiv='Content-type' content='text/html; charset=$encoding' />\n";
//	echo "<link href='$path_to_root/themes/$def_theme/default.css' rel='stylesheet' type='text/css'> \n";
//	echo "<link href='$path_to_root/themes/$def_theme/login.css' rel='stylesheet' type='text/css'> \n";
	
	send_scripts();
	if (!$login_timeout)
	{
		echo $js;
	}
	echo "</head>\n";

	echo "<body class='hold-transition login-page' style='height:0 auto; id='loginscreen' $onload'>";
   
      echo"<div class='login-box' style='margin-top:0px;'>";
      
          echo"<div class='login-logo'>";
 // echo"<img src='$path_to_root/themes/premium/images/hisaab_logo_new.png' class='' height='' width=''>";         
         // echo"<a href='#' style='color:#3c8dbc'><b>Hisaab</b>.PK</a>";
          echo"</div>";//<!-- /.login-logo -->
//echo"<center style='' >HisaaB.PK-Pakistan's Premium<b> Web Based </b>Accounting Solution<br /><br /></center>" ;
	if ($login_timeout) { // DYS logo
		echo "<div id='atimeout' align='center'><font size='5' color='red'><b>" . _("Authorization timeout") . "<b></font></div>";
	}

	if (isset($_GET['islogin']) && ($_GET['islogin'] =='0'))
		echo "<div id='failed' align='center'><font size='5' color='red'><b>" . _("Incorrect Password") . "<b></font></div>";
	
    echo"<div class='login-box-body' style='margin-top:0;'>";
      /* echo"<p class='login-box-msg'>Sign in Here</p>"; */	
       
         echo "<form class='' method='post' action='". $_SESSION['timeout']['uri']."' name='loginform'>";
    //	echo "<label>" . _("User name:") . "</label>";
//	echo "<input type='text' id='user_name_entry_field' name='user_name_entry_field' tabindex='1' required>";
           echo"<div class='form-group has-feedback'>";
                echo"<input type='text' id='user_name_entry_field' name='user_name_entry_field' tabindex='1' required class='form-control' placeholder='Enter user name.'>";
                
                echo"<span class='glyphicon glyphicon-user form-control-feedback'></span>";
        echo '<small><i>User: test</i>';
           echo"</div>";

   //echo "<label>" . _("Password:") . "</label>";
	//echo "<input type='password' id='password' name='password' tabindex='2' required>"; 
           
           echo"<div class='form-group has-feedback'>";
                echo"<input type='password' id='password' name='password' tabindex='2' required class='form-control' placeholder='Enter password.'>";
                echo"<span class='glyphicon glyphicon-lock form-control-feedback'></span>";
        echo '<i>Password: 1234</i>';
           echo"</div>";

  //  echo "<fieldset class='boxBody'>";
    
//	echo "<label>" . _("User name:") . "</label>";
//	echo "<input type='text' id='user_name_entry_field' name='user_name_entry_field' tabindex='1' required>";
//	echo "<label>" . _("Password:") . "</label>";
//	echo "<input type='password' id='password' name='password' tabindex='2' required>";
        	
        	if (isset($_SESSION['wa_current_user']->company))
        		$coy =  $_SESSION['wa_current_user']->company;
        	else
        		$coy = $def_coy;

 /*       echo"<div class='form-group'>";        
              echo"<select name='company_login_name' tabindex='3' class='form-control select2'>\n";
        	for ($i = 0; $i < count($db_connections); $i++)
        		echo "<option value=$i ".($i==$coy ? 'selected':'') .">" . $db_connections[$i]["name"] . "</option>";
    	   echo "</select>\n";
        echo"</div>";
*/

//dz 16.6.17
if ($AllowCompanySelectionBox) {
            echo"<div class='form-group'>";        
              echo"<select name='company_login_name' tabindex='3' class='form-control select2'>\n";
        	for ($i = 0; $i < count($db_connections); $i++)
        		echo "<option value=$i ".($i==$coy ? 'selected':'') .">" . $db_connections[$i]["name"] . "</option>";
    	   echo "</select>\n";
        echo"</div>";
        } 
else {
        //    text_row(_("Company"), "company_login_nickname", "", 20, 30);
	echo "<label>" . _("") . "</label>";
          echo"<div class='form-group has-feedback'>"; //dz
	echo "<input type='text' id='company_login_nickname' name='company_login_nickname' tabindex='3' required class='form-control' placeholder='Enter company code.'>";
                echo"<span class='glyphicon glyphicon-home form-control-feedback'></span>"; //dz
                       echo '<i>Company Code: DEMO</i></small>';

           echo"</div>"; //dz



        }
             /*echo"<div class='form-group'>";
                echo"<label>Select Company</label>";
                 echo"<select class='form-control select2' style='width: 100%;'>";
                    for ($i = 0; $i < count($db_connections); $i++)
                    echo "<option value=$i ".($i==$coy ? 'selected':'') .">" . $db_connections[$i]["name"] . "</option>";
                 echo "</select>";
              echo"</div>";*///<!-- /.form-group -->          
        	//echo "<label></label>
            
            echo"<div class='row'>";
               echo"<div class='col-sm-12 col-lg-12 col-xs-12'>";
               
             //  echo"<div class='col-sm-4 col-lg-4 col-xs-12'>";
                     echo "<input type='submit' class='btn btn-primary btn-block btn-flat col-sm-4 col-lg-4 col-xs-12' tabindex='4' value='&nbsp;&nbsp;"._("Login")."&nbsp;&nbsp;' name='SubmitUser'"
		             .($login_timeout ? '':" onclick='set_fullmode();'")." />\n";
             // echo"</div>";
              
            //  echo"<div class='col-sm-8 col-lg-8 col-xs-12'>";
            //    echo "<a href='$path_to_root/modules/mobile/index.html' class='btn btn-primary btn-block btn-flat col-sm-8 col-lg-8 col-xs-12' tabindex='5' >click here for mobile version</a>";
            //  echo"</div>";
              
              echo"</div>";//<!-- /.col -->
               
            echo"</div>";
        echo"</form>";
        echo "<input type='hidden' id=ui_mode name='ui_mode' value='".$_SESSION["wa_current_user"]->ui_mode."' />";
        //   echo"<center><a href='http://hisaab.pk'>Click Here</a> to Visit Our Website</center>";

            echo"Phone : 021-34330999 /34330907 
            
            ";
        echo '<div class="fb-like" data-href="https://www.facebook.com/HisaabErp/" data-layout="button_count" data-action="like" data-size="small" data-show-faces="true" data-share="false"></div>';           
           // echo"<a href='Register.html' class='text-center'>Register a new membership</a>";
            foreach($_SESSION['timeout']['post'] as $p => $val)
             {
        		// add all request variables to be resend together with login data
        		if (!in_array($p, array('ui_mode', 'user_name_entry_field', 
        			'password', 'SubmitUser', 'company_login_name'))) 
        			echo "<input type='hidden' name='$p' value='$val'>";
	         }
	         
    echo"</div>";//<!-- /.login-box-body -->
   
   echo "<div>";
        echo"<center><b>Note :</b><span style='font-size:12px;color:#3c8dbc'> For an optimum display please use Firefox, Chrome or Safari</span>";
        echo"<center>Copyright &copy; 2018<a href='http://hisaab.pk' target=_blank bstyle='color:#019BF5;'> Hisaab.pk Accounting Software and ERP</a></center>";
      echo'  
      <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
.fa {
  padding: 14px;
  font-size: 15px;
  width: 40px;
  text-align: center;
  text-decoration: none;
  margin: 5px 2px;
  border-radius: 50%;
}

.fa:hover {
    opacity: 0.7;
}

.fa-facebook {
  background: #3B5998;
  color: white;
}

.fa-facebook-messenger {
  background: #3B5998;
  color: white;
}

.fa-twitter {
  background: #55ACEE;
  color: white;
}

.fa-whatsapp {
  background: #25d366;
  color: white;
}

.fa-linkedin {
  background: #007bb5;
  color: white;
}

.fa-youtube {
  background: #bb0000;
  color: white;
}

.fa-skype {
  background: #00aff0;
  color: white;
}

.fa-android {
  background: #a4c639;
  color: white;
}

</style>
</head>
<body>

            <!-- Add font awesome icons -->
                <a href="https://api.whatsapp.com/send?phone=923008115737&text=Hi Hisaab.pk Support i want to ask that" class="fa fa-whatsapp" target="blank"></a>
                <a href="https://m.me/HisaabErp" class="fa fa-facebook" target="blank"></a>                
                <a href="skype:hisaab.pk?call" class="fa fa-skype" target="blank"></a>                
                <a href="https://www.youtube.com/c/HisaabERP" class="fa fa-youtube" target="blank"></a>
				<!--<a href="https://www.facebook.com/HisaabErp/" class="fa fa-facebook" target="blank"></a> -->
                <a href="https://twitter.com/HisaabERP" class="fa fa-twitter" target="blank"></a>                
                <!--
                <a href="https://play.google.com/store/apps/details?id=com.Hisaabpk" class="fa fa-android" target="blank"></a>
                -->

      ';
    echo"</div>";
    
    echo"</div>";//<!-- /.login-box -->
    
    //<select name='company_login_name' tabindex='3' class='txtCombobox'>\n";
   // 	for ($i = 0; $i < count($db_connections); $i++)
    //		echo "<option value=$i ".($i==$coy ? 'selected':'') .">" . $db_connections[$i]["name"] . "</option>";
	//echo "</select>\n";	
	
   // echo "</fieldset>";
    
/*	echo "<footer>";
	echo "<input type='submit' class='btnLogin' tabindex='4' value='&nbsp;&nbsp;"._("Login")."&nbsp;&nbsp;' name='SubmitUser'"
		.($login_timeout ? '':" onclick='set_fullmode();'")." />\n";
echo"footer here coming ";
	echo "</footer>";
    
	echo "<input type='hidden' id=ui_mode name='ui_mode' value='".$_SESSION["wa_current_user"]->ui_mode."' />\n";

	foreach($_SESSION['timeout']['post'] as $p => $val) {
		// add all request variables to be resend together with login data
		if (!in_array($p, array('ui_mode', 'user_name_entry_field', 
			'password', 'SubmitUser', 'company_login_name'))) 
			echo "<input type='hidden' name='$p' value='$val'>";
	}*/

 ?>
<!-- <br>
<center> <a href='/modules/mobile/index.html'>Click here for Mobile Version </a> </center>
-->
<?php
	
	//echo "<link href='$path_to_root/modules/mobile/index.html'>";
	end_form(1);
	$Ajax->addScript(true, "document.forms[0].password.focus();");



   /* echo "<script language='JavaScript' type='text/javascript'>
    //<![CDATA[
            <!--
            document.forms[0].user_name_entry_field.select();
            document.forms[0].user_name_entry_field.focus();
            //-->
    //]]>
    </script>";*/
   
 div_end();
 
 echo "<script src='$path_to_root/themes/premium/all_js/jQuery-2.1.4.min.js'></script>";
 echo "<script src='$path_to_root/themes/premium/all_js/bootstrap.min.js'></script>";
 echo "<script src='$path_to_root/themes/premium/all_js/icheck.min.js'></script>";
 echo"<script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>";
	echo "</body></html>";



	
echo "</html>";
	


?>