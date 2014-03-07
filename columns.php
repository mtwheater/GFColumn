<?php

/*
Plugin Name: FFG - Column Addon
Plugin URI: http://www.splicertechnology.com
Description: Adds CSS to Columnize Gravity Forms
Version: 0.1
Author: Splicer Technology
Author URI: http://www.splicertechnology.com

------------------------------------------------------------------------
Copyright 2014 splicer technology

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

add_action('init',  array('GFColumns', 'init'));

class GFColumns {

    public static function init() {

        add_filter('gform_field_content', array('GFTrello', 'gform_column_splits'), 10, 5);

        self::frontend_css();

    }

    public function gform_column_splits($content, $field, $value, $lead_id, $form_id) {
        if(!IS_ADMIN) { // only perform on the front end

            // target section breaks
            if($field['type'] == 'section') {
                $form = RGFormsModel::get_form_meta($form_id, true);

                // check for the presence of our special multi-column form class
                $form_class = explode(' ', $form['cssClass']);
                $form_class_matches = array_intersect($form_class, array('two-column', 'three-column', 'four-column'));

                // check for the presence of our special section break column class
                $field_class = explode(' ', $field['cssClass']);
                $field_class_matches = array_intersect($field_class, array('gform_column'));

                // if we have a column break field in a multi-column form, perform the list split
                if(!empty($form_class_matches) && !empty($field_class_matches)) {

                    // we'll need to retrieve the form's properties for consistency
                    $form = RGFormsModel::add_default_properties($form);
                    $description_class = rgar($form, 'descriptionPlacement') == 'above' ? 'description_above' : 'description_below';
                    // close current field's li and ul and begin a new list with the same form properties
                    return '</li></ul><ul class="gform_fields '.$form['labelPlacement'].' '.$description_class.' '.$field['cssClass'].'"><li class="gfield gsection">';

                }
            }
        }

        return $content;
    }

    /**
     * Add CSS in the frontend area
     *
     */
	public static function frontend_css()
	{
		wp_enqueue_style('gf-column-css', plugins_url( 'gf-column.css' , __FILE__ ));
	}
