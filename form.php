<?php 
global $wp;
$current_url = home_url(add_query_arg(array(),$wp->request));
$error = array();  

if ( isset( $_POST['submitted'] ) && isset( $_POST['add_listing_nonce_field'] ) && wp_verify_nonce( $_POST['add_listing_nonce_field'], 'add_listing_nonce' ) ) {
	
	
	// Do the code for Post Title Here
	$post_title = "Post Title";
	
	// Do the code for Post Description Here
	$post_description = "Post Description";
	
	
	// Check Gallery Images 
			
	if ( $_FILES["gallery_images"]  ){
		 // If you want to store image count 
		 $count = $_FILES["gallery_images"];
		 
		 
		  require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		  require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		  require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		
		
		$uploaded_featured_image = $_FILES['gallery_images']; /*Receive the uploaded image from form*/
		$returned_featured_image_ids = uploaded_galllery_images($uploaded_featured_image); /*Call image uploader function*/
	}else{
		 $error[] = __('Gallery images are required', 'bonuin_theme');
	}
	
	
	if ( count($error) == 0 ) {
		$post = array(
			'post_title' => wp_strip_all_tags( $post_title ),
			'post_content' => $post_description,
			'post_status' => 'publish',			// Choose: publish, preview, future, etc.
			'post_type' => 'post'  // Use a custom post type if you want to
		);
		
		$post_id = wp_insert_post( $post );
		
		// Upload Gallery Images
		update_post_meta($post_id, 'vdw_gallery_id', $returned_featured_image_ids);
		
		
		$added_post_link = get_post_permalink( $post_id );
		//Redirect browser and also add Post Id - Which in this case we are going to use for the button when clicked it sends to post published
		wp_redirect( $current_url.'?post-published=true&post_id='.$post_id ); exit;
		
	}
	
}
?>

<?php if(isset($_GET['post-published'])){
			
			if ( $_GET['post-published'] == 'true' ) { ?> 
					<div class="notification_style_2 updated">
						<p>Your post has been published.</p>
					</div> 
					<div class="listing_added_extras">
						<?php 
							// Get Post ID from URL paramether
							$added_new_post_id = $_GET['post_id'];
							// Get URL of POST ID from URL PARAMETHER
							$added_post_link = get_post_permalink( $added_new_post_id );
						?>
						<ul class="list-inline">
								<li><a href="<?php echo $added_post_link;?>" class="simple_link" target="_blank">View Listing</a></li>
						</ul>
					
					</div>
					
						
				<?php }else{?>
					<div class="notification_style_2 error">
						<p>Please check all required boxes.</p>
						<?php if ( count($error) > 0 ) echo '<div class="notification error"><p>' . implode("<br />", $error) . '</p></div>'; ?>
					</div> 
					
				
				<?php 
					
				}
} ?>
			
			
<form method="post"  enctype="multipart/form-data"  >


	<div class="form_group upload_image_section">
			<div class="listing_gallery_image_upload">
					<input type="file" name="gallery_images[]" id="add_listing_image_uploader" multiple="multiple" >
			</div>
			
	</div>
							
							
	
	 <input type="hidden" name="submitted" id="submitted" value="true" />
	<!--//This will help us against any security issues and prevent any malicious attacks.-->
	<?php wp_nonce_field( 'add_listing_nonce', 'add_listing_nonce_field' ); ?>
	<button type="submit" class="next_step">Publish Listing</button>
																		

</form>
