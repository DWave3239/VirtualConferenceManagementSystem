<?php
class statistics extends Base {

  function __construct($host=null, $user=null, $pass=null, $db=null, $port=null, $socket=null){
    parent::__construct($host, $user, $pass, $db, $port, $socket);

    if(!$_SESSION['logged_in'] || $_SESSION['auth_level'] < ADMINISTRATOR){
        $this->redirect();
        die();
    }

    $eventIcons = [
      'ROOM_DESTROYED' => '<i class="far fa-times-circle" title="Room destroyed"></i>',
      'PARTICIPANT_LEFT' => '<i class="fas fa-sign-out-alt" title="User left room"></i>',
      'PARTICIPANT_JOINED' => '<i class="fas fa-sign-in-alt" title="User joined room"></i>',
      'ROOM_CREATED' => '<i class="fas fa-plus-circle" title="Room created"></i>'
    ];
    $this->rain->assign('eventIcons', $eventIcons);
  }

  function index(){
    $var['list'] = $this->db->query('SELECT we.eventName, we.roomName, we.timestamp, u.userId, u.full_name FROM webhookEvents we LEFT JOIN users u USING(userId) ORDER BY we.timestamp DESC, CASE WHEN eventName LIKE "ROOM_DESTROYED" THEN 4 WHEN eventName LIKE "PARTICIPANT_LEFT" THEN 3 WHEN eventName LIKE "PARTICIPANT_JOINED" THEN 2 WHEN eventName LIKE "ROOM_CREATED" THEN 1 ELSE 0 END DESC');
    $var['rooms'] = array_unique(array_column($var['list'], 'roomName'));
    $var['users'] = [];
    foreach($var['list'] as $row){
      if(isset($row['userId'])){
        $var['users'][$row['userId']] = ['userId'=>$row['userId'], 'full_name'=>$row['full_name']];
      }
    }
    $this->draw(array('templates/header', 'statistics/index', 'templates/footer'), $var);
  }

  function user($userId){
    $var['full_name'] = $this->db->select('users', 'full_name', array('userId'=>$userId))[0]['full_name'];
    $events = $this->db->select('webhookEvents', '*', array('userId'=>$userId), array('timestamp DESC'));
    $overall = [];
    $roomsDetails = [];
    foreach($events as $e){
      $overall[] = $e;
      if(!isset($roomsDetails[$e['roomName']])){
        $roomsDetails[$e['roomName']] = [];
      }
      $roomsDetails[$e['roomName']][] = $e;
    }
    $var['overall'] = $overall;
    $rooms = [0 => []];
    $roomsSums = [];
    $entireSum = 0;
    foreach($roomsDetails as $i=>$u){
      $rooms[$i] = $this->eventsToTimespans($u, ($i == -1) ? 'room' : 'user');

      $roomsSums[$i] = 0;
      foreach($rooms[$i] as $entry){
        $roomsSums[$i] += $entry['span'];
      }
      $entireSum += $roomsSums[$i];
      $roomsSums[$i] = array('span'=>$roomsSums[$i], 'span_string'=>$this->secondsToFormattedText($roomsSums[$i]));
    }
    $roomsSums[0] = array('span'=>$entireSum, 'span_string'=>$this->secondsToFormattedText($entireSum));
    $var['roomsDetails'] = $roomsDetails;
    $var['roomsSums'] = $roomsSums;
    $var['rooms'] = $rooms;
    $this->draw(array('templates/header', 'statistics/user', 'templates/footer'), $var);
  }

  function room($roomName){
    $var['roomName'] = $roomName;
    $events = $this->db->select('webhookEvents', '*', array('roomName'=>$roomName), array('timestamp DESC', 'CASE WHEN eventName LIKE "ROOM_DESTROYED" THEN 4 WHEN eventName LIKE "PARTICIPANT_LEFT" THEN 3 WHEN eventName LIKE "PARTICIPANT_JOINED" THEN 2 WHEN eventName LIKE "ROOM_CREATED" THEN 1 ELSE 0 END DESC'));
    $users = $this->db->select('users');
    $userNames = [];
    foreach($users as $u){
      $userNames[$u['userId']] = $u['full_name'];
    }
    $userNames[-1] = 'Room';
    $var['userNames'] = $userNames;
    $overall = [];
    $userDetails = [];
    foreach($events as $e){
      $overall[] = $e;
      if(!isset($userDetails[$e['userId']])){
        $userDetails[$e['userId']] = [];
      }
      $userDetails[$e['userId']][] = $e;
    }
    ksort($userDetails);
    $var['overall'] = $overall;
    $users = [];
    $userSums = [];
    foreach($userDetails as $i=>$u){
      $users[$i] = $this->eventsToTimespans($u, ($i == -1) ? 'room' : 'user');

      $userSums[$i] = 0;
      foreach($users[$i] as $entry){
        $userSums[$i] += $entry['span'];
      }
      $userSums[$i] = array('span'=>$userSums[$i], 'span_string'=>$this->secondsToFormattedText($userSums[$i]));
    }
    $var['userDetails'] = $userDetails;
    $var['userSums'] = $userSums;
    $var['users'] = $users;
    $this->draw(array('templates/header', 'statistics/room', 'templates/footer'), $var);
  }

  private function eventsToTimespans($events, $type = 'room'){
    $collection = [];
    $count = -1;
    if($type == 'room'){
      foreach($events as $e){
        if($e['eventName'] == 'ROOM_DESTROYED'){
          $timespans[] = [];
          $count++;
        }
        $collection[$count][] = $e['timestamp'];
      }
    }else{ // type == 'user'
      foreach($events as $e){
        if($e['eventName'] == 'PARTICIPANT_LEFT'){
          $timespans[] = [];
          $count++;
        }
        $collection[$count][] = $e['timestamp'];
      }
    }

    $timespans = [];
    foreach($collection as $i=>$c){
      $span = strtotime($c[0]) - strtotime($c[1]);
      $span_text = $this->secondsToFormattedText($span);
      $timespans[$i] = array('from'=>strtotime($c[1]), 'from_string'=>$c[1], 'until'=>strtotime($c[0]), 'until_string'=>$c[0], 'span'=>$span, 'span_string'=>$span_text);
    }
    return $timespans;
  }

  private function secondsToFormattedText($seconds){
    $formatted = '';
    if($seconds >= 60){
      $formatted = ':'.str_pad($seconds%60, 2, '0', STR_PAD_LEFT);
      $minutes = intval($seconds/60);
      if($minutes >= 60){
        $formatted = intval($minutes/60).':'.($minutes%60).$formatted;
      }else{
        $formatted = '00:'.str_pad($minutes, 2, '0', STR_PAD_LEFT).$formatted;
      }
    }else{
      $formatted = '00:00:'.str_pad($seconds, 2, '0', STR_PAD_LEFT);
    }
    return $formatted;
  }
}