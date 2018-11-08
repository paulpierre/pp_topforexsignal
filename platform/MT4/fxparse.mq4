
/** ==================================
 *  FxParse - An EA for TopForexSignal
 *  ==================================
 *	(C)opyright 2014-2015 TopForexSignal
 *	Developed by Paul Pierre - ##########
 */

#property copyright "©Copyright 2015 TopForexSignal"
#property link      "http://www.TopForexSignal.com"

#include <mql4-mysql.mqh>

#define OP_BALANCE 6
#define OP_CREDIT  7
#define PROD 1
#define LOCAL 0

#define NONE 0
#define OMEGA_FX 1     
#define CONVENTIONAL77 2
#define SPECIALZ_TRADER 3
#define TTFA 4
#define PAULPIERRE 5
#define FXPIPMAKERS 6
#define WOLFOFWALLSTREET 7
#define GOLDDIGGER 8
#define SOROS11 9
#define STRATEGICTRADING 10
#define ROBORAZ 11
#define LIVEACCOUNTSIGNAL 12
#define STEADYPROFITSIGNAL 13


#define OPEN_ORDERS_DELAY 300 			//how often we should update open orders in seconds. in this case every 5 minutes
#define DRAWDOWN_DELAY 1500	//every 15 minutes

double InitialDeposit;
double SummaryProfit;
double GrossProfit;
double GrossLoss;
double MaxProfit;
double MinProfit;
double ConProfit1;
double ConProfit2;
double ConLoss1;
double ConLoss2;
double MaxLoss;
double MaxDrawdown;
double MaxDrawdownPercent;
double RelDrawdownPercent;
double RelDrawdown;
double ExpectedPayoff;
double ProfitFactor;
double AbsoluteDrawdown;
int    SummaryTrades;
int    ProfitTrades;
int    LossTrades;
int    ShortTrades;
int    LongTrades;
int    WinShortTrades;
int    WinLongTrades;
int    ConProfitTrades1;
int    ConProfitTrades2;
int    ConLossTrades1;
int    ConLossTrades2;
int    AvgConWinners;
int    AvgConLosers;

double prior_deposit;

double PeakDraw;
double account_deposit;
double _dd;

int    lastTime_orders;	//when we last cyled through out open trades check
int    lastTime_dd;	//when we last cyled through out open trades check

//#property script_show_inputs
enum MODE
  {
   Production=1,     
   Local=0    
  };
//--- input parameters
input MODE RunMode=Local;

enum BOT
  {
   none=0,
   omega_fx,     
   conventional77,
   specialz_trader,
   ttfa,
   paulpierre,
   fxpipmakers,
   wolfofwallstreet,
   golddigger,
   soros11,
   strategictrading,
   roboraz,
   liveaccountsignal,
   steadyprofitsignal
  };
//--- input parameters
input BOT BotProfile=none;



string  host;
string  user;
string  pass;
string  dbName;


int     port     = 3306;
int     socket   = 0;
int     client   = 0;

int     dbConnectId = 0;
bool    goodConnect = false;
string fxQuery;

int brokerID;
int traderID;
string botID;
int traderAccount  = AccountNumber();

// open positions array as on the previous tick
int pre_OrdersArray[][2]; // [amount of positions][ticket #, position type]
// first launch flag
static bool first = true;
// last error code
int _GetLastError = 0;
// total amount of positions
int _OrdersTotal = 0;
// the amount of positions that meet the criteria (the current symbol and the MagicNumber),
// as on the current tick
int now_OrdersTotal = 0;
// the amount of positions that meet the criteria (the current symbol and the specified MagicNumber),
// as on the previous tick
static int pre_OrdersTotal = 0;
// open positions array as on the current tick
int now_OrdersArray[][2]; // [# in the list][ticket #, position type]
// the current number of the position in the array now_OrdersArray (for search)
int now_CurOrder = 0;
// the current number of the position in the array pre_OrdersArray (for search)
int pre_CurOrder = 0;

// array for storing the amount of closed positions of each type
int now_ClosedOrdersArray[6][3]; // [order type][closing type]
// array for storing the amount of triggered pending orders
int now_OpenedPendingOrders[4]; // [order type] (there are only 4 types of pending orders totally)

// temporary flags
bool OrderClosed = true, PendingOrderOpened = false;
// temporary variables
int ticket = 0, type = -1, close_type = -1,buy_type=-1;

int DB_COL_COUNT = 21; // number of columns we have in the database


bool get_account()
{
	Print("Looking up user via account#:" + AccountNumber() + " botID:" + botID + " broker_name:" + AccountCompany());
	fxQuery = StringConcatenate("SELECT brokers.broker_id, traders.trader_id FROM brokers LEFT JOIN traders ON traders.broker_id=brokers.broker_id WHERE traders.trader_account='" + AccountNumber() +"' AND traders.trader_name='" + botID + "' AND brokers.broker_name='" + AccountCompany() + "'");
    string data[][2];   // important: second dimension size must be equal to the number of columns
    int result = MySQL_FetchArray(dbConnectId, fxQuery, data);
	brokerID = data[0][0];
	traderID = data[0][1];
	Print("mysql: " + fxQuery + " result:" + result);
	Print("traderID:" + traderID + " brokerID:" + brokerID);
	fxQuery = "";
	if(brokerID==0 || traderID==0) return false; else return true;
}

bool create_account()
{
	//lets create the broker account if it doesn't exist yet
	if(set_broker())
	{
		if(set_trader())
		{
			if(get_account())
			{
				return true;
			} else return false;
		} else return false;
	} else return false;

}

int get_trader_id()
{
	fxQuery = StringConcatenate("SELECT traders.trader_id FROM brokers LEFT JOIN traders ON traders.broker_id=brokers.broker_id WHERE traders.trader_account='" + AccountNumber() +"' AND traders.trader_name='" + botID +"' AND brokers.broker_name='" + AccountCompany() + "'");
    string data[][1];   // important: second dimension size must be equal to the number of columns
    int result = MySQL_FetchArray(dbConnectId, fxQuery, data);
	Print("mysql: " + fxQuery + " result:" + result);
	Print("traderID:" + traderID);
	fxQuery = "";
	if(result==0|| result == -1) return 0; else return data[0][0];
}

//lets create a trader account
bool set_trader()
{
	int traderResult;
		//broker does not exists so lets create it	
	fxQuery = "INSERT INTO `traders` (trader_name,trader_full_name,trader_account,trader_trade_allowed,trader_status,trader_tcreate,trader_tmodified,broker_id,trader_server,trader_currency) VALUES ('" + botID + "','New User','" + AccountNumber()  + "'," + IsTradeAllowed() + ",1,"+TimeCurrent()+"," +TimeCurrent() + "," + brokerID + ",'" + AccountServer() +"','" + AccountCurrency() + "')";
    if ( MySQL_Query(dbConnectId, fxQuery) ) {
        Print( AccountNumber() +"successfully added to database");
		fxQuery = "";
	
		traderResult = get_trader_id();
		if(traderResult  > 0) 
		{
			traderID = traderResult;
			//now lets create the table for this user based on their user_ID
			fxQuery = "CREATE TABLE " + traderResult + "_tx LIKE _tx";
		    if ( MySQL_Query(dbConnectId, fxQuery) ) return true; else return false;

		
		} else { return false; }
		
    } else {
		Print("failed to add user to db");
		return false;
	}
	fxQuery = "";

 
}

int get_broker_id()
{
	fxQuery = StringConcatenate("SELECT broker_id FROM brokers WHERE broker_name='" + AccountCompany() + "'");
    string data[][1];   // important: second dimension size must be equal to the number of columns
    int result = MySQL_FetchArray(dbConnectId, fxQuery, data);
	fxQuery = "";
	Print("Looking up brokerID " + AccountCompany() + " result:" + data[0][0]);
	
	if(result < 1) return 0; else return data[0][0];
}

//lets set broker data
bool set_broker()
{
	int brokerResult;
	brokerResult = get_broker_id();
	
    if (brokerResult ==0)
	{
		//broker does not exists so lets create it
		fxQuery = StringConcatenate(
        "INSERT INTO `brokers` VALUES (", "0"
                                        , "," , "'" + AccountCompany() + "'"
                                        , "," , 1								    
                                        , "," , "'" + TimeCurrent()  + "'"
                                        , "," , "'" + TimeCurrent()  + "'"
                                        , ")"
        );
	    if ( MySQL_Query(dbConnectId, fxQuery) ) {
	        Print( AccountCompany() +"successfully added to database");
			brokerResult = get_broker_id();
			Print("Fetching brokerID:" + brokerResult);
			
			if(brokerResult > 0) 
			{
				brokerID = brokerResult;
				return true;
			} else { return false; }
	    } else {
			Print("failed to add " + AccountCompany() + " to db");
			return false;
		}
		fxQuery = "";
		
    } else {
		brokerID = brokerResult;
		Print("BrokerID found in database and set to " + brokerID);
		return true;
   }
}



bool trader_table_exists()
{
	fxQuery = StringConcatenate("SHOW TABLES LIKE '" + traderID + "_tx'");
    string data[][1];   // important: second dimension size must be equal to the number of columns
    int result = MySQL_FetchArray(dbConnectId, fxQuery, data);
	Print("mysql: " + fxQuery + " result:" + result);
	fxQuery = "";
	
    if ( result == 0 || result == -1 ) {
		Print("Trader Table " + AccountNumber() + " DOES NOT");
		return false;
    } else {
		Print("Trader Table " + AccountNumber() + " DOES EXIST");
		return true;
   }
}


double trader_max_draw()
{
	fxQuery = StringConcatenate("SELECT trader_max_draw FROM traders WHERE trader_id=" + traderID);
    string data[][1];   // important: second dimension size must be equal to the number of columns
    float result = MySQL_FetchArray(dbConnectId, fxQuery, data);
	Print("Max drawdown in database: " + result);
	return data[0][0];
}

double trader_initial_deposit() //prior_deposit
{
	fxQuery = StringConcatenate("SELECT trader_deposit_withdrawal FROM traders WHERE trader_id=" + traderID);
    string data[][1];   // important: second dimension size must be equal to the number of columns
    float result = MySQL_FetchArray(dbConnectId, fxQuery, data);
	Print("Found the initial deposit: " + result);
	return data[0][0];
	
}


bool trader_id_exists()
{
	fxQuery = StringConcatenate("SELECT trader_id FROM traders WHERE trader_id=" + traderID);
    string data[][1];   // important: second dimension size must be equal to the number of columns
    int result = MySQL_FetchArray(dbConnectId, fxQuery, data);
	Print("mysql: " + fxQuery + " result:" + result);
	fxQuery = "";
	
    if ( result == 0 || result == -1 ) {
		Print("Row in Trader table DOES NOT exist");
		return false;
    } else {
		Print("Row in Trader table DOES exist");
		return true;
   }
}


bool tx_exists(string tx)
{
	fxQuery = StringConcatenate(
        "SELECT tx_id ",
            "FROM " + traderID + "_tx ",
            "WHERE tx_ticket = ",
 			"'" + tx + "'");
    string data[][21];   // important: second dimension size must be equal to the number of columns
    int result = MySQL_FetchArray(dbConnectId, fxQuery, data);
	fxQuery = "";
	//Print("mysql: " + fxQuery + " result:" + result);
    if ( result == 0 || result == -1 ) {
		Print("order " + tx + " does NOT exist");
		return false;
    } else {
		Print("order " + tx + " DOES exist");
		return true;
   }
}

void deinit()
{
	deinit_MySQL(dbConnectId);
    return;
}

void update_trader_stats(int trader_id)
{
	//lets get the prior deposit, if it doesn't exist, then lets calculate it, if not use the existing one	
	
	Print("#### checking if there was an initial deposit..");
	prior_deposit = trader_initial_deposit(); //prior_deposit

	if(prior_deposit > 0) {
		Print("initial deposit found: " + prior_deposit);
	}
	
	account_deposit = (prior_deposit > 0)?prior_deposit:CalculateInitialDeposit();
	CalculateSummary(account_deposit); //or AccountBalance() .. dunno
	double _dd = trader_max_draw(); //grab max draw from database
	double trader_max_draw;
	
	if(PeakDraw > MaxDrawdownPercent && PeakDraw > _dd && PeakDraw !=0.0) trader_max_draw = PeakDraw;
	if(_dd > MaxDrawdownPercent && _dd > PeakDraw && _dd !=0.0) trader_max_draw = _dd;
	if(MaxDrawdownPercent > _dd && MaxDrawdownPercent > PeakDraw && MaxDrawdownPercent !=0.0) trader_max_draw = MaxDrawdownPercent;
	
	Print("[Drawdown] Historical: " + MaxDrawdownPercent + " Database: " + _dd + " Session: " + PeakDraw + " LOWEST: " + trader_max_draw);
	
	double trader_max_rel_draw = RelDrawdownPercent;
	int trader_win = ProfitTrades;
	int trader_loss = LossTrades;
	
	double trader_growth;
	
	trader_growth = (((AccountBalance() - account_deposit) / account_deposit) * 100);

	
	/**
	Items that should be set upon account creation:
	trader_name
	trader_account
	trader_email
	trader_currency
	trader_server
	trader_company
	trader_account_type
	trader_trade_allowed
	trader_expert_advisor_allowed
	trader_status
	trader_tcreate
	http://www.mql5.com/en/docs/constants/environment_state/statistics
	**/
	
	fxQuery = StringConcatenate(
       							"UPDATE traders SET "
								, "trader_leverage=" , AccountLeverage()
								, ",trader_balance=" , AccountBalance()
								, ",trader_credit_facility=" , AccountCredit()
								, ",trader_equity=" , AccountEquity()
								, ",trader_margin=" , AccountMargin()
								, ",trader_free_margin=" , AccountFreeMargin()
								, ",trader_profit=" , AccountInfoDouble(ACCOUNT_PROFIT)
								
								
								//, ",trader_closed_pnl=" , AccountFreeMargin()
								, ",trader_deposit_withdrawal=" , account_deposit
								, ",trader_growth=" , trader_growth //(account_deposit / SummaryProfit)
								, ",trader_max_draw=" , trader_max_draw
								, ",trader_max_rel_draw=" , trader_max_rel_draw
								
								//, ",trader_avg_mo_growth=" , AccountFreeMargin()
								//, ",trader_floating_pnl=" , AccountFreeMargin()
								
								, ",trader_win=",trader_win
								, ",trader_loss=",trader_loss
								
								
								, ",trader_tmodified=" , TimeCurrent() 
								, ",trader_digits=",MarketInfo(Symbol(), MODE_DIGITS)
								, " WHERE trader_id=" + trader_id
       );
	Print("mysql: " + fxQuery);
	 if ( MySQL_Query(dbConnectId, fxQuery) ) {
	       Print("Account: " + trader_id + " successfully updated");
    } else {
		Print("failed to update account: " + trader_id + " to db");
	}
	fxQuery = "";
}


void update_open_orders()
{
	double bidPrice;
	double pips;
	Print("Updating open orders in the DB");
	for (int i = 0; i < OrdersTotal(); i++) {
	    if(OrderSelect(i, SELECT_BY_POS, MODE_TRADES))
		{
			bidPrice = MarketInfo(OrderSymbol(),MODE_BID);

			if(OrderType() == OP_BUY)
				pips = (bidPrice-OrderOpenPrice())/MarketInfo(OrderSymbol(),MODE_POINT) * 0.1;
			else if(OrderType() == OP_SELL)
				pips = (OrderOpenPrice()-bidPrice)/MarketInfo(OrderSymbol(),MODE_POINT) * 0.1;
				
							
			fxQuery = StringConcatenate(
		       							"UPDATE " + traderID+ "_tx SET "
										, "tx_commission=" , OrderCommission()
										, ",tx_swap=" , OrderSwap()
										, ",tx_profit=" , OrderProfit()
										, ",tx_current_price=" , bidPrice
										, ",tx_pips=", pips
										, ",tx_takeprofit=" , OrderTakeProfit()
										, ",tx_tmodified=" , TimeCurrent() 
										, " WHERE tx_ticket='" + OrderTicket() + "'"
		       );
			
			 if ( MySQL_Query(dbConnectId, fxQuery) ) {
					Print("mysql: " + fxQuery);
			       Print(OrderTicket() + " successfully updated");
		    } else {
				Print("failed to close update " + OrderTicket() + " to db");
			}
			fxQuery = "";	
		}
	}
	
}

void get_open_orders()
{
	double bidPrice; 
	double pips;
	
	Print("getting open orders");
	for (int i = 0; i < OrdersTotal(); i++) {
	    if(OrderSelect(i, SELECT_BY_POS, MODE_TRADES))
		{
			bidPrice = MarketInfo(OrderSymbol(),MODE_BID);
			if(OrderType() == OP_BUY)
				pips = (bidPrice-OrderOpenPrice())/MarketInfo(OrderSymbol(),MODE_POINT) * 10;
			else if(OrderType() == OP_SELL)
				pips = (OrderOpenPrice()-bidPrice)/MarketInfo(OrderSymbol(),MODE_POINT) * 10;			
			
			Print("checking db for: " + OrderTicket());
			if(!tx_exists(OrderTicket()))
			{
				Print("does not exist!");
			  	fxQuery = StringConcatenate(
		        "INSERT INTO `" + traderID + "_tx` VALUES (", "0"
		                                        , "," , "'" + OrderTicket() + "'"
		                                        , "," , "'" + OrderOpenTime()  + "'"
										        , "," , "''" 
		                                        , "," , "'" + OrderType()  + "'"
		                                        , "," , 0
		                                        , "," , "'" + OrderLots()  + "'"
		                                        , "," , "'" + OrderSymbol()  + "'"
		                                        , "," , "'" + OrderOpenPrice()  + "'"
		                                        , "," , 0
		                                        , "," , "'" + bidPrice + "'"
		                                        , "," , "'" + pips + "'"
		                                        , "," , "'" + OrderStopLoss()  + "'"
		                                        , "," , "'" + OrderTakeProfit()  + "'"
		                                        , "," , "'" + OrderCommission()  + "'"
		                              			, "," , "''" 
                                           		, "," , "'" + OrderSwap()  + "'"
		                                        , "," , "'" + OrderProfit()  + "'"
 												, "," , "''" 
		                                        , "," , "'" + TimeCurrent()  + "'"
		                                        , "," , "'" + TimeCurrent()  + "'"
		                                        , "," , brokerID
												, "," , traderID
		                                        , ")"
		        );
				Print("mysql: " + fxQuery);
			    if ( MySQL_Query(dbConnectId, fxQuery) ) {
			        Print(OrderTicket() + " successfully added to database");
			    } else {
					Print("failed to add " + OrderTicket() + " to db");
				}
				fxQuery = "";
			}
	    }
	}
}

void add_open_order(string tx)
{
	double bidPrice; 
	double pips;
	if ( !OrderSelect(tx, SELECT_BY_TICKET ) )
    {
        _GetLastError = GetLastError();
        Print( "OrderSelect( ", ticket, ", SELECT_BY_TICKET ) - Error #", _GetLastError );
        return;
    }
	bidPrice =  MarketInfo(OrderSymbol(),MODE_BID);
	if(OrderType() == OP_BUY)
		pips = (bidPrice-OrderOpenPrice())/MarketInfo(OrderSymbol(),MODE_POINT) * 10;
	else if(OrderType() == OP_SELL)
		pips = (OrderOpenPrice()-bidPrice)/MarketInfo(OrderSymbol(),MODE_POINT) * 10;	
	fxQuery = StringConcatenate(
        "INSERT INTO `" + traderID +"_tx` VALUES (", "0"
                                        , "," , "'" + OrderTicket() + "'"
                                        , "," , "'" + OrderOpenTime()  + "'"
								        , "," , "''" 
                                        , "," , "'" + OrderType()  + "'"
                                        , "," , 0
                                        , "," , "'" + OrderLots()  + "'"
                                        , "," , "'" + OrderSymbol()  + "'"
                                        , "," , "'" + OrderOpenPrice()  + "'"
                                        , "," , "'" + bidPrice  + "'"
                                        , "," , "'" + bidPrice  + "'"
                                        , "," , "'" + pips  + "'"

                                        , "," , "'" + OrderStopLoss()  + "'"
                                        , "," , "'" + OrderTakeProfit()  + "'"
                                        , "," , "'" + OrderCommission()  + "'"
                              			, "," , "''" 
                                   		, "," , "'" + OrderSwap()  + "'"
                                        , "," , "'" + OrderProfit()  + "'"
										, "," , "''" 
                                        , "," , "'" + TimeCurrent()  + "'"
                                        , "," , "'" + TimeCurrent()  + "'"
                                        , "," , brokerID
										, "," , traderID
                                        , ")"
       );
	Print("mysql: " + fxQuery);
	 if ( MySQL_Query(dbConnectId, fxQuery) ) {
	       Print(tx + " successfully added new order " + tx);
    } else {
		Print("failed to insert " + tx + " to db");
	}
	fxQuery = "";
}

void close_open_order(string tx)
{
	double bidPrice;
	double pips;
	
	if ( !OrderSelect(tx, SELECT_BY_TICKET ) )
    {
        _GetLastError = GetLastError();
        Print( "OrderSelect( ", ticket, ", SELECT_BY_TICKET ) - Error #", _GetLastError );
        return;
    }
	bidPrice =  MarketInfo(OrderSymbol(),MODE_BID);
	if(OrderType() == OP_BUY)
		pips = (OrderClosePrice()-OrderOpenPrice())/MarketInfo(OrderSymbol(),MODE_POINT) * 0.1;
	else if(OrderType() == OP_SELL)
		pips = (OrderOpenPrice()-OrderClosePrice())/MarketInfo(OrderSymbol(),MODE_POINT) * 0.1;	
	
	fxQuery = StringConcatenate(
       							"UPDATE " + traderID+ "_tx SET "

								, "tx_tclose=" , OrderCloseTime()
								, ",tx_close_price=" , NormalizeDouble(OrderClosePrice(),5)
								, ",tx_commission=" , OrderCommission()
								, ",tx_pips=", pips
								, ",tx_current_price=" , bidPrice
								, ",tx_swap=" , OrderSwap()
								, ",tx_profit=" , OrderProfit()
								, ",tx_takeprofit=" , OrderTakeProfit()
								, ",tx_size=" ,OrderLots()
								, ",tx_status=1"
								, ",tx_tmodified=" , TimeCurrent() 
								, " WHERE tx_ticket='" + OrderTicket() + "'"
       );
	Print("mysql: " + fxQuery);
	 if ( MySQL_Query(dbConnectId, fxQuery) ) {
	       Print(tx + " successfully closed and updated");
    } else {
		Print("failed to close update " + tx + " to db");
	}
	fxQuery = "";
	
	// lets update the traders stats now that they've closed a trade
	update_trader_stats(traderID);
}

//checks to see if an order is open in the database
bool order_is_open_db(string tx)
{
	fxQuery = StringConcatenate(
        "SELECT tx_tclose ",
            "FROM " + traderID + "_tx ",
            "WHERE tx_ticket = ",
 			"'" + tx + "'");
    string data[][21];   // important: second dimension size must be equal to the number of columns
    int result = MySQL_FetchArray(dbConnectId, fxQuery, data);
	fxQuery = "";
	//Print("mysql: " + fxQuery + " result:" + result);
    if ( result == 0) {
		Print("order " + tx + " IS STILL OPEN");
		return true;
    } else if(result == -1 ) {
		Print("There was MYSQL error in checking to see if order " + tx + " was open or not.");
	} else if(result > 0) {
		Print("order " + tx + " had an order close time of " + result);
		return false;
   }
}

void update_order_history()
{
	
	double pips;
	
	Print("checking order history");
	for (int i = 0; i < OrdersHistoryTotal(); i++) {

		//if there is an error grabbing the history, lets bomb out
		if(OrderSelect(i,SELECT_BY_POS,MODE_HISTORY)==false)
	    {
	        Print("Access to history failed with error (",GetLastError(),")");
	        break;
       	}

		Print("checking db for: " + OrderTicket());
		
		//see if this ticket exists in our database
		if(tx_exists(OrderTicket()))
		{	
			//if the order is still open in the db, but it closed already lets update it as closed
			if(order_is_open_db(OrderTicket()) && OrderCloseTime() > 0 )
			{
				close_open_order(OrderTicket());
			}
			//otherwise if it doesn't exist, lets add it to the database
		} else if (!tx_exists(OrderTicket()))
		{
			int _orderStatus;
			
			if(OrderType() == OP_BUY)
			{
				Print("Dividing: " + (OrderClosePrice()-OrderOpenPrice()) + " / " + MarketInfo(OrderSymbol(),MODE_POINT) + " SYMBOL: " + OrderSymbol());
				pips = (OrderClosePrice()-OrderOpenPrice())/MarketInfo(OrderSymbol(),MODE_POINT) * 0.1;
				}
			else if(OrderType() == OP_SELL)
			{
				pips = (OrderOpenPrice()-OrderClosePrice())/MarketInfo(OrderSymbol(),MODE_POINT) * 0.1;
			}
			
			//if there is an order close time then this particular order is done
			if(OrderCloseTime() > 0) _orderStatus = 1; else _orderStatus=0;
			
			Print("Order "+ OrderTicket() + " found in history, but not in database. Adding.");
		  	fxQuery = StringConcatenate(
	        "INSERT INTO `" + traderID + "_tx` VALUES (", "0"
	                                        , "," , "'" + OrderTicket() + "'"
	                                        , "," , "'" + OrderOpenTime()  + "'"
	                                        , "," , "'" + OrderCloseTime()  + "'"
	                                        , "," , "'" + OrderType()  + "'"
	                                        , "," , _orderStatus
	                                        , "," , "'" + OrderLots()  + "'"
	                                        , "," , "'" + OrderSymbol()  + "'"
	                                        , "," , "'" + OrderOpenPrice()  + "'"
	                                        , "," ,  NormalizeDouble(OrderClosePrice(),5)
	                              			, "," , "''" 
	                                        , "," , "'" + pips + "'"

	                                        , "," , "'" + OrderStopLoss()  + "'"
	                                        , "," , "'" + OrderTakeProfit()  + "'"
	                                        , "," , "'" + OrderCommission()  + "'"
	                              			, "," , "''" 
                                          		, "," , "'" + OrderSwap()  + "'"
	                                        , "," , "'" + OrderProfit()  + "'"
												, "," , "''" 
	                                        , "," , "'" + TimeCurrent()  + "'"
	                                        , "," , "'" + TimeCurrent()  + "'"
	                                        , "," , brokerID
											, "," , traderID
	                                        , ")"
	        );
			Print("mysql: " + fxQuery);
		    if ( MySQL_Query(dbConnectId, fxQuery) ) {
		        Print(OrderTicket() + " successfully added to database");
		    } else {
				Print("failed to add " + OrderTicket() + " to db");
			}
			fxQuery = "";
		}
	
	}


}

void dump()
{
	Print("botID: " + botID + " trader_id: " + traderID + " brokerID: " + brokerID);
}

void init()
{
	Print("Loading profile for bot: " + BotProfile);
	
	switch(BotProfile)
	{
		case CONVENTIONAL77:
			botID = "conventional77";
			break;
			
		case OMEGA_FX:
			botID = "omega_fx";
			break;
			
		case SPECIALZ_TRADER:
			botID = "specialz_trader";
			break;
		
		case TTFA:
			botID = "ttfa";
			break;
		
		case PAULPIERRE:
			botID = "paulpierre";
			break;
			
		case FXPIPMAKERS:
			botID = "fxpipmakers";
			break;
			
		case WOLFOFWALLSTREET:
			botID = "wolfofwallstreet";
			break;
			
		case GOLDDIGGER:
			botID = "golddigger";
			break;
			
		case SOROS11:
			botID = "soros11";
			break;
			
		case STRATEGICTRADING:
			botID = "strategictrading";
			break;
			
		case ROBORAZ:
			botID = "roboraz";
			break;
		
		case LIVEACCOUNTSIGNAL:
			botID = "liveaccountsignal";
			break;
			
		case STEADYPROFITSIGNAL:
			botID = "steadyprofitsignal";
			break;
			
		default:	
		case NONE:
			Print("You must select a bot profile to run under, " + BotProfile + " is not a valid profile.");
			return;
		break;
	}
	
	Print("Running in environment mode mode: " + (RunMode==1)?"Production":"Local" + "with Bot: " + botID);
	switch(RunMode)
	{
		case LOCAL:		
			host     = "127.0.0.1";
			user     = "##########";
			pass     = "##########";
			dbName   = "fxparse";
			break;
			
		case PROD:
			host     = "##########";
			user     = "##########";
			pass     = "##########";
			dbName   = "##########";
			break;
	}
	
	goodConnect = init_MySQL(dbConnectId, host, user, pass, dbName, port, socket, client);
    
    if ( !goodConnect ) {
		Print("failed to get to database :(");
        return; // bad connect
        deinit_MySQL(dbConnectId);
		
    }

	if(!get_account())
	{
		if(!create_account())
		{
			Print("##Fatal error, unable to create account.");
			dump();
			deinit_MySQL(dbConnectId);
			return;
		}
	}




	Print("updating database for any missing orders, etc.");
	
	
	update_order_history();
	//lets update the user's account history
	update_trader_stats(traderID);

	Print("grabbing open orders..");
 	get_open_orders();

	Print("monitoring trades..");
	
 	PeakDraw =	get_current_drawdown();
	Print("Current max drawdown: " + PeakDraw + " Equity: " + AccountEquity() + " Deposit: "+ account_deposit);
	
 
    //+------------------------------------------------------------------+
    //| Infinite loop
    //+------------------------------------------------------------------+

	//double _dd = get_current_drawdown();

    while ( !IsStopped() )
    {
		datetime now = TimeCurrent();	
		
		if(now > lastTime_dd + DRAWDOWN_DELAY){
			lastTime_dd = now;
			_dd = get_current_drawdown();
			if(_dd > PeakDraw) {
				PeakDraw = _dd;
				Print("Current drawdown: " + _dd + " Last drawdown: " + PeakDraw);
				update_trader_stats(traderID);
			}
	   	}
		
		
	   	if(now > lastTime_orders + OPEN_ORDERS_DELAY){
			update_open_orders();
			lastTime_orders = now;
		}
	
        // memorize the total amount of positions
        _OrdersTotal = OrdersTotal();
        // change the open positions array size for the current amount
        ArrayResize( now_OrdersArray, _OrdersTotal );
        // zeroize the array
        ArrayInitialize( now_OrdersArray, 0.0 );
        // zeroize the amount of positions met the criteria
        now_OrdersTotal = 0;
 
        // zeroize the arrays of closed positions and triggered orders
        ArrayInitialize( now_ClosedOrdersArray, 0.0 );
        ArrayInitialize( now_OpenedPendingOrders, 0.0 );
 
        //+------------------------------------------------------------------+
        //| Search in all positions and write only those in the array that   
        //| meet the criteria
        //+------------------------------------------------------------------+
        for ( int z = _OrdersTotal - 1; z >= 0; z -- )
        {
            if ( !OrderSelect( z, SELECT_BY_POS ) )
            {
                _GetLastError = GetLastError();
                Print( "OrderSelect( ", z, ", SELECT_BY_POS ) - Error #", _GetLastError );
                continue;
            }
            // Count orders for the current symbol and with the specified MagicNumber
            if (OrderSymbol() == Symbol() )
            {
                now_OrdersArray[now_OrdersTotal][0] = OrderTicket();
                now_OrdersArray[now_OrdersTotal][1] = OrderType();
                now_OrdersTotal ++;
               }
        }
        // change the open positions array size for the amount of positions met the criteria
        ArrayResize( now_OrdersArray, now_OrdersTotal );
 
        //+------------------------------------------------------------------+
        //| Search in the list of positions on the previous tick and count
        //| how many positions have been closed and pending orders triggered
        //+------------------------------------------------------------------+
        for ( pre_CurOrder = 0; pre_CurOrder < pre_OrdersTotal; pre_CurOrder ++ )
        {
            // memorize the ticket and the order type
            ticket = pre_OrdersArray[pre_CurOrder][0];
            type   = pre_OrdersArray[pre_CurOrder][1];
            // suppose that, if it is a position, it has been closed
            OrderClosed = true;
            // suppose that, if it is a pending order, it has not triggered
            PendingOrderOpened = false;
 
            // search in all positions from the current list of open positions
            for ( now_CurOrder = 0; now_CurOrder < now_OrdersTotal; now_CurOrder ++ )
            {
                // if a position with this ticket is in the list,
                if ( ticket == now_OrdersArray[now_CurOrder][0] )
                {
                    // the position has not been closed (the order has not been cancelled)
                    OrderClosed = false;
 
                    // if its type has changed,
                    if ( type != now_OrdersArray[now_CurOrder][1] )
                    {
                        // it is a pending order that has triggered
                        PendingOrderOpened = true;
                    }
                    break;
                } 
            }
		
			
			//NEW ORDER
			if( now_CurOrder == pre_OrdersTotal)
			{
				if(OrderSelect( OrdersTotal()-1, SELECT_BY_POS, MODE_TRADES) == true)
				{
					string _ticket = OrderTicket();
					if(!tx_exists(OrderTicket())) 
					{ 
						Print("New ticket " + _ticket + " found and it's not in the DB. Adding it.");
						add_open_order(_ticket);
					}
					//
				} else continue;
			

			}
			


            // if a position has not been closed (the order has not been cancelled),
            if ( OrderClosed )
            {
                // select it
                if ( !OrderSelect( ticket, SELECT_BY_TICKET ) )
                {
                    _GetLastError = GetLastError();
                    Print( "OrderSelect( ", ticket, ", SELECT_BY_TICKET ) - Error #", _GetLastError );
                    continue;
                }

				close_open_order(ticket);
                // and check HOW the position has been closed (the order has been cancelled):
                if ( type < 2 )
                {
                    // Buy and Sell: 0 - manually, 1 - by SL, 2 - by TP
                    close_type = 0;
                    if ( StringFind( OrderComment(), "sl:" ) >= 0 ) close_type = 1;
                    if ( StringFind( OrderComment(), "tp:" ) >= 0 ) close_type = 2;
					
                }
                else
                {
                    // Pending orders: 0 - manually, 1 - expiration
                    close_type = 0;
                    if ( StringFind( OrderComment(), "expiration" ) >= 0 ) close_type = 1;
                }
                
                // and write in the closed orders array that the order of type 'type' 
                // was cancelled as close_type
                now_ClosedOrdersArray[type][close_type] ++;
                continue;
            }
            // if a pending order has triggered,
            if ( PendingOrderOpened )
            {
               
                // write in the triggered orders array that the order of type 'type' has triggered
                now_OpenedPendingOrders[type-2] ++;
				close_open_order(ticket);
                continue;

	
        }
	            }
 
        //+------------------------------------------------------------------+
        //| Collected all necessary information - display it
        //+------------------------------------------------------------------+
        // if it is not the first launch of the Expert Advisor
        if ( !first )
        {
            // search in all elements of the triggered pending orders array
            for ( type = 2; type < 6; type ++ )
            {
                // if the element is not empty (an order of the type has triggered), display information
                if ( now_OpenedPendingOrders[type-2] > 0 )
					
					
					
                    Alert( Symbol(), ": triggered ", _OrderType_str( type ), " order!" );
				
            }
 
            // search in all elements of the closed positions array
            for ( type = 0; type < 6; type ++ )
            {
                for ( close_type = 0; close_type < 3; close_type ++ )
                {
                    // if the element is not empty (the position has been closed), display information
                    if ( now_ClosedOrdersArray[type][close_type] > 0 ) 
							{
																
								CloseAlert( type, close_type );
								
								
							}
                }
            }
        }
        else
        {
            first = false;
        }
 
        //---- save the current positions array in the previous positions array
        ArrayResize( pre_OrdersArray, now_OrdersTotal );
        for ( now_CurOrder = 0; now_CurOrder < now_OrdersTotal; now_CurOrder ++ )
        {
            pre_OrdersArray[now_CurOrder][0] = now_OrdersArray[now_CurOrder][0];
            pre_OrdersArray[now_CurOrder][1] = now_OrdersArray[now_CurOrder][1];
        }
        pre_OrdersTotal = now_OrdersTotal;
 
        Sleep(100);
    }
	return;
}
void CloseAlert( int alert_type, int alert_close_type )
{
    string action = "";
    if ( alert_type < 2 )
    {
        switch ( alert_close_type )
        {
            case 1: action = " by StopLoss!"; break;
            case 2: action = " by TakeProfit!"; break;
            default: action = " manually!"; break;
        }
        Alert( Symbol(), ": ", _OrderType_str( alert_type ), "-position closed", action );
    }
    else
    {
        switch ( alert_close_type )
        {
            case 1: action = " by expiration!"; break;
            default: action = " manually!"; break;
        }
        Alert( Symbol(), ": ", _OrderType_str( alert_type ), "-order cancelled", action );
    }
}
// returns OrderType as a text
string _OrderType_str( int _OrderType )
{
    switch ( _OrderType )
    {
        case OP_BUY:            return("Buy");
        case OP_SELL:            return("Sell");
        case OP_BUYLIMIT:        return("BuyLimit");
        case OP_BUYSTOP:        return("BuyStop");
        case OP_SELLLIMIT:    return("SellLimit");
        case OP_SELLSTOP:        return("SellStop");
        default:                    return("UnknownOrderType");
    }
}


double get_current_drawdown()
{
	
	double initial_deposit = CalculateInitialDeposit();
	
	int    sequence=0, profitseqs=0, lossseqs=0;
	double sequential=0.0, prevprofit=EMPTY_VALUE, drawdownpercent, drawdown;
 	double maxpeak=initial_deposit, minpeak=initial_deposit, balance=initial_deposit;
	
	//---- initialize summaries
	InitializeSummaries(initial_deposit);

	for (int i = 0; i < OrdersTotal(); i++)
	{
		if(!OrderSelect(i,SELECT_BY_POS,MODE_TRADES)) continue;
	
      	int type=OrderType();

      	//---- initial balance not considered
		if(i==0 && type==OP_BALANCE) continue;
      
		//---- calculate profit
      	double profit=OrderProfit()+OrderCommission()+OrderSwap();
      	balance+=profit;
      
		//---- drawdown check
      	if(maxpeak<balance)
        {
        	drawdown=maxpeak-minpeak;
         
			if(maxpeak!=0.0)
          		{
           		drawdownpercent=drawdown/maxpeak*100.0;
				if(RelDrawdownPercent<drawdownpercent)
				{
					RelDrawdownPercent=drawdownpercent;
					RelDrawdown=drawdown;
				}
			}

			if(MaxDrawdown<drawdown)
	     	{
 				MaxDrawdown=drawdown;
				if(maxpeak!=0.0) MaxDrawdownPercent=MaxDrawdown/maxpeak*100.0;
	            else MaxDrawdownPercent=100.0;
	       	}

			maxpeak=balance;
			minpeak=balance;
		}

		
		if(minpeak>balance) minpeak=balance;
		if(MaxLoss>balance) MaxLoss=balance;
		
		//---- market orders only
		if(type!=OP_BUY && type!=OP_SELL) continue;
	}
	
	//---- final drawdown check
   	drawdown=maxpeak-minpeak;
   	
	if(maxpeak!=0.0)
	{
		drawdownpercent=drawdown/maxpeak*100.0;
		if(RelDrawdownPercent<drawdownpercent)
        {
			RelDrawdownPercent=drawdownpercent;
			RelDrawdown=drawdown;
        }
	}
	
	if(MaxDrawdown<drawdown)
	{
		MaxDrawdown=drawdown;
		if(maxpeak!=0) MaxDrawdownPercent=MaxDrawdown/maxpeak*100.0;
		else MaxDrawdownPercent=100.0;
	}

	return MaxDrawdownPercent;
}



void CalculateSummary(double initial_deposit)
  {
   int    sequence=0, profitseqs=0, lossseqs=0;
   double sequential=0.0, prevprofit=EMPTY_VALUE, drawdownpercent, drawdown;
   double maxpeak=initial_deposit, minpeak=initial_deposit, balance=initial_deposit;
   int    trades_total=HistoryTotal();
//---- initialize summaries
   InitializeSummaries(initial_deposit);
//----
   for(int i=0; i<trades_total; i++)
     {
      if(!OrderSelect(i,SELECT_BY_POS,MODE_HISTORY)) continue;
      int type=OrderType();
      //---- initial balance not considered
      if(i==0 && type==OP_BALANCE) continue;
      //---- calculate profit
      double profit=OrderProfit()+OrderCommission()+OrderSwap();
      balance+=profit;
      //---- drawdown check
      if(maxpeak<balance)
        {
         drawdown=maxpeak-minpeak;
         if(maxpeak!=0.0)
           {
            drawdownpercent=drawdown/maxpeak*100.0;
            if(RelDrawdownPercent<drawdownpercent)
              {
               RelDrawdownPercent=drawdownpercent;
               RelDrawdown=drawdown;
              }
           }
         if(MaxDrawdown<drawdown)
           {
            MaxDrawdown=drawdown;
            if(maxpeak!=0.0) MaxDrawdownPercent=MaxDrawdown/maxpeak*100.0;
            else MaxDrawdownPercent=100.0;
           }
         maxpeak=balance;
         minpeak=balance;
        }
      if(minpeak>balance) minpeak=balance;
      if(MaxLoss>balance) MaxLoss=balance;
      //---- market orders only
      if(type!=OP_BUY && type!=OP_SELL) continue;
      //---- calculate profit in points
      // profit=(OrderClosePrice()-OrderOpenPrice())/MarketInfo(OrderSymbol(),MODE_POINT);
      SummaryProfit+=profit;
      SummaryTrades++;
      if(type==OP_BUY) LongTrades++;
      else             ShortTrades++;
      //---- loss trades
      if(profit<0)
        {
         LossTrades++;
         GrossLoss+=profit;
         if(MinProfit>profit) MinProfit=profit;
         //---- fortune changed
         if(prevprofit!=EMPTY_VALUE && prevprofit>=0)
           {
            if(ConProfitTrades1<sequence ||
               (ConProfitTrades1==sequence && ConProfit2<sequential))
              {
               ConProfitTrades1=sequence;
               ConProfit1=sequential;
              }
            if(ConProfit2<sequential ||
               (ConProfit2==sequential && ConProfitTrades1<sequence))
              {
               ConProfit2=sequential;
               ConProfitTrades2=sequence;
              }
            profitseqs++;
            AvgConWinners+=sequence;
            sequence=0;
            sequential=0.0;
           }
        }
      //---- profit trades (profit>=0)
      else
        {
         ProfitTrades++;
         if(type==OP_BUY)  WinLongTrades++;
         if(type==OP_SELL) WinShortTrades++;
         GrossProfit+=profit;
         if(MaxProfit<profit) MaxProfit=profit;
         //---- fortune changed
         if(prevprofit!=EMPTY_VALUE && prevprofit<0)
           {
            if(ConLossTrades1<sequence ||
               (ConLossTrades1==sequence && ConLoss2>sequential))
              {
               ConLossTrades1=sequence;
               ConLoss1=sequential;
              }
            if(ConLoss2>sequential ||
               (ConLoss2==sequential && ConLossTrades1<sequence))
              {
               ConLoss2=sequential;
               ConLossTrades2=sequence;
              }
            lossseqs++;
            AvgConLosers+=sequence;
            sequence=0;
            sequential=0.0;
           }
        }
      sequence++;
      sequential+=profit;
      //----
      prevprofit=profit;
     }
//---- final drawdown check
   drawdown=maxpeak-minpeak;
   if(maxpeak!=0.0)
     {
      drawdownpercent=drawdown/maxpeak*100.0;
      if(RelDrawdownPercent<drawdownpercent)
        {
         RelDrawdownPercent=drawdownpercent;
         RelDrawdown=drawdown;
        }
     }
   if(MaxDrawdown<drawdown)
     {
      MaxDrawdown=drawdown;
      if(maxpeak!=0) MaxDrawdownPercent=MaxDrawdown/maxpeak*100.0;
      else MaxDrawdownPercent=100.0;
     }
//---- consider last trade
   if(prevprofit!=EMPTY_VALUE)
     {
      profit=prevprofit;
      if(profit<0)
        {
         if(ConLossTrades1<sequence ||
            (ConLossTrades1==sequence && ConLoss2>sequential))
           {
            ConLossTrades1=sequence;
            ConLoss1=sequential;
           }
         if(ConLoss2>sequential ||
            (ConLoss2==sequential && ConLossTrades1<sequence))
           {
            ConLoss2=sequential;
            ConLossTrades2=sequence;
           }
         lossseqs++;
         AvgConLosers+=sequence;
        }
      else
        {
         if(ConProfitTrades1<sequence ||
            (ConProfitTrades1==sequence && ConProfit2<sequential))
           {
            ConProfitTrades1=sequence;
            ConProfit1=sequential;
           }
         if(ConProfit2<sequential ||
            (ConProfit2==sequential && ConProfitTrades1<sequence))
           {
            ConProfit2=sequential;
            ConProfitTrades2=sequence;
           }
         profitseqs++;
         AvgConWinners+=sequence;
        }
     }
//---- collecting done
   double dnum, profitkoef=0.0, losskoef=0.0, avgprofit=0.0, avgloss=0.0;
//---- average consecutive wins and losses
   dnum=AvgConWinners;
   if(profitseqs>0) AvgConWinners=dnum/profitseqs+0.5;
   dnum=AvgConLosers;
   if(lossseqs>0)   AvgConLosers=dnum/lossseqs+0.5;
//---- absolute values
   if(GrossLoss<0.0) GrossLoss*=-1.0;
   if(MinProfit<0.0) MinProfit*=-1.0;
   if(ConLoss1<0.0)  ConLoss1*=-1.0;
   if(ConLoss2<0.0)  ConLoss2*=-1.0;
//---- profit factor
   if(GrossLoss>0.0) ProfitFactor=GrossProfit/GrossLoss;
//---- expected payoff
   if(ProfitTrades>0) avgprofit=GrossProfit/ProfitTrades;
   if(LossTrades>0)   avgloss  =GrossLoss/LossTrades;
   if(SummaryTrades>0)
     {
      profitkoef=1.0*ProfitTrades/SummaryTrades;
      losskoef=1.0*LossTrades/SummaryTrades;
      ExpectedPayoff=profitkoef*avgprofit-losskoef*avgloss;
     }
//---- absolute drawdown
   AbsoluteDrawdown=initial_deposit-MaxLoss;
  }
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
void InitializeSummaries(double initial_deposit)
  {
   InitialDeposit=initial_deposit;
   MaxLoss=initial_deposit;
   SummaryProfit=0.0;
   GrossProfit=0.0;
   GrossLoss=0.0;
   MaxProfit=0.0;
   MinProfit=0.0;
   ConProfit1=0.0;
   ConProfit2=0.0;
   ConLoss1=0.0;
   ConLoss2=0.0;
   MaxDrawdown=0.0;
   MaxDrawdownPercent=0.0;
   RelDrawdownPercent=0.0;
   RelDrawdown=0.0;
   ExpectedPayoff=0.0;
   ProfitFactor=0.0;
   AbsoluteDrawdown=0.0;
   SummaryTrades=0;
   ProfitTrades=0;
   LossTrades=0;
   ShortTrades=0;
   LongTrades=0;
   WinShortTrades=0;
   WinLongTrades=0;
   ConProfitTrades1=0;
   ConProfitTrades2=0;
   ConLossTrades1=0;
   ConLossTrades2=0;
   AvgConWinners=0;
   AvgConLosers=0;
  }

double CalculateInitialDeposit()
  {
   double initial_deposit=AccountBalance();
	//----
   for(int i=HistoryTotal()-1; i>=0; i--)
     {
      if(!OrderSelect(i,SELECT_BY_POS,MODE_HISTORY)) continue;
      int type=OrderType();
      //---- initial balance not considered
      if(i==0 && type==OP_BALANCE) break;
      if(type==OP_BUY || type==OP_SELL)
        {
         //---- calculate profit
         double profit=OrderProfit()+OrderCommission()+OrderSwap();
         //---- and decrease balance
         initial_deposit-=profit;
        }
      if(type==OP_BALANCE || type==OP_CREDIT)
         initial_deposit-=OrderProfit();
     }
	//----
   return(initial_deposit);
  }