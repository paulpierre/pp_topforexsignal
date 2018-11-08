<?php
class Trader extends Order {

    const TABLE_NAME = 'traders';
    const CLIENTTRADES_TABLE = 'clienttrades';

    public $trader_id;
    public $trader_account;
    public $trader_type;

    public function get_trader_account_id($trader_id)
    {
        $db_columns = array(
            'trader_account'
        );
        $db_conditions = array(
            'trader_id'=>$trader_id
        );

        $result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,null,false);
        if(empty($result)) return false;
        $this->trader_id  = $trader_id;
        $this->trader_account = $result[0];
        return $result[0];
    }

    public function get_trader_account_id_by_trader_name($trader_name)
    {
        $db_columns = array(
            'trader_id'
        );
        $db_conditions = array(
            'trader_status'=>3,
            'trader_name'=>$trader_name
        );

        $result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,null,false);
        if(empty($result)) return false;
        return $result[0]['trader_id'];
    }





    public function get_trader_master_copier_id($trader_id)
    {

        $db_columns = array(
            'trader_master_copier_id'
        );
        $db_conditions = array(
            'trader_id'=>$trader_id

        );

        $result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,null,false);
        if(empty($result)) return false;
        return $result[0]['trader_master_copier_id'];
    }


    public function get_trader_account_by_server_name($name)
    {
        $db_columns = array('trader_id','trader_account','trader_status');
        $db_conditions = array('trader_server'=>$name);
        $result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,null,false);
        if(empty($result)) return false;

        foreach($result as $r)
        {
            if(intval($r['trader_status']) > 0) $traderIDs[] = $r['trader_account'];
        }
        return $traderIDs;

    }

    /** ==============================================
     *  Temporary function to add fake followers in DB
     *  ==============================================
     */
    public function _add_followers($exclusionList)
    {


        $db_columns = array('trader_id','trader_followers','trader_name');
        $db_conditions = array('trader_status'=>3);
        $result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,null,false);
        if(empty($result)) return false;

        $accounts = array();

        foreach($result as $trader)
        {
            $traderID = $trader['trader_id'];
            if(in_array($traderID,$exclusionList)) continue;

            $traderName = $trader['trader_name'];
            $r = rand(0,3);
            $traderFollowers = intval($trader['trader_followers']) + $r;

            $db_columns = array(
                'trader_followers'=>$traderFollowers
            );

            $db_conditions = array(
                'trader_id'=>$traderID
            );

            $result = $this->db_update($this::TABLE_NAME,$db_columns,$db_conditions,false);
            if($result) $accounts['success'][] = array(
                'trader_id'=>$traderID,
                'trader_name'=>$traderName,
                'trader_followers' => $traderFollowers,
                'followers_added'=> $r
            );
            else $accounts['failure'][] = array(
                'trader_id' =>$traderID,
                'trader_name'=>$traderName
            );

        }
        return $accounts;



        //error_log(basename(__FILE__) . ' UPDATE result: ' . print_r($result,true));
    }


    public function get_trader_myfxbook_urls()
    {
        $db_columns = array(
            'trader_id',
            'trader_myfxbook_url'
        );
        $db_conditions = array(
            'trader_status'=>3
        );
        $result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,null,true);
        if(empty($result)) return false;
        return $result;
    }

    public function get_trader_myfxbook_url($trader_id)
    {
        //if no valid ID is provided, return false
        if(!isset($trader_id) || !is_numeric($trader_id)) return false;


        $db_columns = array(
            'trader_myfxbook_url'
        );
        $db_conditions = array(
            'trader_id'=>$trader_id
        );
        $result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,null,false);

        if(empty($result)) return false;
        return $result[0]['trader_myfxbook_url'];
    }



    public function get_traders_list()
    {
        $db_columns = array(
            'trader_id',
            'trader_name'
        );
        $db_conditions = array(
            'trader_status'=>3
        );
        //$result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,' ORDER BY mfb_gain_abs DESC',true); //trader_growth
        $result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,' ORDER BY trader_sort ASC',false); //trader_growth
        if(empty($result)) return false;
        return $result;
    }



    /** =========================
     *  Trader Weekly Performance
     *  =========================
     */


    function get_trader_weekly_performance($trader_id)
    {

        $db_columns = array(
            'trader_name',
            'trader_full_name',
            'trader_growth',
            'trader_type',
            'mfb_gain_abs',
            'trader_balance',
        );
        $db_conditions = array(
            'trader_id'=>$trader_id
        );
        $result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,null,true);
        $_pips = $this->get_trader_pips_gained($trader_id,time()-(60*60*24*7),time());
        $weekly_pip_gain =($_pips[0]['pips'])?$_pips[0]['pips']:0;
        $weekly_growth = number_format($this->get_7_day_growth(),2);
        $_growth = intval($result[0]['trader_growth']);


        switch($result[0]['trader_type'])
        {

            case 1:
                $_g = $this->get_daily_growth();
                $total_growth = $_g[count($_g)-2]['growth'];
                break;
            default:
            case 0:
                $total_growth = (intval($result[0]['mfb_gain_abs'])!==0)?$result[0]['mfb_gain_abs']:$_growth;
                break;
        }

        $trader_data = array(
            'trader_name'=>$result[0]['trader_full_name'],
            'trader_id'=> $trader_id,
            'trader_growth_total'=>number_format($total_growth,2),
            'trader_growth_week'=>$weekly_growth,
            'trader_pips_week'=>$weekly_pip_gain
        );

        return $trader_data;


    }


    public function get_trader_pips_gained($trader_id,$start_date,$end_date)
    {
        $q = 'select sum(tx_pips) as pips from ' . $trader_id .'_tx where DATE_FORMAT(from_unixtime(tx_tclose), \'%Y/%m/%d %T\') between DATE_FORMAT(from_unixtime(' . $start_date .'), \'%Y/%m/%d %T\') AND DATE_FORMAT(from_unixtime(' . $end_date .'), \'%Y/%m/%d %T\') ;';
        return  $this->db_query($q);
    }

    public function get_trader_ids()
    {
        $db_columns = array(
            'trader_id',
            'trader_name',
            'trader_full_name'
        );

        $db_conditions  = array(
            'trader_status'=>3
        );

        $result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,' ORDER BY trader_name ASC',false); //trader_growth
        if(empty($result)) return false;
        return $result;
    }


    public function get_trader_billing_email($trader_id)
    {
        $q = "select custom_fields from users where trader_id=" . $trader_id;
        $result =  $this->db_query($q);

        $traderEmail  = explode('::',$result[0]['custom_fields']);
        return $traderEmail[4];


    }



    public function is_user($trader_id)
    {
        $db_columns = array(
            'trader_id',
            'trader_account'
        );
        $db_conditions = array(
            'trader_id'=>$trader_id
        );

        $result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,null,false);
        if(empty($result)) return false;
        $this->trader_id = $trader_id;
        $this->trader_account = $result[0]['trader_account'];
        return true;
    }

    public function get_trader_profile($trader_id)
    {
        //if this isn't a real user return false
        if(!$this->is_user($trader_id)) {
            //error_log(basename(__FILE__) . ' get_trader_profile: user ' . $trader_id . ' does NOT exist! returning false..');
            return false;
        }

        /**
         * Total Growth                 ->  get_total_growth()
         * Average Monthly Growth       ->  get_avg_monthly_growth()
         * System Type                  ->  trader_system_type
         * Max Historic Drawdown        ->  trader_max_draw
         * Master Account Leverage      ->  trader_leverage
         * Minimum Account Leverage     ->  trader_min_account_leverage
         * Age in weeks                 ->  get_account_age()
         * Recommended Min Investment   ->  trader_min_investment
         * Live Master Account          ->  trader_account_type 0=test 1=live
         * Master Balance               ->  trader_balance
         * Winning Trade Percentage     ->  (trader_win / (trader_win + trader_loss)) * 100
         * Master Account Broker        ->  broker_id (look it up)
         * Avg Traders Per Week         ->  get_avg_trades_per_week()
         * Average Trade Length         ->  get_avg_trade_length()
         * Trader Broker -              ->  get_trader_broker_name()
         * Trader Bio                   ->  trader_bio
         * Trader Image                 ->  trader_img
         * Trader Tagline               ->  trader_tagline
         * Trader Name                  ->  trader_name
         * Trader account status        ->  trader_status
         */


        $db_columns = array(
            'trader_id',
            'trader_bio',
            'trader_img',
            'trader_type',
            'trader_name',
            'trader_full_name',
            'trader_currency',
            'trader_tagline',
            'trader_min_investment',
            'trader_account_type',
            'trader_system_type',
            'trader_leverage',
            'trader_min_account_leverage',
            'trader_growth', //total growth
            'mfb_drawdown', //max historic drawdown
            'trader_max_draw',
            'mfb_gain_monthly',
            'trader_avg_mo_growth',
            'mfb_gain_abs',
            'trader_balance', //master balance
            'trader_win',
            'trader_loss',
            'trader_deposit_withdrawal',     //initial deposit
            'trader_status',
            'trader_myfxbook_url',
            'trader_show_open_trades',
            'trader_followers'
        );
        $db_conditions = array(
            'trader_id'=>$this->trader_id
        );
        $result = $this->db_retrieve(self::TABLE_NAME,$db_columns,$db_conditions,null,true);

        $profile = $result[0];

        $account_type = intval($profile['trader_type']);

        $account_age = $this->get_account_age();
        $growth_30d = number_format($this->get_30_day_growth(),2);

        $profit = $this->get_trader_total_profit();
        $initial_deposit = $this->get_trader_calculated_initial_deposit();

        switch($account_type)
        {

            case 1:
                //$total_growth = $this->get_total_growth();  //
                $master_balance = $this->get_trader_calculated_balance();
                //$total_growth = ((($master_balance - $initial_deposit)/$initial_deposit)*100);
                $_g = $this->get_daily_growth();
                $total_growth = $_g[count($_g)-2]['growth'];
                //error_log(print_r($_g,true));
                //exit();
                //$growth_avg_monthly = $this->get_avg_monthly_growth();//$total_growth / ((time()-$account_age['timestamp'])/2592000);//$this->get_avg_monthly_growth();
                $growth_avg_monthly =  $total_growth / ((time()-$account_age['timestamp'])/2592000);
                $win_percentage = ($this->get_trader_winning_trades_count()/$this->get_trader_total_trades_count()) * 100;
                $max_drawdown = floatval($profile['trader_max_draw']);
                break;
            default:
            case 0:
                $total_growth = isset($profile['mfb_gain_abs'])?$profile['mfb_gain_abs']:$this->get_total_growth();
                //$growth_avg_monthly = $total_growth / ((time()-$account_age['timestamp'])/2592000);//$this->get_avg_monthly_growth();
                $growth_avg_monthly = isset($profile['mfb_gain_monthly'])?$profile['mfb_gain_monthly']: $total_growth / ((time()-$account_age['timestamp'])/2592000);
                $max_drawdown = $profile['mfb_drawdown'];
                $master_balance = $profile['trader_balance'];
                $win_percentage = ($profile['trader_win']/($profile['trader_win']+$profile['trader_loss'])) * 100;

            break;
        }


        $broker_name = $this->get_trader_broker_name();
        $avg_trade_per_week = $this->get_avg_trades_per_week();
        $avg_trade_length = duration($this->get_avg_trade_length());

        switch($profile['trader_account_type']) {
            case TRADER_ACCOUNT_DEMO: $account_type = 'Demo'; break;
            case TRADER_ACCOUNT_LIVE: $account_type = 'Live';  break;
            default: $account_type = 'Unknown'; break;
        }

        switch($profile['trader_system_type']){
            case TRADER_MANUAL: $system_type = 'Manual';break;
            case TRADER_EA: $system_type = 'Expert Advisor'; break;
            case TRADER_HYBRID: $system_type = "Hybrid"; break;
            default: $system_type = 'Unknown'; break;
        }

        return array(
            //Trader information
             'user_name'                =>  $profile['trader_name']
            ,'full_name'                =>  $profile['trader_full_name']
            ,'bio'                      =>  $profile['trader_bio']
            ,'tagline'                  =>  $profile['trader_tagline']
            ,'currency'                 =>  $profile['trader_currency']
            ,'id'                       =>  $profile['trader_id']
            ,'img'                      =>  $profile['trader_img']
            ,'broker_name'              =>  $broker_name
            ,'system_type'              =>  $system_type
            ,'account_description'      =>  $account_type
            ,'account_type'             =>  $profile['trader_account_type']
            ,'myfxbook'                 =>  $profile['trader_myfxbook_url']
            ,'show_open_trades'         =>  $profile['trader_show_open_trades']
            ,'profit'                   =>  $profit
            //Trader performance
            ,'total_growth'             =>  $total_growth
            //,'total_growth'              => $profile['mfb_gain_abs']
            ,'initial_balance'          =>  $initial_deposit
            ,'30day_growth'             =>  $growth_30d
            ,'avg_monthly_growth'       =>  $growth_avg_monthly
            ,'avg_trade_length'         =>  $avg_trade_length
            //,'max_drawdown'             =>  floatval($profile['trader_max_draw'])
            ,'max_drawdown'             =>  $max_drawdown

            ,'leverage'                 =>  $profile['trader_leverage']
            ,'min_leverage'             =>  $profile['trader_min_account_leverage']
            ,'account_age'              =>  time_ago($account_age['timestamp'])
            ,'master_balance'           =>  $master_balance
            ,'win_percentage'           =>  $win_percentage
            ,'avg_trade_per_week'       =>  $avg_trade_per_week
            ,'avg_trade_length'         =>  $avg_trade_length
            ,'min_investment'           =>  $profile['trader_min_investment']
            ,'followers'                =>  $profile['trader_followers']
        );
    }



    public function get_initial_deposit()
    {
        return $this->get_deposit('initial');
    }

    public function get_trader_broker_name()
    {
        $q = 'SELECT brokers.broker_name FROM brokers LEFT JOIN traders ON traders.broker_id=brokers.broker_id WHERE trader_id=' . $this->trader_id;
        $result = $this->db_query($q);
        return !empty($result[0]['broker_name'])?$result[0]['broker_name']:false;
    }

    public function get_trader_total_followers($account_id,$range='')
    {
        $q = 'select distinct(trader_account_id) from clienttrades  where trader_name=' . $account_id . $range ;
        return  $this->db_query($q);
    }

    public function get_trader_trade_volume($account_id,$range='')
    {
        $q = 'select  sum(trade_size) as trade_size from clienttrades where trader_name=' . $account_id . $range;
        return  $this->db_query($q);
    }

    public function get_trader_profitable_trade_volume($account_id,$range='')
    {
        $q = 'select  sum(trade_size) as trade_size from clienttrades where trade_profit > 0 AND  trader_name=' . $account_id . $range;
        return  $this->db_query($q);
    }



    public function get_trader_daily_trade_volume($account_id,$range='')
    {
        $q = 'select sum(trade_size) as trade_size,cast(trade_close_time AS DATE) as date from clienttrades where trader_name=' . $account_id. $range. ' group by date(trade_close_time)';
        return $this->db_query($q);
    }

    public function get_trader_daily_profitable_trades($account_id,$range='')
    {
        $q = 'select count(trade_profit) as trades,sum(trade_size) as trade_size, cast(trade_close_time AS DATE) as date from clienttrades where trader_name=' . $account_id. $range. ' and trade_profit > 0 group by date(trade_close_time) ORDER BY trade_close_time ASC';
        return $this->db_query($q);
    }

    public function get_trader_daily_unprofitable_trades($account_id,$range='')
    {
        $q = 'select count(trade_profit) as trades,sum(trade_size) as trade_size, cast(trade_close_time AS DATE) as date from clienttrades where trader_name=' . $account_id. $range. ' and trade_profit <= 0 group by date(trade_close_time)  ORDER BY trade_close_time ASC';
        return $this->db_query($q);
    }

    public function get_trader_daily_total_trades($account_id,$range='')
    {
        $q = 'select count(trade_profit) as trades,cast(trade_close_time AS DATE) as date from clienttrades where trader_name=' . $account_id . $range. ' group by date(trade_close_time)';
        return $this->db_query($q);
    }

    public function get_trader_total_trades($account_id,$range='')
    {
        $q = 'select count(trade_id) as trade_count, sum(trade_size) as trade_size,sum(trade_profit) as trade_profit,cast(trade_close_time AS DATE) as date from clienttrades where trader_name=' . $account_id . $range;
        return $this->db_query($q);
    }


    public function get_trader_total_profitable_trades($account_id,$range='')
    {
        //$q = 'select count(trade_id) as trade_count, sum(trade_size) as trade_size,sum(trade_profit) as trade_profit,cast(trade_close_time AS DATE) as date from clienttrades where trade_profit > 0 AND trader_name=' . $account_id . $range;
        $q = 'select count(tx_id) as trade_count, sum(tx_size) as trade_size,sum(tx_profit) as trade_profit,cast(tx_tclose AS DATE) as date from '.$account_id.'_tx where tx_profit > 0 AND trader_id=' . $account_id . str_replace('trade_close_time','from_unixtime(tx_tclose)',$range);


        return $this->db_query($q);
    }

    public function get_trader_total_unprofitable_trades($account_id,$range='')
    {
        //$q = 'select count(trade_id) as trade_count, sum(trade_size) as trade_size,sum(trade_profit) as trade_profit,cast(trade_close_time AS DATE) as date from clienttrades where trade_profit <=0 AND trader_name=' . $account_id . $range;
        $q = 'select count(tx_id) as trade_count, sum(tx_size) as trade_size,sum(tx_profit) as trade_profit,cast(tx_tclose AS DATE) as date from '.$account_id.'_tx where tx_profit <= 0 AND trader_id=' . $account_id . str_replace('trade_close_time','from_unixtime(tx_tclose)',$range);

        return $this->db_query($q);
    }

    public function get_trader_daily_commissions($account_id,$range='')
    {
        $q = 'select  cast(trade_close_time AS DATE) as date,count(trade_id) as trade_count,sum(trade_size) as total_size, sum(trade_size)*5 as commission from clienttrades where trader_name=' . $account_id . $range . '  AND trade_profit > 0 group by date';
        return $this->db_query($q);
    }

    public function get_trader_total_commissions($account_id,$range='')
    {
        //$q = 'select  trade_profit, sum(trade_size)*5 as commission from clienttrades where trade_profit > 0 AND trader_name=' . $account_id . $range;

        $q = 'select cast(trade_close_time AS DATE) as date,count(trade_id) as trade_count,sum(trade_size) as total_size, sum(trade_size)*5 as commission from clienttrades where trader_name=' . $account_id . $range . ' AND trade_profit > 0'; //AND  YEAR(trade_close_time) = YEAR(CURDATE()) AND MONTH(trade_close_time) = MONTH(CURDATE()
        return $this->db_query($q);
    }

    /** =================
     *  Commissions Admin
     * ==================
     */

    public function get_all_trader_pending_commissions($range='')
    {
        $traderIDs = $this->get_trader_ids();
        $traderData = array();
        foreach($traderIDs as $trader)
        {
            $traderID = $trader['trader_id'];
            $traderEmail  = $this->get_trader_billing_email($traderID);
            $traderUserName = $trader['trader_name'];
            $traderDisplayName = $trader['trader_full_name'];
            $paidCommissions = $this->get_trader_paid_commissions($traderID);
            $pendingCommissions = $this->get_trader_pending_commissions($traderID);
            $traderData[]= array(
            'id'=>$traderID,
            'pending_commissions' => $pendingCommissions,
            'paid_commissions' => $paidCommissions[0]['payout'],
            'display_name' => $traderDisplayName,
            'user_name' => $traderUserName,
            'email'=>$traderEmail
                );
        }

        return $traderData;

    }




    /** ===========
     *  Commissions
     *  ============
     */

    public function get_trader_pending_commissions($account_id,$range='')
    {
        /**
         *  We grab total commissions earned and subtract commissions paid which will leave us with an outstanding balance
         */
        $traderName = $this->get_trader_master_copier_id($account_id);
        $trader_total_commissions = $this->get_trader_total_commissions("'". $traderName . "'");
        $trader_paid_commissions = $this->get_trader_paid_commissions($account_id);
        return floatval($trader_total_commissions[0]['commission']) - floatval($trader_paid_commissions[0]['payout']);
    }




    public function get_trader_approved_commissions($account_id,$range='')
    {
        /**
         *  We grab the commissions with a status of 0 from the payout table
         */

        $q = 'select cast(payout_tmodified AS DATE) as date, sum(payout_amount) as payout from payout where payout_status=0 AND trader_id=' . $account_id . $range; //AND  YEAR(trade_close_time) = YEAR(CURDATE()) AND MONTH(trade_close_time) = MONTH(CURDATE()
        return $this->db_query($q);
    }

    public function get_trader_paid_commissions($account_id,$range='')
    {
        /**
         *  We grab commission payment from the db with a status of 1
         */
        $q = 'select cast(payout_tmodified AS DATE) as date, sum(payout_amount) as payout from payout where payout_status=1 AND trader_id=' . $account_id . $range; //AND  YEAR(trade_close_time) = YEAR(CURDATE()) AND MONTH(trade_close_time) = MONTH(CURDATE()
        return $this->db_query($q);
    }

    public function get_trader_commission_history($account_id,$range='')
    {
        $q = 'select cast(payout_tmodified AS DATE) as date,payout_notes,payout_email,payout_method,payout_status,payout_amount,trader_id,payout_id from payout where trader_id=' . $account_id . $range; //AND  YEAR(trade_close_time) = YEAR(CURDATE()) AND MONTH(trade_close_time) = MONTH(CURDATE()
        return $this->db_query($q);
    }

    public function update_trader_pending_commissions($traderData)
    {


        $db_columns = array(
            'trader_id'=>$traderData['trader_id'],
            'payout_notes'=>$traderData['trader_note'],
            'payout_amount'=>$traderData['trader_commission'],
            'payout_status'=>1,
            'payout_method'=>1,
            'payout_email'=>$traderData['trader_email'],
            'payout_tmodified'=>date("Y-m-d H:i:s"),
            'payout_tcreate'=>date("Y-m-d H:i:s")
        );
        $result = $this->db_create('payout',$db_columns);
        if(empty($result)) return false; else return true;

    }




    public function get_account_age()
    {
        $q = 'SELECT DATE_FORMAT(FROM_UNIXTIME(tx_topen), \'%m/%d/%Y\') as date, tx_topen as timestamp, DATEDIFF(CURDATE(),FROM_UNIXTIME(tx_topen)) as days FROM ' . $this->trader_id.'_tx WHERE tx_topen > 0 ORDER BY tx_topen ASC LIMIT 1;';
        $result = $this->db_query($q);
        return !empty($result[0])?$result[0]:false;
    }

    public function update_master_trade($tradeData)
    {
        if(empty($tradeData)) return false;

        $column_count = count($tradeData);
        $columns = '';
        $values = '';
        $i=0;
        foreach($tradeData as $columnKey=>$columnVal)
        {
            $db_value = $columnVal;
            $db_column = $columnKey;
            $separator = ($i<=$column_count && $i > 0)?',':'';
            $columns .= $separator . $db_column;
            $values .= $separator . (is_numeric($db_value) ? $db_value: '\''.$db_value.'\'');
            $i++;

        }
        $q = ('INSERT IGNORE INTO ' . $this::CLIENTTRADES_TABLE . '('  . $columns . ')' .
            ' VALUES( '.$values .')');

        $result = $this->db_query($q);
        //error_log(basename(__FILE__) . ' UPDATE result: ' . print_r($result,true));
        return $result;
    }



    public function update_mfb_stats($data)
    {

        $traderID = $data['trader_id'];

        if(!$this->is_user($traderID))
        {
            //error_log(basename(__FILE__) . ' traderID ' . $traderID . ' does NOT exist! returning false for update_mfb_status');
            return false;
        }

        $db_columns = array(
            'mfb_gain_abs'=>trim($data['gain_absolute']),
            'mfb_gain_daily'=>trim($data['gain_daily']),
            'mfb_gain_monthly'=>trim($data['gain_monthly']),
            'mfb_drawdown'=>trim($data['drawdown']),
            'mfb_balance'=>trim($data['balance']),
            'mfb_equity'=>trim($data['equity']['amount']),
            'mfb_equity_percentage'=>trim($data['equity']['percentage']),
            'mfb_equity_high'=>trim($data['equity_highest']['amount']),
            'mfb_equity_high_date'=>trim($data['equity_highest']['date']),
            'mfb_profit'=>trim($data['profit']),
            'mfb_interest'=>trim($data['interest']),
            'mfb_deposits'=>trim($data['deposits']),
            'mfb_withdrawals'=>trim($data['withdrawals'])
        );

        $db_conditions = array(
            'trader_id'=>$traderID
        );

        $result = $this->db_update($this::TABLE_NAME,$db_columns,$db_conditions,false);

        //error_log(basename(__FILE__) . ' UPDATE result: ' . print_r($result,true));


        if(empty($result)) return false; else return true;
    }

    public function update_trader_mt4_server($tradeData)
    {
        $traderServer = $tradeData['trader_server'];
        $traderLogin = $tradeData['trader_account'];


        $db_columns = array(
            'trader_server'=>$traderServer
        );

        $db_conditions = array(
            'trader_account'=>$traderLogin
        );

        $result = $this->db_update($this::TABLE_NAME,$db_columns,$db_conditions,false);

        //error_log(basename(__FILE__) . ' UPDATE result: ' . print_r($result,true));


        if(empty($result)) return false; else return true;
    }


}