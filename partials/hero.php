<?php
/**
 * Hero
 *
 * Functions are found in functions/hero.php
 */
 
if ( false === is_singular() ) {
	$post = thd_get_hero();
}

setup_postdata( $post );
thd_hero_style( $post );
?>
<section class="hero overlay_<?php echo get_field( 'hero_overlay' ); ?>">
	<div class="container">
		<h1><?php thd_hero_title( $post ); ?></h1>
		<p class="subtitle"><?php thd_hero_subtitle( $post ); ?></p>
	</div>
</section>
<?php wp_reset_postdata(); ?>