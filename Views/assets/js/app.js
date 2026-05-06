// Ajout dynamique de lignes pour les règles
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addRuleBtn');
    const rulesContainer = document.getElementById('rulesContainer');
    
    if (addBtn && rulesContainer) {
        let ruleIndex = document.querySelectorAll('.rule-row').length;
        
        addBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.className = 'rule-row card';
            newRow.style.marginBottom = '1rem';
            newRow.style.padding = '1rem';
            newRow.innerHTML = `
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <div style="flex: 2;">
                        <input type="text" name="titres[]" class="form-control" placeholder="Titre de la règle" required>
                    </div>
                    <div style="flex: 3;">
                        <textarea name="descriptions[]" class="form-control" placeholder="Description" rows="2"></textarea>
                    </div>
                    <div style="flex: 1;">
                        <input type="number" name="positions[]" class="form-control" placeholder="Position">
                    </div>
                    <div style="flex: 0;">
                        <button type="button" class="btn btn-danger removeRuleBtn" style="background:#ef4444; padding:8px 16px;">✖</button>
                    </div>
                </div>
            `;
            rulesContainer.appendChild(newRow);
            
            newRow.querySelector('.removeRuleBtn').addEventListener('click', function() {
                newRow.remove();
            });
        });
    }
    
    // Import JSON preview
    const importBtn = document.getElementById('previewImport');
    const jsonInput = document.getElementById('importJson');
    const previewDiv = document.getElementById('jsonPreview');
    
    if (importBtn && jsonInput) {
        importBtn.addEventListener('click', function() {
            try {
                const data = JSON.parse(jsonInput.value);
                previewDiv.innerHTML = '<div class="badge badge-success">✓ JSON valide - ' + data.length + ' règles détectées</div>';
            } catch(e) {
                previewDiv.innerHTML = '<div class="badge badge-danger">✗ JSON invalide : ' + e.message + '</div>';
            }
        });
    }
});
