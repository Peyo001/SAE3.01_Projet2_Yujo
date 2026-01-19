(function(){
  document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('quiz-form');
    if (!form) return;
    const resultEl = document.getElementById('quiz-result');

    form.addEventListener('submit', function(e){
      e.preventDefault();
      const inputs = Array.from(form.querySelectorAll('input[name="answer"]'));
      const selected = inputs.filter(i => i.checked);
      if (selected.length === 0) {
        resultEl.textContent = "Choisis au moins une réponse.";
        resultEl.className = 'mt-2 fw-semibold text-warning';
        return;
      }
      // Vérifier si toutes les réponses sélectionnées sont correctes
      const allSelectedAreCorrect = selected.every(i => i.dataset.correct === '1');
      // Vérifier si toutes les réponses correctes ont été sélectionnées
      const allCorrectAreSelected = inputs.every(i => i.dataset.correct !== '1' || i.checked);
      
      if (allSelectedAreCorrect && allCorrectAreSelected) {
        resultEl.textContent = "Bravo, bonne réponse !";
        resultEl.className = 'mt-2 fw-semibold text-success';
      } else {
        resultEl.textContent = "Ce n'est pas tout à fait ça. Essaie encore.";
        resultEl.className = 'mt-2 fw-semibold text-danger';
      }
    });
  });
})();
