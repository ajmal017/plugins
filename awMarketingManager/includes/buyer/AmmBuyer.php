<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AmmBuyer {

	public static function getBonds() {
		
		if( ! self::ifLoggedIn() ){
			die;
		}	
        $args   = array(
                'post_status'   => 'draft',                
                'post_type'     => 'bond',
                'post_per_page' => -1                
                );
        $posts  = get_posts($args);
        echo self::showBonds($posts);
	}


    public static function messagepopup(){

        $html = '<div id="message-div" style="margin-bottom:2%;" class="hide text-center">';
        $html .= '<img src="'.site_url().'/wp-content/plugins/awMarketingManager/assets/image/ajax-loader.gif" />';
        $html .= '</div>';

        return $html;
    }

	public static function showBonds( $postData ) {

		$html  = '';
        $html  = self::messagepopup();
        $html .= '<table class="table-front">';
        $html .=    '<thead>';
        $html .=        '<tr class="success">';
        $html .=           '<th>Type</th>';
        $html .=           '<th>Orig Rating</th>';
        $html .=           '<th>CU SIP/ISIN</th>';
        $html .=           '<th>Ticker</th>';
        $html .=           '<th>Orig Size (MM)</th>';
        $html .=           '<th>Manager</th>';                        
        $html .=           '<th>Offer Price</th>';                        
        $html .=           '<th>Action</th>';                        
        $html .=        '</tr>';
        $html .=    '</thead>';
        $html .=    '<tbody>';

        foreach ( $postData as $bond ) { 

            $user_id    = get_current_user_id();         	 
          	
            $list_id        = $bond->ID;
            $type           = get_post_meta($bond->ID,"type",true);
            $orig_rating    = get_post_meta($bond->ID,"orig_rating",true);
            $cusip_isin     = get_post_meta($bond->ID,"cusip/isin",true);
            $ticker         = get_post_meta($bond->ID,"ticker",true);
            $orig_size      = get_post_meta($bond->ID,"orig_size_(mm)",true);
            $curr_size      = get_post_meta($bond->ID,"curr_size_(mm)",true);
            $c_e            = get_post_meta($bond->ID,"c/e",true);
            $manager        = get_post_meta($bond->ID,"manager",true);
            $offer_price  	= get_post_meta($bond->ID,"offer_price",true);

            $offerprice    = $offer_price;
        
            $meta_key       = 'market_placed_bid_'.$bond->ID;
            $userPlacedBid  = get_post_meta( $bond->ID, $meta_key, true );

            $bidPrice       = $userPlacedBid[$user_id][$bond->ID];
        
            $offer_price  	= self::formatAmount($offer_price);
            $orig_size  	= self::formatAmount($orig_size);

            $keyTradeWinner   = 'market_trade_bid_'.$bond->ID;
            $getTradeWinner   = get_post_meta($bond->ID,$keyTradeWinner,true);
            $tradedUserID     =   $getTradeWinner[$bond->ID]['user_id'];
            
            if (!empty($getTradeWinner)) {
                continue;
            }else{
                $html .=        '<tr class="warning"  data-toggle="collapse" data-target="#accordion-'.$bond->ID.'" class="clickable">';
                $html .=            '<td>'.$type.'</td>';
                $html .=            '<td>'.$orig_rating.'</td>';
                $html .=            '<td>'.$cusip_isin.'</td>';
                $html .=            '<td>'.$ticker.'</td>';        
                $html .=            '<td>'.$orig_size.'</td>';        
                $html .=            '<td>'.$manager.'</td>';        
                $html .=            '<td>'.$offer_price.'</td>';        
                $html .=            '<td>
                						<a class="btn btn-default" href="#"  data-toggle="modal" data-target="#'.$bond->ID.'">Place Counter Bid</a>
                						'.self::getPopUp( $bond->ID, $type, $bidPrice, $offerprice, $ticker, $orig_size ).'
                                        <button type="button" data-bondid="'.$bond->ID.'" data-offerprice="'.$offerprice.'" class="accept-offerprice btn btn-default">Accept</button>

                					</td>';
                $html .=        '</tr>';
            }

        }

        $html .=    '</tbody>';
        $html .= '</table>';

        return $html;

    }

    public static function getPopUp( $divID, $title, $bidPrice = '', $offerprice,  $ticker, $orig_size ){ 
    	ob_start();
    	?>    	
    	<!-- Modal -->
		<div class="modal fade" id="<?php echo $divID;?>" role="dialog">
			<div class="modal-dialog">  
				<!-- Modal content-->
				<div class="modal-content">


					
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h3 class="modal-title">
                            <strong>Place Bid for <?php echo $ticker." - ".$orig_size." MM";?></strong>
                        </h3>
					</div>
					
					<div class="modal-body">
						
                        
                      
                        <div class="form-group">
							<label for="amount">Amount</label>
							<input type="number" class="form-control bid_amount" id="amount-<?php echo $divID;?>" data-offerprice= "<?php echo $offerprice;?>" value="<?php echo $bidPrice;?>">
						</div>
						<button type="submit" class="place-market-bid btn btn-default">Place Your Bid</button>&nbsp;

                        
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>					
				</div>    
			</div>
		</div>
		<?php
		$output = ob_get_clean();
		return $output;
    }

	public static function ifLoggedIn(){
		if (!is_user_logged_in()) {
        	echo "<h2>You are not logged in user. Please login.</h2>";
        	return false;
        }else{
        	return true;
        }
	}

	public static function formatAmount( $price ) {

		/* check if $ exist in cover price */
        $findDoller     = strpos($price[0],'$');
        $appendCover    = "";
        if($findDoller === false){
            $appendCover =    "$".$price;
        }else{
            $appendCover =    $price;
        }


        /* check if . exist in orig size */
        $findDot    = strpos($appendCover,'.');
        $appendOrig = "";
        if($findDot === false){
            $appendOrig =    $appendCover.".00";
        }else{
            $appendOrig =    $appendCover;
        }

        return $appendOrig;
	}
} /*Class ends */    