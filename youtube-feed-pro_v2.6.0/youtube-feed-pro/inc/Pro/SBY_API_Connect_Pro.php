<?php
/**
 * Class SB_Instagram_API_Connect_Pro
 *
 * Adds support for additional endpoints:
 *
 * - Personal account comments
 * - Business account top and recent hashtags
 * - Business account stories
 * - Business account comments
 * - Business account hashtag IDs
 *
 * @since 5.0
 */
namespace SmashBalloon\YouTubeFeed\Pro;

use SmashBalloon\YouTubeFeed\SBY_API_Connect;

class SBY_API_Connect_Pro extends SBY_API_Connect
{
	public function connect() {
		$url = $this->get_url();

		if ( strpos( $url, 'iframe' ) === 0 ) {
			$response = $this->get_iframe_response();
			$response = json_decode( str_replace( '%22', '&rdquo;', $response ), true );
		} else {
			$response = wp_remote_get( esc_url_raw( $url ), $this->get_args() );

			if ( ! is_wp_error( $response ) ) {
				// certain ways of representing the html for double quotes causes errors so replaced here.
				$response = json_decode( str_replace( '%22', '&rdquo;', $response['body'] ), true );
			}

		}

		$this->response = $response;

	}

	private function get_iframe_response() {
		return '{"kind":"youtube#playlistItemListResponse","nextPageToken":"single","items":[{"iframe":"'.str_replace(  'iframe_', '', $this->get_url() ).'","id":"blank","snippet":{"publishedAt":"2020-04-01T17:45:02.000Z","channelId":"blank","title":"","description":"","thumbnails":{"default":{"url":"'.trailingslashit( SBY_PLUGIN_URL ) . 'img/placeholder.png'.'","width":120,"height":90},"medium":{"url":"'.trailingslashit( SBY_PLUGIN_URL ) . 'img/placeholder.png'.'","width":320,"height":180},"high":{"url":"'.trailingslashit( SBY_PLUGIN_URL ) . 'img/placeholder.png'.'","width":480,"height":360},"standard":{"url":"'.trailingslashit( SBY_PLUGIN_URL ) . 'img/placeholder.png'.'","width":640,"height":480},"maxres":{"url":"'.trailingslashit( SBY_PLUGIN_URL ) . 'img/placeholder.png'.'","width":1280,"height":720}},"channelTitle":"","playlistId":"UU-blank","position":0,"resourceId":{"kind":"youtube#video","videoId":"iframe"},"contentDetails":{"videoId":"iframe","videoPublishedAt":"2020-04-01T17:45:02.000Z"},"status":{"privacyStatus":"public"}}}]}';
	}

	public function set_response( $response ) {
		$this->response = $response;
	}


	public function get_next_page( $params = false ) {

		if ( $params && isset( $params['video_ids'] ) ) {
			if ( isset( $params['nextPageToken'] ) ) {
				if ( count( $params['nextPageToken'] ) > SBY_MAX_SINGLE_PAGE ) {
					return array_slice( $params['nextPageToken'], SBY_MAX_SINGLE_PAGE );
				} else {
					return '';
				}
			} elseif ( count( $params['video_ids'] ) > SBY_MAX_SINGLE_PAGE ) {
				return array_slice( $params['video_ids'], SBY_MAX_SINGLE_PAGE );
			} else {
				return '';
			}
		} else {
			if ( ! empty( $this->response['nextPageToken'] ) ) {
				return $this->response['nextPageToken'];
			} else {
				return '';
			}
		}

	}

	/**
	 * Sets the url for the API request based on the account information,
	 * type of data needed, and additional parameters
	 *
	 * @param $connected_account
	 * @param $endpoint_slug header or user
	 * @param $params
	 *
	 * @since 1.0
	 */
	public function set_url( $connected_account, $endpoint_slug, $params = [], $api_key = null ) {
		$num = ! empty( $params['num'] ) ? (int)$params['num'] : 50;

		$access_credentials = isset( $connected_account['api_key'] ) ? 'key=' . $connected_account['api_key'] : 'access_token=' . $connected_account['access_token'];
		$next_page = '';
		if ( isset( $params['nextPageToken'] ) && ! is_array( $params['nextPageToken'] ) ) {
			$next_page = '&pageToken=' . $params['nextPageToken'];
		}

		if ( $endpoint_slug === 'tokeninfo' ) {
			$url = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $connected_account['access_token'];
		} elseif ( $endpoint_slug === 'channels' ) {
			$channel_param = 'mine=true';
			if ( isset( $params['channel_name'] ) ) {
				$channel_param = 'forUsername=' . $params['channel_name'];
			} elseif ( isset( $params['channel_id'] ) ) {
				$channel_param = 'id=' . $params['channel_id'];
			}

			$url = 'https://www.googleapis.com/youtube/v3/channels?part=id,snippet,statistics,contentDetails&'.$channel_param.'&' . $access_credentials . $next_page;
		} elseif ( $endpoint_slug === 'live' ) {
			$url = 'iframe_'.$params['channelId'];
		} elseif ( $endpoint_slug === 'search' ) {
			$part = 'snippet';
			if ( isset( $params['part'] ) ) {
				$part = $this->formatted_part_param_string( $params['part'] );
			}
			if ( ! isset( $params['isCustom'] ) ) {
				if ( isset( $params['eventType'] ) && $params['eventType'] === 'upcoming' ) {
					$num = 50; // get max videos so we can reverse sort them to show soonest playing live streams first, default order is by publish date
				}
				$params_string = $this->formatted_param_string( $params );

			} else {
				$params_string = $params['customSearch'];

				if ( isset( $params['nextPageToken'] ) ) {
					$params_string .= '&pageToken=' . $params['nextPageToken'];
				}
			}
			$num = max( 10, $num );

			$query_var_string= 'type=video&part='.$part.'&maxResults=' . $num . $params_string;

			$url = 'https://www.googleapis.com/youtube/v3/search?'.$query_var_string.'&'.$access_credentials.$next_page;
		} elseif ( $endpoint_slug === 'playlistItems' ) {
			$url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=id,snippet,contentDetails,status&maxResults='.$num.'&playlistId='.$params['playlist_id'].'&' . $access_credentials.$next_page;
		} elseif ( $endpoint_slug === 'single' ) {
			$part = 'id,statistics,contentDetails,snippet,liveStreamingDetails';

			$vid_ids = empty( $params['nextPageToken'] ) ? $params['video_ids'] : $params['nextPageToken'];
			$vid_ids = array_slice( $vid_ids, 0, SBY_MAX_SINGLE_PAGE );
			$vid_id_string  = implode( ',', $vid_ids );

			$url = 'https://www.googleapis.com/youtube/v3/videos?part='.$part.'&id='.$vid_id_string.'&maxResults=50&' . $access_credentials;

		} elseif ( $endpoint_slug === 'videos' ) {
			$params_string = $this->formatted_param_string( $params );
			$part = 'id,statistics,contentDetails';
			if ( isset( $params['part'] ) ) {
				$part = $this->formatted_part_param_string( $params['part'] );
			}

			$url = 'https://www.googleapis.com/youtube/v3/videos?part='.$part.$params_string.'&maxResults='.$num.'&' . $access_credentials;
		} elseif ( $endpoint_slug === 'videosDuration' ) {
			$ids = isset( $params['ids'] ) ? $params['ids'] : [];
			$part = 'id,statistics,contentDetails';
			$url = 'https://www.googleapis.com/youtube/v3/videos?part='.$part.'&id='.$ids.'&maxResults='.$num.'&' . $access_credentials;
		} elseif ( $endpoint_slug === 'comments' ) {
			$part = 'snippet,replies';
			$video_id = isset( $params['video_id'] ) ? $params['video_id'] : [];
			$url = 'https://www.googleapis.com/youtube/v3/commentThreads?part='. $part .'&videoId='.$video_id.'&order=relevance&maxResults='.$num.'&' . $access_credentials;
		} elseif ( $endpoint_slug === 'livestream' ) {
			$page_token_param = '';
			$part = 'id';
			$channel_id = isset( $params['channel_id'] ) ? $params['channel_id'] : '';
			$event_type = isset( $params['event_type'] ) ? $params['event_type'] : '';
			$page_token = isset( $params['page_token'] ) ? $params['page_token'] : '';

			if ( !empty($page_token) ) {
				$page_token_param = '&pageToken=' . $page_token;
			}

			$url = 'https://www.googleapis.com/youtube/v3/search?part='. $part .'&channelId='.$channel_id .'&eventType='.$event_type .'&order=date&maxResults=50&type=video&' . $access_credentials . $page_token_param;

		} else {
			$channel_param = 'mine=true';
			if ( isset( $params['username'] ) ) {
				$channel_param = 'forUsername=' . $params['username'];
			} elseif ( isset( $params['channel_id'] ) ) {
				$channel_param = 'id=' . $params['channel_id'];
			}

			$url = 'https://www.googleapis.com/youtube/v3/channels?part=id,snippet&'.$channel_param.'&' . $access_credentials.$next_page;
		}

		$this->set_url_from_args( $url );
	}
}
