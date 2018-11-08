
/**
  ===========
  commissions
  ===========
  Database records of all trades we get commissions from
 */
DROP TABLE IF EXISTS `commissions`;
CREATE TABLE `commissions`(
 `id` int(10) NOT NULL AUTO_INCREMENT,  /* Unique transaction ID of the slave's trade from 4X solutions */
 `trade_ticket` int(10) NOT NULL,
 `user_id` int(10) NOT NULL,  /* ID of the trader */
 `trade_symbol` varchar(9) NOT NULL,
 `broker_id` int(3) NOT NULL,
 `trade_profit` float(10,2) NOT NULL,
 `trade_date` DATETIME NOT NULL,
 `trade_tcreate` DATETIME NOT NULL,
  PRIMARY KEY (`trade_ticket`,`user_id`,`trade_date`),
  KEY(`id`),
  KEY (`broker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8






/**
  ========
  carbonfx
  ========
  A historical record of signals sent, used for the signals landing page
 */
 DROP TABLE IF EXISTS `carbonfx`;
 CREATE TABLE `carbonfx`(
 `id` int(10) NOT NULL AUTO_INCREMENT,  /* Unique transaction ID of the slave's trade from 4X solutions */
 `trader_id` int(10) NOT NULL,  /* ID of the trader */
 `trader_investor_pw` varchar(256) NOT NULL, /* the amount in USD we are to pay them out with */
 `trader_name` varchar(256) NOT NULL,
 `trader_master_pw` varchar(256) NOT NULL, /* date the record was created */
 `trader_server` varchar(256) NOT NULL, /* date record was modified */
 `trader_status` int(3) NOT NULL, /* 0=pending 1=paid 2=flagged */
 `trade_type` int(3) NOT NULL,
 `trader_online` int(3) NOT NULL, /* any feedback we have to give the user on the payment */
 `trader_copy_master_id` int(10) NOT NULL
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/**
  ============
  trader_payout
  ============
  A historical record of signals sent, used for the signals landing page
 */
 DROP TABLE IF EXISTS `payout`;
 CREATE TABLE `payout`(
 `payout_id` int(10) NOT NULL AUTO_INCREMENT,  /* Unique transaction ID of the slave's trade from 4X solutions */
 `trader_id` int(10) NOT NULL,  /* ID of the trader */
 `payout_amount` float(10,2) NOT NULL, /* the amount in USD we are to pay them out with */
 `payout_tcreate` DATETIME NOT NULL, /* date the record was created */
 `payout_tmodified` DATETIME NOT NULL, /* date record was modified */
 `payout_status` int(3) NOT NULL, /* 0=pending 1=paid 2=flagged */
 `payout_notes` varchar(256) NOT NULL, /* any feedback we have to give the user on the payment */
 `payout_email` varchar(256) NOT NULL, /* the paypal email we are paying out to. we track this in case it changes */
 `payout_method` int(3) NOT NULL,  /* how we pay them out  1=paypal 2=? */
  PRIMARY KEY (`payout_id`),
  KEY(`trader_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/**
  ============
  signals
  ============
  A historical record of signals sent, used for the signals landing page
 */
 DROP TABLE IF EXISTS `signals`;
 CREATE TABLE `signals`(
 `signal_id` int(10) NOT NULL AUTO_INCREMENT,  /* Unique transaction ID of the slave's trade from 4X solutions */
 `signal_status` int(3) NOT NULL,
 `signal_action` int(3) NOT NULL,
 `signal_pair` varchar(10) NOT NULL,  /* account ID of the slave inside 4x solutions */
 `signal_tp` float(10,5) NOT NULL,
 `signal_sl` float(10,5) NOT NULL,  /* the unique ID of the MASTER's trade the slave is copying */
 `signal_price` float(10,5) NOT NULL, /* I'm assuming the MT4 login of the user */
 `signal_result` int(10) NOT NULL,
 `signal_date` DATETIME NOT NULL,
 `signal_notes` VARCHAR(255) NOT NULL,
 `signal_tmodified` DATETIME NOT NULL,
 `signal_tcreate` DATETIME NOT NULL,
 `signal_winloss` int(3) NOT NULL,
 `trader_id` int(10) NOT NULL,
  PRIMARY KEY (`signal_id`),
  KEY(`trader_id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/**
  ============
  clienttrades
  ============
  Table of all the master trades
 */

 DROP TABLE IF EXISTS `clienttrades`;
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
  Traders
  =======
  Table of all the traders in the system
 */

DROP TABLE IF EXISTS `traders`;
CREATE TABLE `traders` (
  `trader_id` int(10) NOT NULL AUTO_INCREMENT,
  `trader_name` varchar(256) NOT NULL, /* display name of the trader */
  `trader_img` varchar(256) NOT NULL, /* avatar of the trader */
  `trader_full_name` varchar(256) NOT NULL, /* the trader's real name */
  `trader_hash` varchar(256) NOT NULL,
  `trader_master_copier_id` varchar(255) NOT NULL,
  `trader_email` varchar(256) NOT NULL, /* traders email address */
  `trader_commission` floatval(10,2) NOT NULL, /* total floating commission for the trader */
  `trader_commission_total` floatval(10,2) NOT NULL, /* total lifetime commission earned for trader */
  `trader_commission_payout` floatval(10,2) NOT NULL, /* total life time commission paid out to trader */
  `trader_account` int(25) NOT NULL, /* the account # of the trader on their broker */
  `trader_account_password` varchar(256) NOT NULL, /* password of aaccount */
  `trader_currency` varchar(3) NOT NULL, /* the currency used by the trader e.g. USD  */
  `trader_leverage` int(10) NOT NULL, /* leverage on a trader's account */
  `trader_closed_pnl` float(10,2) NOT NULL,
  `trader_floating_pnl` float(10,2) NOT NULL,
  `trader_equity` float(10,2) NOT NULL,
  `trader_margin` float(10,2) NOT NULL,
  `trader_free_margin` float(10,2) NOT NULL,
  `trader_credit_facility` float(10,2) NOT NULL,
  `trader_balance` float(10,2) NOT NULL,
  `trader_deposit_withdrawal` float(10,2) NOT NULL,
  `trader_growth` float(10,2) NOT NULL,
  `trader_max_draw` float(10,2) NOT NULL,
  `trader_max_rel_draw` float(10,2) NOT NULL,
  `trader_avg_mo_growth` float(10,2) NOT NULL,
  `trader_system_type` int(3) NOT NULL,
  `trader_profit` float(10,2) NOT NULL,
  `trader_server` varchar(256) NOT NULL,
  `trader_company` varchar(256) NOT NULL, /* if the trader is a company, this is their company name */
  `trader_account_type` int(3) NOT NULL, /* 0 = test account, 1 = live master account */
  `trader_trade_allowed` int(1) NOT NULL,
  `trader_expert_advisor_allowed` int(1) NOT NULL,
  `trader_win` int(10) NOT NULL,
  `trader_loss` int(10) NOT NULL,
  `trader_status` int(3) NOT NULL, /* 0 user created account   1 user submitted info  2 user approved 3 user live on site */
  `trader_min_investment` float(10,2) NOT NULL, /* minimum recommended deposit */
  `broker_id` int(10) NOT NULL, /* broker ID this trading account is associated with */
  `trader_bio` varchar(1000) NOT NULL,
  `trader_tagline` varchar(256) NOT NULL,
  `trader_min_account_leverage` int(10) NOT NULL,
  `trader_tcreate` int(11) NOT NULL,
  `trader_tmodified` int(11) NOT NULL,
  `trader_show_open_trades` int(1) NOT NULL,
  `trader_myfxbook_url` varchar(500) NOT NULL,
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
  `trader_followers` int(10) NOT NULL,
  `trader_digits` int(1) NOT NULL,
  `trader_sort` int(10) NOT NULL,
  `trader_type` int(3) NOT NULL,
  `trader_tignore` int(11) NOT NULL, /* date we want to start tracking history */
  `trader_lot_modifier` float(10,2) NOT NULL /* lots we want to modify each trade by */
  PRIMARY KEY (`trader_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE traders AUTO_INCREMENT = 10000;


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
  =====
  Clients
  =====
  Table of all the users who wish to trade copy
 */
DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `broker_id` int(10) NOT NULL,
  `broker_name` varchar(256) NOT NULL,
  `user_broker_account` varchar(256) NOT NULL,
  `user_full_name` varchar(500) NOT NULL,
  `user_name` varchar(256) NOT NULL,
  `user_email` varchar(500) NOT NULL,
  `user_phone` varchar(25) NOT NULL,
  `user_country` varchar(256) NOT NULL,
  `user_status` int(3) NOT NULL,
  `user_tsignup` int(11) NOT NULL,
  `user_tmodified` int(11) NOT NULL,
  `trader_id` int(15) NOT NULL,
  `trader_name` varchar(256) NOT NULL,
  `user_tcreate` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY(`broker_id`)
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