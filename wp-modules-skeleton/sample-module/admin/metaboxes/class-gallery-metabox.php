<?php

namespace WP_Modules_Skeleton\Sample_Module\Admin\Metaboxes;

class Gallery_Metabox{

	public function __construct( $post_type = 'post' ) {
        $this->post_type = $post_type;
	    add_action( 'admin_enqueue_scripts', [ $this, 'add_media' ] );
	    add_action( 'add_meta_boxes', [ $this, 'add_gallery_metabox' ] );
	    add_action( 'save_post', [ $this, 'gallery_meta_save' ] );
    }

    public function add_media($hook){
        if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
            wp_enqueue_script('gallery-metabox', WPMU_PLUGIN_URL . '/wp-modules-skeleton/shop-module/admin/metaboxes/media/js/gallery-metabox.js', array('jquery', 'jquery-ui-sortable'));
            wp_enqueue_style('gallery-metabox', WPMU_PLUGIN_URL . '/wp-modules-skeleton/shop-module/admin/metaboxes/media/css/gallery-metabox.css');
        }
    }

    public function add_gallery_metabox() {
	    add_meta_box( 'gallery-metabox', __( 'Gallery' ), [ $this, 'gallery_meta_callback' ], $this->post_type, 'normal', 'high' );
    }

    function gallery_meta_callback($post) {
	    wp_nonce_field( basename( __FILE__ ), 'gallery_meta_nonce' );
	    $ids = get_attached_media( 'image', $post->ID );
        ?>
        <table class="form-table">
            <tr>
                <td>
                    <a class="gallery-add button" href="#" data-uploader-title="Add image(s) to gallery" data-uploader-button-text="Add image(s)">Add image(s)</a>
                    <ul id="gallery-metabox-list">
	                    <?php if ( $ids ) : foreach ( $ids as $key => $value ) : ?>
                            <?php $image = wp_get_attachment_image_src($value->ID);?>
                            <li>
                                <input type="hidden" name="attached[<?php echo $key; ?>]" value="<?php echo $value->ID; ?>">
                                <img class="image-preview" src="<?php echo $image[0]; ?>">
                                <small><a class="remove-image" href="#">Remove image</a></small>
                            </li>
                        <?php endforeach; endif; ?>
                    </ul>
                </td>
            </tr>
        </table>
    <?php
    }

	public function gallery_meta_save( $post_id ) {

	    if ( ! isset( $_POST['gallery_meta_nonce'] ) || ! wp_verify_nonce( $_POST['gallery_meta_nonce'], basename( __FILE__ ) ) ) {
		    return;
	    }

	    if ( ! current_user_can( 'edit_post', $post_id ) ) {
		    return;
	    }

	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		    return;
	    }

		if ( $_POST['attached'] ) {
			$attached_data = $_POST['attached'];
			if ( $attached_data['delete'] ) {
				foreach ( $attached_data['delete'] as $id ) {
					wp_update_post( [ 'ID' => $id, 'post_parent' => 0 ] );
				}
			}
			unset( $attached_data['delete'] );
			foreach ( $attached_data as $menu_order => $attachment_id ) {
				if ( ! current_user_can( 'edit_post', $attachment_id ) ) {
					continue;
				}

				if ( ! $attachment = get_post( $attachment_id ) ) {
					continue;
				}

				if ( 'attachment' != $attachment->post_type ) {
					continue;
				}

				wp_update_post( [
					'ID'          => $attachment_id,
					'menu_order'  => $menu_order,
					'post_parent' => $post_id
				] );
			}
		}
	}
}