<?php
/*
Plugin Name: Gaxx Keywords
Plugin URI: http://www.gaxx.co.uk/gaxx-keywords
Description: Create keywords and description metatags on-the-fly for pages based on a number of criteria.
Version: 0.2
Author: GAxx
Author URI: http://www.gaxx.co.uk/
*/

function akg_notify () {
	echo '<p>You are using <strong><a href="http://www.gaxx.co.uk/gaxx-keywords" target="_blank">Gaxx Keywords Plugin</a></strong> to improve your SEO</p>';
}

function akg_parse ($str) {
	$parsed = str_replace(" ",",",$str);
	if (strpos($str," ")) return $str.",".$parsed;
	else return $str;
}

function akg_act () {
	$name = get_option("blogname");
	$desc = get_option("blogdescription");
	$stat1 = get_option("kw_format_static1");        
	$stat2 = get_option("kw_format_static2");        

        $title = "";
        $cat = "";
        $tag = "";

        $keywords = "";
        $description = "";
        $summary = "";
	
	if (is_tag()) {
          $title = single_tag_title('',false);
          $tag = single_tag_title('',false);
          $keywords = get_option('kw_format_tag') ;
          $description = get_option('kw_format_tag_desc') ;         
        }
	if (is_category()) {
          $title = single_cat_title('',false);
          $cat = single_cat_title('',false);;
          $keywords = get_option('kw_format_cat') ;
          $description = get_option('kw_format_cat_desc') ;         
        }
	if (is_single() || is_page()) {
		$p = get_query_var("p");
		$post = get_post($p);
		$title = $post->post_title; /* single_post_title('',false); */
		
		$cats = get_the_category($post->ID);
		if (is_array($cats)) {
			foreach ($cats as $cats) {
				$cat .= ",".$cats->name;
			}
		}
		
		$tags = get_the_tags($post->ID);
		if (is_array($tags)) {
			foreach ($tags as $tags) {
				$tag .= ",".$tags->name;
			}
		}
		
		$summary = substr(strip_tags($post->post_content),0,200);
	        if (is_single()) {
                   $keywords = get_option('kw_format_post') ;
                   $description = get_option('kw_format_post_desc') ;         
                }
           	if (is_page()) {
                   $keywords = get_option('kw_format_page') ;
                   $description = get_option('kw_format_page_desc') ;         
                }
	}
	if (is_home()) {
            $keywords = get_option('kw_format_home') ;
            $description = get_option('kw_format_home_desc') ;
	}
        $keywords = str_replace("[name]", $name, $keywords);
        $keywords = str_replace("[desc]", akg_parse($desc), $keywords);
        $keywords = str_replace("[page]", akg_parse($title), $keywords);
        $keywords = str_replace("[tags]", $tag, $keywords);
        $keywords = str_replace("[cats]", $cat, $keywords);
        $keywords = str_replace("[stat1]", $stat1, $keywords);
        $keywords = str_replace("[stat2]", $stat2, $keywords);
        $keywords = str_replace(",,",",", $keywords);
        $keywords = preg_replace("/, $/","", $keywords);        

        $description = str_replace("[name]", $name, $description);
        $description = str_replace("[desc]", $desc, $description);
        $description = str_replace("[page]", $title, $description );
        $description = str_replace("[tags]", $tag, $description );
        $description = str_replace("[cats]", $cat, $description );
        $description = str_replace("[summary]", $summary, $description );
        $description = str_replace("[stat1]", $stat1, $description );
        $description = str_replace("[stat2]", $stat2, $description );
        $description = str_replace(",,", ",", $description );
        $description = preg_replace("/, $/","", $description );        

	echo '<meta name="keywords" content="'.$keywords.'" />';
	echo '<meta name="description" content="'.$description.'" />';
}

function keywords_options() {
    // variables for the field and option names 
    $opt_name1 = 'kw_format_home';
    $data_field_name1 = 'kw_format_home';
    $opt_name1_desc = 'kw_format_home_desc';
    $data_field_name1_desc = 'kw_format_home_desc';
    $opt_name2 = 'kw_format_cat';
    $data_field_name2 = 'kw_format_cat';
    $opt_name2_desc = 'kw_format_cat_desc';
    $data_field_name2_desc = 'kw_format_cat_desc';
    $opt_name3 = 'kw_format_tag';
    $data_field_name3 = 'kw_format_tag';
    $opt_name3_desc = 'kw_format_tag_desc';
    $data_field_name3_desc = 'kw_format_tag_desc';
    $opt_name4 = 'kw_format_page';
    $data_field_name4 = 'kw_format_page';
    $opt_name4_desc = 'kw_format_page_desc';
    $data_field_name4_desc = 'kw_format_page_desc';
    $opt_name5 = 'kw_format_post';
    $data_field_name5 = 'kw_format_post';
    $opt_name5_desc = 'kw_format_post_desc';
    $data_field_name5_desc = 'kw_format_post_desc';
    $opt_name6 = 'kw_format_static1';
    $data_field_name6 = 'kw_format_static1';
    $opt_name7 = 'kw_format_static2';
    $data_field_name7 = 'kw_format_static2';

    $hidden_field_name = 'kw_submit_hidden';

    // Read in existing option value from database
    $opt_val1 = get_option( $opt_name1 );
    $opt_val1_desc = get_option( $opt_name1_desc );
    $opt_val2 = get_option( $opt_name2 );
    $opt_val2_desc = get_option( $opt_name2_desc );
    $opt_val3 = get_option( $opt_name3 );
    $opt_val3_desc = get_option( $opt_name3_desc );
    $opt_val4 = get_option( $opt_name4 );
    $opt_val4_desc = get_option( $opt_name4_desc );
    $opt_val5 = get_option( $opt_name5 );
    $opt_val5_desc = get_option( $opt_name5_desc );
    $opt_val6 = get_option( $opt_name6 );
    $opt_val7 = get_option( $opt_name7 );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val1 = $_POST[ $data_field_name1 ];
        $opt_val1_desc = $_POST[ $data_field_name1_desc ];
        $opt_val2 = $_POST[ $data_field_name2 ];
        $opt_val2_desc = $_POST[ $data_field_name2_desc ];
        $opt_val3 = $_POST[ $data_field_name3 ];
        $opt_val3_desc = $_POST[ $data_field_name3_desc ];
        $opt_val4 = $_POST[ $data_field_name4 ];
        $opt_val4_desc = $_POST[ $data_field_name4_desc ];
        $opt_val5 = $_POST[ $data_field_name5 ];
        $opt_val5_desc = $_POST[ $data_field_name5_desc ];
        $opt_val6 = $_POST[ $data_field_name6 ];
        $opt_val7 = $_POST[ $data_field_name7 ];

        // Save the posted value in the database
        update_option( $opt_name1, $opt_val1 );
        update_option( $opt_name1_desc, $opt_val1_desc );
        update_option( $opt_name2, $opt_val2 );
        update_option( $opt_name2_desc, $opt_val2_desc );
        update_option( $opt_name3, $opt_val3 );
        update_option( $opt_name3_desc, $opt_val3_desc );
        update_option( $opt_name4, $opt_val4 );
        update_option( $opt_name4_desc, $opt_val4_desc );
        update_option( $opt_name5, $opt_val5 );
        update_option( $opt_name5_desc, $opt_val5_desc );
        update_option( $opt_name6, $opt_val6 );
        update_option( $opt_name7, $opt_val7 );

        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php

    }

    // Now display the options editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Keywords Options', 'mt_trans_domain' ) . "</h2>";

    // options form
    
    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<h3>Static Strings</h3>
<p>
These can be used in keyword formatting (below).
</p>
<table>
<tbody>
  <tr>
    <th style='text-align: left'>
       <?php _e("Stat1:", 'mt_trans_domain' ); ?> 
    </th>
    <td>
      <input type="text" name="<?php echo $data_field_name6; ?>" value="<?php echo $opt_val6; ?>" size="100">
    </td>
  </tr>
  <tr>
    <th style='text-align: left'>
       <?php _e("Stat2:", 'mt_trans_domain' ); ?> 
    </th>
    <td>
      <input type="text" name="<?php echo $data_field_name7; ?>" value="<?php echo $opt_val7; ?>" size="100">
    </td>
  </tr>
</tbody>
</table>

<h3>Keywords Formattng</h3>
<p>
Please indicate what keywords you would like on pages of various types.  The following substituions are valid:
</p>
<ul style="margin-left: 20px;">
<li>[name]: Blog name</li>
<li>[desc]: Blog description</li>
<li>[page]: Page title</li>
<li>[tags]: Post tags</li>
<li>[cats]: Post categories</li>
<li>[summary]: First 200 characters of a single post</li>
<li>[stat1]: Static string 1</li>
<li>[stat2]: Static string 2</li>
</ul>
<table>
<thead>
<tr><th style="text-align: left; padding-right: 10px;">Page Type</th><th style="text-align: left; padding-right: 10px;">Keyword Formatting</th><th style="text-align: left;">Description Formatting</th></tr>
</thead>
<tbody>
<tr>
<td>
<?php _e("Home:", 'mt_trans_domain' ); ?> 
</td>
<td>
<input type="text" name="<?php echo $data_field_name1; ?>" value="<?php echo $opt_val1; ?>" size="40">
</td>
<td>
<input type="text" name="<?php echo $data_field_name1_desc; ?>" value="<?php echo $opt_val1_desc; ?>" size="60">
</td>
</tr>

<tr>
<td>
<?php _e("Category:", 'mt_trans_domain' ); ?> 
</td>
<td>
<input type="text" name="<?php echo $data_field_name2; ?>" value="<?php echo $opt_val2; ?>" size="40">
</td>
<td>
<input type="text" name="<?php echo $data_field_name2_desc; ?>" value="<?php echo $opt_val2_desc; ?>" size="60">
</td>
</tr>

<tr>
<td>
<?php _e("Tag:", 'mt_trans_domain' ); ?> 
</td>
<td>
<input type="text" name="<?php echo $data_field_name3; ?>" value="<?php echo $opt_val3; ?>" size="40">
</td>
<td>
<input type="text" name="<?php echo $data_field_name3_desc; ?>" value="<?php echo $opt_val3_desc; ?>" size="60">
</td>
</tr>

<tr>
<td>
<?php _e("Static:", 'mt_trans_domain' ); ?> 
</td>
<td>
<input type="text" name="<?php echo $data_field_name4; ?>" value="<?php echo $opt_val4; ?>" size="40">
</td>
<td>
<input type="text" name="<?php echo $data_field_name4_desc; ?>" value="<?php echo $opt_val4_desc; ?>" size="60">
</td>
</tr>

<tr>
<td>
<?php _e("Post:", 'mt_trans_domain' ); ?> 
</td>
<td>
<input type="text" name="<?php echo $data_field_name5; ?>" value="<?php echo $opt_val5; ?>" size="40">
</td>
<td>
<input type="text" name="<?php echo $data_field_name5_desc; ?>" value="<?php echo $opt_val5_desc; ?>" size="60">
</td>
</tr>

</tbody>
</table>
<p style="font-weight: bold;">Be careful - the formatting tags are case-sensitive</p>
<hr />

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p>

</form>
<h3>Credits</h3>
<p>Designed, implimented and messed about with by Gaxx (<a href='http://www.gaxx.co.uk/'>http://www.gaxx.co.uk/</a>).</p>
<p>
Based heavily upon "Automated Keywords Generator" (<a href='http://mr.hokya.com/automated-keywords-generator'>http://mr.hokya.com/automated-keywords-generator</a>) by Julian Widya Perdana.
</p>
</div>

<?php
 
}


function init_menu() { 
    add_options_page(__('Keywords'), __('Keywords'), 'manage_options', 'keywords_options', 'keywords_options');
}

function kw_set_defaults() {
    $min_default = 1;
    $default_set = get_option( 'kw_default_set' );
    if ($default_set < $min_default) {
        $opt_name1 = 'kw_format_home';
        $opt_name1_desc = 'kw_format_home_desc';
        $opt_name2 = 'kw_format_cat';
        $opt_name2_desc = 'kw_format_cat_desc';
        $opt_name3 = 'kw_format_tag';
        $opt_name3_desc = 'kw_format_tag_desc';
        $opt_name4 = 'kw_format_page';
        $opt_name4_desc = 'kw_format_page_desc';
        $opt_name5 = 'kw_format_post';
        $opt_name5_desc = 'kw_format_post_desc';
        $opt_name6 = 'kw_format_static1';
        $opt_name7 = 'kw_format_static2';
        update_option( $opt_name1, '[name],[desc]' );
        update_option( $opt_name1_desc, '[name]: [desc]' );
        update_option( $opt_name2, '[page],[cats]' );
        update_option( $opt_name2_desc, '[name]: [desc]' );
        update_option( $opt_name3, '[page],[tags]' );
        update_option( $opt_name3_desc, '[name]: [desc]' );
        update_option( $opt_name4, '[name],[page]' );
        update_option( $opt_name4_desc, '[page]: [summary]' );
        update_option( $opt_name5, '[name],[page],[cats],[tags]' );
        update_option( $opt_name5_desc, '[page]: [summary]' );
        update_option( $opt_name6, '' );
        update_option( $opt_name7, '' );
    }
    update_option('kw_default_set', $min_default );
}

/* Check for and, if necessary, set default values */
function kw_avtivate () {
  kw_set_defaults();
}



add_action('wp_head','akg_act');
add_action('rightnow_end','akg_notify');

// Hook for adding admin menus
add_action('admin_menu', 'init_menu');
register_activation_hook( __FILE__, 'kw_avtivate' );

?>