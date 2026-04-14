// validate.js — Pure JS validation. NO HTML5 attributes.
// id="mail" (NOT id="email") — mirrors PHP column name

function validateForm() {
    var errors   = [];
    var nom      = document.getElementById('nom')      ? document.getElementById('nom').value.trim()      : null;
    var prenom   = document.getElementById('prenom')   ? document.getElementById('prenom').value.trim()   : null;
    var mail     = document.getElementById('mail').value.trim();
    var password = document.getElementById('password').value.trim();

    // Empty checks
    if (nom !== null && nom === '')      errors.push('Le Nom est obligatoire.');
    if (prenom !== null && prenom === '') errors.push('Le Prénom est obligatoire.');
    if (mail === '')                      errors.push('Le Mail est obligatoire.');
    if (password === '')                  errors.push('Le Mot de passe est obligatoire.');

    var lettersOnly = /^[a-zA-ZÀ-ÿ]+$/;
    if (nom && nom !== '' && !lettersOnly.test(nom))
        errors.push('Le Nom doit contenir uniquement des lettres.');
    if (prenom && prenom !== '' && !lettersOnly.test(prenom))
        errors.push('Le Prénom doit contenir uniquement des lettres.');

    if (mail !== '' && !/^[a-zA-Z0-9._%+\-]+@gmail\.com$/.test(mail))
        errors.push("Le mail doit être au format exemple@gmail.com.");

    if (password !== '' && !/^\d+$/.test(password))
        errors.push('Le mot de passe doit contenir uniquement des chiffres.');
    if (password !== '' && password.length < 4)
        errors.push('Le mot de passe doit contenir au moins 4 chiffres.');

    if (errors.length > 0) {
        showErrors(errors);
        return false;
    }
    return true;
}

function showErrors(errors) {
    var existing = document.getElementById('error-box');
    if (existing) existing.remove();

    var box = document.createElement('div');
    box.id = 'error-box';
    box.style.cssText = 'background:#ffe0e0;border:1px solid red;color:red;' +
                        'padding:10px 15px;margin-bottom:15px;border-radius:5px;font-size:14px;';
    var html = '<ul style="margin:0;padding-left:18px;">';
    for (var i = 0; i < errors.length; i++) {
        html += '<li>' + errors[i] + '</li>';
    }
    html += '</ul>';
    box.innerHTML = html;

    var form = document.getElementById('user-form');
    form.parentNode.insertBefore(box, form);
    window.scrollTo(0, 0);
}

// Live border feedback on blur
window.onload = function () {
    var rules = {
        nom     : function(v){ return /^[a-zA-ZÀ-ÿ]+$/.test(v); },
        prenom  : function(v){ return /^[a-zA-ZÀ-ÿ]+$/.test(v); },
        mail    : function(v){ return /^[a-zA-Z0-9._%+\-]+@gmail\.com$/.test(v); },
        password: function(v){ return /^\d{4,}$/.test(v); }
    };

    Object.keys(rules).forEach(function(id) {
        var el = document.getElementById(id);
        if (!el) return;

        el.addEventListener('blur', function() {
            var val = this.value.trim();
            if (val === '') {
                this.style.border = '2px solid orange';
            } else {
                this.style.border = rules[id](val) ? '2px solid green' : '2px solid red';
            }
        });

        el.addEventListener('focus', function() {
            this.style.border = '2px solid #aaaaaa';
        });
    });
};
