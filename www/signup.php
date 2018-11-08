<?
if(!isset($_SESSION['traderIDs']))
{
    $res = json_decode(file_get_contents('http://' .  API_HOST.'/trader/get_trader_ids'),true);
    if($res && $res['response'] == 1)
    {
        $traderList = $res['data']['traders'];
    } else $traderList = false;
} else {
    $traderList = $_SESSION['traderIDs'];
}
?>
<!-- home section Starts here -->
<section id="home" class="content signup" xmlns="http://www.w3.org/1999/html">

    <div class="margin20"></div>

    <div class="container">

        <ul id="progressbar">
            <li class="active"><a href="/signup/#1">Choose System</a></li>
            <li><a href="/signup/#2">Confirm<span> </span></a></li>
            <li>Checkout<span> </span></li>
        </ul>
        <div class="trader-container" style="padding-bottom:30px;">
            <div id="msform">
                <!-- fieldsets -->
                <fieldset id="step1">
                    <div id="form-broker-register">
                        <form id="register-form"  method="post" accept-charset="iso-8859-1" action="http://www.aweber.com/scripts/addlead.pl" >
                            <input type="hidden" name="meta_web_form_id" value="2044839129" />
                            <input type="hidden" name="meta_split_id" value="" />
                            <input type="hidden" name="listname" value="awlist3591883" />
                            <input id="redirect" type="hidden" name="redirect" value=""  />

                            <input type="hidden" name="meta_adtracking" value="My_Web_Form" />
                            <input type="hidden" name="meta_message" value="1" />
                            <input type="hidden" name="meta_required" value="name,email" />

                            <input type="hidden" name="meta_tooltip" value="custom Country||Your country,,custom Phone Number||+65 12354233" />
                            <input type="hidden" name="meta_preselected_awf_field-65794368" value=""/>

                            <h3>Welcome to our <b>Forex Auto Copier</b> signup page, <br/>To start copying our signal providers, please complete the signup process.</h3>
                                <input type="text" class="userName" name="name" placeholder="Full name" />
                                <div class="margin10"></div>

                                <input name="email" type="text" placeholder="Email address" class="userEmail" />
                                <!--<select id="countries" class="form-control bfh-countries" data-country="ZZ" name="custom Country" tabindex="502"></select>-->
                                <div class="margin10"></div>

                                <select id="countries" name="custom Country" tabindex="502" class="countries ">
                                <option selected="selected" class="multiChoice" value="ZZ">Your country</option>
                                <option class="multiChoice" value="Afghanistan">Afghanistan</option>
                                <option class="multiChoice" value="Albania">Albania</option>
                                <option class="multiChoice" value="Algeria">Algeria</option>
                                <option class="multiChoice" value="Andorra">Andorra</option>
                                <option class="multiChoice" value="Angola">Angola</option>
                                <option class="multiChoice" value="Anguilla">Anguilla</option>
                                <option class="multiChoice" value="Antigua Barbuda">Antigua Barbuda</option>
                                <option class="multiChoice" value="Argentina">Argentina</option>
                                <option class="multiChoice" value="Aruba">Aruba</option>
                                <option class="multiChoice" value="Australia">Australia</option>
                                <option class="multiChoice" value="Austria">Austria</option>
                                <option class="multiChoice" value="Azerbaijan">Azerbaijan</option>
                                <option class="multiChoice" value="Bahamas">Bahamas</option>
                                <option class="multiChoice" value="Bahrain">Bahrain</option>
                                <option class="multiChoice" value="Bangladesh">Bangladesh</option>
                                <option class="multiChoice" value="Barbados">Barbados</option>
                                <option class="multiChoice" value="Belarus">Belarus</option>
                                <option class="multiChoice" value="Belgium">Belgium</option>
                                <option class="multiChoice" value="Belize">Belize</option>
                                <option class="multiChoice" value="Benin">Benin</option>
                                <option class="multiChoice" value="Bermuda">Bermuda</option>
                                <option class="multiChoice" value="Bhutan">Bhutan</option>
                                <option class="multiChoice" value="Bolivia">Bolivia</option>
                                <option class="multiChoice" value="Bosnia-Herzegovina">Bosnia-Herzegovina</option>
                                <option class="multiChoice" value="Botswana">Botswana</option>
                                <option class="multiChoice" value="Brazil">Brazil</option>
                                <option class="multiChoice" value="British Virgin Islands">British Virgin Islands</option>
                                <option class="multiChoice" value="Brunei">Brunei</option>
                                <option class="multiChoice" value="Bulgaria">Bulgaria</option>
                                <option class="multiChoice" value="Burkina">Burkina</option>
                                <option class="multiChoice" value="Burkina Faso">Burkina Faso</option>
                                <option class="multiChoice" value="Burma">Burma</option>
                                <option class="multiChoice" value="Burundi">Burundi</option>
                                <option class="multiChoice" value="Cambodia">Cambodia</option>
                                <option class="multiChoice" value="Cameroon">Cameroon</option>
                                <option class="multiChoice" value="Canada">Canada</option>
                                <option class="multiChoice" value="Cape Verde">Cape Verde</option>
                                <option class="multiChoice" value="Cayman Islands">Cayman Islands</option>
                                <option class="multiChoice" value="Chad">Chad</option>
                                <option class="multiChoice" value="Chile">Chile</option>
                                <option class="multiChoice" value="China">China</option>
                                <option class="multiChoice" value="Colombia">Colombia</option>
                                <option class="multiChoice" value="Comoros">Comoros</option>
                                <option class="multiChoice" value="Congo">Congo</option>
                                <option class="multiChoice" value="Costa Rica">Costa Rica</option>
                                <option class="multiChoice" value="Cote D'Ivoire">Cote D'Ivoire</option>
                                <option class="multiChoice" value="Croatia">Croatia</option>
                                <option class="multiChoice" value="Cuba">Cuba</option>
                                <option class="multiChoice" value="Cyprus">Cyprus</option>
                                <option class="multiChoice" value="Czech Republic">Czech Republic</option>
                                <option class="multiChoice" value="Denmark">Denmark</option>
                                <option class="multiChoice" value="Djibouti">Djibouti</option>
                                <option class="multiChoice" value="Dominica">Dominica</option>
                                <option class="multiChoice" value="Dominican Republic">Dominican Republic</option>
                                <option class="multiChoice" value="Ecuador">Ecuador</option>
                                <option class="multiChoice" value="Egypt">Egypt</option>
                                <option class="multiChoice" value="El Salvador">El Salvador</option>
                                <option class="multiChoice" value="Equatorial Guinea">Equatorial Guinea</option>
                                <option class="multiChoice" value="Eritrea">Eritrea</option>
                                <option class="multiChoice" value="Estonia">Estonia</option>
                                <option class="multiChoice" value="Ethiopia">Ethiopia</option>
                                <option class="multiChoice" value="Falkland Islands">Falkland Islands</option>
                                <option class="multiChoice" value="Faroe Islands">Faroe Islands</option>
                                <option class="multiChoice" value="Fiji">Fiji</option>
                                <option class="multiChoice" value="Finland">Finland</option>
                                <option class="multiChoice" value="France">France</option>
                                <option class="multiChoice" value="French Polynesia">French Polynesia</option>
                                <option class="multiChoice" value="Gabon">Gabon</option>
                                <option class="multiChoice" value="Germany">Germany</option>
                                <option class="multiChoice" value="Ghana">Ghana</option>
                                <option class="multiChoice" value="Gibraltar">Gibraltar</option>
                                <option class="multiChoice" value="Greece">Greece</option>
                                <option class="multiChoice" value="Grenada">Grenada</option>
                                <option class="multiChoice" value="Guam">Guam</option>
                                <option class="multiChoice" value="Guatemala">Guatemala</option>
                                <option class="multiChoice" value="Guinea">Guinea</option>
                                <option class="multiChoice" value="Guinea-Bissau">Guinea-Bissau</option>
                                <option class="multiChoice" value="Guyana">Guyana</option>
                                <option class="multiChoice" value="Haiti">Haiti</option>
                                <option class="multiChoice" value="Honduras">Honduras</option>
                                <option class="multiChoice" value="Hong Kong">Hong Kong</option>
                                <option class="multiChoice" value="Hungary">Hungary</option>
                                <option class="multiChoice" value="Iceland">Iceland</option>
                                <option class="multiChoice" value="India">India</option>
                                <option class="multiChoice" value="Indonesia">Indonesia</option>
                                <option class="multiChoice" value="Iran">Iran</option>
                                <option class="multiChoice" value="Iraq">Iraq</option>
                                <option class="multiChoice" value="Ireland">Ireland</option>
                                <option class="multiChoice" value="Israel">Israel</option>
                                <option class="multiChoice" value="Italy">Italy</option>
                                <option class="multiChoice" value="Jamaica">Jamaica</option>
                                <option class="multiChoice" value="Japan">Japan</option>
                                <option class="multiChoice" value="Jordan">Jordan</option>
                                <option class="multiChoice" value="Kazakstan">Kazakstan</option>
                                <option class="multiChoice" value="Kenya">Kenya</option>
                                <option class="multiChoice" value="Kiribati">Kiribati</option>
                                <option class="multiChoice" value="Kuwait">Kuwait</option>
                                <option class="multiChoice" value="Kyrgyzstan">Kyrgyzstan</option>
                                <option class="multiChoice" value="Laos">Laos</option>
                                <option class="multiChoice" value="Latvia">Latvia</option>
                                <option class="multiChoice" value="Lebanon">Lebanon</option>
                                <option class="multiChoice" value="Lesotho">Lesotho</option>
                                <option class="multiChoice" value="Liberia">Liberia</option>
                                <option class="multiChoice" value="Libya">Libya</option>
                                <option class="multiChoice" value="Liechtenstein">Liechtenstein</option>
                                <option class="multiChoice" value="Lithuania">Lithuania</option>
                                <option class="multiChoice" value="Luxembourg">Luxembourg</option>
                                <option class="multiChoice" value="Macedonia">Macedonia</option>
                                <option class="multiChoice" value="Madagascar">Madagascar</option>
                                <option class="multiChoice" value="Malawi">Malawi</option>
                                <option class="multiChoice" value="Malaysia">Malaysia</option>
                                <option class="multiChoice" value="Maldives">Maldives</option>
                                <option class="multiChoice" value="Mali">Mali</option>
                                <option class="multiChoice" value="Malta">Malta</option>
                                <option class="multiChoice" value="Marshall Islands">Marshall Islands</option>
                                <option class="multiChoice" value="Mauritania">Mauritania</option>
                                <option class="multiChoice" value="Mauritius">Mauritius</option>
                                <option class="multiChoice" value="Mexico">Mexico</option>
                                <option class="multiChoice" value="Micronesia">Micronesia</option>
                                <option class="multiChoice" value="Monaco">Monaco</option>
                                <option class="multiChoice" value="Mongolia">Mongolia</option>
                                <option class="multiChoice" value="Montenegro">Montenegro</option>
                                <option class="multiChoice" value="Montserrat">Montserrat</option>
                                <option class="multiChoice" value="Morocco">Morocco</option>
                                <option class="multiChoice" value="Mozambique">Mozambique</option>
                                <option class="multiChoice" value="Namibia">Namibia</option>
                                <option class="multiChoice" value="Nauru">Nauru</option>
                                <option class="multiChoice" value="Nepal">Nepal</option>
                                <option class="multiChoice" value="Netherlands">Netherlands</option>
                                <option class="multiChoice" value="Netherlands Antilles">Netherlands Antilles</option>
                                <option class="multiChoice" value="New Zealand">New Zealand</option>
                                <option class="multiChoice" value="Nicaragua">Nicaragua</option>
                                <option class="multiChoice" value="Niger">Niger</option>
                                <option class="multiChoice" value="Nigeria">Nigeria</option>
                                <option class="multiChoice" value="North Korea">North Korea</option>
                                <option class="multiChoice" value="Northern Mariana Islands">Northern Mariana Islands</option>
                                <option class="multiChoice" value="Norway">Norway</option>
                                <option class="multiChoice" value="Oman">Oman</option>
                                <option class="multiChoice" value="Pakistan">Pakistan</option>
                                <option class="multiChoice" value="Palua">Palua</option>
                                <option class="multiChoice" value="Panama">Panama</option>
                                <option class="multiChoice" value="Papua New Guinea">Papua New Guinea</option>
                                <option class="multiChoice" value="Paraguay">Paraguay</option>
                                <option class="multiChoice" value="Peru">Peru</option>
                                <option class="multiChoice" value="Philippines">Philippines</option>
                                <option class="multiChoice" value="Pitcairn Island">Pitcairn Island</option>
                                <option class="multiChoice" value="Poland">Poland</option>
                                <option class="multiChoice" value="Portugal">Portugal</option>
                                <option class="multiChoice" value="Puerto Rico">Puerto Rico</option>
                                <option class="multiChoice" value="Qatar">Qatar</option>
                                <option class="multiChoice" value="Romania">Romania</option>
                                <option class="multiChoice" value="Russia">Russia</option>
                                <option class="multiChoice" value="Rwanda">Rwanda</option>
                                <option class="multiChoice" value="Samoa">Samoa</option>
                                <option class="multiChoice" value="San Marino">San Marino</option>
                                <option class="multiChoice" value="Sao Tome and Principe">Sao Tome and Principe</option>
                                <option class="multiChoice" value="Saudi Arabia">Saudi Arabia</option>
                                <option class="multiChoice" value="Senegal">Senegal</option>
                                <option class="multiChoice" value="Serbia">Serbia</option>
                                <option class="multiChoice" value="Seychelles">Seychelles</option>
                                <option class="multiChoice" value="Sierra Leone">Sierra Leone</option>
                                <option class="multiChoice" value="Singapore">Singapore</option>
                                <option class="multiChoice" value="Slovakia">Slovakia</option>
                                <option class="multiChoice" value="Slovenia">Slovenia</option>
                                <option class="multiChoice" value="Solomon Islands">Solomon Islands</option>
                                <option class="multiChoice" value="Somalia">Somalia</option>
                                <option class="multiChoice" value="South Africa">South Africa</option>
                                <option class="multiChoice" value="South Georgia">South Georgia</option>
                                <option class="multiChoice" value="South Korea">South Korea</option>
                                <option class="multiChoice" value="Spain">Spain</option>
                                <option class="multiChoice" value="Sri Lanka">Sri Lanka</option>
                                <option class="multiChoice" value="St Helena">St Helena</option>
                                <option class="multiChoice" value="St Kitts Nevis">St Kitts Nevis</option>
                                <option class="multiChoice" value="St Lucia">St Lucia</option>
                                <option class="multiChoice" value="St Vincent The Grenadines">St Vincent The Grenadines</option>
                                <option class="multiChoice" value="Sudan">Sudan</option>
                                <option class="multiChoice" value="Suriname">Suriname</option>
                                <option class="multiChoice" value="Swaziland">Swaziland</option>
                                <option class="multiChoice" value="Sweden">Sweden</option>
                                <option class="multiChoice" value="Switzerland">Switzerland</option>
                                <option class="multiChoice" value="Syria">Syria</option>
                                <option class="multiChoice" value="Taiwan">Taiwan</option>
                                <option class="multiChoice" value="Tanzania">Tanzania</option>
                                <option class="multiChoice" value="Thailand">Thailand</option>
                                <option class="multiChoice" value="The Gambia">The Gambia</option>
                                <option class="multiChoice" value="Togo">Togo</option>
                                <option class="multiChoice" value="Tonga">Tonga</option>
                                <option class="multiChoice" value="Trinidad and Tobago">Trinidad and Tobago</option>
                                <option class="multiChoice" value="Tunisia">Tunisia</option>
                                <option class="multiChoice" value="Turkey">Turkey</option>
                                <option class="multiChoice" value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                                <option class="multiChoice" value="Tuvalu">Tuvalu</option>
                                <option class="multiChoice" value="Uganda">Uganda</option>
                                <option class="multiChoice" value="Ukraine">Ukraine</option>
                                <option class="multiChoice" value="United Arab Emirates">United Arab Emirates</option>
                                <option class="multiChoice" value="United Kingdom">United Kingdom</option>
                                <option class="multiChoice" value="Uruguay">Uruguay</option>
                                <option class="multiChoice" value="Uzbekistan">Uzbekistan</option>
                                <option class="multiChoice" value="Vanuatu">Vanuatu</option>
                                <option class="multiChoice" value="Vatican City State">Vatican City State</option>
                                <option class="multiChoice" value="Venezuela">Venezuela</option>
                                <option class="multiChoice" value="Vietnam">Vietnam</option>
                                <option class="multiChoice" value="West Indies">West Indies</option>
                                <option class="multiChoice" value="Western Samoa">Western Samoa</option>
                                <option class="multiChoice" value="Yemen">Yemen</option>
                                <option class="multiChoice" value="Zambia">Zambia</option>
                                <option class="multiChoice" value="Zimbabwe">Zimbabwe</option>
                                </select>
                        </form>

                        <div class="margin30"></div>
                        <h3 class="aligncenter">Select a Signal System of your choice:</h3>


                        <div id="select-sp" class="aligncenter">
                            <select name="select-sp" id="sp-dropwdown">
                                <option value="">Select a Signal System </option>
                                <?
                                foreach($traderList as $t_item)
                                {
                                ?>
                                <option data-user-trader="<? print $t_item['trader_full_name']; ?>" value="<? print $t_item['trader_name'];?>"><? print $t_item['trader_full_name']; ?></option>
                                <?
                                }
                                ?>
                            </select>
                        </div>
                        <div class="aligncenter" style="color:#c0c0c0;">Note: Signal Systems can be changed anytime after signup.</div>
                        <div class="container aligncenter" style="width:100%;">
                            <?php

                            $result = file_get_contents('http://' . API_HOST . '/trader');
                            $traders = json_decode($result,true);
                            //aasort($traders['data']['traders],'user_name');
                            foreach($traders['data']['traders'] as $item)
                            {
                                ?>

                            <div class="trader-container-signup"  data-user-name="<? print $item['user_name'];?>">
                                    <span class="one-half column  trader-stats">
                                       <div class="trader-name"><h2 class="trader-title"><? print $item['full_name']?></h2></div>
                                       <div class="growth-30day">Total growth: <strong class="green"><? print $item['total_growth'];?>%</strong></div>

                                       <div class="growth-30day">Growth in the last 30 days: <strong class="green"><? print number_format($item['30day_growth'],2);?>%</strong></div>
                                       <div class="maxdd">Max Drawdown: <strong class="red">-<? print $item['max_drawdown'];?>%</strong></div>
                                       <div class="growth-avg">Average monthly growth: <strong class="green"><? print number_format($item['avg_monthly_growth'],2);?>%</strong></div>
                                       <div class="running-weeks">Account age:<strong><? print $item['account_age'];?></strong></div>
                                       <div class="running-weeks"><span class="fa fa-users"></span>  Followers: <strong><? print ($item['followers']>0)?$item['followers']:'NEW';?></strong></div>

                                    </span>

                                    <span class="one-fourth column trader-chart" style="float:right;">
                                        <div>Performance</div>
                                       <!--<img src="/images/chart.png" width="215" height="125"/>-->
                                        <div class="chart-container-traders">
                                            <canvas id="trader_chart_large-<? print $item['id']?>"  class="alignright" width="300" height="200"></canvas>
                                        </div>
                                        <script>
                                            growth_small(<? print $item['id']; ?>,'trader_chart_large-<? print $item['id']; ?>','<? print API_HOST;?>','small');
                                        </script>
                                    </span>
                            </div>

                                <?
                            }
                            ?>
                        </div>
                        <div class="margin10"></div>


                        <a class="button large next alignright" href="/signup/#2">Next<span class="fa fa-chevron-right"></span></a>

            </div>
            </fieldset>


            <fieldset style="text-align: center !important;" id="step2">
                    <div id="has-account">
                        <h2 class="aligncenter title">Please confirm your information below</h2>
                        <div class="verification-container">
                        <h3 id="userFullName">Full name:  <span>User name</span></h3>
                        <h3 id="userEmail">Email: <span>User email</span></h3>
                        <h3 id="userTrader">Signal Provider: <strong></strong></h3>
                    </div>

                    <div class="margin10"></div>
                    <div class="container aligncenter" style="width:100%;">
                        <?php

                        //$result = file_get_contents('http://' . API_HOST . '/trader');
                        //$traders = json_decode($result,true);
                        //aasort($traders['data']['traders],'user_name');
                        foreach($traders['data']['traders'] as $item)
                        {
                            ?>

                            <div class="trader-container-signup"  data-user-name="<? print $item['user_name'];?>">
                                    <span class="one-half column  trader-stats">
                                       <div class="trader-name"><h2 class="trader-title"><? print $item['full_name']?></h2></div>
                                       <div class="growth-30day">Total growth: <strong class="green"><? print $item['total_growth'];?>%</strong></div>

                                       <div class="growth-30day">Growth in the last 30 days: <strong class="green"><? print number_format($item['30day_growth'],2);?>%</strong></div>
                                       <div class="maxdd">Max Drawdown: <strong class="red">-<? print $item['max_drawdown'];?>%</strong></div>
                                       <div class="growth-avg">Average monthly growth: <strong class="green"><? print number_format($item['avg_monthly_growth'],2);?>%</strong></div>
                                       <div class="running-weeks">Account age:<strong><? print $item['account_age'];?></strong></div>
                                       <div class="running-weeks"><span class="fa fa-users"></span>  Followers: <strong><? print ($item['followers']>0)?$item['followers']:'NEW';?></strong></div>

                                    </span>

                                    <span class="one-fourth column trader-chart" style="float:right;">
                                        <div>Performance</div>
                                       <!--<img src="/images/chart.png" width="215" height="125"/>-->
                                        <div class="chart-container-traders">
                                            <canvas id="trader_chart_large-<? print $item['id'] . '2'?>"  class="alignright" width="300" height="200"></canvas>
                                        </div>
                                        <script>
                                            growth_small(<? print $item['id']; ?>,'trader_chart_large-<? print $item['id'] . '2'; ?>','<? print API_HOST;?>','small');
                                        </script>
                                    </span>
                            </div>

                            <?
                        }
                        ?>

                    </div>
                    <div class="aligncenter" style="color:#c0c0c0;">Note: Signal Systems can be changed anytime after signup.</div>

                    <div class="margin20"></div>

                    <a class="button large form-broker-register-next">Confirm</a>
                    <div class="margin10"></div>


                        <span style="color:#ff0000">Click the "Confirm" button to proceed</span>


                    <div class="margin20"></div>


                    <div class="margin10"></div>
                    <a class="button large previous alignright" href="/signup/#1"><span class="fa fa-chevron-left" style="margin-right:7px;"></span>Back</a>
                </div>



            </fieldset>


            <fieldset style="text-align: center !important;" id="step3">


                    <h2 class="aligncenter title">Excellent Choice! <strong style="color:red;">You're almost done.</strong></h2>

                    <div class="margin20"></div>
                    <p>
                      Thanks for confirming. Click <strong style="color:red;">"NEXT"</strong> to checkout
                    </p>

                    <div class="container aligncenter" style="width:100%;">
                        <?php

                        //$result = file_get_contents('http://' . API_HOST . '/trader');
                        //$traders = json_decode($result,true);
                        //aasort($traders['data']['traders],'user_name');
                        foreach($traders['data']['traders'] as $item)
                        {
                            ?>

                            <div class="trader-container-signup"  data-user-name="<? print $item['user_name'];?>">
                                    <span class="one-half column  trader-stats">
                                       <div class="trader-name"><h2 class="trader-title"><? print $item['full_name']?></h2></div>
                                       <div class="growth-30day">Total growth: <strong class="green"><? print $item['total_growth'];?>%</strong></div>

                                       <div class="growth-30day">Growth in the last 30 days: <strong class="green"><? print number_format($item['30day_growth'],2);?>%</strong></div>
                                       <div class="maxdd">Max Drawdown: <strong class="red">-<? print $item['max_drawdown'];?>%</strong></div>
                                       <div class="growth-avg">Average monthly growth: <strong class="green"><? print number_format($item['avg_monthly_growth'],2);?>%</strong></div>
                                       <div class="running-weeks">Account age:<strong><? print $item['account_age'];?></strong></div>
                                       <div class="running-weeks"><span class="fa fa-users"></span>  Followers: <strong><? print ($item['followers']>0)?$item['followers']:'NEW';?></strong></div>

                                    </span>

                                    <span class="one-fourth column trader-chart" style="float:right;">
                                        <div>Performance</div>
                                       <!--<img src="/images/chart.png" width="215" height="125"/>-->
                                        <div class="chart-container-traders">
                                            <canvas id="trader_chart_large-<? print $item['id'] . '3'?>"  class="alignright" width="300" height="200"></canvas>
                                        </div>
                                        <script>
                                            growth_small(<? print $item['id']; ?>,'trader_chart_large-<? print $item['id'] . '3'; ?>','<? print API_HOST;?>','small');
                                        </script>
                                    </span>
                            </div>

                            <?
                        }
                        ?>
                    </div>
                <div class="aligncenter" style="color:#c0c0c0;">Note: Signal Systems can be changed anytime after signup.</div>


                <a id="fx-signup" class="button large" id="" href="" target="_blank">Next</a>

                    <div class="margin10"></div>



                    <div class="margin10"></div>
                    <a class="button large previous alignright" href="/signup/#2"><span class="fa fa-chevron-left" style="margin-right:7px;"></span>Back</a>
            </fieldset>




        </div>
    </div>
    </div>
</section>
