<?php

//error_reporting(E_ALL);
ini_set('display_errors', 0);
/*
	Plugin Name: AW Bond Management
	Plugin URI:
	Description: This plugin handles all functions related to bonds and auctions.
	Version: 1.0.0
	Author: A0800
	Author URI:
	License:
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Include external classes */
include('classes/aw_widget.php');
include('classes/AwTiedBids.php');
include('classes/AwAuction.php');
include('classes/AwSeller.php');
include('classes/AwBidder.php');
include('classes/AwBond.php');
include('classes/AwAjax.php');
include('classes/AwMetaBoxes.php');

// include('classes/AwAdminAuctionList.php');
include('aw_main.php');

/*  create plugin object. */
new aw_bond_management;
?>
