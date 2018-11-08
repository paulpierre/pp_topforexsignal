<?php

/** =============
 *  Signals Model
 *  =============
 */

class Signal extends Database {

    public $table_name;
    const SIGNAL_TABLE = 'signals';


    public function get_signals()
    {
        $db_columns = array(
            'signal_id',
            'signal_status',
            'signal_pair',
            'signal_tp',
            'signal_sl',
            'signal_price',
            'signal_winloss',
            'signal_result',
            'signal_tmodified',
            'signal_tcreate',
            'signal_date',
            'signal_action',
            'trader_id'
        );



        $result = $this->db_retrieve($this::SIGNAL_TABLE. ' ORDER BY signal_tcreate DESC',$db_columns,false,null,true);

        if(empty($result)) return false;
        return $result;
    }

    public function add_signal($o)
    {
        if(empty($o)) return false;
        $db_columns = array(
            'signal_status'=>$o['signal_status'],
            'signal_action'=>$o['signal_action'],
            'signal_pair'=>$o['signal_pair'],
            'signal_tp'=>$o['signal_tp'],
            'signal_sl'=>$o['signal_sl'],
            'signal_price'=>$o['signal_price'],
            'trader_id'=>$o['trader_id'],
            'signal_result'=>$o['signal_result'],
            'signal_winloss'=>$o['signal_winloss'],
            'signal_tmodified'=>date("Y-m-d H:i:s"),
            'signal_tcreate'=>date("Y-m-d H:i:s"),
            'signal_date'=>date("Y-m-d H:i:s")

        );
        $result = $this->db_create($this::SIGNAL_TABLE ,$db_columns);
        if(empty($result)) return false; else return true;
    }


    public function update_signal($o)
    {


        if(!isset($o['signal_id']))
        {
            return false;
        }
        $signal_id = $o['signal_id'];

        $db_columns = array(
            'signal_id'=>$o['signal_id'],
            'signal_action'=>$o['signal_action'],
            'signal_status'=>$o['signal_status'],
            'signal_pair'=>$o['signal_pair'],
            'signal_tp'=>$o['signal_tp'],
            'signal_sl'=>$o['signal_sl'],
            'trader_id'=>$o['trader_id'],
            'signal_price'=>$o['signal_price'],
            'signal_result'=>$o['signal_result'],
            'signal_winloss'=>$o['signal_winloss'],
            'signal_tmodified'=>date("Y-m-d H:i:s"),
            'signal_date'=>$o['signal_date']

        );

        $db_conditions = array(
            'signal_id'=>$signal_id
        );

        $result = $this->db_update($this::SIGNAL_TABLE,$db_columns,$db_conditions,false);

        //error_log(basename(__FILE__) . ' UPDATE result: ' . print_r($result,true));

        if(empty($result)) return false; else return true;
    }




}