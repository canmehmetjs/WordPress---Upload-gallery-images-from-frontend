<?php 
// Put this code in your functions.php or in my case I've included a file in functions php that requires this kind of extra functions.

function uploaded_galllery_images($upload)
{
	
 $number_of_images = count($upload['name']);

 $images_array;
 
 
 for ($i = 0; $i < $number_of_images; $i++) {
    $images_array[$i]['name'] =  $upload['name'][$i];
    $images_array[$i]['type'] =  $upload['type'][$i];
    $images_array[$i]['tmp_name'] =  $upload['tmp_name'][$i];
    $images_array[$i]['error'] =  $upload['error'][$i];
    $images_array[$i]['size'] =  $upload['size'][$i];
}  
	
 $uploads = wp_upload_dir(); /*Get path of upload dir of wordpress*/

 
 
 $attachment_ids = array();
 
 
 if (is_writable($uploads['path'])){
	 
  for ($i = 0; $i < $number_of_images; $i++) {	 
	 
	  if ((!empty($images_array[$i]['name']))){
		   if ($images_array[$i]['name']) {
			$file=handle_image_upload($images_array[$i]); /*Call our custom function to ACTUALLY upload the image*/

			$attachment = array  /*Create attachment for our post*/
			(
			 
			  'post_mime_type' => $file['type'],  /*Type of attachment*/
			  'post_content'   => '',
			  'post_status'    => 'inherit'
			);

			$aid = wp_insert_attachment($attachment, $file['file']);  /*Insert post attachment and return the attachment id*/
			$a = wp_generate_attachment_metadata($aid, $file['file'] );  /*Generate metadata for new attacment*/

			if ( !is_wp_error($aid) ) 
			{
			 wp_update_attachment_metadata($aid, wp_generate_attachment_metadata($aid, $file['file'] ) );  /*If there is no error, update the metadata of the newly uploaded image*/
			 $attachment_ids[$i] = $aid;
			}
		   }
	  }
	  else {
	  // echo 'Please upload the image.';
	  }
  }  
 }
 return $attachment_ids;
}






  
function handle_image_upload($upload)
{
 global $post;
		$result = apply_filters( 'file_is_displayable_image', $upload['tmp_name'] ); 
        if ($result != '') /*Check if image*/
        {
            /*handle the uploaded file*/
            $overrides = array('test_form' => false);
            $file=wp_handle_upload($upload, $overrides);
        }
 return $file;
}