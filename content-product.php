<?php
/**
 * content-product.php
 * Template padrão sobrescrito para usar SEMPRE o layout custom.
 */

defined( 'ABSPATH' ) || exit;

// Carrega SEMPRE o template custom que você criou
wc_get_template_part( 'loop/content', 'product-related-custom' );
