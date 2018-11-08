<?php
  /**
   * Stripe IPN
   *
   * @package Membership Manager Pro
   * @author wojoscripts.com
   * @copyright 2013
   * @version $Id: ipn.php, v2.00 2013-05-08 10:12:05 gewa Exp $
   */
  define("_VALID_PHP", true);
  require_once ("../../init.php");

  if (!$user->logged_in)
      exit;  
	  

  ini_set('log_errors', true);
  ini_set('error_log', dirname(__file__) . '/ipn_errors.log');

  if (isset($_POST['processStripePayment'])) {
	  require_once (dirname(__file__) . '/lib/Stripe.php');
	  
      $key = $db->first("SELECT * FROM gateways WHERE name = 'stripe'");
      $stripe = array("secret_key" => $key->extra, "publishable_key" => $key->extra3);
      Stripe::setApiKey($stripe['secret_key']);

      try {
          $charge = Stripe_Charge::create(array(
              "amount" => round($_POST['amount'] * 100, 0), // amount in cents, again
              "currency" => $_POST['currency_code'],
              "card" => array(
                  "number" => $_POST['card-number'],
                  "exp_month" => $_POST['card-expiry-month'],
                  "exp_year" => $_POST['card-expiry-year'],
                  "cvc" => $_POST['card-cvc'],
                  ),
              "description" => $_POST['item_name']));
			  
          $json = json_decode($charge);
          $amount_charged = round(($json->{'amount'} / 100), 2);
		  
          
		  /* == Payment Completed == */
          $inv_id = $_POST['item_number'];
		  $row = $db->first("SELECT * FROM " . Membership::mTable . " WHERE id='" . (int)$inv_id . "'");
		  
		  if ($row) {
			  $data = array(
				  'txn_id' => time(),
				  'membership_id' => $row->id,
				  'user_id' => $user->uid,
				  'rate_amount' => floatval($amount_charged),
				  'ip' => $_SERVER['REMOTE_ADDR'],
				  'date' => "NOW()",
				  'pp' => "Stripe",
				  'currency' => sanitize($_POST['currency_code']),
				  'status' => 1);
					  
                  $db->insert(Membership::pTable, $data);

                  $udata = array(
                      'membership_id' => $row->id,
                      'mem_expire' => $user->calculateDays($row->id),
                      'trial_used' => ($row->trial == 1) ? 1 : 0);

                  $db->update(Users::uTable, $udata, "id=" . $user->uid);


			  $jn['type'] = 'success';
			  $jn['message'] = 'Thank you payment completed';
			  print json_encode($jn);

		  
			  /* == Notify Administrator == */
			  require_once (BASEPATH . "lib/class_mailer.php");
			  $row2 = Core::getRowById(Core::eTable, 5);

			  $body = str_replace(array(
				  '[USERNAME]',
				  '[ITEMNAME]',
				  '[PRICE]',
				  '[STATUS]',
				  '[PP]',
				  '[IP]'), array(
				  $user->username,
				  $row->title,
				  $core->formatMoney($amount_charged),
				  "Completed",
				  "Stripe",
				  $_SERVER['REMOTE_ADDR']), $row2->body);

			  $newbody = cleanOut($body);

			  $mailer = $mail->sendMail();
			  $message = Swift_Message::newInstance()
						->setSubject($row2->subject)
						->setTo(array($core->site_email => $core->site_name))
						->setFrom(array($core->site_email => $core->site_name))
						->setBody($newbody, 'text/html');

			  $mailer->send($message);
			  
			  /* == Notify User == */
			  $row3 = Core::getRowById(Core::eTable, 15);
			  //$uemail = getValueById("email", Users::uTable, intval($user_id));

			  $body2 = str_replace(array(
				  '[USERNAME]',
				  '[MNAME]',
				  '[VALID]'), array(
				  $user->username,
				  $row->title,
				  $udata['mem_expire']), $row3->body);

			  $newbody2 = cleanOut($body2);

			  $mailer2 = $mail->sendMail();
			  $message2 = Swift_Message::newInstance()
						->setSubject($row3->subject)
						->setTo(array($user->email => $user->username))
						->setFrom(array($core->site_email => $core->site_name))
						->setBody($newbody2, 'text/html');

			  $mailer2->send($message2);

		  }
      }
      catch (Stripe_CardError $e) {
          //$json = json_decode($e);
          $body = $e->getJsonBody();
          $err = $body['error'];
          $json['type'] = 'error';
          Filter::$msgs['status'] = 'Status is:' . $e->getHttpStatus() . "\n";
          Filter::$msgs['type'] = 'Type is:' . $err['type'] . "\n";
          Filter::$msgs['code'] = 'Code is:' . $err['code'] . "\n";
          Filter::$msgs['param'] = 'Param is:' . $err['param'] . "\n";
          Filter::$msgs['msg'] = 'Message is:' . $err['message'] . "\n";
          $json['message'] = Filter::msgStatus();
          print json_encode($json);
		  
		  
      }
      catch (Stripe_InvalidRequestError $e) {}
      catch (Stripe_AuthenticationError $e) {}
      catch (Stripe_ApiConnectionError $e) {}
      catch (Stripe_Error $e) {}
      catch (exception $e) {}
  }
?>