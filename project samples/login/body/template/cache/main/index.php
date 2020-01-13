<p><?php if (!$isAuthenticated):?>
<a href="/login"><?=  $language->get('page.index.login') ?></a> | <a href="/register"><?=  $language->get('page.index.register') ?></a><?php else:?><a href="/logged"><?=  $language->get('page.index.logged', null, $authUser->getName()) ?></a> | <a href="/logout"><?=  $language->get('page.index.logout') ?></a>
<?php  endif; ?> 
| <a href="/new_template"><?=  $language->get('page.index.new_template') ?></a> | <a href="/parameters_examples"><?=  $language->get('page.index.parameters_examples') ?></a></p>