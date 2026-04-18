/**
 * YUMEHO 3D People Scene
 * Renders cutout figures on billboard planes in a Three.js scene
 * with scroll-driven camera movement and gentle floating animation.
 */
(function () {
  'use strict';

  const CONTAINER_ID = 'yumeho-3d-scene';
  const container = document.getElementById(CONTAINER_ID);
  if (!container) return;

  /* ── Load Three.js from CDN ── */
  const script = document.createElement('script');
  script.src = 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js';
  script.onload = init;
  document.head.appendChild(script);

  function init() {
    const THREE = window.THREE;

    /* ── Scene setup ── */
    const scene = new THREE.Scene();

    const camera = new THREE.PerspectiveCamera(
      50,
      container.clientWidth / container.clientHeight,
      0.1,
      100
    );
    camera.position.set(0, 0.5, 6);

    const renderer = new THREE.WebGLRenderer({
      antialias: true,
      alpha: true,
    });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.setClearColor(0x000000, 0);
    container.appendChild(renderer.domElement);

    /* ── Lighting ── */
    const ambient = new THREE.AmbientLight(0xffffff, 0.9);
    scene.add(ambient);
    const directional = new THREE.DirectionalLight(0xffffff, 0.4);
    directional.position.set(2, 3, 5);
    scene.add(directional);

    /* ── Texture loader ── */
    const loader = new THREE.TextureLoader();
    const basePath = 'assets/img/3d/';
    const planes = [];

    const figures = [
      { file: 'person3.png',       x: -2.8, y: -0.3, z: 0.5, scale: 2.4,  ratio: 0.52 },
      { file: 'person_patient.png', x: 0,    y: -0.2, z: -0.5, scale: 2.2, ratio: 1.42 },
      { file: 'person_dev.png',     x: 2.6,  y:  0.1, z: 0.2,  scale: 1.8, ratio: 1.78 },
    ];

    figures.forEach(function (fig, i) {
      loader.load(basePath + fig.file, function (texture) {
        texture.encoding = THREE.sRGBEncoding;
        texture.minFilter = THREE.LinearFilter;

        var w = fig.scale * fig.ratio;
        var h = fig.scale;
        var geo = new THREE.PlaneGeometry(w, h);
        var mat = new THREE.MeshBasicMaterial({
          map: texture,
          transparent: true,
          side: THREE.DoubleSide,
          depthWrite: false,
        });
        var mesh = new THREE.Mesh(geo, mat);
        mesh.position.set(fig.x, fig.y, fig.z);

        // initial state - slightly transparent, offset
        mesh.material.opacity = 0;
        mesh.userData = {
          baseY: fig.y,
          baseX: fig.x,
          index: i,
          floatSpeed: 0.3 + Math.random() * 0.4,
          floatAmp: 0.03 + Math.random() * 0.02,
          revealed: false,
        };

        scene.add(mesh);
        planes.push(mesh);
      });
    });

    /* ── Floor grid (subtle) ── */
    var gridHelper = new THREE.GridHelper(12, 24, 0x0068b7, 0x0068b7);
    gridHelper.position.y = -1.5;
    gridHelper.material.opacity = 0.06;
    gridHelper.material.transparent = true;
    scene.add(gridHelper);

    /* ── Floating particles ── */
    var particleCount = 40;
    var particleGeo = new THREE.BufferGeometry();
    var positions = new Float32Array(particleCount * 3);
    var sizes = new Float32Array(particleCount);
    for (var i = 0; i < particleCount; i++) {
      positions[i * 3] = (Math.random() - 0.5) * 10;
      positions[i * 3 + 1] = (Math.random() - 0.5) * 6;
      positions[i * 3 + 2] = (Math.random() - 0.5) * 6;
      sizes[i] = Math.random() * 3 + 1;
    }
    particleGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    particleGeo.setAttribute('size', new THREE.BufferAttribute(sizes, 1));

    var particleMat = new THREE.PointsMaterial({
      color: 0x0068b7,
      size: 0.04,
      transparent: true,
      opacity: 0.3,
      sizeAttenuation: true,
    });
    var particles = new THREE.Points(particleGeo, particleMat);
    scene.add(particles);

    /* ── Mouse tracking ── */
    var mouse = { x: 0, y: 0 };
    var targetMouse = { x: 0, y: 0 };

    container.addEventListener('mousemove', function (e) {
      var rect = container.getBoundingClientRect();
      targetMouse.x = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
      targetMouse.y = -((e.clientY - rect.top) / rect.height - 0.5) * 2;
    });

    /* ── Scroll-driven reveal ── */
    var scrollProgress = 0;
    var isVisible = false;

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          isVisible = entry.isIntersecting;
        });
      },
      { threshold: 0.1 }
    );
    observer.observe(container);

    function updateScroll() {
      var rect = container.getBoundingClientRect();
      var windowH = window.innerHeight;
      var raw = 1 - rect.top / windowH;
      scrollProgress = Math.max(0, Math.min(1, raw));
    }
    window.addEventListener('scroll', updateScroll, { passive: true });
    updateScroll();

    /* ── Animation loop ── */
    var clock = new THREE.Clock();

    function animate() {
      requestAnimationFrame(animate);

      if (!isVisible) return;

      var elapsed = clock.getElapsedTime();

      // smooth mouse
      mouse.x += (targetMouse.x - mouse.x) * 0.05;
      mouse.y += (targetMouse.y - mouse.y) * 0.05;

      // camera responds to mouse
      camera.position.x = mouse.x * 0.5;
      camera.position.y = 0.5 + mouse.y * 0.3;
      camera.lookAt(0, 0, 0);

      // animate planes
      planes.forEach(function (mesh) {
        var ud = mesh.userData;

        // reveal animation based on scroll
        var revealDelay = ud.index * 0.15;
        var revealProgress = Math.max(0, Math.min(1, (scrollProgress - revealDelay) * 3));

        mesh.material.opacity = revealProgress;
        mesh.position.x = ud.baseX + (1 - revealProgress) * (ud.index === 0 ? -2 : ud.index === 2 ? 2 : 0);

        // floating
        mesh.position.y = ud.baseY + Math.sin(elapsed * ud.floatSpeed) * ud.floatAmp;

        // subtle rotation toward camera
        mesh.rotation.y = mouse.x * 0.1;
      });

      // rotate particles slowly
      particles.rotation.y = elapsed * 0.02;
      particles.rotation.x = Math.sin(elapsed * 0.01) * 0.1;

      renderer.render(scene, camera);
    }

    animate();

    /* ── Resize handler ── */
    function onResize() {
      camera.aspect = container.clientWidth / container.clientHeight;
      camera.updateProjectionMatrix();
      renderer.setSize(container.clientWidth, container.clientHeight);
    }
    window.addEventListener('resize', onResize);
  }
})();
