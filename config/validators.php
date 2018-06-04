<?php
/*Reference : https://css-tricks.com/serious-form-security/*/
/*Web Security*/
function generateFormToken($form = "jagannath") {

      $form = base64_encode($form);
      $token = md5(uniqid(microtime(), true));  
      $_SESSION[$form.'_token'] = $token; 
      return $token;

}

function verifyFormToken($form = "jagannath") {
    $form = base64_encode($form);
    // check if a session is started and a token is transmitted, if not return an error
  if(!isset($_SESSION[$form.'_token'])) { 
    return false;
    }
  
  // check if the form is sent with token in it
  if(!isset($_POST['token'])) {
    return false;
    }
  
  // compare the tokens against each other if they are still the same
  if ($_SESSION[$form.'_token'] !== $_POST['token']) {
    return false;
    }
  
  return true;
}

function sendhackingmessage($subject,$logging) {
  $to = 'manish.arora@jimsindia.org';  
  $subject = $subject;
  $header = 'From: admin@jagannath.org'. "\r\n";
  $header .= 'Cc: rajkamal@jimsindia.org' . "\r\n";
  if (mail($to, $subject, $logging, $header)) {
    echo "Sent notice to admin.";
  }
}

function writeLog($where) {

  $ip = $_SERVER["REMOTE_ADDR"]; // Get the IP from superglobal
  $host = gethostbyaddr($ip);    // Try to locate the host of the attack
  $date = date("d M Y");
  
  // create a logging message with php heredoc syntax
  $logging = <<<LOG
    \n
    << Start of Message >>
    There was a hacking attempt on your form. \n 
    Date of Attack: {$date}
    IP-Adress: {$ip} \n
    Host of Attacker: {$host}
    Point of Attack: {$where}
    Page Url: {$GLOBALS['currenturl']}
    << End of Message >>
LOG;
        
        // open log file
    if($handle = fopen('hacklog.log', 'a')) {
    
      fputs($handle, $logging);  // write the Data to file
      fclose($handle);           // close the file
      sendhackingmessage('HACK ATTEMPT - Jagannath University',$logging);    
      die();
      
    } else {  // if first method is not working, for example because of wrong file permissions, email the data
          sendhackingmessage('HACK ATTEMPT - Jagannath University',$logging);    
          die();
          /*$to = 'manish.arora@jimsindia.org';  
          $subject = 'HACK ATTEMPT';
          $header = 'From: admin@jagannath.org';
          if (mail($to, $subject, $logging, $header)) {
            echo "Sent notice to admin.";
          }*/

  }
}

function valid_url() {
  if(!filter_var($_POST['URL-main'],FILTER_VALIDATE_URL)) {
   writeLog('URL Validation');
   die('Please insert a valid URL');
  }
}

function whitelist($array = array()) {
  foreach ($_POST as $key=>$item) {
    // Check if the value $key (fieldname from $_POST) can be found in the whitelisting array, if not, die with a short message to the hacker
    if (!in_array($key, $whitelist)) {
      writeLog('Unknown form fields');
      die("Hack-Attempt detected. Please use only the fields in the form");
    }
  }
}


function validate_me() {
  if (verifyFormToken()) {
    //valid_url();
    //whitelist();
     

  } else {
     echo "Hack-Attempt detected. Got ya!.";
     writeLog('Formtoken');

  }
}

function stripcleantohtml($s){
    // Restores the added slashes (ie.: " I\'m John " for security in output, and escapes them in htmlentities(ie.:  &quot; etc.)
    // Also strips any <html> tags it may encouter
    // Use: Anything that shouldn't contain html (pretty much everything that is not a textarea)
    return htmlentities(trim(strip_tags(stripslashes(mysqli_real_escape_string($GLOBALS['con'], $s)))), ENT_NOQUOTES, "UTF-8");
}

function cleantohtml($s){
    // Restores the added slashes (ie.: " I\'m John " for security in output, and escapes them in htmlentities(ie.:  &quot; etc.)
    // It preserves any <html> tags in that they are encoded aswell (like &lt;html&gt;)
    // As an extra security, if people would try to inject tags that would become tags after stripping away bad characters,
    // we do still strip tags but only after htmlentities, so any genuine code examples will stay
    // Use: For input fields that may contain html, like a textarea
    //return htmlentities(trim(strip_tags(addslashes($s))), ENT_NOQUOTES, "UTF-8");
    return htmlentities(trim(addslashes($s)), ENT_NOQUOTES, "UTF-8");
}

function cleantohtmldecode($s) {
  return html_entity_decode(stripslashes($s));
}

//$csrf_token = generateFormToken();
function csrf_token() {
  $token = generateFormToken();
  echo $token;
}
// Building a whitelist array with keys which will send through the form, no others would be accepted later on
//$whitelist = array('token','req-name','req-email','typeOfChange','urgency','URL-main','addURLS', 'curText', 'newText', 'save-stuff');

// Building an array with the $_POST-superglobal 
