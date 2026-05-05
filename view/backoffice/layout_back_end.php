    </main>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const animateElements = document.querySelectorAll('.animate-number');
    animateElements.forEach(el => {
        const targetStr = el.getAttribute('data-target');
        const target = parseFloat(targetStr);
        if (isNaN(target)) return;
        
        const duration = 1500; 
        const frameRate = 30; 
        const totalFrames = Math.round(duration / frameRate);
        let currentFrame = 0;
        
        const isFloat = targetStr.includes('.');
        
        const interval = setInterval(() => {
            currentFrame++;
            const progress = currentFrame / totalFrames;
            const currentCount = target * (1 - Math.pow(1 - progress, 3));
            
            el.textContent = isFloat ? currentCount.toFixed(1) : Math.round(currentCount);
            
            if (currentFrame >= totalFrames) {
                clearInterval(interval);
                el.textContent = targetStr;
            }
        }, frameRate);
    });

    const animateBars = document.querySelectorAll('.animate-bar');
    animateBars.forEach(el => {
        const targetHeight = el.getAttribute('data-target-height');
        setTimeout(() => {
            el.style.height = targetHeight;
        }, 100);
    });
});

// 🩺 Health AI Global Logic (Analyse)
function resetHealthForm() {
  document.getElementById('health-form-step').style.display = 'block';
  document.getElementById('health-result-step').style.display = 'none';
}

async function runClinicalAi() {
  const btn = document.getElementById('btn-run-health');
  if (!btn) return;
  const oldText = btn.textContent;
  btn.textContent = "⌛ Analyse en cours...";
  btn.disabled = true;

  const payload = {
    age: document.getElementById('h-age').value,
    trestbps: document.getElementById('h-trestbps').value,
    chol: document.getElementById('h-chol').value,
    thalach: document.getElementById('h-thalach').value,
    oldpeak: document.getElementById('h-oldpeak').value,
    ca: document.getElementById('h-ca').value,
    sex: document.getElementById('h-sex').checked ? 'male' : 'female',
    exang: document.getElementById('h-exang').checked ? 1 : 0,
    fbs: document.getElementById('h-fbs').checked ? 1 : 0,
    restecg: document.getElementById('h-restecg').checked ? 1 : 0,
    thal: document.getElementById('h-thal').checked ? 1 : 0,
    smoker: document.getElementById('h-smoker').checked ? 1 : 0
  };

  try {
    const response = await fetch('index.php?ctrl=user&action=healthAi', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await response.json();

    if (data.error) {
      alert("Erreur: " + data.error);
    } else {
      document.getElementById('health-form-step').style.display = 'none';
      document.getElementById('health-result-step').style.display = 'block';

      document.getElementById('res-diag').textContent = data.diagnostic;
      document.getElementById('res-score').textContent = data.score + '%';
      
      const badge = document.getElementById('res-badge');
      badge.textContent = data.diagnostic;
      badge.className = 'diag-badge ' + (data.score > 50 ? 'badge-high' : 'badge-low');

      const probBox = document.getElementById('prob-container');
      probBox.innerHTML = '';
      const probColors = { absence: '#48BB78', stade1: '#ED8936', stade2: '#F6AD55', stade3: '#E53E3E' };
      const probLabels = { absence: 'Absence de maladie', stade1: 'Stade 1 (léger)', stade2: 'Stade 2 (modéré)', stade3: 'Stade 3 (sévère)' };
      
      for (const [key, val] of Object.entries(data.probabilities)) {
        probBox.innerHTML += `
          <div class="prob-row">
            <span class="prob-label">${probLabels[key]}</span>
            <div class="prob-bar-bg"><div class="prob-bar-fill" style="width:${val}%; background:${probColors[key]}"></div></div>
            <span class="prob-val">${val}%</span>
          </div>
        `;
      }

      const featBox = document.getElementById('feat-container');
      featBox.innerHTML = '';
      data.importance.forEach(f => {
        featBox.innerHTML += `
          <div class="feat-row">
            <span class="feat-label">${f.feature}</span>
            <div class="feat-dot"></div>
            <div class="feat-bar"><div class="feat-bar-fill" style="width:${f.value}%"></div></div>
            <span style="font-size:0.75rem; color:var(--text3); width:30px; text-align:right;">${f.value}%</span>
          </div>
        `;
      });

      const tipsBox = document.getElementById('res-tips');
      tipsBox.innerHTML = '';
      data.conseils.forEach(c => {
        tipsBox.innerHTML += `<li>${c}</li>`;
      });
    }
  } catch (e) {
    console.error(e);
    alert("Erreur technique lors de l'analyse.");
  } finally {
    btn.textContent = oldText;
    btn.disabled = false;
  }
}

async function runClinicalAi() {
  const btn = document.getElementById('btn-run-health');
  if (!btn) return;
  const oldText = btn.textContent;
  btn.textContent = "⌛ Analyse en cours...";
  btn.disabled = true;

  const payload = {
    age: document.getElementById('h-age').value,
    trestbps: document.getElementById('h-trestbps').value,
    chol: document.getElementById('h-chol').value,
    thalach: document.getElementById('h-thalach').value,
    oldpeak: document.getElementById('h-oldpeak').value,
    ca: document.getElementById('h-ca').value,
    sex: document.getElementById('h-sex').checked ? 'male' : 'female',
    exang: document.getElementById('h-exang').checked ? 1 : 0,
    fbs: document.getElementById('h-fbs').checked ? 1 : 0,
    restecg: document.getElementById('h-restecg').checked ? 1 : 0,
    thal: document.getElementById('h-thal').checked ? 1 : 0,
    smoker: document.getElementById('h-smoker').checked ? 1 : 0
  };

  try {
    const response = await fetch('index.php?ctrl=user&action=healthAi', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await response.json();

    if (data.error) {
      alert("Erreur: " + data.error);
    } else {
      document.getElementById('health-form-step').style.display = 'none';
      document.getElementById('health-result-step').style.display = 'block';

      document.getElementById('res-diag').textContent = data.diagnostic;
      document.getElementById('res-score').textContent = data.score + '%';
      
      const badge = document.getElementById('res-badge');
      badge.textContent = data.diagnostic;
      badge.className = 'diag-badge ' + (data.score > 50 ? 'badge-high' : 'badge-low');

      const probBox = document.getElementById('prob-container');
      probBox.innerHTML = '';
      const probColors = { absence: '#48BB78', stade1: '#ED8936', stade2: '#F6AD55', stade3: '#E53E3E' };
      const probLabels = { absence: 'Absence de maladie', stade1: 'Stade 1 (léger)', stade2: 'Stade 2 (modéré)', stade3: 'Stade 3 (sévère)' };
      
      for (const [key, val] of Object.entries(data.probabilities)) {
        probBox.innerHTML += `
          <div class="prob-row">
            <span class="prob-label">${probLabels[key]}</span>
            <div class="prob-bar-bg"><div class="prob-bar-fill" style="width:${val}%; background:${probColors[key]}"></div></div>
            <span class="prob-val">${val}%</span>
          </div>
        `;
      }

      const featBox = document.getElementById('feat-container');
      featBox.innerHTML = '';
      data.importance.forEach(f => {
        featBox.innerHTML += `
          <div class="feat-row">
            <span class="feat-label">${f.feature}</span>
            <div class="feat-dot"></div>
            <div class="feat-bar"><div class="feat-bar-fill" style="width:${f.value}%"></div></div>
            <span style="font-size:0.75rem; color:var(--text3); width:30px; text-align:right;">${f.value}%</span>
          </div>
        `;
      });

      const tipsBox = document.getElementById('res-tips');
      tipsBox.innerHTML = '';
      data.conseils.forEach(c => {
        tipsBox.innerHTML += `<li>${c}</li>`;
      });
    }
  } catch (e) {
    console.error(e);
    alert("Erreur technique lors de l'analyse.");
  } finally {
    btn.textContent = oldText;
    btn.disabled = false;
  }
}
</script>

<!-- 🩺 Health AI Modal Global -->
<div class="modal" id="health-modal" style="max-width: 800px;">
  <div class="modal-header">
    <div style="display:flex; align-items:center; gap:12px;">
      <span style="font-size:1.8rem;">🩺</span>
      <div>
        <h3 style="margin:0;">Prédicteur de Santé Cardiaque</h3>
        <p style="margin:0; font-size:0.75rem; color:var(--text3);">Basé sur le dataset Cleveland Heart Disease (UCI)</p>
      </div>
    </div>
    <button class="modal-close" onclick="closeAllModals()">✕</button>
  </div>
  <div class="modal-body">
    <div id="health-form-step">
      <div class="health-input-grid">
        <div class="left-col">
          <div class="health-slider-group">
            <div class="health-slider-head"><label>Âge (ans)</label><span class="health-slider-val" id="val-age">52</span></div>
            <input type="range" id="h-age" min="1" max="100" value="52" oninput="document.getElementById('val-age').textContent=this.value">
          </div>
          <div class="health-slider-group">
            <div class="health-slider-head"><label>Pression systolique (mmHg)</label><span class="health-slider-val" id="val-trestbps">130</span></div>
            <input type="range" id="h-trestbps" min="80" max="200" value="130" oninput="document.getElementById('val-trestbps').textContent=this.value">
          </div>
          <div class="health-slider-group">
            <div class="health-slider-head"><label>Cholestérol (mg/dL)</label><span class="health-slider-val" id="val-chol">240</span></div>
            <input type="range" id="h-chol" min="100" max="600" value="240" oninput="document.getElementById('val-chol').textContent=this.value">
          </div>
          <div class="health-slider-group">
            <div class="health-slider-head"><label>Fréq. cardiaque max</label><span class="health-slider-val" id="val-thalach">150</span></div>
            <input type="range" id="h-thalach" min="60" max="220" value="150" oninput="document.getElementById('val-thalach').textContent=this.value">
          </div>
          <div class="health-slider-group">
            <div class="health-slider-head"><label>Dépression ST (mm)</label><span class="health-slider-val" id="val-oldpeak">1.0</span></div>
            <input type="range" id="h-oldpeak" min="0" max="10" step="0.1" value="1.0" oninput="document.getElementById('val-oldpeak').textContent=this.value">
          </div>
          <div class="health-slider-group">
            <div class="health-slider-head"><label>Nb. vaisseaux (0-3)</label><span class="health-slider-val" id="val-ca">0</span></div>
            <input type="range" id="h-ca" min="0" max="3" value="0" oninput="document.getElementById('val-ca').textContent=this.value">
          </div>
        </div>
        <div class="right-col" style="display:flex; flex-direction:column; gap:12px; padding-top:10px;">
          <label style="display:flex; align-items:center; gap:10px; cursor:pointer;"><input type="checkbox" id="h-sex" checked> Sexe masculin</label>
          <label style="display:flex; align-items:center; gap:10px; cursor:pointer;"><input type="checkbox" id="h-exang"> Angine induite à l'effort</label>
          <label style="display:flex; align-items:center; gap:10px; cursor:pointer;"><input type="checkbox" id="h-fbs"> Glycémie à jeun > 120</label>
          <label style="display:flex; align-items:center; gap:10px; cursor:pointer;"><input type="checkbox" id="h-restecg"> ECG anormal (hypertrophie)</label>
          <label style="display:flex; align-items:center; gap:10px; cursor:pointer;"><input type="checkbox" id="h-thal"> Thalassémie réversible</label>
          <label style="display:flex; align-items:center; gap:10px; cursor:pointer;"><input type="checkbox" id="h-smoker"> Fumeur actif</label>
        </div>
      </div>
      <div style="margin-top:30px; text-align:center;">
        <button class="btn btn-primary" onclick="runClinicalAi()" id="btn-run-health" style="width:100%; padding:14px;">🚀 Lancer l'analyse</button>
      </div>
    </div>
    <div id="health-result-step" style="display:none;">
      <div class="diag-box">
        <div>
          <div style="font-size:0.75rem; color:var(--text3); text-transform:uppercase;">Diagnostic prédit</div>
          <div class="diag-title" id="res-diag">Chargement...</div>
          <div style="font-size:0.8rem; color:var(--text2);">Score de risque : <strong id="res-score">0%</strong></div>
        </div>
        <div class="diag-badge" id="res-badge">...</div>
      </div>
      <div class="health-container">
        <div class="health-card">
          <div class="health-title">Probabilités</div>
          <div id="prob-container"></div>
        </div>
        <div class="health-card">
          <div class="health-title">Importance features</div>
          <div id="feat-container"></div>
        </div>
        <div class="health-card health-full">
          <div class="health-title">Conseils</div>
          <ul id="res-tips" style="padding-left:20px; font-size:0.85rem;"></ul>
        </div>
      </div>
      <button class="btn btn-outline" onclick="resetHealthForm()" style="width:100%; margin-top:20px;">Réinitialiser</button>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
