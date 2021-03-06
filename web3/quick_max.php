<?php
/*
 * 
 * Program      : TSWebTek Lotto Center ver. 0.5
 * File         : 
 * Programmed By: Piratheep Mahenthiran
 * Date         : Mar 2011
 * Copyright (C) 2011 TSWebTek Ltd.

*/
//ob_start();
session_start();

  setlocale(LC_MONETARY, 'en_CA.UTF-8');
  include_once("inc/smarty/libs/Smarty.class.php");
  
  require_once("../inc/validform/libraries/ValidForm/class.validform.php");
  include_once("../inc/class_db.php");
  include_once("../inc/incGenDates.php");
  include_once("../inc/incNaLottery.php");
  include_once("../inc/incLottery.php");
  include_once("../inc/incOLGLottery.php");
  include_once("../inc/class_http.php");
  require_once("../inc/incUser.php");
  require_once("../inc/incAnalytics.php");
  require_once("../inc/incQuickPick.php");
  
  $sNickName = "";
  $bLoggedIn = false;
  
  
  $objUser    = new User();
  $objLottery = new Lottery();
  $objDate    = new GenDates();
  $naLottery  = new NALottery();
  
  if ($_SESSION['valid']) {
    $iuserNo    = $_SESSION['userid'];
    $sNickName  = $_SESSION['_nickname'];
    $ds_user    = $objUser->UserGet($iuserNo);
    //print_r($ds_user);
    if (is_array($ds_user)) {
      $bLoggedIn = true;
    }
  }
  
  
  
  //$sSelectedDate = mktime(0,0,0,date('m'),date('d'),date('Y'));
  
  
  
  
   $data_avail = $naLottery->na649GetFirstLastDataAvail();

	$smarty = new Smarty();

	$smarty->template_dir = '/home1/tswebtek/tswlotto/web3/templates/';
	$smarty->compile_dir  = '/home1/tswebtek/tswlotto/web3/templates_c/';
	$smarty->config_dir   = '/home1/tswebtek/tswlotto/web3/configs/';
	$smarty->cache_dir	  = '/home1/tswebtek/tswlotto/web3/cache/';
	$smarty->left_delimiter = "[";
	$smarty->right_delimiter = "]";
	
$htmltopOut = "";
// Display User if logged in
 if ($bLoggedIn == true) {
	$smarty->assign('userLoggedIn', 1);
	$smarty->assign('arUser', array('_nickname'=> $_SESSION['_nickname'],
									'userid' => $_SESSION['userid']
									)
					);
	$htmltopOut .= "Hi " . $_SESSION['_nickname'];
	$htmltopOut .= "| <a href='user_logout.php'>Logout</a>";
} else {
		$smarty->assign('userLoggedIn', 0);
	$htmltopOut .= "<a href='user_login.php'>Login</a>";
}

// Display earliest and latest date of lotto data available									
if (is_array($data_avail)) {
	$smarty->assign('data_avail', array("earliest" => date('Y-m-d',strtotime($data_avail["earliest"])),
										"latest" =>  date('Y-m-d',strtotime($data_avail["latest"]))
										)
					);
	$htmltopOut .= "<br />Data Available from " . date('Y-m-d',strtotime($data_avail["earliest"])) . " till " . date('Y-m-d',strtotime($data_avail["latest"]));
}

$smarty->assign('htmltopOut', $htmltopOut);

$htmlFormStartOut = "";

$htmlFormStartOut .= '<form name="frmViewLotto" id="frmViewLotto" method="get" action="view_649.php">';


/*
$htmlThirdNav .= '<br /><div id="nav_disp_limit">';
$htmlThirdNav .= "<span class='not-selected' id='nav_disp_limit_M'>Month</span> | <span class='not-selected' id='nav_disp_limit_Y'>Year</span> | <span class='not-selected' id='nav_disp_limit_100'>100 Draws</span> | <span class='not-selected' id='nav_disp_limit_200'>200 Draws</span>"
			  . "</div>";
$htmlThirdNav .= '<div id="nav_action">'
			  . '<span id="nav_act_submit"><input type="submit" name="action" value="submit" /></span>'
			  . '</div>';
			  

$htmlThirdNav .= '<div id="nav_draw_view_date">'
  			  . '<span class="not-selected">Prev Month</span> | <span class="selected">Current Month</span> | <span class="not-selected">Next Month</span>'
			  . '</div>'
			  . '<div id="game_draw_header">'
			  . '&nbsp;</div>';
			  
*/	

/*
 * sortBy: [drawDate, Number]
 * startDate: 
 * endDate:
 * PageNum:
 * rows:
 * limit:
 * 
 * 
 * 
 */



$htmlMainCont = "";
$htmlMainCont .= '<div id="game_draw_body"><table border="0" colspan="0" width="100%" height="400px">';
/*
print "st DT: " . $st_date;
print "\n ed DT: " . $ed_date;
print "\n stRowNum: " . $st_row_num;
print "\n edRowNum: " . $ed_row_num;
 * 
 */
//$db_res = $naLottery->na649GetDraw($st_date, $ed_date, $st_row_num, $ed_row_num);
//print_r($db_res);



$QuickPick = new QuickPick();
$htmlMainCont .= '<tr><td align="center"><h3>Lotto Max Quick Pick : ' . implode("-",$QuickPick->naMaxQuickPick()) . '</h3></td></tr>';

$htmlMainCont .= '</table>';

  $objAnalytics   = new Analytics();
  $htmlMainCont .= $objAnalytics->GoogleAnalytics();


$htmlFormEndOut = '</form>';

$smarty->assign("htmlFormStart", $htmlFormStartOut);
$smarty->assign("htmlThirdNav", $htmlThirdNav);
$smarty->assign("htmlOut", $htmlMainCont);
$smarty->assign("htmlFormEnd", $htmlFormEndOut);
$smarty->display('quick_numbers.tpl');
