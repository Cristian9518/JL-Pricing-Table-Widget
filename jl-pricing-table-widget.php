<?php
/**
 * Plugin Name: JL Pricing Table Widget
 * Description: Widget personalizado de Elementor para tablas de precios.
 * Version: 1.3.1
 * Author: JL
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Constantes base para centralizar rutas y facilitar una futura modularización.
define( 'JL_PRICING_TABLE_WIDGET_VERSION', '1.3.1' );
define( 'JL_PRICING_TABLE_WIDGET_FILE', __FILE__ );
define( 'JL_PRICING_TABLE_WIDGET_URL', plugin_dir_url( JL_PRICING_TABLE_WIDGET_FILE ) );

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;

/**
 * Registra la hoja de estilos principal del widget.
 *
 * Elementor se encarga de encolarla cuando el widget declara la dependencia
 * mediante get_style_depends(), y el render la refuerza para compatibilidad.
 */
function jl_pricing_table_register_styles() {
    wp_register_style(
        'jl-pricing-table',
        JL_PRICING_TABLE_WIDGET_URL . 'assets/css/jl-pricing-table.css',
        [],
        JL_PRICING_TABLE_WIDGET_VERSION
    );
}
add_action( 'wp_enqueue_scripts', 'jl_pricing_table_register_styles' );
add_action( 'elementor/editor/after_enqueue_styles', 'jl_pricing_table_register_styles' );

add_action( 'elementor/widgets/register', function( $widgets_manager ) {

    if ( ! did_action( 'elementor/loaded' ) ) {
        return;
    }

    class JL_Pricing_Table_Widget extends Widget_Base {

        public function get_name() {
            return 'jl_pricing_table';
        }

        public function get_title() {
            return 'JL Pricing Table';
        }

        public function get_icon() {
            return 'eicon-price-table';
        }

        public function get_categories() {
            return [ 'general' ];
        }

        public function get_style_depends() {
            return [ 'jl-pricing-table' ];
        }

        protected function register_controls() {

            /*
            =========================
            CONTENT
            =========================
            */

            $this->start_controls_section(
                'content_section',
                [
                    'label' => 'Planes',
                    'tab'   => Controls_Manager::TAB_CONTENT,
                ]
            );

            $repeater = new Repeater();

            $repeater->add_control(
                'plan_name',
                [
                    'label'   => 'Nombre del plan',
                    'type'    => Controls_Manager::TEXT,
                    'default' => 'Growth',
                ]
            );

            $repeater->add_control(
                'plan_subtitle',
                [
                    'label'   => 'Subtítulo',
                    'type'    => Controls_Manager::TEXT,
                    'default' => 'Market Expansion',
                ]
            );

            $repeater->add_control(
                'plan_price',
                [
                    'label'   => 'Precio',
                    'type'    => Controls_Manager::TEXT,
                    'default' => '£749',
                ]
            );

            $repeater->add_control(
                'vat_text',
                [
                    'label'   => 'Texto VAT',
                    'type'    => Controls_Manager::TEXT,
                    'default' => '(EX. VAT)',
                ]
            );

            $repeater->add_control(
                'features',
                [
                    'label'       => 'Características',
                    'type'        => Controls_Manager::TEXTAREA,
                    'default'     => "9 posts/mo + 1 reel\n1 blog post/mo\nCommunity engagement",
                    'description' => 'Una característica por línea.',
                ]
            );

            $repeater->add_control(
                'button_text',
                [
                    'label'   => 'Texto botón',
                    'type'    => Controls_Manager::TEXT,
                    'default' => 'SELECT PLAN',
                ]
            );

            $repeater->add_control(
                'button_url',
                [
                    'label' => 'URL',
                    'type'  => Controls_Manager::URL,
                ]
            );

            $repeater->add_control(
                'is_featured',
                [
                    'label' => 'Destacado',
                    'type'  => Controls_Manager::SWITCHER,
                ]
            );

            $repeater->add_control(
                'badge_text',
                [
                    'label'   => 'Badge',
                    'type'    => Controls_Manager::TEXT,
                    'default' => 'MOST POPULAR',
                    'condition' => [
                        'is_featured' => 'yes',
                    ],
                ]
            );

            $repeater->add_control(
                'plan_icon',
                [
                    'label' => 'Icono',
                    'type'  => Controls_Manager::ICONS,
                ]
            );

            $this->add_control(
                'plans',
                [
                    'label'       => 'Planes',
                    'type'        => Controls_Manager::REPEATER,
                    'fields'      => $repeater->get_controls(),
                    'title_field' => '{{{ plan_name }}}',
                ]
            );

            $this->end_controls_section();





            /*
            =========================
            STYLE - CARDS
            =========================
            */

            $this->start_controls_section(
                'section_style_cards',
                [
                    'label' => 'Cards',
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'card_bg',
                [
                    'label' => 'Background',
                    'type' => Controls_Manager::COLOR,
                    'default' => '#091317',
                    'selectors' => [
                        '{{WRAPPER}} .jl-pricing-card' => 'background: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'featured_bg',
                [
                    'label' => 'Featured Background',
                    'type' => Controls_Manager::COLOR,
                    'default' => '#31566b',
                    'selectors' => [
                        '{{WRAPPER}} .jl-pricing-card.is-featured' => 'background: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'card_border_color',
                [
                    'label' => 'Border Color',
                    'type' => Controls_Manager::COLOR,
                    'default' => 'rgba(255,255,255,.12)',
                    'selectors' => [
                        '{{WRAPPER}} .jl-pricing-card' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'grid_gap',
                [
                    'label' => 'Gap',
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .jl-pricing-wrapper' => 'gap: {{SIZE}}px;',
                    ],
                ]
            );

            $this->add_responsive_control(
                'card_padding',
                [
                    'label' => 'Padding',
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px' ],
                    'selectors' => [
                        '{{WRAPPER}} .jl-pricing-content' =>
                            'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_section();





            /*
            =========================
            TITLE
            =========================
            */

            $this->start_controls_section(
                'section_title_style',
                [
                    'label' => 'Title',
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'title_color',
                [
                    'label' => 'Color',
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .jl-pricing-title' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'title_typography',
                    'selector' => '{{WRAPPER}} .jl-pricing-title',
                ]
            );

            $this->end_controls_section();





            /*
            =========================
            SUBTITLE
            =========================
            */

            $this->start_controls_section(
                'section_subtitle_style',
                [
                    'label' => 'Subtitle',
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'subtitle_color',
                [
                    'label' => 'Color',
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .jl-pricing-subtitle' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'subtitle_typography',
                    'selector' => '{{WRAPPER}} .jl-pricing-subtitle',
                ]
            );

            $this->end_controls_section();





            /*
            =========================
            PRICE
            =========================
            */

            $this->start_controls_section(
                'section_price_style',
                [
                    'label' => 'Price',
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'price_color',
                [
                    'label' => 'Color',
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .jl-pricing-price' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'price_typography',
                    'selector' => '{{WRAPPER}} .jl-pricing-price',
                ]
            );

            $this->end_controls_section();





            /*
            =========================
            FEATURES
            =========================
            */

            $this->start_controls_section(
                'section_features_style',
                [
                    'label' => 'Features',
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'features_color',
                [
                    'label' => 'Color',
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .jl-pricing-features li' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'features_typography',
                    'selector' => '{{WRAPPER}} .jl-pricing-features li',
                ]
            );

            $this->end_controls_section();





            /*
            =========================
            BUTTON
            =========================
            */

            $this->start_controls_section(
                'section_button_style',
                [
                    'label' => 'Button',
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'button_text_color',
                [
                    'label' => 'Text Color',
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .jl-pricing-button' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_bg',
                [
                    'label' => 'Background',
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .jl-pricing-button' => 'background: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'button_border',
                    'selector' => '{{WRAPPER}} .jl-pricing-button',
                ]
            );

            $this->add_responsive_control(
                'button_radius',
                [
                    'label' => 'Border Radius',
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .jl-pricing-button' =>
                            'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'button_typography',
                    'selector' => '{{WRAPPER}} .jl-pricing-button',
                ]
            );

            $this->end_controls_section();
        }

        protected function render() {

            $settings = $this->get_settings_for_display();

            if ( empty( $settings['plans'] ) ) {
                return;
            }

            // Encola el CSS externo del widget sin imprimir estilos inline en el render.
            wp_enqueue_style( 'jl-pricing-table' );

            ?>

            <div class="jl-pricing-wrapper">

                <?php foreach ( $settings['plans'] as $plan ) :

                    $featured = ! empty( $plan['is_featured'] ) && $plan['is_featured'] === 'yes';

                    $features = ! empty( $plan['features'] )
                        ? explode( "\n", $plan['features'] )
                        : [];

                    $url = ! empty( $plan['button_url']['url'] )
                        ? $plan['button_url']['url']
                        : '#';

                    ?>

                    <?php $card_classes = $featured && ! empty( $plan['badge_text'] ) ? 'is-featured has-badge' : ( $featured ? 'is-featured' : '' ); ?>

                    <div class="jl-pricing-card <?php echo esc_attr( $card_classes ); ?>">

                        <?php if ( $featured && ! empty( $plan['badge_text'] ) ) : ?>

                            <div class="jl-pricing-badge">
                                <?php echo esc_html( $plan['badge_text'] ); ?>
                            </div>

                        <?php endif; ?>

                        <div class="jl-pricing-content">

                            <div class="jl-pricing-header">

                                <div class="jl-pricing-heading">

                                    <h3 class="jl-pricing-title">
                                        <?php echo esc_html( $plan['plan_name'] ); ?>
                                    </h3>

                                    <div class="jl-pricing-subtitle">
                                        <?php echo esc_html( $plan['plan_subtitle'] ); ?>
                                    </div>

                                </div>

                                <?php if ( ! empty( $plan['plan_icon']['value'] ) ) : ?>

                                    <div class="jl-pricing-icon">

                                        <?php Icons_Manager::render_icon(
                                            $plan['plan_icon'],
                                            [ 'aria-hidden' => 'true' ]
                                        ); ?>

                                    </div>

                                <?php endif; ?>

                            </div>

                            <div class="jl-pricing-price">
                                <?php echo esc_html( $plan['plan_price'] ); ?>
                            </div>

                            <div class="jl-pricing-vat">
                                <?php echo esc_html( $plan['vat_text'] ); ?>
                            </div>

                            <ul class="jl-pricing-features">

                                <?php foreach ( $features as $feature ) :

                                    $feature = trim( $feature );

                                    if ( empty( $feature ) ) {
                                        continue;
                                    }

                                    ?>

                                    <li>
                                        <span class="jl-check">✓</span>
                                        <span><?php echo esc_html( $feature ); ?></span>
                                    </li>

                                <?php endforeach; ?>

                            </ul>

                            <a class="jl-pricing-button" href="<?php echo esc_url( $url ); ?>">
                                <?php echo esc_html( $plan['button_text'] ); ?>
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <?php
        }
    }

    $widgets_manager->register( new JL_Pricing_Table_Widget() );
});