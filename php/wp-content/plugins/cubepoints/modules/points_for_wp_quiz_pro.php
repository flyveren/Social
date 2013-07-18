<?php
 
// Register the module
cp_module_register( __( 'Points for Wp Pro Quiz', 'cp' ) , 'wp_pro_quiz' , '1.0', '<a href="http://www.taraj.sk">Taraj.sk</a>', 'http://www.taraj.sk', 'http://taraj.sk/' , __( 'Points for Wp Pro Quiz!!', 'cp' ), 1 );
 
// On installation, we set the default values for our custom points transactions.
function cp_wp_pro_quiz_install()
{
    add_option( 'cp_wp_pro_quiz_value1', 10 );

}
add_action( 'cp_module_wp_pro_quiz_activate', 'cp_wp_pro_quiz_install' );
 
// If the module is active.
if ( cp_module_activated( 'wp_pro_quiz' ) )
{
    // Add a function to display the form inputs.
    function cp_wp_pro_quiz_config()
    {
 
    ?>
        <br />
        <h3><?php _e( 'Points for Wp Pro Quiz','cp' ); ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="cp_wp_pro_quiz_value1"><?php _e( 'Points for quiz', 'cp' ); ?>:</label>
                </th>
                <td valign="middle">
                    <input type="text" id="cp_wp_pro_quiz_value1" name="cp_wp_pro_quiz_value1" value="<?php echo( get_option( 'cp_wp_pro_quiz_value1' ) ); ?>" size="30" />
                </td>
            </tr>
            
        </table>
 
    <?php
 
    }
    add_action( 'cp_config_form','cp_wp_pro_quiz_config' );
 
    // Create a function to process the form inputs when the form is submitted.
    function cp_wp_pro_quiz_config_process()
    {
        // We update the options with the submitted values. We cast them as integers.
        update_option( 'cp_wp_pro_quiz_value1', (int)$_POST['cp_wp_pro_quiz_value1'] );

    }
    add_action( 'cp_config_process','cp_wp_pro_quiz_config_process' );
     
}



add_action('wp_pro_quiz_completed_quiz','my_wp_pro_quiz_add_cp');
// Log
add_action('cp_logs_description','cp_wp_pro_quiz_log', 10, 4);
function cp_wp_pro_quiz_log($type,$uid,$points,$data){
if($type!='cp_wp_pro_quiz') { return; }
_e('Points for completing the quiz','cp_buddypress');
}




function my_wp_pro_quiz_add_cp() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_wp_pro_quiz', $bp->loggedin_user->id, get_option('cp_wp_pro_quiz_value1'), "");		
	}

}


 
?>