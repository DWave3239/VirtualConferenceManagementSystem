<?php
class backend extends Base {

  function __construct($host=null, $user=null, $pass=null, $db=null, $port=null, $socket=null){
    parent::__construct($host, $user, $pass, $db, $port, $socket);

    if(!$_SESSION['logged_in'] || $_SESSION['auth_level'] < PROGRAM_CHAIR){
        $this->redirect();
        die();
    }

    foreach($_SESSION as $key=>$val){
        $this->rain->assign($key, $val);
    }
  }

  public function index(){
    $this->draw(array('templates/header', 'cruds/index', 'templates/footer'));
  }

  public function news($action='list', $pkId=null, $save=0){
    $var['url'] = __FUNCTION__;
    $tab = "news";
    
    $var['action'] = $action;
    $var['pkId'] = $pkId;

    switch($action){
        case 'list':
            $var['list'] = $this->db->select($tab);
            $this->addJs('crud');
            $this->addOverlays($this->getDeletionConfirmationOverlay());
            break;
    
        case 'add':
            if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    $this->db->insert($tab, $_POST);
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
            }
            $var['pkId'] = -1;
            break;
    
        case 'edit':
            if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    $this->db->update($tab, $_POST, array('newsId'=>$pkId));
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['row'] = $this->db->select($tab, '*', array('newsId'=>$pkId))[0];
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
            }
            break;
    
        case 'delete':
            $this->db->delete($tab, array('newsId'=>$pkId));
            $this->redirect('backend/'.__FUNCTION__);
            die();
    }

    $this->draw(array('templates/header', 'cruds/news', 'templates/footer'), $var);
  }

  public function notifications($action='list', $pkId=null, $save=0){
    $var['url'] = __FUNCTION__;
    $tab = "notifications";
    
    $var['action'] = $action;
    $var['pkId'] = $pkId;

    switch($action){
        case 'list':
            $var['list'] = $this->db->select($tab);
            $this->addJs('crud');
            $this->addOverlays($this->getDeletionConfirmationOverlay());
            break;
    
        case 'add':
            if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    $this->db->insert($tab, $_POST);
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
            }
            $var['pkId'] = -1;
            break;
    
        case 'edit':
            if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    $this->db->update($tab, $_POST, array('notificationId'=>$pkId));
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['row'] = $this->db->select($tab, '*', array('notificationId'=>$pkId))[0];
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
            }
            break;
    
        case 'delete':
            $this->db->delete($tab, array('notificationId'=>$pkId));
            $this->redirect('backend/'.__FUNCTION__);
            die();
    }

    $this->draw(array('templates/header', 'cruds/notifications', 'templates/footer'), $var);
  }

  public function users($action='list', $pkId=null, $save=0){
    if($_SESSION['auth_level'] < ADMINISTRATOR){ 
        $this->redirect('backend/index');
        die();
    }
    $var['url'] = __FUNCTION__;
    $tab = "users";
    
    $var['action'] = $action;
    $var['pkId'] = $pkId;

    switch($action){
        case 'list':
            $var['list'] = $this->db->select($tab);
            $this->addJs('crud');
            $this->addOverlays($this->getDeletionConfirmationOverlay());
            break;
    
        case 'add':
            if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    $_POST['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
                    $this->db->insert($tab, $_POST);
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['tickets'] = $this->db->select('tickets');
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
            }
            $var['pkId'] = -1;
            break;
    
        case 'edit':
           if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    $_POST['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
                    $this->db->update($tab, $_POST, array('userId'=>$pkId));
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['row'] = $this->db->select($tab, '*', array('userId'=>$pkId))[0];
                $var['tickets'] = $this->db->select('tickets');
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
            }
            break;
    
        case 'delete':
            $this->db->update('papers', array('userId'=>NULL), array('userId'=>$pkId));
            $this->db->update('segments', array('userId'=>NULL), array('chairId'=>$pkId));
            $this->db->delete('papers2users', array('userId'=>$pkId));
            $this->db->delete($tab, array('userId'=>$pkId));
            $this->redirect('backend/'.__FUNCTION__);
            die();
    }

    $this->draw(array('templates/header', 'cruds/users', 'templates/footer'), $var);
  }
  
  public function papers($action='list', $pkId=null, $save=0){
    $var['url'] = __FUNCTION__;
    $tab = "papers";
    
    $var['action'] = $action;
    $var['pkId'] = $pkId;

    switch($action){
        case 'list':
            $var['list'] = $this->db->query('SELECT p.paperId, p.name, s.name as segmentName, s.subtitle, u.full_name FROM papers p LEFT JOIN segments s ON s.segmentId=p.segmentId LEFT JOIN users u ON u.userId=p.userId');
            $this->addJs('crud');
            $this->addOverlays($this->getDeletionConfirmationOverlay());
            break;
    
        case 'add':
            if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    
                    // temporarily save authors
                    if(isset($_POST['authors']) && is_array($_POST['authors'])){
                        $auths = $_POST['authors'];
                    }
                    unset($_POST['authors']);

                    $rowsChanged = $this->db->insert($tab, $_POST);
                    $pkId = $this->db->insert_id();

                    if(isset($authors)){
                        // insert authors
                        $mapped = array_map(function($uId) use($pkId) { return array('paperId'=>$pkId, 'userId'=>$uId); }, $authors);
                        $this->db->multiInsert('papers2users', $mapped);
                    }

                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['authors'] = $this->db->select('users', '*', 'auth_level = '.PRESENTER);
                $var['segments'] = $this->db->select('segments');
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
                $this->addJs('papersHelper');
            }
            $var['pkId'] = -1;
            break;
    
        case 'edit':
            if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);

                    $this->db->delete('papers2users', array('paperId'=>$pkId));
                    if(isset($_POST['authors'])){
                        $mapped = array_map(function($uId) use($pkId) { return array('paperId'=>$pkId, 'userId'=>$uId); }, $_POST['authors']);
                        $this->db->multiInsert('papers2users', $mapped);
                        unset($_POST['authors']);
                    }

                    $this->db->update($tab, $_POST, array('paperId'=>$pkId));
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['row'] = $this->db->select($tab, '*', array('paperId'=>$pkId))[0];
                $var['authors'] = $this->db->select('users', '*', 'auth_level = '.PRESENTER);
                $var['team'] = array_column($this->db->select('papers2users', 'userId', 'paperId = '.$pkId), 'userId');
                $var['segments'] = $this->db->select('segments');
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
                $this->addJs('papersHelper');
            }
            break;
    
        case 'delete':
            $this->db->delete('papers2users', array('paperId'=>$pkId));
            $this->db->delete($tab, array('paperId'=>$pkId));
            $this->redirect('backend/'.__FUNCTION__);
            die();
    }

    $this->draw(array('templates/header', 'cruds/papers', 'templates/footer'), $var);
  }

  public function segments($action='list', $pkId=null, $save=0){
    $var['url'] = __FUNCTION__;
    $tab = "segments";
    
    $var['action'] = $action;
    $var['pkId'] = $pkId;

    switch($action){
        case 'list':
            $var['list'] = $this->db->query('SELECT * FROM segments LEFT JOIN users ON chairId = userId');
            $this->addJs('crud');
            $this->addOverlays($this->getDeletionConfirmationOverlay());
            break;
    
        case 'add':
            if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    
                    // build delay
                    if(isset($_POST['delay']) && !empty($_POST['delay'])){
                        $delayTemp = explode(':', $_POST['delay']);
                        $delayTemp[0] *= $_POST['delaySign'] ?? 1;
                        $_POST['delay'] = implode(':', $delayTemp).':00';
                    }else{
                        $_POST['delay'] = "00:00:00";
                    }
                    unset($_POST['delaySign']);
                    
                    // temporarily save tracks
                    if(isset($_POST['tracks']) && is_array($_POST['tracks'])){
                        $tracks = $_POST['tracks'];
                    }
                    if(isset($_POST['auths']) && is_array($_POST['auths'])){
                        $auths = $_POST['auths'];
                    }
                    unset($_POST['tracks'], $_POST['auths']);
                    
                    $rowsChanged = $this->db->insert($tab, $_POST);
                    $pkId = $this->db->insert_id();
                    
                    if(isset($tracks)){
                        // insert tracks
                        $mapped = array_map(function($tId) use($pkId) { return array('segmentId'=>$pkId, 'trackId'=>$tId); }, $tracks);
                        $this->db->multiInsert('segments2tracks', $mapped);
                    }
                    if(isset($auths)){
                        // insert auths
                        $mapped = array_map(function($aId) use($pkId) { return array('segmentId'=>$pkId, 'authId'=>$aId); }, $auths);
                        $this->db->multiInsert('auths2segments', $mapped);
                    }

                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['sessionchairs'] = $this->db->select('users', 'userId, username, full_name', 'auth_level >= '.SESSION_CHAIR);
                $var['timeslots'] = $this->db->select('timeslots', '*', '', 'date, time_from');
                $var['tracks'] = $this->db->select('tracks');
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
                $var['auths'] = $this->db->select('auths');
            }
            $var['pkId'] = -1;
            break;
    
        case 'edit':
           if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    
                    // delay
                    if(isset($_POST['delay']) && !empty($_POST['delay'])){
                        $delayTemp = explode(':', $_POST['delay']);
                        $delayTemp[0] *= $_POST['delaySign'];
                        $_POST['delay'] = implode(':', $delayTemp).':00';
                    }
                    unset($_POST['delaySign']);
                    
                    // tracks
                    $this->db->delete('segments2tracks', array('segmentId'=>$pkId));
                    if(isset($_POST['tracks'])){
                        $mapped = array_map(function($tId) use($pkId) { return array('segmentId'=>$pkId, 'trackId'=>$tId); }, $_POST['tracks']);
                        $this->db->multiInsert('segments2tracks', $mapped);
                        unset($_POST['tracks']);
                    }

                    // auths
                    $this->db->delete('auths2segments', array('segmentId'=>$pkId));
                    if(isset($_POST['auths'])){
                        $mapped = array_map(function($aId) use($pkId) { return array('segmentId'=>$pkId, 'authId'=>$aId); }, $_POST['auths']);
                        $this->db->multiInsert('auths2segments', $mapped);
                        unset($_POST['auths']);
                    }

                    $rowsUpdated = $this->db->update($tab, $_POST, array('segmentId'=>$pkId));
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['row'] = $this->db->query("SELECT * FROM segments s LEFT JOIN timeslots t USING(timeslotId) WHERE segmentId=?", array($pkId))[0];
                if(isset($var['row']['delay']) && !empty($var['row']['delay'])){
                    $delayTemp = explode(':', $var['row']['delay']);
                    $var['row']['delaySign'] = intval($delayTemp[0]) <=> 0;
                    $delayTemp[0] *= $var['row']['delaySign'];
                    if($delayTemp[0] < 10) $delayTemp[0] = '0'.$delayTemp[0];
                    $var['row']['delay'] = implode(':', $delayTemp);
                }else{
                    $var['row']['delaySign'] = 1;
                }
                $var['sessionchairs'] = $this->db->select('users', 'userId, username, full_name', 'auth_level >= '.SESSION_CHAIR);
                $var['timeslots'] = $this->db->select('timeslots', '*', '', 'date, time_from');
                $var['tracks'] = $this->db->select('tracks');
                $var['selectedTracks'] = array_column($this->db->select('segments2tracks', 'trackId', array('segmentId'=>$pkId)), 'trackId');
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
                $var['auths'] = $this->db->select('auths');
                $var['auths2segments'] = array_column($this->db->select('auths2segments', 'authId', array('segmentId', $pkId)), 'authId');
            }
            break;
    
        case 'delete':
            $this->db->delete('auths2tracks', array('segmentId'=>$pkId));
            $this->db->delete('segments2tracks', array('segmentId'=>$pkId));
            $this->db->delete($tab, array('segmentId'=>$pkId));
            $this->redirect('backend/'.__FUNCTION__);
            die();
    }

    $this->draw(array('templates/header', 'cruds/segments', 'templates/footer'), $var);
  }

  public function timeslots($action='list', $pkId=null, $save=0){
    $var['url'] = __FUNCTION__;
    $tab = "timeslots";
    
    $var['action'] = $action;
    $var['pkId'] = $pkId;

    switch($action){
        case 'list':
            $var['list'] = $this->db->select($tab, '*', '', 'date ASC, time_from ASC');
            $this->addJs('crud');
            $this->addOverlays($this->getDeletionConfirmationOverlay());
            break;
    
        case 'add':
            if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    
                    $_POST['time_from'] .= ':00';
                    $_POST['time_until'] .= ':00';

                    $this->db->insert($tab, $_POST);
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
            }
            $var['pkId'] = -1;
            break;
    
        case 'edit':
           if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    
                    $_POST['time_from'] .= ':00';
                    $_POST['time_until'] .= ':00';

                    $this->db->update($tab, $_POST, array('timeslotId'=>$pkId));
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['row'] = $this->db->select($tab, '*', array('timeslotId'=>$pkId))[0];
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
            }
            break;
    
        case 'delete':
            $this->db->delete($tab, array('timeslotId'=>$pkId));
            $this->redirect('backend/'.__FUNCTION__);
            die();
    }

    $this->draw(array('templates/header', 'cruds/timeslots', 'templates/footer'), $var);
  }

  public function tracks($action='list', $pkId=null, $save=0){
    $var['url'] = __FUNCTION__;
    $tab = "tracks";
    
    $var['action'] = $action;
    $var['pkId'] = $pkId;

    switch($action){
        case 'list':
            $var['list'] = $this->db->select($tab);
            $this->addJs('crud');
            $this->addOverlays($this->getDeletionConfirmationOverlay());
            break;
    
        case 'add':
            if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);

                    // temporarily save auths
                    if(isset($_POST['auths']) && is_array($_POST['auths'])){
                        $auths = $_POST['auths'];
                    }
                    unset($_POST['auths']);

                    $rowsChanged = $this->db->insert($tab, $_POST);
                    $pkId = $this->db->insert_id();                    

                    if(isset($auths)){
                        // insert auths
                        $mapped = array_map(function($aId) use($pkId) { return array('trackId'=>$pkId, 'authId'=>$aId); }, $auths);
                        $this->db->multiInsert('auths2tracks', $mapped);
                    }
                    
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
                $var['auths'] = $this->db->select('auths');
                $var['auths2tracks'] = array_column($this->db->select('auths2tracks', 'authId', array('trackId'=>$pkId)), 'authId');
            }
            $var['pkId'] = -1;
            break;
    
        case 'edit':
           if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);

                    // auths
                    $this->db->delete('auths2tracks', array('trackId'=>$pkId));
                    if(isset($_POST['auths'])){
                        $mapped = array_map(function($aId) use($pkId) { return array('trackId'=>$pkId, 'authId'=>$aId); }, $_POST['auths']);
                        $this->db->multiInsert('auths2tracks', $mapped);
                        unset($_POST['auths']);
                    }

                    $this->db->update($tab, $_POST, array('trackId'=>$pkId));
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['row'] = $this->db->select($tab, '*', array('trackId'=>$pkId))[0];
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
                $var['auths'] = $this->db->select('auths');
                $var['auths2tracks'] = array_column($this->db->select('auths2tracks', 'authId', array('trackId'=>$pkId)), 'authId');
            }
            break;
    
        case 'delete':
            $this->db->delete('segments2tracks', array('trackId'=>$pkId));
            $this->db->delete('auths2tracks', array('trackId'=>$pkId));
            $this->db->delete($tab, array('trackId'=>$pkId));
            $this->redirect('backend/'.__FUNCTION__);
            die();
    }

    $this->draw(array('templates/header', 'cruds/tracks', 'templates/footer'), $var);
  }

  public function tickets($action='list', $pkId=null, $save=0){
    if($_SESSION['auth_level'] < ADMINISTRATOR){ 
        $this->redirect('backend/index');
        die();
    }
    $var['url'] = __FUNCTION__;
    $tab = "tickets";
    
    $var['action'] = $action;
    $var['pkId'] = $pkId;

    switch($action){
        case 'list':
            $var['list'] = $this->db->select($tab);
            $this->addJs('crud');
            $this->addOverlays($this->getDeletionConfirmationOverlay());
            break;
    
        case 'add':
            if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);

                    // temporarily save auths
                    if(isset($_POST['auths']) && is_array($_POST['auths'])){
                        $auths = $_POST['auths'];
                    }
                    unset($_POST['auths']);

                    $rowsChanged = $this->db->insert($tab, $_POST);
                    $pkId = $this->db->insert_id();

                    if(isset($auths)){
                        // insert auths
                        $mapped = array_map(function($aId) use($pkId) { return array('ticketId'=>$pkId, 'authId'=>$aId); }, $auths);
                        $this->db->multiInsert('auths2tickets', $mapped);
                    }

                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
                $var['auths'] = $this->db->select('auths');
            }
            $var['pkId'] = -1;
            break;
    
        case 'edit':
           if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);

                    $this->db->delete('auths2tickets', array('ticketId'=>$pkId));
                    if(isset($_POST['auths'])){
                        $mapped = array_map(function($aId) use($pkId) { return array('ticketId'=>$pkId, 'authId'=>$aId); }, $_POST['auths']);
                        $this->db->multiInsert('auths2tickets', $mapped);
                        unset($_POST['auths']);
                    }

                    $this->db->update($tab, $_POST, array('ticketId'=>$pkId));

                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['row'] = $this->db->select($tab, '*', array('ticketId'=>$pkId))[0];
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
                $var['auths'] = $this->db->select('auths');
                $var['auths2tickets'] = array_column($this->db->select('auths2tickets', 'authId', 'ticketId='.$pkId), 'authId');
            }
            break;
    
        case 'delete':
            $this->db->delete('auths2tickets', array('ticketId'=>$pkId));
            $this->db->delete($tab, array('ticketId'=>$pkId));
            $this->redirect('backend/'.__FUNCTION__);
            die();
    }

    $this->draw(array('templates/header', 'cruds/tickets', 'templates/footer'), $var);
  }


  public function auths($action='list', $pkId=null, $save=0){
    if($_SESSION['auth_level'] < ADMINISTRATOR){ 
        $this->redirect('backend/index');
        die();
    }
    $var['url'] = __FUNCTION__;
    $tab = "auths";
    
    $var['action'] = $action;
    $var['pkId'] = $pkId;

    switch($action){
        case 'list':
            $var['list'] = $this->db->select($tab);
            $this->addJs('crud');
            $this->addOverlays($this->getDeletionConfirmationOverlay());
            break;
    
        case 'add':
            if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    $this->db->insert($tab, $_POST);
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
            }
            $var['pkId'] = -1;
            break;
    
        case 'edit':
           if($save == 1){
                if(isset($_SESSION['token'], $_POST['token']) && $_POST['token'] == $_SESSION['token']){
                    unset($_POST['token'], $_SESSION['token']);
                    $this->db->update($tab, $_POST, array('authId'=>$pkId));
                    $this->redirect('backend/'.__FUNCTION__);
                    die();
                }else{
                    $var['error'] = 'Error during saving. Invalid token encountered.';
                }
            }else{
                $var['row'] = $this->db->select($tab, '*', array('authId'=>$pkId))[0];
                $var['token'] = $_SESSION['token'] = $this->tokenGenerator();
            }
            break;
    
        case 'delete':
            $this->db->delete('auths2segments', array('authId'=>$pkId));
            $this->db->delete('auths2tickets', array('authId'=>$pkId));
            $this->db->delete('auths2tracks', array('authId'=>$pkId));
            $this->db->delete($tab, array('authId'=>$pkId));
            $this->redirect('backend/'.__FUNCTION__);
            die();
    }

    $this->draw(array('templates/header', 'cruds/auths', 'templates/footer'), $var);
  }

  private function getDeletionConfirmationOverlay(){
    return array(array(
        'id'                 => 'crudDeletionOverlay',
        'title'              => 'Deletion Confirmation',
        'size'               => 'md',
        'content'            => 'Are you sure you want to delete this element?',
        'confirmationDialog' => true
    ));
  }
}