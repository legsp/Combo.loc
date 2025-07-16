<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OMW_Button_Cart extends OMW_Button {
	public $cart_items;

	public function __construct() {
		$this->button_custom_message = get_option( 'evwapp_opiton_message_cart' );
		$this->button_text = get_option( 'evwapp_opiton_text_button_cart' );
		$this->target = get_option( 'evwapp_opiton_cart_button_target' );
	}

	public function output_btn() {
		if ( ! is_cart() || WC()->cart->is_empty() ) {
			return;
		}

		$this->cart_items = WC()->cart->get_cart_contents();
		$shared_text = $this->create_shared_text();
		$whatsapp_link = $this->create_whatsapp_link( $shared_text );

		$button_html = sprintf(
			'<div class="div_evowap_btn" style="width: 100%%; margin-top: 1em; text-align:center;"><a href="%s" class="evowap_btn button" id="evowap_btn_cart" role="button" target="%s" style="justify-content: center;">%s%s</a></div>',
			esc_url( $whatsapp_link ),
			esc_attr( $this->target ),
			$this->icon,
			esc_html( $this->button_text )
		);

		$button_html_js = addslashes( str_replace( [ "\r", "\n" ], '', $button_html ) );

		// This script targets the specific container for the checkout button in your Salient/block-based theme.
		$script = "
        document.addEventListener('DOMContentLoaded', function() {
            function injectWhatsAppButton() {
                const checkoutContainer = document.querySelector('.wc-block-cart__submit-container');
                if (checkoutContainer && !document.getElementById('evowap_btn_cart')) {
                    checkoutContainer.insertAdjacentHTML('beforeend', '{$button_html_js}');
                }
            }
            // Block-based carts can be slow to render, so we'll check multiple times.
            const interval = setInterval(() => {
                const checkoutContainer = document.querySelector('.wc-block-cart__submit-container');
                if (checkoutContainer) {
                    injectWhatsAppButton();
                    clearInterval(interval);
                }
            }, 500);

            // A final check after 2 seconds as a fallback.
            setTimeout(() => {
                clearInterval(interval);
                injectWhatsAppButton();
            }, 2000);
        });
		";

		wc_enqueue_js( $script );
	}

	public function create_shared_text() {
        $cart_contents = $this->get_cart_items();
		$cart_total = __( 'Total do Carrinho:', 'woo-order-on-whatsapp' ) . ' ' . strip_tags( WC()->cart->get_cart_total() );
		$site_title = get_bloginfo( 'name' );
		$header = sprintf( __( 'Olá! Gostaria de fazer um orçamento em %s dos seguintes itens:', 'woo-order-on-whatsapp' ), $site_title );
        
        $message = get_option( 'evwapp_opiton_message_cart' ) ? get_option( 'evwapp_opiton_message_cart' ) : $header;

		return sprintf(
			'%1$s%2$s%3$s%2$s%4$s',
			$message,
			OMW_Utils::$doble_break_line,
			implode( OMW_Utils::$break_line, $cart_contents ),
			$cart_total
		);
	}

	public function get_cart_items() {
		$items = [];
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$product = $cart_item['data'];
			$product_name = $product->get_name();
			$quantity = $cart_item['quantity'];
			$line_total = strip_tags( wc_price( $cart_item['line_total'] ) );
			
			$item_string = "• {$quantity}x {$product_name} - {$line_total}";
			$items[] = $item_string;
		}
		return $items;
	}
}