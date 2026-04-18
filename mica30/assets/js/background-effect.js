/**
 * MICA30 Medical Background Effect
 * ECG waveform lines + hexagonal molecular network
 */
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.createElement('canvas');
    canvas.id = 'medical-bg';
    const isSP = window.innerWidth <= 640;
    Object.assign(canvas.style, {
        position: 'fixed',
        top: '0',
        left: '0',
        width: '100%',
        height: '100%',
        zIndex: '-1',
        pointerEvents: 'none',
        opacity: isSP ? '0.12' : '0.5'
    });
    document.body.prepend(canvas);

    const ctx = canvas.getContext('2d');
    let width, height;

    const palette = {
        primary: '#005f73',
        accent1: '#0a9396',
        accent2: '#94d2bd',
        accent3: '#e9d8a6',
        line: 'rgba(0, 95, 115, 0.08)',
        ecg: 'rgba(0, 95, 115, 0.12)'
    };

    let mouse = { x: null, y: null };
    window.addEventListener('mousemove', (e) => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
    });

    function resize() {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
        initECGLines();
        initHexNodes();
    }
    window.addEventListener('resize', resize);

    /* ═══════════════════════════════════════════
       ECG WAVEFORM LINES
       ═══════════════════════════════════════════ */
    const ecgLines = [];
    const numECGLines = 3;

    function generateECGPath(lineWidth) {
        // Generate a repeating ECG-like waveform pattern
        const points = [];
        const segmentWidth = 120;
        const numSegments = Math.ceil(lineWidth / segmentWidth) + 2;

        for (let i = 0; i < numSegments; i++) {
            const baseX = i * segmentWidth;
            // Flat line
            points.push({ x: baseX, y: 0 });
            points.push({ x: baseX + segmentWidth * 0.35, y: 0 });
            // Small P-wave bump
            points.push({ x: baseX + segmentWidth * 0.38, y: -4 });
            points.push({ x: baseX + segmentWidth * 0.42, y: 0 });
            // QRS complex - sharp spike
            points.push({ x: baseX + segmentWidth * 0.46, y: 3 });
            points.push({ x: baseX + segmentWidth * 0.48, y: -22 });
            points.push({ x: baseX + segmentWidth * 0.51, y: 8 });
            points.push({ x: baseX + segmentWidth * 0.54, y: 0 });
            // T-wave
            points.push({ x: baseX + segmentWidth * 0.62, y: 0 });
            points.push({ x: baseX + segmentWidth * 0.68, y: -6 });
            points.push({ x: baseX + segmentWidth * 0.75, y: 0 });
            // Flat
            points.push({ x: baseX + segmentWidth, y: 0 });
        }
        return points;
    }

    function initECGLines() {
        ecgLines.length = 0;
        for (let i = 0; i < numECGLines; i++) {
            ecgLines.push({
                y: height * (0.25 + i * 0.25),
                offsetX: 0,
                speed: 0.3 + Math.random() * 0.2,
                amplitude: 0.6 + Math.random() * 0.4,
                opacity: 0.06 + Math.random() * 0.06,
                path: generateECGPath(width + 300)
            });
        }
    }

    function drawECGLines() {
        ecgLines.forEach(line => {
            line.offsetX -= line.speed;
            if (line.offsetX < -120) line.offsetX += 120;

            ctx.save();
            ctx.translate(line.offsetX, line.y);
            ctx.beginPath();
            const pts = line.path;
            ctx.moveTo(pts[0].x, pts[0].y * line.amplitude);
            for (let i = 1; i < pts.length; i++) {
                ctx.lineTo(pts[i].x, pts[i].y * line.amplitude);
            }
            ctx.strokeStyle = palette.primary;
            ctx.globalAlpha = line.opacity;
            ctx.lineWidth = 1.5;
            ctx.stroke();
            ctx.restore();
        });
    }

    /* ═══════════════════════════════════════════
       HEXAGONAL MOLECULAR NETWORK
       ═══════════════════════════════════════════ */
    const hexNodes = [];
    const numNodes = isSP ? 12 : 30;
    const connectionDist = isSP ? 100 : 160;

    class HexNode {
        constructor() {
            this.x = Math.random() * width;
            this.y = Math.random() * height;
            this.dx = (Math.random() - 0.5) * 0.4;
            this.dy = (Math.random() - 0.5) * 0.4;
            this.radius = Math.random() * 3 + 2;
            this.hexSize = Math.random() * 8 + 5;
            this.isHex = Math.random() > 0.5;
            this.rotation = Math.random() * Math.PI;
            this.rotSpeed = (Math.random() - 0.5) * 0.003;
            const c = [palette.primary, palette.accent1, palette.accent2];
            this.color = c[Math.floor(Math.random() * c.length)];
            this.pulsePhase = Math.random() * Math.PI * 2;
        }

        update(time) {
            this.x += this.dx;
            this.y += this.dy;
            this.rotation += this.rotSpeed;

            if (this.x < -20) this.x = width + 20;
            if (this.x > width + 20) this.x = -20;
            if (this.y < -20) this.y = height + 20;
            if (this.y > height + 20) this.y = -20;

            // Mouse repulsion
            if (mouse.x != null) {
                const dx = this.x - mouse.x;
                const dy = this.y - mouse.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < 180) {
                    const force = (180 - dist) / 180;
                    this.x += (dx / dist) * force * 1.5;
                    this.y += (dy / dist) * force * 1.5;
                }
            }

            this.pulse = 0.5 + 0.5 * Math.sin(time * 0.001 + this.pulsePhase);
        }

        draw() {
            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate(this.rotation);
            ctx.globalAlpha = 0.35 + this.pulse * 0.25;

            if (this.isHex) {
                // Draw hexagon
                ctx.beginPath();
                for (let i = 0; i < 6; i++) {
                    const angle = (Math.PI / 3) * i;
                    const px = this.hexSize * Math.cos(angle);
                    const py = this.hexSize * Math.sin(angle);
                    if (i === 0) ctx.moveTo(px, py);
                    else ctx.lineTo(px, py);
                }
                ctx.closePath();
                ctx.strokeStyle = this.color;
                ctx.lineWidth = 1;
                ctx.stroke();
            } else {
                // Draw circle (atom)
                ctx.beginPath();
                ctx.arc(0, 0, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();
            }

            ctx.restore();
        }
    }

    function initHexNodes() {
        hexNodes.length = 0;
        for (let i = 0; i < numNodes; i++) {
            hexNodes.push(new HexNode());
        }
    }

    function drawConnections() {
        ctx.save();
        ctx.lineCap = 'round';

        for (let i = 0; i < hexNodes.length; i++) {
            for (let j = i + 1; j < hexNodes.length; j++) {
                const dx = hexNodes[i].x - hexNodes[j].x;
                const dy = hexNodes[i].y - hexNodes[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < connectionDist) {
                    const alpha = (1 - dist / connectionDist) * 0.12;
                    ctx.beginPath();
                    ctx.moveTo(hexNodes[i].x, hexNodes[i].y);
                    ctx.lineTo(hexNodes[j].x, hexNodes[j].y);
                    ctx.lineWidth = 1;
                    ctx.strokeStyle = palette.primary;
                    ctx.globalAlpha = alpha;
                    ctx.stroke();
                }
            }
        }

        ctx.restore();
    }

    /* ═══════════════════════════════════════════
       CROSS-HAIR GRID (subtle)
       ═══════════════════════════════════════════ */
    function drawGrid() {
        ctx.save();
        ctx.globalAlpha = 0.025;
        ctx.strokeStyle = palette.primary;
        ctx.lineWidth = 0.5;

        // Vertical lines
        for (let x = 0; x < width; x += 80) {
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, height);
            ctx.stroke();
        }
        // Horizontal lines
        for (let y = 0; y < height; y += 80) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(width, y);
            ctx.stroke();
        }
        ctx.restore();
    }

    /* ═══════════════════════════════════════════
       ANIMATION LOOP
       ═══════════════════════════════════════════ */
    resize();

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

        // Layer 1: Subtle grid (static - draw once per resize)
        drawGrid();

        // Layer 2: ECG waveforms
        drawECGLines();

        // Layer 3: Connections
        drawConnections();

        // Layer 4: Hex nodes
        hexNodes.forEach(n => {
            n.update(time);
            n.draw();
        });

        requestAnimationFrame(animate);
    }
    animate(0);
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) requestAnimationFrame(animate);
    });
});
