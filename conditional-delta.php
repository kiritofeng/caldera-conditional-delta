<?php

/**
 * Plugin Name: Caldera Forms - Conditional Delta
 * Description: Extension of Caldera Form's default Increment Processor
 * Version: 1.0.0
 * Author: Roger Fu, TOPS '19
 * Author URI: https://kiritofeng.tk
 */

add_filter('caldera_forms_get_form_processors', 'register_processor');

/**
 * Adds a custom processor for conditional deltas
 *
 * @uses 'cf_custom_cond_delta'
 *
 * @param array processors Processor configs
 *
 * @return array
 */
function register_processor($processors) {
    $processors['cf_custom_cond_delta'] = array(
        'name' => 'Custom Conditional Delta',
        'description' => 'Conditionally increments a variable',
        'processor' => 'cf_custom_cond_delta',
        'template' => dirname(__FILE__) . '/config.php',
        'magic_tags' => array(
            'current_value',
        ),
    );

    return $processors;
}

/**
 * Run required code
 * 
 * @param array $config Processor config
 * @param array $form Form config
 *
 * @return void|array
 */
function cf_custom_cond_delta($processors, $config, $process_id) {
    // get the values
    $current_value = Caldera_Forms::do_magic_tags($config['start_value']);
    $delta = Caldera_Forms::do_magic_tags($config['delta']);

    // get the accepted min and max
    $min_val = (isset($config['min_val'])) ? $config['min_val'] : -INF;
    $max_val = (isset($config['max_val'])) ? $config['max_val'] : INF;

    if($min_val > $max_val) {
        // what are you doing...
        return array(
            'note' => 'Please ensure `min_val` is *less than or equal to* `max_val`!',
            'type' => 'error'
        );
    }

    $current_value = min(max($current_value + $delta, $min_val), $max_val);

    $config['start_value'] = $current_value;
}

/**
 * Setup fields to pass to Caldera_Forms_Processor_UI::config_fields in config
 *
 * @return array
 */
function cf_custom_cond_delta_fields() {
    return array(
        array(
            'id' => 'start_value',
            'label' => 'Initial Value',
            'type' => 'number',
            'required' => true,
            'magic' => true,
            'desc' => 'The value to start the counter at.'
        ),
        array(
            'id' => 'delta',
            'label' => 'Delta',
            'type' => 'number',
            'required' => true,
            'magic' => true,
            'desc' => 'The value to change the counter by.'
        ),
        array(
            'id' => 'min_val',
            'label' => 'Minimum Value',
            'type' => 'number',
            'required' => false,
            'magic' => true,
            'desc' => 'The minimum value the counter can obtain.'
        ),
        array(
            'id' => 'max_val',
            'label' => 'Maximum Value',
            'type' => 'number',
            'required' => false,
            'magic' => true,
            'desc' => 'The maximum value the counter can obtain.'
        ),
        array(
            'id' => 'field',
            'label' => 'Increment Field',
            'type' => 'advanced',
            'required' => false,
            'magic' => true,
            'allow_types' => array('hidden'),
            'exclude' => array('system', 'variables'),
            'desc' => 'If you want to show the incremented value in the entries, select a hidden field in form to capture the value in.'
        )
    );
}
