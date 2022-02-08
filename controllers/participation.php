<?php
class participation extends Base {

  function __construct($host=null, $user=null, $pass=null, $db=null, $port=null, $socket=null){
    parent::__construct($host, $user, $pass, $db, $port, $socket);

    // requirement: being logged in
    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != 1){
      $this->redirect();
      die();
    }
  }

  function index(){
    // where user is a chair and where the user presents something
    $var['userSegments'] = $this->getUserSegments($only_future=false, $adjust_timezone=false);

    // dynamic schedule for the future
    $var['schedule'] = $this->getSchedule($only_future=true);
    $var['collapsed'] = true;
    $var['print'] = false;
    $var['customSchedulerId'] = "participantSchedule";
    $var['skip_day_counter'] = true;

    $this->draw(array('templates/header', 'participations/overview', 'templates/footer'), $var);
  }

  function watch($room){
    $jwt = $this->createRoomToken($room);

    if(!is_null($jwt)){
      $this->draw(array('templates/header', 'participations/watch', 'templates/footer'), array('room'=>$room, 'jwt'=>$jwt, 'delay'=>$this->roomDelay));
    }else{
      $this->draw(array('templates/header', 'templates/footer'));
    }
  }

  private function getUserSegments($only_future = false, $adjust_timezone = false){
    $chair_in = $this->db->query("SELECT *, 
                                  IF(s.individual_link IS NULL OR s.individual_link = '', 
                                    (SELECT tr.link FROM tracks tr LEFT JOIN segments2tracks s2t ON s2t.trackId=tr.trackId WHERE s2t.segmentId=s.segmentId), 
                                    s.individual_link
                                  ) as 'roomLink'
                                  FROM segments s
                                    LEFT JOIN timeslots t USING(timeslotId)
                                  WHERE s.chairId=?", array($_SESSION['userdata']['userId']));

    $presenter_in = $this->db->query("SELECT p.name AS 'paper_name', s.name AS 'segment_name', COALESCE(s.individual_link, tr.link) AS 'roomLink', t.date, t.time_from, t.time_until
                                      FROM papers p 
                                        LEFT JOIN segments s USING(segmentId) 
                                        LEFT JOIN timeslots t  USING(timeslotId)
                                        LEFT JOIN segments2tracks s2t USING(segmentId)
                                        LEFT JOIN tracks tr USING(trackId)
                                      WHERE p.userId=?
                                      GROUP BY paperId", array($_SESSION['userdata']['userId']));
    
    return array('chair_in'=>$chair_in, 'presenter_in'=>$presenter_in);
  }

  private function createRoomToken($room){
    // check user role in room & generate jwt accordingly
    $jwtCreator = $this->loadJwtCreator();

    $now = gmdate("Y-m-d H:i:s", time());
    list($date, $time) = explode(' ', $now);
    
    $roomData = $this->db->query(
       "SELECT *
        FROM
          (
            (
              SELECT ts.time_from, ts.time_until, s.chairId, GROUP_CONCAT(p.userId) AS 'authors', s.delay
              FROM tracks t
                LEFT JOIN segments2tracks s2t ON s2t.trackId = t.trackId
                LEFT JOIN segments s ON s.segmentId = s2t.segmentId
                LEFT JOIN timeslots ts ON ts.timeslotId = s.timeslotId
                LEFT JOIN papers p ON p.segmentId = s.segmentId
              WHERE t.link LIKE ? AND ts.date LIKE ? AND ? BETWEEN ts.time_from AND ts.time_until
            ) UNION ( 
              SELECT ts.time_from, ts.time_until, s.chairId, GROUP_CONCAT(p.userId) AS 'authors', s.delay
              FROM segments s
                LEFT JOIN timeslots ts ON ts.timeslotId = s.timeslotId
                LEFT JOIN papers p ON p.segmentId = s.segmentId
              WHERE s.individual_link LIKE ? AND ts.date LIKE ? AND ? BETWEEN ts.time_from AND ts.time_until
            )
          ) AS temp
        WHERE temp.chairId IS NOT NULL
        LIMIT 1", array($room, $date, $time, $room, $date, $time));

    $userData = array(
      'userEmail' => $_SESSION['userdata']['email'],
      'userName' => $_SESSION['userdata']['full_name'],
      'userId' => $_SESSION['userdata']['userId'],
      'userIsModerator' => false,
      'userAvatarUrl' => ''
    );
    $permissions = array(
      'liveStreamingEnabled' => false,
      'recordingEnabled' => false,
      'outboundEnabled' => false, 
      'transcriptionEnabled' => false
    );
    $delays = array(
      'expDelay' => 0,
      'nbfDelay' => 0
    );

    if(count($roomData) > 0){
      $roomData = $roomData[0];

      $delays['expDelay'] = strtotime(date('Y-m-d', strtotime('+1 day')));
      $delays['nbfDelay'] = strtotime($date);

      if($roomData['chairId'] == $_SESSION['userdata']['userId']){
        $permissions['liveStreamingEnabled'] = true;
        $permissions['recordingEnabled'] = true;
        $permissions['outboundEnabled'] = true;
        $permissions['transcriptionEnabled'] = true;
        $userData['userIsModerator'] = true;
      }elseif(in_array($_SESSION['userdata']['userId'], explode(',',$roomData['authors']))){
        $userData['userIsModerator'] = true;
      }
      $this->roomDelay = $roomData['delay'];
    }else{
      $this->roomDelay = null;
    }

    if($_SESSION['userdata']['auth_level'] == ADMINISTRATOR){ // admins are always allowed and have max permissions
      $permissions['liveStreamingEnabled'] = true;
      $permissions['recordingEnabled'] = true;
      $permissions['outboundEnabled'] = true;
      $permissions['transcriptionEnabled'] = true;
      
      $userData['userIsModerator'] = true;

      $delays['expDelay'] = strtotime(date('Y-m-d', strtotime('+1 day')));
      $delays['nbfDelay'] = strtotime($date);
    }
    #$this->dump($userData, $permissions, $delays, $room); die();
    return $jwtCreator->createJaasToken($userData, $permissions, $delays, $room);
  }
}