<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AwShowerFields {
	/* variables */

	/* methods */
	public static function shower_height( $roomTypeID, $areaTypeID, $section_inner, $name = '', $value = '' ){
		ob_start();	
		$height = AwComponents::$itemFirePlace['height_'.$section_inner];
		?>
			<select id="height_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" name="height_<?php echo $section_inner;?>">
			            	<option>Height</option>
			            	<option>Height2</option>
			            	<option>Height3</option>
			            </select>	
		<?php                
		$output = ob_get_clean();
		return $output;
	}


	public static function shower_edge($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){
		ob_start();	
		$edge = AwComponents::$itemFirePlace['edge_'.$section_inner];
		?>

		<?php 
		$edge_query = new WP_Query(array('post_type'=>'edge', 'post_status'=>'publish', 'posts_per_page'=>-1));
			if ( $edge_query->have_posts() ) : ?>
				<select class="edge_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" name="edge_<?php echo $section_inner;?>"> 
						<option>Select Edge Treatment type </option>
					<?php while ( $edge_query->have_posts() ) : $edge_query->the_post();
						$title = get_the_title( get_the_ID() );
					 ?>
						<?php if($edge == $title){?>
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

	public static function shower_transition($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){
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
	

	public static function shower_amount($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$amount = AwComponents::$itemFirePlace['amount_'.$section_inner];
		?>

			<input type="text" placeholder="Amount" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" class="amount_<?php echo $section_inner;?>" name="amount_<?php echo $section_inner;?>" value="<?php echo $amount;?>">
		<?php                
		$output = ob_get_clean();
		return $output;
	}

	public static function shower_lf($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$lf = AwComponents::$itemFirePlace['lf_'.$section_inner];
		?>

			<input type="text" placeholder="Lf" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" class="lf_<?php echo $section_inner;?>" name="lf_<?php echo $section_inner;?>"  value="<?php echo $lf;?>">
		<?php                
		$output = ob_get_clean();
		return $output;
	}
	
	public static function shower_instructions($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$instructions = AwComponents::$itemFirePlace['instructions_'.$section_inner];
		?>

			<?php  $special_edge_query = new WP_Query(array('post_type'=>'special_edge', 'post_status'=>'publish', 'posts_per_page'=>-1));
					if ( $special_edge_query->have_posts() ) : ?>
						<select  class="instructions_<?php echo $section_inner;?>"data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" name="instructions_<?php echo $section_inner;?>"> <option>Select Edge Treatments Instructions </option>
							<?php while ( $special_edge_query->have_posts() ) : $special_edge_query->the_post(); 
									$title = get_the_title( get_the_ID() );
							?>
									<?php if($instructions == $title){?>
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

	public static function shower_edge_instructions($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$edge_instructions = AwComponents::$itemFirePlace['edge_instructions_'.$section_inner];
		?>

			<select  class="edge_instructions_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" name="edge_instructions_<?php echo $section_inner;?>">
		                	<option> Edge Treatments Instructions</option>
		    </select>
		<?php                
		$output = ob_get_clean();
		return $output;
	}	

	
	public static function shower_additional($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['additional_'.$section_inner];
		?>

			<input placeholder="Additiona field" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" class="additional_<?php echo $section_inner;?>" name="additional_<?php echo $section_inner;?>" type="text" value="<?php echo $value;?>">
		<?php                
		$output = ob_get_clean();
		return $output;
	}	

	public static function shower_corner_shelf($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['corner_shelf_'.$section_inner];
		?>

			<?php  $niche_location_query = new WP_Query(array('post_type'=>'shelve_niche', 'post_status'=>'publish', 'posts_per_page'=>-1));
						   if ( $niche_location_query->have_posts() ) : ?>
							<select class="corner_shelf_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" name="corner_shelf_<?php echo $section_inner;?>"><option>Corner Shelf Type</option>
							<?php while ( $niche_location_query->have_posts() ) : $niche_location_query->the_post();$title = get_the_title( get_the_ID() );
							?>
									<?php if($value == $title){?>
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

	public static function shower_qty($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['qty_'.$section_inner];
		?>

			<input type="text" placeholder="Qty" class="qty_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" name="qty_<?php echo $section_inner;?>" value="<?php echo $value;?>">
		<?php                
		$output = ob_get_clean();
		return $output;
	}

	public static function shower_location($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['location_'.$section_inner];
		?>

			<?php  $shelve_localtion_query = new WP_Query(array('post_type'=>'shelve_localtion', 'post_status'=>'publish', 'posts_per_page'=>-1));
					   if ( $shelve_localtion_query->have_posts() ) : ?>
						<select class="location_<?php echo $section_inner;?>" name="location_<?php echo $section_inner;?>"data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" ><option>Location</option>
						<?php while ( $shelve_localtion_query->have_posts() ) : $shelve_localtion_query->the_post();
									$title = get_the_title( get_the_ID() );
							?>
									<?php if($value == $title){?>
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

	public static function shower_shelve_height($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['selves_height_'.$section_inner];
		?>

			<?php  $shelves_height_query = new WP_Query(array('post_type'=>'shelves_height', 'post_status'=>'publish', 'posts_per_page'=>-1));
					    if ( $shelves_height_query->have_posts() ) : ?>
						<select class="selves_height_<?php echo $section_inner;?>" name="selves_height_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>">	<option> Height</option>
						<?php while ( $shelves_height_query->have_posts() ) : $shelves_height_query->the_post(); 
									$title = get_the_title( get_the_ID() );
							?>
									<?php if($value == $title){?>
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

	public static function shower_niche_shelf($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['niche_shelf_'.$section_inner];
		?>

			<?php  $niche_new_location_query = new WP_Query(array('post_type'=>'niches', 'post_status'=>'publish', 'posts_per_page'=>-1));
					   if ( $niche_new_location_query->have_posts() ) : ?>
						<select class="niche_shelf_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>"name="niche_shelf_<?php echo $section_inner;?>"><option>Niches Shelf Type</option>
						<?php while ( $niche_new_location_query->have_posts() ) : $niche_new_location_query->the_post(); $title = get_the_title( get_the_ID() );
							?>
									<?php if($value == $title){?>
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

	public static function shower_niche_qty($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['niche_qty_'.$section_inner];
		?>

			<input type="text" placeholder="Qty" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" class="qty_<?php echo $section_inner;?>"name="niche_qty_<?php echo $section_inner;?>" value="<?php echo $value;?>">
		<?php                
		$output = ob_get_clean();
		return $output;
	}

	public static function shower_niche_location($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['niche_location_'.$section_inner];
		?>

			<?php  $niche_location_query = new WP_Query(array('post_type'=>'niche_location', 'post_status'=>'publish', 'posts_per_page'=>-1));
						   if ( $niche_location_query->have_posts() ) : ?>
							<select class="location_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>"name="niche_location_<?php echo $section_inner;?>">	<option>Location</option>
							<?php while ( $niche_location_query->have_posts() ) : $niche_location_query->the_post(); 
							$title = get_the_title( get_the_ID() );
							?>
									<?php if($value == $title){?>
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

	public static function shower_niche_height($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['niche_height_'.$section_inner];
		?>

			<?php  $niche_height_query = new WP_Query(array('post_type'=>'niche_height', 'post_status'=>'publish', 'posts_per_page'=>-1));
					   if ( $niche_height_query->have_posts() ) : ?>
						<select class="height_<?php echo $section_inner;?>"data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" name="niche_height_<?php echo $section_inner;?>"><option>Height</option>
						<?php while ( $niche_height_query->have_posts() ) : $niche_height_query->the_post(); 
									$title = get_the_title( get_the_ID() );
							?>
									<?php if($value == $title){?>
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


	public static function shower_bench($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['bench_'.$section_inner];
		?>

				<?php  $banche_query = new WP_Query(array('post_type'=>'banche', 'post_status'=>'publish', 'posts_per_page'=>-1));
						   if ( $banche_query->have_posts() ) : ?>
							<select class="bench_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>"name="bench_<?php echo $section_inner;?>">	<option>Special Bench Type</option>
							<?php while ( $banche_query->have_posts() ) : $banche_query->the_post(); 

							$title = get_the_title( get_the_ID() );
							?>
									<?php if($value == $title){?>
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
	
	public static function shower_bench_qty($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['bench_qty_'.$section_inner];
		?>

				<input type="text" placeholder="Qty" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" class="qty_<?php echo $section_inner;?>" name="bench_qty_<?php echo $section_inner;?>" value="<?php echo $value;?>">
		<?php                
		$output = ob_get_clean();
		return $output;
	}

	public static function shower_bench_location($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['bench_location_'.$section_inner];
		?>

				<?php  $shelve_localtion_query = new WP_Query(array('post_type'=>'shelve_localtion', 'post_status'=>'publish', 'posts_per_page'=>-1));
					   if ( $shelve_localtion_query->have_posts() ) : ?>
						<select class="shelve_location_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>" name="bench_location_<?php echo $section_inner;?>"><option>Location</option>
						<?php while ( $shelve_localtion_query->have_posts() ) : $shelve_localtion_query->the_post(); 
							$title = get_the_title( get_the_ID() );
							?>
									<?php if($value == $title){?>
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

	public static function shower_bench_height($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['bench_height_'.$section_inner];
		?>

				<?php  $shelves_height_query = new WP_Query(array('post_type'=>'shelves_height', 'post_status'=>'publish', 'posts_per_page'=>-1));
					   if ( $shelves_height_query->have_posts() ) : ?>
						<select class="shelve_height_<?php echo $section_inner;?>" name="bench_height_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>">	<option>Height</option>
						<?php while ( $shelves_height_query->have_posts() ) : $shelves_height_query->the_post(); 
						$title = get_the_title( get_the_ID() );
							?>
									<?php if($value == $title){?>
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


	public static function shower_niche_instruction($roomTypeID, $areaTypeID, $section_inner, $name = '', $value = ''){

		ob_start();	
		$value = AwComponents::$itemFirePlace['niche_instruction_'.$section_inner];
		?>

				<?php  $special_niche_query = new WP_Query(array('post_type'=>'special_niche', 'post_status'=>'publish', 'posts_per_page'=>-1));
					   if ( $special_niche_query->have_posts() ) : ?>
						<select class="niche_instruction_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>"name="niche_instruction_<?php echo $section_inner;?>">	<option>Special Niches Instructions</option>
						<?php while ( $special_niche_query->have_posts() ) : $special_niche_query->the_post(); 
						$title = get_the_title( get_the_ID() );
							?>
									<?php if($value == $title){?>
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
