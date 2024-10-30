<?php

function jobs_search_widget() {
    global $wpdb;
    $setting = $wpdb->get_row(@$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "jobs_settings",""));
    $feed_url = $setting->http_url; //'https://jobs.chameleoni.com/api/PostXML/PostXml.aspx';
    $AuthKey = $setting->authKey;
    $AuthPassword = $setting->authPassword;
    $APIKey = $setting->aPIKey;
    $UserName = $setting->userName;
    $Thank_you_page = get_option("thank_you_page");
    $feed_location = $setting->feed_location;
    $feed_type = $setting->feed_type;
    $feed_salary = $setting->feed_salary;
    $feed_summary = $setting->feed_summary;
    $page_size = $setting->number_of_jobsper_Page;
    $summary_characters = $setting->summary_characters;
    $jobs_results_page_url = (get_option("jobs_search_url") != false) ? get_option("jobs_search_url") : '';
    $t1tag = 'Web Location';
    $request = '<ChameleonIAPI>
							<Method>TagListT2ForT1</Method>
							<APIKey>' . $APIKey . '</APIKey>
							<UserName>' . $UserName . '</UserName>
							<Filter>
								<Param Name="T1" Value="' . $t1tag . '" />
							</Filter>
						</ChameleonIAPI>';
    $encoded = 'Xml=' . $request . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
    $ch = curl_init($feed_url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    $result = str_replace('utf-16', 'utf-8', $result);
    $xml = simplexml_load_string($result);
    $json = json_encode($xml);
    $array = json_decode($json, TRUE);
    $job_taglist1 = array();
    if ($array['Status'] == 'Pass') {
        $job_taglist1 = @$array['TagListT2ForT1']['Tag'];
    }

    $t2tag = 'Web Expertise';
    /* Get T2 tags from T1 tag */
    $request = '<ChameleonIAPI>
							<Method>TagListT2ForT1</Method>
							<APIKey>' . $APIKey . '</APIKey>
							<UserName>' . $UserName . '</UserName>
							<Filter>
								<Param Name="T1" Value="' . $t2tag . '" />
							</Filter>
						</ChameleonIAPI>';
    $encoded = 'Xml=' . $request . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
    $ch = curl_init($feed_url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    $result = str_replace('utf-16', 'utf-8', $result);
    $xml = simplexml_load_string($result);
    $json = json_encode($xml);
    $array = json_decode($json, TRUE);
    $job_taglist2 = array();
    if ($array['Status'] == 'Pass') {
        $job_taglist2 = @$array['TagListT2ForT1']['Tag'];
    }
    ?>
    <div class="chameleoni_listing">
        <div class="search_form">
         
            <form method="get" action="<?= $jobs_results_page_url ?>" name="frmsearch">
                <input type="hidden" name="do" value="1"/>
                
                
                <div class="search-control-all">
                <div class="search-control-left">
                     <label class="searchlabel" for="location">Location </label>
                    </div>  
                      <div class="search-control-right">
                        <select id="t1tag" name="t1tag">
                                    <option value="">Select</option>
                                    <?php foreach ($job_taglist1 as $value) { ?>
                                        <option value="<?= $value['Tag'] ?>" <?= isset($_REQUEST['t1tag']) && $value['Tag'] == $_REQUEST['t1tag'] ? 'selected="selected"' : '' ?>><?= $value['Tag'] ?></option>
                                    <?php }
                                    ?>
                                </select>
                    </div>  
                </div>
                
                
                  <div class="search-control-all">
                <div class="search-control-left">
                    <label class="searchlabel" for="tag">Sector</label>
                      </div>
                           <div class="search-control-right">
                                  <select id="t2tag" name="t2tag">
                                    <option value="">Select</option>
                                    <?php
                                    foreach ($job_taglist2 as $value) {
                                        ?>
                                        <option value="<?= $value['TagId'] ?>" <?= isset($_REQUEST['t1tag']) && $value['TagId'] == $_REQUEST['t2tag'] ? 'selected="selected"' : '' ?>><?= $value['Tag'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                      </div>
                </div>
                
                 <div class="search-control-all">
                <div class="search-control-left">
                      <label class="searchlabel" for="tag">Keyword search</label>
                     </div>
                     
                     
                     <div class="search-control-right">
                 
                                        <input id="keyword" class="search_form_Keywordsearch" value="<?= $_REQUEST['keyword'] ?>" name="keyword" type="text" />                              

                     </div>
                     
                     
                </div>
                
                

                <?php
                                $filter = (@$_REQUEST['filter']) ? @$_REQUEST['filter'] : array();
                                $permanent = true;
                                $contract = true;
                                $temporary = true;
                                $self_employed = true;
                                
                                if (isset($_REQUEST["do"])) {
                                    if (isset($filter['permanent'])) {
                                        $permanent = true;
                                    } else {
                                        $permanent = false;
                                    }

                                    if (isset($filter['contract'])) {
                                        $contract = true;
                                    } else {
                                        $contract = false;
                                    }
                                    if (isset($filter['temporary'])) {
                                        $temporary = true;
                                    } else {
                                        $temporary = false;
                                    }
                                    if (isset($filter['self_employed'])) {
                                        $self_employed = true;
                                    } else {
                                        $self_employed = false;
                                    }
                                }
                                ?>
                                
                        
                                




			<!--align-->
			<div class="search-control-all">
				<div class="search-control-left">
					<label class="searchlabel" for="tag"> Job type</label>
				</div>
				<div class="search-control-right">
					<div class="row-cheackboxs">
						<div class="cheackbox-left">
                                       Permanent  <input class="tickbox_search" type="checkbox" <?= $permanent == true ? "checked" : "" ?> name="filter[permanent]" id="permanent" value="1">
                                    </div>
						</div>
					</div>
			</div>
			<div class="search-control-all">
					<div class="search-control-left">
						<label class="searchlabel" for="tag"> &nbsp;</label>
					</div>
					<div class="search-control-right">
						<div class="cheackbox-left">
                                        Contract  <input class="tickbox_search" type="checkbox" <?= $contract == true ? "checked" : "" ?> name="filter[contract]" value="1" id="contract">
                                    </div>
						</div>
			</div>
			<div class="search-control-all">
						<div class="search-control-left">
							<label class="searchlabel" for="tag"> &nbsp;</label>
						</div>
						<div class="search-control-right">
							<div class="cheackbox-left">
                                      Temporary   <input class="tickbox_search" type="checkbox" <?= $temporary == true ? "checked" : "" ?> name="filter[temporary]" id="temporary" value="1"> 
									</div>
							</div>
			</div>
			<div class="search-control-all">
							<div class="search-control-left">
								<label class="searchlabel" for="tag"> &nbsp;</label>
							</div>
							<div class="search-control-right">
								<div class="cheackbox-right">
                                      Self Employed <input class="tickbox_search" type="checkbox" <?= $self_employed == true ? "checked" : "" ?> style="margin-left: 3px;" name="filter[self_employed]" id="self_employed" value="1">
                                </div>
							</div>
			</div>
			<!--align-->
            
            




                                
                 <div class="search-control-all">
                   
                        <div class="search-control-all">
                                <div class="search-control-left">
                                    <input type="hidden" name="task" value="search" />
                                    <input type="submit" class="jobapply" value="Search" name="submit">
                                </div>
                        </div>
                     
                </div>
                
             <br />
                 <br />
            </form>
        </div>
    </div>
    <?php
}





/* wa-20190310 commented the following function & created another one below it*/
/*

function jobs_do_chameloni_search() 
{
	
	
	
    if (isset($_GET["task"])) 
	{
		
	
		
        if ($_GET["task"] == "search") 
		{
			
			
			
            global $wpdb, $post;
            $setting = $wpdb->get_row(@$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "jobs_settings",""));
            $feed_url = $setting->http_url; //'https://jobs.chameleoni.com/api/PostXML/PostXml.aspx';
            $AuthKey = $setting->authKey;
            $AuthPassword = $setting->authPassword;
            $APIKey = $setting->aPIKey;
            $UserName = $setting->userName;
            $Thank_you_page = get_option("thank_you_page");
            $feed_location = $setting->feed_location;
            $feed_type = $setting->feed_type;
            $feed_salary = $setting->feed_salary;
            $feed_summary = $setting->feed_summary;
            $page_size = $setting->number_of_jobsper_Page;
            $summary_characters = $setting->summary_characters;
            $post = @$_REQUEST['post'];
            $PageNo = (@$_REQUEST['PageNo']) ? @$_REQUEST['PageNo'] : 1;
            $_SESSION['PageNo'] = $PageNo;
            $filter = (@$_REQUEST['filter']) ? @$_REQUEST['filter'] : array();
			$Keywords = (@$_REQUEST['keyword']) ? @$_REQUEST['keyword'] : '';


            $job_type_arr = array("Permanent" => "Permanent", "Contract" => "Contract", "Temporary" => "Temporary", "Self_Employed" => "Self Employed");
            if (isset($_REQUEST["do"])) {
                                    if (!isset($filter['permanent'])) {
                                        unset($job_type_arr["Permanent"]);
                                    } 

                                    if (!isset($filter['contract'])) {
                                        unset($job_type_arr["Contract"]);
                                    } 
                                    if (!isset($filter['temporary'])) {
                                        unset($job_type_arr["Temporary"]);
                                    } 
                                    if (!isset($filter['self_employed'])) {
                                        unset($job_type_arr["Self_Employed"]);
                                    } 
                                }
			
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            $job_type_str = implode(",", $job_type_arr);
            $page_rows = $page_size;
            $t1tag = $t2tag = '';
            $t1tag = @$_REQUEST['t1tag'] ? @$_REQUEST['t1tag'] : '';
            $t2tag = @$_REQUEST['t2tag'] ? @$_REQUEST['t2tag'] : '';
            $taglistpass = $t1tag . ',' . $t2tag;
            $taglistpass = trim($taglistpass, ',');
            $request = '<ChameleonIAPI>
										<Method>SearchVacancies2</Method>
										<APIKey>' . $APIKey . '</APIKey>
										<UserName>' . $UserName . '</UserName>
                                                                                <OrderBy>JobTitle ASC</OrderBy>
										<Filter>
                           <Param Name="VacancyId" Operator="=" Value="" />

                           <Param Name="AgencyId" Operator="=" Value="" />

                           <Param Name="JobType" Operator="CONTAINS" Value="' . $job_type_str . '" />

                           <Param Name="DatePosted" Operator="=" Value="" />

                           <Param Name="DateClosed" Operator="=" Value="" />

                           <Param Name="StartDate" Operator="=" Value="" />

                           <Param Name="EndDate" Operator="=" Value="" />

                           <Param Name="TagSearch" Operator="=" Value="' . $t2tag . '" />

                           <Param Name="RatesPer" Operator="=" Value="" />

                           <Param Name="Currency" Operator="=" Value="" />

                           <Param Name="RateFrom" Operator="=" Value="" />

                           <Param Name="RateTo" Operator="=" Value="" />

                           <Param Name="Benefits" Operator="=" Value="" />

                           <Param Name="LocationTag" Operator="=" Value="' . $t1tag . '" />

                           <Param Name="Location" Operator="=" Value="" />

                           <Param Name="Towns" Operator="=" Value="" />

                           <Param Name="Country" Operator="=" Value="" />

                           <Param Name="TownId" Operator="=" Value="" />

                           <Param Name="Description" Operator="=" Value="" />

                           <Param Name="Bonus" Operator="=" Value=""/>

                           <Param Name="ShowOnHomePage" Operator="=" Value="" />

                           <Param Name="HotJob" Operator="=" Value="" />

                           <Param Name="JobPostCode" Operator="=" Value="" />

                           <Param Name="ConsultantName" Operator="=" Value="" />

                           <Param Name="ConsultantTelNo" Operator="=" Value="" />

                           <Param Name="ConsultantEmail" Operator="=" Value="" />
                           <Param Name="ClientIP" Operator="=" Value="' . $ip . '" />
                           <Param Name="PageNo" Value="' . $PageNo . '" />
                           <Param Name="PageSize" Value="' . $page_size . '" />

										</Filter>
										
										<Keywords> '. $Keywords . '</Keywords>
									</ChameleonIAPI>';
            $encoded = 'Xml=' . $request . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
            $ch = curl_init($feed_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            $result = curl_exec($ch);

            curl_close($ch);
            $result = str_replace('utf-16', 'utf-8', $result);

            $xml = simplexml_load_string($result);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            $jobs_vac = array();

            if (isset($array['Vacancies']) && count($array['Vacancies']) > 0) {
                if (isset($array['Vacancies']['Vacancy'][0])) {
                    $jobs_vac = @$array['Vacancies']['Vacancy'];
                    //@$total = count($array['Vacancies']['Vacancy']);
                    @$total = @$array['Vacancies']['Vacancy'][0]['TotalCount'];
                } else {
                    $temp_arr = $array['Vacancies']['Vacancy'];
                    unset($array['Vacancies']['Vacancy']);
                    //foreach($temp_arr as $k => $v)
                    //{
                    $array['Vacancies']['Vacancy'][] = $temp_arr;
                    //}
                    $jobs_vac = $array['Vacancies']['Vacancy'];
                   @$total = @$array['Vacancies']['Vacancy'][0]['TotalCount'];
                }
            }
            $_SESSION['currentpage'] = $_SESSION['PageNo'];
            $pageid = $post->ID;
            $det = JOBINFO_URL . '/jobs_details.php';
            if (!empty($jobs_vac)) {
                ?>
                <h2 class="title">Search Results</h2>
                <?php foreach ($jobs_vac as $job) { ?>
                    <div class="job">

                        <p>
                           <span class="type">
                                <label><b>Job Title:</b></label>
                                        <?= esc_html($job['JobTitle']) ?> 
                                <?php if ($feed_location == '1') { ?>
                                    <p><span class="location"><label><b>Location:</b></label>
                                            <?= esc_html(is_array($job['LocationTag']) ? implode(', ', $job['LocationTag']) : $job['LocationTag']); ?>
                                        </span><br>
                                    </p>
                                <?php } ?>
                            </span>
                        </p>

                        <?php if ($feed_type == '1') { ?>
                            <p><span class="type">
                                    <label><b>Type:</b></label><?= esc_html(($job['JobType'])) ?>
                                </span>
                            </p>
                        <?php } ?>

                        <?php if ($feed_salary == '1') { ?>
                            <p><span class="type"><label><b>Salary:</b></label><?= esc_html(($job['Pay'])) ?></span></p>
                        <?php } ?>

                        <p><span class="type"><label><b>Date Posted:</b></label><?= esc_html(date("d/m/Y", strtotime($job['DatePosted']))) ?></span></p>
                        <p><span class="type"><label><b>Close Date:</b></label>
                                <?= esc_html(date("d/m/Y", strtotime($job['DateClosed']))); ?>
                            </span></p>
                        <?php if ($feed_summary == '1' && $job['Description'] != '') { ?>

                            <p style="display:none"><span class="summary"><label><b>Summary:</b></label>
                                   <?= (($job['Description']) ? substr(nl2br($job['Description']), 0, $summary_characters) . ((strlen($job['Description']) > $summary_characters) ? '.....' : '..') : '..') ?>
                                </span></p>
                        <?php } ?>
                        <p><span class="consultantname"><label><b>Consultant Name:</b></label><?= esc_html($job['ConsultantName']); ?></span></p>
                        <p> <span class="consultantemail"><label><b>Consultant Email:</b></label><a href="mailto:<?= esc_html($job['ConsultantEmail']) ?>"><?= $job['ConsultantEmail'] ?></a></span></p>
                        <?php if ($feed_summary == '1' && $job['Description'] != '') { ?>
                         <p><span class="summary"><label><b>Summary:</b></label>
                                <?php if(!empty($job['Description'])) {echo $job['Description'];} ?>
                                </span></p>
                          <?php } ?>
                        <?php
                        unset($_REQUEST['submit']);
                        unset($_REQUEST['task']);
                        $url_params = http_build_query($_REQUEST);
                        ?>
                        <span><a class="view_morebtn" href="?task=jobdetails&jobid=<?= esc_html($job['VacancyId']) ?>&<?= $url_params ?>">View More</a> </span>

                    </div>
                    <?php
                }
                $additional_params = '';
                if (!empty($_GET)) {
                    $p = array();
                    foreach ($_GET as $k => $v) {
                        if ($k == "PageNo")
                            continue;
                        $p[$k] = $v;
                    }
                    $additional_params .= "&" . http_build_query($p);
                }
                if (!(isset($pagenum))) {
                    $pagenum = 1;
                }
                $page_rows = $page_size;
                $last = ceil($total / $page_rows);
                //this makes sure the page number isn't below one, or more than our maximum pages 
                if ($pagenum < 1) {
                    $pagenum = 1;
                } elseif ($pagenum > $last) {
                    $pagenum = $last;
                }
                $pagenum;
                if ($total > $page_size) {
                    ?>
                    <ul class="pageul">
                        <?php if ($PageNo != 1) { ?>
                            <li><b><a href='?PageNo=<?= ($PageNo - 1) . $additional_params ?>'>Previous << </a></b> </li>
                        <?php } ?>
                        <?php for ($t = 1; $t <= $last; $t++) { ?>
                            <li><b><a href='?PageNo=<?= ($t) . $additional_params ?>'> <?= $t ?> </a></b> </li>
                        <?php } ?>
                        <?php if ($last != $PageNo) { ?>
                            <li><b><a href='?PageNo=<?= ($PageNo + 1) . $additional_params ?>'>Next >></a></b> </li>
                        <?php } ?>
                    </ul>
                    <input type="hidden" name="PageNo" id="PageNo" value="1" />
                    <?php
                }
            } else {
                ?>
                <h3>No listing found</h3>
                <?php
            }
            ?>
            <script type="text/javascript">
                function nextprevious(ch) {
                    if (ch == "p")
                    {
                        document.getElementById("PageNo").value = <?= $PageNo - 1 ?>
                    } else if (ch == "n") {
                        document.getElementById("PageNo").value = <?= $PageNo + 1 ?>
                    }
                    document.frmpagination.submit();
                }
                function mysubmit(PageNo)
                {
                    document.getElementById("PageNo").value = PageNo;
					
                    document.frmpagination.submit();
                }
            </script>
            <?php
        } elseif ($_GET["task"] == "jobdetails" && isset($_GET["jobid"])) {
            echo call_user_func("show_job_details");
        } elseif ($_GET["task"] == "login_apply" && isset($_GET["jobid"])) {
            echo call_user_func("login_apply");
        } elseif ($_GET["task"] == "register_apply" && isset($_GET["jobid"])) {
            echo call_user_func("register_apply");
        }
    }
}

*/










/* wa-20190310 */
function jobs_do_chameloni_search() 
{
	
	
	
   // if (isset($_GET["task"])) 
	//{
		
		
		
        //if ($_GET["task"] == "search") 
		if (is_null($_GET["task"]) || ($_GET["task"] == "search")) 
		{
			
		
			
            global $wpdb, $post;
            $setting = $wpdb->get_row(@$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "jobs_settings",""));
            $feed_url = $setting->http_url; //'https://jobs.chameleoni.com/api/PostXML/PostXml.aspx';
            $AuthKey = $setting->authKey;
            $AuthPassword = $setting->authPassword;
            $APIKey = $setting->aPIKey;
            $UserName = $setting->userName;
            $Thank_you_page = get_option("thank_you_page");
            $feed_location = $setting->feed_location;
            $feed_type = $setting->feed_type;
            $feed_salary = $setting->feed_salary;
            $feed_summary = $setting->feed_summary;
            $page_size = $setting->number_of_jobsper_Page;
            $summary_characters = $setting->summary_characters;
            $post = @$_REQUEST['post'];
            $PageNo = (@$_REQUEST['PageNo']) ? @$_REQUEST['PageNo'] : 1;
            $_SESSION['PageNo'] = $PageNo;
            $filter = (@$_REQUEST['filter']) ? @$_REQUEST['filter'] : array();
			$Keywords = (@$_REQUEST['keyword']) ? @$_REQUEST['keyword'] : '';


            $job_type_arr = array("Permanent" => "Permanent", "Contract" => "Contract", "Temporary" => "Temporary", "Self_Employed" => "Self Employed");
            if (isset($_REQUEST["do"])) {
                                    if (!isset($filter['permanent'])) {
                                        unset($job_type_arr["Permanent"]);
                                    } 

                                    if (!isset($filter['contract'])) {
                                        unset($job_type_arr["Contract"]);
                                    } 
                                    if (!isset($filter['temporary'])) {
                                        unset($job_type_arr["Temporary"]);
                                    } 
                                    if (!isset($filter['self_employed'])) {
                                        unset($job_type_arr["Self_Employed"]);
                                    } 
                                }
			
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            $job_type_str = implode(",", $job_type_arr);
            $page_rows = $page_size;
            $t1tag = $t2tag = '';
            $t1tag = @$_REQUEST['t1tag'] ? @$_REQUEST['t1tag'] : '';
            $t2tag = @$_REQUEST['t2tag'] ? @$_REQUEST['t2tag'] : '';
            $taglistpass = $t1tag . ',' . $t2tag;
            $taglistpass = trim($taglistpass, ',');
            $request = '<ChameleonIAPI>
										<Method>SearchVacancies2</Method>
										<APIKey>' . $APIKey . '</APIKey>
										<UserName>' . $UserName . '</UserName>
                                                                                <OrderBy>JobTitle ASC</OrderBy>
										<Filter>
                           <Param Name="VacancyId" Operator="=" Value="" />

                           <Param Name="AgencyId" Operator="=" Value="" />

                           <Param Name="JobType" Operator="CONTAINS" Value="' . $job_type_str . '" />

                           <Param Name="DatePosted" Operator="=" Value="" />

                           <Param Name="DateClosed" Operator="=" Value="" />

                           <Param Name="StartDate" Operator="=" Value="" />

                           <Param Name="EndDate" Operator="=" Value="" />

                           <Param Name="TagSearch" Operator="=" Value="' . $t2tag . '" />

                           <Param Name="RatesPer" Operator="=" Value="" />

                           <Param Name="Currency" Operator="=" Value="" />

                           <Param Name="RateFrom" Operator="=" Value="" />

                           <Param Name="RateTo" Operator="=" Value="" />

                           <Param Name="Benefits" Operator="=" Value="" />

                           <Param Name="LocationTag" Operator="=" Value="' . $t1tag . '" />

                           <Param Name="Location" Operator="=" Value="" />

                           <Param Name="Towns" Operator="=" Value="" />

                           <Param Name="Country" Operator="=" Value="" />

                           <Param Name="TownId" Operator="=" Value="" />

                           <Param Name="Description" Operator="=" Value="" />

                           <Param Name="Bonus" Operator="=" Value=""/>

                           <Param Name="ShowOnHomePage" Operator="=" Value="" />

                           <Param Name="HotJob" Operator="=" Value="" />

                           <Param Name="JobPostCode" Operator="=" Value="" />

                           <Param Name="ConsultantName" Operator="=" Value="" />

                           <Param Name="ConsultantTelNo" Operator="=" Value="" />

                           <Param Name="ConsultantEmail" Operator="=" Value="" />
                           <Param Name="ClientIP" Operator="=" Value="' . $ip . '" />
                           <Param Name="PageNo" Value="' . $PageNo . '" />
                           <Param Name="PageSize" Value="' . $page_size . '" />

										</Filter>
										
										<Keywords> '. $Keywords . '</Keywords>
									</ChameleonIAPI>';
            $encoded = 'Xml=' . $request . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
            $ch = curl_init($feed_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            $result = curl_exec($ch);

            curl_close($ch);
            $result = str_replace('utf-16', 'utf-8', $result);

            $xml = simplexml_load_string($result);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            $jobs_vac = array();

            if (isset($array['Vacancies']) && count($array['Vacancies']) > 0) {
                if (isset($array['Vacancies']['Vacancy'][0])) {
                    $jobs_vac = @$array['Vacancies']['Vacancy'];
                    //@$total = count($array['Vacancies']['Vacancy']);
                    @$total = @$array['Vacancies']['Vacancy'][0]['TotalCount'];
                } else {
                    $temp_arr = $array['Vacancies']['Vacancy'];
                    unset($array['Vacancies']['Vacancy']);
                    //foreach($temp_arr as $k => $v)
                    //{
                    $array['Vacancies']['Vacancy'][] = $temp_arr;
                    //}
                    $jobs_vac = $array['Vacancies']['Vacancy'];
                   @$total = @$array['Vacancies']['Vacancy'][0]['TotalCount'];
                }
            }
            $_SESSION['currentpage'] = $_SESSION['PageNo'];
            $pageid = $post->ID;
            $det = JOBINFO_URL . '/jobs_details.php';
            if (!empty($jobs_vac)) {
                ?>
                <h2 class="title">Search Results</h2>
                <?php foreach ($jobs_vac as $job) { ?>
                    <div class="job">

                        <p>
                           <span class="type">
                                <label><b>Job Title:</b></label>
                                        <?= esc_html($job['JobTitle']) ?> 
                                <?php if ($feed_location == '1') { ?>
                                    <p><span class="location"><label><b>Location:</b></label>
                                            <?= esc_html(is_array($job['LocationTag']) ? implode(', ', $job['LocationTag']) : $job['LocationTag']); ?>
                                        </span><br>
                                    </p>
                                <?php } ?>
                            </span>
                        </p>

                        <?php if ($feed_type == '1') { ?>
                            <p><span class="type">
                                    <label><b>Type:</b></label><?= esc_html(($job['JobType'])) ?>
                                </span>
                            </p>
                        <?php } ?>

                        <?php if ($feed_salary == '1') { ?>
                            <p><span class="type"><label><b>Salary:</b></label><?= esc_html(($job['Pay'])) ?></span></p>
                        <?php } ?>

                        <p><span class="type"><label><b>Date Posted:</b></label><?= esc_html(date("d/m/Y", strtotime($job['DatePosted']))) ?></span></p>
                        <p><span class="type"><label><b>Close Date:</b></label>
                                <?= esc_html(date("d/m/Y", strtotime($job['DateClosed']))); ?>
                            </span></p>
                        <?php if ($feed_summary == '1' && $job['Description'] != '') { ?>

                            <p style="display:none"><span class="summary"><label><b>Summary:</b></label>
                                   <?= (($job['Description']) ? substr(nl2br($job['Description']), 0, $summary_characters) . ((strlen($job['Description']) > $summary_characters) ? '.....' : '..') : '..') ?>
                                </span></p>
                        <?php } ?>
                        <p><span class="consultantname"><label><b>Consultant Name:</b></label><?= esc_html($job['ConsultantName']); ?></span></p>
                        <p> <span class="consultantemail"><label><b>Consultant Email:</b></label><a href="mailto:<?= esc_html($job['ConsultantEmail']) ?>"><?= $job['ConsultantEmail'] ?></a></span></p>
                        <?php if ($feed_summary == '1' && $job['Description'] != '') { ?>
                         <p>
                                <span class="summary"><label><b>Summary:</b></label>
                            
								<table >
								
									
									<tr>

										 <td>
											<div  style="font-size: var(--neve-font-size-body, var(--wp--preset--font-size--normal));font-weight: var(--neve-font-weight-body, 400);    line-height: var(--neve-font-line-height-body, 1.7);color: var(--wp--preset--color--ti-fg);">

                                    <?php if(!empty($job['Description'])) 
                                    {
                                     //   echo $job['Description'];} 
                                     //echo nl2br($job['Description']);
                                     echo (($job['Description']) ? substr(nl2br($job['Description']), 0, $summary_characters) . ((strlen($job['Description']) > $summary_characters) ? '.....' : '..') : '..');
                                    } 
                                    ?>

                                    </div>
									 </td>
										
									</tr>
								</table>

                            
                                </span>


                            </p>
                          <?php } ?>
                        <?php
                        unset($_REQUEST['submit']);
                        unset($_REQUEST['task']);
                        $url_params = http_build_query($_REQUEST);
                        ?>
                        <span><a class="view_morebtn" href="?task=jobdetails&jobid=<?= esc_html($job['VacancyId']) ?>&<?= $url_params ?>">View More</a> </span>

                    </div>
                    <?php
                }
                $additional_params = '';
                if (!empty($_GET)) {
                    $p = array();
                    foreach ($_GET as $k => $v) {
                        if ($k == "PageNo")
                            continue;
                        $p[$k] = $v;
                    }
                    $additional_params .= "&" . http_build_query($p);
                }
                if (!(isset($pagenum))) {
                    $pagenum = 1;
                }
                $page_rows = $page_size;
                $last = ceil($total / $page_rows);
                //this makes sure the page number isn't below one, or more than our maximum pages 
                if ($pagenum < 1) {
                    $pagenum = 1;
                } elseif ($pagenum > $last) {
                    $pagenum = $last;
                }
                $pagenum;
                if ($total > $page_size) {
                    ?>
                    <ul class="pageul">
                        <?php if ($PageNo != 1) { ?>
                            <li><b><a href='?PageNo=<?= ($PageNo - 1) . $additional_params ?>'>Previous << </a></b> </li>
                        <?php } ?>
                        <?php for ($t = 1; $t <= $last; $t++) { ?>
                            <li><b><a href='?PageNo=<?= ($t) . $additional_params ?>'> <?= $t ?> </a></b> </li>
                        <?php } ?>
                        <?php if ($last != $PageNo) { ?>
                            <li><b><a href='?PageNo=<?= ($PageNo + 1) . $additional_params ?>'>Next >></a></b> </li>
                        <?php } ?>
                    </ul>
                    <input type="hidden" name="PageNo" id="PageNo" value="1" />
                    <?php
                }
            } else {
                ?>
                <h3>No listing found</h3>
                <?php
            }
            ?>
            <script type="text/javascript">
                function nextprevious(ch) {
                    if (ch == "p")
                    {
                        document.getElementById("PageNo").value = <?= $PageNo - 1 ?>
                    } else if (ch == "n") {
                        document.getElementById("PageNo").value = <?= $PageNo + 1 ?>
                    }
                    document.frmpagination.submit();
                }
                function mysubmit(PageNo)
                {
                    document.getElementById("PageNo").value = PageNo;
					
                    document.frmpagination.submit();
                }
            </script>
            <?php
			
		
        } 
		elseif ($_GET["task"] == "jobdetails" && isset($_GET["jobid"])) {
            echo call_user_func("show_job_details");
        } elseif ($_GET["task"] == "login_apply" && isset($_GET["jobid"])) {
            echo call_user_func("login_apply");
        } elseif ($_GET["task"] == "register_apply" && isset($_GET["jobid"])) {
            echo call_user_func("register_apply");
        }
		
 //   }
}










function test_shortcode() 
{
				?>
                <h3>Test shortcode</h3>
				<?php
}





function show_job_details() {
    global $post;
    $pageid = $post->ID;
    global $wpdb;

    $setting = $wpdb->get_row(@$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "jobs_settings",""));
    $feed_url = $setting->http_url; //'http://jobs.chameleoni.com/xmlfeed.aspx';
    $AuthKey = $setting->authKey;
    $AuthPassword = $setting->authPassword;
    $APIKey = $setting->aPIKey;
    $UserName = $setting->userName;
    $Thank_you_page = get_option("thank_you_page");
    $feed_location = $setting->feed_location;
    $feed_type = $setting->feed_type;
    $feed_salary = $setting->feed_salary;
    $feed_summary = $setting->feed_summary;
    $jobId = $_REQUEST['jobid'];


    if (isset($_GET['action']) && $_GET['action'] == "apply") {
        $contact_id = $_SESSION['ContactId'];

        if ($contact_id) {

            // Apply to the job
            $job_name = $_REQUEST['job_title'];
            $job_id = $_REQUEST['jobid'];
            $job_ref = $_REQUEST['job_ref'];

            $res_job_apply = '<ChameleonIAPI><Method>CandidateApplication</Method>
				  <APIKey>' . $APIKey . '</APIKey>
				<UserName>' . $UserName . '</UserName>
				<InputData>
                                <Input Name="RequirementId" Value="' . $job_id . '"/>
<Input Name="CandidateId" Value="' . $contact_id . '"/>
<Input Name="WebApplicationTaskTypeId" Value="119"/>
<Input Name="ApplicationText" Value="' . $job_name . '"/>
<Input Name="MailName" Value="Email - Application"/>
<Input Name="SiteName" Value=""/>
				</InputData>
				</ChameleonIAPI>';

            $encoded = 'Xml=' . $res_job_apply . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
            $ch = curl_init($feed_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            $result = curl_exec($ch);

            curl_close($ch);

            $result = str_replace('utf-16', 'utf-8', $result);
            $xml = simplexml_load_string($result);
            $json = json_encode($xml);
            $array_res = json_decode($json, TRUE);
            if ($array_res['Status'] == "Pass") {
                $_REQUEST['task'] = "search";
                unset($_REQUEST['jobid']);
                unset($_REQUEST['action']);
                unset($_REQUEST['job_title']);
                unset($_REQUEST['job_ref']);
                $url_params = http_build_query($_REQUEST);
                echo "<div class='alert alert-success'>Application for position reference " . $job_ref . "
Thank you, we have received your application for this position. One of our consultants will be in touch shortly.<a href='" . get_option("jobs_search_url") . "?" . $url_params . "'>Back to our live vacancies</a>.</div>
<style> #vacancy_details{ display: none; } </style>";
            }
        }
    }

    $request = '
						<ChameleonIAPI>
							<Method>SearchVacancies2</Method>
							<APIKey>' . $APIKey . '</APIKey>
							<UserName>' . $UserName . '</UserName>
							<Filter>
								<!-- Options Placeholder -->
                                                                <Param Name="VacancyId" Value="' . $jobId . '"  Operator="="/>
							</Filter>	
						</ChameleonIAPI>';
    $encoded = 'Xml=' . $request . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
    $ch = curl_init($feed_url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    $result = str_replace('utf-16', 'utf-8', $result);
    $xml = simplexml_load_string($result);
    $json = json_encode($xml);
    $array = json_decode($json, TRUE);
    $job_details = array();
    if (count($array['Vacancies']['Vacancy'])) {
        $job_details = @$array['Vacancies']['Vacancy'];
        //$session->set('recent_job',$this->job);
    }
    
$job_Description="";
$job_Benefits = "";
if(!empty($job_details['Description'])) {$job_Description= $job_details['Description'];}
if(!empty($job_details['Benefits'])) {$job_Benefits= $job_details['Benefits'];}


    $job_details_str = '<div id="vacancy_details">
					<p><span class="location"><label><b>Job Title:</b></label>' . esc_html($job_details['JobTitle']) . ' </span></p>
					<p><span class="location"><label><b>Location:</b></label>' . esc_html($job_details['LocationTag']) . '</span></p>
					<p><span class="type"><label><b>Job Reference:</b></label>' . esc_html($job_details['Reference']) . '</span></p>
					<p><span class="type"><label><b>Type:</b></label>' . $job_details['JobType'] . '</span></p>
					<p><span class="salary"><label><b>Salary: </b></label>' . esc_html(($job_details['Pay']));
    
    
    
    $job_details_str .= '</span></p>
					<p><span class="type"><label><b>Close Date:</b></label>' . esc_html(date("d/m/Y", strtotime($job_details['DateClosed']))) . '</span></p>
					<p>
                        <span class="summary"><div class="search-control-all"><label><b>Summary: </b></label>
                        <div class="right-content-jobs">

                                    <table >
                                            
                                                
                                    <tr>

                                        <td>
                                            <div  style="font-size: var(--neve-font-size-body, var(--wp--preset--font-size--normal));font-weight: var(--neve-font-weight-body, 400);    line-height: var(--neve-font-line-height-body, 1.7);color: var(--wp--preset--color--ti-fg);">

                                            ' . nl2br($job_Description) .'

                                            </div>
                                    </td>
                                        
                                    </tr>
                                </table>                        
                        </div></div></span>
                    </p>
					<p><span class="type"><label><b>Benefits:</b></label>' . $job_Benefits . '</span></p>
					<p> <span class="consultantname"><label><b>Consultant Name:</b></label>' . esc_html($job_details['ConsultantName']) . '</span></p>
					<p><span class="consultantemail"><label><b>Consultant Email:</b></label><a href="mailto:' . $job_details['ConsultantEmail'] . '">' . esc_html($job_details['ConsultantEmail']) . '</a></span></p>
				 <span>
                                 <!--<a class="view_morebtn" href="index.php?page_id=' . $pageid . '&task=apply&jobid=' . $_REQUEST['jobid'] . '&job_title=' . esc_html($job_details['JobTitle']) . '">Apply</a>-->';

    if (isset($_SESSION['ContactId'])) {
        $res_job_apply = '<?xml version="1.0" encoding="utf-16" ?>
                                       <ChameleonIAPI>
                                           <Method>CheckAppliedStatus</Method>
                                           <APIKey>' . $APIKey . '</APIKey>
                                           <UserName>' . $UserName . '</UserName>
                                           <InputData>
                                                <Input Name="RequirementId" Value="' . $_REQUEST['jobid'] . '" />
                                                <Input Name="CandidateId" Value="' . $_SESSION['ContactId'] . '" />
                                           </InputData>
                                       </ChameleonIAPI>';

        $encoded = 'Xml=' . $res_job_apply . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
        $ch = curl_init($feed_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        $result = curl_exec($ch);


        curl_close($ch);

        $result = str_replace('utf-16', 'utf-8', $result);
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array_res2 = json_decode($json, TRUE);
        if ($array_res2['AppliedStatus'] == 1) {
            $btns = '<a class="view_morebtn" href="#" disabled>Already Applied</a>';
        } elseif ($array_res2['AppliedStatus'] == 0) {
            unset($_REQUEST['task']);
            unset($_REQUEST['jobid']);
            $url_params = http_build_query($_REQUEST);
            $btns = '<a class="view_morebtn" href="?task=jobdetails&action=apply&jobid=' . $jobId . '&job_title=' . esc_html($job_details['JobTitle']) . '&job_ref=' . $job_details['Reference'] . "&" . $url_params . '">Apply</a>';
        }
    } else {
        unset($_REQUEST['task']);
        unset($_REQUEST['jobid']);
        $url_params = http_build_query($_REQUEST);
        $btns = '<a class="view_morebtn" href="?task=login_apply&jobid=' . $jobId . '&job_title=' . esc_html($job_details['JobTitle']) . '&job_ref=' . $job_details['Reference'] . "&" . $url_params . '">Login & Apply </a>
                 <a class="view_morebtn" href="?task=register_apply&jobid=' . $jobId . '&job_title=' . esc_html($job_details['JobTitle']) . '&job_ref=' . $job_details['Reference'] . "&" . $url_params . '">Register & Apply</a>';
    }
    $_REQUEST['task'] = "search";
    unset($_REQUEST['jobid']);
    $url_params = http_build_query($_REQUEST);
    $job_details_str .= $btns . '</span>
				<a href="?' . $url_params . '" class="view_morebtn">Back to Results</a>
				</p>
				</div>';


    return $job_details_str;
}

function login_apply() {
    global $post;
    $pageid = $post->ID;
    global $wpdb;
    $setting = $wpdb->get_row(@$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "jobs_settings",""));
    $job_settings_str = '';
    if (isset($_POST['login_submit']) == "Submit") {
        global $wpdb;
        $setting = $wpdb->get_row(@$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "jobs_settings",""));
        $feed_url = $setting->http_url; //'http://jobs.chameleoni.com/xmlfeed.aspx';
        $AuthKey = $setting->authKey;
        $AuthPassword = $setting->authPassword;
        $APIKey = $setting->aPIKey;
        $UserName = $setting->userName;
        $Thank_you_page = get_option("thank_you_page");
        $feed_location = $setting->feed_location;
        $feed_type = $setting->feed_type;
        $feed_salary = $setting->feed_salary;
        $feed_summary = $setting->feed_summary;
        $page_size = $setting->number_of_jobsper_Page;




				//validate captcha
				if ( (!isset($_POST['g-recaptcha-response']))  )
				{
					echo "invalid captcha." ;
					return;
				}

				$captcha_response = sanitize_text_field($_POST['g-recaptcha-response']);
				$captcha_params = 'secret=6Ld-EroUAAAAALbixufOd8I7DieG01uVEMv-HmWl' . '&response=' . $captcha_response ;
				$captcha_request = curl_init("https://www.google.com/recaptcha/api/siteverify");

				curl_setopt($captcha_request, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($captcha_request, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($captcha_request, CURLOPT_POSTFIELDS, $captcha_params);
				curl_setopt($captcha_request, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($captcha_request, CURLOPT_VERBOSE, 0);

				$captcha_google_response = curl_exec($captcha_request);  
				
				$decoded_response = json_decode($captcha_google_response, true);
				$captcha_validation_check = $decoded_response["success"];

				if($captcha_validation_check != '1')
				{
					echo '<div class="row">Captcha validation failed.<div>
						  <div class="row">
							<a HREF="#"  onclick ="(function(){ window.location.href = window.location.href; })();return false;">Try again</a>
						  </div>
						' ;
					return;
				}
				//validate captcha

                





        $request_login = '
        <?xml version="1.0" encoding="utf-16" ?>
<ChameleonIAPI>
    <Method>CandidateLogin</Method>
    <APIKey>' . $APIKey . '</APIKey>
    <UserName>' . $UserName . '</UserName>
    <InputData>
            <Input Name="Email" Value="' . sanitize_email($_POST['email']) . '" />
            <Input Name="Password" Value="' . $_POST['password'] . '" />
    </InputData>
</ChameleonIAPI>';

        $encoded = 'Xml=' . $request_login . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
        $ch = curl_init($feed_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        $result = curl_exec($ch);

        curl_close($ch);

        //if (!session_id())
        //  session_start();

        $result = str_replace('utf-16', 'utf-8', $result);
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array_contactid = json_decode($json, TRUE);
        if (isset($array_contactid['ContactId']) && !empty($array_contactid['ContactId'])) {
            $_SESSION['ContactId'] = $array_contactid['ContactId'];

            // Apply to the job
            $job_name = $_GET['job_title'];
            $job_id = $_GET['jobid'];
            $job_ref = $_GET['job_ref'];

            $res_job_apply = '<ChameleonIAPI><Method>CandidateApplication</Method>
				  <APIKey>' . $APIKey . '</APIKey>
				<UserName>' . $UserName . '</UserName>
				<InputData>
                                <Input Name="RequirementId" Value="' . $job_id . '"/>
<Input Name="CandidateId" Value="' . $_SESSION['ContactId'] . '"/>
<Input Name="WebApplicationTaskTypeId" Value="119"/>
<Input Name="ApplicationText" Value="' . $job_name . '"/>
<Input Name="MailName" Value="Email - Application"/>
<Input Name="SiteName" Value=""/>
				</InputData>
				</ChameleonIAPI>';

            $encoded = 'Xml=' . $res_job_apply . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
            $ch = curl_init($feed_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            $result = curl_exec($ch);

            curl_close($ch);

            $result = str_replace('utf-16', 'utf-8', $result);
            $xml = simplexml_load_string($result);
            $json = json_encode($xml);
            $array_res = json_decode($json, TRUE);
            unset($_GET['jobid']);
            unset($_GET['job_title']);
            unset($_GET['job_ref']);
            $_GET['task'] = "search";
            $url_params = http_build_query($_GET);
            if ($array_res['AppliedStatus'] == 0) {
                echo "<div class='alert alert-success'>Thank you for your application for vacancy " . $job_ref . ". One of our consultants will be in touch shortly.
<a href='" . get_option("jobs_search_url") . "?" . $url_params . "'>Please click here to go back to our live vacancies.</a></div><style>  #apply_head{ display:none; } .front-end-edit{ display:none; }  </style>";
            } elseif ($array_res['AppliedStatus'] == 1) {
                echo "<div class='alert alert-danger'>You already applied</div>";
            }

//                    if ($setting->thank_you_page == 0) {
//
//
//                        $redpage = "index.php";
//                    } else {
//                        $redpage = "?page_id=$setting->thank_you_page";
//                    }
        } else {
            echo "<div class='alert alert-danger'>Error no such user!</div>";
        }
    }
    //$job_settings_str = '';
    $job_settings_str .= '<script type="application/javascript">
				function app_validate()
				{
					validation_string = new Array();
        if (document.getElementById("email").value == "")
        {
            document.getElementById("email").focus();
            validation_string.push("email");
        }else if (document.getElementById("email").value != "")
        {
            var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
            if (!document.getElementById("email").value.match(emailExp)) {
                document.getElementById("email").focus();
                validation_string.push("Valid Email Address");
            } 
        }
        if (document.getElementById("password").value == "")
        {
            document.getElementById("password").focus();
            validation_string.push("password");
        }
        if (validation_string != "") {
            alert("Please enter " + validation_string.join(", ").toString());
            return false;
        }
        return true;
				}
				</script>
                
                
                <script>
                var script_callback = function()
                {
                    setTimeout(function()
                            { 
                                grecaptcha.render("captcha_placeholder", 
                                {
                                    "sitekey" : "6Ld-EroUAAAAAK5zaBg3C2Qr7Gg7C0lSIS_AsBx-"
                                }
                                );                                
                            }, 
                            250
                        );
                }			
			</script>
			
			<script src="https://www.google.com/recaptcha/api.js?onload=script_callback&render=explicit" async defer></script>

                
                
                ';

    $job_settings_str .= '<div class="application-edit front-end-edit">
					<h3 id="apply_head">Apply for position: ' . $_REQUEST['job_title'] . '</h3>
					<form id="form-application" action="" method="post" class="form-validate" enctype="multipart/form-data" autocomplete="off">        		
						<input  type="hidden" class="required"  id="job_id" name="job_id"   value="' . $_REQUEST['jobid'] . '">
						<input type="hidden" class="required" value="' . $_REQUEST['job_title'] . '" id="job_title" name="job_title">
						<table border="0">
							<tr>
								<td>Email </td>
								<td><input type="email" name="email" id="email" autocomplete="off" value=""></td>
							</tr>
                                                        <tr>
								<td>Password </td>
								<td><input type="password" name="password" id="password" autocomplete="off" value=""></td>
							</tr>

                            <tr>
                            <td>&nbsp;</td>
                            </tr>

                            <tr>
                            <td colspan="2" >
                                <div id="captcha_placeholder"></div>
                            </td>
                            </tr>

							<tr>
								<td>
<input type="submit" name="login_submit" value="Login & Apply" onclick="return app_validate();" /> 


<p><a href="' . get_option("forget_page_url") . '">Forgot your password</a></p>
							
								</td>
							</tr>
						</table>
					 </form>
				</div>';

    return $job_settings_str;
}






function register_apply() {
    global $post;
    $pageid = $post->ID;
    global $wpdb;
    $setting = $wpdb->get_row(@$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "jobs_settings",""));
    $job_settings_str = '';
    if (isset($_POST['apply-submit']) == "Submit") {
        global $wpdb;
        $setting = $wpdb->get_row(@$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "jobs_settings",""));
        $feed_url = $setting->http_url; //'http://jobs.chameleoni.com/xmlfeed.aspx';
        $AuthKey = $setting->authKey;
        $AuthPassword = $setting->authPassword;
        $APIKey = $setting->aPIKey;
        $UserName = $setting->userName;
        $Thank_you_page = get_option("thank_you_page");
        $feed_location = $setting->feed_location;
        $feed_type = $setting->feed_type;
        $feed_salary = $setting->feed_salary;
        $feed_summary = $setting->feed_summary;
        $page_size = $setting->number_of_jobsper_Page;



        //validate captcha
        if ( (!isset($_POST['g-recaptcha-response']))  )
        {
            echo "invalid captcha." ;
            return;
        }

        $captcha_response = sanitize_text_field($_POST['g-recaptcha-response']);
        $captcha_params = 'secret=6Ld-EroUAAAAALbixufOd8I7DieG01uVEMv-HmWl' . '&response=' . $captcha_response ;
        $captcha_request = curl_init("https://www.google.com/recaptcha/api/siteverify");

        curl_setopt($captcha_request, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($captcha_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($captcha_request, CURLOPT_POSTFIELDS, $captcha_params);
        curl_setopt($captcha_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($captcha_request, CURLOPT_VERBOSE, 0);

        $captcha_google_response = curl_exec($captcha_request);  
        
        $decoded_response = json_decode($captcha_google_response, true);
        $captcha_validation_check = $decoded_response["success"];

        if($captcha_validation_check != '1')
        {
            echo '<div class="row">Captcha validation failed.<div>
                  <div class="row">
                    <a HREF="#"  onclick ="(function(){ window.location.href = window.location.href; })();return false;">Try again</a>
                  </div>
                ' ;
            return;
        }
        
        //validate captcha
        




        $request_email = '<ChameleonIAPI>
				<Method>CheckEmail</Method>
				<APIKey>' . $APIKey . '</APIKey>
				<UserName>' . $UserName . '</UserName>
				 <InputData>
				 <Input Name="Email" Value="' . sanitize_email($_POST['Email']) . '" />
				 </InputData>
				</ChameleonIAPI>';

        $encoded = 'Xml=' . $request_email . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
        $ch = curl_init($feed_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        $result_email = curl_exec($ch);
        $result_email = str_replace('utf-16', 'utf-8', $result_email);
        $xml = simplexml_load_string($result_email);
        $json = json_encode($xml);
        $array_email = json_decode($json, TRUE);

        // end verification 

        if ($array_email['ContactCount'] == 0) {


            $request = '<ChameleonIAPI>
				<Method>CandidateRegister </Method>
				<APIKey>' . $APIKey . '</APIKey>
				<UserName>' . $UserName . '</UserName>
				<InputData>
						<Input Name="TitleId" Value="1" />
						<Input Name="Forename" Value="' . sanitize_text_field($_POST['Forename']) . '" />
						<Input Name="Surname" Value="' . sanitize_text_field($_POST['Surname']) . '" />
						<Input Name="Email" Value="' . sanitize_email($_POST['Email']) . '" />
						<Input Name="WebPassword" Value="' . sanitize_text_field($_POST['WebPassword']) . '" />
						<Input Name="HomeTelNo" Value="' . sanitize_text_field($_POST['HomeTelNo']) . '" />
						<Input Name="MobileTelNo" Value="' . sanitize_text_field($_POST['MobileTelNo']) . '" />
						<Input Name="WorkTelNo" Value="' . sanitize_text_field($_POST['WorkTelNo']) . '" />
				</InputData>
			</ChameleonIAPI>';


            $encoded = 'Xml=' . $request . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
            $ch = curl_init($feed_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            $result = curl_exec($ch);
            $result = str_replace('utf-16', 'utf-8', $result);
            $xml = simplexml_load_string($result);
            $json = json_encode($xml);
            $array_contactid = json_decode($json, TRUE);
            curl_close($ch);

            //if(!session_id())
            //session_start();


            $ContactId = $array_contactid['ContactId'];
            $_SESSION['ContactId'] = $ContactId;
            if (isset($_FILES['cv']) and strlen($_FILES['cv']['name'])) {
                $cv_parts = explode('.', @$_FILES['cv']['name']);
                $ftype = end($cv_parts);
                $fcontent = file_get_contents($_FILES['cv']['tmp_name']);
                $fcontent = base64_encode($fcontent);

                $res_conid = '<ChameleonIAPI><Method>AttachCV</Method>
				  <APIKey>' . $APIKey . '</APIKey>
				<UserName>' . $UserName . '</UserName>
				<InputData><Input Name="ContactId" Value="' . $ContactId . '" />
				<Input Name="CVDocType" Value="' . $ftype . '" />
				 <Input Name="CVBase64" Value="' . $fcontent . '"/>
				</InputData>
				</ChameleonIAPI>';

                $encoded = 'Xml=' . $res_conid . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
                $ch = curl_init($feed_url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_VERBOSE, 0);
                $result = curl_exec($ch);



                curl_close($ch);
            }
            $_SESSION['Forename'] = '';
            $_SESSION['Surname'] = '';
            $_SESSION['WebPassword'] = '';
            $_SESSION['HomeTelNo'] = '';
            $_SESSION['MobileTelNo'] = '';
            $_SESSION['WorkTelNo'] = '';
            $_SESSION['cv'] = '';
            $_SESSION['Email'] = '';


            // Apply to the job
            $job_name = $_GET['job_title'];
            $job_id = $_GET['jobid'];
            $job_ref = $_GET['job_ref'];

            $res_job_apply = '<ChameleonIAPI><Method>CandidateApplication</Method>
				  <APIKey>' . $APIKey . '</APIKey>
				<UserName>' . $UserName . '</UserName>
				<InputData>
                                <Input Name="RequirementId" Value="' . $job_id . '"/>
<Input Name="CandidateId" Value="' . $ContactId . '"/>
<Input Name="WebApplicationTaskTypeId" Value="119"/>
<Input Name="ApplicationText" Value="' . $job_name . '"/>
<Input Name="MailName" Value="Email - Application"/>
<Input Name="SiteName" Value=""/>
				</InputData>
				</ChameleonIAPI>';

            $encoded = 'Xml=' . $res_job_apply . '&Action=postxml&AuthKey=' . $AuthKey . '&AuthPassword=' . $AuthPassword;
            $ch = curl_init($feed_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            $result = curl_exec($ch);


            curl_close($ch);

            $result = str_replace('utf-16', 'utf-8', $result);
            $xml = simplexml_load_string($result);
            $json = json_encode($xml);
            $array_res = json_decode($json, TRUE);
            unset($_GET['jobid']);
            unset($_GET['job_title']);
            unset($_GET['job_ref']);
            $_GET['task'] = "search";
            $url_params = http_build_query($_GET);
            if ($array_res['AppliedStatus'] == 0) {
                echo "<div class='alert alert-success'>Thank you for your application for vacancy " . $job_ref . ". One of our consultants will be in touch shortly.
<a href='" . get_option("jobs_search_url") . "?" . $url_params . "'>Please click here to go back to our live vacancies.</a></div><style> #form-application{ display:none !important; } #apply_head{ display:none; } </style>";
            } elseif ($array_res['AppliedStatus'] == 1) {
                echo "<div class='alert alert-danger'>You already applied</div>";
            }

//                    if ($setting->thank_you_page == 0) {
//
//
//                        $redpage = "index.php";
//                    } else {
//                        $redpage = "?page_id=$setting->thank_you_page";
//                    }
        } // if email not exit;
        else {


            //if (!session_id())
            //  session_start();


            $_SESSION['Forename'] = sanitize_text_field($_POST['Forename']);
            $_SESSION['Surname'] = sanitize_text_field($_POST['Surname']);
            $_SESSION['WebPassword'] = sanitize_text_field($_POST['WebPassword']);
            $_SESSION['HomeTelNo'] = sanitize_text_field($_POST['HomeTelNo']);
            $_SESSION['MobileTelNo'] = sanitize_text_field($_POST['MobileTelNo']);
            $_SESSION['WorkTelNo'] = sanitize_text_field($_POST['WorkTelNo']);
            $_SESSION['cv'] = sanitize_file_name($_FILES['cv']['name']);
            $_SESSION['Email'] = sanitize_email($_POST['Email']);


            $job_settings_str = '<div class="alert alert-danger">Your Email Address Already Exists</div>';
        }
    }
    //$job_settings_str = '';
    $job_settings_str .= '<script type="application/javascript">
				function app_validate()
				{
					validation_string = new Array();
					var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;

if(document.getElementById("cv").value == "")
{
document.getElementById("cv").focus();
validation_string.push("cv");
}


					if(document.getElementById("Forename").value == "")
					{
						document.getElementById("Forename").focus();
						validation_string.push("Forename");
					}
					if(document.getElementById("Surname").value == "")
					{
						document.getElementById("Surname").focus();
						validation_string.push("Surname");
					}
					if(document.getElementById("Email").value == "")
					{
						validation_string.push("Email");
						document.getElementById("Email").focus();
					}
					
					if(document.getElementById("Email").value != "" && !document.getElementById("Email").value.match(emailExp))
					{
							
						validation_string.push("Valid Email Address");
						document.getElementById("Email").focus();

					}
                                        if(document.getElementById("MobileTelNo").value == "")
					{
						document.getElementById("MobileTelNo").focus();
						validation_string.push("MobileTelNo");
                                        }


					if(validation_string != ""){
						alert("Please enter "+validation_string.join(", ").toString());
						return false;
					}
					
					return true;
                }
                
              


                </script>
               
                ';
    $job_settings_str .= '
	
	
			<script>
                var script_callback = function()
                {
                    setTimeout(function()
                            { 
                                grecaptcha.render("captcha_placeholder", 
                                {
                                    "sitekey" : "6Ld-EroUAAAAAK5zaBg3C2Qr7Gg7C0lSIS_AsBx-"
                                }
                                );                                
                            }, 
                            250
                        );
                }			
			</script>
			
			<script src="https://www.google.com/recaptcha/api.js?onload=script_callback&render=explicit" async defer></script>
	 
	<div class="application-edit front-end-edit">
					<h3 id="apply_head">Apply for position: ' . $_REQUEST['job_title'] . '</h3>
					<form id="form-application" action="" method="post" class="form-validate" enctype="multipart/form-data" >        		
						<input  type="hidden" class="required"  id="job_id" name="job_id"   value="' . $_REQUEST['jobid'] . '">
						<input type="hidden" class="required" value="' . $_REQUEST['job_title'] . '" id="job_title" name="job_title">
						<table border="0">
							<tr>							
								<td>Forename <span class="star">&nbsp;*</span> </td>
								<td><input type="hidden" name="TitleId" value="1"><input type="text" name="Forename" id="Forename"  value="' . @$_SESSION['Forename'] . '" /></td>
							</tr>
							<tr>
								<td>Surname <span class="star">&nbsp;*</span> </td>
								<td><input type="text" name="Surname" id="Surname"  value="' . @$_SESSION['Surname'] . '" /></td>
							</tr>
							<tr>
								<td>Email<span class="star">&nbsp;*</span> </td>
								<td><input type="text" name="Email" id="Email" value="' . @$_SESSION['Email'] . '" /></td>
							</tr>
							<tr>
								<td>Password </td>
								<td><input type="password" name="WebPassword" id="WebPassword"  value="' . @$_SESSION['WebPassword'] . '" /></td>
							</tr> <tr>
								<td>Home Tel </td>
								<td><input type="text" name="HomeTelNo" id="HomeTelNo"  value="' . @$_SESSION['HomeTelNo'] . '" /></td>
							</tr> <tr>
								<td>Mobile Tel<span class="star">&nbsp;*</span></td>
								<td><input type="text" name="MobileTelNo" id="MobileTelNo"  value="' . @$_SESSION['MobileTelNo'] . '" /></td>
							</tr> <tr>
								<td>Work Tel  </td>
								<td><input type="text" name="WorkTelNo" id="WorkTelNo"  value="' . @$_SESSION['WorkTelNo'] . '" /></td>
							</tr>
							<tr>
								<td>Attach CV <span class="star">&nbsp;*</span></td>
								<td><input type="file" name="cv"  id="cv"  value="' . @$_SESSION['cv'] . '" /></td>
							</tr>




<tr>
<td colspan="2" >
    <div id="captcha_placeholder"></div>
</td>
</tr>


							<tr>
								<td><input type="submit" value="Submit" name="apply-submit"  class="jobapply" onclick="return app_validate();"/>
								
								</td>
							</tr>
						</table>
					 </form>
				</div>';
    return $job_settings_str;
}
