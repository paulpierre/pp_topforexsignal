<?php

class FXParse extends Database
{
    public $url;
    public $document;
    public $traderName;
    public $traderAccount;
    public $traderCurrency;
    public $traderLeverage;
    public $traderFloatingPNL;
    public $traderEquity;
    public $traderMargin;
    public $traderFreeMargin;
    public $traderCreditFacility;
    public $traderBalance;
    public $traderDepositWithDrawal;
    public $traderGrowth;
    public $traderMaxDraw;
    public $traderAvgMonthlyGrowth;
    public $traderSystemType;
    public $traderAccountCreated;
    public $traderAccountModified;
    public $traderBroker;
    public $traderTransactions;

    public function __construct() {

    }

    public function connect($url)
    {
        $doc = phpQuery::newDocument(file_get_contents($this->$url));
        $this->document = $doc;
    }

    public function create_trader_table()
    {

    }



}
