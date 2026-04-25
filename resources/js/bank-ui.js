/**
 * Scroll reveal + light enhancement for public bank pages.
 */
function initBankUi() {
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduceMotion) {
    document.querySelectorAll('.reveal, .reveal-child').forEach((el) => {
      el.classList.add('is-visible');
    });
    return;
  }

  const revealEls = document.querySelectorAll('.reveal, .reveal-child');
  if (!revealEls.length) return;

  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    },
    { root: null, rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
  );

  revealEls.forEach((el) => io.observe(el));
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initBankUi);
} else {
  initBankUi();
}
