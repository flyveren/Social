<?php

class Eab_Shortcodes extends Eab_Codec {

	protected $_shortcodes = array (
		'calendar' => 'eab_calendar',
		'archive' => 'eab_archive',
		'single' => 'eab_single',
		'expired' => 'eab_expired',
		'events_map' => 'eab_events_map',
	);

	public static function serve () {
		$me = new Eab_Shortcodes;
		$me->_register();
	}

	/**
	 * Events map shortcode
	 * @param  array   $args    Shortcode arguments
	 * @param  boolean $content Fallback content
	 * @return string           Map string or fallback content
	 */
	function process_events_map_shortcode ($args=array(), $content=false) {
		if (!class_exists('AgmMapModel') || !class_exists('AgmMarkerReplacer')) return $content;

		$map_args = $args;
		$args = $this->_preparse_arguments($args, array(
		// Date arguments
			'date' => false, // Starting date - default to now
			'lookahead' => false, // Don't use default monthly page - use weeks count instead
			'weeks' => false, // Look ahead this many weeks
		// Query arguments
			'category' => false, // ID or slug
			'categories' => false, // Comma-separated list of IDs
			'limit' => false, // Show at most this many events
			'order' => false,
		// Appearance arguments
			'featured_image' => false,
			'class' => false,
			'template' => 'get_shortcode_events_map_marker_body_output', // Always a template class call
		));
		$args['featured_image'] = $this->_arg_to_bool($args['featured_image']);
		$class = $args['class'] ? 'class="' . $args['class'] . '"' : '';

		$query = $this->_to_query_args($args);

		$order_method = $args['order']
			? create_function('', 'return "' . $args['order'] . '";')
			: false
		;
		if ($order_method) add_filter('eab-collection-date_ordering_direction', $order_method);

		// Lookahead - depending on presence, use regular upcoming query, or poll week count
		if ($args['lookahead']) {
			$method = $args['weeks']
				? create_function('', 'return ' . $args['weeks'] . ';')
				: false;
			;
			if ($method) add_filter('eab-collection-upcoming_weeks-week_number', $method);
			$events = Eab_CollectionFactory::get_upcoming_weeks($args['date'], $query);
			if ($method) remove_filter('eab-collection-upcoming_weeks-week_number', $method);
		} else {
			// No lookahead, get the full month only
			$events = Eab_CollectionFactory::get_upcoming($args['date'], $query);
		}
		if ($order_method) remove_filter('eab-collection-date_ordering_direction', $order_method);

		$model = new AgmMapModel;
		$raw_maps = $model->get_custom_maps($events->query);
		if (empty($raw_maps)) return $content;

		$maps = array();
		foreach ($raw_maps as $key => $map) {
			if (empty($map['markers']) || count($map['markers']) > 1) continue;
			$event = !empty($map['post_ids']) && !empty($map['post_ids'][0])
				? new Eab_EventModel(get_post($map['post_ids'][0]))
				: false
			;
			if (!$event) continue;

			$map['markers'][0]['title'] = $event->get_title();
			$map['markers'][0]['body'] = Eab_Template::util_apply_shortcode_template($event, $args);//eab_call_template('util_apply_shortcode_template', $event, $args);
			if ($args['featured_image']) {
				$icon = $event->get_featured_image_url();
				if ($icon) $map['markers'][0]['icon'] = $icon;
			}

			$maps[] = $map;
		}
		if (!$maps) return $content;

		$codec = new AgmMarkerReplacer;
		return "<div {$class}>" . $codec->create_overlay_tag($maps, $map_args) . '</div>';
	}

	function add_events_map_shortcode_help ($help) {
		$help[] = array(
			'title' => __('Event map shortcode', Eab_EventsHub::TEXT_DOMAIN),
			'tag' => 'eab_events_map',
			'note' => __('Requires Google Maps plugin.', Eab_EventsHub::TEXT_DOMAIN),
			'arguments' => array(
				'date' => array('help' => __('Starting date - default to now', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:date'),
				'lookahead' => array('help' => __('Don\'t use default monthly page - use weeks count instead', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
				'weeks' => array('help' => __('Look ahead this many weeks', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'integer'),
				'category' => array('help' => __('Show events from this category (ID or slug)', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:or_integer'),
				'categories' => array('help' => __('Show events from these categories - accepts comma-separated list of IDs', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:id_list'),
				'limit' => array('help' => __('Show at most this many events', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'integer'),
				'order' => array('help' => __('Sort events in this direction', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:sort'),
				'featured_image' => array('help' => __('Use event featured image instead of map markers', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
				'template' => array('help' => __('Subtemplate file, or template class call', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string'),
				'...' => array('help' => __('and Google Maps shortcode attributes', Eab_EventsHub::TEXT_DOMAIN)),
			),
			'advanced_arguments' => array('template'),
		);
		return $help;
	}

	/**
	 * Calendar shortcode handler.
	 */
	function process_calendar_shortcode ($args=array(), $content=false) {
		$args = $this->_preparse_arguments($args, array(
			'network' => false,
			'date' => false,
			'footer' => false,
			'class' => 'eab-shortcode_calendar',
			'navigation' => false,
			'title_format' => 'M Y',
			'template' => 'get_shortcode_calendar_output', // Subtemplate file, or template class call
			'override_styles' => false,
			'override_scripts' => false,
		));

		if (!empty($_GET['date'])) {
			$date = strtotime($_GET['date']);
			if ($date) $args['date'] = $date;
		}

		$events = ($args['network'] && is_multisite())
			? Eab_Network::get_upcoming_events(30)
			: Eab_CollectionFactory::get_upcoming_events($args['date'])
		;

		$output = Eab_Template::util_apply_shortcode_template($events, $args);
		$output = $output ? $output : $content;

		if (!$args['override_styles']) wp_enqueue_style('eab_calendar_shortcode', eab_call_template('util_get_default_template_style', 'calendar'));
		return $output;
	}

	function add_calendar_shortcode_help ($help) {
		$help[] = array(
			'title' => __('Calendar shortcode', Eab_EventsHub::TEXT_DOMAIN),
			'tag' => 'eab_calendar',
			'arguments' => array(
				'network' => array('help' => __('Query type', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
				'date' => array('help' => __('Starting date - default to now', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:date'),
				'navigation' => array('help' => __('Show navigation', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
				'title_format' => array('help' => __('Date format used in the navigation title, defaults to "M Y"', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:date_format'),
				'footer' => array('help' => __('Show calendar table footer', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
				'class' => array('help' => __('Apply this CSS class', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string'),
				'template' => array('help' => __('Subtemplate file, or template class call', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string'),
				'override_styles' => array('help' => __('Toggle default styles usage', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
				'override_scripts' => array('help' => __('Toggle default scripts usage', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
			),
			'advanced_arguments' => array('template', 'override_scripts', 'override_styles'),
		);
		return $help;
	}

	/**
	 * Archive shortcode handler.
	 */
	function process_archive_shortcode ($args=array(), $content=false) {
		$args = $this->_preparse_arguments($args, array(
			'network' => false, // Query type
		// Date arguments
			'date' => false, // Starting date - default to now
			'lookahead' => false, // Don't use default monthly page - use weeks count instead
			'weeks' => false, // Look ahead this many weeks
		// Query arguments
			'category' => false, // ID or slug
			'categories' => false, // Comma-separated list of category IDs
			'limit' => false, // Show at most this many events
			'order' => false,
		// Paging arguments
			'paged' => false,
			'page' => 1,
		// Appearance arguments
			'class' => false,
			'template' => 'get_shortcode_archive_output', // Subtemplate file, or template class call
			'override_styles' => false,
			'override_scripts' => false,
		));

		if ($args['paged']) {
			$requested_page = get_query_var('page');
			$args['page'] = $requested_page ? $requested_page : $args['page'];
		}

		$events = array();
		if (is_multisite() && $args['network']) {
			$events = Eab_Network::get_upcoming_events(30);
		} else {
			$query = $this->_to_query_args($args);

			$order_method = $args['order']
				? create_function('', 'return "' . $args['order'] . '";')
				: false
			;
			if ($order_method) add_filter('eab-collection-date_ordering_direction', $order_method);

			// Lookahead - depending on presence, use regular upcoming query, or poll week count
			if ($args['lookahead']) {
				$method = $args['weeks']
					? create_function('', 'return ' . $args['weeks'] . ';')
					: false;
				;
				if ($method) add_filter('eab-collection-upcoming_weeks-week_number', $method);
				$events = Eab_CollectionFactory::get_upcoming_weeks_events($args['date'], $query);
				if ($method) remove_filter('eab-collection-upcoming_weeks-week_number', $method);
			} else {
				// No lookahead, get the full month only
				$events = Eab_CollectionFactory::get_upcoming_events($args['date'], $query);
			}
			if ($order_method) remove_filter('eab-collection-date_ordering_direction', $order_method);
		}

		$output = Eab_Template::util_apply_shortcode_template($events, $args);
		if ($output) {
			if ($args['paged']) {
				if ($method) add_filter('eab-collection-upcoming_weeks-week_number', $method);
				$events_query = $args['lookahead']
					? Eab_CollectionFactory::get_upcoming_weeks($args['date'], $query)
					: Eab_CollectionFactory::get_upcoming($args['date'], $query)
				;
				if ($method) remove_filter('eab-collection-upcoming_weeks-week_number', $method);
				$output .= eab_call_template('get_shortcode_paging', $events_query, $args);
			}
		} else $output = $content;

		if (!$args['override_styles']) wp_enqueue_style('eab_front');
		if (!$args['override_scripts']) wp_enqueue_script('eab_event_js');
		return $output;
	}

	function add_archive_shortcode_help ($help) {
		$help[] = array(
			'title' => __('Archive shortcode', Eab_EventsHub::TEXT_DOMAIN),
			'tag' => 'eab_archive',
			'arguments' => array(
				'network' => array('help' => __('Query type', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
				'date' => array('help' => __('Starting date - default to now', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:date'),
				'lookahead' => array('help' => __('Don\'t use default monthly page - use weeks count instead', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
				'weeks' => array('help' => __('Look ahead this many weeks', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'integer'),
				'category' => array('help' => __('Show events from this category (ID or slug)', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:or_integer'),
				'categories' => array('help' => __('Show events from these categories - accepts comma-separated list of IDs', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:id_list'),
				'limit' => array('help' => __('Show at most this many events', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'integer'),
				'order' => array('help' => __('Sort events in this direction', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:sort'),
				'paged' => array('help' => __('Allow paging - use with "limit" argument', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
				'page' => array('help' => __('Start on this page', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'integer'),
				'class' => array('help' => __('Apply this CSS class', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string'),
				'template' => array('help' => __('Subtemplate file, or template class call', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string'),
				'override_styles' => array('help' => __('Toggle default styles usage', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
				'override_scripts' => array('help' => __('Toggle default scripts usage', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
			),
			'advanced_arguments' => array('template', 'override_scripts', 'override_styles'),
		);
		return $help;
	}

	/**
	 * Expired shortcode handler.
	 */
	function process_expired_shortcode ($args=array(), $content=false) {
		$args = $this->_preparse_arguments($args, array(
		// Appearance arguments
			'class' => false,
		// Query arguments
			'category' => false, // ID or slug
			'categories' => false, // Comma-separated list of category IDs
			'limit' => false, // Show at most this many events
			'order' => false,
		// Template options
			'template' => 'get_shortcode_archive_output', // Subtemplate file, or template class call
			'override_styles' => false,
			'override_scripts' => false,
		));

		$query = $this->_to_query_args($args);
		$events = Eab_CollectionFactory::get_expired_events($query);

		$output = Eab_Template::util_apply_shortcode_template($events, $args);//eab_call_template('util_apply_shortcode_template', $events, $args);
		$output = $output ? $output : $content;

		if (!$args['override_styles']) wp_enqueue_style('eab_front');
		if (!$args['override_scripts']) wp_enqueue_script('eab_event_js');
		return $output;
	}

	function add_expired_shortcode_help ($help) {
		$help[] = array(
			'title' => __('Expired events shortcode', Eab_EventsHub::TEXT_DOMAIN),
			'tag' => 'eab_expired',
			'arguments' => array(
				'class' => array('help' => __('Apply this CSS class', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string'),
				'category' => array('help' => __('Show events from this category (ID or slug)', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:or_integer'),
				'categories' => array('help' => __('Show events from these categories - accepts comma-separated list of IDs', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:id_list'),
				'limit' => array('help' => __('Show at most this many events', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'integer'),
				'order' => array('help' => __('Sort events in this direction', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string:sort'),
				'template' => array('help' => __('Subtemplate file, or template class call', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string'),
				'override_styles' => array('help' => __('Toggle default styles usage', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
				'override_scripts' => array('help' => __('Toggle default scripts usage', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
			),
			'advanced_arguments' => array('template', 'override_scripts', 'override_styles'),
		);
		return $help;
	}

	/**
	 * Single event shortcode handler.
	 */
	function process_single_shortcode ($args=array(), $content=false) {
		$args = $this->_preparse_arguments($args, array(
			'id' => false,
			'slug' => false,
		// Appearance arguments
			'class' => false,
			'template' => 'get_shortcode_single_output', // Subtemplate file, or template class call
			'override_styles' => false,
			'override_scripts' => false,
		));
		$args['id'] = $this->_arg_to_int($args['id']);
		$event = false;

		if ($args['id']) $event = new Eab_EventModel(get_post($args['id']));
		else {
			$q = new WP_Query(array(
				'post_type' => Eab_EventModel::POST_TYPE,
				'name' => $args['slug'],
				'posts_per_page' => 1,
			));
			if (isset($q->posts[0])) $event = new Eab_EventModel($q->posts[0]);
		}
		if (!$event) return $content;

		$output = Eab_Template::util_apply_shortcode_template($event, $args);//eab_call_template('util_apply_shortcode_template', $event, $args);
		$output = $output ? $output : $content;

		if (!$args['override_styles']) wp_enqueue_style('eab_front');
		if (!$args['override_scripts']) wp_enqueue_script('eab_event_js');
		return $output;
	}

	function add_single_shortcode_help ($help) {
		$help[] = array(
			'title' => __('Single event shortcode', Eab_EventsHub::TEXT_DOMAIN),
			'tag' => 'eab_single',
			'arguments' => array(
				'id' => array('help' => __('Event ID to show', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'integer'),
				'slug' => array('help' => __('Show event by this slug', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string'),
				'class' => array('help' => __('Apply this CSS class', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string'),
				'template' => array('help' => __('Subtemplate file, or template class call', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'string'),
				'override_styles' => array('help' => __('Toggle default styles usage', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
				'override_scripts' => array('help' => __('Toggle default scripts usage', Eab_EventsHub::TEXT_DOMAIN), 'type' => 'boolean'),
			),
			'advanced_arguments' => array('template', 'override_scripts', 'override_styles'),
		);
		return $help;
	}
}