<?php

/** ==============
 *  CarbonFX Model
 *  ==============
 */

class CarbonFX extends Database {

    public $table_name;
    const CARBONFX_TABLE = 'carbonfx';


    public function get_accounts()
    {
        $db_columns = array(
            'id',
            'trader_id',
            'trader_investor_pw',
            'trader_name',
            'trader_master_pw',
            'trader_server',
            'trader_status',
            'trader_online',
            'trader_type',
            'trader_copy_master_id'
        );

        $db_conditions = array(
            'trader_status'=>1
        );

        $result = $this->db_retrieve($this::CARBONFX_TABLE,$db_columns,$db_conditions,false);
        if(empty($result)) return false;
        return $result;
    }



}