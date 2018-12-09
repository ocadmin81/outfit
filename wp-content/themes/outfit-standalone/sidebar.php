<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */
$pId = 'page-id-'.get_the_ID();
if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area static-sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Blog Sidebar', 'twentyseventeen' ); ?>">
	<div class="widget-title">תפריט מידע</div>
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside><!-- #secondary -->
<script type="text/javascript">
	jQuery( document ).ready(function() {
		jQuery( ".static-sidebar li" ).each(function( index ) {				
			var classN = $j(this).attr('class');
			if('<?php echo $pId; ?>' == classN)
				jQuery(this).addClass('active');
		});
	});	
</script>