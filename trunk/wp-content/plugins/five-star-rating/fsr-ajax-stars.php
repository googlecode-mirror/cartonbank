<?php
require_once('../../../wp-config.php');
require_once('fsr.class.php');
$picture_id = $_REQUEST['p'];
$star_type = $_REQUEST['starType'];
$FSR = new FSR();
$FSR->init();
echo $FSR->getVotingStars($star_type);
?>
