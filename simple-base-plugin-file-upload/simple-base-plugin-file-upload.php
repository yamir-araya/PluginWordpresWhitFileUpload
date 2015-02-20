<?php
/**
 * Plugin Name: Simple Base Plugin File Upload
 * Plugin URI: http://letplus.cl
 * Description: Simple Base Plugin With File Upload From Wordpress
 * Version: 1.0.0
 * Author: Yamir Araya
 * Author URI: http://letplus.cl
 * License: GPL2
 * Referencia http://premium.wpmudev.org/blog/wordpress-plugin-development-guide/
 * Referencia media Upload: http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
 * 							https://codestag.com/how-to-use-wordpress-3-5-media-uploader-in-theme-options/
 */


/**
* Load media files needed for Wordpress Uploader
*/
add_action( 'admin_enqueue_scripts', 'load_wp_media_files_for_upload' );
function load_wp_media_files_for_upload() {
  wp_enqueue_media();
}
/**
* Load media files needed for Wordpress Uploader
*/


/**
* Adding menu to the admin
*/
add_action('admin_menu', 'simple_base_menu_file_upload_menu');
function simple_base_menu_file_upload_menu() {
	add_menu_page('Simple File Upload ', 'Simple File Upload', 'administrator', 'simple-base-plugin-file-upload-settings', 'simple_base_menu_file_html', 'dashicons-media-spreadsheet');
	//Other Dash Icons for Wordpress Menu can be found here
	//https://developer.wordpress.org/resource/dashicons
}
/**
* Adding menu to the admin
*/



/**
* Generating the html for de plugin page. Called from the 5th paremeter of the last function
*/
function simple_base_menu_file_html() {
  ?>
    <div class="wrap">
    	<h2>Simple File Uploader</h2>
        <?php 
			if (isset($_GET["load_file"])):
				if ($_GET["load_file"] == "ok"):
					?><div class="updated below-h2" id="message"><p>File Uploaded!</p></div><?php
				else:
					?><div class="error below-h2" id="message"><p>File not uploaded.</p></div><?php
				endif;
			endif;
			
			if (isset($_GET["err"])) {
				?><div class="error below-h2" id="message"><p>Error. Detail: <?php echo $_GET["err"]; ?>.</p></div><?php
			}
		?>
        
        
        
        <form id="form_file" method="post" action="<?php echo admin_url( 'admin.php' ); ?>" enctype="multipart/form-data">
            <input type="hidden" name="action" value="process_file" />
            <table class="form-table">
                <tr valign="top">
                	<th scope="row">
                        <div class="uploader">
                            <input  id="file_upload_button" class="button" name="file_upload_button" type="text" value="Seleccionar archivo" />
                        </div>
					</th>
                </tr>
                <tr valign="top"> 
                    <td>Actual selected: <span  id="file_upload_text" ></span></td>
                </tr>
                <tr valign="top">
                    <td scope="row">Last uploaded file: <?php echo esc_attr( get_option('last_uploaded_file') ); //Getting last saved file from options ?>
                    <input  id="file_upload" name="last_uploaded_file" type="hidden" value="<?php echo esc_attr( get_option('last_uploaded_file') ); ?>" /></td>
                </tr>
            </table>        
            <?php submit_button(); ?>
        </form>
    </div> 
    
	<script>
    // Uploading files
    var file_frame;
    jQuery('#file_upload_button').live('click', function( event ){
        event.preventDefault();
        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }
        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
															//frame: 'post',
                                                            title: jQuery( this ).data( 'uploader_title' ),
                                                            button: {
                                                                text: jQuery( this ).data( 'uploader_button_text' ),
                                                            },
                                                            multiple: false // Set to true to allow multiple files to be selected
                                                            });
                                                            
        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {		 
            var selection = file_frame.state().get('selection');
            selection.map( function( attachment ) {			 
                attachment = attachment.toJSON();
                //alert(JSON.stringify(attachment));
                // Do something with attachment.id and/or attachment.url here
				
				var ext = attachment.url.split('.').pop();
				if (ext == 'xls' || ext == 'xlsx') {
                	jQuery('#file_upload').val(attachment.url);
                	jQuery('#file_upload_text').html(attachment.url);
				} else {
					alert('El archivo selecionado no tiene formato xls o xlsx');
				}
            });
        }); 
        // Finally, open the modal
        file_frame.open();
    }); 
	
	
	jQuery(document).ready(function(e) {
		jQuery('#submit_form_file').click( function (e) {
			var url_file = jQuery('#file_upload_text').html();
			var id_campaign = jQuery('#campaign_id').val();
			if (id_campaign != "0") {
				if (url_file != "") {
					//FILE EXTENSION CHECK
					var ext = url_file.split('.').pop();
					if (ext == 'xls' || ext == 'xlsx') {
						jQuery('#form_file').submit();
					} else {
						alert('El archivo selecionado no tiene formato xls o xlsx');
					}
				} else {
					alert('Seleccione un archivo');
				}
			} else {
					alert('Seleccione una campaña');
			}
		});
    });
	
    </script>
  <?php
}
/**
* Generating the html for de plugin page. Called from the 5th paremeter of the last function
*/




/**
* Registering Setting and Group
*/
add_action( 'admin_init', 'simple_base_menu_file_settings' );
function simple_base_menu_file_settings() {
	register_setting( 'simple-base-menu-file-settings-group', 'last_uploaded_file' );
}
/**
* Registering Setting and Group
*/



/**
* Procesing Action and Saving Value
* Note: process_file value in the hidden input on the form on the function simple_base_menu_file_html()
*       that is who call this function admin_action_process_file()
*/
add_action( 'admin_action_process_file', 'process_file' );
function process_file()
{
    // Do your stuff here
	if (isset($_POST["last_uploaded_file"])) {
		
		
		$last_uploaded_file = $_POST["last_uploaded_file"];
		//saving the value
		update_option( 'last_uploaded_file', esc_sql($last_uploaded_file));  //Scaping SQL for inyection
		
		wp_redirect(get_bloginfo('url')."/wp-admin/admin.php?page=simple-base-plugin-file-upload-settings&load_file=ok",301);
		exit;
	} else {
		wp_redirect(get_bloginfo('url')."/wp-admin/admin.php?page=simple-base-plugin-file-upload-settings&load_file=fail",301);
		exit;
	}	
}
/**
* Procesing Action and Saving value
*/