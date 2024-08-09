<?php
/**
 * Display Featured Product Categories Widget Class
 *
 * @package Orchid_Store
 */

if ( ! class_exists( 'Orchid_Store_Featured_Product_Categories_Widget' ) ) {
	/**
	 * Widget class - Orchid_Store_Featured_Product_Categories_Widget.
	 *
	 * @since 1.0.0
	 *
	 * @package orchit_store
	 */
	class Orchid_Store_Featured_Product_Categories_Widget extends WP_Widget {

		/**
		 * Slug or id.
		 *
		 * @var string
		 */
		public $value_as;


		/**
		 * Define id, name and description of the widget.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct(
				'orchid-store-featured-product-categories-widget',
				esc_html__( 'OS: Featured Product Categories', 'orchid-store' ),
				array(
					'classname'   => '',
					'description' => esc_html__( 'Displays featured product categories.', 'orchid-store' ),
				)
			);

			$this->value_as = orchid_store_get_option( 'value_as' );
		}


		/**
		 * Renders widget at the frontend.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args Provides the HTML you can use to display the widget title class and widget content class.
		 * @param array $instance The settings for the instance of the widget..
		 */
		public function widget( $args, $instance ) {

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			$product_categories = isset( $instance['product_categories'] ) ? $instance['product_categories'] : array();

			if ( ! empty( $product_categories ) ) {
				?>
			
				<?php
			}
		}


		/**
		 * Adds setting fields to the widget and renders them in the form.
		 *
		 * @since 1.0.0
		 *
		 * @param array $instance The settings for the instance of the widget..
		 */
		public function form( $instance ) {

			$instance['title'] = isset( $instance['title'] ) ? $instance['title'] : '';

			$instance['product_categories'] = isset( $instance['product_categories'] ) ? $instance['product_categories'] : array();
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
					<strong><?php esc_html_e( 'Title', 'orchid-store' ); ?></strong>
				</label>
				<input
					class="widefat"
					id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
					type="text" value="<?php echo esc_attr( $instance['title'] ); ?>"
				>   
			</p>

			<p>
				<span class="sldr-elmnt-title">
					<strong><?php esc_html_e( 'Product Categories', 'orchid-store' ); ?></strong>
				</span>
				<span class="sldr-elmnt-desc">
					<?php esc_html_e( 'Below are the list of product categories. Check on a category to set is as a filter item.', 'orchid-store' ); ?>
				</span>

				<span class="widget_multicheck">
				<?php

				$product_categories = orchid_store_all_product_categories();

				if ( ! empty( $product_categories ) ) {

					if ( 'slug' === $this->value_as ) {
						foreach ( $product_categories as $index => $product_category ) {
							?>
							<span class="sldr-elmnt-cntnr">
								<label for="<?php echo esc_attr( $this->get_field_id( 'product_categories' ) . $product_category->term_id ); ?>">
									<input
										id="<?php echo esc_attr( $this->get_field_id( 'product_categories' ) . $product_category->term_id ); ?>"
										name="<?php echo esc_attr( $this->get_field_name( 'product_categories' ) ); ?>[]"
										type="checkbox"
										value="<?php echo esc_attr( $product_category->slug ); ?>" 
										<?php
										if ( ! empty( $instance['product_categories'] ) ) {
											checked( in_array( $product_category->slug, $instance['product_categories'], true ), true ); }
										?>
									>
									<strong><?php echo esc_html( $product_category->name ); ?></strong>
								</label>
							</span><!-- .sldr-elmnt-cntnr -->
							<?php
						}
					} else {
						foreach ( $product_categories as $index => $product_category ) {
							?>
							<span class="sldr-elmnt-cntnr">
								<label for="<?php echo esc_attr( $this->get_field_id( 'product_categories' ) . $product_category->term_id ); ?>">
									<input
										id="<?php echo esc_attr( $this->get_field_id( 'product_categories' ) . $product_category->term_id ); ?>"
										name="<?php echo esc_attr( $this->get_field_name( 'product_categories' ) ); ?>[]"
										type="checkbox"
										value="<?php echo esc_attr( $product_category->term_id ); ?>" 
										<?php
										if ( ! empty( $instance['product_categories'] ) ) {
											checked( in_array( $product_category->term_id, $instance['product_categories'], true ), true ); }
										?>
									>
									<strong><?php echo esc_html( $product_category->name ); ?></strong>
								</label>
							</span><!-- .sldr-elmnt-cntnr -->
							<?php
						}
					}
				} else {
					?>
					<input
						id="<?php echo esc_attr( $this->get_field_id( 'product_categories' ) ); ?>"
						name="<?php echo esc_attr( $this->get_field_name( 'product_categories' ) ); ?>[]"
						type="hidden"
						value="" checked
					>
					<small>
						<?php echo esc_html__( 'There are no product categories to select.', 'orchid-store' ); ?>
					</small>
					<?php
				}
				?>
				</span>
			</p>
			<?php
		}


		/**
		 * Sanitizes and saves the instance of the widget.
		 *
		 * @since 1.0.0
		 *
		 * @param array $new_instance The settings for the new instance of the widget.
		 * @param array $old_instance The settings for the old instance of the widget.
		 * @return array Sanitized instance of the widget.
		 */
		public function update( $new_instance, $old_instance ) {

			$instance = $old_instance;

			$instance['title'] = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';

			if ( 'slug' === $this->value_as ) {
				$instance['product_categories'] = isset( $new_instance['product_categories'] ) ? array_map( 'sanitize_text_field', $new_instance['product_categories'] ) : array();
			} else {
				$instance['product_categories'] = isset( $new_instance['product_categories'] ) ? array_map( 'absint', $new_instance['product_categories'] ) : array();
			}

			return $instance;
		}
	}
}
