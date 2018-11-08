
/*************************************************************************
  +----------------------+
  |  CarbonFX Database  |
  +---------------------+
  CarbonFX is a forex multi symbol trade copier platform based on NJ4X
  Developed by Paul Pierre - ##########

  Databases
  [trader_db] Contains data of all the trader
  [client_X_db] Where X is the incremental index of the client database.
 ************************************************************************/




/**
  =======
  Traders
  =======
  Table of all the traders in the system
 */

DROP TABLE IF EXISTS `master`;
CREATE TABLE `master` (
  `master_id` int(10) NOT NULL AUTO_INCREMENT,
  `master_user_name` varchar(256) NOT NULL, /* display name of the trader */
  `master_display_name` varchar(256) NOT NULL, /* the trader's real name */
  `master_master_copier_id` varchar(255) NOT NULL,
  `master_MT4_login` int(25) NOT NULL, /* the account # of the trader on their broker */
  `master_MT4_password` varchar(256) NOT NULL, /* password of aaccount */
  `master_MT4_server` varchar(256) NOT NULL,
  `broker_id` int(10) NOT NULL, /* broker ID this trading account is associated with */

  `master_leverage` int(10) NOT NULL, /* leverage on a trader's account */
  `master_closed_pnl` float(10,2) NOT NULL,
  `master_floating_pnl` float(10,2) NOT NULL,
  `master_equity` float(10,2) NOT NULL,
  `master_margin` float(10,2) NOT NULL,
  `master_free_margin` float(10,2) NOT NULL,
  `master_credit_facility` float(10,2) NOT NULL,
  `master_balance` float(10,2) NOT NULL,
  `master_deposit_withdrawal` float(10,2) NOT NULL,
  `master_growth` float(10,2) NOT NULL,
  `master_max_draw` float(10,2) NOT NULL,
  `master_max_rel_draw` float(10,2) NOT NULL,
  `master_avg_mo_growth` float(10,2) NOT NULL,
  `master_system_type` int(3) NOT NULL,
  `master_profit` float(10,2) NOT NULL,
  `master_account_type` int(3) NOT NULL, /* 0 = test account, 1 = live master account */
  `master_trade_allowed` int(1) NOT NULL,
  `master_expert_advisor_allowed` int(1) NOT NULL,
  `master_win` int(10) NOT NULL,
  `master_loss` int(10) NOT NULL,
  `master_status` int(3) NOT NULL, /* 0 user created account   1 user submitted info  2 user approved 3 user live on site */
  `master_min_investment` float(10,2) NOT NULL, /* minimum recommended deposit */
  `master_bio` varchar(1000) NOT NULL,
  `master_tagline` varchar(256) NOT NULL,
  `master_min_account_leverage` int(10) NOT NULL,
  `master_tcreate` int(11) NOT NULL,
  `master_tmodified` int(11) NOT NULL,
  `master_show_open_trades` int(1) NOT NULL,
  `master_myfxbook_url` varchar(500) NOT NULL,
  `master_followers` int(10) NOT NULL,
  `master_digits` int(1) NOT NULL,
  `mfb_gain_abs` float(10,2) NOT NULL,
  `mfb_gain_daily`  float(10,2) NOT NULL,
  `mfb_gain_monthly`  float(10,2) NOT NULL,
  `mfb_drawdown`  float(10,2) NOT NULL,
  `mfb_balance`  float(10,2) NOT NULL,
  `mfb_equity`  float(10,2) NOT NULL,
  `mfb_equity_percent` float(10,2) NOT NULL,
  `mfb_equity_high`  float(10,2) NOT NULL,
  `mfb_equity_high_date` varchar(256) NOT NULL,
  `mfb_profit`  float(10,2) NOT NULL,
  `mfb_interest`  float(10,2) NOT NULL,
  `mfb_deposits`  float(10,2) NOT NULL,
  `mfb_withdrawals`  float(10,2) NOT NULL,

  PRIMARY KEY (`trader_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE traders AUTO_INCREMENT = 10000;


/**
  ============
  client
  ============
  Table of all the master trades
 */

 DROP TABLE IF EXISTS `clients`;
 CREATE TABLE `clienttrades`(
 `trade_id` int(15) NOT NULL,  /* Unique transaction ID of the slave's trade from 4X solutions */
 `trader_name` varchar(255) NOT NULL,
 `trader_account_id` int(10) NOT NULL,  /* account ID of the slave inside 4x solutions */
 `trade_close_price` float(10,5),
 `trade_close_time` DATETIME NOT NULL,
 `trader_login` int(10) NOT NULL, /* I'm assuming the MT4 login of the user */
 `trade_master_trade_id` int(10) NOT NULL,  /* the unique ID of the MASTER's trade the slave is copying */
 `trade_open_price` float(10,5) NOT NULL,
 `trade_open_time` DATETIME NOT NULL,
 `trade_order_type` VARCHAR(10) NOT NULL,
 `trade_pips` float(10,2) NOT NULL,
 `trade_profit` float(10,2) NOT NULL,
 `trade_size` float(10,2) NOT NULL,
 `trade_stop_loss` float(10,5) NOT NULL,
 `trader_subscription_id` int(10) NOT NULL,
 `trade_symbol_id` VARCHAR(10) NOT NULL,
 `trade_take_profit` float(10,5) NOT NULL,
 `trade_ticket` int(10) NOT NULL,
 `trade_fx_account_id` int(10) NOT NULL,  /* Our main main account on 4x under TFS */
 `trade_tcreate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`trade_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/**
  =======
  Brokers
  =======
  Table of all the brokers associated with the traders
 */
DROP TABLE IF EXISTS `brokers`;
CREATE TABLE `brokers` (
  `broker_id` int(10) NOT NULL AUTO_INCREMENT,
  `broker_name` varchar(256) NOT NULL,
  `broker_status` varchar(256) NOT NULL,
  `broker_tmodified` int(11) NOT NULL,
  `broker_tcreate` int(11) NOT NULL,
  PRIMARY KEY (`broker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



/**
  =======
  XXXX_tx
  =======
  Transactional table, each traders gets their own table of
  transactions with their trader_id as the prefix
 */

DROP TABLE IF EXISTS `_tx`;
CREATE TABLE `_tx` (
  `tx_id` int(10) NOT NULL AUTO_INCREMENT,
  `tx_ticket` int(10) NOT NULL,
  `tx_topen` int(11) NOT NULL,
  `tx_tclose` int(11) NOT NULL,
  `tx_type` int(1) NOT NULL,
  `tx_status` int(1) NOT NULL, /* 0=open 1=closed 2=working */
  `tx_size` float(10,2) NOT NULL,
  `tx_item` varchar(10) NOT NULL,
  `tx_open_price` float(10,5) NOT NULL,
  `tx_close_price` float(10,5) NOT NULL,
  `tx_current_price` float(10,5) NOT NULL,
  `tx_pips` float(10,2) NOT NULL,
  `tx_stoploss` float(10,5) NOT NULL,
  `tx_takeprofit` float(10,5) NOT NULL,
  `tx_commission` float(10,2) NOT NULL,
  `tx_taxes` float(10,2) NOT NULL,
  `tx_swap` float (10,2) NOT NULL,
  `tx_profit` float(10,2) NOT NULL,
  `tx_market_price` float(10,5) NOT NULL,
  `tx_tmodified` int(11) NOT NULL,
  `tx_tcreate` int(11) NOT NULL,
  `broker_id` int(3) NOT NULL,
  `trader_id` int(10) NOT NULL,
  PRIMARY KEY (`tx_id`),
  KEY(`broker_id`),
  KEY(`trader_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*CREATE TABLE recipes_new LIKE _tx; INSERT recipes_new SELECT * FROM production.recipes;*/