// Валидация категории: первая буква должна быть заглавной

document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('quiz-create-form');
  const categoryInput = document.getElementById('create-category');

  form.addEventListener('submit', function(e) {
    const value = categoryInput.value.trim();
    if (!value) {
      alert('Пожалуйста, введите категорию.');
      categoryInput.focus();
      e.preventDefault();
      return;
    }
    if (value[0] !== value[0].toUpperCase()) {
      alert('Категория должна начинаться с заглавной буквы!');
      categoryInput.focus();
      e.preventDefault();
      return;
    }
  });
}); 