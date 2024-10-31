<?php
/*
Plugin Name: PageCrumbs
Plugin URI:  https://github.com/anywherecreative/pagecrumbs
Description: a simple way to display breadcrumbs on your website. 
Version:     1.1.0
Author:      Jeff Manning / Think Forward Media
Author URI:  https://www.thinkforwardmedia.com
Text Domain: tfm
License:     GPL3
 
PageCrumbs is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.
 
PageCrumbs is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with PageCrumbs. If not, see https://github.com/anywherecreative/pagecrumbs/blob/master/LICENSE.
*/

function get_post_breadcrumb() {
        global $post;
        $breadcrumb = "<a href='" . get_home_url() . "'>" . __( 'Home', 'tfm') . "</a> / ";
        $posts = get_post_ancestors($post->ID);
        if(is_front_page()) {
                return "<strong>Home</strong>";
        }

        if(is_single()) {
                $cat = get_post_primary_category($post->ID);
                $cat = $cat['primary_category'];
                $breadcrumb .= "<a href='" . get_category_link($cat) . "'>" . $cat->name . "</a> / <strong>" . get_the_title($post) . "</strong>";
                return $breadcrumb;
        }

        if(!empty($posts)) {
                foreach($posts as $p) {
                        $breadcrumb .= "<a href='" . get_permalink($p) . "'>" . get_the_title($p) . "</a> / ";
                }
                $breadcrumb .= "<strong>" . get_the_title() . "</strong>";
                return $breadcrumb;
        }
        else {
                return $breadcrumb . "<strong>" . get_the_title() . "</strong>";
        }
}

function get_post_primary_category($post_id, $term='category', $return_all_categories=false){
        $return = array();

        if (class_exists('WPSEO_Primary_Term')){
            // Show Primary category by Yoast if it is enabled & set
            $wpseo_primary_term = new WPSEO_Primary_Term( $term, $post_id );
            $primary_term = get_term($wpseo_primary_term->get_primary_term());

            if (!is_wp_error($primary_term)){
                $return['primary_category'] = $primary_term;
            }
        }

        if (empty($return['primary_category']) || $return_all_categories){
            $categories_list = get_the_terms($post_id, $term);

            if (empty($return['primary_category']) && !empty($categories_list)){
                $return['primary_category'] = $categories_list[0];  //get the first category
            }
            if ($return_all_categories){
                $return['all_categories'] = array();

                if (!empty($categories_list)){
                    foreach($categories_list as &$category){
                        $return['all_categories'][] = $category->term_id;
                    }
                }
            }
        }

        return $return;
    }   

add_shortcode('page_breadcrumb','get_post_breadcrumb');