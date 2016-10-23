<?php
/*
*
*
*	Gallery metabox will show a metabox on your custom post type , or post , pages etc . 
*	You can create a folder in your theme with name : "metabox" inside it create another one "gallery-metabox" . 
*   If you do this way , you can just include this file "gallery.php" from functions.php of your theme .
*
*	
*/

/*
	In your template inside a loop, grab the IDs of all the images with the following:
	$images = get_post_meta($post->ID, 'vdw_gallery_id', true);


	Then you can loop through the IDs and call wp_get_attachment_link or wp_get_attachment_image to display the images with or without a link respectively:

	foreach ($images as $image) {
	  echo wp_get_attachment_link($image, 'large');
	  // echo wp_get_attachment_image($image, 'large');
	}

*/

function gallery_metabox_enqueue($hook) {
    if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
      wp_enqueue_script('gallery-metabox', get_template_directory_uri() . '/inc/metabox/gallery-metabox/js/gallery-metabox.js', array('jquery', 'jquery-ui-sortable'));
      wp_enqueue_style('gallery-metabox', get_template_directory_uri() . '/inc/metabox/gallery-metabox/css/gallery-metabox.css');
    }
  }
  add_action('admin_enqueue_scripts', 'gallery_metabox_enqueue');

  function add_gallery_metabox($post_type) {
    $types = array('post'); // You can also custom post type here

    if (in_array($post_type, $types)) {
      add_meta_box(
        'gallery-metabox',
        'Gallery',
        'gallery_meta_callback',
        $post_type,
        'normal',
        'low'
      );
    }
  }
  add_action('add_meta_boxes', 'add_gallery_metabox');

  function gallery_meta_callback($post) {
    wp_nonce_field( basename(__FILE__), 'gallery_meta_nonce' );
    $ids = get_post_meta($post->ID, 'vdw_gallery_id', true);

    ?>
    <table class="form-table">
      <tr><td>
        <a class="gallery-add button" href="#" data-uploader-title="Add image(s) to gallery" data-uploader-button-text="Add image(s)">Add image(s)</a>
		<p>Don't forget to hold down CTRL and select multiple images to add to this box</p>
        <ul id="gallery-metabox-list">
        <?php if ($ids) : foreach ($ids as $key => $value) : $image = wp_get_attachment_image_src($value); ?>

          <li>
            <input type="hidden" name="vdw_gallery_id[<?php echo $key; ?>]" value="<?php echo $value; ?>">
            <img class="image-preview" src="<?php echo $image[0]; ?>">
            <a class="change-image button button-small" href="#" data-uploader-title="Change image" data-uploader-button-text="Change image">Change image</a><br>
            <small><a class="remove-image" href="#">Remove image</a></small>
          </li>

        <?php endforeach; endif; ?>
        </ul>

      </td></tr>
    </table>
  <?php }

  function gallery_meta_save($post_id) {
    if (!isset($_POST['gallery_meta_nonce']) || !wp_verify_nonce($_POST['gallery_meta_nonce'], basename(__FILE__))) return;

    if (!current_user_can('edit_post', $post_id)) return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if(isset($_POST['vdw_gallery_id'])) {
      update_post_meta($post_id, 'vdw_gallery_id', $_POST['vdw_gallery_id']);
    } else {
      delete_post_meta($post_id, 'vdw_gallery_id');
    }
  }
  add_action('save_post', 'gallery_meta_save');

?>