<?php 
/**
 *	@functions <func:get_header>, <func:have_posts>, <func:the_post>,<func:the_title>, <func:the_content>, <func:get_sidebar>, <func:get_footer> 
 *	@description 
*/
?>
<?php get_header(); ?>
<?php if (have_posts()): the_post(); ?>
	<?php the_title(); ?>
	<?php the_content(); ?>
<?php endif; ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>