<?php
/**
 * Plugin Name: Category Posts Tabber
 * Description: Allow to create widgets containing tabs to show on sidebars. Every tab is the list of posts of each particular category.
 * Version: 3.0.0
 * Author: Phú Phan Thanh
 * Author URI: https://www.phanthanhphu.com/
 * License: GPLv2 or later
 */

class CPT_Widget extends WP_Widget {

	protected $WP_VER;

	function __construct() {

		$this->WP_VER = str_replace('.', '', get_bloginfo('version'));

		$verLength = strlen($this->WP_VER);
		if ( $verLength == 2 )
				$this->WP_VER = $this->WP_VER . '0';
		elseif ( $verLength == 1 )
			$this->WP_VER = $this->WP_VER . '00';

		$this->WP_VER = (int)$this->WP_VER;

		add_action( 'init', array(&$this, 'cpt_init') );
		add_action('wp_enqueue_scripts', array(&$this, 'cpt_register_scripts'));
		add_action('admin_enqueue_scripts', array(&$this, 'cpt_admin_scripts'));
		
		$widget_ops = array('classname' => 'cpt-widget', 'description' => __('List single category posts'));

		if ($this->WP_VER >= 430)
			parent::__construct('category-posts-tabber', __('Category Posts Tabber'), $widget_ops);
		else
			$this->WP_Widget('category-posts-tabber', __('Category Posts Tabber'), $widget_ops);

	}

	function cpt_init() {
		add_image_size( 'cpt-thumbnail', 70, 60, true );
	}

	function cpt_admin_scripts($hook) {
		if ($hook != 'widgets.php')
			return;

		wp_register_script('cpt_widget_admin', plugins_url('js/cpt-admin.js', __FILE__), true);  
		wp_enqueue_script('cpt_widget_admin');
	}

	function cpt_register_scripts() { 
		wp_register_script('category_posts_tabber', plugins_url('js/cpt-widget.js', __FILE__), array('jquery'), null, true );
		wp_register_style('category_posts_tabber', plugins_url('css/cpt-widget.css', __FILE__), array(), null );
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 
			'tab_name' => array(),
			'category' => array(),
			'widget_title' => '',
			'post_num' => 5,
			'show_thumbnail' => 1,
			'thumbnail_width' => '',
			'show_date' => 1,
			'show_comment_num' => 0,
			'order_by' => 'date'
		) );

		extract($instance);

		?>
		<h4>
			<a href="#" id="select-tab-toggle" style="font-weight: 700;"><?php _e('Select Tabs'); ?></a>
		</h4>
		<div id="cpt-select-tab" style="margin-bottom: 30px; <?php if (!$tab_name) echo 'display: none'; ?>">
			<p>
				<a id="add-tab" href="#"><?php _e( 'Add New Tab +' ); ?></a>
			</p>
			<hr />
			<ul id="cpt-tab-list">
				<?php if ($tab_name) : ?>
				<?php foreach ($tab_name as $index => $name): ?>
				<?php $cat_id = isset($category[$index]) ? $category[$index] : ''; ?>
					<li>
						<p>
							<label>
								<?php _e( 'Tab name' ); ?>:
								<input name="<?php echo $this->get_field_name("tab_name"); ?>[]" type="text" value="<?php echo $name; ?>" />
							</label>
						</p>
						<p>
							<label>
								<?php _e( 'Category' ); ?>:
								<?php wp_dropdown_categories( array( 'name' => $this->get_field_name('category') . '[]', 'selected' => $cat_id) ); ?>
							</label>
						</p>
						<p>
							<a href="#" class="remove-tab" style="color: red;"><?php _e( 'Delete this tab' ); ?></a>
						</p>
						<hr />
					</li>
				<?php endforeach; ?>
				<?php endif; ?>
			</ul>

			<div id="html-tab-wrapper" style="display: none">
				<p>
					<label>
						<?php _e( 'Tab name' ); ?>:
						<input name="<?php echo $this->get_field_name("tab_name_sample"); ?>[]" type="text" value="" />
					</label>
				</p>
				<p>
					<label>
						<?php _e( 'Category' ); ?>:
						<?php wp_dropdown_categories( array( 'name' => $this->get_field_name('category_sample') . '[]') ); ?>
					</label>
				</p>
				<p>
					<a href="#" class="remove-tab"><?php _e( 'Delete this tab' ); ?></a>
				</p>
				<hr />
			</div>
		</div>
		
		<h4>
			<a id="cpt-option-toggle" style="font-weight: 700;" href="#"><?php _e('Options'); ?></a>
		</h4>
		<div id="cpt-option">
			<p>
				<label for="<?php echo $this->get_field_id("widget_title"); ?>">
					<?php _e( 'Widget title' ); ?>:
					<input class="cpt-widget-title" size="2" id="<?php echo $this->get_field_id("widget_title"); ?>" name="<?php echo $this->get_field_name("widget_title"); ?>" type="text" value="<?php echo $widget_title; ?>" />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id("post_num"); ?>">
					<?php _e( 'Number of posts to show' ); ?>:
					<input class="cpt-post-num" size="2" id="<?php echo $this->get_field_id("post_num"); ?>" name="<?php echo $this->get_field_name("post_num"); ?>" type="text" value="<?php echo $post_num; ?>" />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id("show_thumbnail"); ?>">				
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_thumbnail"); ?>" name="<?php echo $this->get_field_name("show_thumbnail"); ?>" value="1" <?php if (isset($show_thumbnail)) { checked( 1, $show_thumbnail, true ); } ?> />
					<?php _e( 'Show post thumbnails'); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id("thumbnail_width"); ?>">
					<?php _e( 'Thumbnail width (e.g. 50px, 27%)' ); ?>:
					<input class="cpt-thumbnail-width" size="2" id="<?php echo $this->get_field_id("thumbnail_width"); ?>" name="<?php echo $this->get_field_name("thumbnail_width"); ?>" type="text" value="<?php echo $thumbnail_width; ?>" />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id("show_date"); ?>">				
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_date"); ?>" name="<?php echo $this->get_field_name("show_date"); ?>" value="1" <?php if (isset($show_date)) { checked( 1, $show_date, true ); } ?> />
					<?php _e( 'Show post date'); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id("show_comment_num"); ?>">				
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_comment_num"); ?>" name="<?php echo $this->get_field_name("show_comment_num"); ?>" value="1" <?php if (isset($show_comment_num)) { checked( 1, $show_comment_num, true ); } ?> />
					<?php _e( 'Show number of comments'); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('order_by'); ?>"><?php _e('Order by:'); ?></label> 
				<select id="<?php echo $this->get_field_id('order_by'); ?>" name="<?php echo $this->get_field_name('order_by'); ?>">
					<option value="date" <?php selected($order_by, 'date', true); ?>><?php _e('Date'); ?></option>
					<option value="comment_count" <?php selected($order_by, 'comment_count', true); ?>><?php _e('Comment count'); ?></option>    
				</select>
			</p>
		</div>
		<?php
	}

	function update( $new_instance, $old_instance ) {	
		if (!preg_match('/^\d+$/', $new_instance['post_num'])) {
			$new_instance['post_num'] = $old_instance['post_num'];
		}

		$new_instance['widget_title'] = $new_instance['widget_title'] ? $new_instance['widget_title'] : '';
		$new_instance['show_thumbnail'] = $new_instance['show_thumbnail'] ? $new_instance['show_thumbnail'] : '';
		$new_instance['thumbnail_width'] = $new_instance['thumbnail_width'] ? $new_instance['thumbnail_width'] : '';
		$new_instance['show_date'] = $new_instance['show_date'] ? $new_instance['show_date'] : '';
		$new_instance['show_comment_num'] = $new_instance['show_comment_num'] ? $new_instance['show_comment_num'] : '';

		if (isset($new_instance['tab_name_sample'])) {
			unset($new_instance['tab_name_sample']);
		}
		if (isset($new_instance['category_sample'])) {
			unset($new_instance['category_sample']);
		}

		return $new_instance;	
	}

	function widget( $args, $instance ) {
		extract($instance);

		wp_enqueue_script('category_posts_tabber');
		wp_enqueue_style('category_posts_tabber');

		?>
		<?php
			if (isset($tab_name) && $tab_name) :
				$tab_width = 100/count($tab_name);
				$tab_width = number_format($tab_width, 2, '.', '');

				$thumbnail_css = '';
				if ( $thumbnail_width ) {
					$thumbnail_css = 'width: ' . esc_attr($thumbnail_width) . ';';
				}
		?>
		<?php echo $args['before_widget'] ?>
			<?php if (isset($widget_title) && $widget_title) : ?>
				<h2 class="widget-title"><?php esc_html_e($widget_title) ?></h2>
			<?php endif; ?>

			<div class="cpt-widget-wrapper">
				<ul class="cpt-tab">
					<?php
						foreach ($tab_name as $index => $name) :
							if (!$name) {
								$default = __('Featured Posts');
								if (isset($category[$index])) {
									$name = get_cat_name($category[$index]);
									if (!$name) {
										$name = $default;
									}
								} else {
									$name = $default;
								}
							}
					?>
						<li style="width: <?php echo $tab_width ?>%">
							<a class="cpt-tab-item <?php if (!$index) { echo 'cpt-current-item'; } ?>" id="cpt-tab-<?php echo $index; ?>" href="#"><?php echo $name; ?></a>
						</li>
					<?php endforeach; ?>
				</ul>

				<div class="cpt-content-wrapper <?php echo ( ! $show_thumbnail ) ? 'no-thumbnail' : ''; ?>">
					<?php foreach ($tab_name as $index => $name) : ?>
					<?php
						if (isset($category[$index])) :
							$category_id = $category[$index];

							$cpt_query = new WP_Query(
									'cat=' . $category_id .
									'&posts_per_page=' . $post_num .
									'&orderby=' . $order_by .
									'&order=DESC'
								);

					?>
						<ul id="cpt-content-<?php echo $index; ?>" style="<?php echo $index === 0 ? 'display: block;' : ''; ?>" class="cpt-tab-content <?php if (!$index) { echo 'cpt-current-content'; } ?>">
							<?php if ($cpt_query->have_posts()) : ?>
							<?php while ($cpt_query->have_posts()) : $cpt_query->the_post(); ?>
							<li>
								<?php if ( has_post_thumbnail() && $show_thumbnail ) : ?>
								<div class="cpt-thumbnail" style="<?php echo esc_attr($thumbnail_css) ?>">
									<a href="<?php the_permalink() ?>">
									<?php the_post_thumbnail('cpt-thumbnail', array('title' => '')); ?>
									</a>
								</div>	
								<?php endif; ?>
								
								<div class="cpt-title">
									<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
								</div>

								<div class="cpt-post-meta">
									<?php if ($show_date) : ?>
									<span class="cpt-date"><?php echo get_the_date(); ?></span>
									<?php endif; ?>

									<?php if ($show_date && $show_comment_num) : ?>
									<span class="cpt-separate">|</span>
									<?php endif; ?>
									
									<?php if ($show_comment_num) : ?>
									<span class="cpt-comment-num"><?php comments_number(); ?></span>
									<?php endif; ?>
								</div>
							</li>
							<?php endwhile; ?>
							<?php else : ?>
								<p class="cpt-no-post">No Posts Found.</p>
							<?php endif; ?>
						</ul>
					<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php echo $args['after_widget'] ?>
		<?php endif; ?>
		<?php
	}

}

function cpt_register_widget() {
	return register_widget('CPT_Widget');
}

add_action( 'widgets_init', 'cpt_register_widget' );
?>