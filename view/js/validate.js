/**
 * validate.js — Client-side validation
 * NO HTML5 attributes used. All rules enforced here in pure JS.
 * Mirrors exactly the PHP rules in UserController::validateForm().
 */

function validateForm() {
  var nom      = document.getElementById('nom').value.trim();
  var prenom   = document.getElementById('prenom').value.trim();
  var email    = document.getElementById('email').value.trim();
  var password = document.getElementById('password').value.trim();

  var errors = [];

  // RULE 1: No empty fields
  if (nom === '')      errors.push('Le champ Nom est obligatoire.');
  if (prenom === '')   errors.push('Le champ Prénom est obligatoire.');
  if (email === '')    errors.push('Le champ Email est obligatoire.');
  if (password === '') errors.push('Le champ Mot de passe est obligatoire.');

  // RULE 2: Nom — letters only (no numbers, no symbols, no spaces)
  var lettersOnly = /^[a-zA-ZÀ-ÿ]+$/;
  if (nom !== '' && !lettersOnly.test(nom)) {
    errors.push('Le Nom doit contenir uniquement des lettres.');
  }

  // RULE 3: Prenom — letters only (same rule)
  if (prenom !== '' && !lettersOnly.test(prenom)) {
    errors.push('Le Prénom doit contenir uniquement des lettres.');
  }

  // RULE 4: Email — must end with @gmail.com exactly
  var emailRegex = /^[a-zA-Z0-9._%+\-]+@gmail\.com$/;
  if (email !== '' && !emailRegex.test(email)) {
    errors.push("L'email doit être au format exemple@gmail.com.");
  }

  // RULE 5: Password minimum 6 characters
  if (password !== '' && password.length < 6) {
    errors.push('Le mot de passe doit contenir au moins 6 caractères.');
  }

  if (errors.length > 0) {
    showErrors(errors);
    return false; // BLOCK form submission
  }
  return true; // ALLOW form submission
}

function showErrors(errors) {
  // Remove existing error box if present
  var existing = document.getElementById('error-box');
  if (existing) existing.remove();

  var box = document.createElement('div');
  box.id = 'error-box';
  box.style.cssText = [
    'background:#ffe0e0',
    'border:1px solid red',
    'color:red',
    'padding:10px 15px',
    'margin-bottom:15px',
    'border-radius:5px',
    'font-size:14px'
  ].join(';');

  var list = '<ul style="margin:0;padding-left:18px;">';
  for (var i = 0; i < errors.length; i++) {
    list += '<li>' + errors[i] + '</li>';
  }
  list += '</ul>';
  box.innerHTML = list;

  var form = document.getElementById('user-form');
  form.parentNode.insertBefore(box, form);
  window.scrollTo(0, 0);
}

/**
 * Real-time border feedback on blur/focus.
 * No HTML5 — purely JS-driven visual cues.
 */
function addLiveValidation() {
  var fields = ['nom', 'prenom', 'email', 'password'];

  fields.forEach(function (fieldId) {
    var input = document.getElementById(fieldId);
    if (!input) return;

    input.addEventListener('blur', function () {
      var val   = this.value.trim();
      var valid = true;

      if (val === '') {
        valid = false;
      } else if (fieldId === 'nom' || fieldId === 'prenom') {
        valid = /^[a-zA-ZÀ-ÿ]+$/.test(val);
      } else if (fieldId === 'email') {
        valid = /^[a-zA-Z0-9._%+\-]+@gmail\.com$/.test(val);
      } else if (fieldId === 'password') {
        valid = val.length >= 6;
      }

      this.style.border = valid ? '2px solid green' : '2px solid red';
    });

    input.addEventListener('focus', function () {
      this.style.border = '2px solid #aaa';
    });
  });
}

window.onload = function () {
  addLiveValidation();
};
