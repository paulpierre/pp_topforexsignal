<?php
  /**
   * User Class
   *
   * @package Membership Manager Pro
   * @author wojoscripts.com
   * @copyright 2010
   * @version $Id: class_user.php, v2.00 2011-07-10 10:12:05 gewa Exp $
   */

  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');

  class Users
  {
      const uTable = "users";
      const traderTable = 'traders';
      public $logged_in = null;
      public $uid = 0;
      public $userid = 0;
      public $username;
      public $email;
      public $tfs_id;
      public $name;
      public $membership_id = 0;
      public $trader_status = 0;
      public $userlevel;
      public $cookie_id = 0;
	  public $last;
      private $lastlogin = "NOW()";
      private static $db;


      /**
       * Users::__construct()
       * 
       * @return
       */
      function __construct()
      {
          self::$db = Registry::get("Database");

          $this->getUserId();
          $this->startSession();


      }

      /**
       * Users::getUserId()
       * 
       * @return
       */
      private function getUserId()
      {
          if (isset($_GET['userid'])) {
              $userid = (is_numeric($_GET['userid']) && $_GET['userid'] > -1) ? intval($_GET['userid']) : false;
              $userid = sanitize($userid);

              if ($userid == false) {
                  Filter::error("You have selected an Invalid Userid", "Users::getUserId()");
              } else
                  return $this->userid = $userid;
          }
      }

      /**
       * Users::startSession()
       * 
       * @return
       */
      private function startSession()
      {
          if (strlen(session_id()) < 1)
              session_start();

          $this->logged_in = $this->loginCheck();

          if (!$this->logged_in) {
              $this->username = $_SESSION['username'] = "Guest";
              $this->sesid = sha1(session_id());
              $this->userlevel = 0;
          }
      }

      /**
       * Users::loginCheck()
       * 
       * @return
       */
      private function loginCheck()
      {
          if (isset($_SESSION['username']) && $_SESSION['username'] != "Guest") {
			  
              $row = $this->getUserInfo($_SESSION['username']);
              $this->uid = $row->id;
              $this->username = $row->username;
              $this->email = $row->email;
              $this->name = $row->fname . ' ' . $row->lname;
              $this->userlevel = $row->userlevel;
              $this->cookie_id = $row->cookie_id;
			  $this->last = $row->lastlogin;
              $this->membership_id = $row->membership_id;
              $this->tfs_id = $row->trader_id;
              return true;
          } else {
              return false;
          }
      }

      /**
       * Users::is_Admin()
       * 
       * @return
       */
      public function is_Admin()
      {
          return ($this->userlevel == 9);

      }

      /**
       * Users::login()
       * 
       * @param mixed $username
       * @param mixed $pass
       * @return
       */
      public function login($username, $pass)
      {
          if ($username == "" && $pass == "") {
              Filter::$msgs['username'] = 'Please enter valid username and password.';
          } else {
              $status = $this->checkStatus($username, $pass);

              switch ($status) {
                  case 0:
                      Filter::$msgs['username'] = 'Login and/or password did not match to the database.';
                      break;

                  case 1:
                      Filter::$msgs['username'] = 'Your account has been banned.';
                      break;

                  case 2:
                      Filter::$msgs['username'] = 'Your account it\'s not activated.';
                      break;

                  case 3:
                      Filter::$msgs['username'] = 'You need to verify your email address.';
                      break;
              }
          }
          if (empty(Filter::$msgs) && $status == 5) {
              $row = $this->getUserInfo($username);
              $this->tfs_id = $SESSION['tfs_id'] = $row->trader_id;
              $this->uid = $_SESSION['userid'] = $row->id;
              $this->username = $_SESSION['username'] = $row->username;
              $this->email = $_SESSION['email'] = $row->email;
              $this->name = $_SESSION['name'] = $row->fname . ' ' . $row->lname;
              $this->userlevel = $_SESSION['userlevel'] = $row->userlevel;
              $this->cookie_id = $_SESSION['cookie_id'] = $this->generateRandID();
			  $this->last = $_SESSION['last'] = $row->lastlogin;
              $this->membership_id = $_SESSION['membership_id'] = $row->membership_id;

              $data = array(
                  'lastlogin' => $this->lastlogin,
                  //'cookie_id' => $this->cookie_id,
                  'lastip' => sanitize($_SERVER['REMOTE_ADDR'])
				  );
				  
              self::$db->update(self::uTable, $data, "username='" . $this->username . "'");
              if (!$this->validateMembership()) {
                  $data = array('membership_id' => 0, 'mem_expire' => "0000-00-00 00:00:00");
                  self::$db->update(self::uTable, $data, "username='" . $this->username . "'");
              }


              return true;
          } else
              Filter::msgStatus();
      }

      /**
       * Users::logout()
       * 
       * @return
       */
      public function logout()
      {

          unset($_SESSION['username']);
          unset($_SESSION['email']);
          unset($_SESSION['name']);
          unset($_SESSION['membership_id']);
          unset($_SESSION['userid']);
          unset($_SESSION['cookie_id']);
          session_destroy();
          session_regenerate_id();

          $this->logged_in = false;
          $this->username = "Guest";
          $this->userlevel = 0;
      }

      /**
       * User::confirmUserID()
       * 
       * @param mixed $username
       * @param mixed $cookie_id
       * @return
       */
      function confirmUserID($username, $cookie_id)
      {

          $sql = "SELECT cookie_id FROM users WHERE username = '" . self::$db->escape($username) . "'";
          $result = self::$db->query($sql);
          if (!$result || (self::$db->numrows($result) < 1)) {
              return 1;
          }

          $row = self::$db->fetch($result);
          $row->cookie_id = sanitize($row->cookie_id);
          $cookie_id = sanitize($cookie_id);

          if ($cookie_id == $row->cookie_id) {
              return 0;
          } else {
              return 2;
          }
      }

      /**
       * Users::getUserInfo()
       * 
       * @param mixed $username
       * @return
       */
      private function getUserInfo($username)
      {
          $username = sanitize($username);
          $username = self::$db->escape($username);

          $sql = "SELECT * FROM " . self::uTable . " WHERE username = '" . $username . "' OR email ='" . $username . "'";
          $row = self::$db->first($sql);
          if (!$username)
              return false;

          return ($row) ? $row : 0;
      }



      public function getUserStatus($traderID)
      {
          //$traderID = $this->tfs_id;
          if(isset($traderID))
          {
              $sql = 'SELECT trader_status FROM ' . self::traderTable . ' WHERE trader_id=' . $traderID;
              $row = self::$db->first($sql);
               $traderStatus = $row->trader_status;
              if (!$traderStatus)
                  return false;
              $this->trader_status = $traderStatus;
              return $traderStatus;
          } else {
              return false;
          }
      }



      /**
       * Users::checkStatus()
       * 
       * @param mixed $username
       * @param mixed $pass
       * @return
       */
      public function checkStatus($username, $pass)
      {

          $username = sanitize($username);
          $username = self::$db->escape($username);
          $pass = sanitize($pass);

          $sql = "SELECT password, active FROM " . self::uTable 
		  . "\n WHERE username = '" . $username . "' OR email = '" . $username . "'";
          $result = self::$db->query($sql);

          if (self::$db->numrows($result) == 0)
              return 0;

          $row = self::$db->fetch($result);
          $entered_pass = sha1($pass);

          switch ($row->active) {
              case "b":
                  return 1;
                  break;

              case "n":
                  return 2;
                  break;

              case "t":
                  return 3;
                  break;

              case "y" && $entered_pass == $row->password:
                  return 5;
                  break;
          }
      }

      /**
       * Users::getUsers()
       * 
       * @param bool $from
       * @return
       */
      public function getUsers($from = false)
      {

          $pager = Paginator::instance();
          $pager->items_total = countEntries(self::uTable);
          $pager->default_ipp = Registry::get("Core")->perpage;
          $pager->paginate();

          if (isset($_GET['sort'])) {
              list($sort, $order) = explode("-", $_GET['sort']);
              $sort = sanitize($sort);
              $order = sanitize($order);
              if (in_array($sort, array(
                  "username",
                  "fname",
                  "lname",
                  "email",
                  "mem_expire"))
				  ) {
                  $ord = ($order == 'DESC') ? " DESC" : " ASC";
                  $sorting = " u." . $sort . $ord;
              } else {
                  $sorting = " u.created DESC";
              }
          } else {
              $sorting = " u.created DESC";
          }

          $clause = (isset($clause)) ? $clause : null;

          if (isset($_POST['fromdate']) && $_POST['fromdate'] <> "" || isset($from) && $from != '') {
              $enddate = date("Y-m-d");
              $fromdate = (empty($from)) ? $_POST['fromdate'] : $from;
              if (isset($_POST['enddate']) && $_POST['enddate'] <> "") {
                  $enddate = $_POST['enddate'];
              }
              $clause .= " WHERE u.created BETWEEN '" . trim($fromdate) . "' AND '" . trim($enddate) . " 23:59:59'";
          }

          $sql = "SELECT u.*, CONCAT(u.fname,' ',u.lname) as name, m.title, m.id as mid," 
		  . "\n DATE_FORMAT(u.created, '%d. %b. %Y.') as cdate," 
		  . "\n DATE_FORMAT(u.lastlogin, '%d. %b. %Y.') as adate" 
		  . "\n FROM " . self::uTable . " as u" 
		  . "\n LEFT JOIN memberships as m ON m.id = u.membership_id" 
		  . "\n " . $clause 
		  . "\n ORDER BY " . $sorting . $pager->limit;
          $row = self::$db->fetch_all($sql);

          return ($row) ? $row : 0;
      }

      /**
       * Users::processUser()
       * 
       * @return
       */
      public function processUser()
      {

          if (!Filter::$id) {
              Filter::checkPost('username', "Please Enter Valid Username!");

              if ($value = $this->usernameExists($_POST['username'])) {
                  if ($value == 1)
                      Filter::$msgs['username'] = 'Username Is Too Short (less Than 4 Characters Long).';
                  if ($value == 2)
                      Filter::$msgs['username'] = 'Invalid Characters Found In Username.';
                  if ($value == 3)
                      Filter::$msgs['username'] = 'Sorry, This Username Is Already Taken';
              }
          }

          Filter::checkPost('fname', "Please Enter First Name!");
          Filter::checkPost('lname', "Please Enter Last Name!");

          if (!Filter::$id) {
              Filter::checkPost('password', "Please Enter Valid Password!");
          }

          Filter::checkPost('email', "Please Enter Valid Email Address!");
          if (!Filter::$id) {
              if ($this->emailExists($_POST['email']))
                  Filter::$msgs['email'] = 'Entered Email Address Is Already In Use.';
          }
          if (!$this->isValidEmail($_POST['email']))
              Filter::$msgs['email'] = 'Entered Email Address Is Not Valid.';

          if (!empty($_FILES['avatar']['name'])) {
              if (!preg_match("/(\.jpg|\.png)$/i", $_FILES['avatar']['name'])) {
                  Filter::$msgs['avatar'] = "Illegal file type. Only jpg and png file types allowed.";
              }
              $file_info = getimagesize($_FILES['avatar']['tmp_name']);
              if (empty($file_info))
                  Filter::$msgs['avatar'] = "Illegal file type. Only jpg and png file types allowed.";
          }
		  
		  $this->verifyCustomFields("profile");


          if (empty(Filter::$msgs)) {

              $trial = getValueById("trial", Membership::mTable, intval($_POST['membership_id']));
			  $cur_mem = getValueById("membership_id", self::uTable, Filter::$id);
			  $mem_exp = getValueById("mem_expire", self::uTable, Filter::$id);
              $data = array(
                  'username' => sanitize($_POST['username']),
                  'email' => sanitize($_POST['email']),
                  'lname' => sanitize($_POST['lname']),
                  'fname' => sanitize($_POST['fname']),
                  'membership_id' => ($cur_mem != intval($_POST['membership_id'])) ? intval($_POST['membership_id']) : 0,
                  'mem_expire' => ($cur_mem != intval($_POST['membership_id'])) ? $this->calculateDays($_POST['membership_id']) : $mem_exp,
				  'notes' => sanitize($_POST['notes']),
                  'trial_used' => ($trial) ? 1 : 0,
                  'newsletter' => intval($_POST['newsletter']),
                  'userlevel' => intval($_POST['userlevel']),
                  'active' => sanitize($_POST['active'])
				  );

              if (!Filter::$id)
                  $data['created'] = "NOW()";

              if (Filter::$id)
                  $userrow = Registry::get("Core")->getRowById(self::uTable, Filter::$id);

              if ($_POST['password'] != "") {
                  $data['password'] = sha1($_POST['password']);
              } else {
                  $data['password'] = $userrow->password;
              }

			  // Start Custom Fields
			  $fl_array = array_key_exists_wildcard($_POST, 'custom_*', 'key-value');
			  if (isset($fl_array)) {
				  $fields = $fl_array;
				  $total = count($fields);
				  if (is_array($fields)) {
					  $fielddata = '';
					  foreach ($fields as $fid) {
						  $fielddata .= $fid . "::";
					  }
				  }
				  $data['custom_fields'] = $fielddata;

              }
              if(isset(Filter::$id))$this->update_tfs(Filter::$id,$data['custom_fields']);

              // Procces Avatar
              if (!empty($_FILES['avatar']['name'])) {
                  $thumbdir = UPLOADS;
                  $tName = "AVT_" . randName();
                  $text = substr($_FILES['avatar']['name'], strrpos($_FILES['avatar']['name'], '.') + 1);
                  $thumbName = $thumbdir . $tName . "." . strtolower($text);
                  if (Filter::$id && $thumb = getValueById("avatar", self::uTable, Filter::$id)) {
                      @unlink($thumbdir . $thumb);
                  }
                  move_uploaded_file($_FILES['avatar']['tmp_name'], $thumbName);
                  $data['avatar'] = $tName . "." . strtolower($text);
              }

              (Filter::$id) ? self::$db->update(self::uTable, $data, "id='" . Filter::$id . "'") : self::$db->insert(self::uTable, $data);
              $message = (Filter::$id) ? '<span>Success!</span>User updated successfully!' : '<span>Success!</span>User added successfully!';

              if (self::$db->affected()) {
                  Filter::msgOk($message);

                  if (isset($_POST['notify']) && intval($_POST['notify']) == 1) {
					  if(Filter::$id) {
						  $randpass = $this->getUniqueCode(12);
						  $newpass = sha1($randpass);
						  $pass = $randpass;
						  $pdata['password'] = $newpass;
						  self::$db->update(self::uTable, $pdata, "id='" . Filter::$id . "'");
					  } else {
						  $pass = $_POST['password'];
					  }
					  
                      require_once (BASEPATH . "lib/class_mailer.php");
                      $mailer = $mail->sendMail();

                      $row = Registry::get("Core")->getRowById("email_templates", 3);

                      $body = str_replace(array(
                          '[USERNAME]',
                          '[PASSWORD]',
                          '[NAME]',
                          '[SITE_NAME]',
                          '[URL]'), array(
                          $data['username'],
                          $pass,
                          $data['fname'] . ' ' . $data['lname'],
                          Registry::get("Core")->site_name,
                          Registry::get("Core")->site_url), $row->body);

                      $message = Swift_Message::newInstance()
								->setSubject($row->subject)
								->setTo(array($data['email'] => $data['fname'] . ' ' . $data['lname']))
								->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
								->setBody(cleanOut($body), 'text/html');

                      $numSent = $mailer->send($message);
                  }
              } else
                  Filter::msgAlert('<span>Alert!</span>Nothing to process.');
          } else
              print Filter::msgStatus();
      }

      /**
       * Users::updateProfile()
       * 
       * @return
       */
      public function updateProfile()
      {

          error_log('updateProfile()');
          Filter::checkPost('fname', "Please Enter First Name!");
          Filter::checkPost('lname', "Please Enter Last Name!");
          Filter::checkPost('email', "Please Enter Valid Email Address'!");

          if (!$this->isValidEmail($_POST['email']))
              Filter::$msgs['email'] = 'Entered Email Address Is Not Valid.';

          if (!empty($_FILES['avatar']['name'])) {
              if (!preg_match("/(\.jpg|\.png)$/i", $_FILES['avatar']['name'])) {
                  Filter::$msgs['avatar'] = "Illegal file type. Only jpg and png file types allowed.";
              }
              $file_info = getimagesize($_FILES['avatar']['tmp_name']);
              if (empty($file_info))
                  Filter::$msgs['avatar'] = "Illegal file type. Only jpg and png file types allowed.";
          }
		  
		  $this->verifyCustomFields("profile");



          if (empty(Filter::$msgs)) {

              $data = array(
                  'email' => sanitize($_POST['email']),
                  'lname' => sanitize($_POST['lname']),
                  'fname' => sanitize($_POST['fname']),
                  'newsletter' => intval($_POST['newsletter'])
				  );

              // Procces Avatar
              if (!empty($_FILES['avatar']['name'])) {
                  $thumbdir = UPLOADS;
                  $tName = "AVT_" . randName();
                  $text = substr($_FILES['avatar']['name'], strrpos($_FILES['avatar']['name'], '.') + 1);
                  $thumbName = $thumbdir . $tName . "." . strtolower($text);
                  if (Filter::$id && $thumb = getValueById("avatar", self::uTable, Filter::$id)) {
                      @unlink($thumbdir . $thumb);
                  }
                  move_uploaded_file($_FILES['avatar']['tmp_name'], $thumbName);
                  $data['avatar'] = $tName . "." . strtolower($text);
              }

              $userpass = getValueById("password", self::uTable, $this->uid);

              if ($_POST['password'] != "") {
                  $data['password'] = sha1($_POST['password']);
              } else
                  $data['password'] = $userpass;

			  $fl_array = array_key_exists_wildcard($_POST, 'custom_*', 'key-value');
			  if (isset($fl_array)) {
				  $fields = $fl_array;
				  $total = count($fields);
				  if (is_array($fields)) {
					  $fielddata = '';
					  foreach ($fields as $fid) {
						  $fielddata .= $fid . "::";
					  }
				  }
				  $data['custom_fields'] = $fielddata;
			  }

              $userID = (isset(Filter::$id) && Filter::$id > 0)?Filter::$id:$this->uid;
              error_log('examining user ID: ' . $userID);

              self::$db->update(self::uTable, $data, "id='" .$userID . "'");
              $this->update_tfs($userID,$data['custom_fields']);


              (self::$db->affected()) ? Filter::msgOk('<span>Success!</span> You have successfully updated your profile.') : Filter::msgAlert('<span>Alert!</span>Nothing to process.');
          } else
              print Filter::msgStatus();
      }


      /**
       * User::update_tfs()
       * @param $user_id
       * @param $data
       * ===================
       *  Updates TopForexSignal database with new user data
       *
       */
      public function update_tfs($user_id,$data)
      {
          $field_data = explode('::',$data);
          $trader_description = $field_data[0];
          $trader_byline = $field_data[1];
          $trader_mt4_login = $field_data[2];
          $trader_mt4_password = $field_data[3];
          $trader_display_name = $field_data[5];
          $trader_myfxbook_url = $field_data[6];
          $trader_master_copier_id = $field_data[7];

          $userObj = $this->getTraderID($user_id);
          $trader_id = $userObj->trader_id;

          $userObj = $this->getUserData();
          error_log('getUserData for user ' . $user_id .':' . print_r($userObj,true));

          $lastStatus = $this->getUserStatus($trader_id);


          if(isset($trader_master_copier_id) && strlen($trader_master_copier_id) > 3 && $lastStatus !==3)
          {
              $newStatus = 2;
          } elseif($lastStatus !==3) {
              $newStatus =1;
          }

          if($lastStatus == 3) $newStatus = 3;


          error_log('lastStatus:' . $lastStatus . ' newStatus:'. $newStatus . ' trader_master_copier_id:' . $trader_master_copier_id);

          $avatar = $userObj->avatar;
          $trader_avatar = '/thumbmaker.php?src='.UPLOADURL.(($avatar) ? $avatar : 'blank.png' ).'&w=160&h=160&s=1&aa=t1';
          $trader_data = array(
              'trader_full_name'=>$trader_display_name,
              'trader_img'=>$trader_avatar,
              'trader_email'=>$_POST['email'],
              'trader_account'=>$trader_mt4_login,
              'trader_account_password'=>$trader_mt4_password,
              'trader_bio'=>$trader_description,
              'trader_tagline'=>$trader_byline,
              'trader_myfxbook_url'=>$trader_myfxbook_url,
              'trader_master_copier_id'=>$trader_master_copier_id,
              'trader_status'=>$newStatus
          );

          error_log('updating user id: '. $trader_id . ' with data:'.PHP_EOL . print_r($trader_data,true) );//. PHP_EOL . 'post data:' . print_r($_POST,true));
          self::$db->update(self::traderTable, $trader_data, " trader_id = " . $trader_id);

          if($lastStatus == 0)
          {
              /**
               * A Top Forex Signal Trader with the email address [EMAIL] is requesting an account on 4X Solutions with the follow MT4 credentials
              MT4 Investor Login: [MT4LOGIN]
              MT4 Investor Password: [MT4PASSWORD]
               */
              $row = Registry::get("Core")->getRowById("email_templates", 16);

              $body = str_replace(array(
                  '[EMAIL]',
                  '[MT4LOGIN]',
                  '[MT4PASSWORD]',), array(
                  $_POST['email'],
                  $trader_mt4_login,
                  $trader_mt4_password
                 ), $row->body);

              $newbody = cleanOut($body);

              require_once (BASEPATH . "lib/class_mailer.php");



              $mailer = $mail->sendMail();
              $message = Swift_Message::newInstance()
                  ->setSubject($row->subject)
                  ->setTo(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
                  ->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
                  ->setBody($newbody, 'text/html');
              $mailer->send($message);
          } elseif($lastStatus == 1 && $newStatus == 2)
          {
              /** Let the user know they've been approved
               * */
              require_once (BASEPATH . "lib/class_mailer.php");
              $row = Registry::get("Core")->getRowById("email_templates", 17);
              error_log('DASHBOARD APPROVED! sending an email to: ' . $_POST['email']);
              $mailer = $mail->sendMail();
              $message = Swift_Message::newInstance()
                  ->setSubject($row->subject)
                  ->setTo(array($_POST['email'] => $trader_display_name))
                  ->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
                  //->setTo(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
                  //->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
                  ->setBody(cleanOut($row->body), 'text/html');
              $mailer->send($message);

          }



      }

      /**
       *
       */
      public function register_tfs($userID)
      {

          $regData = array(
              'trader_name' => sanitize($_POST['username']),
              'trader_email'=>$_POST['email'],
              'trader_tcreate'=>time(),
              'trader_tmodified'=>time()
          );
          $rowID = self::$db->insert($this::traderTable, $regData);


          $sql = "CREATE TABLE " . $rowID . "_tx LIKE _tx;";
          $row = self::$db->first($sql);


          self::$db->update(self::uTable, array('trader_id'=>$rowID), "id='" . $userID . "'");
          error_log('Trader ID and table successfully created for id:' . $row . ' for user id:' . $userID);



      }

      /**
       * User::register()
       * 
       * @return
       */
      public function register()
      {

          Filter::checkPost('username', "Please Enter Valid Username!");

          if ($value = $this->usernameExists($_POST['username'])) {
              if ($value == 1)
                  Filter::$msgs['username'] = 'Username Is Too Short (less Than 4 Characters Long).';
              if ($value == 2)
                  Filter::$msgs['username'] = 'Invalid Characters Found In Username.';
              if ($value == 3)
                  Filter::$msgs['username'] = 'Sorry, This Username Is Already Taken';
          }

          Filter::checkPost('fname', "Please Enter First Name!");
          Filter::checkPost('lname', "Please Enter Last Name!");
          Filter::checkPost('pass', "Please Enter Valid Password!");

          if (strlen($_POST['pass']) < 6)
              Filter::$msgs['pass'] = 'Password is too short (less than 6 characters long)';
          elseif (!preg_match("/^[a-z0-9_-]{6,15}$/", ($_POST['pass'] = trim($_POST['pass']))))
              Filter::$msgs['pass'] = 'Password entered contains invalid characters.';
          elseif ($_POST['pass'] != $_POST['pass2'])
              Filter::$msgs['pass'] = 'Your password did not match the confirmed password!.';

          Filter::checkPost('email', "Please Enter Valid Email Address!");

          if ($this->emailExists($_POST['email']))
              Filter::$msgs['email'] = 'Entered Email Address Is Already In Use.';

          if (!$this->isValidEmail($_POST['email']))
              Filter::$msgs['email'] = 'Entered Email Address Is Not Valid.';

		  Filter::checkPost('captcha', "Please enter captcha code!");
		  
		  if ($_SESSION['captchacode'] != $_POST['captcha'])
			  Filter::$msgs['captcha'] = "Entered captcha code is incorrect";

          $this->verifyCustomFields("register");
		  
          if (empty(Filter::$msgs)) {

              $token = (Registry::get("Core")->reg_verify == 1) ? $this->generateRandID() : 0;
              $pass = sanitize($_POST['pass']);

              if (Registry::get("Core")->reg_verify == 1) {
                  $active = "t";
              } elseif (Registry::get("Core")->auto_verify == 0) {
                  $active = "n";
              } else {
                  $active = "y";
              }

              $data = array(
                  'username' => sanitize($_POST['username']),
                  'password' => sha1($_POST['pass']),
                  'email' => sanitize($_POST['email']),
                  'fname' => sanitize($_POST['fname']),
                  'lname' => sanitize($_POST['lname']),
                  'token' => $token,
                  'active' => $active,
                  'created' => "NOW()"
				  );

			  $fl_array = array_key_exists_wildcard($_POST, 'custom_*', 'key-value');
			  if (isset($fl_array)) {
				  $fields = $fl_array;
				  $total = count($fields);
				  if (is_array($fields)) {
					  $fielddata = '';
					  foreach ($fields as $fid) {
						  $fielddata .= $fid . "::";
					  }
				  }
				  $data['custom_fields'] = $fielddata;
			  }

              $row_id = self::$db->insert(self::uTable, $data);

              $this->register_tfs($row_id);


              require_once (BASEPATH . "lib/class_mailer.php");

              if (Registry::get("Core")->reg_verify == 1) {
                  $actlink = Registry::get("Core")->site_url . "/activate.php";
                  $row = Registry::get("Core")->getRowById("email_templates", 1);

                  $body = str_replace(array(
                      '[NAME]',
                      '[USERNAME]',
                      '[PASSWORD]',
                      '[TOKEN]',
                      '[EMAIL]',
                      '[URL]',
                      '[LINK]',
                      '[SITE_NAME]'), array(
                      $data['fname'] . ' ' . $data['lname'],
                      $data['username'],
                      $_POST['pass'],
                      $token,
                      $data['email'],
                      Registry::get("Core")->site_url,
                      $actlink,
                      Registry::get("Core")->site_name), $row->body);

                  $newbody = cleanOut($body);

                  $mailer = $mail->sendMail();
                  $message = Swift_Message::newInstance()
							->setSubject($row->subject)
							->setTo(array($data['email'] => $data['username']))
							->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
							->setBody($newbody, 'text/html');

                  $mailer->send($message);

              } elseif (Registry::get("Core")->auto_verify == 0) {
                  $row = Registry::get("Core")->getRowById("email_templates", 14);

                  $body = str_replace(array(
                      '[NAME]',
                      '[USERNAME]',
                      '[PASSWORD]',
                      '[URL]',
                      '[SITE_NAME]'), array(
                      $data['fname'] . ' ' . $data['lname'],
                      $data['username'],
                      $_POST['pass'],
                      Registry::get("Core")->site_url,
                      Registry::get("Core") > site_name), $row->body);

                  $newbody = cleanOut($body);

                  $mailer = $mail->sendMail();
                  $message = Swift_Message::newInstance()
							->setSubject($row->subject)
							->setTo(array($data['email'] => $data['username']))
							->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
							->setBody($newbody, 'text/html');

                  $mailer->send($message);

              } else {
                  $row = Registry::get("Core")->getRowById("email_templates", 7);

                  $body = str_replace(array(
                      '[NAME]',
                      '[USERNAME]',
                      '[PASSWORD]',
                      '[URL]',
                      '[SITE_NAME]'), array(
                      $data['fname'] . ' ' . $data['lname'],
                      $data['username'],
                      $_POST['pass'],
                      Registry::get("Core")->site_url,
                      Registry::get("Core")->site_name), $row->body);

                  $newbody = cleanOut($body);

                  $mailer = $mail->sendMail();
                  $message = Swift_Message::newInstance()
							->setSubject($row->subject)
							->setTo(array($data['email'] => $data['username']))
							->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
							->setBody($newbody, 'text/html');

                  $mailer->send($message);
              }
              if (Registry::get("Core")->notify_admin) {
                  $arow = Registry::get("Core")->getRowById("email_templates", 13);

                  $abody = str_replace(array(
                      '[USERNAME]',
                      '[EMAIL]',
                      '[NAME]',
                      '[IP]'), array(
                      $data['username'],
                      $data['email'],
                      $data['fname'] . ' ' . $data['lname'],
                      $_SERVER['REMOTE_ADDR']), $arow->body);

                  $anewbody = cleanOut($abody);

                  $amailer = $mail->sendMail();
                  $amessage = Swift_Message::newInstance()
							->setSubject($arow->subject)
							->setTo(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
							->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
							->setBody($anewbody, 'text/html');

                  $amailer->send($amessage);
              }

              (self::$db->affected() && $mailer) ? print "OK" : Filter::msgError('<span>Error!</span>There was an error during registration process. Please contact the administrator...', false);
          } else


              print Filter::msgStatus();


      }

      /**
       * User::passReset()
       * 
       * @return
       */
      public function passReset()
      {

          Filter::checkPost('uname', "Please enter Valid Username!");
		  Filter::checkPost('email', "Please enter Email Address!");

          $uname = $this->usernameExists($_POST['uname']);
          if (strlen($_POST['uname']) < 4 || strlen($_POST['uname']) > 30 || !preg_match("/^[a-z0-9_-]{4,15}$/", $_POST['uname']) || $uname != 3)
              Filter::$msgs['uname'] = 'We are sorry, selected username does not exist in our database';

          if (!$this->emailExists($_POST['email']))
              Filter::$msgs['uname'] = 'Entered Email Address Does Not Exists.';

		  Filter::checkPost('captcha', "Please enter captcha code!");
		  if ($_SESSION['captchacode'] != $_POST['captcha'])
			  Filter::$msgs['captcha'] = "Entered captcha code is incorrect";

          if (empty(Filter::$msgs)) {

              $user = $this->getUserInfo($_POST['uname']);
              $randpass = $this->getUniqueCode(12);
              $newpass = sha1($randpass);

              $data['password'] = $newpass;

              self::$db->update(self::uTable, $data, "username = '" . $user->username . "'");

              require_once (BASEPATH . "lib/class_mailer.php");
              $row = Registry::get("Core")->getRowById("email_templates", 2);

              $body = str_replace(array(
                  '[USERNAME]',
                  '[PASSWORD]',
                  '[URL]',
                  '[LINK]',
                  '[IP]',
                  '[SITE_NAME]'), array(
                  $user->username,
                  $randpass,
                  Registry::get("Core")->site_url,
                  Registry::get("Core")->site_url,
                  $_SERVER['REMOTE_ADDR'],
                  Registry::get("Core")->site_name), $row->body);

              $newbody = cleanOut($body);

              $mailer = $mail->sendMail();
              $message = Swift_Message::newInstance()
						->setSubject($row->subject)
						->setTo(array($user->email => $user->username))
						->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
						->setBody($newbody, 'text/html');

              (self::$db->affected() && $mailer->send($message)) ? Filter::msgOk('<span>Success!</span>You have successfully changed your password. Please check your email for further info!', false) : Filter::msgError('<span>Error!</span>There was an error during the process. Please contact the administrator.', false);

          } else
              print Filter::msgStatus();
      }

      /**
       * User::activateAccount()
       * 
       * @return
       */
      public function activateAccount()
      {

          $data['active'] = "y";
		  self::$db->update(self::uTable, $data, "id = '" . Filter::$id . "'");
		  
		  require_once (BASEPATH . "lib/class_mailer.php");
		  $row = Registry::get("Core")->getRowById("email_templates", 16);
		  $usr = Registry::get("Core")->getRowById(self::uTable, Filter::$id);

		  $body = str_replace(array(
			  '[NAME]',
			  '[URL]',
			  '[SITE_NAME]'), array(
			  $usr->fname . ' ' .$usr->lname,
			  Registry::get("Core")->site_url,
			  Registry::get("Core")->site_name), $row->body);

		  $newbody = cleanOut($body);

		  $mailer = $mail->sendMail();
		  $message = Swift_Message::newInstance()
					->setSubject($row->subject)
					->setTo(array($usr->email => $usr->username))
					->setFrom(array(Registry::get("Core")->site_email => Registry::get("Core")->site_name))
					->setBody($newbody, 'text/html');

		  (self::$db->affected() && $mailer->send($message)) ? Filter::msgOk('<span>Success!</span>User have been successfully activated and email has been sent.', false) : Filter::msgError('<span>Error!</span>There was an error while sending email.');

      }
	  
      /**
       * User::activateUser()
       * 
       * @return
       */
      public function activateUser()
      {

          Filter::checkPost('email', "Please enter Valid Email Address!");

          if (!$this->emailExists($_POST['email']))
              Filter::$msgs['email'] = 'Entered Email Address Does Not Exists.';

          Filter::checkPost('token', "The token code is not valid!");

          if (!$this->validateToken($_POST['token']))
              Filter::$msgs['token'] = 'This account has been already activated!';

          if (empty(Filter::$msgs)) {
              $email = sanitize($_POST['email']);
              $token = sanitize($_POST['token']);
              $message = (Registry::get("Core")->auto_verify == 1) ? '<span>Success!</span>You have successfully activated your account!' : '<span>Success!</span>Your account is now active. However you still need to wait for administrative approval.';

              $data = array('token' => 0, 'active' => (Registry::get("Core")->auto_verify) ? "y" : "n");

              self::$db->update(self::uTable, $data, "email = '" . $email . "' AND token = '" . $token . "'");
              (self::$db->affected()) ? Filter::msgOk($message, false) : Filter::msgError('<span>Error!</span>There was an error during the activation process. Please contact the administrator.', false);
          } else
              print Filter::msgStatus();
      }

      /**
       * Users::getUserData()
       * 
       * @return
       */
      public function getUserData()
      {

          $sql = "SELECT *, DATE_FORMAT(created, '%a. %d, %M %Y') as cdate," 
		  . "\n DATE_FORMAT(lastlogin, '%a. %d, %M %Y') as ldate" 
		  . "\n FROM " . self::uTable 
		  . "\n WHERE id = " . $this->uid;
          $row = self::$db->first($sql);

          return ($row) ? $row : 0;
      }

      public function getTraderID($id)
      {

          $sql = "SELECT trader_id, DATE_FORMAT(created, '%a. %d, %M %Y') as cdate,"
              . "\n DATE_FORMAT(lastlogin, '%a. %d, %M %Y') as ldate"
              . "\n FROM " . self::uTable
              . "\n WHERE id = " . $id;
          $row = self::$db->first($sql);

          return ($row) ? $row : 0;
      }

      /**
       * Users::getUserMembership()
       * 
       * @return
       */
      public function getUserMembership()
      {

          $sql = "SELECT u.*, m.title," 
		  . "\n DATE_FORMAT(u.mem_expire, '%d. %b. %Y.') as expiry" 
		  . "\n FROM " . self::uTable . " as u" 
		  . "\n LEFT JOIN " . Membership::mTable . " as m ON m.id = u.membership_id" 
		  . "\n WHERE u.id = " . $this->uid;
          $row = self::$db->first($sql);

          return ($row) ? $row : 0;
      }

      /**
       * Users::calculateDays()
       * 
       * @return
       */
      public function calculateDays($membership_id)
      {

          $now = date('Y-m-d H:i:s');
          $row = self::$db->first("SELECT days, period FROM " . Membership::mTable . " WHERE id = '" . (int)$membership_id . "'");
          if ($row) {
              switch ($row->period) {
                  case "D":
                      $diff = $row->days;
                      break;
                  case "W":
                      $diff = $row->days * 7;
                      break;
                  case "M":
                      $diff = $row->days * 30;
                      break;
                  case "Y":
                      $diff = $row->days * 365;
                      break;
              }
              $expire = date("Y-m-d H:i:s", strtotime($now . + $diff . " days"));
          } else {
              $expire = "0000-00-00 00:00:00";
          }
          return $expire;
      }

      /**
       * User::trialUsed()
       * 
       * @return
       */
      public function trialUsed()
      {
          $sql = "SELECT trial_used" 
		  . "\n FROM " . self::uTable 
		  . "\n WHERE id = " . $this->uid
		  . "\n LIMIT 1";
          $row = self::$db->first($sql);

          return ($row->trial_used == 1) ? true : false;
      }

      /**
       * Users::validateMembership()
       * 
       * @return
       */
      public function validateMembership()
      {

          $sql = "SELECT mem_expire" 
		  . "\n FROM " . self::uTable 
		  . "\n WHERE id = " . $this->uid
		  . "\n AND TO_DAYS(mem_expire) > TO_DAYS(NOW())";
          $row = self::$db->first($sql);

          return ($row) ? $row : 0;
      }

      /**
       * Users::checkMembership()
       * 
       * @param string $memids
       * @return
       */
      public function checkMembership($memids)
      {

          $m_arr = explode(",", $memids);
          reset($m_arr);

          if ($this->logged_in and $this->validateMembership() and in_array($this->membership_id, $m_arr)) {
              return true;
          } else
              return false;
      }

	  /**
	   * verifyCustomFields()
	   * 
	   * @param mixed $type
	   * @return
	   */
	  public function verifyCustomFields($type)
	  {
	
		  if ($fdata = self::$db->fetch_all("SELECT * FROM " . Core::fTable . " WHERE type = '" . $type . "' AND active = 1 AND req = 1")) {
	
			  $res = '';
			  foreach ($fdata as $cfrow) {
				  if (empty($_POST['custom_' . $cfrow->name]))
					  $res .= Filter::$msgs['custom_' . $cfrow->name] = "Please Enter " . $cfrow->title;
			  }
			  return $res;
			  unset($cfrow);
	
		  }
	
	  } 
	  
      /**
       * Users::usernameExists()
       * 
       * @param mixed $username
       * @return
       */
      private function usernameExists($username)
      {

          $username = sanitize($username);
          if (strlen(self::$db->escape($username)) < 4)
              return 1;

          //Username should contain only alphabets, numbers, underscores or hyphens.Should be between 4 to 15 characters long
		  $valid_uname = "/^[a-z0-9_-]{4,15}$/"; 
          if (!preg_match($valid_uname, $username))
              return 2;

          $sql = self::$db->query("SELECT username" 
		  . "\n FROM " . self::uTable 
		  . "\n WHERE username = '" . $username . "'" 
		  . "\n LIMIT 1");

          $count = self::$db->numrows($sql);

          return ($count > 0) ? 3 : false;
      }

      /**
       * User::emailExists()
       * 
       * @param mixed $email
       * @return
       */
      private function emailExists($email)
      {

          $sql = self::$db->query("SELECT email" 
		  . "\n FROM " . self::uTable 
		  . "\n WHERE email = '" . sanitize($email) . "'" 
		  . "\n LIMIT 1");

          if (self::$db->numrows($sql) == 1) {
              return true;
          } else
              return false;
      }

      /**
       * User::isValidEmail()
       * 
       * @param mixed $email
       * @return
       */
      private function isValidEmail($email)
      {
          if (function_exists('filter_var')) {
              if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                  return true;
              } else
                  return false;
          } else
              return preg_match('/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', $email);
      }

      /**
       * User::validateToken()
       * 
       * @param mixed $token
       * @return
       */
      private function validateToken($token)
      {
          $token = sanitize($token, 40);
          $sql = "SELECT token" 
		  . "\n FROM " . self::uTable 
		  . "\n WHERE token ='" . self::$db->escape($token) . "'" 
		  . "\n LIMIT 1";
          $result = self::$db->query($sql);

          if (self::$db->numrows($result))
              return true;
      }

      /**
       * Users::getUniqueCode()
       * 
       * @param string $length
       * @return
       */
      private function getUniqueCode($length = "")
      {
          $code = sha1(uniqid(rand(), true));
          if ($length != "") {
              return substr($code, 0, $length);
          } else
              return $code;
      }

      /**
       * Users::generateRandID()
       * 
       * @return
       */
      private function generateRandID()
      {
          return sha1($this->getUniqueCode(24));
      }

      /**
       * Users::levelCheck()
       * 
       * @param string $levels
       * @return
       */
      public function levelCheck($levels)
      {
          $m_arr = explode(",", $levels);
          reset($m_arr);

          if ($this->logged_in and in_array($this->userlevel, $m_arr))
              return true;
      }

      /**
       * Users::getUserLevels()
       * 
       * @return
       */
      public function getUserLevels($level = false)
      {
          $arr = array(
              9 => 'Super Admin',
              1 => 'Registered User',
              2 => 'User Level 2',
              3 => 'User Level 3',
              4 => 'User Level 4',
              5 => 'User Level 5',
              6 => 'User Level 6',
              7 => 'User Level 7');

          $list = '';
          foreach ($arr as $key => $val) {
              if ($key == $level) {
                  $list .= "<option selected=\"selected\" value=\"$key\">$val</option>\n";
              } else
                  $list .= "<option value=\"$key\">$val</option>\n";
          }
          unset($val);
          return $list;
      }

      /**
       * Users::getUserFilter()
       * 
       * @return
       */
      public static function getUserFilter()
      {
          $arr = array(
              'username-ASC' => 'Username &uarr;',
              'username-DESC' => 'Username & &darr;',
              'fname-ASC' => 'First Name &uarr;',
              'fname-DESC' => 'First Name &darr;',
              'lname-ASC' => 'Last Name &uarr;',
              'lname-DESC' => 'Last Name &darr;',
              'email-ASC' => 'Email Address &uarr;',
              'email-DESC' => 'Email Address &darr;',
              'mem_expire-ASC' => 'Membership Expire &uarr;',
              'mem_expire-DESC' => 'Membership Expire &darr;',
              );

          $filter = '';
          foreach ($arr as $key => $val) {
              if ($key == get('sort')) {
                  $filter .= "<option selected=\"selected\" value=\"$key\">$val</option>\n";
              } else
                  $filter .= "<option value=\"$key\">$val</option>\n";
          }
          unset($val);
          return $filter;
      }
  }
?>