<?php

/**
 * Widget_Instance is an instance of a user widget, tied to a widget type as
 * well as to a specific user id
 *
 * For example, if users 4 and 5 each have a Text widget and a Positions widget
 * on their profile, there would be a total of 4 Widget_Instances
 *
 * @since 1.0
 */
class CACAP_Widget_Instance {
	public $user_id;
	public $widget_type;
	public $key;
	public $value;
	public $position;

	/**
	 * Constructor
	 *
	 * If you pass in a $data array (using the format defined in
	 * CACAP_Widget_Instance::format_instance()), the constructor will
	 * attempt to pre-fetch the data
	 *
	 * @since 1.0
	 *
	 * @param $data array
	 */
	public function __construct( $data = null ) {
		if ( ! is_null( $data ) ) {
			// temp
			if ( ! empty( $data['type'] ) && ! isset( $data['widget_type'] ) ) {
				$data['widget_type'] = $data['type'];
			}

			if ( ! empty( $data['widget_type'] ) ) {
				$widget_types = cacap_widget_types();
				$wt = isset( $data['widget_type']->slug ) ? $data['widget_type']->slug : $data['widget_type'];
				if ( isset( $widget_types[ $wt ] ) ) {
					$this->widget_type = new $widget_types[ $wt ];
				}
			}

			if ( ! empty( $data['key'] ) ) {
				$this->key = $data['key'];
			}

			if ( ! empty( $data['user_id'] ) ) {
				$this->user_id = $data['user_id'];
				$this->value = $this->get_value();
			}

			$this->position = isset( $data['position'] ) ? intval( $data['position'] ) : 50;
			$this->css_id = $this->get_css_id();
		}
	}

	/**
	 * Fetch the content value of this widget instance
	 *
	 * Since content types are highly specific to widget types (Text
	 * widgets are an array of text content and a title; some widget
	 * content is plaintext; some may be taxonomies, etc), this method acts
	 * as a wrapper for the get_instance_for_user() method of the
	 * appropriate widget type object.
	 *
	 * If your widget content is stored as structured data, it's
	 * recommended that the value returned from get_instance_for_user() is
	 * also structured. You'll have a chance to flatten it into HTML in
	 * a separate method.
	 *
	 * @since 1.0
	 */
	public function get_value() {
		return $this->widget_type->get_instance_for_user( array(
			'user_id' => $this->user_id,
			'key' => $this->key,
		) );
	}

	/**
	 * Create a CSS ID for this item
	 *
	 * We have to have a specific format for jQuery UI sortable
	 *
	 * @since 1.0
	 */
	public function get_css_id() {
		$id = $this->key;
		$id = strtolower( $id );
		$id = sanitize_title_with_dashes( $id );

		return $id;
	}

	/**
	 * Create a new widget instance
	 *
	 * Calls save_instance_for_user() on the appropriate widget type, as
	 * different types will have different ways of saving their data.
	 *
	 * @since 1.0
	 *
	 * @param array $args
	 * @return array $widget_instance_data A structured array of metadata
	 *   about the stored widget instance. See self::format_instance()
	 */
	public function create( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'widget_type' => '',
			'title' => '',
			'content' => '',
		) );

		$types = cacap_widget_types();
		if ( isset( $types[ $r['widget_type'] ] ) ) {
			$widget_type = $types[ $r['widget_type'] ];
		} else {
			// do something bad
			return;
		}

		$widget_instance_data = $widget_type->save_instance_for_user( $r );

		return $widget_instance_data;
	}

	/**
	 * Return the value of the instance for display
	 *
	 * Some methods need access to the field value, but some widget types
	 * may store a complex object as "value" and parse it later. The value
	 * returned from this function should be a string.
	 *
	 * @return string
	 */
	public function get_display_value() {
		return $this->widget_type->get_display_value_from_value( $this->value );
	}

	/**
	 * Return the markup for displaying the public view of the title
	 *
	 * We pass $this->value to the widget type method because some widget
	 * types allow for user-configured titles, which are stored in this
	 * variable.
	 *
	 * @return string The HTML-ready title
	 */
	public function display_title() {
		return $this->widget_type->display_title_markup( $this->value );
	}

	/**
	 * Todo: run through proper filters (not just wpautop)
	 */
	public function display_content() {
		return wpautop( $this->widget_type->display_content_markup( $this->get_display_value() ) );
	}

	public function edit_title() {
		$html  = '<span class="hide-if-no-js cacap-edit-title-text cacap-hide-on-edit">' . $this->display_title() . '</span>';
		$html .= '<span class="hide-if-js cacap-edit-title-input cacap-show-on-edit">' . $this->widget_type->edit_title_markup( $this->value, $this->css_id ) . $this->ok_cancel_buttons() . '</span>';
		return $html;
	}

	public function edit_content() {
		$html  = $this->widget_type->edit_content_markup( $this->value, $this->css_id );
		$html .= '<input name="' . $this->css_id . '" class="editable-content-stash" type="hidden" value="" />';
		$html .= $this->ok_cancel_buttons();
		//$html  = '<span class="hide-if-no-js cacap-edit-content-text cacap-hide-on-edit">' . $this->display_content() . '</span>';
		//$html .= '<span class="hide-if-js cacap-edit-content-input cacap-show-on-edit">' . $this->widget_type->edit_content_markup( $this->value, $this->css_id ) . $this->ok_cancel_buttons() . '</span>';
		return $html;
	}

	public function ok_cancel_buttons() {
		return '<div class="cacap-ok-cancel">'
		     .   '<a href="#" class="button cacap-ok">' . __( 'OK', 'cacap' ) . '</a>'
		     .   '<a href="#" class="button cacap-cancel">' . __( 'Cancel', 'cacap' ) . '</a>'
		     . '</div>';
	}

	/**
	 * Formats instance metadata for storage in usermeta
	 *
	 * This central static method makes it possible to flatten metadata
	 * from all types of widgets into a standardized format, so they can be
	 * more easily stored and queried later on.
	 *
	 * The array returned from the function looks like:
	 *   'user_id' => 4,
	 *   'key' => 'foo',
	 *   'widget_type' => 'text'
	 *
	 * - 'key' is a unique string. The format of the string is arbitrary;
	 *   it's up to the widget type to decide what kind of key it's helpful
	 *   to store. For instance, widgets that store data in usermeta will
	 *   probably store the meta_key value here
	 * - 'widget_type' should match a key in the array returned by
	 *   cacap_widget_types()
	 *
	 * @since 1.0
	 *
	 * @param array $args See the inline definition of defaults
	 * @return array $retval
	 */
	public static function format_instance( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'user_id' => 0,
			'key' => '',
			'widget_type' => '',
			'position' => 50,
		) );

		$retval = array(
			'user_id' => $r['user_id'],
			'key' => $r['key'],
			'widget_type' => $r['widget_type'],
			'position' => $r['position'],
		);

		return $retval;
	}
}
