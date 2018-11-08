<?php
class Trader extends Database{




    /** ================
     *	USER INFORMATION
     * ================
     */


    public function get_verified_accounts()
    {
        /* GRAB ALL USERS AND MAP USER NAMES */
        $q='SELECT sum(commissions.trade_profit) as total_profit,commissions.user_id,clients.user_status, clients.user_full_name as user_name, clients.user_email as email, clients.user_country as country, DATE_FORMAT(from_unixtime(user_tsignup), \'%Y/%m/%d %T\') as registered FROM commissions INNER JOIN clients ON commissions.user_id=clients.user_broker_account group by user_id order by total_profit DESC';
        return $this->db_query($q);
    }

    public function get_all_accounts()
    {
        /* GET ALL NEW REGISTERD USERS REGARDLESS OF CONFIRMATION */
        $q='select user_id,user_full_name,user_broker_account,user_email,user_country,user_status , broker_name,user_tmodified, DATE_FORMAT(from_unixtime(user_tcreate), \'%Y/%m/%d %T\') as created, DATE_FORMAT(from_unixtime(user_tmodified), \'%Y/%m/%d %T\') as modified from clients ORDER BY user_tmodified desc';
        return $this->db_query($q);
    }



}