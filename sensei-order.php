<?php
/*========================================================================
Plugin Name: Sensei Re-ordering
Plugin URI: http://ainsworthEtc.com
Description: Sensei Re-ordering allows you to set the order of courses and lessons through a drag and drop interface.
Version: 1.02
Author: Chuck Ainsworth
Author URI: http://ainsworthEtc.com
Author Email: cdainsworth@gmail.com
Note: Modeled after "My Page Order". Requires Sensei by WooThemes to be loaded.

License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
/*  This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    For a copy of the GNU General Public License write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    Or, see <http://www.gnu.org/licenses/>.

=============================================================================*/

$results = array();

/*==============================
  Function Load the js libraries
==============================*/
function sensei_js_libs() {
      wp_enqueue_script('jquery');
      wp_enqueue_script('jquery-ui-core');
      wp_enqueue_script('jquery-ui-sortable');
}

/*==============================
  Function to set the meta links
==============================*/
function sensei_set_plugin_meta($links, $file) {
   $plugin = plugin_basename(__FILE__);

  // create link
   if ($file == $plugin) {
      return array_merge( $links, array(
         '<a href="' . mypageorder_getTarget() . '">' . __('Order Sensei', 'sensei_order') . '</a>',
         '<a href="http://wordpress.org/tags/my-page-order?forum_id=10">' . __('Support Forum', 'mypageorder') . '</a>',
         '<a href="http://www.ainsworthetc.com/plugins/">' . __('Donate', 'mypageorder') . '</a>'
      ));
   }
   return $links;
}
/*=================
  Sensei Order Menu
=================*/
function sensei_order_menu() {
  add_submenu_page( 'edit.php?post_type=lesson','Course Re-order', 'Course Re-Order','edit_pages', 'course_re-order', 'sensei_course_reorder' );
  add_submenu_page( 'edit.php?post_type=lesson','Lesson Re-order', 'Lesson Re-Order','edit_pages', 'lesson_re-order', 'sensei_lesson_reorder' );
}

/*=======================
  See if plugin is active
=======================*/
function is_active_plugin ($plugin_path) {

  $return_var = in_array( $plugin_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
  return $return_var;
}

/*===============================
  Function for reordering courses
===============================*/
function sensei_course_reorder() {

   $rows = sensei_course_Query();
   $results = sensei_order($rows, 'Order Courses', 'course' );
}

/*===============================
  Function for reordering lessons
================================*/
function sensei_lesson_reorder() {

   $rows = sensei_lesson_Query();
   $results = sensei_order($rows, 'Order Lessons', 'lesson' );
   return $results;
}

/*===============================
  Function for reordering quizzes
================================*/
function sensei_quiz_reorder() {

   $rows = sensei_quiz_Query();
   $results = sensei_order($rows, 'Order Quizzes', 'quiz' );
}

/*==============================
  Function to return course rows
==============================*/
function sensei_course_Query() {

   $posts_array = array();
   $post_args = array(  'post_type'  => 'course',
				   'numberposts' 		=> -1,
				   'orderby'          => 'menu_order',
				   'order'            => 'ASC',
				   'post_status'      => 'publish',
				   'include'          => '',
				   'exclude'          => '',
				   'suppress_filters' => false
				   );
   $posts_array = get_posts( $post_args );

   return $posts_array;
}

/*==============================
  Function to return lesson rows
==============================*/
function sensei_lesson_Query() {

   $lessons = course_lessons( '', 'any' );
   return($lessons);
}

/*==============================
  Function to get link to course
===============================*/
function sensei_getCourseLink($senseiID) {
global $wpdb;

   if ($senseiID !=0) {
	  $courses = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE ID = " . $senseiID, ARRAY_N);
	  $course = $courses[0];
      return "&nbsp;&nbsp;<input type='submit' class='button' id='btnLessonParent' name='btnLessonParent' value='" . __('Re-order ' . $course, 'sensei_order') ."' />";
   } else {

      return "";
   }
}

/*==============================
  Function to return quiz rows
==============================*/
function sensei_quiz_Query() {
   global $wpdb;

   return $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'quiz' AND post_status != 'trash' AND post_status != 'auto-draft' ORDER BY menu_order ASC");
}

/*=================================
  Function to return a specific row
=================================*/
function sensei_Query($sensiID) {
   global $wpdb;

   return $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID = $sensiID AND post_status != 'trash' AND post_status != 'auto-draft' ORDER BY menu_order ASC");
}

/*=======================================
  Set up message to return to Course
=======================================*/
function sensei_getReturnCourse($senseiID)
{
	if($senseiID != 0)
		return "&nbsp;&nbsp;<input type='submit' class='button' id='btnReturnParent' name='btnReturnParent' value='" . __('Return to Courses', 'sensei_order') ."' />";
	else
		return "";
}

/*=================================
  Function to return the sub levels
=================================*/
function sensei_findSubPages($type) {

	$post_args = array(  'post_type'  => 'course',
				   'numberposts' 		=> -1,
				   'orderby'          => 'menu_order',
				   'order'            => 'ASC',
				   'post_status'      => 'publish',
				   'include'          => '',
				   'exclude'          => '',
				   'suppress_filters' => false
				   );

	$subRowID   = "";
	$subPageStr = "";
	$IDs        = array();
	$courses = get_posts($post_args);

	foreach($courses as $row) {
       $subPageStr = $subPageStr . "<option value='$row->ID'>" . __($row->post_title) . "</option>";
       $subRowID   = $subRowID   . "<option value='$row->ID'>" . __($row->ID) . "</option>";
       $IDs[]        = __($row->ID);
	}

    return array($subRowID, $subPageStr);
}

/*=============================================
  Function to return lessons based on course ID
=============================================*/
function course_lessons($course_id, $post_status = 'publish') {

	$posts_array = array();
	$post_args = array(	'post_type' 		=> 'lesson',
						'numberposts' 		=> -1,
						'orderby'         	=> 'menu_order',
						'order'           	=> 'ASC',
						'meta_key'        	=> '_lesson_course',
						'meta_value'      	=> $course_id,
						'post_status'       => $post_status,
						'suppress_filters' 	=> 0
						);
	$posts_array = get_posts( $post_args );

	return $posts_array;

} // End course_lessons()

/*=======================================
  Switch page target depending on version
=======================================*/
//Switch page target depending on version
function sensei_order_getTarget() {
   global $wp_version;

   if (version_compare($wp_version, "1.000", ">"))
      return "edit.php?post_type=page&page=sensei-order";
   else
      return "edit-pages.php?page=sensei-order";
}

/*==========================
  Function to re-order items
==========================*/
function sensei_order($results, $title, $type) {
global $wpdb;

$senseiID = 0;

if (isset($_POST['btnSubPages'])) {
   $senseiID = $_POST['pages'];
} elseif (isset($_POST['hdnCourseID'])) {
   $senseiID = $_POST['hdnCourseID'];
}

if (isset($_POST['btnReturnParent'])) {
	$lessonsCourse = $wpdb->get_row("SELECT post_parent FROM $wpdb->posts WHERE ID = " . $_POST['hdnParentID'], ARRAY_N);
	$senseiID = $lessonsCourse[0];
}

if(isset($_GET['hideNote'])) {
   update_option('sensei_order_hideNote', '1');
}

$success = "";
if (isset($_POST['btnOrderPages'])) {
   $success = sensei_order_updateOrder();
}
?>

<div class='wrap'>
<form name="frmSenseiOrder" method="post" action="">
   <h2><?php _e('Sensei Order', 'sensei-order') ?></h2>
   <?php
   echo $success;
   if (get_option("sensei_order_hideNote") != "1")
   {  ?>
      <div class="updated">
         <strong><p><?php _e('If you like my plugin please consider donating. Every little bit helps me provide support and continue development.','sensei-order'); ?> <a href="http://ainsworthetc.com"><?php _e('Donate', 'sensei-order'); ?></a>&nbsp;&nbsp;<small><a href="<?php echo sensei_order_getTarget(); ?>&hideNote=true"><?php _e('No thanks, hide this', 'sensei-order'); ?></a></small></p></strong>
      </div>
   <?php
   }

   /* Check to see if we are doing something other than courses */
   if(($subPageStr == "" ) && ($type != 'course')) {

      list($subRowID, $subPageStr) = sensei_findSubPages($type);
      ?>

      <p> <?php _e('Initially all lessons are shown.  To filter by course, choose a course from the Courses drop down list, click on Order Lessons, and then order the lessons by dragging and dropping them into the desired order.', 'sensei-order') ?></p>
      <h3><?php _e('Courses', 'sensei-order') ?></h3>

      <select id="pages" name="pages">
         <?php echo $subPageStr; ?>
      </select>

      &nbsp;<input type="submit" name="btnSubPages" class="button" id="btnSubPages" value="<?php _e('Order Lessons', 'sensei_order') ?>" />

      <?php
      $senseID = $_POST['pages'];
      $results = course_lessons($senseID);
   }

   /* display the order type */
   if ($type == 'course') { ?>
	   <h3><?php _e('Order Courses', 'sensei-order') ?></h3>
   <?php } else { ?>
	   <h3><?php _e('Order Lessons', 'sensei-order') ?></h3>
   <?php } ?>

   <ul id="SenseiOrderList">

   <?php
   /* create the display list */
   foreach($results as $row)
      echo "<li id='id_$row->ID' class='lineitem'>".__($row->post_title)."</li>";
   ?>
   </ul>

   <?php
   /* Display the Click button */
   if ($type == 'course')  { ?>
      <input type="submit" name="btnOrderPages" id="btnOrderPages" class="button-primary" value="<?php _e('Click to Re-order Courses', 'sensei_order') ?>" onclick="javascript:orderPages(); return true;" />
   <?php } else {
      $courses = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE ID = " . $senseiID, ARRAY_N);
      $course = '--' . $courses[0] . '--'; ?>

      <input type="submit" name="btnOrderPages" id="btnOrderPages" class="button-primary" value="<?php _e('Click to Re-order ' . $course, 'sensei_order') ?>" onclick="javascript:orderPages(); return true;" />
   <?php } ?>

   <?php
   /* Reset the return to course button */
   echo sensei_getReturnCourse($senseiID); ?>

   &nbsp;&nbsp;<strong id="updateText"></strong>
   <br /><br />
   <p>
   <a href="http://ainsworthetc.com/sensei-order"><?php _e('Plugin Homepage', 'sensei_order') ?></a>&nbsp;|&nbsp;<a href="http://www.ainsworthetc.com/plugins/"><?php _e('Donate', 'sensei_order') ?></a>&nbsp;|&nbsp;<a href="http://wordpress.org/tags/my-page-order?forum_id=10"><?php _e('Support Forum', 'sensei-order') ?></a>
   </p>
   <input type="hidden" id="hdnSenseiOrder" name="hdnSenseiOrder" />
   <input type="hidden" id="hdnCourseID" name="hdnCourseID" value="<?php echo $senseiID; ?>" />
</form>
</div>

<style type="text/css">
   #SenseiOrderList {
      width: 80%;
      border:1px solid #B2B2B2;
      margin:10px 10px 10px 0px;
      padding:5px 10px 5px 10px;
      list-style:none;
      background-color:#fff;
      -moz-border-radius:3px;
      -webkit-border-radius:3px;
   }

   li.lineitem {
      border:1px solid #B2B2B2;
      -moz-border-radius:3px;
      -webkit-border-radius:3px;
      background-color:#F1F1F1;
      color:#000;
      cursor:move;
      font-size:13px;
      margin-top:5px;
      margin-bottom:5px;
      padding: 2px 5px 2px 5px;
      height:1.5em;
      line-height:1.5em;
   }

   .sortable-placeholder{
      border:1px dashed #B2B2B2;
      margin-top:5px;
      margin-bottom:5px;
      padding: 2px 5px 2px 5px;
      height:1.5em;
      line-height:1.5em;
   }
</style>


<script type="text/javascript">
// <![CDATA[

   function senseiorderaddloadevent(){
      jQuery("#SenseiOrderList").sortable({
         placeholder: "sortable-placeholder",
         revert: false,
         tolerance: "pointer"
      });
   };

   addLoadEvent(senseiorderaddloadevent);

   function orderPages() {
      jQuery("#updateText").html("<?php _e('Updating' . $title . ' Order...', 'sensei_order') ?>");
      jQuery("#hdnSenseiOrder").val(jQuery("#SenseiOrderList").sortable("toArray"));
   }

// ]]>
</script>
<?php
}

/*================
  Update the order
================*/
function sensei_order_updateOrder() {
   if (isset($_POST['hdnSenseiOrder']) && $_POST['hdnSenseiOrder'] != "") {
      global $wpdb;

      $hdnSenseiOrder = $_POST['hdnSenseiOrder'];
      $IDs = explode(",", $hdnSenseiOrder);
      $result = count($IDs);

      for($i = 0; $i < $result; $i++)
      {
         $str = str_replace("id_", "", $IDs[$i]);
         $wpdb->query("UPDATE $wpdb->posts SET menu_order = '$i' WHERE id ='$str'");
      }

      return '<div id="message" class="updated fade"><p>'. __('Page order updated successfully.', 'sensei_order').'</p></div>';
   }
   else
      return '<div id="message" class="updated fade"><p>'. __('An error occured, order has not been saved.', 'sensei_order').'</p></div>';
}

/*===========================
  Load the tranlations tables
===========================*/
function sensei_order_loadtranslation() {
	load_plugin_textdomain('sensei-order', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
}

/*======================================
  Get path to plugins and use it
======================================*/
$woothemes = "woothemes-sensei/woothemes-sensei.php";
if ( is_active_plugin ( $woothemes ) ) {
   add_action('admin_menu', 'sensei_order_menu' );
   add_action('admin_print_scripts', 'sensei_js_libs');
   add_action('init', 'sensei_order_loadtranslation');
} else {
   $ordering = "sensei-order/sensei-order.php";
   if ( is_active_plugin ( $ordering ) ) {
     register_deactivation_hook( __FILE__, 'sensei-order' );
   } else {
     exit("This plugin works with Woo-Themes Sensei plugin.  Please install and activate the Sensei plugin.");
   }
}

?>