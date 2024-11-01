<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Helper' ) ) {

    final class TEO_Helper {

        /**
         * It returns an array of Post objects.
         * Get all products of the shop via $wpdb.
         *
         * @since 1.0.0
         * @return mixed
         *
         * @param null $limit
         * @param string $order_by
         * @return mixed
         */
        public static function get_all_products( $limit = null , $order_by = 'DESC' ) {

            global $wpdb;

            $query = "
                      SELECT *
                      FROM $wpdb->posts
                      WHERE post_status = 'publish'
                      AND post_type = 'product'
                      ORDER BY $wpdb->posts.post_date " . $order_by . "
                    ";

            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

        /**
         * Get all the pages of the current site via wpdb.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $limit
         * @param string $order_by
         * @return mixed
         */
        public static function get_all_site_pages( $limit = null , $order_by = 'DESC' ) {

            global $wpdb;

            $query = "
                      SELECT * FROM $wpdb->posts
                      WHERE $wpdb->posts.post_status = 'publish'
                      AND $wpdb->posts.post_type = 'page'
                      ORDER BY $wpdb->posts.post_date " . $order_by . "
                     ";

            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

        /**
         * Get the title of the page id based on the page type.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $type
         * @param $id
         * @return mixed
         */
        public static function get_id_title( $type , $id ) {

			$title = "";

            switch ( $type ) {

                case 'page':
                case 'post':
                case 'product':
					$title = get_the_title( $id );
                    break;

                case 'product-category':
					$title = get_cat_name( $id );
                    break;

            }

            return apply_filters( 'teo_get_id_text' , $title , $type , $id );

        }

        /**
         * Get variable product variations.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $args
         * @return array
         */
        public static function get_product_variations( $args ) {

            if ( isset( $args[ 'product' ] ) )
                $product = $args[ 'product' ];
            elseif ( isset( $args[ 'variable_id' ] ) )
                $product = wc_get_product( $args[ 'variable_id' ] );

			$variation_arr = array();

			if ( $product ) {

				$product_variations = $product->get_available_variations();
				$product_attributes = $product->get_attributes();

				foreach ( $product_variations as $variation ) {

					if ( isset( $args[ 'variation_id' ] ) && $args[ 'variation_id' ] != $variation[ 'variation_id' ] )
						continue;

					$variation_obj            = wc_get_product( $variation[ 'variation_id' ] );
					$variation_attributes     = $variation_obj->get_variation_attributes();
					$friendly_variation_text  = null;
					$variation_attributes_arr = array();

					foreach ( $variation_attributes as $variation_name => $variation_val ) {

						foreach ( $product_attributes as $attribute_key => $attribute_arr ) {

							if ( $variation_name != 'attribute_' . sanitize_title( $attribute_arr[ 'name' ] ) )
								continue;

							$attr_found = false;

							if ( $attribute_arr[ 'is_taxonomy' ] ) {

								// This is a taxonomy attribute
								$variation_taxonomy_attribute = wp_get_post_terms( $product->id , $attribute_arr[ 'name' ] );

								foreach ( $variation_taxonomy_attribute as $var_tax_attr ) {

									if ( $variation_val == $var_tax_attr->slug ) {

										if ( is_null( $friendly_variation_text ) )
											$friendly_variation_text = str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": " . $var_tax_attr->name;
										else
											$friendly_variation_text .= ", " . str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": " . $var_tax_attr->name;

										$attr_key = "attribute_pa_" . str_replace( " " , "-" , strtolower( str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) ) );
										$attr_val = $var_tax_attr->slug;

										if ( isset( $variation_attributes_arr[ $variation[ 'variation_id' ] ] ) )
											$variation_attributes_arr[ $variation[ 'variation_id' ] ][ $attr_key ] = $attr_val;
										else
											$variation_attributes_arr[ $variation[ 'variation_id' ] ] = array( $attr_key => $attr_val );

										$attr_found = true;
										break;

									} elseif ( empty( $variation_val ) ) {

										if ( is_null( $friendly_variation_text ) )
											$friendly_variation_text = str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": Any";
										else
											$friendly_variation_text .= ", " . str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": Any";

										$attr_key = "attribute_pa_" . str_replace( " " , "-" , strtolower( str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) ) );

										if ( isset( $variation_attributes_arr[ $variation[ 'variation_id' ] ] ) )
											$variation_attributes_arr[ $variation[ 'variation_id' ] ][ $attr_key ] = "any";
										else
											$variation_attributes_arr[ $variation[ 'variation_id' ] ] = array( $attr_key => "any" );

										$attr_found = true;
										break;

									}

								}

							} else {

								// This is not a taxonomy attribute

								$attr_val = explode( '|' , $attribute_arr[ 'value' ] );

								foreach ( $attr_val as $attr ) {

									$attr = trim( $attr );

									// I believe the reason why I wrapped the $attr with sanitize_title is to remove special chars
									// We need ot wrap variation_val too to properly compare them
									if ( sanitize_title( $variation_val ) == sanitize_title( $attr ) ) {

										if ( is_null( $friendly_variation_text ) )
											$friendly_variation_text = str_replace( ":" , "" , $attribute_arr[ 'name' ] ) . ": " . $attr;
										else
											$friendly_variation_text .= ", " . str_replace( ":" , "" , $attribute_arr[ 'name' ] ) . ": " . $attr;

										$attr_key = "attribute_" . str_replace( " " , "-" , strtolower( str_replace( ":" , "" , $attribute_arr[ 'name' ] ) ) );

										if ( isset( $variation_attributes_arr[ $variation[ 'variation_id' ] ] ) )
											$variation_attributes_arr[ $variation[ 'variation_id' ] ][ $attr_key ] = $attr;
										else
											$variation_attributes_arr[ $variation[ 'variation_id' ] ] = array( $attr_key => $attr );

										$attr_found = true;
										break;

									} elseif ( empty( $variation_val ) ) {

										if ( is_null( $friendly_variation_text ) )
											$friendly_variation_text = str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": Any";
										else
											$friendly_variation_text .= ", " . str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": Any";

										$attr_key = "attribute_" . str_replace( " " , "-" , strtolower( str_replace( ":" , "" , $attribute_arr[ 'name' ] ) ) );

										if ( isset( $variation_attributes_arr[ $variation[ 'variation_id' ] ] ) )
											$variation_attributes_arr[ $variation[ 'variation_id' ] ][ $attr_key ] = "Any";
										else
											$variation_attributes_arr[ $variation[ 'variation_id' ] ] = array( $attr_key => "Any" );

										$attr_found = true;
										break;

									}

								}

							}

							if ( $attr_found )
								break;

						}

					}

					if ( ( $product->managing_stock() === true && $product->get_total_stock() > 0 && $variation_obj->managing_stock() === true && $variation_obj->get_total_stock() > 0 && $variation_obj->is_purchasable() ) ||
						 ( $product->managing_stock() !== true && $variation_obj->is_in_stock() && $variation_obj->is_purchasable() ) ||
						 ( $variation_obj->backorders_allowed() && $variation_obj->is_purchasable() ) ) {

						//if ( $variation[ 'is_in_stock' ] && $variation_obj->is_purchasable() ) {
						$variation_arr[] = array(
							'value'      => $variation[ 'variation_id' ],
							'text'       => $friendly_variation_text,
							'disabled'   => false,
							'visible'    => true,
							'attributes' => $variation_attributes_arr
						);

					} else {

						$visibility = false;
						if ( $variation_obj->variation_is_visible() )
							$visibility = true;

						$variation_arr[] = array(
							'value'      => 0,
							'text'       => $friendly_variation_text,
							'disabled'   => true,
							'visible'    => $visibility,
							'attributes' => $variation_attributes_arr
						);

					}

				}

				wp_reset_postdata();

				usort( $variation_arr , array( 'TEO_Helper' , 'usort_variation_menu_order') ); // Sort variations via menu order

			}

            return $variation_arr;

        }

        /**
         * usort callback that sorts variations based on menu order.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $arr1
         * @param $arr2
         * @return int
         */
        public static function usort_variation_menu_order( $arr1 , $arr2 ) {

            $product1_id = $arr1[ 'value' ];
            $product2_id = $arr2[ 'value' ];

            $product1_menu_order = get_post_field( 'menu_order', $product1_id );
            $product2_menu_order = get_post_field( 'menu_order', $product2_id );

            if ( $product1_menu_order == $product2_menu_order )
                return 0;

            return ( $product1_menu_order < $product2_menu_order ) ? -1 : 1;

        }

        /**
         * It returns an array of Post objects.
         * Get all coupons of the shop via $wpdb.
         *
         *
         * @param null $limit
         * @param string $order_by
         * @return mixed
         */
        public static function get_all_coupons( $limit = null , $order_by = 'DESC' ) {

            global $wpdb;

            $query = "
                      SELECT *
                      FROM $wpdb->posts
                      WHERE post_status = 'publish'
                      AND post_type = 'shop_coupon'
                      ORDER BY $wpdb->posts.post_date " . $order_by . "
                    ";

            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

		/**
		 * Get info about a given coupon.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param $coupon_id Id of the coupon ( not the coupon code, the numeric id )
		 * @return array|WP_Error Array of coupon information if success, WP_Error on failure
		 */
        public static function get_coupon_info( $coupon_id ) {

			if ( is_string( get_post_status( $coupon_id ) ) && get_post_type( $coupon_id ) == 'shop_coupon' ) {

				$coupon_code = get_the_title( $coupon_id );
				$coupon_obj  = new WC_Coupon( $coupon_code );
				$coupon_types = wc_get_coupon_types();
                $coupon_obj_type = self::get_coupon_data( $coupon_obj , 'discount_type' );

				return array(
						'coupon_url'       => home_url( "/wp-admin/post.php?post=" . $coupon_id . "&action=edit" ),
						'coupon_type_text' => isset( $coupon_types[ $coupon_obj_type ] ) ? $coupon_types[ $coupon_obj_type ] : '',
						'coupon_obj'	   => $coupon_obj
				);

			} else
				return new WP_Error( __( "Invalid Coupon Id" , "timed-email-offers" ) , __( "Coupon Id supplied is invalid or does not exist" , "timed-email-offers" ) );

        }

		/**
		 * Check if current user is authorized to execute an operation within the 'Timed Email Offers' plugin.
		 *
		 * @access public
		 * @since 1.0.0
		 *
		 * @param TEO_Constants|null $constants
		 * @param null $user
		 * @return bool
		 */
		public static function current_user_authorized( TEO_Constants $constants = null , $user = null ) {

			if ( is_null( $constants ) )
				$constants = TEO_Constants::instance();

			$teo_admin_roles = $constants->ROLES_ALLOWED_TO_MANAGE_TEO();

			if ( is_null( $user ) )
				$user = wp_get_current_user();

			if ( $user->ID ) {

				if ( count( array_intersect( ( array ) $user->roles , $teo_admin_roles ) ) )
					return true;
				else
					return false;

			} else
				return false;

		}

		/**
		 * Get all timed email offers.
		 *
		 * @since 1.0.0
         * @since 1.2.0 Now sorts offers by offer order then by post_date.
		 * @access public
		 *
		 * @param $post_status
		 * @param null $limit
		 * @return array
		 */
		public static function get_all_timed_email_offers( $post_status = array( 'publish' ) , $limit = null ) {

			global $wpdb;

			$constants = TEO_Constants::instance();

			$comma_count     = count( $post_status ) - 1;
			$post_status_str = "";

			foreach ( $post_status as $stat ) {

				$post_status_str .= "'" . $stat . "'";

				if ( $comma_count > 0 ) {

					$post_status_str .= ",";
					$comma_count--;

				}

			}

			$query = "
                      SELECT posts_table.* , post_meta_table.meta_value AS offer_order
                      FROM $wpdb->posts posts_table
                      INNER JOIN $wpdb->postmeta as post_meta_table
                      ON posts_table.ID = post_meta_table.post_id
                      WHERE posts_table.post_status IN (" . $post_status_str . ")
                      AND posts_table.post_type = '" . $constants->OFFER_CPT_NAME() . "'
                      AND post_meta_table.meta_key = '" . $constants->POST_META_OFFER_ORDER() . "'
                      ORDER BY
                      ABS( post_meta_table.meta_value ) ASC,
                      posts_table.post_date DESC
                    ";

			if ( $limit && is_numeric( $limit ) )
				$query .= " LIMIT " . $limit;

            $timed_email_offers = $wpdb->get_results( $query );

            // The idea is that offers with empty offer order, they will always be on top, they should be last
            // So we pluck em out, then add em to the end of the result set.
            $offers_with_no_offer_order = array();

            foreach ( $timed_email_offers as $index => $offer ) {

                if ( $offer->offer_order == '' ) {

                    $offers_with_no_offer_order[] = $offer;
                    unset( $timed_email_offers[ $index ] );

                }

            }

            foreach ( $offers_with_no_offer_order as $offer )
                $timed_email_offers[] = $offer;

			return $timed_email_offers;

		}

        /**
         * Get all timed email offers.
         * Legacy, gets just the offers disregarding other parameters.
         * Main purpose is for migration.
         *
         * @since 1.2.0
         * @access public
         *
         * @return array
         */
        public static function get_all_timed_email_offers_legacy() {

			global $wpdb;

			$constants = TEO_Constants::instance();

			$query = "SELECT * FROM $wpdb->posts
                      WHERE $wpdb->posts.post_status = 'publish'
                      AND $wpdb->posts.post_type = '" . $constants->OFFER_CPT_NAME() . "'";

			return $wpdb->get_results( $query );

        }

		/**
		 * Get all orders from a customer via customer's email.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param $email
		 * @param string $status
		 * @param null $limit
		 * @param string $order_by
		 * @return mixed
		 */
		public static function get_all_orders_from_customer_via_email( $email , $status = "'wc-completed'" , $limit = null , $order_by = 'DESC' ) {

			global $wpdb;

			$query = "
                      SELECT * FROM $wpdb->posts post_table
                      INNER JOIN $wpdb->postmeta post_meta_table
                      ON post_meta_table.post_id = post_table.ID
                      WHERE post_table.post_status IN (" . $status . ") AND post_table.post_type = 'shop_order'
                      AND post_meta_table.meta_key = '_billing_email' AND post_meta_table.meta_value = '" . $email . "'
                      ORDER BY post_table.post_date " . $order_by . "
                    ";

			if ( $limit && is_numeric( $limit ) )
				$query .= " LIMIT " . $limit;

			return $wpdb->get_results( $query );

		}

		/**
		 * Get id of various woocommerce page.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param $page
		 * @return mixed
		 */
		public static function get_woocommerce_page_id( $page ) {

			return apply_filters( 'woocommerce_get_' . $page . '_page_id' , get_option( 'woocommerce_' . $page . '_page_id' ) );

		}

        /**
         * Sort datatables data. Supports sorting with multiple element as base for sorting.
         * Ex. sort by name, then by email, then by order no. and so on and so forth.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $a
         * @param $b
         * @param null $column Column or index to base sorting
         * @param null $dir Sort direction, 'asc' or 'desc'
         * @return int
         */
        public static function sort_datatables_data( $a , $b , $column = null , $dir = null ) {

            if ( is_null( $column ) || is_null( $dir ) ) {

                foreach( $_REQUEST[ 'order' ] as $order ) {

                    $result = self::sort_datatables_data( $a , $b , $order[ 'column' ] , $order[ 'dir' ] );

                    if ( $result == 0 )
                        continue;

                    return $result;

                }

            } else {

                if ( $a[ $column ] == $b[ $column ] )
                    return 0;

                if ( $dir == 'asc' )
                    return ( $a[ $column ] < $b[ $column ] ) ? -1 : 1;
                elseif ( $dir == 'desc' )
                    return ( $a[ $column ] > $b[ $column ] ) ? -1 : 1;

            }

        }

        /**
         * Get the user of an order.
         *
         * @since 1.2.0
         * @access public
         *
         * @param int $order_id WooCommerce Order id.
         * @return mixed Array of customer data on success, WP_Error on failure.
         */
        public static function get_order_user( $order_id ) {

            $order = wc_get_order( $order_id );

            if ( $order ) {

                $customer = $order->get_user();

                if ( !$customer ) {

                    $customer             = new stdClass();
                    $customer->ID         = 0;
                    $customer->first_name = TEO_Helper::get_order_data( $order , 'billing_first_name' );
                    $customer->last_name  = TEO_Helper::get_order_data( $order , 'billing_last_name' );
                    $customer->user_email = TEO_Helper::get_order_data( $order , 'billing_email' );
                    $customer->roles      = array( 'guest' );

                }

            } else
                return new WP_Error( 'teo-get_order_user-no-order-with-the-specified-id' , __( 'No order with the specified id' , 'timed-email-offers' ) , array( 'order_id' => $order_id ) );

            return $customer;

        }

        /**
         * Get all offer recipients by offer id.
         *
         * @since 1.2.0
         * @since 1.2.1 Add $status parameter to specify the status of the recipient to retrieve.
         * @access public
         *
         * @param int $offer_id Timed email offer id.
         * @return array Array of offer recipients.
         */
        public static function get_offer_recipients_by_offer_id( $offer_id , $status = 'all' ) {

            global $wpdb;

            $constants = TEO_Constants::instance();

            $query = "SELECT * FROM " . $constants->CUSTOM_TABLE_OFFER_RECIPIENTS() . " WHERE offer_id = $offer_id";

            if ( $status !== 'all' && array_key_exists( $status , $constants->RECIPIENT_OFFER_RESPONSE_STATUS() ) )
                $query .= " AND response_status = '" . $status . "'";

            return $wpdb->get_results( $query );

        }

        /**
         * Get offer recipient.
         *
         * @since 1.2.0
         * @access public
         *
         * @param int $order_id WooCommerce Order id.
         * @param int $offer_id Timed email offer id.
         * @return mixed Array of recipient data on success, null on failure.
         */
        public static function get_offer_recipient( $order_id , $offer_id ) {

            global $wpdb;

            $constants = TEO_Constants::instance();

            $query     = "SELECT * FROM " . $constants->CUSTOM_TABLE_OFFER_RECIPIENTS() . " WHERE order_id = $order_id AND offer_id = $offer_id LIMIT 1;";
            $recipient = $wpdb->get_results( $query );
            $recipient = !empty( $recipient ) ? $recipient[ 0 ] : null;

            return $recipient;

        }

        /**
         * Get offer recipient entry id.
         *
         * @since 1.2.0
         * @access public
         *
         * @param int $order_id WooCommerce Order id.
         * @param int $offer_id Timed email offer id.
         * @return int Offer recipient entry id.
         */
        public static function get_offer_recipient_id( $order_id , $offer_id ) {

            global $wpdb;

            $constants = TEO_Constants::instance();

            $query        = "SELECT recipient_id FROM " . $constants->CUSTOM_TABLE_OFFER_RECIPIENTS() . " WHERE order_id = $order_id AND offer_id = $offer_id LIMIT 1;";
            $recipient_id = $wpdb->get_results( $query );
            return !empty( $recipient_id ) ? $recipient_id[ 0 ]->recipient_id : 0;

        }

        /**
         * Get offer scheduled emails.
         *
         * @since 1.2.0
         * @access public
         *
         * @param int $recipient_id Offer recipient id.
         * @return array Offer scheduled emails.
         */
        public static function get_offer_scheduled_emails( $recipient_id ) {

            global $wpdb;

            $constants = TEO_Constants::instance();

            $query           = "SELECT * FROM " . $constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() . " WHERE recipient_id = $recipient_id;";
            $email_schedules = $wpdb->get_results( $query );

            return $email_schedules;

        }

        /**
         * Get offer scheduled email.
         *
         * @since 1.2.0
         * @access public
         *
         * @param string $email_token  Unique email token.
         * @param int    $recipient_id Offer recipient id.
         * @return array Array of data of a specific scheduled email.
         */
        public static function get_offer_scheduled_email( $email_token , $recipient_id ) {

            global $wpdb;

            $constants = TEO_Constants::instance();

            $query          = "SELECT * FROM " . $constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() . " WHERE email_token = '$email_token' AND recipient_id = $recipient_id;";
            $email_schedule = $wpdb->get_results( $query );
            $email_schedule = !empty( $email_schedule ) ? $email_schedule[ 0 ] : null;

            return $email_schedule;

        }

        /**
         * Check an order if it converted.
         *
         * @since 1.2.0
         * @access public
         *
         * @param int   $order_id               Order id.
         * @param array $order_completed_status Array of completed order statuses.
         * @return boolean True if order indeed converted, false otherwise.
         */
        public static function check_order_if_converted( $order_id , $order_completed_status = array( "'wc-completed'" ) ) {

            if ( !$order_id )
                return false;

            global $wpdb;

            $constants = TEO_Constants::instance();

            $order_completed_status = apply_filters( 'teo_completed_order_status' , $order_completed_status );
            $order_completed_status = implode( ',' , $order_completed_status );

            $query  = "SELECT * FROM $wpdb->posts WHERE post_type = 'shop_order' AND ID = $order_id AND post_status IN ( $order_completed_status );";
            $result = $wpdb->get_results( $query );

            return !empty( $result ) ? true : false;

        }

        /**
         * Log debug information to plugin debug.log file.
         *
         * @since 1.2.0
         * @access public
         *
         * @param string $msg Debug message to log.
         */
        public static function debug_log( $msg ) {

            $constants = TEO_Constants::instance();

            error_log( "\n[" . current_time( 'mysql' ) . "]\n" . $msg . "\n--------------------------------------------------\n" , 3 , $constants->LOGS_ROOT_PATH() . 'debug.log' );

        }

        /**
         * Get variation data by attributes.
         *
         * @since 1.2.0
         * @access public
         *
         * @param int   $variable_product_id Variable product id.
         * @param array $selected_attributes Selected variable product attributes.
         * @return int Variation id
         */
        public static function get_variation_data_by_attributes( $variable_product_id , $selected_attributes ) {

            $product = wc_get_product( $variable_product_id );

            if ( $product->product_type == 'variable' ) {

                $product_variations = $product->get_available_variations();
                $product_attributes = $product->get_attributes();
                $varaition_data     = array();

                foreach( $product_variations as $variation ) {

                    $variation_hit                  = true;
                    $varaition_data[ 'attributes' ] = array();

                    foreach ( $selected_attributes as $selected_attribute => $selected_attribute_value ) {

                        if ( !array_key_exists( 'attribute_' . $selected_attribute , $variation[ 'attributes' ] ) ||
                              strcasecmp( $variation[ 'attributes' ][ 'attribute_' . $selected_attribute ] , $selected_attribute_value ) !== 0 ) {

                            $variation_hit = false;
                            break;

                        }

                        $variation_data[ 'attributes' ][ $selected_attribute ] = $variation[ 'attributes' ][ 'attribute_' . $selected_attribute ];

                    }

                    if ( $variation_hit ) {

                        $variation_data[ 'variation_id' ] = $variation[ 'variation_id' ];
                        break;

                    }

                }

                return $variation_data;

            } else
                return false; // Not a variable product

        }

        /**
         * Get offer scheduled email total views.
         *
         * @since 1.2.0
         * @access public
         *
         * @param array   $conditions Array of sql conditions data.
         * @param boolean $unique     Flag that determines whether to return unique results.
         * @return array Array of results.
         */
        public static function get_total_email_views( $conditions , $unique ) {

            $conditions_str = "";

            foreach ( $conditions as $condition_key => $condition_data ) {

                $condition_val   = $condition_data[ 'string' ] ? "'" . $condition_data[ 'value' ] . "'" : $condition_data[ 'value' ];
                $conditions_str .= !empty( $conditions_str ) ? " AND" : "";
                $conditions_str .= " " . $condition_key . "=" . $condition_val;

            }

            $unique_str = $unique ? 'DISTINCT' : '';

            global $wpdb;

            $constants = TEO_Constants::instance();
            $query     = "SELECT " . $unique_str . " email_token , recipient_id FROM " . $constants->CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS() . " WHERE" . $conditions_str;
            return $wpdb->get_results( $query );

        }

	    /**
		 * Returns the timezone string for a site, even if it's set to a UTC offset
		 *
		 * Adapted from http://www.php.net/manual/en/function.timezone-name-from-abbr.php#89155
		 *
		 * Reference:
		 * http://www.skyverge.com/blog/down-the-rabbit-hole-wordpress-and-timezones/
		 *
		 * @since 1.2.0
		 * @access public
		 *
		 * @return string valid PHP timezone string
		 */
		public static function get_site_current_timezone() {

			// if site timezone string exists, return it
			if ( $timezone = get_option( 'timezone_string' ) )
				return $timezone;

			// get UTC offset, if it isn't set then return UTC
			if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) )
				return 'UTC';

			return self::convert_utc_offset_to_timezone( $utc_offset );

		}

		/**
		 * Conver UTC offset to timezone.
		 *
		 * @since 1.2.0
		 * @access public
		 *
		 * @param $utc_offset float/int/sting UTC offset.
		 * @return string valid PHP timezone string
		 */
		public static function convert_utc_offset_to_timezone( $utc_offset ) {

			// adjust UTC offset from hours to seconds
			$utc_offset *= 3600;

			// attempt to guess the timezone string from the UTC offset
			if ( $timezone = timezone_name_from_abbr( '' , $utc_offset , 0 ) )
				return $timezone;

			// last try, guess timezone string manually
			$is_dst = date( 'I' );

			foreach ( timezone_abbreviations_list() as $abbr ) {

				foreach ( $abbr as $city ) {

					if ( $city[ 'dst' ] == $is_dst && $city[ 'offset' ] == $utc_offset )
						return $city[ 'timezone_id' ];

				}

			}

			// fallback to UTC
			return 'UTC';

		}

        /**
         * Utility function that determines if a plugin is active or not.
         *
         * @since 1.2.1
         * @access public
         *
         * @param string $plugin_basename Plugin base name. Ex. woocommerce/woocommerce.php
         * @return boolean True if active, false otherwise.
         */
        public static function is_plugin_active( $plugin_basename ) {

            // Makes sure the plugin is defined before trying to use it
            if ( !function_exists( 'is_plugin_active' ) )
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            return is_plugin_active( $plugin_basename );

        }

        /**
         * Get data about the current woocommerce installation.
         *
         * @since 1.1.2
         * @access public
         * @return array Array of data about the current woocommerce installation.
         */
        public static function get_woocommerce_data() {

            if ( ! function_exists( 'get_plugin_data' ) )
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

            return get_plugin_data( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' );

        }

        /**
         * Get order properties based on the key. WC 2.7
         *
         * @since 1.1.2
         * @access public
         *
         * @param WC_Order $order  order object
         * @param string   $key    order property
         * @return string   order property
         */
        public static function get_order_data( $order , $key ) {

            if ( is_a( $order , 'WC_Order' ) ) {

                $woocommerce_data = self::get_woocommerce_data();

                if ( version_compare( $woocommerce_data[ 'Version' ] , '2.7.0' , '>=' ) || $woocommerce_data[ 'Version' ] === '2.7.0-RC1' ) {

                    switch ( $key ) {

                        case 'modified_date' :
                            return date( 'Y-m-d H:i:s', $order->get_date_modified() );
                            break;

                        case 'completed_date' :
                            return date( 'Y-m-d H:i:s', $order->get_date_completed() );
                            break;

                        case 'order_date' :
                            return date( 'Y-m-d H:i:s', $order->get_date_created() );
                            break;

                        case 'order_total' :
                            return $order->get_total();
                            break;

                        default:
                            $key = 'get_' . $key;
                            return $order->$key();
                            break;
                    }

                }
                else
                    return $order->$key;

            } else {

                error_log( 'TEO Error : get_order_data helper functions expect parameter $order of type WC_Order.' );
                return 0;

            }
        }

        /**
         * Get coupon properties based on the key. WC 2.7
         *
         * @since 1.1.2
         * @access public
         *
         * @param WC_Coupon $coupon  coupon object
         * @param string   $key    coupon property
         * @return string   coupon property
         */
        public static function get_coupon_data( $coupon , $key ) {

            if ( is_a( $coupon , 'WC_Coupon' ) ) {

                $woocommerce_data = self::get_woocommerce_data();

                if ( version_compare( $woocommerce_data[ 'Version' ] , '2.7.0' , '>=' ) || $woocommerce_data[ 'Version' ] === '2.7.0-RC1' ) {

                    switch ( $key ) {

                        case 'coupon_amount' :
                            return $coupon->get_amount();
                            break;

                        default:
                            $key = 'get_' . $key;
                            return $coupon->$key();
                            break;
                    }

                } else
                    return $coupon->$key;

            } else {

                error_log( 'TEO Error : get_coupon_data helper functions expect parameter $order of type WC_Coupon.' );
                return 0;

            }
        }

    }

}
