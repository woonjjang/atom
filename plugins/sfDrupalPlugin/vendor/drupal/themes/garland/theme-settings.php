<?php
// $Id: theme-settings.php 5047 2010-01-27 01:19:02Z jablko $

/**
 * Implements hook_form_system_theme_settings_alter().
 *
 * @param $form
 *   The form.
 * @param $form_state
 *   The form state.
 */
function garland_form_system_theme_settings_alter(&$form, $form_state) {
  $form['garland_width'] = array(
    '#type' => 'radios',
    '#title' => t('Content width'),
    '#options' => array(
      'fluid' => t('Fluid width'),
      'fixed' => t('Fixed width'),
    ),
    '#default_value' => theme_get_setting('garland_width'),
    '#description' => t('Specify whether the content will wrap to a fixed width or will fluidly expand to the width of the browser window.'),
    // Place this above the color scheme options.
    '#weight' => -2,
  );
}
