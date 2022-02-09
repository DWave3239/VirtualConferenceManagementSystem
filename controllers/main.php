<?php
class main extends Base {
  
  function __construct($host=null, $user=null, $pass=null, $db=null, $port=null, $socket=null){
    parent::__construct($host, $user, $pass, $db, $port, $socket);

    foreach($_SESSION as $key=>$val){
      $this->rain->assign($key, $val);
    }
  }

  function index(){
    $this->draw(array('templates/header', 'mainPage', 'templates/footer'));
  }

  function login(){
    $users = $this->db->query("SELECT * FROM users WHERE username=?", array($_POST['username'])); // select all users
    foreach($users as $user){
      if($user['username'] == $_POST['username'] && password_verify($_POST['password'], $user['password'])){
        $_SESSION['logged_in'] = true;
        $_SESSION['userdata'] = $user;
        $_SESSION['auth_level'] = $user['auth_level'];
        if($_SESSION['auth_level'] == ADMINISTRATOR){
          $_SESSION['auths'] = $this->db->select('auths', 'authId');
        }else{
          $_SESSION['auths'] = $this->db->select('auths2tickets', 'authId', array('ticketId'=>$user['ticketId']));
        }
        $_SESSION['auths'] = array_column($_SESSION['auths'], 'authId');
        break;
      }
    }

    if($_SESSION['logged_in']){
      $this->redirect("participation/index");
    }else{
      $this->redirect();
    }
  }

  function logout(){
    $_SESSION['logged_in'] = false;
    unset($_SESSION['userdata'], $_SESSION['presenter'], $_SESSION['auths'], $_SESSION['auth_level']);
    $this->redirect();
  }

  function schedule2(){
    $schedule = $this->getSchedule();

    $var['schedule'] = $schedule;

    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest"){
      $var['collapsed'] = $var['print'] = false;
      $this->draw(array('scheduleDynamic'), $var);
    }else{
      require_once VENDOR_DIR.'/autoload.php';

      $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
      $mpdf->WriteHTML(file_get_contents(ASSETS_DIR.'css/print.css'), \Mpdf\HTMLParserMode::HEADER_CSS);
      $var['print'] = true;
      $mpdf->WriteHTML($this->draw(array('scheduleDynamic'), $var, true), \Mpdf\HTMLParserMode::HTML_BODY);
      $mpdf->Output();
    }
  }

  function committees($chosen_committee = null){
    $var = array(
      'subnav' => 'committees'
    );

    if($chosen_committee == null){
      $this->draw(array('templates/header', 'committees', 'templates/footer'), $var);    
    }else{
      $this->draw(array('templates/header', 'committee'.ucfirst($chosen_committee), 'templates/footer'), $var);    
    }
  }

  function register(){
    if(!empty($_POST)){
      $price = 0;

      $early_bird = true;

      if($_POST['role'] == 'non-presenter'){
        $price = ($early_bird) ? 50 : 75;
      }else{
        $price = 280;
        for($i=1; $i<4; $i++){
          if(!empty($_POST['additional_paper'+$i])){
            $price += 280;
          }
        }
        if(!empty($_POST['additional_people'])){
          $price += 50*intval($_POST['additional_people']);
        }
      }

      $this->draw(array('templates/header', 'registered', 'templates/footer'), array('post_data'=>print_r($_POST, true), 'price'=>number_format($price, 2, ',', '.')));
    }else{
      $this->draw(array('templates/header', 'register', 'templates/footer'));
    }
  }

  function session_killer(){
    session_destroy();
    $this->redirect();
  }

  private function log(){
    $log = PHP_EOL.print_r($_SERVER, true);
    $log .= PHP_EOL.print_r($this->pd, true);

    error_log($log);
    var_dump($log);
    exit;
  }

	function setTimezoneOffset($offset){
		$_SESSION['timezone_offset'] = $offset; // offset in minutes
		echo "done";
	}

  function news($fromId=null){
    $news = $this->getNews(isset($_SESSION['timezone_offset']), $fromId);

    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest"){
      echo json_encode($news);
    }else{
      $this->draw(array('templates/header', 'news', 'templates/footer'), array('news'=>$news));
    }
  }
  
  function notifications($fromId=null){
    if($_SESSION['logged_in']){
      $notifications = $this->getNotifications($_SESSION['userdata']['userId'], true, $fromId);
    }else{
      $notifications = $this->getNotifications(null, false, $fromId);
    }
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest"){
      echo json_encode($notifications);
    }else{
      die($this->draw(array('dialogContent/notifications'), array('notifications'=>$notifications), true));
    }
  }

  function webhook(){
    if(!isset($_SERVER['HTTP_X_JAAS_SIGNATURE'])) die('DENIED');

    // fetch stuff
    $signature = explode(',', $_SERVER['HTTP_X_JAAS_SIGNATURE']);
    $headers = [];
    foreach($signature as $parts){
      list($key, $value) = explode('=', $parts, 2);
      $headers[$key] = $value;
    }

    if(!isset($headers['t'], $headers['v1'])){
      return;
    }

    $timestamp = $headers['t'];
    $signature = $headers['v1'];
    $rawPayload = file_get_contents('php://input');
    $payload = json_decode($rawPayload, true);
    
    // create signed payload
    $signed_payload = $timestamp.'.'.$rawPayload;
    
    // secret
    $secret = 'whsec_d9c6ade9b3d046f5a5c57710b53c5395';

    // create hmac
    $hmac = hash_hmac('sha256', $signed_payload, $secret);

    // debug
    // file_put_contents('webhook.log', $signature.PHP_EOL.base64_encode(hex2bin($hmac)).PHP_EOL.PHP_EOL, FILE_APPEND);
    
    // compare signatures
    if(hash_equals($signature, base64_encode(hex2bin($hmac)))){
      $userId = $payload['data']['id'] ?? -1;
      $roomName = substr($payload['fqn'], strlen($payload['appId'])+1);
      $this->db->insert('webhookEvents', array('userId'=>$userId, 'eventName'=>$payload['eventType'], 'roomName'=>$roomName, 'data'=>$payload, 'timestamp'=>time()));
    }
  }
}
?>
