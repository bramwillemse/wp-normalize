<?php 
class normalizeImages {

	/**
	* Constructor
	*
	* @since  1.0
	*/
    public function __construct() {				
		# Images
		add_filter( 'upload_mimes', array( &$this, 'cc_mime_types' ) ); // Allow SVG upload
    }

	/* 	========================================================================
	   	Images
	   	===================================================================== */

	/*
	 * Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
	 */
	public function remove_thumbnail_dimensions( $html ){
	    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
	    return $html;
	}

	/* 
	 * Allow SVG upload
	 */
	public function cc_mime_types( $mimes ){
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
			
}
new normalizeImages(); 
?>