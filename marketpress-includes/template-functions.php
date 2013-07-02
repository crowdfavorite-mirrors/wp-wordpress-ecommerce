<?php
/*
MarketPress Template Functions
*/

/**
 * deleted but needed functions
 */
function mp_buy_button() {} 
 

/**
 * Display product tag cloud.
 *
 * The text size is set by the 'smallest' and 'largest' arguments, which will
 * use the 'unit' argument value for the CSS text size unit. The 'format'
 * argument can be 'flat' (default), 'list', or 'array'. The flat value for the
 * 'format' argument will separate tags with spaces. The list value for the
 * 'format' argument will format the tags in a UL HTML list. The array value for
 * the 'format' argument will return in PHP array type format.
 *
 * The 'orderby' argument will accept 'name' or 'count' and defaults to 'name'.
 * The 'order' is the direction to sort, defaults to 'ASC' and can be 'DESC'.
 *
 * The 'number' argument is how many tags to return. By default, the limit will
 * be to return the top 45 tags in the tag cloud list.
 *
 * The 'topic_count_text_callback' argument is a function, which, given the count
 * of the posts  with that tag, returns a text for the tooltip of the tag link.
 *
 * The 'exclude' and 'include' arguments are used for the {@link get_tags()}
 * function. Only one should be used, because only one will be used and the
 * other ignored, if they are both set.
 *
 * @param bool $echo Optional. Whether or not to echo.
 * @param array|string $args Optional. Override default arguments.
 */
function mp_tag_cloud($echo = true, $args = array()) {

  $args['echo'] = false;
  $args['taxonomy'] = 'product_tag';

  $cloud = '<div id="mp_tag_cloud">' . wp_tag_cloud( $args ) . '</div>';

  if ($echo)
    echo $cloud;
  else
    return $cloud;
}


/**
 * Display or retrieve the HTML list of product categories.
 *
 * The list of arguments is below:
 *     'show_option_all' (string) - Text to display for showing all categories.
 *     'orderby' (string) default is 'ID' - What column to use for ordering the
 * categories.
 *     'order' (string) default is 'ASC' - What direction to order categories.
 *     'show_last_update' (bool|int) default is 0 - See {@link
 * walk_category_dropdown_tree()}
 *     'show_count' (bool|int) default is 0 - Whether to show how many posts are
 * in the category.
 *     'hide_empty' (bool|int) default is 1 - Whether to hide categories that
 * don't have any posts attached to them.
 *     'use_desc_for_title' (bool|int) default is 1 - Whether to use the
 * description instead of the category title.
 *     'feed' - See {@link get_categories()}.
 *     'feed_type' - See {@link get_categories()}.
 *     'feed_image' - See {@link get_categories()}.
 *     'child_of' (int) default is 0 - See {@link get_categories()}.
 *     'exclude' (string) - See {@link get_categories()}.
 *     'exclude_tree' (string) - See {@link get_categories()}.
 *     'current_category' (int) - See {@link get_categories()}.
 *     'hierarchical' (bool) - See {@link get_categories()}.
 *     'title_li' (string) - See {@link get_categories()}.
 *     'depth' (int) - The max depth.
 *
 * @param bool $echo Optional. Whether or not to echo.
 * @param string|array $args Optional. Override default arguments.
 */
function mp_list_categories( $echo = true, $args = '' ) {
  $args['taxonomy'] = 'product_category';
  $args['echo'] = false;

  $list = '<ul id="mp_category_list">' . wp_list_categories( $args ) . '</ul>';

  if ($echo)
    echo $list;
  else
    return $list;
}

/**
 * Display or retrieve the HTML dropdown list of product categories.
 *
 * The list of arguments is below:
 *     'show_option_all' (string) - Text to display for showing all categories.
 *     'show_option_none' (string) - Text to display for showing no categories.
 *     'orderby' (string) default is 'ID' - What column to use for ordering the
 * categories.
 *     'order' (string) default is 'ASC' - What direction to order categories.
 *     'show_last_update' (bool|int) default is 0 - See {@link get_categories()}
 *     'show_count' (bool|int) default is 0 - Whether to show how many posts are
 * in the category.
 *     'hide_empty' (bool|int) default is 1 - Whether to hide categories that
 * don't have any posts attached to them.
 *     'child_of' (int) default is 0 - See {@link get_categories()}.
 *     'exclude' (string) - See {@link get_categories()}.
 *     'depth' (int) - The max depth.
 *     'tab_index' (int) - Tab index for select element.
 *     'name' (string) - The name attribute value for select element.
 *     'id' (string) - The ID attribute value for select element. Defaults to name if omitted.
 *     'class' (string) - The class attribute value for select element.
 *     'selected' (int) - Which category ID is selected.
 *     'taxonomy' (string) - The name of the taxonomy to retrieve. Defaults to category.
 *
 * The 'hierarchical' argument, which is disabled by default, will override the
 * depth argument, unless it is true. When the argument is false, it will
 * display all of the categories. When it is enabled it will use the value in
 * the 'depth' argument.
 *
 *
 * @param bool $echo Optional. Whether or not to echo.
 * @param string|array $args Optional. Override default arguments.
 */
function mp_dropdown_categories( $echo = true, $args = '' ) {
  $args['taxonomy'] = 'product_category';
  $args['echo'] = false;
  $args['id'] = 'mp_category_dropdown';

  $dropdown = wp_dropdown_categories( $args );
  $dropdown .= '<script type="text/javascript">
/* <![CDATA[ */
	var dropdown = document.getElementById("mp_category_dropdown");
	function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
			location.href = "'.get_home_url().'/?product_category="+dropdown.options[dropdown.selectedIndex].value;
		}
	}
	dropdown.onchange = onCatChange;
/* ]]> */
</script>';
  
  if ($echo)
    echo $dropdown;
  else
    return $dropdown;
}

/**
 * Displays a list of popular products ordered by sales.
 *
 * @param bool $echo Optional, whether to echo or return
 * @param int $num Optional, max number of products to display. Defaults to 5
 */
function mp_popular_products( $echo = true, $num = 5 ) {
  //The Query
  $custom_query = new WP_Query('post_type=product&post_status=publish&posts_per_page='.intval($num).'&meta_key=mp_sales_count&meta_compare=>&meta_value=0&orderby=meta_value&order=DESC');

  $content = '<ul id="mp_popular_products">';

  if (count($custom_query->posts)) {
    foreach ($custom_query->posts as $post) {
      $content .= '<li><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></li>';
    }
  } else {
    $content .= '<li>' . __('No Products', 'mp') . '</li>';
  }

  $content .= '</ul>';

  if ($echo)
    echo $content;
  else
    return $content;
}




/*
 * function mp_list_products
 * Displays a list of products according to preference. Optional values default to the values in Presentation Settings -> Product List
 *
 * @param bool $echo Optional, whether to echo or return
 * @param bool $paginate Optional, whether to paginate
 * @param int $page Optional, The page number to display in the product list if $paginate is set to true.
 * @param int $per_page Optional, How many products to display in the product list if $paginate is set to true.
 * @param string $order_by Optional, What field to order products by. Can be: title, date, ID, author, price, sales, rand
 * @param string $order Optional, Direction to order products by. Can be: DESC, ASC
 * @param string $category Optional, limit to a product category
 * @param string $tag Optional, limit to a product tag
 */
function mp_list_products( $echo = true, $paginate = '', $page = '', $per_page = '', $order_by = '', $order = '', $category = '', $tag = '', $clear = false ) {
  global $wp_query, $mp;
  $settings = get_option('mp_settings');

  //setup taxonomy if applicable
//  if ($category) {
    $taxonomy_query = '&product_tag=2013&product_category=' . sanitize_title($category); // hard-coded year because you couldn't do both tag and cat
//    print $taxonomy_query;
//  } 

  if ($tag) {
    $taxonomy_query .= '&product_tag=' . sanitize_title($tag);
  } 
  /*
  else if ($wp_query->query_vars['taxonomy'] == 'product_category' || $wp_query->query_vars['taxonomy'] == 'product_tag') {
    $taxonomy_query = '&' . $wp_query->query_vars['taxonomy'] . '=' . get_query_var($wp_query->query_vars['taxonomy']);
  }
*/

  //setup pagination
  $paged = false;
  if ($paginate) {
    $paged = true;
  } else if ($paginate === '') {
    if ($settings['paginate'])
      $paged = true;
    else
      $paginate_query = '&nopaging=true';
  } else {
    $paginate_query = '&nopaging=true';
  }

  //get page details
  if ($paged) {
    //figure out perpage
    if (intval($per_page)) {
      $paginate_query = '&posts_per_page='.intval($per_page);
    } else {
      $paginate_query = '&posts_per_page='.$settings['per_page'];
		}

    //figure out page
    if ($wp_query->query_vars['paged'])
      $paginate_query .= '&paged='.intval($wp_query->query_vars['paged']);

    if (intval($page))
      $paginate_query .= '&paged='.intval($page);
    else if ($wp_query->query_vars['paged'])
      $paginate_query .= '&paged='.intval($wp_query->query_vars['paged']);
  }

  //get order by
  if (!$order_by) {
    if ($settings['order_by'] == 'price')
      $order_by_query = '&meta_key=mp_price_sort&orderby=meta_value_num';
    else if ($settings['order_by'] == 'sales')
      $order_by_query = '&meta_key=mp_sales_count&orderby=meta_value_num';
    else
      $order_by_query = '&orderby='.$settings['order_by'];
  } else {
  	if ('price' == $order_by)
  		$order_by_query = '&meta_key=mp_price_sort&orderby=meta_value_num';
    else
    	$order_by_query = '&orderby='.$order_by;
  }

  //get order direction
  if (!$order) {
    $order_query = '&order='.$settings['order'];
  } else {
    $order_query = '&order='.$order;
  }

  //The Query
  $custom_query = new WP_Query('post_type=product&post_status=publish' . $taxonomy_query . $paginate_query . $order_by_query . $order_query);

  //allows pagination links to work get_posts_nav_link()
  if ($wp_query->max_num_pages == 0 || $taxonomy_query)
    $wp_query->max_num_pages = $custom_query->max_num_pages;

  $content = '<div id="mp_product_list">';

  if ($last = count($custom_query->posts)) {
    $count = 1;
    foreach ($custom_query->posts as $post) {

	    if(get_post_meta( $post->ID, 'editors_pick', true )=="Yes") {
	    	$eds_pick = '<img src="/wp-content/themes/snowboard/images/plat_picks.jpg" /><br />';
	    } else  {
	    	$eds_pick = "";
	    }
	    $mens_or_womens = get_post_meta( $post->ID, 'mens_or_womens', true );
	    $mens_or_womens = str_replace("ns","n's",$mens_or_womens);
	    if(get_post_meta( $post->ID, 'review_or_brand_guide', true )=="Review") {
	    	$is_review = "Review";
	    } else {
	    	$is_review = "Info";
	    }	     

			//add last css class for styling grids
			if ($count == $last)
			  $class = array('mp_product', 'last-product');
			else
			  $class = 'mp_product';

      $content .= '<div '.mp_product_class(false, $class, $post->ID).'>';
      $content .= '<div class="mp_product_content">';
      $content .= '<div id="mp_product_image_box">';
      $product_content = mp_product_image( false, 'list', $post->ID, 183 );
      if ($settings['show_excerpt'] == 1)
        //$product_content .= $mp->product_excerpt($post->post_excerpt, $post->post_content, $post->ID);
      $content .= apply_filters( 'mp_product_list_content', $product_content, $post->ID );
	  $content .= '</div><br />';
	  
	  $content .= "$eds_pick";	  
	   
      $content .= '<h3 class="mp_product_name" style="margin-bottom:0;"><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></h3>';          
      
      $content .= "$is_review &middot; $mens_or_womens";
       
      $content .= '</div>';

      $content .= '<div class="mp_product_meta">';
      //price
      //$meta = mp_product_price(false, $post->ID);
      //button
      $meta .= mp_buy_button(false, 'list', $post->ID);
      $content .= apply_filters( 'mp_product_list_meta', $meta, $post->ID );      
      $content .= '</div>';

      $content .= '</div>';
      
      if ($count % 3 == 0 && $clear) { $content .= '<div style="clear:both;"></div>'; }
      else if ($count % 2 == 0 && !$clear) { $content .= '<div style="clear:both;"></div>'; }

      $count++;
    }
  } else {
    $content .= '<div id="mp_no_products">' . apply_filters( 'mp_product_list_none', __('No Products', 'mp') ) . '</div>';
  }

  $content .= '</div>';

  if ($echo)
    echo $content;
  else
    return $content;
}


/**
 * Retrieve product's category list in either HTML list or custom format.
 *
 * @param int $product_id Optional. Post ID to retrieve categories.
 * @param string $before Optional. Before list.
 * @param string $sep Optional. Separate items using this.
 * @param string $after Optional. After list.
 */
function mp_category_list( $product_id = false, $before = '', $sep = ', ', $after = '' ) {
  $terms = get_the_term_list( $product_id, 'product_category', $before, $sep, $after );
  if ( $terms )
    return $terms;
  else
		return __( 'Uncatagorized', 'mp' );
}


/**
 * Retrieve product's tag list in either HTML list or custom format.
 *
 * @param int $product_id Optional. Post ID to retrieve categories.
 * @param string $before Optional. Before list.
 * @param string $sep Optional. Separate items using this.
 * @param string $after Optional. After list.
 */
function mp_tag_list( $product_id = false, $before = '', $sep = ', ', $after = '' ) {
  $terms = get_the_term_list( $product_id, 'product_tag', $before, $sep, $after );
  if ( $terms )
    return $terms;
  else
		return __( 'No Tags', 'mp' );
}

/**
 * Display the classes for the product div.
 *
 * @param bool $echo Whether to echo class.
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id The post_id for the product. Optional if in the loop
 */
function mp_product_class( $echo = true, $class = '', $post_id = null ) {
	// Separates classes with a single space, collates classes for post DIV
	$content = 'class="' . join( ' ', mp_get_product_class( $class, $post_id ) ) . '"';

	if ($echo)
    echo $content;
  else
    return $content;
}


/**
 * Retrieve the list of classes for the product as an array.
 *
 * The class names are add are many. If the post is a sticky, then the 'sticky'
 * class name. The class 'hentry' is always added to each post. For each
 * category, the class will be added with 'category-' with category slug is
 * added. The tags are the same way as the categories with 'tag-' before the tag
 * slug. All classes are passed through the filter, 'post_class' with the list
 * of classes, followed by $class parameter value, with the post ID as the last
 * parameter.
 *
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id The post_id for the product. Optional if in the loop
 * @return array Array of classes.
 */
function mp_get_product_class( $class = '', $post_id = null ) {
  global $id;
  $post_id = ( NULL === $post_id ) ? $id : $post_id;

	$post = get_post($post_id);

	$classes = array();

	if ( empty($post) )
		return $classes;

	$classes[] = 'product-' . $post->ID;
	$classes[] = $post->post_type;
	$classes[] = 'type-' . $post->post_type;

	// sticky for Sticky Posts
	if ( is_sticky($post->ID))
		$classes[] = 'sticky';

	// hentry for hAtom compliace
	$classes[] = 'hentry';

	// Categories
	$categories = get_the_terms($post->ID, "product_category");
	foreach ( (array) $categories as $cat ) {
		if ( empty($cat->slug ) )
			continue;
		$classes[] = 'category-' . sanitize_html_class($cat->slug, $cat->cat_ID);
	}

	// Tags
	$tags = get_the_terms($post->ID, "product_tag");
	foreach ( (array) $tags as $tag ) {
		if ( empty($tag->slug ) )
			continue;
		$classes[] = 'tag-' . sanitize_html_class($tag->slug, $tag->term_id);
	}

	if ( !empty($class) ) {
		if ( !is_array( $class ) )
			$class = preg_split('#\s+#', $class);
		$classes = array_merge($classes, $class);
	}

	$classes = array_map('esc_attr', $classes);

	return $classes;
}


/*
 * Displays the product price (and sale price)
 *
 * @param bool $echo Optional, whether to echo
 * @param int $post_id The post_id for the product. Optional if in the loop
 * @param sting $label A label to prepend to the price. Defaults to "Price: "
 */
function mp_product_price( $echo = true, $post_id = NULL, $label = true ) {
  global $id, $mp;
  $post_id = ( NULL === $post_id ) ? $id : $post_id;

  $label = ($label === true) ? __('Price: ', 'mp') : $label;

  $settings = get_option('mp_settings');
	$meta = get_post_custom($post_id);
  //unserialize
  foreach ($meta as $key => $val) {
	  $meta[$key] = maybe_unserialize($val[0]);
	  if (!is_array($meta[$key]) && $key != "mp_is_sale" && $key != "mp_track_inventory" && $key != "mp_product_link" && $key != "mp_file" && $key != "mp_price_sort")
	    $meta[$key] = array($meta[$key]);
	}

  if ((is_array($meta["mp_price"]) && count($meta["mp_price"]) == 1) || !empty($meta["mp_file"])) {
    if ($meta["mp_is_sale"]) {
	    $price = '<span class="mp_special_price"><del class="mp_old_price">'.$mp->format_currency('', $meta["mp_price"][0]).'</del>';
	    $price .= '<span class="mp_current_price">'.$mp->format_currency('', $meta["mp_sale_price"][0]).'</span></span>';
	  } else {
	    $price = '<span class="mp_normal_price"><span class="mp_current_price">'.$mp->format_currency('', $meta["mp_price"][0]).'</span></span>';
	  }
	} else {
		return '';
	}

  $price = apply_filters( 'mp_product_price_tag', '<span class="mp_product_price">' . $label . $price . '</span>', $post_id, $label );

  if ($echo)
    echo $price;
  else
    return $price;
}



/*
 * Displays the product featured image
 *
 * @param bool $echo Optional, whether to echo
 * @param string $context Options are list, single, or widget
 * @param int $post_id The post_id for the product. Optional if in the loop
 * @param int $size An optional width/height for the image if contect is widget
 */
function mp_product_image( $echo = true, $context = 'list', $post_id = NULL, $size = NULL ) {
  global $id;
  $post_id = ( NULL === $post_id ) ? $id : $post_id;
  // Added WPML
  $post_id = apply_filters('mp_product_image_id', $post_id);

  $post = get_post($post_id);

  $settings = get_option('mp_settings');
  $post_thumbnail_id = get_post_thumbnail_id( $post_id );

  if ($context == 'list') {
    //quit if no thumbnails on listings
    if (!$settings['show_thumbnail'])
      return '';

    //size
    if (intval($size)) {
      $size = array(intval($size), intval($size));
    } else {
      if ($settings['list_img_size'] == 'custom')
        $size = array($settings['list_img_width'], $settings['list_img_height']);
      else
        $size = $settings['list_img_size'];
    }
    
    //link
    $link = get_permalink($post_id);

    $title = esc_attr($post->post_title);

  } else if ($context == 'single') {
    //size
    if ($settings['product_img_size'] == 'custom')
      $size = array($settings['product_img_width'], $settings['product_img_height']);
    else
      $size = $settings['product_img_size'];

    //link
    $temp = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
    $link = $temp[0];

    $title = __('View Larger Image &raquo;', 'mp');
    $class = ' class="mp_product_image_link mp_lightbox" rel="lightbox"';

  } else if ($context == 'widget') {
    //size
    if (intval($size))
      $size = array(intval($size), intval($size));
    else
      $size = array(50, 50);

    //link
    $link = get_permalink($post_id);

    $title = esc_attr($post->post_title);

  }

  $image = get_the_post_thumbnail($post_id, $size, array('class' => 'alignleft mp_product_image_'.$context, 'title' => $title));

  //add the link
  if ($link)
    $image = '<a id="product_image-' . $post_id . '"' . $class . ' href="' . $link . '">' . $image . '</a>';

  if ($echo)
    echo $image;
  else
    return $image;
}


/**
 * Echos the current store link.
 * @param bool $echo Optional, whether to echo. Defaults to true
 * @param bool $url Optional, whether to return a link or url. Defaults to show link.
 * @param string $link_text Optional, text to show in link.
 */
function mp_store_link($echo = true, $url = false, $link_text = '') {
	$settings = get_option('mp_settings');
  $link = home_url(trailingslashit($settings['slugs']['store']));

  if (!$url) {
    $text = ($link_text) ? $link_text : __('Visit Store', 'mp');
    $link = '<a href="' . $link . '" class="mp_store_link">' . $text . '</a>';
  }

  $link = apply_filters( 'mp_store_link', $link, $echo, $url, $link_text );

  if ($echo)
    echo $link;
  else
    return $link;
}

/**
 * Echos the current product list link.
 * @param bool $echo Optional, whether to echo. Defaults to true
 * @param bool $url Optional, whether to return a link or url. Defaults to show link.
 * @param string $link_text Optional, text to show in link.
 */
function mp_products_link($echo = true, $url = false, $link_text = '') {
	$settings = get_option('mp_settings');
  $link = home_url( $settings['slugs']['store'] . '/' . $settings['slugs']['products'] . '/' );

  if (!$url) {
    $text = ($link_text) ? $link_text : __('View Products', 'mp');
    $link = '<a href="' . $link . '" class="mp_products_link">' . $text . '</a>';
  }

  $link = apply_filters( 'mp_products_link', $link, $echo, $url, $link_text );

  if ($echo)
    echo $link;
  else
    return $link;
}



/**
 * Echos the current store navigation links.
 *
 * @param bool $echo Optional, whether to echo. Defaults to true
 */
function mp_store_navigation( $echo = true ) {
	$settings = get_option('mp_settings');

  //navigation
  if (!$settings['disable_cart']) {
    $nav = '<ul class="mp_store_navigation"><li class="page_item"><a href="' . mp_products_link(false, true) . '" title="' . __('Products', 'mp') . '">' . __('Products', 'mp') . '</a></li>';
		$nav .= '<li class="page_item"><a href="' . mp_cart_link(false, true) . '" title="' . __('Shopping Cart', 'mp') . '">' . __('Shopping Cart', 'mp') . '</a></li>';
    $nav .= '<li class="page_item"><a href="' . mp_orderstatus_link(false, true) . '" title="' . __('Order Status', 'mp') . '">' . __('Order Status', 'mp') . '</a></li>
</ul>';
  } else {
    $nav = '<ul class="mp_store_navigation">
<li class="page_item"><a href="' . mp_products_link(false, true) . '" title="' . __('Products', 'mp') . '">' . __('Products', 'mp') . '</a></li>
</ul>';
  }

  if ($echo)
    echo $nav;
  else
    return $nav;
}

/**
 * Determine if on a MarketPress shop page
 *
 * @retuns bool whether current page is a MarketPress store page.
 */
function mp_is_shop_page() {
  global $mp;
  return $mp->is_shop_page;
}



/**
 * Determine the number of published products
 *
 * @retuns int number of published products.
 */
function mp_products_count() {
  $custom_query = new WP_Query('post_type=product&post_status=publish');
  return $custom_query->post_count;
}
