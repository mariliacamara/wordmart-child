(function($){
  function moveCatToggles() {
    $('.widget_product_categories .product-categories li.cat-parent').each(function(){
      var $li = $(this);
      var $toggle = $li.find('> .wd-cats-toggle').first();
      var $link = $li.find('> a').first();
      if ($toggle.length && $link.length) {
        // se ainda estiver depois do ul, move para antes do link
        if ($toggle.index() > $link.index()) {
          $toggle.insertBefore($link);
        }
      }
    });
  }

  // executa depois do DOM e também quando woodmart reinicializar
  $(function(){
    moveCatToggles();
    // tenta novamente algumas vezes caso o tema adicione dinamicamente
    var tries = 0;
    var timer = setInterval(function(){
      moveCatToggles();
      tries++;
      if (tries > 10) clearInterval(timer);
    }, 300);
  });

  // opcional: também ouve evento do tema (se existir)
  if (typeof woodmartThemeModule !== 'undefined') {
    woodmartThemeModule.$document.on('wdShopPageInit wdBackHistory', moveCatToggles);
  }
})(jQuery);
