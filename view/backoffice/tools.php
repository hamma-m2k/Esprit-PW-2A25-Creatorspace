<?php require_once __DIR__ . '/layout_back.php'; ?>

<div class="back-section active">
    <div class="back-header">
        <div>
            <h2 style="font-size:1.6rem; color:var(--text);">🛠️ Outils Avancés (AI & OCR)</h2>
            <p style="color:var(--text3); font-size:0.9rem; margin-top:4px;">Utilisez l'intelligence artificielle pour analyser votre plateforme.</p>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:30px;">
        <!-- OCR CARD -->
        <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3); border-radius:16px; padding:20px;">
            <h3 style="color:#ffffff; margin-bottom:15px; display:flex; align-items:center; gap:10px;">📄 OCR (Reconnaissance de texte)</h3>
            <p style="color:#94a3b8; font-size:0.9rem; margin-bottom:20px;">Analysez une image pour en extraire le texte automatiquement.</p>
            
            <form action="index.php?ctrl=user&action=ocrScan" method="POST" enctype="multipart/form-data">
                <div style="border:2px dashed rgba(108,63,197,0.4); border-radius:12px; padding:30px; text-align:center; cursor:pointer; margin-bottom:15px;" 
                     onclick="document.getElementById('ocr_file').click()">
                    <input type="file" name="ocr_image" id="ocr_file" style="display:none;" accept="image/*" onchange="this.form.submit()">
                    <span style="color:#a855f7; font-size:1.2rem;">📁 Cliquez pour uploader une image</span>
                </div>
            </form>

            <?php if (isset($ocrResult)): ?>
                <div style="background:rgba(0,0,0,0.3); border-radius:8px; padding:15px; margin-top:15px; color:#ffffff; font-family:monospace; font-size:0.9rem; border:1px solid #a855f7;">
                    <strong>Texte extrait :</strong><br><br>
                    <?= nl2br(htmlspecialchars($ocrResult)) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- AI ANALYSIS CARD -->
        <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3); border-radius:16px; padding:20px;">
            <h3 style="color:#ffffff; margin-bottom:15px; display:flex; align-items:center; gap:10px;">🤖 Classification AI (HuggingFace)</h3>
            <p style="color:#94a3b8; font-size:0.9rem; margin-bottom:20px;">Classez automatiquement vos textes par thématique.</p>
            
            <form action="index.php?ctrl=user&action=aiAnalyze" method="POST">
                <textarea name="ai_text" placeholder="Entrez un texte à analyser..." 
                          style="width:100%; height:120px; background:rgba(0,0,0,0.2); border:1px solid rgba(108,63,197,0.4); border-radius:8px; color:white; padding:12px; outline:none; margin-bottom:15px;"></textarea>
                <button type="submit" class="btn btn-primary" style="width:100%;">🚀 Analyser le texte</button>
            </form>

            <?php if (isset($aiResult)): ?>
                <div style="background:rgba(0,0,0,0.3); border-radius:8px; padding:15px; margin-top:15px; color:#ffffff; border:1px solid #00C2CB;">
                    <strong>Résultats de l'analyse :</strong><br><br>
                    <?php if (isset($aiResult['labels'])): ?>
                        <?php foreach($aiResult['labels'] as $index => $label): ?>
                            <div style="margin-bottom:8px;">
                                <div style="display:flex; justify-content:space-between; font-size:0.85rem; margin-bottom:4px;">
                                    <span><?= ucfirst($label) ?></span>
                                    <span><?= round($aiResult['scores'][$index] * 100) ?>%</span>
                                </div>
                                <div style="height:4px; background:rgba(255,255,255,0.1); border-radius:2px;">
                                    <div style="height:100%; width:<?= $aiResult['scores'][$index] * 100 ?>%; background:#00C2CB; border-radius:2px;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span style="color:#ef4444;">Erreur AI : <?= htmlspecialchars(json_encode($aiResult)) ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- CHAT WIDGET -->
    <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3); border-radius:16px; padding:20px;">
        <h3 style="color:#ffffff; margin-bottom:15px; display:flex; align-items:center; gap:10px;">💬 Chat Support (Intégration Directe)</h3>
        <p style="color:#94a3b8; font-size:0.9rem; margin-bottom:20px;">Le widget de chat est activé pour les administrateurs et les utilisateurs.</p>
        <div style="background:rgba(108,63,197,0.1); padding:20px; border-radius:12px; text-align:center;">
            <p style="color:#a855f7; font-weight:600;">Regardez en bas à droite de votre écran !</p>
            <p style="color:#94a3b8; font-size:0.8rem;">Note : Le script de chat Tawk.to est injecté automatiquement.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
