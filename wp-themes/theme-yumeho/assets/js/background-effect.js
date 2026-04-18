/**
 * Interactive Organic Background: Complex Network
 * Features large "Hub" amoebas and many small "Particle" nodes,
 * connecting to form a complex, organic infographic web.
 */

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.createElement('canvas');
    canvas.id = 'organic-bg';
    const isSP = window.innerWidth <= 640;
    Object.assign(canvas.style, {
        position: 'fixed',
        top: '0',
        left: '0',
        width: '100%',
        height: '100%',
        zIndex: '-1',
        pointerEvents: 'none',
        opacity: isSP ? '0.12' : '0.6'
    });
    document.body.prepend(canvas);

    const ctx = canvas.getContext('2d');
    let width, height;

    // MindMarket Palette
    const colors = ['#a0e0db', '#fbd568', '#ff9aa2', '#9d8df1'];


    let particles = [];

    const config = {
        numParticles: isSP ? 15 : 45,
        connectionDist: isSP ? 100 : 180,
        mouseDist: 300
    };

    // Mouse Tracking
    let mouse = { x: null, y: null };
    window.addEventListener('mousemove', (e) => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
    });

    function resize() {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resize);
    resize();



    // --- SMALL PARTICLE (Dot) ---
    class Particle {
        constructor() {
            this.init();
        }

        init() {
            this.radius = Math.random() * 4 + 2; // Small: 2-6px
            this.x = Math.random() * width;
            this.y = Math.random() * height;
            this.dx = (Math.random() - 0.5) * 0.8; // Faster
            this.dy = (Math.random() - 0.5) * 0.8;
            this.color = colors[Math.floor(Math.random() * colors.length)];
        }

        update() {
            this.x += this.dx;
            this.y += this.dy;

            if (this.x < 0) this.x = width;
            if (this.x > width) this.x = 0;
            if (this.y < 0) this.y = height;
            if (this.y > height) this.y = 0;

            // Mouse Interaction
            if (mouse.x != null) {
                const dx = this.x - mouse.x;
                const dy = this.y - mouse.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < 200) {
                    const force = (200 - dist) / 200;
                    this.x += (dx / dist) * force * 2.0;
                    this.y += (dy / dist) * force * 2.0;
                }
            }
        }

        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = this.color;
            ctx.globalAlpha = 0.6;
            ctx.fill();
        }
    }

    // Init Population

    for (let i = 0; i < config.numParticles; i++) particles.push(new Particle());

    let lastFrame = 0;
    const FPS_INTERVAL = 1000 / 30; // Cap at 30fps

    function animate(time) {
        if (document.hidden) return;

        const delta = time - lastFrame;
        if (delta < FPS_INTERVAL) {
            requestAnimationFrame(animate);
            return;
        }
        lastFrame = time - (delta % FPS_INTERVAL);

        ctx.clearRect(0, 0, width, height);

        // Update Particles
        particles.forEach(p => p.update());

        // --- DRAW CONNECTIONS ---
        ctx.save();
        ctx.globalAlpha = 1;
        ctx.lineCap = 'round';

        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const dist = dx * dx + dy * dy; // skip sqrt
                if (dist < config.connectionDist * config.connectionDist) {
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.lineWidth = 1;
                    ctx.strokeStyle = 'rgba(0,0,0,0.1)';
                    ctx.stroke();
                }
            }
        }
        ctx.restore();

        particles.forEach(p => p.draw());

        requestAnimationFrame(animate);
    }
    animate(0);
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) requestAnimationFrame(animate);
    });
});
