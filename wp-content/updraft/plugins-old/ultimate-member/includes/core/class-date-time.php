<?php
namespace um\core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'um\core\Date_Time' ) ) {

	/**
	 * Class Date_Time
	 * @package um\core
	 */
	class Date_Time {

		/**
		 * Display time in specific format
		 *
		 * @param $format
		 *
		 * @return int|string
		 */
		public function get_time( $format ) {
			return current_time( $format );
		}

		/**
		 * Show a cool time difference between 2 timestamps
		 *
		 * @todo compare this function with WordPress native `human_time_diff()` and matbe refactored it.
		 *
		 * @param int $from
		 * @param int $to
		 *
		 * @return string
		 */
		public function time_diff( $from, $to = '' ) {
			$since = '';

			if ( empty( $to ) ) {
				$to = time();
			}

			$diff = (int) abs( $to - $from );
			if ( $diff < 60 ) {

				$since = __( 'just now', 'ultimate-member' );

			} elseif ( $diff < HOUR_IN_SECONDS ) {

				$mins = round( $diff / MINUTE_IN_SECONDS );
				if ( $mins <= 1 ) {
					$mins = 1;
				}

				// translators: %s: min time.
				$since = sprintf( _n( '%s min', '%s mins', $mins, 'ultimate-member' ), $mins );

			} elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {

				$hours = round( $diff / HOUR_IN_SECONDS );
				if ( $hours <= 1 ) {
					$hours = 1;
				}

				// translators: %s: hours.
				$since = sprintf( _n( '%s hr', '%s hrs', $hours, 'ultimate-member' ), $hours );

			} elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {

				$days = round( $diff / DAY_IN_SECONDS );
				if ( $days <= 1 ) {
					$days = 1;
				}

				if ( 1 === $days ) {
					// translators: %s: time.
					$since = sprintf( __( 'Yesterday at %s', 'ultimate-member' ), date_i18n( get_option( 'time_format' ), $from ) );
				} else {
					// translators: %1$s is a date; %2$s is a time.
					$since = sprintf( __( '%1$s at %2$s', 'ultimate-member' ), date_i18n( 'F d', $from ), date_i18n( get_option( 'time_format' ), $from ) );
				}
			} elseif ( $diff < 30 * DAY_IN_SECONDS && $diff >= WEEK_IN_SECONDS ) {

				// translators: %1$s is a date; %2$s is a time.
				$since = sprintf( __( '%1$s at %2$s', 'ultimate-member' ), date_i18n( 'F d', $from ), date_i18n( get_option( 'time_format' ), $from ) );

			} elseif ( $diff < YEAR_IN_SECONDS && $diff >= 30 * DAY_IN_SECONDS ) {

				// translators: %1$s is a date; %2$s is a time.
				$since = sprintf( __( '%1$s at %2$s', 'ultimate-member' ), date_i18n( 'F d', $from ), date_i18n( get_option( 'time_format' ), $from ) );

			} elseif ( $diff >= YEAR_IN_SECONDS ) {

				// translators: %1$s is a date; %2$s is a time.
				$since = sprintf( __( '%1$s at %2$s', 'ultimate-member' ), date_i18n( get_option( 'date_format' ), $from ), date_i18n( get_option( 'time_format' ), $from ) );

			}

			/**
			 * UM hook
			 *
			 * @type filter
			 * @title um_human_time_diff
			 * @description Change human time string
			 * @input_vars
			 * [{"var":"$since","type":"string","desc":"Disable UM Cron?"},
			 * {"var":"$diff","type":"int","desc":"Difference in seconds"},
			 * {"var":"$from","type":"int","desc":"From Timestamp"},
			 * {"var":"$to","type":"int","desc":"To Timestamp"}]
			 * @change_log
			 * ["Since: 2.0"]
			 * @usage add_filter( 'um_human_time_diff', 'function_name', 10, 4 );
			 * @example
			 * <?php
			 * add_filter( 'um_human_time_diff', 'my_human_time_diff', 10, 4 );
			 * function my_human_time_diff( $since, $diff, $from, $to ) {
			 *     // your code here
			 *     return $since;
			 * }
			 * ?>
			 */
			return apply_filters( 'um_human_time_diff', $since, $diff, $from, $to );
		}

		/**
		 * Get age.
		 *
		 * @todo working with timestamps in this function.
		 *
		 * @param string $then
		 *
		 * @return string
		 */
		public function get_age( $then ) {
			if ( ! $then ) {
				return '';
			}

			$then_ts   = strtotime( $then );
			$then_year = date( 'Y', $then_ts );
			$age       = date( 'Y' ) - $then_year;
			if ( strtotime( '+' . $age . ' years', $then_ts ) > current_time( 'timestamp' ) ) {
				$age--;
			}

			if ( 0 === $age ) {
				return __( 'Less than 1 year old', 'ultimate-member' );
			}

			// translators: %s: age.
			return sprintf( _n( '%s year old', '%s years old', $age, 'ultimate-member' ), $age );
		}

		/**
		 * Reformat dates
		 *
		 * @param $old
		 * @param $new
		 *
		 * @return string
		 */
		public function format( $old, $new ) {
			$datetime = new \DateTime( $old );
			$output   = $datetime->format( $new );
			return $output;
		}

		/**
		 * Get last 30 days as array
		 *
		 * @deprecated 2.8.0
		 *
		 * @param int $num
		 * @param bool $reverse
		 *
		 * @return array
		 */
		public function get_last_days( $num = 30, $reverse = true ) {
			_deprecated_function( __METHOD__, '2.8.0' );
			$d = array();
			for ( $i = 0; $i < $num; $i++ ) {
				$d[ date('Y-m-d', strtotime( '-' . $i . ' days' ) ) ] = date( 'm/d', strtotime( '-' . $i . ' days' ) );
			}

			return ( $reverse ) ? array_reverse( $d ) : $d;
		}
	}
}
