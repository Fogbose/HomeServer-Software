document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('.homehub');
  if (!root) return;
  const cards = Array.from(root.querySelectorAll('.homehub-card'));
  const search = root.querySelector('#homehub-search');
  const buttons = root.querySelectorAll('[data-category]:is(button)');
  let category = 'Tout';
  function update() {
    const query = search.value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('fr');
    let count = 0;
    cards.forEach(card => {
      const text = card.textContent.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('fr');
      card.hidden = !(category === 'Tout' || card.dataset.category === category) || !text.includes(query);
      if (!card.hidden) count++;
    });
    root.querySelector('#homehub-count').textContent = count + (count === 1 ? ' application' : ' applications');
    root.querySelector('#homehub-empty').hidden = count !== 0;
  }
  search.addEventListener('input', update);
  buttons.forEach(button => button.addEventListener('click', () => {
    category = button.dataset.category;
    buttons.forEach(other => other.setAttribute('aria-pressed', String(other === button)));
    update();
  }));
});
