<?php
global $controllerID,$controllerObject,$controllerFunction;

/** ===============
 *  User Controller
 *  ===============
 */



switch($controllerFunction)
{


    /** ==============
     *  /user/register
     *  ==============
     *  register a new user
     */

    case 'register':

        $userBrokerID = (isset($_POST['userBrokerID']))?$_POST['userBrokerID']:$_GET['userBrokerID'];
        $userBrokerName = (isset($_POST['userBrokerName']))?$_POST['userBrokerName']:$_GET['userBrokerName'];
        $userFullName = (isset($_POST['userFullName']))?$_POST['userFullName']:$_GET['userFullName'];
        $userEmail = (isset($_POST['userEmail']))?$_POST['userEmail']:$_GET['userEmail'];
        $userCountry = (isset($_POST['userCountry']))?$_POST['userCountry']:$_GET['userCountry'];

        /** =========================================================
         *  Email, full name, broker, and country are required fields
         *  =========================================================
         */

        if(isset($userEmail) && isset($userFullName) && isset($userBrokerName) && isset($userCountry))
        {
            $userInstance = new User();

            //lets make sure the user doesn't exist
            $user_id = $userInstance->get_user_account_by_email($userEmail);
            if(empty($user_id))
            {
                $data = array();
                if(isset($userBrokerID)) $data['id'] = $userBrokerID;
                $data['email'] = $userEmail;
                $data['country'] = $userCountry;
                $data['broker'] = $userBrokerName;
                $data['name'] = $userFullName;


                $result = $userInstance->add_user_account($data);
                if($result) {
                    api_response(array(
                        'code'=> RESPONSE_SUCCESS,
                        'data'=> array('message'=>'Successfully added user to system')
                    ));
                } else {
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>ERROR_INTERNAL_ERROR)
                    ));
                }
            }
            else {
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>'This email address has already been registred. Please try again with a different email address.')
                ));
            }




        } else {
            /** ======================================
             *  They did not POST the appropriate data
             *  ======================================
             */
            api_response(array(
                'code'=> RESPONSE_ERROR,
                'data'=> array('message'=>ERROR_INVALID_PARAMETERS)
            ));
        }

    break;

    case 'register2':

        $userTraderID = (isset($_POST['userTraderID']))?$_POST['userTraderID']:$_GET['userTraderID'];
        $userFullName = (isset($_POST['userFullName']))?$_POST['userFullName']:$_GET['userFullName'];
        $userEmail = (isset($_POST['userEmail']))?$_POST['userEmail']:$_GET['userEmail'];
        $userCountry = (isset($_POST['userCountry']))?$_POST['userCountry']:$_GET['userCountry'];

        /** =========================================================
         *  Email, full name, broker, and country are required fields
         *  =========================================================
         */

        if(isset($userEmail) && isset($userFullName) && isset($userTraderID) && isset($userCountry))
        {
            $userInstance = new User();

            //lets make sure the user doesn't exist
            $user_id = $userInstance->get_user_account_by_email($userEmail);
            if(empty($user_id))
            {


                $result = $userInstance->add_user_account(array(
                    'email'=>$userEmail,
                    'country'=>$userCountry,
                    'name'=>$userFullName,
                    'trader'=>$userTraderID
                ));
                if($result) {
                    api_response(array(
                        'code'=> RESPONSE_SUCCESS,
                        'data'=> array('message'=>'Successfully added user to system')
                    ));
                } else {
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>ERROR_INTERNAL_ERROR)
                    ));
                }
            }
            else {
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>'This email address has already been registered. Please try again with a different email address.')
                ));
            }




        } else {
            /** ======================================
             *  They did not POST the appropriate data
             *  ======================================
             */
            api_response(array(
                'code'=> RESPONSE_ERROR,
                'data'=> array('message'=>ERROR_INVALID_PARAMETERS)
            ));
        }

        break;




    default:
        api_response(array(
            'code'=> RESPONSE_ERROR,
            'data'=> array('message'=>ERROR_INVALID_FUNCTION)
        ));
    break;

}


