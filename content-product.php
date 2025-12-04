<?php
/**
 * content-product.php
 * Template padrão sobrescrito para usar SEMPRE o layout custom.
 */

defined( 'ABSPATH' ) || exit;

// Carrega SEMPRE o template custom que você criou em:
// /wp-content/themes/SEU-CHILD-THEME/woocommerce/loop/content-product-related-custom.php
wc_get_template_part( 'content', 'product-related' );
