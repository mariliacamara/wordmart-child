                      'aria-label'       => $product->add_to_cart_description(),
                      'rel'              => 'nofollow',
                    ) ),
                    esc_html__( 'Adicionar', 'woocommerce' )
                  ),
                  $product,
                  $product->get_id()
                );
              ?>
            </div>

          <?php endif; ?>

        <?php endif; ?>

      </div>
    </div>


    </div> <!-- .product-element-bottom -->

  </div> <!-- .product-wrapper -->
</li>