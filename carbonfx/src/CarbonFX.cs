
using System;
using System.Collections;
using System.Configuration;
using System.Reflection;
using System.Threading;
using log4net;
using nj4x.Metatrader;
using System.Net;
using System.Net.Http;
using System.Threading.Tasks;
using System.IO;
using nj4x.CarbonFX;
using Newtonsoft.Json.Linq;
using System.Text;
using System.Collections.Generic;
using System.Web;
using MySql.Data.MySqlClient;


namespace nj4x.CarbonFX
{
    /// <summary>
    /// <para>This is one-to-one MT4 Account copier application, NJ4X API usage sample.</para>
    /// <para>You can edit nj4x_copier_demo.exe.config (App.config) to setup your own Master/Slave accounts and brokers.</para>
    /// <para></para>
    /// <para>What it does ...</para>
    /// <list type="bullet">
    ///     <item>
    ///         <description>Detects new orders made by a Master account and copies them to the Copier account</description>
    ///     </item>
    ///     <item>
    ///         <description>Detects Master orders cancellation and cancels respective Copier account orders</description>
    ///     </item>
    ///     <item>
    ///         <description>Detects closed Master oreders and closes respective Copier account orders</description>
    ///     </item>
    /// </list>
    /// <para></para>
    /// <para>You can easily extend its functionality to ...</para>
    /// <list type="bullet">
    ///     <item>
    ///         <description>Handle one-to-many Master/Slave accounts</description>
    ///     </item>
    ///     <item>
    ///         <description>Handle many-to-many Master/Slave accounts</description>
    ///     </item>
    ///     <item>
    ///         <description>Detect pending orders modifications and reflect them in Copier account orders</description>
    ///     </item>
    ///     <item>
    ///         <description>Detect partially closed Master oreders and close partially respective Copier orders</description>
    ///     </item>
    ///     <item>
    ///         <description>Create Copier Orders using proportional volumes</description>
    ///     </item>
    ///     <item>
    ///         <description>Map different symbols for different brokers</description>
    ///     </item>
    ///     <item>
    ///         <description>Apply custom order copy filtering algorithms</description>
    ///     </item>
    ///     <item>
    ///         <description>Build GUI/WEB applications on top of that</description>
    ///     </item>
    /// </list>
    /// </summary>
    /// 

    public class TraderAccount
    {
        public int id { get; set; }
        public int trader_id { get; set; }
        public string trader_investor_pw { get; set; }
        public string trader_name { get; set; }
        public string trader_master_pw { get; set; }
        public string trader_server { get; set; }
        public int trader_status { get; set; }
        public int trader_online { get; set; }
        public int trader_type { get; set; }
    }


    public  class CarbonFX
    {
    
        /// <summary>
        /// NJ4X Terminal Server host IP address
        /// </summary>
        public static readonly string TerminalHost = ConfigurationManager.AppSettings["terminal_host"];

        /// <summary>
        /// NJ4X Terminal Server port number
        /// </summary>
        public static readonly int TerminalPort = Int32.Parse(ConfigurationManager.AppSettings["terminal_port"]);

        /// <summary>
        /// Master account broker name
        /// </summary>
        public static readonly string MasterBroker = ConfigurationManager.AppSettings["master_broker"];

        /// <summary>
        /// Master Account number
        /// </summary>
        public static readonly string MasterAccount = ConfigurationManager.AppSettings["master_account"];

        /// <summary>
        /// Master Account password
        /// </summary>
        public static readonly string MasterPassword = ConfigurationManager.AppSettings["master_password"];

        /// <summary>
        /// Slave Account Broker Name
        /// </summary>
        public static readonly string CopierBroker = ConfigurationManager.AppSettings["copier_broker"];

        /// <summary>
        /// Slave Account number
        /// </summary>
        public static readonly string CopierAccount = ConfigurationManager.AppSettings["copier_account"];

        /// <summary>
        /// Slave Account password
        /// </summary>
        public static readonly string CopierPassword = ConfigurationManager.AppSettings["copier_password"];

        /// <summary>
        /// CarbonFX application entry point.
        /// </summary>
        /// <param name="args">no arguments are expected, all taken from nj4x_copier_demo.exe.config (App.config)</param>

        public static List<TraderAccount> AccountList;
        public int AccountCount;

        public static string TraderListURL = @"http://api.topforexsignal.com/carbonfx/list";


        public const int TRADER_TYPE_SLAVE = 2;
        public const int TRADER_TYPE_MASTER = 1;
        
        public static void Main(string[] args)
        {
            //
            //ConfigurationManager.AppSettings.Set("nj4x_activation_key", "271143354");
            ConfigurationManager.AppSettings.Set("nj4x_activation_key", "1735263226");
            LoadAccounts();

            TraderAccount MasterTrader = new TraderAccount();
            List<TraderAccount> SlaveAccounts = new List<TraderAccount>();

            foreach (TraderAccount account in CarbonFX.AccountList)
            {
                string type = @"";
                switch(account.trader_type)
                {
                    case TRADER_TYPE_SLAVE:
                        type = "[slave]";
                        SlaveAccounts.Add(account);
                        break;
                    case TRADER_TYPE_MASTER:
                        type = "[master]";
                        MasterTrader = account;
                        break;
                }
                Console.WriteLine("Trader trader_name: " + account.trader_name + " " + type);
            }

            if(MasterTrader.trader_type == 1) Console.WriteLine("Found master ID: " + MasterTrader.trader_id + " " +  MasterTrader.trader_name);
            Console.WriteLine("{0} slaves found",SlaveAccounts.Count);
            Console.WriteLine("Tap ENTER to start mirror trading..");
            Console.ReadLine();
            //System.Environment.Exit(1);
            try
            {
                Console.WriteLine("Starting mirror trading..");
                //var master = new Master(MasterAccount, MasterPassword, MasterBroker);
                //var slave = new Copier(CopierAccount, CopierPassword, CopierBroker);
                Console.WriteLine("Creating master terminal..");
                Console.WriteLine("[M]" + MasterTrader.trader_id.ToString() + " pw:" + MasterTrader.trader_master_pw + " server:" + MasterTrader.trader_server.ToString());
                var master = new Master(MasterTrader.trader_id.ToString(),MasterTrader.trader_master_pw,MasterTrader.trader_server);
                Console.WriteLine("Master created..");
                foreach (TraderAccount account in SlaveAccounts)
                {   
                    Console.WriteLine("Creating slave terminal for: " + account.trader_name + " on MT4 ID: " + account.trader_id);
                    try
                    {
                        var slave = new Copier(account.trader_id.ToString(), account.trader_master_pw, account.trader_server);
                        //slave.IsReconnect = true;
                        Console.WriteLine("[M]" + MasterTrader.trader_id.ToString() + "->[S]" + account.trader_id.ToString());
                        master.AddSlave(slave);
                    }
                    catch (Exception e)
                    {
                        Console.WriteLine(e);

                    }

                } 

                //
                //master.AddSlave(slave);
                //
                while (true)
                {
                    Thread.Sleep(100000);
                }
            }
            catch (Exception e)
            {
                Console.WriteLine(e);
            }
        }

  

        public static void LoadAccounts()
        {
            string response = HTTPRequest(CarbonFX.TraderListURL);
            Console.WriteLine("Requesting Trader List from API: " + CarbonFX.TraderListURL);
    
            JObject json = JObject.Parse(response);

           Console.WriteLine("Server response: {0}",json["response"]);

           IList accounts = (IList)json["data"]["accounts"];
           CarbonFX.AccountList = new List<TraderAccount>();
            foreach (JToken account in accounts)
            {
                TraderAccount traderAccount = Newtonsoft.Json.JsonConvert.DeserializeObject<TraderAccount>(account.ToString());
                CarbonFX.AccountList.Add(traderAccount);
            }
            Console.WriteLine("{0} Trader accounts loaded",CarbonFX.AccountList.Count);
           return;

            //Console.WriteLine("data: " + response)
            //dynamic accountObj = Newtonsoft.Json.JsonConvert.DeserializeObject(response);
            //Console.WriteLine("response: {0}", accountObj);
            /**
            foreach(var name in accountObj.GetType().GetProperties())
            {
                Console.WriteLine("{0}",name);
            }**/

        }


        public static String HTTPRequest(string requestUrl)
        {
            try
            {
                  HttpWebRequest request = (HttpWebRequest)WebRequest.Create (requestUrl);

            // Set some reasonable limits on resources used by this request
            request.MaximumAutomaticRedirections = 4;
            request.MaximumResponseHeadersLength = 4;
            // Set credentials to use for this request.
            request.Credentials = CredentialCache.DefaultCredentials;
            HttpWebResponse response = (HttpWebResponse)request.GetResponse ();

            //Console.WriteLine ("Content length is {0}", response.ContentLength);
            //Console.WriteLine ("Content type is {0}", response.ContentType);

            // Get the stream associated with the response.
            Stream receiveStream = response.GetResponseStream ();

            // Pipes the stream to a higher level stream reader with the required encoding format. 
            StreamReader readStream = new StreamReader (receiveStream, Encoding.UTF8);

            //Console.WriteLine ("Response stream received.");
            //Console.WriteLine (readStream.ReadToEnd ());
            String res = readStream.ReadToEnd();

                response.Close ();
            readStream.Close();
            return res;
                
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return null;
            }
        }
    }

 

    /// <summary>
    /// Copier class implements MT4 Copier Account Logic,
    /// i.e. synchronizes trading operations between Master and Copier accounts
    /// </summary>
    public class Copier : Strategy
    {
        private static readonly ILog Logger = LogManager.GetLogger(MethodBase.GetCurrentMethod().DeclaringType);
        private readonly string _acc;
        private readonly string _broker;
        private readonly Hashtable _ordersMap;
        private readonly string _passwd;
        private Master _master;

        /// <summary>
        /// When constructed, tries to connect to MT4 Server immediately
        /// </summary>
        /// <param name="acc">MT4 account id</param>
        /// <param name="passwd">MT4 account password</param>
        /// <param name="broker">MT4 Broker Name (known to NJ4X Terminal Server)</param>
        public Copier(string acc, string passwd, string broker)
        {
            _acc = acc;
            _passwd = passwd;
            _broker = broker;
            _ordersMap = new Hashtable();
            Connect(CarbonFX.TerminalHost, CarbonFX.TerminalPort, new Broker(_broker), _acc, _passwd);
            Info("Slave connected");
        }

        private void Info(String msg)
        {
            Logger.Info(_acc + "@" + _broker + "> " + msg);
        }

        /// <summary>
        /// Loads current Copier account orders at creation.
        /// </summary>
        public override void Init()
        {
            int ordersTotal = OrdersTotal();
            for (int i = 0; i < ordersTotal; i++)
            {
                IOrderInfo order = OrderGet(i, SelectionType.SELECT_BY_POS, SelectionPool.MODE_TRADES);
                if (order != null)
                {
                    /*
                    if (order.GetMagic() != 0)
                    {
                        Console.WriteLine("Closing order: " + order);
                        CloseOrder(order);
                    }
                    continue;
                    */
                    if (order.GetMagic() != 0)
                    {
                        _ordersMap.Add(
                            order.GetMagic(), // Master's order ticket
                            order.GetTicket()
                            );
                        Info(String.Format("Master order {0} is mapped to {1}", order.GetMagic(), order));
                    }
                    else
                    {
                        Info(String.Format("Custom order {0} left unmanaged", order));

                        //lets try to close this
                        Info(String.Format("Closing orphaned order: {0}", order));
                        
                    }
                     

                }
            }
        }

        internal void SetMaster(object master)
        {
            _master = (Master) master;
            Info(String.Format("Attached to {0}", _master.Acc + "@" + _master.Broker));
            //
            IPositionInfo positionInfo = _master.PositionInfo;
            foreach (IOrderInfo masterOrder in positionInfo.GetHistoricalOrders().Values)
            {
                MasterOrderClosedOrDeleted(masterOrder);
            }
        }

        internal void MasterOrderClosedOrDeleted(IOrderInfo masterOrder)
        {
            if (_ordersMap.Contains(masterOrder.GetTicket()))
            {
                var orderTicket = (int) _ordersMap[masterOrder.GetTicket()];
                IOrderInfo order = OrderGet(orderTicket, SelectionType.SELECT_BY_TICKET, SelectionPool.MODE_TRADES);
                if (order != null)
                {
                    if (CloseOrder(order))
                    {
                        _ordersMap.Remove(masterOrder.GetTicket());
                    }
                }
                else
                {
                    Info(String.Format("Order {0} not found", orderTicket));
                    _ordersMap.Remove(masterOrder.GetTicket());
                }
            }
        }

        private bool CloseOrder(IOrderInfo order)
        {
            try
            {
                TradeOperation orderType = order.GetTradeOperation();

                Console.WriteLine("Closing an order - order.closePrice: {0} market.bid: {1} market.ask: {2} order.OrderType: {3}", order.GetClosePrice(),Marketinfo(order.GetSymbol(),MarketInfo.MODE_BID),Marketinfo(order.GetSymbol(),MarketInfo.MODE_ASK),orderType);   

                switch (orderType)
                {
                    case TradeOperation.OP_BUY:
                    case TradeOperation.OP_SELL:
                        RefreshRates();
                        if (order.GetTicket() >0 && OrderClose(order.GetTicket(),order.GetLots(),
                                       Marketinfo(order.GetSymbol(),
                                                  orderType == TradeOperation.OP_BUY
                                                      ? MarketInfo.MODE_BID
                                                      : MarketInfo.MODE_ASK),
                                       20, 0))
                        {
                            Info(String.Format("Order {0} has been closed", order));
                            return true;
                        }
                        Info(String.Format("Order {0} to be closed; todo", order));
                        return false;
                    default:
                        if (OrderDelete(order.GetTicket(), Color.Black))
                        {
                            Info(String.Format("Order {0} has been deleted", order));
                            return true;
                        }
                        else
                        {
                            Info(String.Format("Can not delete order {0}", order));
                            return false;
                        }
                }
            }
            catch (ErrInvalidTicket)
            {
                Info(String.Format("Looks like order {0} has been deleted manually", order));
                return true;
            }
            catch (ErrInvalidPrice)
            {
                Info(String.Format("## ErrInvalidPrice - Price slippage ## - order.closePrice: {0} market.bid: {1} market.ask: {2} order.OrderType: {3}", order.GetClosePrice(), Marketinfo(order.GetSymbol(), MarketInfo.MODE_BID), Marketinfo(order.GetSymbol(), MarketInfo.MODE_ASK), order.GetTradeOperation()));
                return false;
            }
        }

  

        internal void MasterOrderCreated(IOrderInfo masterOrder)
        {



            RefreshRates();
            double orderPrice =  Marketinfo(masterOrder.GetSymbol(),
                                                   (masterOrder.GetTradeOperation() == TradeOperation.OP_BUY)
                                                      ? MarketInfo.MODE_ASK
                                                      : MarketInfo.MODE_BID);
            TradeOperation orderType = masterOrder.GetTradeOperation();

            Console.WriteLine("Creating an order - order.closePrice: {0} market.bid: {1} market.ask: {2} order.OrderType: {3}", masterOrder.GetClosePrice(), Marketinfo(masterOrder.GetSymbol(), MarketInfo.MODE_BID), Marketinfo(masterOrder.GetSymbol(), MarketInfo.MODE_ASK), orderType);   


            //Info(String.Format("Executing order for account {0}", _acc));

           try {
                     int ticket = OrderSend(
                        masterOrder.GetSymbol(),
                        masterOrder.GetTradeOperation(),
                        masterOrder.GetLots(),
                        //masterOrder.GetOpenPrice(), // todo: adjust price to current Bid/Ask if OrderType==OP_BUY/SELL
                        orderPrice,
                        20,
                        masterOrder.GetStopLoss(),
                        masterOrder.GetTakeProfit(),
                        _master.Acc + "@" + _master.Broker + "-> _acc:" + _acc,
                        masterOrder.GetTicket(),
                        masterOrder.GetExpiration(),
                        0
                        );
                    if (ticket != 0)
                    {
                        _ordersMap.Add(masterOrder.GetTicket(), ticket);
                        Info(String.Format("Master order {0} is mapped to {1}", masterOrder, ticket));
                    }
                
                }
            catch(ErrTradeDisabled)
           {
               Info(String.Format("Trade disabled for accoutn: {0}", _acc));
           }
            catch(ErrInvalidPrice)
           {
               Info(String.Format("## ErrInvalidPrice - Price slippage ## - order.closePrice: {0} market.bid: {1} market.ask: {2} order.OrderType: {3}", masterOrder.GetClosePrice(), Marketinfo(masterOrder.GetSymbol(), MarketInfo.MODE_BID), Marketinfo(masterOrder.GetSymbol(), MarketInfo.MODE_ASK), orderType)); 
           }
            /*
         catch(ErrInvalidPrice)
         {
             Info(String.Format("Price invalid: {0} ", masterOrder.GetOpenPrice()));

         }
         catch(ErrInvalidTradeVolume)
         {
             Info(String.Format("Trade volume invalid: {0} ", masterOrder.GetLots()));

         }
         catch(ErrTradeDisabled)
         {
             Info(String.Format("Trade disabled!!"));

         }
         catch(ErrOffQuotes)
         {
             Info(String.Format("This trade is off quotes!!"));

         }
              **/

        }

        /// <summary>
        /// It is called by Master account on its position change.
        /// </summary>
        /// <param name="masterPositionChanges">Changes in a Master's position (IPositionChangeInfo)</param>
        public void OnChange(object masterPositionChanges)
        {
            var changes = (IPositionChangeInfo) masterPositionChanges;
            foreach (IOrderInfo o in changes.GetNewOrders())
            {
                Logger.Info("NEW: " + o);
                
                MasterOrderCreated(o);
            }
            foreach (IOrderInfo o in changes.GetModifiedOrders())
            {
                Logger.Info("MODIFIED: " + o);
            }
            foreach (IOrderInfo o in changes.GetClosedOrders())
            {
                Logger.Info("CLOSED: " + o);
                MasterOrderClosedOrDeleted(o);
            }
            foreach (IOrderInfo o in changes.GetDeletedOrders())
            {
                Logger.Info("DELETED: " + o);
                MasterOrderClosedOrDeleted(o);
            }
        }
    }

    /// <summary>
    /// This class implements Master MT4 account logic, 
    /// i.e. detects changes to the master account and informs registered Copier accounts to reflect those changes.
    /// </summary>
    public class Master : Strategy
    {
        private static readonly ILog Logger = LogManager.GetLogger(MethodBase.GetCurrentMethod().DeclaringType);
        internal readonly string Acc;
        internal readonly string Broker;
        internal readonly string Passwd;
        internal readonly ArrayList Slaves;
        internal IPositionInfo PositionInfo;

        /// <summary>
        /// When constructed, tries to connect to MT4 Server immediately
        /// </summary>
        /// <param name="acc">MT4 account id</param>
        /// <param name="passwd">MT4 account password</param>
        /// <param name="broker">MT4 Broker Name (known to NJ4X Terminal Server)</param>
        public Master(string acc, string passwd, string broker)
        {
            Acc = acc;
            Passwd = passwd;
            Broker = broker;
            if(acc == null || passwd == null || broker == null)
            {
                Logger.Info("There was an error connecting to the master account");
                System.Environment.Exit(1);
            }
            //
            Slaves = new ArrayList();
            //
            SetPositionListener(
                delegate(IPositionInfo info)
                    {
                        PositionInfo = info;
                    },
                delegate(IPositionInfo info, IPositionChangeInfo changes)
                    {
                        foreach (Copier c in Slaves)
                        {
                            ThreadPool.QueueUserWorkItem(c.OnChange, changes);
                        }
                    });
            Connect(CarbonFX.TerminalHost, CarbonFX.TerminalPort, new Broker(Broker), Acc, Passwd);
            //
            Info("Master connected");
        }

        /*
        public override int CoordinationIntervalMillis()
        {
            return 1000;
        }

        public override void Coordinate()
        {
            Hashtable liveOrders = PositionInfo.GetLiveOrders();
            ICollection values = liveOrders.Values;
            foreach (IOrderInfo order in values)
            {
                Console.WriteLine("{3} -> OP={0} price={1} profit={2}", order.GetTradeOperation(),
                                  order.GetOpenPrice(), order.GetProfit(), order.Ticket());
            }
        }
        */

        private void Info(String msg)
        {
            Logger.Info(Acc + "@" + Broker + "> " + msg);
        }

        /// <summary>
        /// Attaches Copier account to this Master.
        /// </summary>
        public void AddSlave(Copier copier)
        {
            Slaves.Add(copier);
            //copier.SetMaster(this);
            ThreadPool.QueueUserWorkItem(copier.SetMaster, this);
        }
    }
}