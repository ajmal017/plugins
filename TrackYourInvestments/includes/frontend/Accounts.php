<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Accounts {

    /* Create Account*/
	public static function createAccount(){
        self::accountHTML();
        die();
    }

    public static function accountHTML(){
    	$url            = admin_url().'admin-ajax.php';
        $loader         = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/ajax-loader.gif';
        if (!isset($_GET['page']) && $_GET['page'] != 'edit' && $_GET['page'] != 'trash') {
    ?>
        	<div class="account">
                <!-- message -->
                <div class="success"><span>Account has been created successfully.</span></div>
                <div class="errors"><span>Please fill all the fields.</span></div>
                <div class='loader'><img src='<?php echo $loader; ?>' height='25' width='25'></div>
                <div class='delete-account'><span>Account has been deleted successfully.</span></div>
        		<!-- Create Account Table Start -->
        		<table class="create-account table-style">
        			<thead>
        				<tr>
        					<th><span>Create Account</span></th>
        					<th>
        						<button type="button" name="create" class="btn" id="create-btn">Create</button>
        						<input type="hidden" name="userId" class="userId" value="<?php echo get_current_user_id(); ?>">
        						<!--<input type="hidden" name="updateDate" class="updateDate" value="">-->
        						<input type="hidden" name="createdDate" class="createdDate" value="<?php echo date("Y-m-d");?>">
        						<input type="hidden" name="url" class="url" value="<?php echo $url; ?>">
        					</th>
        				</tr>
        			</thead>
        			<tbody>
        				<tr>
        					<td><span>Account Name</span></td>
        					<td><input type="text" name="account-name" class="account-name" placeholder="account name"></td>
        				</tr>
        				<tr>
        					<td><span>Recording Currency</span></td>
        					<td>
        						<select name="rec-curr" class="rec-curr">
        							<option value="0">Select Recording Currency</option>
                                    <option value="SGD">SGD</option>
        							<option value="USD">USD</option>
        							<option value="HKD">HKD</option>
        							<option value="AED">AED</option>
        						</select>
        					</td>
        				</tr>
        				<tr>
        					<td><span>Description</span></td>
        					<td><textarea name="description" class="description" placeholder="description" maxlength="256"></textarea></td>
        				</tr>
        			</tbody>
        		</table>
        		<!-- Create Account Table End -->

        		<!-- All Account Table Start -->
        		<table class="all-account table-style">
        			<thead>
        				<tr>
        					<th colspan="4">All Accounts</th>
        				</tr>
                        <tr>
                            <th><span>Account Name</span></th>
                            <th><span>Recording Currency</span></th>
                            <th><span>Description</span></th>
                            <th><span>Action</span></th>
                        </tr>
        			</thead>
        			<tbody>
                    <?php 
                        $results = self::getAllAccounts();
                        foreach ($results as $value) {
                            $id             = $value->id;
                            $accountName    = $value->account_name;
                            $descriptions   = $value->description;
                            $recCurrency    = $value->recording_currency;


                            $edit = sprintf('<a href="?page=%s&id=%s">Edit</a>','edit', absint($id) );
                            $delete = sprintf('<a href="javascript:void(0);" class="trash" id='.$id.'>Trash</a>');
                            
                            echo "<tr id=".$id."><td>".$accountName."</td><td>".$recCurrency."</td><td>".$descriptions."</td><td>".$edit." ".$delete."</td></tr>";
                        }
                    ?>
        			</tbody>
        		</table>
        		<!-- All Account Table End -->
        	</div>
    <?php    
        }elseif (isset($_GET['page']) && $_GET['page'] == 'edit' ) {
            $editId = $_GET['id'];
            self::editAccount($editId);
        }else{
        }
    }

    /* fetch all accounts */
    public static function getAllAccounts(){
        
        global $wpdb;
        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        $results = $wpdb->get_results("SELECT * FROM $cj_user_portfolios WHERE user_id = ".get_current_user_id());
        return $results;
    }

    /* edit page functionality */
    public static function editAccount($id){

        global $wpdb;
        $loader         = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/ajax-loader.gif';
        $url            = admin_url().'admin-ajax.php';
        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        $results = $wpdb->get_results("SELECT * FROM $cj_user_portfolios WHERE id = ".$id);
    ?>
        <div class="account-update">
            <!-- message -->
            <div class="success"><span>Account has been updated successfully.</span></div>
            <div class="errors"><span>Please fill all the fields.</span></div>
            <div class='loader'><img src='<?php echo $loader; ?>' height='25' width='25'></div>

            <!-- Create Account Table Start -->
            <table class="update-account table-style">
                <thead>
                    <tr>
                        <th><span>Update Account</span></th>
                        <th>
                            <button type="button" name="update" class="btn" id="update-btn">Update</button>
                            <input type="hidden" name="userId" class="userId" value="<?php echo get_current_user_id();?>">
                            <input type="hidden" name="url" class="url" value="<?php echo $url; ?>">
                            <input type="hidden" name="id" class="id" value="<?php echo $id; ?>">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span>Account Name</span></td>
                        <td><input type="text" name="account-name" class="account-name" value="<?php echo $results[0]->account_name; ?>"></td>
                    </tr>
                    <tr>
                        <td><span>Recording Currency</span></td>
                        <td>
                            <select name="rec-curr" class="rec-curr">
                                <option value="0" <?php echo $results[0]->recording_currency == '0'?'SELECTED':''; ?>>Select Recording Currency</option>
                                <option value="SGD" <?php echo $results[0]->recording_currency == 'SGD'?'SELECTED':''; ?>>SGD</option>
                                <option value="USD" <?php echo $results[0]->recording_currency == 'USD'?'SELECTED':''; ?>>USD</option>
                                <option value="HKD" <?php echo $results[0]->recording_currency == 'HKD'?'SELECTED':''; ?>>HKD</option>
                                <option value="AED" <?php echo $results[0]->recording_currency == 'AED'?'SELECTED':''; ?>>AED</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><span>Description</span></td>
                        <td><textarea name="description" class="description" maxlength="256"><?php echo $results[0]->description;?></textarea></td>
                    </tr>
                </tbody>
            </table>
            <div class="back"><a href="<?php echo site_url()?>/index.php/accounts/">Back</a></div>
        </div>
    <?php
    }

    /* Delete account functionality */
    public static function deleteAccount(){
        if( isset($_POST['action']) ) {
            global $wpdb;
            $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
            $wpdb->delete( $cj_user_portfolios, array( 'id' => $_POST['id'] ), array( '%d' ) );
            echo "accountdeleted";
            die();
        }
    }

}/*class ends here*/
?>