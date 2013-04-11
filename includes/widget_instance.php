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
	protected $user_id;
	protected $type;
	protected $key;
	protected $value;

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
			if ( ! empty( $data['type'] ) && ! empty( $data['key'] ) && ! empty( $data['user_id'] ) ) {

				$widget_types = cacap_widget_types();
				if ( isset( $widget_types[ $data['type'] ] ) ) {
					$this->type = new $widget_types[ $data['type'] ];
				}

				$this->key = $data['key'];
				$this->user_id = $data['user_id'];

				$this->value = $this->get_value();
			}
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
		return $this->type->get_instance_for_user( array(
			'user_id' => $this->user_id,
			'key' => $this->key,
		) );
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
			'type' => '',
			'title' => '',
			'content' => '',
		) );

		$types = cacap_widget_types();
		if ( isset( $types[ $r['type'] ] ) ) {
			$widget_type = $types[ $r['type'] ];
		} else {
			// do something bad
			return;
		}

		$widget_instance_data = $widget_type->save_instance_for_user( $r );

		return $widget_instance_data;
	}

	public function display_title() {
		return $this->type->display_title_markup( $this->value );
	}

	public function display_content() {
		return $this->type->display_content_markup( $this->value );
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
	 *   'type' => 'text'
	 *
	 * - 'key' is a unique string. The format of the string is arbitrary;
	 *   it's up to the widget type to decide what kind of key it's helpful
	 *   to store. For instance, widgets that store data in usermeta will
	 *   probably store the meta_key value here
	 * - 'type' should match a key in the array returned by
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
			'type' => '',
		) );

		$retval = array(
			'user_id' => $r['user_id'],
			'key' => $r['key'],
			'type' => $r['type'],
		);

		return $retval;
	}
}
