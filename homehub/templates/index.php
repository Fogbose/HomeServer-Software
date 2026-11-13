<?php
declare(strict_types=1);
style('homehub', 'hub');
script('homehub', 'hub');
?>
<div id="app-content" class="homehub">
  <div class="homehub-inner">
    <header class="homehub-header">
      <p class="homehub-eyebrow">VOTRE ESPACE NEXTCLOUD</p>
      <h1>Bienvenue à la maison.</h1>
      <p class="homehub-intro">Vos applications, réunies au même endroit.</p>
    </header>
    <div class="homehub-toolbar">
      <label class="homehub-search"><span class="hidden-visually">Rechercher une application</span>
        <input id="homehub-search" type="search" placeholder="Rechercher une application…" autocomplete="off">
      </label>
      <div class="homehub-filters" role="group" aria-label="Catégories">
        <?php foreach (['Tout', 'Quotidien', 'Organisation', 'Documents'] as $category): ?>
          <button type="button" data-category="<?php p($category); ?>" aria-pressed="<?php p($category === 'Tout' ? 'true' : 'false'); ?>"><?php p($category); ?></button>
        <?php endforeach; ?>
      </div>
    </div>
    <p id="homehub-count" class="homehub-count" aria-live="polite"><?php p(count($_['cards'])); ?> applications</p>
    <nav class="homehub-grid" aria-label="Applications du foyer">
      <?php foreach ($_['cards'] as $card): ?>
        <a id="homehub-app-<?php p($card['id']); ?>" class="homehub-card" href="<?php p($card['url']); ?>" target="_self" data-app="<?php p($card['id']); ?>" data-category="<?php p($card['category']); ?>">
          <span class="homehub-app-icon" style="background-color:<?php p($card['color']); ?>"><img src="<?php p($card['icon']); ?>" alt="" width="32" height="32"></span>
          <span class="homehub-card-copy"><strong><?php p($card['name']); ?></strong><span><?php p($card['description']); ?></span></span>
          <span class="homehub-arrow" aria-hidden="true">↗</span>
        </a>
      <?php endforeach; ?>
    </nav>
    <p id="homehub-empty" class="homehub-empty" hidden>Aucune application ne correspond à cette recherche.</p>
    <footer class="homehub-footer">Vos applications utilisent votre compte et vos droits Nextcloud.</footer>
  </div>
</div>
