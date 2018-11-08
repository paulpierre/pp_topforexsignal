<?php

/** ==========
 *  User model
 *  ==========
 */

class User extends Database {

    public $table_name;
    const USER_TABLE = 'clients';


    public function is_verified($userEmail,$userBrokerID)
    {
        $db_columns = array(
            'user_email',
            'user_broker_account'
        );

        $db_conditions = array(
            'user_email'=>$userEmail,
            'user_broker_account'=>$userBrokerID
        );

        $result = $this->db_retrieve($this::USER_TABLE,$db_columns,$db_conditions,null,false);
        if(empty($result)) return false;
        else return true;
    }

    public function get_user_broker_account_list()
    {
        $db_columns = array(
            'user_broker_account'
        );


        $db_conditions = array(
           'user_broker_account >'=>1
        );

        $result = $this->db_retrieve($this::USER_TABLE,$db_columns,$db_conditions,null,false);
        //exit('<pre>'.print_r($result,true));
        if(empty($result)) return false;
        $accountList = Array();

        //return $result;

        foreach($result as $item)
        {
            $accountList[] = $item['user_broker_account'];
        }

        return $accountList;
    }

    public function get_user_account_by_email($user_email)
    {
        if(!isset($user_email)) return false;

        $db_columns = array(
            'user_id'
        );

        $db_conditions = array(
            'user_email'=>$user_email,
        );

        $result = $this->db_retrieve($this::USER_TABLE,$db_columns,$db_conditions,null,false);
        return $result[0]['user_id'];
    }


    public function add_user_account($o) {

        $traderID = 0;
        if(isset($o['trader']))
        {
            $traderInstance = new Trader();
            $traderID = $traderInstance->get_trader_account_id_by_trader_name($o['trader']);
            unset($traderInstance);
        }

        $db_columns = array(
            'user_broker_account'=>isset($o['id'])?$o['id']:'',
            'user_full_name'=>isset($o['name'])?$o['name']:'',
            'user_country'=>isset($o['country'])?$o['country']:'',
            'broker_name'=>isset($o['broker'])?$o['broker']:'',
            'user_tsignup'=>isset($o['date'])?strtotime($o['date']):'',
            'user_email'=>isset($o['email'])?$o['email']:'',
            'user_status'=>isset($o['status'])?$o['status']:0,
            'trader_name'=>isset($o['trader'])?$o['trader']:'',
            'trader_id'=>isset($o['trader_id'])?$o['trader_id']:$traderID,
            'user_tmodified'=>time(),
            'user_tcreate'=>time()
        );
        $result = $this->db_create($this::USER_TABLE,$db_columns);
        if(empty($result)) return false; else return true;
    }


    public function update_user_account($o)
    {
        //the columns we want to retrieve
        $db_columns = array(
            'user_id',
            'broker_id',
            'user_name',
            'user_country',
            'user_full_name',
            'user_status',
            'user_tsignup',
            'user_broker_account',
            'user_email',
            'broker_name',
            'user_phone'
        );
        $db_conditions = array();

        /** ==================================================
         *  for any of the provided criteria, lets inclusively
         *  query all of the provided parameters
         *  ==================================================
         */
        foreach($o as $key=>$value)
        {
            if($key == 'id') $db_conditions['user_broker_account'] = $value;
            if($key == 'email') $db_conditions['user_email'] = $value;
            if($key == 'broker') $db_conditions['broker_name'] = $value;
        }
        $result = $this->db_retrieve($this::USER_TABLE,$db_columns,$db_conditions,null,false);
        if(empty($result))
        {
            /** ====================================================
             *  User record with any provided credentials not found,
             *  so lets just create a new account in this case
             *  ====================================================
             */
            $result = $this->add_user_account($o);
            return $result;
        }

        if(isset($result[0]['user_id'])) unset($result[0]['user_id']);
        $db_columns = $result[0];
        foreach($o as $key=>$value)
        {
            if($key == 'id') $db_columns['user_broker_account'] = $value;
            if($key == 'email') $db_columns['user_email'] = $value;
            if($key == 'broker') $db_columns['broker_name'] = $value;
            if($key == 'date') $db_columns['user_tsignup'] = strtotime($value);
            if($key == 'country') $db_columns['user_country'] = $value;
            if($key == 'phone') $db_columns['user_phone'] = $value;
            if($key == 'name') $db_columns['user_full_name'] = $value;

        }

        $db_conditions = array('user_id'=>$result[0]['user_id']);
        $result = $this->db_update($this::USER_TABLE,$db_columns,$db_conditions,false);
        if(empty($result)) return false; else return true;
    }


}