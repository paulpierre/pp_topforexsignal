<?php
class Broker extends Database {

    const TABLE_NAME = 'brokers';

    public $broker_id;
    public $trader_id;
    public $trader_account;



    public function get_last_record_date()
    {
        $q = 'SELECT id,trade_date FROM commissions ORDER BY trade_date DESC LIMIT 1';
        $result = $this->db_query($q);
        return $result[0]['trade_date'];
    }


    public function update_commission($o)
    {
        $hadError = false;
        foreach($o as $item)
        {
            $db_columns = $item;
            //$result = $this->db_create('commissions',$db_columns);
            $q = "INSERT IGNORE INTO commissions(trade_ticket,user_id,trade_symbol,trade_profit,trade_date,broker_id,trade_tcreate) VALUES(" . $item['trade_ticket']. ",". $item['user_id']. ",'" . $item['trade_symbol']. "'," . $item['trade_profit']. ",'" . $item['trade_date']. "'," . $item['broker_id']. ",'" . $item['trade_tcreate']. "')";
            $result = $this->db_query($q);
            if(empty($result)) continue;
            if(!$result) $hadError = true;
        }
        if($hadError) return false; else return true;
    }
}