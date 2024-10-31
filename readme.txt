=== Sensei Re-Ordering ===
Contributors: Chuck Ainsworth
Donate link: http://ainsworthetc.com/plugins
Tags: sensei,course,lesson,module
Requires at least: 2.8
Tested up to: 3.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin Sensei Re-ordering allows you to re-order courses and lessons
through a drag and drop interface that have been created with Wootheme-
Sensei

== Description ==

Plugin Sensei Re-ordering allows you to re-order courses and lessons,
created by WooThemes-Sensei, through a drag and drop interface. This
plugin follows similiar principles used in plugin 'My Page Order' and
has been tested as best as possible with WooTheme-Sensei and WooThemes
Sensei-module.

== Installation ==

Extract the zip file and just drop the contents in the wp-
content/plugins/ directory of your WordPress installation and then
activate the Plugin from Plugins page.

After activation, Course Re-Order and Lessons Re-Order will be found in
the Sensei Lessons sub-menu.

== Donation ==

If you like the plugin, consider showing your appreciation by saying
thank you or making a [small donation](http://ainsworthetc.com/plugins).

== Known Issues ==

I have not created the translation table for other languages, but the
code is written for translation.

== Disclaimer ==

Ainsworth, Etc., LLC does not warrant the accuracy or completeness of
any plugin and is not responsible for mistakes or omissions that may be
caused as a result of using it.

== Changes in Woothemes-Sensi to support this plugin. ==

To support this plugin, there are changes that will need to be made in
the Woothemes-Sensi files to support the change in sort orders.

1. Replace the file "./wp_content/plugins/woothemes-
sensei/templates/wrappers/pagination-posts.php" with the one included
zip file.

2. Replace the file "./wp_content/plugins/woothemes-
sensei/inc/woothemes-sensei-template.php" with the one included zip
file.

3. Replace the file "./wp_content/plugins/woothemes-
sensei/classes/class-woothemes-sensei-course.php" with the one included
zip file.

Note: If you have altered any of the above files,make the changes as
noted below.


== Following are the changes made to the files ==

'./woolthemes-sensei/classes/class-woothemes-sensei-course.php'
---------------------------------------------------------------

   lines 492, 493 were changed:
   from  'orderby' => 'date',       'order'  => 'DESC',
   to    'orderby' => 'menu_order', 'order'  => 'ASC',

   lines 521, 522 were changed:
   from  'orderby' => 'date',       'order'  => 'DESC',
   to    'orderby' => 'menu_order', 'order'  => 'ASC',

   lines 572, 573 were changed:
   from  'orderby' => 'date',       'order'  => 'DESC',
   to    'orderby' => 'menu_order', 'order'  => 'ASC',

   lines 604, 605 were changed:
   from  'orderby' => 'date',       'order'  => 'DESC',
   to    'orderby' => 'menu_order', 'order'  => 'ASC',

   lines 617, 618 were changed:
   from  'orderby' => 'date',       'order'  => 'DESC',
   to    'orderby' => 'menu_order', 'order'  => 'ASC',

'./woothemes-sensei/inc/woothemes-sensei-template.php'
------------------------------------------------------

   lines 38,39 from
      set_query_var( 'orderby', 'date' );
      set_query_var( 'order', 'DESC' );
   to
      set_query_var( 'orderby', 'menu_order' );
      set_query_var( 'order', 'ASC' );

   lines 42.43 from
      set_query_var( 'orderby', 'date' );
      set_query_var( 'order', 'DESC' );
   to
      set_query_var( 'orderby', 'menu_order' );
      set_query_var( 'order', 'ASC' );

   lines 49.50 from
      set_query_var( 'orderby', 'date' );
      set_query_var( 'order', 'DESC' );
   to
      set_query_var( 'orderby', 'menu_order' );
      set_query_var( 'order', 'ASC' );

   lines 56.57 from
      set_query_var( 'orderby', 'date' );
      set_query_var( 'order', 'DESC' );
   to
      set_query_var( 'orderby', 'menu_order' );
      set_query_var( 'order', 'ASC' );

   Following function inserted at line 489
   ---------------------------------------
    /**
    * sensei_get_prev_next_courses Returns the next and previous Courses
    * since 1.0.9
    * @param  integer $course_id
    * @return array $return_values
    */
    function sensei_get_prev_next_courses( $course_id = 0 ) {
      global $woothemes_sensei;

      $return_values = array();
      $return_values['prev_lesson'] = 0;
      $return_values['next_lesson'] = 0;

      if ( 0 < $course_id ) {
         // Get the List of Courses
         $args = array( 'post_type'       => 'course',
                     'numberposts'     => -1,
                     'orderby'            => 'menu_order',
                     'order'              => 'ASC',
                     'post_status'        => 'publish',
                     'suppress_filters'   => true
                     );

         $courses = get_posts( $args );

         // Index the Lessons
         if ( 0 < count( $courses ) ) {
            $found_index = false;
            foreach ($courses as $course){
               if ( $found_index && $return_values['next_lesson'] == 0 )
               {
                  $return_values['next_lesson'] = $course->ID;
               } // End If Statement

               if ( $course->ID == $course_id ) {
                  // Is the current post
                  $found_index = true;
               } // End If Statement

               if ( !$found_index ) {
                  $return_values['prev_lesson'] = $course->ID;
               } // End If Statement
            } // End For Loop
         } // End If Statement
      } // End If Statement
      return $return_values;
   } // End sensei_get_prev_next_courses()


'./woothemes-sensei/templates/wrappers/pagination-posts.php'
------------------------------------------------------------

   Re-written



== Frequently Asked Questions ==

This is a new plugin.

== Screenshots ==

1. Course Re-ording allows you to re-order WooThemes-Sensei courses
through a drag and drop interface.

2. Lesson Re-Order allows you to re-order WooThemes-Sensei lessons
through a drag and drop interface. Initally all lessons shown. You can
then filter the lessons down to the course itself, thus greatly reducing
the lessons list.

== Upgrade Notice ==

1st submisstion.

== Changelog ==

1.00 - Inital Version

1.01 - Fixed problem when WooSensei was updated, Sensei-Ordering would
lock in a continuous reporting error. Changed Re-ordering button to
show what lesson what being updated.


1.02 - Tested with 3.9 and donation had incorrect web address.
