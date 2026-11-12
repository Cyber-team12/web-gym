const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* Bintang yang ngikutin kursor */
function initStars() {
    const canvas = document.createElement('canvas');
    canvas.id = 'star-canvas';
    canvas.setAttribute('aria-hidden', 'true');
    document.body.appendChild(canvas);
    const ctx = canvas.getContext('2d');

    let w = 0;
    let h = 0;
    const resize = () => {
        const dpr = window.devicePixelRatio || 1;
        w = window.innerWidth;
        h = window.innerHeight;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    };
    resize();
    window.addEventListener('resize', resize);

    const COLORS = ['#FFD166', '#FFF3C4', '#FF5C8A', '#B8B2FF'];
    const MAX_STARS = 160;
    const stars = [];
    let running = false;
    let lastX = null;
    let lastY = null;

    function spawn(x, y, burst = false) {
        if (stars.length >= MAX_STARS) return;
        const angle = Math.random() * Math.PI * 2;
        const speed = burst ? 1.5 + Math.random() * 3.5 : 0.3 + Math.random() * 0.9;
        stars.push({
            x,
            y,
            vx: Math.cos(angle) * speed,
            vy: Math.sin(angle) * speed - (burst ? 1 : 0),
            size: burst ? 6 + Math.random() * 7 : 4 + Math.random() * 6,
            rot: Math.random() * Math.PI,
            vr: (Math.random() - 0.5) * 0.1,
            life: 1,
            decay: burst ? 0.015 + Math.random() * 0.01 : 0.02 + Math.random() * 0.015,
            color: COLORS[(Math.random() * COLORS.length) | 0],
        });
        if (!running) {
            running = true;
            requestAnimationFrame(tick);
        }
    }

    function drawStar(s) {
        const outer = s.size;
        const inner = s.size * 0.38;
        ctx.save();
        ctx.translate(s.x, s.y);
        ctx.rotate(s.rot);
        ctx.globalAlpha = Math.max(s.life, 0);
        ctx.fillStyle = s.color;
        ctx.shadowColor = s.color;
        ctx.shadowBlur = 10;
        ctx.beginPath();
        for (let i = 0; i < 8; i++) {
            const r = i % 2 === 0 ? outer : inner;
            const a = (Math.PI / 4) * i - Math.PI / 2;
            ctx.lineTo(Math.cos(a) * r, Math.sin(a) * r);
        }
        ctx.closePath();
        ctx.fill();
        ctx.restore();
    }

    function tick() {
        ctx.clearRect(0, 0, w, h);
        for (let i = stars.length - 1; i >= 0; i--) {
            const s = stars[i];
            s.x += s.vx;
            s.y += s.vy;
            s.vy += 0.03;
            s.rot += s.vr;
            s.life -= s.decay;
            if (s.life <= 0) {
                stars.splice(i, 1);
                continue;
            }
            drawStar(s);
        }
        if (stars.length) {
            requestAnimationFrame(tick);
        } else {
            running = false;
            ctx.clearRect(0, 0, w, h);
        }
    }

    window.addEventListener(
        'pointermove',
        (e) => {
            if (lastX === null || Math.hypot(e.clientX - lastX, e.clientY - lastY) > 16) {
                spawn(e.clientX, e.clientY);
                lastX = e.clientX;
                lastY = e.clientY;
            }
        },
        { passive: true }
    );

    window.addEventListener('pointerdown', (e) => {
        for (let i = 0; i < 14; i++) spawn(e.clientX, e.clientY, true);
    });
}

/* Cahaya kartu ngikutin mouse */
function initGlowCards() {
    document.addEventListener(
        'pointermove',
        (e) => {
            const card = e.target.closest ? e.target.closest('.glow-card') : null;
            if (!card) return;
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${e.clientX - rect.left}px`);
            card.style.setProperty('--my', `${e.clientY - rect.top}px`);
        },
        { passive: true }
    );
}

/*Angka dashboard hitung naik */
function initCountUp() {
    document.querySelectorAll('[data-count]').forEach((el) => {
        const target = parseInt(el.dataset.count, 10) || 0;
        if (reduceMotion || target === 0) {
            el.textContent = target.toLocaleString('id-ID');
            return;
        }
        const duration = 900;
        const start = performance.now();
        const step = (now) => {
            const p = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(target * eased).toLocaleString('id-ID');
            if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    });
}

/* Tema Gelap & Terang (Color Mode Switcher) */
function initTheme() {
    const savedTheme = localStorage.getItem('fitgym_theme') || 'dark';
    applyTheme(savedTheme);

    document.querySelectorAll('.theme-toggle-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'dark';
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
        });
    });
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    localStorage.setItem('fitgym_theme', theme);

    document.querySelectorAll('.theme-toggle-btn').forEach((btn) => {
        const icon = btn.querySelector('.theme-icon');
        const text = btn.querySelector('.theme-text');
        if (theme === 'light') {
            if (icon) icon.textContent = '☀️';
            if (text) text.textContent = 'Tema Terang';
            btn.classList.remove('btn-outline-warning');
            btn.classList.add('btn-outline-primary');
        } else {
            if (icon) icon.textContent = '🌙';
            if (text) text.textContent = 'Tema Gelap';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-outline-warning');
        }
    });
}

if (!reduceMotion) initStars();
initGlowCards();
initCountUp();
initTheme();
