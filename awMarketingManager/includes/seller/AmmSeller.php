<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AmmSeller {

	public static function getSeller() {
		
		if( ! self::ifLoggedIn() ){
			die;
		}	

		global $current_user;


        $args   = array(
        		'author' 		=>  $current_user->ID,
                'post_status'   => 'draft',                
                'post_type'     => 'bond',
                'post_per_page' => -1                
                );
        $posts  = get_posts($args);
        echo self::showBonds($posts);
	}

	public static function showBonds( $postData ) {

		$html  = '';
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

            $offer_price  	= self::formatAmount($offer_price);
            $orig_size  	= self::formatAmount($orig_size);

            
            $html .=        '<tr class="warning"  data-toggle="collapse" data-target="#accordion-'.$bond->ID.'" class="clickable">';
            $html .=            '<td>'.$type.'</td>';
            $html .=            '<td>'.$orig_rating.'</td>';
            $html .=            '<td>'.$cusip_isin.'</td>';
            $html .=            '<td>'.$ticker.'</td>';        
            $html .=            '<td>'.$orig_size.'</td>';        
            $html .=            '<td>'.$manager.'</td>';        
            $html .=            '<td>'.$offer_price.'</td>';        
            $html .=            '<td>
            						<button class="btn btn-default" >Show Bids</button>
            						'.self::getPopUp( $bond->ID, $type ).'
            					</td>';
            $html .=        '</tr>';
             $html .=        '<tr id="accordion-'.$bond->ID.'" class="collapse">';
            $html .=            '<td colspan="12">';
            $html .=                '<div>';
            $html .=                    self::getBidUser($bond->ID);
            $html .=                '</div>';
            $html .=            '</td>';
            $html .=        '</tr>';
        }

        $html .=    '</tbody>';
        $html .= '</table>';

        return $html;

    }

    public static function getBidUser($bond_ID ){

        $html = '';
        $html .= '<table class="table-front">';
        $html .=    '<thead>';
        $html .=        '<tr class="success">';
        $html .=           '<th>Bid Price</th>';
        $html .=           '<th></th>';
        $html .=        '</tr>';
        $html .=    '</thead>';
        $html .=    '<tbody>';

        $meta_key       = 'market_placed_bid_'.$bond_ID;
        $placedBids     = get_post_meta($bond_ID,$meta_key,true);

         

        $user_id        = get_current_user_id();

        $keyTradeWinner   = 'market_trade_bid_'.$bond_ID;
        $getTradeWinner     = get_post_meta($bond_ID,$keyTradeWinner,true);
        $tradedUserID       =   $getTradeWinner[$bond_ID]['user_id'];
        if(!empty($placedBids)){

            foreach ($placedBids as $key => $bid ) {

                $trade_btn = '<button  data-price="'.$placedBids[$key][$bond_ID].'" data-bond="'.$bond_ID.'" class="amm_trade_btn btn-default" data-user="'.$key.'" id="trade_'.$bond_ID.'">Trade</button>';   
                
                $html .=        '<tr class="warning user_'.$key.'">';
                $html .=            '<td>$'.$placedBids[$key][$bond_ID].'</td>';
                //if (!empty($getTradeWinner)) {
                if (!empty($getTradeWinner)) {
                    if ($tradedUserID == $key) {
                        $html .= '<td style="width:15%">Winner</td>';
                    }else{
                        $html .= '<td style="width:15%"></td>';
                    }
                }else{
                    $html .= '<td style="width:15%">'.$trade_btn.'</td>';
                }

                $html .=        '</tr>';

            }
        }else{
            $html .=        '<tr class="warning">';
            $html .=            '<td colspan="4" style="text-align:center"><strong>No Bid Placed.</strong></td>';
            $html .=        '</tr>';
        }

        $html .=    '</tbody>';
        $html .= '</table>';


    	
        return $html;
    }


    public static function getPopUp( $divID, $title ){ 
    	ob_start();
    	?>    	
    	<!-- Modal -->
		<div class="modal fade" id="<?php echo $divID;?>" role="dialog">
			<div class="modal-dialog">  
				<!-- Modal content-->
				<div class="modal-content">
					
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h3 class="modal-title"><strong>Place Bid - <?php echo $title;?></strong></h3>
					</div>
					
					<div class="modal-body">
						<div class="form-group">
							<label for="amount">Amount</label>
							<input type="number" class="form-control" id="amount-<?php echo $divID;?>">
						</div>
						<button type="submit" class="btn btn-default">Place Bid</button>&nbsp;


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