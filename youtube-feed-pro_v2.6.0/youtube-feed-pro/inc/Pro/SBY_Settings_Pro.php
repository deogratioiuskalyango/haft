<?php
namespace SmashBalloon\YouTubeFeed\Pro;

use SmashBalloon\YouTubeFeed\SBY_Settings;

class SBY_Settings_Pro extends SBY_Settings {

	protected function after_settings_set() {
		if ( $this->settings['type'] === 'search'
		     && $this->settings['sortby'] === 'none' ) {
			$this->settings['sortby'] = 'relevance';
		}
	}

	/**
	 * Based on the settings related to retrieving post data from the API,
	 * this setting is used to make sure all endpoints needed for the feed are
	 * connected and stored for easily looping through when adding posts
	 *
	 * Overwritten in the Pro version.
	 *
	 * @since 1.0
	 */
	public function set_feed_type_and_terms() {
		//global $sby_posts_manager;

		$connected_accounts_in_feed = array();
		$feed_type_and_terms = array();

		$connected_account = sby_get_first_connected_account();
		$channel_only = isset( $connected_account['api_key'] ) ? false : true;

		if ( $channel_only ) {

			$updated_hoverinclude = array();
			if ( \is_array( $this->settings['hoverinclude'] ) ) {
				foreach ( $this->settings['hoverinclude'] as $hoverinclude ) {
					if ( ! in_array( $hoverinclude, array( 'views', 'stats', 'countdown' ), true ) ) {
						$updated_hoverinclude[] = $hoverinclude;
					}
				}
				$this->settings['hoverinclude'] = $updated_hoverinclude;
			}

			if ( is_array( $this->settings['include'] ) ) {
				$updated_include = array();
				foreach ( $this->settings['include'] as $include ) {
					if ( ! in_array( $include, array( 'views', 'stats', 'countdown' ), true ) ) {
						$updated_include[] = $include;
					}
				}
				$this->settings['include'] = $updated_include;
			}

			global $sby_posts_manager;

			$message = '<p><b>' . __( 'Important: No API Key Entered.', 'feeds-for-youtube' ). '</b>';
			$message .= '<p>'. __( 'Many features are not available without adding an API Key. Please go to the YouTube Feeds settings page to add an API key after following <a href="https://smashballoon.com/youtube-api-key/" target="_blank" rel="noopener">these instructions.</a>', 'feeds-for-youtube' ). '</p>';

			$sby_posts_manager->add_frontend_error( 'no_api_key', $message );
		}

		if ( ! $channel_only && $this->settings['type'] === 'search' ) {
			$feed_type_and_terms = array( 'search' => array() );
			if ( $this->settings['usecustomsearch'] ) {
				$searches_array = is_array( $this->settings['customsearch'] ) ? $this->settings['customsearch'] : explode( ',', $this->settings['customsearch'] );
				foreach ( $searches_array as $search ) {
					$search_slug = trim( preg_replace( "/[^A-Za-z0-9]/", '', $search ) );
					$feed_type_and_terms['search'][] = array(
						'term' => $search_slug,
						'params' => array(
							'isCustom' => true,
							'customSearch' => $this->settings['customsearch'],
							'type' => 'video'
						)
					);
					$connected_accounts_in_feed[ $search_slug ] = $connected_account;
				}
			} elseif ( ! empty( $this->settings['search'] ) ) {
				$searches_array = is_array( $this->settings['search'] ) ? $this->settings['search'] : explode( ',', $this->settings['search'] );
				foreach ( $searches_array as $search ) {
					$search_slug = urlencode( trim( $search ) );
					$feed_type_and_terms['search'][] = array(
						'term' => $search_slug,
						'params' => array(
							'q' => trim( $search ),
							'type' => 'video'
						)
					);
					$connected_accounts_in_feed[ $search_slug ] = $connected_account;
				}
			}
		} elseif ( ! $channel_only && $this->settings['type'] === 'live' ) {
			$feed_type_and_terms = array( 'live' => array() );

			$raw_term_att = '';
			if ( ! empty( $this->settings['live'] ) ) {
				$raw_term_att = $this->settings['live'];
			} elseif ( ! empty( $this->settings['id'] ) ) {
				$raw_term_att = $this->settings['id'];
			} elseif ( ! empty( $this->settings['channel'] ) ) {
				$raw_term_att = $this->settings['channel'];
			}

			if ( ! empty( $raw_term_att ) ) {
				$channels_array = is_array( $raw_term_att ) ? $raw_term_att : explode( ',', $raw_term_att );
				if ( empty( $this->settings['headerchannel'] ) ) {
					$this->settings['headerchannel'] = $channels_array[0];
				}

				foreach ( $channels_array as $channel ) {
					$feed_type_and_terms['live'][] = array(
						'term' => $channel.'_live',
						'params' => array(
							'channelId' => trim( $channel ),
							'eventType' => 'live',
							'type' => 'video'
						)
					);
					$connected_accounts_in_feed[ $channel.'_live' ] = $connected_account;
				}
			}
		} elseif ( ! $channel_only && $this->settings['type'] === 'playlist' ) {
			$this->settings['sortby'] = 'api';
			$feed_type_and_terms = array( 'playlist' => array() );

			$raw_term_att = '';
			if ( ! empty( $this->settings['playlist'] ) ) {
				$raw_term_att = $this->settings['playlist'];
			} elseif ( ! empty( $this->settings['id'] ) ) {
				$raw_term_att = $this->settings['id'];
			}

			if ( ! empty( $raw_term_att ) ) {
				$playlist_array = is_array( $raw_term_att ) ? $raw_term_att : explode( ',', $raw_term_att );
				foreach ( $playlist_array as $playlist ) {
					$feed_type_and_terms['playlist'][] = array(
						'term' => $playlist,
						'params' => array(
							'playlistId' => $playlist,
						)
					);
					$connected_accounts_in_feed[ $playlist ] = $connected_account;
				}
			}
		} elseif ( ! $channel_only && $this->settings['type'] === 'favorites' ) {
			$feed_type_and_terms = array( 'favorites' => array() );
			if ( ! empty( $this->settings['id'] ) ) {
				$channel_array = is_array( $this->settings['id'] ) ? $this->settings['id'] : explode( ',', str_replace( ' ', '',  $this->settings['id'] ) );
				if ( empty( $this->settings['headerchannel'] ) ) {
					$this->settings['headerchannel'] = $channel_array[0];
				}
				foreach ( $channel_array as $channel ) {
					if ( isset( $this->connected_accounts[ $channel ] ) ) {
						$feed_type_and_terms['favorites'][] = array(
							'term' => $this->connected_accounts[ $channel ]['channel_id'],
							'params' => array(
								'channel_id' => $this->connected_accounts[ $channel ]['channel_id']
							)
						);
						$connected_accounts_in_feed[ $this->connected_accounts[ $channel ]['channel_id'] ] = $this->connected_accounts[ $channel ];
					}
				}

				if ( empty( $connected_accounts_in_feed ) ) {
					$an_account = array();
					foreach ( $this->connected_accounts as $account ) {
						if ( empty( $an_account ) ) {
							$an_account = $account;
						}
					}

					foreach ( $channel_array as $channel ) {
						$feed_type_and_terms['channels'][] = array(
							'term' => $channel,
							'params' => array(
								'channel_id' => $channel
							)
						);
						$connected_accounts_in_feed[ $channel ] = $an_account;
					}
				}

			} elseif ( ! $channel_only && ! empty( $this->settings['favorites'] ) ) {
				$channel_array = is_array( $this->settings['favorites'] ) ? $this->settings['favorites'] : explode( ',', str_replace( ' ', '',  $this->settings['favorites'] ) );

				$an_account = array();
				foreach ( $this->connected_accounts as $account ) {
					if ( empty( $an_account ) ) {
						$an_account = $account;
					}
				}

				if ( empty( $this->settings['headerchannel'] ) ) {
					$this->settings['headerchannel'] = $channel_array[0];
				}

				foreach ( $channel_array as $channel ) {
					if ( strpos( $channel, 'UC' ) !== 0 ) {
						$channel_id = sby_get_channel_id_from_channel_name( $channel );
						if ( $channel_id ) {
							$feed_type_and_terms['favorites'][] = array(
								'term' => $channel_id,
								'params' => array(
									'channel_id' => $channel_id
								)
							);
							$connected_accounts_in_feed[ $channel_id ] = $an_account;
						} else {
							$feed_type_and_terms['favorites'][] = array(
								'term' => $channel,
								'params' => array(
									'channel_name' => $channel
								)
							);
							$connected_accounts_in_feed[ $channel ] = $an_account;
						}

					} else {
						$feed_type_and_terms['favorites'][] = array(
							'term' => $channel,
							'params' => array(
								'channel_id' => $channel
							)
						);
						$connected_accounts_in_feed[ $channel ] = $an_account;
					}
				}

			} elseif ( ! empty( $this->settings['channel'] ) ) {
				$channel_array = is_array( $this->settings['channel'] ) ? $this->settings['channel'] : explode( ',', str_replace( ' ', '',  $this->settings['channel'] ) );

				$an_account = array();
				foreach ( $this->connected_accounts as $account ) {
					if ( empty( $an_account ) ) {
						$an_account = $account;
					}
				}

				foreach ( $channel_array as $channel ) {
					if ( strpos( $channel, 'UC' ) !== 0 ) {
						$channel_id = sby_get_channel_id_from_channel_name( $channel );
						if ( $channel_id ) {
							$feed_type_and_terms['favorites'][] = array(
								'term' => $channel_id,
								'params' => array(
									'channel_id' => $channel_id
								)
							);
							$connected_accounts_in_feed[ $channel_id ] = $an_account;
						} else {
							$feed_type_and_terms['favorites'][] = array(
								'term' => $channel,
								'params' => array(
									'channel_name' => $channel
								)
							);
							$connected_accounts_in_feed[ $channel ] = $an_account;
						}

					} else {
						$feed_type_and_terms['favorites'][] = array(
							'term' => $channel,
							'params' => array(
								'channel_id' => $channel
							)
						);
						$connected_accounts_in_feed[ $channel ] = $an_account;
					}
				}

			}
		} elseif ( ! $channel_only && $this->settings['type'] === 'single' ) {

			$feed_type_and_terms = array( 'single' => array() );
			$videos_array = array();
			if ( ! empty( $this->settings['id'] ) ) {
				$videos_array = is_array( $this->settings['id'] ) ? $this->settings['id'] : explode( ',', str_replace( ' ', '',  $this->settings['id'] ) );
			} elseif ( ! empty( $this->settings['single'] ) ) {
				$videos_array = is_array( $this->settings['single'] ) ? $this->settings['single'] : explode( ',', str_replace( ' ', '',  $this->settings['single'] ) );
			}

			$filtered_vids = array();
			foreach ( $videos_array as $single_video ) {
				if ( strpos( $single_video, '=' ) === false ) {
					$filtered_vids[] = $single_video;
				} else {
					$exploded = explode( '&', $single_video );
					$filtered_vids[] = $exploded[0];
				}
			}
			$videos_array = $filtered_vids;

			if ( ! empty( $videos_array ) ) {
				$feed_type_and_terms['single'][] = array(
					'term' => implode( '', $videos_array ),
					'params' => array(
						'video_ids' => $videos_array
					)
				);
				$connected_accounts_in_feed[ implode( '', $videos_array ) ] = $connected_account;
			}

		} else {
			$feed_type_and_terms = array( 'channels' => array() );
			if ( ! empty( $this->settings['id'] ) ) {
				$channel_array = is_array( $this->settings['id'] ) ? $this->settings['id'] : explode( ',', str_replace( ' ', '',  $this->settings['id'] ) );
				foreach ( $channel_array as $channel ) {
					if ( isset( $this->connected_accounts[ $channel ] ) ) {
						$feed_type_and_terms['channels'][] = array(
							'term' => $this->connected_accounts[ $channel ]['channel_id'],
							'params' => array(
								'channel_id' => $this->connected_accounts[ $channel ]['channel_id']
							)
						);
						$connected_accounts_in_feed[ $this->connected_accounts[ $channel ]['channel_id'] ] = $this->connected_accounts[ $channel ];
					}
				}

				if ( empty( $connected_accounts_in_feed ) ) {
					$an_account = array();
					foreach ( $this->connected_accounts as $account ) {
						if ( empty( $an_account ) ) {
							$an_account = $account;
						}
					}

					foreach ( $channel_array as $channel ) {
						$feed_type_and_terms['channels'][] = array(
							'term' => $channel,
							'params' => array(
								'channel_id' => $channel
							)
						);
						$connected_accounts_in_feed[ $channel ] = $an_account;
					}
				}

			} elseif ( ! empty( $this->settings['channel'] ) ) {
				$channel_array = is_array( $this->settings['channel'] ) ? $this->settings['channel'] : explode( ',', str_replace( ' ', '',  $this->settings['channel'] ) );

				$an_account = array();
				foreach ( $this->connected_accounts as $account ) {
					if ( empty( $an_account ) ) {
						$an_account = $account;
					}
				}

				foreach ( $channel_array as $channel ) {
					if ( strpos( $channel, 'UC' ) !== 0 ) {
						$channel_id = sby_get_channel_id_from_channel_name( $channel );
						if ( $channel_id ) {
							$feed_type_and_terms['channels'][] = array(
								'term' => $channel_id,
								'params' => array(
									'channel_id' => $channel_id
								)
							);
							$connected_accounts_in_feed[ $channel_id ] = $an_account;
						} else {
							$feed_type_and_terms['channels'][] = array(
								'term' => $channel,
								'params' => array(
									'channel_name' => $channel
								)
							);
							$connected_accounts_in_feed[ $channel ] = $an_account;
						}

					} else {
						$feed_type_and_terms['channels'][] = array(
							'term' => $channel,
							'params' => array(
								'channel_id' => $channel
							)
						);
						$connected_accounts_in_feed[ $channel ] = $an_account;
					}
				}

			} else {
				foreach ( $this->connected_accounts as $connected_account ) {
					if ( empty( $feed_type_and_terms['channels'] ) && is_array($connected_account) ) {
						$feed_type_and_terms['channels'][] = array(
							'term' => $connected_account['channel_id'],
							'params' => array(
								'channel_id' => $connected_account['channel_id']
							)
						);
						$connected_accounts_in_feed[ $connected_account['channel_id'] ] = $connected_account;
					}
				}
			}
		}


		$this->connected_accounts_in_feed = $connected_accounts_in_feed;
		$this->feed_type_and_terms = $feed_type_and_terms;
	}

	/**
	 * Uses the feed types and terms as well as as some
	 * settings to create a semi-unique feed id used for
	 * caching and other features.
	 *
	 * Overwritten in the Pro version.
	 *
	 * @param string $transient_name
	 *
	 * @since 1.0
	 */
	public function set_transient_name( $transient_name = '' ) {

		if ( ! empty( $transient_name ) ) {
			$this->transient_name = $transient_name;
		} elseif ( false && ! empty( $this->settings['feedid'] ) ) { //disabled due to new caching system not yet being used
			$this->transient_name = 'sby_' . $this->settings['feedid'];
		} else {
			$feed_type_and_terms = $this->feed_type_and_terms;

			$sby_transient_name = 'sby_';

			$sby_include_words = isset( $this->settings['includewords'] ) ? $this->settings['includewords'] : '';
			$sby_exclude_words = isset( $this->settings['excludewords'] ) ? $this->settings['excludewords'] : '';
			$cache_string_include = '';
			$cache_string_exclude = '';

			//Convert include words array into a string consisting of 3 chars each
			if ( ! empty( $sby_include_words ) ) {
				$sby_include_words_arr = explode(',', $sby_include_words);

				foreach( $sby_include_words_arr as $word ){
					$include_word = str_replace( str_split(' #'), '', $word );
					$cache_string_include .= substr( str_replace('%','', urlencode( $include_word ) ), 0, 3 );
				}

			}

			//Convert exclude words array into a string consisting of 3 chars each
			if ( ! empty( $sby_exclude_words ) ) {
				$sby_exclude_words_arr = explode( ',', $sby_exclude_words );

				foreach( $sby_exclude_words_arr as $word ){
					$exclude_word = str_replace( str_split( ' #' ) , '', $word );
					$cache_string_exclude .= substr( str_replace( '%','', urlencode( $exclude_word ) ), 0, 3 );
				}

			}

			//Figure out how long the first part of the caching string should be
			$cache_string_include_length = strlen( $cache_string_include );
			$cache_string_exclude_length = strlen( $cache_string_exclude );
			$cache_string_length = $cache_string_include_length + $cache_string_exclude_length;

			if ( isset( $feed_type_and_terms['channels'] ) ) {
				foreach ( $feed_type_and_terms['channels'] as $term_and_params ) {
					$channel = $term_and_params['term'];
					$sby_transient_name .= $channel;
				}
			} elseif ( isset( $feed_type_and_terms['playlist'] ) ) {
				foreach ( $feed_type_and_terms['playlist'] as $term_and_params ) {
					$playlist = substr( $term_and_params['term'], 0, 6 );
					$playlist_end = substr( $term_and_params['term'], -7 );
					$sby_transient_name .= $playlist . $playlist_end;
				}
			} elseif ( isset( $feed_type_and_terms['search'] ) ) {
				foreach ( $feed_type_and_terms['search'] as $term_and_params ) {
					$sby_transient_name .= 'Q?';
					$search = $term_and_params['term'];
					$length = strlen( $search );
					$sby_transient_name .= substr( $search, 0, 15 );
					if ( $length > 15 ) {
						$sby_transient_name .= substr( $search, -5, 5 );
					}
				}
			} elseif ( isset( $feed_type_and_terms['live'] ) ) {
				foreach ( $feed_type_and_terms['live'] as $term_and_params ) {
					$sby_transient_name .= $term_and_params['term'];
				}
			} elseif ( isset( $feed_type_and_terms['favorites'] ) ) {
				foreach ( $feed_type_and_terms['favorites'] as $term_and_params ) {
					$sby_transient_name .= 'F!';
					$channel = $term_and_params['term'];
					$sby_transient_name .= $channel;
				}
			} elseif ( isset( $feed_type_and_terms['single'] ) ) {
				foreach ( $feed_type_and_terms['single'] as $term_and_params ) {
					$sby_transient_name .= 'S!';
					$video = $term_and_params['term'];
					$sby_transient_name .= $video;
				}
			}

			$num = $this->settings['num'];

			$num_length = strlen( $num ) + 1;

			//Add both parts of the caching string together and make sure it doesn't exceed 45
			$sby_transient_name = substr( $sby_transient_name, 0, 45 - $num_length - $cache_string_length ) . $cache_string_include . $cache_string_exclude;

			$sby_transient_name .= '#' . $num;

			$this->transient_name = $sby_transient_name;
		}

	}

}
