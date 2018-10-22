<?php
/*==========================
 Select Sub categories AJAX Function
 ===========================*/
add_action('wp_ajax_outfitGetSubCatOnClick', 'outfitGetSubCatOnClick');
add_action('wp_ajax_nopriv_outfitGetSubCatOnClick', 'outfitGetSubCatOnClick');//for users that are not logged in.
function outfitGetSubCatOnClick(){
	if(isset($_POST['mainCat'])){
		$option = '';
		$mainCatID = $_POST['mainCat'];
		if (!outfit_category_exists($mainCatID)) {
			echo $option;
			wp_die('outfit_category_not_exist');
		}
		$cat_child = get_term_children($mainCatID, 'category' );
		if (!empty($cat_child)) {	
			$categories = get_categories('child_of='.$mainCatID.'&hide_empty=0');

			  foreach ($categories as $cat) {				
				$option .= '<option value="'.$cat->term_id.'">';
				$option .= $cat->name;
				$option .= '</option>';
			  }
			  echo '<option value="-1" selected="selected" disabled="disabled">'.esc_html__( "Select Sub Category..", "outfit-standalone" ).'</option>'.$option;
			wp_die();
		}else{			
			echo $option;
			wp_die();
		}
	} // end if
}

?>