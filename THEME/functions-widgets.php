<?php
class _V_WIDGETNAME extends WP_Widget {
	function __construct ( )
	{
		$widget_ops = array( 'classname' => '_V_WIDGETNAME', 'description' => __( '.', '_V') );  
        $this->WP_Widget( 'widget_id', __('WIDGETNAME', '_V'), $widget_ops /*, $control_ops*/ );
	}
	function widget ( $ars, $instance )
	{

	}
	function form ( $instance )
	{

	}
	function update ( $new_instance, $old_instance )
	{
		
	}
}

add_action ('widgets_init', 'register_all_widgets');
function register_all_widgets ( ) { 
	register_widget ( '_V_WIDGETNAME' ); 
}
?>