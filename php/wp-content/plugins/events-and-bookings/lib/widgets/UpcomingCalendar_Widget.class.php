<?php

class Eab_CalendarUpcoming_Widget extends Eab_Widget {

	function __construct () {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Displays List of Upcoming Events from your entire network', $this->translation_domain));

		add_action('wp_enqueue_scripts', array($this, 'css_load_styles'));
		add_action('wp_enqueue_scripts', array($this, 'js_load_scripts'));
		add_action('wp_ajax_eab_cuw_get_calendar', array($this, 'handle_calendar_request'));
		add_action('wp_ajax_nopriv_eab_cuw_get_calendar', array($this, 'handle_calendar_request'));

		parent::WP_Widget(__CLASS__, __('Calendar Upcoming', $this->translation_domain), $widget_ops);
	}

	function css_load_styles () {
		if (!is_admin()) wp_enqueue_style('eab-upcoming_calendar_widget-style', plugins_url('events-and-bookings/css/upcoming_calendar_widget.css'));
	}

	function js_load_scripts () {
		if (!is_admin()) wp_enqueue_script('eab-upcoming_calendar_widget-script', plugins_url('events-and-bookings/js/upcoming_calendar_widget.js'), array('jquery'));
	}

	function form ($instance) {
		$title = esc_attr($instance['title']);
		$date = esc_attr($instance['date']);
		$network = esc_attr($instance['network']) ? 'checked="checked"' : '';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title:', $this->translation_domain) . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" class="widefat" value="' . $title . '"/>';
		$html .= '</p>';

		if (is_multisite() && eab_has_post_indexer()) {
			$html .= '<p>' .
				'<label for="' . $this->get_field_id('network') . '">' .
				'<input type="checkbox" name="' . $this->get_field_name('network') . '" id="' . $this->get_field_id('network') . '" value="1" ' . $network . ' /> ' .
				__('Network-wide?', $this->translation_domain) .
				'</label> ' .
			'</p>';
		}

		echo $html;
	}

	function update ($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['date'] = strip_tags($new_instance['date']);
		$instance['network'] = strip_tags($new_instance['network']);

		return $instance;
	}

	function widget ($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$network = is_multisite() ? (int)$instance['network'] : false;

		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;
		echo $this->_render_calendar(eab_current_time(), $network);
		echo $after_widget;
	}

	private function _render_calendar ($date, $network=false) {
		$events = $network
			? Eab_Network::get_upcoming_events(10)
			: $events = Eab_CollectionFactory::get_upcoming_events($date)
		;
		if (!class_exists('Eab_CalendarTable_UpcomingCalendarWidget')) require_once EAB_PLUGIN_DIR . 'lib/class_eab_calendar_helper.php';
		$renderer = new Eab_CalendarTable_UpcomingCalendarWidget($events);
		return $renderer->get_month_calendar($date);
	}

	function handle_calendar_request () {
		$now = (int)@$_POST['now'];
		$now = $now ? $now : eab_current_time();

		$unit = ("year" == @$_POST['unit']) ? "year" : "month";
		$operand = ("prev" == $_POST['direction']) ? "+1" : "-1";

		$date = strtotime("{$operand} {$unit}", $now);
		echo $this->_render_calendar($date);
		die;
	}
}