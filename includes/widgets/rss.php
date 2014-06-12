<?php

class CACAP_Widget_RSS extends CACAP_Widget {
	var $default_title;

	public function __construct() {
		$this->default_title = __( 'My RSS Feed', 'cacap' );

		parent::init( array(
			'name' => __( 'RSS Feed', 'cacap' ),
			'slug' => 'rss',
			'allow_custom_title' => true,
			'allow_multiple' => true,
		) );
	}

	/**
	 * Saves instance of RSS widget for user
	 *
	 * Overrides the parent method, because on the default schema, RSS
	 * widgets are not stored in xprofile data tables (since users can
	 * create arbitrary RSS widgets, making it impossible to map onto
	 * xprofile fields)
	 *
	 * @since 1.0
	 */
	public function save_instance_for_user( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'key' => '',
			'user_id' => 0,
			'title' => '',
			'content' => '',
		) );

		// @todo better error reporting
		if ( ! $r['user_id'] || ! $r['title'] ) {
			return false;
		}

		$meta_value = array(
			'title' => $r['title'],
			'content' => $r['content'],
		);

		// @todo - uniqueness? what about updating existing?
		$meta_key = empty( $r['key'] ) ? 'cacap_widget_instance_' . sanitize_title_with_dashes( $r['title'] ) : $r['key'];

		if ( update_user_meta( $r['user_id'], $meta_key, $meta_value ) ) {
			return CACAP_Widget_Instance::format_instance( array(
				'user_id' => $r['user_id'],
				'key' => $meta_key,
				'value' => $meta_value,
				'widget_type' => $this->slug,
			) );
		} else {
			// do something bad
		}
	}

	public function get_instance_for_user( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'user_id' => 0,
			'key' => null,
		) );

		return get_user_meta( absint( $r['user_id'] ), $r['key'], true );
	}

	public function get_display_value_from_value( $value ) {
		return $value['content'];
	}

	/**
	 * Return the HTML-ready title of the widget
	 *
	 * We override the parent method because the title is stored in the
	 * $value variable
	 *
	 * @param array $value
	 * @return string
	 */
	public function display_title_markup( $value ) {
		$title = ! empty( $value['title'] ) ? $value['title'] : $this->default_title;
		return $title;
	}

	/**
	 * Todo: needs mucho caching
	 */
	public function display_content_markup( $value ) {
		$html = $this->format_rss_items_html( $value );
		return $html;
	}

	public function edit_title_markup( $value, $key ) {
		$title = ! empty( $value['title'] ) ? $value['title'] : $this->default_title;
		$html  = '<article class="editable-content" contenteditable="true">' . esc_html( strip_tags( $title ) ) . '</article>';
		$html .= '<input name="' . esc_attr( $key ) . '[title]" class="editable-content-stash" type="hidden" value="' . esc_attr( strip_tags( $title ) ) . '" />';

		return $html;
	}

	public function edit_content_markup( $value, $key ) {
		$content = isset( $value['content'] ) ? $value['content'] : '';
		$html  = '<input class="cacap-edit-input" name="' . esc_attr( $key ) . '[content]" value="' . esc_attr( $content ) . '" />';
		$html .= '<p class="description">' . __( 'Enter the URL of your RSS feed (eg <code>http://news.commons.gc.cuny.edu/feed</code>)', 'cacap' ) . '</p>';
		return $html;
	}

	public function format_rss_items_html( $feed_url, $num_items = 5 ) {
		$items = $this->fetch_rss_items( $feed_url, $num_items );

		$html = '';

		$html .= '<ul class="cacap-rss-items">';

		foreach ( $items as $item ) {
			$html .= '<li>';

			$html .=   '<div class="cacap-feed-item-title">';
			$html .=     '<a href="' . esc_attr( $item['permalink'] ) . '">';
			$html .=       esc_html( $item['title'] );
			$html .=     '</a>';
			$html .=   '</div>';

			$html .=   '<div class="cacap-feed-item-content">';
			$html .=     $item['content'];
			$html .=   '</div>';

			$html .=   '<div class="cacap-feed-item-meta">';

			// could probably use simplepie methods to avoid some of this
			if ( ! empty( $item['author'] ) && is_object( $item['author'] ) && ! empty( $item['author']->name ) ) {
				$html .= '<span class="cacap-feed-item-byline">';
				$html .=   esc_html( $item['author']->name );
				$html .= '</span>';
			}

			if ( ! empty( $item['date'] ) ) {
				$html .= '<span class="cacap-feed-item-date">';
				$html .=   esc_html( $item['date'] );
				$html .= '</span>';
			}

			$html .=   '</div>';

			$html .= '</li>';
		}

		$html .= '</ul>';

		return $html;
	}

	public function fetch_rss_items( $feed_url, $num_items = 5 ) {
		$transient_key = substr( md5( 'cacap_external_posts_' . $feed_url . '_' . $num_items ), 0, 40 );

		$items = get_transient( $transient_key );
		if ( empty( $items ) ) {
			$items = $this->format_rss_items( $feed_url, $num_items );
			set_transient( $transient_key, $items, 5 * 60 );
		}

		return $items;
	}

	/**
	 * Given an RSS feed URL, fetch the items and parse into an array containing permalink, title,
	 * and content
	 */
	public function format_rss_items( $feed_url, $num_items = 5 ) {
		$feed_posts = fetch_feed( $feed_url );

		if ( empty( $feed_posts ) || is_wp_error( $feed_posts ) ) {
			return;
		}

		$items = array();

		foreach( $feed_posts->get_items( 0, $num_items ) as $key => $feed_item ) {
			$items[] = array(
				'permalink' => $feed_item->get_link(),
				'title'     => $feed_item->get_title(),
				'content'   => strip_tags( bp_create_excerpt( $feed_item->get_content(), 135, array( 'html' => true ) ) ),
				'author'    => $feed_item->get_author(),
				'date'      => $feed_item->get_date()
			);
		}

		return $items;
	}
}
