<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AwBacksplashFields {
	/* variables */

	/* methods */
	public static function backsplash_height( $roomTypeID, $areaTypeID, $section_inner, $name = '', $value = '' ){
		ob_start();	
		$height = AwComponents::$itemFirePlace['height_'.$section_inner];
		?>
			<select class="height_<?php echo $section_inner;?>" name="height_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>">
	            <option>Height</option>
	            <option>test1</option>
	            <option>test2</option>
	        </select>		
		<?php                
		$output = ob_get_clean();
		return $output;
	}

	public static function backsplash_edge( $roomTypeID, $areaTypeID, $section_inner, $name = '', $value = '' ){
		
		ob_start();

		$edge = AwComponents::$itemFirePlace['edge_'.$section_inner];
		
		?>
		<?php  $edge_query = new WP_Query(array('post_type'=>'edge', 'post_status'=>'publish', 'posts_per_page'=>-1));
		               if ( $edge_query->have_posts() ) : 	?>
		                <select class="edge_<?php echo $section_inner;?>"data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" name="edge_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>"> <option>Select Edge Treatment type </option>
		                <?php while ( $edge_query->have_posts() ) : $edge_query->the_post();
		                 		$title = get_the_title( get_the_ID() );
		                 ?>
		                	
		                	<?php if($edge == $title){ ?>
		                			 <option selected ><?php echo  the_title(); ?></option>
		                	 <?php } 
		                	 else{ ?> 
 
		                        	<option><?php echo  the_title(); ?></option>
		                    <?php } ?>    	
		                <?php endwhile; ?>
		                </select>
		            <?php endif; ?>
		<?php                
		$output = ob_get_clean();
		return $output;
	}


	public static function backsplash_transition( $roomTypeID, $areaTypeID, $section_inner, $name = '', $value = '' ){
		
		ob_start();

		$transition = AwComponents::$itemFirePlace['transition_'.$section_inner];
		
		?>
		<select class="transition_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" name="transition_<?php echo $section_inner;?>">
		    <option>Select Transition</option>

		    

		</select>
		<?php                
		$output = ob_get_clean();
		return $output;
	}


	public static function backsplash_amount( $roomTypeID, $areaTypeID, $section_inner, $name = '', $value = '' ){
		
		ob_start();

		$amount = AwComponents::$itemFirePlace['amount_'.$section_inner];

		?>
		<input type="text" placeholder="Amount"data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>"class="amount_<?php echo $section_inner;?>" name="amount_<?php echo $section_inner;?>" value="<?php echo $amount;?>">
		<?php                
		$output = ob_get_clean();
		return $output;
	}


	public static function backsplash_lf( $roomTypeID, $areaTypeID, $section_inner, $name = '', $value = '' ){
		
		ob_start();
		$lf = AwComponents::$itemFirePlace['lf_'. $section_inner];
		?>
		<input type="text" placeholder="LF" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" class="lf_<?php echo $section_inner;?>" name="lf_<?php echo $section_inner;?>" value="<?php echo $lf;?>">
		<?php                
		$output = ob_get_clean();
		return $output;
	}

	public static function backsplash_edge_instructions( $roomTypeID, $areaTypeID, $section_inner, $name = '', $value = '' ){
		
		ob_start();
		$instructions = AwComponents::$itemFirePlace['edge_instructions_'. $section_inner];
		?>
		<?php  $special_edge_query = new WP_Query(array('post_type'=>'special_edge', 'post_status'=>'publish', 'posts_per_page'=>-1));
		            if ( $special_edge_query->have_posts() ) : ?>
		            <select  data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" class="instructions_<?php echo $section_inner;?>" name="instructions_<?php echo $section_inner;?>"> <option>Select Edge Treatments Instructions </option>
		            	<?php while ( $special_edge_query->have_posts() ) : $special_edge_query->the_post(); 
		            		 $title = get_the_title( get_the_ID() );
		            	?>
		            		<?php if($instructions == $title){?>
								<option selected ><?php echo  the_title(); ?></option>
		            		<?php } 
		            		else{ ?>  
		            			<option><?php echo  the_title(); ?></option>
		            		<?php } ?>
		            	<?php endwhile; ?>
		            </select>
		            <?php endif; ?>
		<?php                
		$output = ob_get_clean();
		return $output;
	}


	public static function backsplash_instructions( $roomTypeID, $areaTypeID, $section_inner, $name = '', $value = '' ){
		
		ob_start();
		$instructions = AwComponents::$itemFirePlace['instructions_'. $section_inner];
		?>
		<?php  $special_edge_query = new WP_Query(array('post_type'=>'special_edge', 'post_status'=>'publish', 'posts_per_page'=>-1));
		            if ( $special_edge_query->have_posts() ) : ?>
		            <select  data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" class="instructions_<?php echo $section_inner;?>" name="instructions_<?php echo $section_inner;?>"> <option>Edge Treatments Instructions </option>
		            <?php while ( $special_edge_query->have_posts() ) : $special_edge_query->the_post(); 
		           		 $title = get_the_title( get_the_ID() );
		            ?>
		            <?php if($instructions == $title){?>
					<option selected><?php echo  the_title(); ?></option>
		            <?php } 
		            else{ ?> 
		            <option><?php echo  the_title(); ?></option>
		            <?php } ?>
		            <?php endwhile; ?>
		            </select>
		            <?php endif; ?>
		<?php                
		$output = ob_get_clean();
		return $output;
	}

	
	public static function backsplash_accent( $roomTypeID, $areaTypeID, $section_inner, $name = '', $value = '' ){ob_start();	
		?>	

	<?php  $deco_instruction_query = new WP_Query(array('post_type'=>'deco_instruction', 'post_status'=>'publish', 'posts_per_page'=>-1));
		    if ( $deco_instruction_query->have_posts() ) : 

		    	$accent = AwComponents::$itemFirePlace['accent_'. $section_inner]; 
		
		    	?>
		    <select class="accent_<?php echo $section_inner;?>"  data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" name="accent_<?php echo $section_inner;?>">    <option>Accent Instructions</option>
		    <?php while ( $deco_instruction_query->have_posts() ) : $deco_instruction_query->the_post(); 
		    	$title = get_the_title( get_the_ID() );

		    ?>
		    <?php if($accent == $title){?>
						<option selected="selected" ><?php echo  the_title(); ?></option>
		            	<?php } 
		            	else{ ?> 
		            	<option><?php echo  the_title(); ?></option>
		            	<?php } ?>
		    <?php endwhile; ?>
		    </select>
		    <?php endif; ?>

		<?php                
		$output = ob_get_clean();
		return $output;

		}


}/* class ends here */

?>
