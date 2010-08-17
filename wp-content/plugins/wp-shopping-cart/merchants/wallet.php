<?php    
//require_once('./admin.php'); 
$nzshpcrt_gateways[$num]['name'] = 'Мой кошелек';
$nzshpcrt_gateways[$num]['internalname'] = 'wallet';
$nzshpcrt_gateways[$num]['function'] = 'gateway_wallet';
$nzshpcrt_gateways[$num]['form'] = "form_wallet";
$nzshpcrt_gateways[$num]['submit_function'] = "submit_wallet";

function gateway_wallet($seperator, $sessionid)
{
  global $userdata, $user_ID, $wpdb;
  $userdata->wallet = (float) $userdata->wallet;
  $totalsum = (float) $_SESSION['nzshpcrt_totalprice']; 
  $transact_url = "";
  if ($userdata->wallet >= $totalsum)
  {
    $userdata->wallet = $userdata->wallet - $totalsum;
    $wpdb->query("UPDATE `".$wpdb->prefix."users` SET `wallet` = ".$userdata->wallet." WHERE `id` ='".$user_ID."' LIMIT 1 ;");
    $transact_url = get_option('transact_url');
    $sql = "UPDATE `".WPSC_TABLE_PURCHASE_LOGS."` SET `processed`= '2' WHERE `sessionid`=".$sessionid;
    $wpdb->query($sql);
    $_SESSION['wallet'] = 'success';
  }
  else
  {
      $_SESSION['wallet'] = 'decline';
      $_SESSION['WpscGatewayErrorMessage'] = "Средств на вашем кошельке недостаточно для проведения транзакции.";
      //$_SESSION['wpsc_checkout_misc_error_messages'][] = "Describe error"; 
      $transact_url = get_option('checkout_url'); 
  }
  header("Location: ".$transact_url.$seperator."sessionid=".$sessionid);
  exit(); 
}

function submit_wallet()
{


$user_id = (int) $user_id;
$current_user = wp_get_current_user();    print_r($current_user); exit();
      //$profileuser = get_user_to_edit($user_id); 
      //$wallet = 
  return true;
}

function form_wallet()
{
  return '';
}
  ?>
