<?php

namespace BEA\WM\Admin;

use BEA\WM as Client;

/**
 * Class Main
 */
class Main {
	/**
	 *
	 */
	function __construct() {
		// Display data
		add_filter( 'attachment_fields_to_edit', array( __CLASS__, 'attachment_fields_to_edit' ), 10, 2 );

		// Save data
		add_filter( 'attachment_fields_to_save', array( __CLASS__, 'attachment_fields_to_save' ), 10, 2 );

		// Send to editor
		add_filter( 'media_send_to_editor', array( __CLASS__, 'media_send_to_editor' ), 10, 3 );
	}

	/**
	 * Add the form field to the right media
	 *
	 *
	 * @param array $form_fields
	 * @param \WP_Post $attachment
	 *
	 * @author Nicolas Juen
	 * @return array mixed
	 */
	public static function attachment_fields_to_edit( $form_fields, $attachment ) {
		// Do not add the watermark on no images
		if ( ! wp_attachment_is_image( $attachment->ID ) ) {
			return $form_fields;
		}

		$form_fields['bea-watermark'] = array(
			'show_in_edit'  => true,
			'show_in_modal' => true,
			'label'         => __( 'Watermark', 'bea-watermark' ),
			'value'         => $attachment->{BEA_WM_META_NAME},
			'input'         => 'textarea',
			'helps'         => __( 'Display a javascript watermark next to the image', 'bea-watermark' ),
		);

		// return the modified images
		return $form_fields;
	}


	/**
	 * Save the data if needed for the media
	 *
	 * @param array $attachment
	 * @param array $data
	 *
	 * @author Nicolas Juen
	 * @return array
	 */
	public static function attachment_fields_to_save( $attachment, $data ) {
		// Do not add the watermark on no images
		if ( ! wp_attachment_is_image( $attachment['ID'] ) ) {
			return $attachment;
		}

		// Remove if not used
		if ( isset( $data['bea-watermark'] ) ) {
			update_post_meta( $attachment['ID'], BEA_WM_META_NAME, sanitize_text_field( $data['bea-watermark'] ) );
		} else {
			delete_post_meta( $attachment['ID'], BEA_WM_META_NAME );
		}

		// return the modified images
		return $attachment;
	}

	/**
	 * Add the watermark parameter if needed
	 *
	 * @param string $html
	 * @param int $id
	 * @param \WP_Post $attachment
	 *
	 * @author Nicolas Juen
	 * @return string
	 */
	public static function media_send_to_editor( $html, $id, $attachment ) {
		if ( ! wp_attachment_is_image( $id ) || ! self::is_image_eligible( $id, $attachment['image-size'] ) ) {
			return $html;
		}

		$watermark = get_post_meta( $id, BEA_WM_META_NAME, true );
		if ( empty( $watermark ) ) {
			return $html;
		}

		return preg_replace( '|(<img)([^>]+)(\>)|', sprintf( '$1 data-watermark="%s" $2$3', $watermark ), $html );
	}

	/**
	 * Check if the image is eligible to watermark
	 *
	 * @param int $id : the attachment id
	 * @param string $size : the size to use
	 *
	 * @author Nicolas Juen
	 * @return bool
	 */
	public static function is_image_eligible( $id, $size ) {
		list( $img_src, $width, $height ) = image_downsize( $id, $size );
		$bea_watermark = get_post_meta( $id, BEA_WM_META_NAME, true );

		if ( ( $width < apply_filters( 'bea-watermark-min-width', 150 ) || empty( $bea_watermark ) ) && false === apply_filters( 'bea_watermark_skip', false, $id, $img_src, $width, $height ) ) {
			return false;
		}

		return true;
	}
}