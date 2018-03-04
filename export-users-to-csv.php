<?php
/*
Plugin Name: Export Users to CSV
Plugin URI: http://wordpress.org/extend/plugins/export-users-to-csv/
Description: Export Users data and metadata to a csv file.
Version: 1.1.1
Author: Matt Cromwell
Author URI: https://www.mattcromwell.com/products/export-users-to-csv
License: GPL2
Text Domain: export-users-to-csv
*/
/*  Copyright 2017  Matt Cromwell  (http://github.com/mathetos/export-users-to-csv)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

load_plugin_textdomain( 'export-users-to-csv', false, basename( dirname( __FILE__ ) ) . '/languages' );

/**
 * Main plugin class
 *
 * @since 0.1
 **/
class PP_EU_Export_Users {

	/**
	 * Class contructor
	 *
	 * @since 0.1
	 **/
	public function __construct() {
		add_filter( 'export_filters', array( $this, 'filter_export_args' ) );
		add_action( 'export_wp', array( $this, 'generate_csv' ) );
		add_filter( 'pp_eu_exclude_data', array( $this, 'exclude_data' ) );
		add_action( 'init', array( $this, 'load_textdomain' ), 0 );
		add_action('admin_notices', array($this, 'eutc_add_export_button') );
		$this->setup_constants();

	}

	private function setup_constants() {
		// Plugin version
		if ( ! defined( 'EUTC_VERSION' ) ) {
			define( 'EUTC_VERSION', '1.1' );
		}
		// Plugin Root File
		if ( ! defined( 'EUTC_PLUGIN_FILE' ) ) {
			define( 'EUTC_PLUGIN_FILE', __FILE__ );
		}
		// Plugin Folder Path
		if ( ! defined( 'EUTC_PLUGIN_DIR' ) ) {
			define( 'EUTC_PLUGIN_DIR', plugin_dir_path( EUTC_PLUGIN_FILE ) );
		}
	}

	public function load_textdomain() {
        $eutc_lang_dir = dirname( plugin_basename( EUTC_PLUGIN_FILE ) ) . '/languages/';
        $eutc_lang_dir = apply_filters( 'eutc_languages_directory', $eutc_lang_dir );
        $locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
        $locale = apply_filters( 'plugin_locale', $locale, 'export-users-to-csv' );
        unload_textdomain( 'export-users-to-csv' );
        load_textdomain( 'export-users-to-csv', WP_LANG_DIR . '/export-users-to-csv/export-users-to-csv-' . $locale . '.mo' );
        load_plugin_textdomain( 'export-users-to-csv', false, $eutc_lang_dir );
	}

	public function filter_export_args() {
	    ?>
        <style>
            .eutcsv_leave_review {
                background-color: #d1ead3;
                border-left-color: #68bb6c;
                color: #2b5f2d;
                border-style: solid;
                border-width: 0 0 0 12px;
                display: block;
                margin-bottom: 24px;
                padding: 12px 20px;
                line-height: 0.7;
                font-size: 110%;
            }
        </style>

        <script type="text/javascript">
            jQuery(document).ready(function($){

                var form = $('#export-filters'),
                    review = $('.eutcsv_leave_review'),
                    filters = form.find('.export-filters');
                filters.hide();
                review.hide();
                $( 'input[value="Download Export File"]' ).on( "click", function() {
                    $(review).delay(2000).slideDown(500);
                });

                form.find('input:radio').off('change').change(function() {
                    filters.slideUp('fast');
                    switch ( $(this).val() ) {
                        case 'posts': $('#post-filters').slideDown(); break;
                        case 'pages': $('#page-filters').slideDown(); break;
                        case 'users': $('#users-filters').slideDown(); break;
                    }
                });
            });
        </script>
        <fieldset>
            <p>
                <label>
                    <input type="radio" name="content" value="users"><?php echo __('Users', 'export-users-to-csv'); ?>
                </label>
            </p>
            <ul id="users-filters" class="export-filters">
                <li>
                    <label><span class="label-responsive"><?php echo __('Role:', 'export-users-to-csv'); ?></span>

                        <select name="role" id="pp_eu_users_role" class="postform">
                            <?php
                            echo '<option value="">' . __( 'Every Role', 'export-users-to-csv' ) . '</option>';
                            global $wp_roles;
                            foreach ( $wp_roles->role_names as $role => $name ) {
                                echo "\n\t<option value='" . esc_attr( $role ) . "'>$name</option>";
                            }
                            ?>
                        </select>
                    </label>
                </li>
                <li>
                    <label><span class="label-responsive"><?php echo __('Date Range:', 'export-users-to-csv'); ?></span>
                    <select name="start_date" id="pp_eu_users_start_date">
                        <option value="0"><?php _e( 'Start Date', 'export-users-to-csv' ); ?></option>
                        <?php $this->export_date_options(); ?>
                    </select>
                    <select name="end_date" id="pp_eu_users_end_date">
                        <option value="0"><?php _e( 'End Date', 'export-users-to-csv' ); ?></option>
                        <?php $this->export_date_options(); ?>
                    </select>
                </li>
            </ul>
        </fieldset>
        <div class="eutcsv_leave_review">
            <h4><?php echo __('Success!', 'export-users-to-csv' ); ?></h4>
            <p><?php echo __('Your file should be downloaded now.', 'export-users-to-csv');?></p>
            <p><?php echo __('If "Export Users to CSV" has been useful for you, please take a minute to let me know by <a href="https://wordpress.org/support/plugin/export-users-to-csv/reviews/?filter=5">leaving a great rating here</a>.', 'export-users-to-csv'); ?></p>
        </div>
        <?php
    }

	/**
	 * Process content of CSV file
	 *
	 * @since 0.1
	 **/
	public function generate_csv( $args ) {

		if ( 'users' == $args['content'] ) {

			$defaults = array( 'content'    => 'all',
			                   'author'     => false,
			                   'category'   => false,
			                   'start_date' => false,
			                   'end_date'   => false,
			                   'status'     => false,
			);

			$user_args = array(
				'role'   => wp_kses_post( $_GET['role'] ),
				'fields' => 'all_with_meta',
			);

			$merge_args = array_merge( $defaults, $user_args );

			$args = wp_parse_args( $args, $merge_args );

			add_action( 'pre_user_query', array( $this, 'pre_user_query' ) );
			$users = get_users( $args );
			remove_action( 'pre_user_query', array( $this, 'pre_user_query' ) );

			if ( ! $users ) {
				$referer = add_query_arg( 'error', 'empty', wp_get_referer() );
				wp_redirect( $referer );
				exit;
			}

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			if ( ! empty( $sitename ) ) {
				$sitename .= '.';
			}
			$filename = $sitename . 'users.' . date( 'Y-m-d-H-i-s' ) . '.csv';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );

			$exclude_data = apply_filters( 'pp_eu_exclude_data', array() );

			global $wpdb;

			$data_keys = array(
				'ID',
				'user_login',
				'user_pass',
				'user_nicename',
				'user_email',
				'user_url',
				'user_registered',
				'user_activation_key',
				'user_status',
				'display_name'
			);
			$meta_keys = $wpdb->get_results( "SELECT distinct(meta_key) FROM $wpdb->usermeta" );
			$meta_keys = wp_list_pluck( $meta_keys, 'meta_key' );
			$fields    = array_merge( $data_keys, $meta_keys );

			$headers = array();

			foreach ( $fields as $key => $field ) {
				if ( in_array( $field, $exclude_data ) ) {
					unset( $fields[ $key ] );
				} else {
					$headers[] = '"' . strtolower( $field ) . '"';
				}
			}

			echo implode( ',', $headers ) . "\n";

			foreach ( $users as $user ) {
				$data = array();
				foreach ( $fields as $field ) {
					$value  = isset( $user->{$field} ) ? $user->{$field} : '';
					$value  = is_array( $value ) ? serialize( $value ) : $value;
					$data[] = '"' . str_replace( '"', '""', $value ) . '"';
				}

				echo implode( ',', $data ) . "\n";
			}

			exit;
		}
	}

	public function exclude_data() {
		$exclude = array( 'user_pass', 'user_activation_key' );

		return $exclude;
	}

	public function pre_user_query( $user_search ) {
		global $wpdb;

		$where = '';

		if ( ! empty( $_GET['start_date'] ) )
			$where .= $wpdb->prepare( " AND $wpdb->users.user_registered >= %s", date( 'Y-m-d', strtotime( $_GET['start_date'] ) ) );

		if ( ! empty( $_GET['end_date'] ) )
			$where .= $wpdb->prepare( " AND $wpdb->users.user_registered < %s", date( 'Y-m-d', strtotime( '+1 month', strtotime( $_GET['end_date'] ) ) ) );

		if ( ! empty( $where ) )
			$user_search->query_where = str_replace( 'WHERE 1=1', "WHERE 1=1$where", $user_search->query_where );

		return $user_search;
	}

	private function export_date_options() {
		global $wpdb, $wp_locale;

		$months = $wpdb->get_results( "
			SELECT DISTINCT YEAR( user_registered ) AS year, MONTH( user_registered ) AS month
			FROM $wpdb->users
			ORDER BY user_registered DESC
		" );

		$month_count = count( $months );
		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;

		foreach ( $months as $date ) {
			if ( 0 == $date->year )
				continue;

			$month = zeroise( $date->month, 2 );
			echo '<option value="' . $date->year . '-' . $month . '">' . $wp_locale->get_month( $month ) . ' ' . $date->year . '</option>';
		}
	}

	function eutc_add_export_button(){
		$screen = get_current_screen();
		if( $screen->id !='users' ){
			return;
		} else {
			?>
            <div class="wrap export-users">
                <a href="<?php echo admin_url( 'export.php' );?>" class="page-title-action">Export Users</a>
            </div>

            <style scoped>
                .wrap.export-users {
                    float: none;
                    display: inline;
                    position: absolute;
                    left: 12em;
                    top: 1.45em;
                }
            </style>
			<?php
		}
	}
}

new PP_EU_Export_Users;
