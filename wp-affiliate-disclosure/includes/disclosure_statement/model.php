<?php

/**
 * Disclosure Statement Model Class
 *
 * @author 		MojofyWP
 * @package 	includes/disclosure_statement
 * 
 */
if ( !class_exists( 'WPADC_Disclosure_Statement_Model' ) ) {
    class WPADC_Disclosure_Statement_Model {
        /**
         * instance
         *
         * @access private
         * @var array
         */
        private $instance = null;

        /**
         * Hook prefix
         *
         * @access private
         * @var string
         */
        private $_hook_prefix = null;

        /**
         * Get instance
         *
         * @access public
         * @return array
         */
        public function get_instance() {
            return $this->instance;
        }

        /**
         * Class Constructor
         *
         * @access private
         */
        function __construct( $instance = array() ) {
            // setup variables
            $this->instance = $instance;
            $this->_hook_prefix = wpadc()->plugin_hook() . 'disclosure_statement/model/';
        }

        /**
         * Get disclosure rules
         *
         * @access public
         * @return string
         */
        public function get_disclosure_rules( $explicit_rule_id = 0 ) {
            global $wp_affiliate_disclosure_fs;
            $rules = array();
            // Explicit rule lookup (shortcode rule="X") — verifies post_type === 'wpadc'.
            if ( $explicit_rule_id > 0 ) {
                $post_obj = get_post( $explicit_rule_id );
                if ( $post_obj && 'wpadc' === $post_obj->post_type && 'publish' === $post_obj->post_status ) {
                    $priority = wpadcb_get_meta( array(
                        'id'      => $explicit_rule_id,
                        'key'     => 'priority',
                        'default' => 1,
                    ) );
                    $rules[$explicit_rule_id] = array(
                        'id'                      => $explicit_rule_id,
                        'disclosure_statement'    => wpadcb_get_meta( array(
                            'id'  => $explicit_rule_id,
                            'key' => 'disclosure_statement',
                        ) ),
                        'statement_position'      => wpadcb_get_meta( array(
                            'id'      => $explicit_rule_id,
                            'key'     => 'statement_position',
                            'default' => 'before-after-content',
                        ) ),
                        'priority'                => intval( $priority ),
                        'post_type'               => wpadcb_get_meta( array(
                            'id'      => $explicit_rule_id,
                            'key'     => 'post_type',
                            'default' => 'post',
                        ) ),
                        'condition'               => wpadcb_get_meta( array(
                            'id'      => $explicit_rule_id,
                            'key'     => 'condition',
                            'default' => 'none',
                        ) ),
                        'ids'                     => wpadcb_get_meta( array(
                            'id'      => $explicit_rule_id,
                            'key'     => 'ids',
                            'default' => '',
                        ) ),
                        'taxonomies'              => wpadcb_get_meta( array(
                            'id'      => $explicit_rule_id,
                            'key'     => 'taxonomies',
                            'default' => '',
                        ) ),
                        'advanced_filter'         => wpadcb_get_meta( array(
                            'id'      => $explicit_rule_id,
                            'key'     => 'advanced_filter',
                            'default' => '',
                        ) ),
                        'exclude_taxonomies'      => wpadcb_get_meta( array(
                            'id'      => $explicit_rule_id,
                            'key'     => 'exclude_taxonomies',
                            'default' => '',
                        ) ),
                        'excludes_ids'            => wpadcb_get_meta( array(
                            'id'      => $explicit_rule_id,
                            'key'     => 'excludes_ids',
                            'default' => '',
                        ) ),
                        'customize_appearance'    => wpadcb_get_meta( array(
                            'id'      => $explicit_rule_id,
                            'key'     => 'customize_appearance',
                            'default' => 0,
                        ) ),
                        'style_preset'            => wpadcb_get_meta( array(
                            'id'      => $explicit_rule_id,
                            'key'     => 'style_preset',
                            'default' => '',
                        ) ),
                        'content_parent_selector' => wpadcb_get_meta( array(
                            'id'      => $explicit_rule_id,
                            'key'     => 'content_parent_selector',
                            'default' => '.entry-content',
                        ) ),
                    );
                }
                return apply_filters( $this->_hook_prefix . 'get_disclosure_rules', $rules, $this );
            }
            $query = new WP_Query(array(
                'post_type'      => 'wpadc',
                'post_status'    => 'publish',
                'paged'          => 1,
                'posts_per_page' => 1,
                'meta_key'       => wpadc()->plugin_meta_prefix() . 'priority',
                'orderby'        => wpadc()->plugin_meta_prefix() . 'priority',
                'order'          => 'ASC',
            ));
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $r_id = get_the_ID();
                    $priority = wpadcb_get_meta( array(
                        'id'      => $r_id,
                        'key'     => 'priority',
                        'default' => 1,
                    ) );
                    $rules[$r_id] = array(
                        'id'                      => $r_id,
                        'disclosure_statement'    => wpadcb_get_meta( array(
                            'id'  => $r_id,
                            'key' => 'disclosure_statement',
                        ) ),
                        'statement_position'      => wpadcb_get_meta( array(
                            'id'      => $r_id,
                            'key'     => 'statement_position',
                            'default' => 'before-after-content',
                        ) ),
                        'priority'                => intval( $priority ),
                        'post_type'               => wpadcb_get_meta( array(
                            'id'      => $r_id,
                            'key'     => 'post_type',
                            'default' => 'post',
                        ) ),
                        'condition'               => wpadcb_get_meta( array(
                            'id'      => $r_id,
                            'key'     => 'condition',
                            'default' => 'none',
                        ) ),
                        'ids'                     => wpadcb_get_meta( array(
                            'id'      => $r_id,
                            'key'     => 'ids',
                            'default' => '',
                        ) ),
                        'taxonomies'              => wpadcb_get_meta( array(
                            'id'      => $r_id,
                            'key'     => 'taxonomies',
                            'default' => '',
                        ) ),
                        'advanced_filter'         => wpadcb_get_meta( array(
                            'id'      => $r_id,
                            'key'     => 'advanced_filter',
                            'default' => '',
                        ) ),
                        'exclude_taxonomies'      => wpadcb_get_meta( array(
                            'id'      => $r_id,
                            'key'     => 'exclude_taxonomies',
                            'default' => '',
                        ) ),
                        'excludes_ids'            => wpadcb_get_meta( array(
                            'id'      => $r_id,
                            'key'     => 'excludes_ids',
                            'default' => '',
                        ) ),
                        'customize_appearance'    => wpadcb_get_meta( array(
                            'id'      => $r_id,
                            'key'     => 'customize_appearance',
                            'default' => 0,
                        ) ),
                        'style_preset'            => wpadcb_get_meta( array(
                            'id'      => $r_id,
                            'key'     => 'style_preset',
                            'default' => '',
                        ) ),
                        'content_parent_selector' => wpadcb_get_meta( array(
                            'id'      => $r_id,
                            'key'     => 'content_parent_selector',
                            'default' => '.entry-content',
                        ) ),
                    );
                }
            }
            wp_reset_postdata();
            // re-arrange rules based on priority
            if ( !empty( $rules ) ) {
                usort( $rules, array(&$this, 'sort_by_priority') );
            }
            return apply_filters( $this->_hook_prefix . 'get_disclosure_rules', $rules, $this );
        }

        /**
         * sort by priority
         *
         * @access public
         * @return string
         */
        public function sort_by_priority( $a, $b ) {
            if ( $a['priority'] == $b['priority'] ) {
                return 0;
            }
            return ( $a['priority'] < $b['priority'] ? -1 : 1 );
        }

        /**
         * sample function
         *
         * @access public
         * @return string
         */
        public function sample_func() {
            $output = '';
            return apply_filters( $this->_hook_prefix . 'sample_func', $output, $this );
        }

        /* END
        	------------------------------------------------------------------- */
    }

    // end - class WPADC_Disclosure_Statement_Model
}
// end - !class_exists('WPADC_Disclosure_Statement_Model')