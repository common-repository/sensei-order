<?php
/**
 * Pagination - Posts
 *
 * @author 		WooThemes
 * @package 	Sensei/Templates
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

global $post;

$nav_id_array = sensei_get_prev_next_courses( $post->ID );
$previous_course_id = absint( $nav_id_array['prev_lesson'] );
$next_course_id = absint( $nav_id_array['next_lesson'] );

// Output HTML
if ( ( 0 < $previous_course_id ) || ( 0 < $next_course_id ) ) { ?>
	<nav id="post-entries" class="fix">
        <?php if ( 0 < $previous_course_id ) { ?><div class="nav-prev fl"><a href="<?php echo esc_url( get_permalink( $previous_course_id ) ); ?>"
        rel="prev"><span class="meta-nav"></span> <?php echo get_the_title( $previous_course_id ); ?></a></div><?php } ?>
        <?php if ( 0 < $next_course_id )     { ?><div class="nav-next fr"><a href="<?php echo esc_url( get_permalink( $next_course_id ) ); ?>"
        rel="prev"><span class="meta-nav"></span> <?php echo get_the_title( $next_course_id ); ?>    </a></div><?php } ?>
    </nav><!-- #post-entries -->
<?php } ?>
