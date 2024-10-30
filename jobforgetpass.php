<?php
if (isset($_POST['fpass_submit']) == 'Submit') {
    global $wpdb;
    $setting = $wpdb->get_row(@$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "jobs_settings",""));
    $feed_url = $setting->http_url; //'http://jobs.chameleoni.com/xmlfeed.aspx';
    $AuthKey = $setting->authKey;
    $AuthPassword = $setting->authPassword;
    $APIKey = $setting->aPIKey;
    $UserName = $setting->userName;
    $Thank_you_page = $setting->thank_you_page;
    $feed_location = $setting->feed_location;
    $feed_type = $setting->feed_type;
    $feed_salary = $setting->feed_salary;
    $feed_summary = $setting->feed_summary;
    $page_size = $setting->number_of_jobsper_Page;



        //validate captcha
        if ( (!isset($_POST['g-recaptcha-response']))  )
        {
            echo "invalid captcha." ;
            return;
        }

        $captcha_response = sanitize_text_field($_POST['g-recaptcha-response']);
        $captcha_params = 'secret=6Ld-EroUAAAAALbixufOd8I7DieG01uVEMv-HmWl' . '&response=' . $captcha_response ;
        $captcha_request = curl_init("https://www.google.com/recaptcha/api/siteverify");

        curl_setopt($captcha_request, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($captcha_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($captcha_request, CURLOPT_POSTFIELDS, $captcha_params);
        curl_setopt($captcha_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($captcha_request, CURLOPT_VERBOSE, 0);

        $captcha_google_response = curl_exec($captcha_request);  
        
        $decoded_response = json_decode($captcha_google_response, true);
        $captcha_validation_check = $decoded_response["success"];

        if($captcha_validation_check != '1')
        {
            echo '<div class="row">Captcha validation failed.<div>
                  <div class="row">
                    <a HREF="#"  onclick ="(function(){ window.location.href = window.location.href; })();return false;">Try again</a>
                  </div>
                ' ;
            return;
        }
		//validate captcha





    $request_fpassemail = '<?xml version="1.0" encoding="utf-16" ?>
<ChameleonIAPI>
    <Method>PasswordReminder</Method>
    <APIKey>'.$APIKey.'</APIKey>
    <UserName>'.$UserName.'</UserName>
    <InputData>
            <Input Name="Email" Value="' . sanitize_email($_POST['Email']) . '" />
            <Input Name="MailName" Value="Email - Password Reminder"/>
    </InputData>
</ChameleonIAPI>';



    $encoded = 'Xml=' . $request_fpassemail . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
    $ch = curl_init($feed_url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    $result = curl_exec($ch);

    curl_close($ch);
    $result = str_replace('utf-16', 'utf-8', $result);
    $xml = simplexml_load_string($result);
    $json = json_encode($xml);
    $array_res = json_decode($json, TRUE);
    if($array_res['Status'] == 'Pass' && $array_res['ContactCount'] == 1)
    {
        echo '<div class="alert alert-success">We have successfully send an email please check your inbox.</div>';
    }else{
        echo '<div class="alert alert-danger">Sorry! There is no user exists with this email with us.</div>';
    }
}?>
<script type="text/javascript">
    function app_validate()
    {
        validation_string = new Array();
        if (document.getElementById("Email").value == "")
        {
            document.getElementById("Email").focus();
            validation_string.push("Email");
        }else if (document.getElementById("Email").value != "")
        {
            var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
            if (!document.getElementById("Email").value.match(emailExp)) {
                document.getElementById("Email").focus();
                validation_string.push("Valid Email Address");
            } 
        }
        if (validation_string != "") {
            alert("Please enter " + validation_string.join(", ").toString());
            return false;
        }
        return true;
    }
</script>


<script>
                var script_callback = function()
                {
                    setTimeout(function()
                            { 
                                grecaptcha.render("captcha_placeholder", 
                                {
                                    "sitekey" : "6Ld-EroUAAAAAK5zaBg3C2Qr7Gg7C0lSIS_AsBx-"
                                }
                                );                                
                            }, 
                            250
                       );
             }			
</script>
			
<script src="https://www.google.com/recaptcha/api.js?onload=script_callback&render=explicit" async defer></script>



<form name="fpass" id="fpass" action="" Method="POST">
    <p>Please enter the email address used during your registration.</p>
    <input type="email" name="Email" id="Email" placeholder="Email Address">
    <input type="submit" name="fpass_submit" value="Submit"  onclick="return app_validate();" class="btn btn-primary">  

    <p>&nbsp;</p>
    <div id="captcha_placeholder"></div>
    
    
</form>
<p><a href="<?= get_option("login_page_url") ?>">Login</a></p>