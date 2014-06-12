<?php

/**
 * Twitter widget
 */
class CACAP_Widget_Twitter extends CACAP_Widget {
	var $default_title;
	public function __construct() {
		$this->default_title = __( 'Twitter', 'cacap' );
		parent::init( array(
			'name' => __( 'Twitter', 'cacap' ),
			'slug' => 'twitter',
			'allow_custom_title' => true,
			'allow_multiple' => true,
		) );
	}

	/**
	 * Saves instance of Twitter widget for user
	 *
	 * Overrides the parent method, because on the default schema, Twitter
	 * widgets are not stored in xprofile data tables (since users can
	 * create arbitrary Twitter widgets, making it impossible to map onto
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

		// Sanitize Twitter handle
		$url_pattern = '|twitter\.com/([^/]+)/?|';
		preg_match( $url_pattern, $r['content'], $url_matches );

		$at_pattern = '|^@(.+)$|';
		preg_match( $at_pattern, $r['content'], $at_matches );

		if ( ! empty( $url_matches[1] ) ) {
			$r['content'] = $url_matches[1];
		}

		if ( ! empty( $at_matches[1] ) ) {
			$r['content'] = $at_matches[1];
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
		$title = $this->default_title;
		if ( ! empty( $value['title'] ) ) {
			$title = esc_html( $value['title'] );
		}

		return $title;
	}

	/**
	 * Todo: needs mucho caching
	 */
	public function display_content_markup( $value ) {
		$config = array(
			'class' => 'twitter-timeline',
			'data-dnt' => 'true',
			'href' => 'https://twitter.com/' . $value,
			'data-widget-id' => '434125814427172864',
			'data-screen-name' => $value,
		);

		$atts = '';
		foreach ( $config as $k => $v ) {
			$atts .= $k . '="' . esc_attr( $v ) . '" ';
		}

		$html  = '<a ' . $atts . '>';
		$html .= sprintf( __( 'Tweets by @%s', 'cacap' ), esc_attr( $value ) );
		$html .= '</a>';
		$html .= '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

		return $html;
	}

	public function edit_title_markup( $value, $key ) {
		$title = ! empty( $value['title'] ) ? $value['title'] : $this->default_title;
		$html  = '<article class="editable-content" contenteditable="true">' . esc_html( strip_tags( $title ) ) . '</article>';
		$html .= '<input name="' . $key . '[title]" class="editable-content-stash" type="hidden" value="' . esc_attr( strip_tags( $title ) ) . '" />';
		return $html;
	}

	public function edit_content_markup( $value, $key ) {
		$content = isset( $value['content'] ) ? $value['content'] : '';
		$html  = '<input class="cacap-edit-input" name="' . esc_attr( $key ) . '[content]" value="' . esc_attr( $content ) . '" />';
		$html .= '<p class="description">' . __( 'Enter your Twitter username.', 'cacap' ) . '</p>';
		return $html;
	}
}
