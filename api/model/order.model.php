<?php

/** ===========
 *  Order Model
 *  ===========
 */

class Order extends Database {

    public $table_name;
    const TRADER_TABLE = 'traders';


    public function get_order_history()
    {
        $db_columns = array(
            'tx_id',
            'tx_ticket',
            'tx_topen',
            'tx_tclose',
            'tx_open_price',
            'tx_close_price',
            'tx_type',
            'tx_size',
            'tx_item',
            'tx_profit'
        );
        $db_conditions = array(
            'tx_type'=>TX_TYPE_BUY,
            'tx_type'=>TX_TYPE_SELL
        );

        $result = $this->db_retrieve($this->trader_id . '_tx',$db_columns,$db_conditions,null,true);

        //$data = self::add_pips($result);
        //$result = $data;

        if(empty($result)) return false;
        return $result;
    }


    public function get_open_orders()
    {

        $digits = intval($this->get_trader_digits($this->trader_id));
        $trader_type = $this->get_trader_type($this->trader_id);


        $lot_modifier = 0;
        /*
        switch($trader_type)
        {
            case 1:
                switch($this->trade_id)
                {
                    case 48:
                        $lot_modifier = 0.92; //green monster
                        break;

                    case 64:
                        $lot_modifier = 0.56; //marigold`
                        break;
                }

                break;

            default:
            case 0:
                $lot_modifier = 0;
                break;
        }*/

        switch($digits)
        {
            case 5:
                $pip_multiplier = .1;
                break;
            case 1:
            case 4:

                $pip_multiplier = 10;
                break;

            default:
                $pip_multiplier = 1;
                break;

        }
        $db_columns = array(
            'tx_id',
            'tx_ticket',
            'date_format(from_unixtime(tx_topen),\'%b %d, %Y %l:%i %p\') as tx_topen',
            'date_format(from_unixtime(tx_tclose),\'%b %d, %Y %l:%i %p\') as tx_tclose',
            'tx_open_price',
            'tx_close_price',
            'tx_current_price',
            'tx_type',
            ($lot_modifier > 0)?'TRUNCATE(ROUND((tx_size + ' . $lot_modifier . '),2),2) as tx_size':'tx_size',
            'tx_item',
            ($lot_modifier>0)?'TRUNCATE(ROUND(((tx_size + ' . $lot_modifier. ') * 10 * ROUND((tx_pips * ' . $pip_multiplier .'),3)),2),2) as tx_profit':'tx_profit',
            'TRUNCATE((tx_pips * ' . $pip_multiplier .'),1) as tx_pips'
        );

        $q = ' WHERE (tx_type=' . TX_TYPE_BUY . ' OR tx_type=' . TX_TYPE_SELL . ') AND tx_tclose=0 AND tx_status=0 ORDER BY tx_topen ASC';
        $result = $this->db_retrieve($this->trader_id . '_tx',$db_columns,'',$q,true);




        if(empty($result)) return false;



        return $result;
    }


    public function get_trader_winning_trades_count()
    {
        $q = 'SELECT count(tx_id) as winning_trades FROM ' . $this->trader_id .'_tx WHERE tx_tclose > 0 AND tx_type < 6 AND tx_profit > 0';
        $result = $this->db_query($q);
        return (!empty($result[0]['winning_trades']))?intval($result[0]['winning_trades']):false;
    }

    public function get_trader_losing_trades_count()
    {
        $q = 'SELECT count(tx_id) as losing_trades FROM ' . $this->trader_id .'_tx WHERE tx_tclose > 0 AND tx_type < 6 AND tx_profit < 0';
        $result = $this->db_query($q);
        return (!empty($result[0]['losing_trades']))?intval($result[0]['losing_trades']):false;
    }

    public function get_trader_total_trades_count()
    {
        $q = 'SELECT count(tx_id) as total_trades FROM ' . $this->trader_id .'_tx WHERE tx_tclose > 0 AND tx_type < 6';
        $result = $this->db_query($q);
        return (!empty($result[0]['total_trades']))?intval($result[0]['total_trades']):false;
    }


    public function get_trader_digits($trader_id)
    {
        $db_columns = array('trader_digits');
        $db_conditions = array('trader_id'=>$trader_id);
        $result = $this->db_retrieve('traders',$db_columns,$db_conditions,null,true);
        if(empty($result)) return false;
        return $result[0]['trader_digits'];
    }

    public function get_trader_type($trader_id)
    {

        $db_columns = array(
            'trader_type'
        );
        $db_conditions = array(
            'trader_id'=>$trader_id

        );

        $result = $this->db_retrieve(self::TRADER_TABLE,$db_columns,$db_conditions,null,false);
        if(empty($result)) return false;
        return intval($result[0]['trader_type']);
    }


    public function get_closed_orders()
    {
        $digits = $this->get_trader_digits($this->trader_id);
        $trader_type = $this->get_trader_type($this->trader_id);

        $lot_modifier = 0;
        /*
        switch($trader_type)
        {
            case 1:

                switch($this->trader_id)
                {
                    case 48:
                        $lot_modifier = .92; //green monster
                        break;

                    case 64:
                        $lot_modifier = 0.56; //marigold`
                        break;


                }

                break;

            default:
            case 0:
                $lot_modifier = 0;
                break;
        }
        */

        switch($digits)
        {
            case 5:
                $pip_multiplier = .1;
                break;
            case 1:
            case 4:

                $pip_multiplier = 10;
                break;

            default:
                $pip_multiplier = 1;
                break;

        }

        $db_columns = array(
            'tx_id',
            'tx_ticket',
            'date_format(from_unixtime(tx_topen),\'%b %d, %Y %l:%i %p\') as tx_topen',
            'date_format(from_unixtime(tx_tclose),\'%b %d, %Y %l:%i %p\') as tx_tclose',
            'tx_open_price',
            'tx_close_price',
            'tx_type',
            ($lot_modifier > 0)?'TRUNCATE(ROUND((tx_size + ' . $lot_modifier . '),2),2) as tx_size':'tx_size',
            'tx_item',
            'TRUNCATE(ROUND((tx_pips * ' . $pip_multiplier .'),3),1) as tx_pips',
             ($lot_modifier>0)?'TRUNCATE(ROUND(((tx_size + ' . $lot_modifier. ') * 10 * ROUND((tx_pips * ' . $pip_multiplier .'),3)),2),2) as tx_profit':'tx_profit',

        );

        $q = ' WHERE (tx_type=' . TX_TYPE_BUY . ' OR tx_type=' . TX_TYPE_SELL . ') AND tx_tclose > 0  ORDER BY tx_tclose ASC';
        $result = $this->db_retrieve($this->trader_id . '_tx',$db_columns,'',$q,true);

        if(empty($result)) return false;
        return $result;
    }



    //add pip data for closed trades
    public function add_pips($trader_history)
    {
        for($i=0;$i < count($trader_history);$i++)
        {
            if(intval($trader_history[$i]['tx_tclose'])>0)
                $trader_history[$i]['tx_pips'] = number_format(floatval((floatval($trader_history[$i]['tx_close_price']) - floatval($trader_history[$i]['tx_open_price']))) *10000,0);
        }
        return $trader_history;
    }

    public function get_avg_trade_length()
    {
        $q = 'SELECT SUM(TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(tx_tclose),FROM_UNIXTIME(tx_topen))))/count(*) as avg_duration FROM ' . $this->trader_id .'_tx WHERE tx_tclose > 0 AND tx_type < 6';
        $result = $this->db_query($q);
        return (!empty($result[0]['avg_duration']))?floor($result[0]['avg_duration']):false;
    }


    public function get_drawdown($mode=null)
    {
        switch($mode)
        {
            /** ========================
             *  DAILY ACCOUNT DRAWDOWN %
             *  ========================
             */
            case 'day':
                break;
            /** ===================================
             *  DAILY ACCOUNT MAX EQUITY DRAWDOWN %
             *  ===================================
             */
            case 'equity':
                break;

            /** =======================
             *  GET HISTORIC DRAWDOWN %
             *  =======================
             */
            default:
            case 'max':
                $db_columns = array(
                    'trader_max_draw'
                );
                $db_conditions = array(
                    'trader_id'=>$this->trader_id
                );

                $result = $this->db_retrieve(self::TRADER_TABLE,$db_columns,$db_conditions,null,true);
                if(empty($result)) return false;
                return floatval($result[0]['trader_max_draw']);
                break;
        }
    }

    public function get_avg_trades_per_day()
    {
        $trades = $this->get_trade_count('day');
        $trade_count = 0;
        foreach($trades as $item)
        {
            $trade_count +=$item['trades'];
        }
        return $trade_count / count($trades);
    }

    public function get_avg_trades_per_week()
    {
        $trades = $this->get_trade_count('week');
        $trade_count = 0;
        foreach($trades as $item)
        {
            $trade_count +=$item['trades'];
        }
        return number_format($trade_count / count($trades));
    }

    public function get_avg_trades_per_month()
    {
        $trades = $this->get_trade_count('month');
        $trade_count = 0;
        foreach($trades as $item)
        {
            $trade_count +=$item['trades'];
        }
        return $trade_count / count($trades);
    }

    public function get_trader_total_profit()
    {

        /**
         *    SELECT SUM(tx_profit) as profit, Month(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\')) as month, Year(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\')) as year,DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\'), \'%m/%d/%Y\') as date FROM ' . $this->trader_id .'_tx WHERE tx_tclose > 0 AND tx_type < 6 GROUP BY DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\'), \'%Y/%m/%d\') ';

         */


        $q = 'SELECT SUM(tx_profit) as profit FROM ' . $this->trader_id .'_tx WHERE tx_tclose > 0 AND tx_type < 6';
        $result = $this->db_query($q);
        return $result[0]['profit'];
    }

    public function get_trade_count($mode=null)
    {
        switch($mode)
        {
            case 'day':
                $q = 'SELECT COUNT(tx_profit) as trades, Day(FROM_UNIXTIME(tx_tclose)) as day,Year(FROM_UNIXTIME(tx_tclose)) as year,Month(FROM_UNIXTIME(tx_tclose)) as month FROM ' . $this->trader_id .'_tx WHERE tx_tclose > 0 AND tx_type < 6 GROUP BY Day(FROM_UNIXTIME(tx_tclose))';
                $result = $this->db_query($q);
                return $result;
                break;

            case 'week':
                $q = 'SELECT COUNT(tx_profit) as trades, Week(FROM_UNIXTIME(tx_tclose)) as week,Year(FROM_UNIXTIME(tx_tclose)) as year,Month(FROM_UNIXTIME(tx_tclose)) as month FROM ' . $this->trader_id .'_tx WHERE tx_tclose > 0 AND tx_type < 6 GROUP BY Week(FROM_UNIXTIME(tx_tclose))';
                $result = $this->db_query($q);
                return $result;
                break;

            case 'month':
                $q='SELECT COUNT(tx_profit) as trades, Year(FROM_UNIXTIME(tx_tclose)) as year,Month(FROM_UNIXTIME(tx_tclose)) as month FROM ' . $this->trader_id .'_tx WHERE tx_tclose > 0 AND tx_type < 6 GROUP BY Month(FROM_UNIXTIME(tx_tclose))';
                $result = $this->db_query($q);
                return $result;
                break;
        }


    }


    public function get_profit($mode=null)
    {
        switch($mode)
        {
            /** ============
             *  DAILY PROFIT
             *  ============
             *  SELECT 	tx_id as id,
             *  sum(tx_profit) as profit,
             *  DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),'+00:00','+07:00'), '%m/%d/%Y') as date
             *  FROM 10026_tx
             *  WHERE tx_type < 6
             *  GROUP BY DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),'+00:00','+07:00'), '%m/%d/%Y');
             */
            case 'day':
                //$q = 'SELECT SUM(tx_profit) as profit, Month(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\')) as month, Year(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\')) as year,DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\'), \'%m/%d/%Y\') as date FROM ' . $this->trader_id .'_tx WHERE tx_tclose > 0 AND tx_type < 6 GROUP BY DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\'), \'%Y/%m/%d\') ';
                $q = 'SELECT SUM(tx_profit) as profit, Month(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\')) as month, Year(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\')) as year,DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\'), \'%m/%d/%Y\') as date FROM ' . $this->trader_id .'_tx WHERE tx_tclose > 0 AND tx_type < 6 GROUP BY DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\'), \'%Y/%m/%d\') ';

                $result = $this->db_query($q);
                return $result;
                break;

            /** ==============
             *  MONTHLY PROFIT
             *  ==============
             */
            case 'month':
                //OLDER $q = 'SELECT SUM(tx_profit) as profit, Year(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose)),\'+00:00\',\'+07:00\')) as year,Month(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose)),\'+00:00\',\'+07:00\')) as month FROM ' . $this->trader_id .'_tx  WHERE tx_tclose > 0 AND tx_type < 6 GROUP BY Month(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose)),\'+00:00\',\'+07:00\'))';
                //OLD $q = 'SELECT tx_profit as profit, Year(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\')) as year , Month(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\')) as month,DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\'), \'%m/%d/%Y\') as date FROM ' . $this->trader_id .'_tx  WHERE tx_tclose > 0 AND tx_type < 6 GROUP BY Month(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\')) ';
                $q = 'SELECT tx_profit as profit, Year(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\')) as year , Month(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\')) as month,DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\'), \'%m/%d/%Y\') as date FROM ' . $this->trader_id .'_tx  WHERE tx_tclose > 0 AND tx_type < 6 GROUP BY Month(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\')) ';

                $result = $this->db_query($q);
                return (!empty($result))?$result:false;
                break;


            /** ============
             *  TOTAL PROFIT
             *  ============
             */
            default:
                //$q = 'SELECT SUM(tx_profit) as profit FROM ' . $this->trader_id . '_tx WHERE tx_tclose > 0 AND tx_type < 6';
                $q = 'SELECT SUM(tx_profit) as profit FROM ' . $this->trader_id . '_tx WHERE tx_tclose > 0 AND tx_type < 6';

                //by default lets return total sum account deposits
                $result = $this->db_query($q);
                return (!empty($result[0]['profit']))?$result[0]['profit']:false;
                break;
        }
    }

    public function get_7_day_growth()
    {
        $q = 'SELECT SUM(tx_profit) as profit FROM ' . $this->trader_id .'_tx  WHERE tx_tclose > 0 AND tx_type < 6 AND CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\') BETWEEN CONVERT_TZ(NOW(),\'+00:00\',\'+07:00\')  - INTERVAL 7 DAY AND CONVERT_TZ(NOW(),\'+00:00\',\'+07:00\') GROUP BY DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\'), \'%m/%d/%Y\')';
        $profits = $this->db_query($q);

        $initial_deposit = $this->get_initial_deposit();
        $cumulative_growth = 0;
        $cumulative_profit = 0;

        foreach($profits as $item)
        {
            $day_growth = round(number_format(($item['profit'] /$initial_deposit) * 100,2),2,PHP_ROUND_HALF_DOWN);
            $day_profit = $item['profit'];
            $cumulative_growth +=$day_growth;
            $cumulative_profit +=$day_profit;
        }

        $growth = ($cumulative_profit/$initial_deposit)*100;
        return (!empty($profits))?$growth:false;
    }

    public function get_30_day_growth()
    {
        $q = 'SELECT SUM(tx_profit) as profit FROM ' . $this->trader_id .'_tx  WHERE tx_tclose > 0 AND tx_type < 6 AND CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\') BETWEEN CONVERT_TZ(NOW(),\'+00:00\',\'+07:00\')  - INTERVAL 30 DAY AND CONVERT_TZ(NOW(),\'+00:00\',\'+07:00\') GROUP BY DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(tx_tclose),\'+00:00\',\'+07:00\'), \'%m/%d/%Y\')';
        $profits = $this->db_query($q);

        $initial_deposit = $this->get_initial_deposit();
        $cumulative_growth = 0;
        $cumulative_profit = 0;

        foreach($profits as $item)
        {
            $day_growth = round(number_format(($item['profit'] /$initial_deposit) * 100,2),2,PHP_ROUND_HALF_DOWN);
            $day_profit = $item['profit'];
            $cumulative_growth +=$day_growth;
            $cumulative_profit +=$day_profit;
        }

        $growth = ($cumulative_profit/$initial_deposit)*100;
        return (!empty($profits))?$growth:false;
    }

    public function get_account_balance()
    {
        $db_columns = array(
            'trader_balance'
        );
        $db_conditions = array(
            'trader_id'=>$this->trader_id
        );

        $result = $this->db_retrieve(self::TRADER_TABLE,$db_columns,$db_conditions,null,true);
        if(empty($result)) return false;
        return floatval($result[0]['trader_balance']);
    }


    public function get_trader_calculated_balance()
    {
        $q = 'SELECT SUM(tx_profit) as profit FROM ' . $this->trader_id .'_tx';
        $result = $this->db_query($q);
        return (!empty($result))?$result[0]['profit']:false;
    }

    public function get_trader_calculated_initial_deposit()
    {
        $q = 'SELECT tx_profit as deposit, tx_topen as date  FROM ' . $this->trader_id .'_tx WHERE tx_type = 6 ORDER BY date ASC LIMIT 1';
        $result = $this->db_query($q);
        return (!empty($result))?$result[0]['deposit']:false;
    }


    public function get_initial_deposit()
    {
        $db_columns = array(
            'trader_deposit_withdrawal'
        );
        $db_conditions = array(
            'trader_id'=>$this->trader_id
        );

        $result = $this->db_retrieve(self::TRADER_TABLE,$db_columns,$db_conditions,null,true);
        if(empty($result)) return false;
        return floatval($result[0]['trader_deposit_withdrawal']);
    }


    public function get_total_growth()
    {
        $db_columns = array(
            'trader_growth'
        );
        $db_conditions = array(
            'trader_id'=>$this->trader_id
        );

        $result = $this->db_retrieve(self::TRADER_TABLE,$db_columns,$db_conditions,null,true);
        if(empty($result)) return false;
        return floatval($result[0]['trader_growth']);
    }

    public function get_daily_growth()
    {

        $initial_deposit = $this->get_initial_deposit();
        $profits = $this->get_profit('day');
        $daily_growth = Array();
        $cumulative_growth = 0;
        foreach($profits as $item)
        {
            $growth = (floatval($item['profit']) /$initial_deposit) * 100;
            $cumulative_growth +=$growth;
            $_growth = number_format(floatval($cumulative_growth),3);
            //$_growth = substr($_g,0,strlen($_g));
            $daily_growth[] = array(
                //'test'=>($item['profit'] /$initial_deposit) * 100,
                'growth'=> $_growth,
                'profit'=>$item['profit'],
                'date' => $item['date'],
                'month'=>$item['month'],
                'year'=>$item['year']
            );
        }
        //aasort($daily_growth,'sort',SORT_ASC);
        //print '<pre>'. print_r($daily_growth,true);
        return $daily_growth;
    }


    public function get_monthly_growth()
    {


        $initial_deposit = $this->get_initial_deposit();
        $profits = $this->get_profit('month');
        $daily_growth = Array();
        $cumulative_growth = 0;
        foreach($profits as $item)
        {
            $growth = ($item['profit'] /$initial_deposit) * 100;
            $cumulative_growth +=$growth;
            $daily_growth[] = array(
                //'test'=>($item['profit'] /$initial_deposit) * 100,
                'growth'=> round(number_format($cumulative_growth,2),2,PHP_ROUND_HALF_DOWN),
                'profit'=>$item['profit'],
                'date' => $item['date']
            );
        }
        return $daily_growth;
    }



    public function get_monthly_deposits()
    {
        return $this->get_deposit('month');
    }

    public function get_monthly_profits()
    {
        return $this->get_profit('month');
    }


    public function get_avg_monthly_growth()
    {
        $initial_deposit = $this->get_initial_deposit();
        $profit = $this->get_daily_growth();
        $cumulative_growth = Array();

        foreach($profit as $item)
        {
            $growth = ($item['profit'] /$initial_deposit) * 100;
            $month = $item['month'];
            $cumulative_growth[$month] +=$growth;
        }

        $monthly_growth = 0;
        foreach($cumulative_growth as $growth)
        {
            $monthly_growth +=$growth;
        }
        return number_format($monthly_growth/count($monthly_growth),2);
    }


    public function get_deposit($mode=null)
    {
        switch($mode)
        {
            /** ==============
             *  DAILY DEPOSITS
             *  ==============
             */
            case 'day':
                $q = 'SELECT DATE_FORMAT(FROM_UNIXTIME(tx_tclose), \'%m/%d/%Y\') as date, SUM(tx_profit) as profit FROM ' . $this->trader_id .'_tx WHERE tx_type=6 GROUP BY DATE_FORMAT(FROM_UNIXTIME(tx_tclose), \'%m/%d/%Y\')';
                $result = $this->db_query($q);
                $_result = Array();
                foreach($result as $item)
                {
                    $_result[$item['date']] = $item['profit'];
                }
                return $_result;
                break;

            /** ================
             *  MONTHLY DEPOSITS
             *  ================
             */
            case 'month':
                $q = 'SELECT SUM(tx_profit) as profit, Year(FROM_UNIXTIME(tx_tclose)) as year,Month(FROM_UNIXTIME(tx_tclose)) as month FROM ' . $this->trader_id .'_tx  WHERE tx_tclose > 0 AND tx_type = 6 GROUP BY Month(FROM_UNIXTIME(tx_tclose))';
                $result = $this->db_query($q);
                return (!empty($result))?$result:false;
                break;

            /** ===================
             *  MT4 INITIAL DEPOSIT
             *  ===================
             */
            case 'initial':
                $db_columns = array(
                    'trader_deposit_withdrawal'
                );
                $db_conditions = array(
                    'trader_id'=>$this->trader_id
                );

                $result = $this->db_retrieve(self::TRADER_TABLE,$db_columns,$db_conditions,null,true);
                if(empty($result)) return false;
                return floatval($result[0]['trader_deposit_withdrawal']);
                break;

            /** ==============
             *  TOTAL DEPOSITS
             *  ==============
             */
            default:
                $q = 'SELECT SUM(tx_profit) as profit FROM ' . $this->trader_id .'_tx as a WHERE tx_tclose > 0 AND tx_type=6';
                //by default lets return total sum account deposits
                $result = $this->db_query($q);
                return (!empty($result[0]['profit']))?$result[0]['profit']:false;
                break;
        }
    }




}