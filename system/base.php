<?php
class Base {

	public $db;
	public $rain;
	private $overlays = array();
	private $jsFiles = array();

	function __construct($db=null){
		ini_set('session.gc_maxlifetime', 3600); // server should keep session data for AT LEAST 1 hour
		session_set_cookie_params(3600); // each client should remember their session id for EXACTLY 1 hour
		session_start();

		$_SESSION['debug'] = false;
		$_SESSION['url'] = CURRENT_ROUTE;
		if(!isset($_SESSION['auths'])) $_SESSION['auths'] = array();

		@set_exception_handler(array($this, 'exception_handler'));

		require_once "db.php";
		$this->db = new DB($db['host'], $db['user'], $db['pass'], $db['db'], $db['port'], $db['socket']);
		$this->db->establish_connection();

		include_once "rain.tpl.class.php";
		$this->rain = new RainTPL();

		// basic
		$this->rain->assign('base_url', 	BASE_URL);
		$this->rain->assign('assets_url', 	ASSETS_URL);
		$this->rain->assign('_base_', 		_BASE_);
		$this->rain->assign('cur_route', 	_BASE_.CURRENT_ROUTE);
		if(!isset($_SESSION['logged_in'])) 	$_SESSION['logged_in'] = false;
		$this->rain->assign('logged_in', 	$_SESSION['logged_in']);
		$this->rain->assign('auth_level', 	$_SESSION['logged_in'] ? $_SESSION['auth_level'] : NO_AUTH);
		$this->rain->assign('username', 	$_SESSION['logged_in'] ? $_SESSION['userdata']['username'] : 'Not logged in');
		$this->rain->assign('full_name', 	$_SESSION['logged_in'] ? $_SESSION['userdata']['full_name'] : 'Not logged in');

		// auth level variables
		$this->rain->assign('auth_level_no_auth', 		NO_AUTH);
		$this->rain->assign('auth_level_participant', 	PARTICIPANT);
		$this->rain->assign('auth_level_presenter', 	PRESENTER);
		$this->rain->assign('auth_level_session_chair', SESSION_CHAIR);
		$this->rain->assign('auth_level_program_chair', PROGRAM_CHAIR);
		$this->rain->assign('auth_level_administrator', ADMINISTRATOR);

		// video link prefix
		$this->rain->assign('_watch_', _BASE_.'participation/watch');

		// standard overlays
		$overlays = array(
			array(
				'id'    => 'schedule2',
				'title' => 'Conference Schedule dynamic',
				'size'  => 'customlarge',
				'print' => true
			),
			array(
			  'id'      => 'loginForm',
			  'title'   => 'Mockup Login',
			  'size'    => 'md',
			  'content' => $this->draw(array('dialogContent/loginForm'), array('users'=>$this->db->select('users')), true)
			),
			array(
			  'id'      => 'registrationForm',
			  'title'   => 'Mockup Registration Form',
			  'size'    => 'lg',
			  'content' => $this->draw(array('dialogContent/registrationForm'), array('early_bird'=>true), true)
			)
		  );
		  
		  if($_SESSION['logged_in']){
			  $overlays[] = array(
				'id'    => 'latestNotifications',
				'title' => 'Latest notifications',
				'size'  => 'lg',
				'content' => $this->draw(array('dialogContent/notifications'), array('load'=>true))
			  );
		  }

		  $this->addOverlays($overlays);
	}

	function exception_handler($exception) {
		echo "<b>Exception:</b> " . $exception->getMessage();
		echo "<br /><pre>".$this->FormatBacktrace()."</pre>";
		var_dump($exception);
	}

	function FormatBacktrace(){
		$result = "";
		if($this->db->last_query()){
			$result = '<h4>------------- Last Query -------------</h4>';
			$result .= $this->db->last_query().'<br>';
		}
		$result .= '<h4>------------- Backtrace -------------</h4>';

		foreach(debug_backtrace() as $trace){
			if ($trace['function'] ==__FUNCTION__) // skip this function
				continue;

			$parameters = '';
			foreach($trace['args'] as $parameter) // concat params
				$parameters .= $parameter.', ';

			if(substr($parameters, -2) == ', ') // cut of trailing ', '
				$parameters = substr($parameters, 0, -2);

			if(array_key_exists('class', $trace)){ // if 'class' is a key
				$result .= sprintf("%s:%s %s::%s(%s)<br>", (isset($trace['file']) ? $trace['file'] : ""), (isset($trace['line']) ? $trace['line'] : ""), (isset($trace['class']) ? $trace['class'] : ""), (isset($trace['function']) ? $trace['function'] : ""), $parameters);
			}else{ // if 'class' is not a key
				$result .= sprintf("%s:%s %s(%s)<br>", (isset($trace['file']) ? $trace['file'] : ""), (isset($trace['line']) ? $trace['line'] : ""), (isset($trace['function']) ? $trace['function'] : ""), $parameters);
			}
		}

		return $result;
	}

	function draw($templates, $variables=array(), $return_string=false){
		if(!is_array($templates)) 	throw new Exception('Templates must be an array');
		if(empty($templates)) 		throw new Exception('No templates given to draw');
		if(!is_array($variables)) 	throw new Exception('Variables must be an array');

		// assigning
		foreach($variables as $k=>$v){
			$this->rain->assign($k, $v);
		}

		// debugging overlay
		if(isset($_SESSION['debug']) && $_SESSION['debug']){
			$this->addOverlays(array(array(
				'id'      => 'debug',
				'title'   => 'DEBUG',
				'size'    => 'lg',
				'content' => '<h4>Session:</h4><pre>'.print_r($_SESSION, true).'</pre><h4>Post:</h4><pre>'.print_r($_POST, true).'</pre>'
			)));
		}

		// assign overlays defined
		$this->rain->assign('overlays', $this->overlays);

		// assign js files defined
		$this->rain->assign('jsFiles', $this->jsFiles);

		// actual drawing
		$output = "";
		foreach($templates as $tpl){
			$output .= $this->rain->draw($tpl, $return_string);
		}
		return $output;
	}

	function addOverlays($overlays){
		foreach($overlays as $o){
			$this->overlays[] = $o;
		}
	}

	function addJs($js){
		$this->jsFiles[] = $js;
	}

	function redirect($target=''){
		if(empty($target)) $target = $_SESSION['logged_in'] ? 'participation/index' : 'main/index';
		$this->draw(array('helperSites/redirecter'), array('target'=>$target));
	}

	protected function getSchedule($only_future = false, $adjust_timezone = false){
		$schedule = array();

		$trackData = $this->db->query("	SELECT tr.*, ti.date
										FROM tracks tr
											LEFT JOIN segments2tracks s2t USING(trackId)
											LEFT JOIN segments s USING(segmentId)
											LEFT JOIN timeslots ti USING(timeslotId)
										GROUP BY tr.trackId, tr.name, tr.description, tr.link, tr.sequence
										ORDER BY ti.date, tr.sequence");
		$invalidPermissionTracks = [];
		foreach($trackData as $key=>$track){
			$permissionsNeeded = array_column($this->db->select('auths2tracks', 'authId', array('trackId'=>$track['trackId'])), 'authId');
			if(empty($permissionsNeeded)) continue; // no permissions needed for this track
			if(count(array_intersect($_SESSION['auths'], $permissionsNeeded)) != count($permissionsNeeded)){ // permissions don't align
				$invalidPermissionTracks[$key] = $track['trackId'];
				unset($trackData[$key]);
			}
		}
		$schedule['tracks'] = $this->getColumns($trackData, array('trackId', 'name', 'description', 'link'));
		$schedule['track_order'] = array_column($schedule['tracks'], 'trackId'); // contains order for table display
		$schedule['procedure'] = array();
		
		$tracksPerDay = array();
		foreach($trackData as $t){
			if(!isset($tracksPerDay[$t['date']])){
				$tracksPerDay[$t['date']] = array();
			}
			$tracksPerDay[$t['date']][] = $t['trackId'];
		}
	
		$track_links = array();
		foreach($schedule['tracks'] as $t){
		  $track_links[$t['trackId']] = $t['link'];
		}

		// fake timeslots
		$this->db->update('timeslots', array('date'=>date('Y-m-d')                     ), "timeslotId < 6");
		$this->db->update('timeslots', array('date'=>date('Y-m-d', strtotime('+1 day'))), "timeslotId > 5 AND timeslotId < 13");
		$this->db->update('timeslots', array('date'=>date('Y-m-d', strtotime('+2 day'))), "timeslotId > 12");
	
		// timeslot piecing
		if($only_future){
		  if($adjust_timezone){
			$timeslots = $this->db->query(" SELECT * 
											FROM timeslots
											WHERE `date` > DATE(INTERVAL NOW() + ? MINUTES) 
												OR (`date` = DATE(INTERVAL NOW() + ? MINUTES) AND time_until >= INTERVAL NOW()  + ? MINUTES)
											ORDER BY `date`, time_from", 
											array($_SESSION['timezone_offset'], $_SESSION['timezone_offset'], $_SESSION['timezone_offset']));
		  }else{
			$timeslots = $this->db->query(" SELECT * 
											FROM timeslots 
											WHERE `date` > CURDATE() 
												OR (`date` = CURDATE() AND time_until >= NOW())
											ORDER BY `date`, time_from");
		  }
		}else{
		  $timeslots = $this->db->query("SELECT * FROM timeslots ORDER BY `date`, time_from");
		}
	
		foreach($timeslots as $slot){
			$originalDate = $slot['date'];
		  // timezone adjustment hack
		  if($adjust_timezone){
			$slot['date']       = $this->adjustToTimezone($slot['date'].' '.$slot['time_from'], $_SESSION['timezone_offset']);
			$slot['time_from']  = $slot['date'];
			$slot['time_until'] = $this->adjustToTimezone($slot['date'].' '.$slot['time_until'], $_SESSION['timezone_offset']);
		  }
		  $slot['date'] = date('d.m.Y', strtotime($slot['date']));
	
		  // create day if necessary
		  if(!isset($schedule['procedure'][$slot['date']])){
			$schedule['procedure'][$slot['date']] = array('tracks'=>array(), 'slots'=>array());
		  }
	
		  // get segments for timeslot
		  $excludeTracks = "";
		  if(count($invalidPermissionTracks) > 0){
			  $excludeTracks = "AND s2t.trackId NOT IN (".implode(',', $invalidPermissionTracks).")";
		  }
		  $segments = $this->db->query("  SELECT s.*, COUNT(s2t.trackId) AS 'colspan', CONCAT(',', GROUP_CONCAT(s2t.trackId), ',') as 'is_in_tracks', u.full_name AS chairName
										  FROM segments s 
											LEFT JOIN segments2tracks s2t ON s2t.segmentId = s.segmentId ".$excludeTracks."
											LEFT JOIN users u ON s.chairId = u.userId
										  WHERE timeslotId=? 
										  GROUP BY segmentId, timeslotId, s.`name`, s.chairId", array($slot['timeslotId']));
		  
		  // order segments by track
		  $segmentIds = array_column($segments, 'segmentId');
		  $trackIds = $this->db->select("segments2tracks", "trackId", "segmentId IN (".implode(',', $segmentIds).")", "FIELD(trackId, ".implode(',', $schedule['track_order']).")");
		  $track_order = array_column($trackIds, 'trackId');
		  $ordered_segments = array();
		  foreach($segments as $segment){
			$permissionsNeeded = array_column($this->db->select('auths2segments', 'authId', array('segmentId'=>$segment['segmentId'])), 'authId');
			if(!empty($permissionsNeeded) && count(array_intersect($_SESSION['auths'], $permissionsNeeded)) != count($permissionsNeeded)){ // permissions don't align
				continue;
			}
			foreach($track_order as $trackId){
			  if($segment['is_in_tracks'] == null) continue; // skip segments where permissions are missing
			  if(strpos($segment['is_in_tracks'], ",".$trackId.",") !== FALSE){
				if(count($ordered_segments) == 0 || $ordered_segments[count($ordered_segments)-1] != $segment){ // add when empty or different from the previous entry
				  $ordered_segments[] = $segment;
				}
			  }
			}
		  }
		  
		  // fill content
		  $content = array();
		  foreach($ordered_segments as $segment){
			$link = null;
			if(count(array_filter(explode(',', $segment['is_in_tracks']))) == 1){
			  $trackId = explode(',', $segment['is_in_tracks'])[1];
			  if(!isset($track_links[$trackId])){
				  continue; // track may not be accessed due to missing permissions	
			  }
			  $link = $track_links[$trackId];
			}
			// get papers for segment
			$papers = $this->db->query("SELECT * FROM papers LEFT JOIN users USING(userId) WHERE segmentId=?", array($segment['segmentId']));
			foreach($papers as $pId => $p){
				$authors = $this->db->query("SELECT u.userId, u.full_name FROM papers2users p2u LEFT JOIN users u ON u.userId=p2u.userId WHERE p2u.paperId=?", array($p['paperId']));
				$papers[$pId]['authorList'] = implode(', ', array_map(function($a) use($p) { return ($p['userId'] == $a['userId']) ? '<b>'.$a['full_name'].'</b>' : $a['full_name'];}, $authors));
			}
			$content[] = array('colspan'=>$segment['colspan'], 'link'=>$link, 'segment'=>$segment, 'papers'=>$papers);
			$schedule['procedure'][$slot['date']]['tracks'] = $tracksPerDay[$originalDate];
		  }
	
		  $schedule['procedure'][$slot['date']]['slots'][] = array('time'=>array('from'=>$slot['time_from'], 'until'=>$slot['time_until']), 'content'=>$content);
		}

		//$this->dump($schedule['procedure']['08.02.2022']); die();

		foreach($schedule['procedure'] as $dayKey => $day){
			$dayTracks = array_flip($day['tracks']);
			foreach($dayTracks as $k=>$dt){ 
				$dayTracks[$k] = true; 
			}
			foreach($day['slots'] as $slotKey => $slot){
				$tracksToCheck = $dayTracks;
				foreach($slot['content'] as $content){
					$contentTracks = array_filter(explode(',', $content['segment']['is_in_tracks']));
					foreach($contentTracks as $ct){
						$tracksToCheck[$ct] = false;
					}
				}
				$tracksToInsert = array_filter($tracksToCheck);
				if(!empty($tracksToInsert)){
					foreach($tracksToInsert as $toi=>$true){
						$position = array_search($toi, $day['tracks']);
						for($i=count($schedule['procedure'][$dayKey]['slots'][$slotKey]['content'])-1; $i >= $position; $i--){
							$schedule['procedure'][$dayKey]['slots'][$slotKey]['content'][$i+1] = $schedule['procedure'][$dayKey]['slots'][$slotKey]['content'][$i];	
						}
						$schedule['procedure'][$dayKey]['slots'][$slotKey]['content'][$position] = array('colspan'=>1);
					}
				}
			}
		}

		//$this->dump($schedule['procedure']['08.02.2022']); die();

		return $schedule;
	}
	
	private function adjustToTimezone($datetime, $timezone){
		if($timezone == 0) return (new DateTime($datetime))->format('Y-m-d H:i:s');
		
		// create timezones
		$gmtTimezone = new DateTimeZone('GMT'); // standard
		
		// create date object
		$gmtDateTime = new DateTime($datetime, $gmtTimezone);
	
		// calculate timezone offset
		$offset = $userTimezone->getOffset($gmtDateTime);
		
		// add difference in timezones
		$interval = DateInterval::createDateFromString((string)$offset . " seconds");
		$gmtDateTime->add($interval);
		
		return $gmtDateTime->format('Y-m-d H:i:s');
	}

	protected function getNews($adjust_timezone=false, $fromId=null){
		$where = "";
		if($fromId != null){
			$where = " AND newsId > ".$fromId;
		}
		$where .= " ORDER BY newsId DESC";
		if($adjust_timezone){
			$news = $this->db->query("	SELECT * 
										FROM news 
										WHERE displayAfter <= TIMESTAMPADD(MINUTE, ?, NOW())".$where,
										array($_SESSION['timezone_offset']));
		}else{
			$news = $this->db->query("SELECT * FROM news WHERE displayAfter <= NOW()".$where);
		}
		return $news;
	}

	
	protected function getNotifications($userId, $adjust_timezone=false, $fromId=null){
		$where = "";
		if($fromId != null){
			$where = " AND notificationId > ".$fromId;
		}

		if($userId != NULL){
			if($adjust_timezone){
				$nofitifcations = $this->db->query("	SELECT * 
											FROM notifications 
											WHERE displayAfter <= TIMESTAMPADD(MINUTE, ?, NOW())".$where,
											array($_SESSION['timezone_offset']));
			}else{
				$nofitifcations = $this->db->query("SELECT * FROM news WHERE displayAfter <= NOW()".$where);	
			}
		}else{
			if($adjust_timezone){
				$nofitifcations = $this->db->query("	SELECT * 
											FROM notifications 
											WHERE displayAfter <= TIMESTAMPADD(MINUTE, ?, NOW())".$where,
											array($_SESSION['timezone_offset']));
			}else{
				$nofitifcations = $this->db->query("SELECT * FROM notifications WHERE displayAfter <= NOW()".$where);
			}
		}
		return $nofitifcations;
	}

	function loadJwtCreator(){
		require SYSTEM_DIR."/jwtCreator.php";
    	return new JWTCreator(JAASAUTH_KEY_PATH);
	}

	function tokenGenerator($length = 64) {
		$alphabet = str_split('0123456789-abcdefghijklm$nopqrstuvwxyz_ABCDEFGHIJKLM/NOPQRSTUVWXYZ');
		if ($length < 1) {
			throw new \RangeException("Length must be a positive integer");
		}
		$letters = [];
		$max = count($alphabet)-1;
		for ($i = 0; $i < $length; ++$i) {
			$letters[] = $alphabet[random_int(0, $max)];
		}
		return implode('', $letters);
	}

	function dump(...$obj){
		echo '<pre>';
		foreach($obj as $o){
			var_dump($o);
		}
		echo '</pre>';
	}

	function getColumns($arr, $cols){
		if(!is_array($arr)) return array();
		$res = [];
		foreach($arr as $a){
			$row = [];
			foreach($cols as $c){
				if(isset($a[$c])){
					$row[$c] = $a[$c];
				}
			}
			$res[] = $row;
		}
		return $res;
	}
}
?>
