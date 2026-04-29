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
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
