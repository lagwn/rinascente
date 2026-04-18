<?php
/**
 * Front Page Template
 *
 * @package Rinascente
 */

get_header();

$mica30_enabled = function_exists( 'rinascente_mica30_enabled' ) && rinascente_mica30_enabled();
?>

  <main>

    <!-- ============ HERO ============ -->
    <section class="hero-dark" style="position:relative;">
      <canvas id="mvBgCanvas" style="
        position:absolute;
        inset:0;
        width:100%;
        height:100%;
        pointer-events:none;
        z-index:0;
        opacity:0.28;
      "></canvas>
      <canvas id="cellCanvas" style="
        position:absolute;
        inset:0;
        width:100%;
        height:100%;
        pointer-events:none;
        z-index:1;
        mix-blend-mode:screen;
        opacity:0.7;
      "></canvas>
      <canvas id="glitchCanvas" style="
        position:absolute;
        inset:0;
        width:100%;
        height:100%;
        pointer-events:none;
        z-index:10;
      "></canvas>
      <div class="hero-bg-text" aria-hidden="true">Rinascente</div>
      <div class="scroll-indicator" aria-hidden="true">Scroll</div>

      <div class="container" style="position:relative;z-index:5;">
        <div style="max-width:800px;">
          <div id="hi-overline" class="hero-overline hero-glitch-el" style="opacity:0;">
            <span class="hero-overline-line"></span>
            <span class="hero-overline-text"><span style="white-space:nowrap;">Healthcare &amp; Beyond</span> — <span style="white-space:nowrap;"><?php echo esc_html( get_theme_mod( 'company_name', '株式会社リナシェンテ' ) ); ?></span></span>
          </div>

          <h1 id="hi-h1" class="hero-glitch-el" style="
            font-family: var(--font-display);
            font-size: clamp(3.5rem, 8vw, 8.5rem);
            font-style: italic;
            font-weight: 300;
            color: var(--white);
            line-height: 0.92;
            letter-spacing: -0.02em;
            margin-bottom: 32px;
            opacity:0;
          ">
            Rinascente
          </h1>

          <p id="hi-sub" class="hero-glitch-el" style="
            font-family: var(--font-ja);
            font-size: clamp(1rem, 2.2vw, 1.5rem);
            font-weight: 300;
            color: rgba(255,255,255,0.7);
            letter-spacing: 0.06em;
            line-height: 1.8;
            margin-bottom: 36px;
            opacity:0;
          ">
            復活する。再生する。<br>
            <span style="color:var(--gold-light);">そして、拡がっていく。</span>
          </p>

          <p id="hi-body" class="hero-body hero-glitch-el" style="opacity:0;">
            Rinascente は、医療・福祉機器の企画販売と新規事業開発を担う企業グループです。現場で積み上げた信頼と知見を土台に、より広い領域へと価値を届けていきます。
          </p>

          <div id="hi-btns" class="hero-actions hero-glitch-el" style="opacity:0;">
            <a href="<?php echo esc_url( home_url( '/identity/' ) ); ?>" class="btn btn-gold btn-lg">企業理念を見る</a>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'case_study' ) ); ?>" class="btn btn-outline-light btn-lg">導入事例</a>
          </div>

        </div>
      </div>

      <!-- ============ MINI VIDEO PANEL ============ -->
      <div id="heroVideoPanel" style="
        position:absolute;
        top: clamp(90px,10vw,120px);
        right: clamp(24px,5vw,72px);
        width: clamp(200px,22vw,320px);
        z-index:20;
        opacity:0;
        transform: translateY(-16px);
        pointer-events:auto;
      ">
        <!-- HUD gadget panel -->
        <div style="position:relative;filter:drop-shadow(0 0 18px rgba(0,180,220,0.18)) drop-shadow(0 0 40px rgba(200,169,110,0.08));">

          <!-- Outer glow ring (animated) -->
          <div style="position:absolute;inset:-3px;border-radius:10px;background:transparent;border:1px solid rgba(0,180,220,0.12);animation:hudRingPulse 3s ease-in-out infinite;pointer-events:none;z-index:3;"></div>

          <!-- Top-left corner HUD decoration -->
          <div style="position:absolute;top:-10px;left:-10px;z-index:4;pointer-events:none;">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
              <path d="M28 2 L2 2 L2 28" stroke="rgba(200,169,110,0.9)" stroke-width="1.5" fill="none"/>
              <path d="M28 2 L2 2 L2 28" stroke="rgba(0,180,220,0.3)" stroke-width="3" fill="none" stroke-dasharray="4 4"/>
            </svg>
          </div>
          <!-- Top-right corner -->
          <div style="position:absolute;top:-10px;right:-10px;z-index:4;pointer-events:none;transform:scaleX(-1);">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
              <path d="M28 2 L2 2 L2 28" stroke="rgba(200,169,110,0.9)" stroke-width="1.5" fill="none"/>
            </svg>
          </div>
          <!-- Bottom-left -->
          <div style="position:absolute;bottom:-10px;left:-10px;z-index:4;pointer-events:none;transform:scaleY(-1);">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
              <path d="M28 2 L2 2 L2 28" stroke="rgba(200,169,110,0.9)" stroke-width="1.5" fill="none"/>
            </svg>
          </div>
          <!-- Bottom-right -->
          <div style="position:absolute;bottom:-10px;right:-10px;z-index:4;pointer-events:none;transform:scale(-1);">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
              <path d="M28 2 L2 2 L2 28" stroke="rgba(200,169,110,0.9)" stroke-width="1.5" fill="none"/>
            </svg>
          </div>

          <!-- TOP STATUS BAR -->
          <div style="
            background:rgba(4,8,12,0.95);
            backdrop-filter:blur(16px);
            border:1px solid rgba(0,180,220,0.2);
            border-bottom:1px solid rgba(0,180,220,0.08);
            border-radius:6px 6px 0 0;
            padding:5px 10px;
            display:flex;align-items:center;justify-content:space-between;
          ">
            <!-- Left: Signal bars + label -->
            <div style="display:flex;align-items:center;gap:7px;">
              <div style="display:flex;align-items:flex-end;gap:2px;height:10px;">
                <div style="width:2px;height:4px;background:rgba(0,200,255,0.8);border-radius:1px;"></div>
                <div style="width:2px;height:6px;background:rgba(0,200,255,0.8);border-radius:1px;"></div>
                <div style="width:2px;height:8px;background:rgba(0,200,255,0.8);border-radius:1px;"></div>
                <div style="width:2px;height:10px;background:rgba(0,200,255,0.5);border-radius:1px;animation:signalBlink 2s ease-in-out infinite;"></div>
              </div>
              <span style="font-size:0.52rem;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:rgba(0,200,255,0.8);">PROMO</span>
              <span style="width:1px;height:10px;background:rgba(255,255,255,0.1);"></span>
              <span style="font-size:0.52rem;letter-spacing:0.15em;text-transform:uppercase;color:rgba(255,255,255,0.35);">SYS-02 / YUMEHO</span>
            </div>
            <!-- Right: status indicators -->
            <div style="display:flex;align-items:center;gap:5px;">
              <span style="width:5px;height:5px;border-radius:50%;background:rgba(0,200,120,0.9);box-shadow:0 0 5px rgba(0,200,120,0.7);display:inline-block;"></span>
              <span style="font-size:0.48rem;color:rgba(0,200,120,0.7);letter-spacing:0.1em;">ONLINE</span>
            </div>
          </div>

          <!-- TITLE BAR -->
          <div style="
            background:linear-gradient(90deg,rgba(0,20,30,0.98),rgba(4,8,12,0.95));
            border-left:1px solid rgba(0,180,220,0.2);
            border-right:1px solid rgba(0,180,220,0.2);
            padding:8px 10px 7px;
            display:flex;align-items:center;justify-content:space-between;
            position:relative;overflow:hidden;
          ">
            <!-- Animated teal accent line -->
            <div style="position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(0,180,220,0.6),rgba(200,169,110,0.4),transparent);animation:scanBar 4s linear infinite;"></div>

            <div style="display:flex;align-items:center;gap:8px;">
              <span id="videoPulseDot" style="
                width:7px;height:7px;border-radius:50%;display:inline-block;
                background:var(--gold);
                box-shadow:0 0 8px rgba(200,169,110,1),0 0 16px rgba(200,169,110,0.4);
                animation:videoPulse 1.4s ease-in-out infinite;
              "></span>
              <div>
                <div style="font-family:var(--font-display);font-size:0.95rem;font-style:italic;font-weight:300;color:var(--white);letter-spacing:0.06em;line-height:1;">YUMEHO</div>
                <div style="font-size:0.48rem;letter-spacing:0.18em;text-transform:uppercase;color:rgba(200,169,110,0.55);margin-top:1px;">Rehabilitation System</div>
              </div>
            </div>
            <!-- Mini data readout -->
            <div style="text-align:right;">
              <div style="font-size:0.48rem;color:rgba(0,180,220,0.5);letter-spacing:0.12em;text-transform:uppercase;">UNIT</div>
              <div style="font-family:monospace;font-size:0.65rem;color:rgba(0,200,255,0.7);letter-spacing:0.05em;">PGT-9001</div>
            </div>
          </div>

          <!-- VIDEO AREA -->
          <div class="hero-video-wrap" style="
            position:relative;background:#000;
            border-left:1px solid rgba(0,180,220,0.2);
            border-right:1px solid rgba(0,180,220,0.2);
            overflow:hidden;cursor:pointer;
          " onclick="toggleHeroVideo(this)">
            <video id="heroVideo"
              src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/movie/yumeho_short_lite.mp4"
              autoplay muted loop playsinline
              preload="metadata"
              style="width:100%;display:block;aspect-ratio:16/9;object-fit:cover;opacity:0.92;"
            ></video>

            <!-- Scanline overlay -->
            <div style="position:absolute;inset:0;background:repeating-linear-gradient(to bottom,transparent 0px,transparent 2px,rgba(0,0,0,0.1) 2px,rgba(0,0,0,0.1) 3px);pointer-events:none;z-index:1;"></div>

            <!-- Vignette -->
            <div style="position:absolute;inset:0;background:radial-gradient(ellipse at center,transparent 55%,rgba(0,0,0,0.55) 100%);pointer-events:none;z-index:1;"></div>

            <!-- Corner HUD overlays on video -->
            <div style="position:absolute;top:6px;left:6px;z-index:2;pointer-events:none;">
              <div style="font-size:0.42rem;font-family:monospace;color:rgba(0,200,255,0.55);letter-spacing:0.1em;line-height:1.6;">
                CAM:01<br>REC●
              </div>
            </div>
            <div style="position:absolute;top:6px;right:6px;z-index:2;pointer-events:none;text-align:right;">
              <div id="hudClock" style="font-size:0.42rem;font-family:monospace;color:rgba(200,169,110,0.55);letter-spacing:0.05em;"></div>
            </div>
            <div style="position:absolute;bottom:6px;left:6px;z-index:2;pointer-events:none;">
              <div style="font-size:0.4rem;font-family:monospace;color:rgba(0,200,255,0.4);letter-spacing:0.08em;">720p · STREAM</div>
            </div>
            <!-- Always-visible expand hint -->
            <div style="
              position:absolute;bottom:6px;right:6px;z-index:2;pointer-events:none;
              display:flex;align-items:center;gap:3px;
              background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);
              border:1px solid rgba(200,169,110,0.3);border-radius:3px;
              padding:2px 5px;
              animation:expandHint 2.8s ease-in-out infinite;
            ">
              <svg width="8" height="8" viewBox="0 0 10 10" fill="none">
                <path d="M1 4V1h3M6 1h3v3M9 6v3H6M4 9H1V6" stroke="rgba(200,169,110,0.85)" stroke-width="1.4" stroke-linecap="round"/>
              </svg>
              <span style="font-size:0.4rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:rgba(200,169,110,0.85);">EXPAND</span>
            </div>

            <!-- Hover play overlay -->
            <div class="hero-video-hover" style="
              position:absolute;inset:0;z-index:3;
              background:rgba(0,10,20,0.55);
              display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;
              opacity:0;transition:opacity 0.25s;pointer-events:none;
            ">
              <!-- Rotating ring -->
              <div style="position:relative;width:48px;height:48px;">
                <svg style="position:absolute;inset:0;animation:hudSpin 3s linear infinite;" width="48" height="48" viewBox="0 0 48 48" fill="none">
                  <circle cx="24" cy="24" r="22" stroke="rgba(0,180,220,0.4)" stroke-width="1" stroke-dasharray="6 4"/>
                </svg>
                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <polygon points="4,2 14,8 4,14" fill="rgba(200,169,110,0.95)"/>
                  </svg>
                </div>
              </div>
              <span style="font-size:0.58rem;letter-spacing:0.18em;text-transform:uppercase;color:rgba(200,169,110,0.8);">EXPAND VIEW</span>
            </div>

            <!-- Bottom shimmer line -->
            <div style="position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,rgba(0,180,220,0.6),rgba(200,169,110,0.4),transparent);z-index:2;"></div>
          </div>

          <!-- BOTTOM DATA BAR -->
          <div style="
            background:rgba(4,8,12,0.95);
            backdrop-filter:blur(16px);
            border:1px solid rgba(0,180,220,0.2);
            border-top:1px solid rgba(0,180,220,0.08);
            border-radius:0 0 6px 6px;
            padding:6px 10px;
            display:flex;align-items:center;justify-content:space-between;
          ">
            <!-- Progress bar (fake signal strength) -->
            <div style="display:flex;align-items:center;gap:6px;flex:1;">
              <span style="font-size:0.46rem;color:rgba(0,180,220,0.45);letter-spacing:0.12em;text-transform:uppercase;white-space:nowrap;">SIG</span>
              <div style="flex:1;height:3px;background:rgba(255,255,255,0.06);border-radius:2px;overflow:hidden;max-width:60px;">
                <div style="height:100%;width:82%;background:linear-gradient(90deg,rgba(0,180,220,0.7),rgba(200,169,110,0.6));border-radius:2px;animation:signalWave 2.5s ease-in-out infinite;"></div>
              </div>
              <span style="font-size:0.46rem;font-family:monospace;color:rgba(0,200,255,0.5);">82%</span>
            </div>
            <div style="width:1px;height:14px;background:rgba(255,255,255,0.08);margin:0 8px;"></div>
            <a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho' ) ); ?>" style="
              font-size:0.52rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;
              color:rgba(200,169,110,0.7);white-space:nowrap;
              padding:3px 8px;border:1px solid rgba(200,169,110,0.2);border-radius:2px;
              transition:all 0.2s;
            " onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)';this.style.background='rgba(200,169,110,0.08)'" onmouseout="this.style.borderColor='rgba(200,169,110,0.2)';this.style.color='rgba(200,169,110,0.7)';this.style.background='transparent'">OPEN →</a>
          </div>

        </div>
      </div>

    </section>

    <!-- ============ MARQUEE ============ -->
    <div class="marquee-wrap" aria-hidden="true">
      <div class="marquee-track">
        <span class="marquee-item">Rehabilitation</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item ja">復活する</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item accent">Rinascente</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item">Medical Imaging</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item ja">再生する</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item">Healthcare</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item ja">拡がっていく</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item">Welfare</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item accent">Rinascente</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item">Innovation</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item ja">復活する</span>
        <span class="marquee-sep"></span>
        <!-- duplicate for seamless loop -->
        <span class="marquee-item">Rehabilitation</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item ja">復活する</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item accent">Rinascente</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item">Medical Imaging</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item ja">再生する</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item">Healthcare</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item ja">拡がっていく</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item">Welfare</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item accent">Rinascente</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item">Innovation</span>
        <span class="marquee-sep"></span>
        <span class="marquee-item ja">復活する</span>
        <span class="marquee-sep"></span>
      </div>
    </div>

    <!-- SP Mini Video Panel (outside hero) -->
    <div class="sp-video-section">
      <div class="container">
        <div class="sp-video-panel">
          <div class="sp-video-panel__header">
            <div style="display:flex;align-items:center;gap:8px;">
              <span class="sp-video-panel__dot"></span>
              <div>
                <div style="font-family:var(--font-display);font-size:0.95rem;font-style:italic;font-weight:300;color:var(--white);letter-spacing:0.06em;line-height:1;">YUMEHO</div>
                <div style="font-size:0.45rem;letter-spacing:0.18em;text-transform:uppercase;color:rgba(200,169,110,0.55);margin-top:1px;">Rehabilitation System</div>
              </div>
            </div>
            <a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho' ) ); ?>" class="sp-video-panel__link">詳細 →</a>
          </div>
          <div class="sp-video-panel__video" onclick="toggleHeroVideo(this)" style="position:relative;cursor:pointer;">
            <video
              src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/movie/yumeho_short_lite.mp4"
              autoplay muted loop playsinline
              preload="metadata"
              style="width:100%;display:block;aspect-ratio:16/9;object-fit:cover;"
            ></video>
            <div class="sp-video-panel__scanline" style="position:absolute;inset:0;pointer-events:none;"></div>
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;z-index:5;">
              <div style="width:40px;height:40px;border-radius:50%;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;border:1px solid rgba(200,169,110,0.6);box-shadow:0 0 16px rgba(0,0,0,0.3);">
                <svg width="12" height="14" viewBox="0 0 14 16" fill="none" style="margin-left:2px;">
                  <path d="M1 1.5v13l12-6.5L1 1.5z" fill="rgba(200,169,110,1)"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ============ 01 MISSION ============ -->
    <section class="section bg-dark" id="mission">
      <div class="container">

        <div class="sec-intro fade-up">
          <div class="sec-intro-left">
            <span class="sec-num" aria-hidden="true">01</span>
            <div class="sec-intro-title">
              <span class="sec-intro-label">Mission</span>
              <h2 class="sec-intro-h">現場の課題を、<br><em style="font-family:'dnp-shuei-gothic-gin-std',var(--font-ja);font-style:normal;font-weight:700;color:var(--gold-light);">人の力で解く。</em></h2>
            </div>
          </div>
          <p class="sec-intro-sub">
            医療や福祉の現場には、まだ多くの課題が残っています。転倒への恐怖、処置の煩雑さ、スタッフの過負担。Rinascente は、テクノロジーと人の知恵を組み合わせ、現場で本当に役立つソリューションを届けます。
          </p>
        </div>

        <div class="mission-grid fade-up d-200">
          <div style="padding:clamp(28px,4vw,44px);background:rgba(255,255,255,0.03);">
            <div style="font-size:0.68rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:var(--gold);margin-bottom:14px;">Vision</div>
            <p style="font-family:'dnp-shuei-gothic-gin-std',var(--font-ja);font-size:clamp(1.15rem,1.8vw,1.6rem);font-style:normal;font-weight:700;color:var(--white);line-height:1.35;margin-bottom:12px;">人が、何度でも<br>立ち上がれる世界へ。</p>
            <p style="font-size:0.86rem;color:rgba(255,255,255,0.5);line-height:1.7;">復活は一度きりではなく、何度でも。再生は終わりではなく、始まりです。</p>
          </div>
          <div style="padding:clamp(28px,4vw,44px);background:rgba(255,255,255,0.02);">
            <div style="font-size:0.68rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:var(--gold);margin-bottom:14px;">Mission</div>
            <p style="font-family:'dnp-shuei-gothic-gin-std',var(--font-ja);font-size:clamp(1.15rem,1.8vw,1.6rem);font-style:normal;font-weight:700;color:var(--white);line-height:1.35;margin-bottom:12px;">現場の声から、<br>ソリューションを。</p>
            <p style="font-size:0.86rem;color:rgba(255,255,255,0.5);line-height:1.7;">患者・スタッフ・家族——すべてのステークホルダーの立場を深く理解し続けます。</p>
          </div>
          <div style="padding:clamp(28px,4vw,44px);background:rgba(200,169,110,0.04);">
            <div style="font-size:0.68rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:var(--gold);margin-bottom:14px;">Values</div>
            <p style="font-family:'dnp-shuei-gothic-gin-std',var(--font-ja);font-size:clamp(1.15rem,1.8vw,1.6rem);font-style:normal;font-weight:700;color:var(--white);line-height:1.35;margin-bottom:12px;">誠実さと洗練を、<br>すべての接点に。</p>
            <p style="font-size:0.86rem;color:rgba(255,255,255,0.5);line-height:1.7;">妥協のない品質、透明性、そして審美的な体験。これが Rinascente の根幹です。</p>
            <a href="<?php echo esc_url( home_url( '/identity/' ) ); ?>" style="display:inline-flex;align-items:center;gap:8px;margin-top:16px;font-size:0.78rem;font-weight:700;color:var(--gold);">詳細を見る →</a>
          </div>
        </div>

      </div>
    </section>

    <!-- ============ 02 INNOVATIONS (SLIDER) ============ -->
    <section class="innovation-section" id="products">

      <div class="innovation-header">
        <div class="container">
          <div class="sec-intro fade-up" style="margin-bottom:0;">
            <div class="sec-intro-left">
              <span class="sec-num" aria-hidden="true">02</span>
              <div class="sec-intro-title">
                <span class="sec-intro-label">Products</span>
                <h2 class="sec-intro-h">製品ラインナップ</h2>
              </div>
            </div>
            <p class="sec-intro-sub">医療・福祉現場の課題に向き合う2つのプロダクト。いずれも国内医療機器認証を取得し、現場での実績を積み上げています。</p>
          </div>
        </div>
      </div>

      <!-- Horizontal slider -->
      <div class="innovation-slider">
        <div class="innovation-track">

          <!-- YUMEHO -->
          <div class="innovation-card">
            <div class="innovation-card-header" style="background:#001f3f;">
              <img
                src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/yumeho.webp"
                alt=""
                aria-hidden="true"
                loading="lazy"
                style="
                  position:absolute;
                  inset:0;
                  width:100%;
                  height:100%;
                  object-fit:cover;
                  object-position:center 30%;
                  opacity:0.55;
                  pointer-events:none;
                "
              >
              <span class="innovation-card-bg-num" aria-hidden="true" style="position:relative;z-index:1;">01</span>
              <div style="position:relative;z-index:1;">
                <div class="innovation-card-product">YUMEHO</div>
                <div class="innovation-card-subtitle" style="color:rgba(100,180,255,0.8);">歩行支援リハビリシステム</div>
              </div>
            </div>
            <div class="innovation-card-body">
              <p class="innovation-card-desc">転倒リスクを物理的に排除しながら、両手フリーのプレイ型リハビリを実現。見守り1名体制での安全な運用で、歩行訓練機会を最大化します。回復期病棟から老健施設まで幅広く導入実績があります。</p>
              <div class="innovation-card-specs">
                <span class="badge badge-blue">デュアルレール</span>
                <span class="badge badge-blue">G-Suit</span>
                <span class="badge badge-blue">天井型 / スタンド型</span>
                <span class="badge badge-blue">プレイ型リハビリ</span>
                <span class="badge badge-blue">1名見守り対応</span>
              </div>
              <div class="innovation-card-footer">
                <a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho' ) ); ?>" class="innovation-card-link">YUMEHOサイトへ →</a>
                <span class="innovation-card-cert">医療機器認証取得済み</span>
              </div>
            </div>
          </div>

          <?php if ( $mica30_enabled ) : ?>
          <!-- MICA30 -->
          <div class="innovation-card">
            <div class="innovation-card-header" style="background:#001a1f;">
              <img
                src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/mica30.webp"
                alt=""
                aria-hidden="true"
                loading="lazy"
                style="
                  position:absolute;
                  inset:0;
                  width:100%;
                  height:100%;
                  object-fit:cover;
                  object-position:center 40%;
                  opacity:0.55;
                  pointer-events:none;
                "
              >
              <span class="innovation-card-bg-num" aria-hidden="true" style="position:relative;z-index:1;">02</span>
              <div style="position:relative;z-index:1;">
                <div class="innovation-card-product">MICA30</div>
                <div class="innovation-card-subtitle" style="color:rgba(100,210,220,0.8);">多相電動式造影剤注入装置</div>
              </div>
            </div>
            <div class="innovation-card-body">
              <p class="innovation-card-desc">血管造影・CT検査のための造影剤を精密に自動注入する医療機器。2系統の気泡センサーと0.05mL単位の精密制御で、高品質な画像診断をサポートします。</p>
              <div class="innovation-card-specs">
                <span class="badge badge-teal">医療機器認証取得</span>
                <span class="badge badge-teal">2系統気泡センサー</span>
                <span class="badge badge-teal">3注入モード</span>
                <span class="badge badge-teal">500psi対応</span>
                <span class="badge badge-teal">0.05mL精密制御</span>
              </div>
              <div class="innovation-card-footer">
                <a href="<?php echo esc_url( rinascente_related_site_url( 'mica30' ) ); ?>" class="innovation-card-link">MICA30サイトへ →</a>
                <span class="innovation-card-cert">認証番号：304ADBZX00064000</span>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <!-- Future product placeholder -->
          <div class="innovation-card" style="opacity:0.5;pointer-events:none;">
            <div class="innovation-card-header" style="background:linear-gradient(135deg,#1a1510,#2a2018,#3a3020);">
              <span class="innovation-card-bg-num" aria-hidden="true">03</span>
              <div>
                <div class="innovation-card-product" style="color:rgba(255,255,255,0.5);">Coming Soon</div>
                <div class="innovation-card-subtitle" style="color:rgba(255,255,255,0.3);">新製品 / 新領域</div>
              </div>
            </div>
            <div class="innovation-card-body">
              <p class="innovation-card-desc" style="color:rgba(255,255,255,0.3);">Rinascente は、医療・福祉機器の枠を超え、旅行業やウェルネス領域への事業拡張を構想しています。新しい「復活と再生」のソリューションを準備中です。</p>
              <div class="innovation-card-specs">
                <span class="badge" style="background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.25);border:1px solid rgba(255,255,255,0.08);">Travel &amp; Wellness</span>
                <span class="badge" style="background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.25);border:1px solid rgba(255,255,255,0.08);">HealthTourism</span>
              </div>
              <div class="innovation-card-footer">
                <span style="font-size:0.78rem;color:rgba(255,255,255,0.2);">開発・調査中</span>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="slider-hint">
        <svg class="slider-hint-icon" width="22" height="16" viewBox="0 0 22 16" fill="none">
          <rect x="1" y="1" width="20" height="14" rx="7" stroke="rgba(200,169,110,0.4)" stroke-width="1"/>
          <circle cx="8" cy="8" r="3" fill="rgba(200,169,110,0.7)">
            <animate attributeName="cx" values="8;14;8" dur="1.6s" repeatCount="indefinite" calcMode="spline" keySplines="0.4 0 0.2 1;0.4 0 0.2 1"/>
          </circle>
        </svg>
        <span class="slider-hint-text">SWIPE</span>
        <span class="slider-hint-line"></span>
        <svg class="slider-hint-arrow" width="24" height="12" viewBox="0 0 24 12" fill="none">
          <path d="M0 6H20M20 6L14 1M20 6L14 11" stroke="rgba(200,169,110,0.7)" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>

    </section>

    <!-- ============ 03 BUSINESS DOMAINS ============ -->
    <section class="section bg-dark" id="domains">
      <div class="container">

        <div class="sec-intro fade-up">
          <div class="sec-intro-left">
            <span class="sec-num" aria-hidden="true">03</span>
            <div class="sec-intro-title">
              <span class="sec-intro-label">Business Domains</span>
              <h2 class="sec-intro-h">事業領域</h2>
            </div>
          </div>
          <p class="sec-intro-sub">現在の医療・福祉を核に、拡張していく可能性の地図。「復活と再生」の理念が新しい分野にも拡がります。</p>
        </div>

        <div class="domain-grid fade-up d-100" data-stagger>
          <!-- Healthcare (現在) -->
          <div class="domain-card" style="background:linear-gradient(135deg,#001f2e,#003350,#004d73);">
            <img
              src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/domain_healthcare_1x.webp"
              srcset="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/domain_healthcare_1x.webp 1x, <?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/domain_healthcare_2x.webp 2x"
              alt="Healthcare & Welfare"
              class="domain-card-bg"
              style="object-fit: cover; object-position: center center; opacity: 0.7;"
            >
            <div class="domain-card-num">01</div>
            <div class="domain-card-title">Healthcare &amp; Welfare</div>
            <p class="domain-card-desc">医療・福祉機器の開発・販売。YUMEHO を中心に現場の課題を解決します。</p>
            <span class="badge badge-gold" style="margin-top:12px;align-self:flex-start;">現在展開中</span>
          </div>

          <!-- Travel -->
          <div class="domain-card" style="background:linear-gradient(135deg,#1a1505,#2a2210,#3a3218);">
            <img
              src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/domain_travel_1x.webp"
              srcset="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/domain_travel_1x.webp 1x, <?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/domain_travel_2x.webp 2x"
              alt="Travel & Wellness"
              class="domain-card-bg"
              style="object-fit: cover; object-position: center center; opacity: 0.7;"
            >
            <div class="domain-card-num">02</div>
            <div class="domain-card-title">Travel &amp; Wellness</div>
            <p class="domain-card-desc">旅行業やウェルネス分野への展開。医療知識を活かしたヘルスツーリズムや滞在型リハビリ旅行を構想中。</p>
            <span class="badge" style="margin-top:12px;align-self:flex-start;background:rgba(200,169,110,0.1);color:rgba(200,169,110,0.6);border:1px solid rgba(200,169,110,0.2);">調査・検討中</span>
          </div>

          <!-- New Domains -->
          <div class="domain-card" style="background:linear-gradient(135deg,#14101a,#201525,#2a1830);">
            <img
              src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/domain_new_1x.webp"
              srcset="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/domain_new_1x.webp 1x, <?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/domain_new_2x.webp 2x"
              alt="New Domains"
              class="domain-card-bg"
              style="object-fit: cover; object-position: center center; opacity: 0.7;"
            >
            <div class="domain-card-num">03</div>
            <div class="domain-card-title">New Domains</div>
            <p class="domain-card-desc">「復活・再生」の理念のもと、人の生活を豊かにする新しい分野へ継続的に挑戦していきます。</p>
            <span class="badge" style="margin-top:12px;align-self:flex-start;background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.35);border:1px solid rgba(255,255,255,0.1);">探索中</span>
          </div>
        </div>

      </div>
    </section>

    <!-- 04 LEADERSHIP / 05 HISTORY — 削除済み -->

    <!-- ============ 06 STATS ============ -->
    <section style="padding:0;background:var(--bg-dark);">
      <div class="container">
        <div class="section-rule" style="color:white;"><span class="section-rule-num">06 — Key Numbers</span></div>
        <div class="stat-grid fade-up" style="margin-bottom:var(--sp-2xl);">
          <div class="stat-item">
            <div class="stat-num" data-target="<?php echo esc_attr( $mica30_enabled ? '2' : '1' ); ?>" data-suffix="製品" data-decimals="0"><?php echo esc_html( $mica30_enabled ? '2製品' : '1製品' ); ?></div>
            <div class="stat-label">医療機器認証取得済み<br>製品ラインナップ</div>
          </div>
          <div class="stat-item">
            <div class="stat-num" data-target="1.5" data-suffix="倍" data-decimals="1">1.5倍</div>
            <div class="stat-label">YUMEHO 導入施設での<br>歩行訓練機会増加実績</div>
          </div>
          <div class="stat-item">
            <div class="stat-num" data-target="40" data-suffix="%" data-decimals="0">40%</div>
            <div class="stat-label">スタッフ負担削減<br>導入後調査結果</div>
          </div>
          <div class="stat-item">
            <div class="stat-num" data-target="<?php echo esc_attr( $mica30_enabled ? '500' : '2' ); ?>" data-suffix="<?php echo esc_attr( $mica30_enabled ? 'psi' : 'タイプ' ); ?>" data-decimals="0"><?php echo esc_html( $mica30_enabled ? '500psi' : '2タイプ' ); ?></div>
            <div class="stat-label"><?php echo $mica30_enabled ? 'MICA30 最大制限圧力<br>精密注入をサポート' : 'YUMEHO 設置方式<br>天井型 / スタンド型に対応'; ?></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ 07 PRESS ============ -->
    <section class="section bg-cream" id="press">
      <div class="container">

        <div class="sec-intro fade-up">
          <div class="sec-intro-left">
            <span class="sec-num on-light" aria-hidden="true">07</span>
            <div class="sec-intro-title">
              <span class="sec-intro-label on-light">Press</span>
              <h2 class="sec-intro-h on-light">最新ニュース</h2>
            </div>
          </div>
          <a href="<?php echo esc_url( get_post_type_archive_link( 'news' ) ); ?>" class="btn btn-outline-dark btn-sm" style="align-self:flex-end;">すべて見る</a>
        </div>

        <div class="fade-up d-100">
          <?php
          $news_query_args = array(
              'post_type'      => 'news',
              'posts_per_page' => 3,
              'orderby'        => 'date',
              'order'          => 'DESC',
          );
          if ( function_exists( 'rinascente_exclude_hidden_post_ids_from_query_args' ) ) {
              $news_query_args = rinascente_exclude_hidden_post_ids_from_query_args( $news_query_args, 'news' );
          }
          $news_query = new WP_Query( $news_query_args );

          if ( $news_query->have_posts() ) :
              while ( $news_query->have_posts() ) : $news_query->the_post();
                  $categories = get_the_terms( get_the_ID(), 'news_category' );
                  $cat_name   = $categories && ! is_wp_error( $categories ) ? $categories[0]->name : '';
                  $badge_class = 'badge badge-light';
                  if ( $cat_name === '製品情報' ) {
                      $badge_class = 'badge badge-blue';
                  } elseif ( $cat_name === '導入事例' ) {
                      $badge_class = 'badge badge-blue';
                  } elseif ( strpos( $cat_name, 'MICA' ) !== false ) {
                      $badge_class = 'badge badge-teal';
                  }
          ?>
          <a href="<?php the_permalink(); ?>" class="press-card" style="text-decoration:none;display:grid;grid-template-columns:120px 1fr;gap:20px;align-items:start;padding:24px 0;border-bottom:1px solid var(--line-light);">
            <div class="press-date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></div>
            <div>
              <?php if ( $cat_name ) : ?>
              <span class="<?php echo esc_attr( $badge_class ); ?> press-tag" style="margin-bottom:8px;"><?php echo esc_html( $cat_name ); ?></span>
              <?php endif; ?>
              <div class="press-title"><?php the_title(); ?></div>
              <?php if ( has_excerpt() ) : ?>
              <div style="font-size:0.83rem;color:var(--mid-gray);margin-top:4px;line-height:1.5;"><?php echo esc_html( get_the_excerpt() ); ?></div>
              <?php endif; ?>
            </div>
          </a>
          <?php
              endwhile;
              wp_reset_postdata();
          else : ?>
          <p style="color:var(--mid-gray);font-size:0.9rem;">ニュースはまだ投稿されていません。</p>
          <?php endif; ?>
        </div>

      </div>
    </section>

    <!-- ============ COLUMN ============ -->
    <section class="section bg-white" id="column">
      <div class="container">

        <div class="sec-intro fade-up">
          <div class="sec-intro-left">
            <span class="sec-num on-light" aria-hidden="true">08</span>
            <div class="sec-intro-title">
              <span class="sec-intro-label on-light">Column</span>
              <h2 class="sec-intro-h on-light">コラム</h2>
            </div>
          </div>
          <a href="<?php echo esc_url( get_post_type_archive_link( 'column' ) ); ?>" class="btn btn-outline-dark btn-sm sec-intro-action">すべて見る</a>
        </div>

        <div class="column-grid fade-up d-100" data-stagger>
          <?php
          $column_query = new WP_Query( array(
              'post_type'      => 'column',
              'posts_per_page' => 3,
              'orderby'        => 'date',
              'order'          => 'DESC',
          ) );

          if ( $column_query->have_posts() ) :
              while ( $column_query->have_posts() ) : $column_query->the_post();
                  $col_cats = get_the_terms( get_the_ID(), 'column_category' );
                  $col_cat_name = $col_cats && ! is_wp_error( $col_cats ) ? $col_cats[0]->name : '';
          ?>
          <a href="<?php the_permalink(); ?>" class="column-card">
            <?php if ( $col_cat_name ) : ?>
            <div class="column-card__category"><?php echo esc_html( $col_cat_name ); ?></div>
            <?php endif; ?>
            <h3 class="column-card__title"><?php the_title(); ?></h3>
            <?php if ( has_excerpt() ) : ?>
            <p class="column-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
            <?php endif; ?>
            <div class="column-card__meta">
              <span><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
              <span class="column-card__cta">続きを読む →</span>
            </div>
          </a>
          <?php
              endwhile;
              wp_reset_postdata();
          else : ?>
          <p style="color:var(--mid-gray);font-size:0.9rem;grid-column:1/-1;">コラムはまだ投稿されていません。</p>
          <?php endif; ?>
        </div>

      </div>
    </section>

    <!-- ============ 09 CASES ============ -->
    <section class="section bg-light" id="cases">
      <div class="container">

        <div class="sec-intro fade-up">
          <div class="sec-intro-left">
            <span class="sec-num on-light" aria-hidden="true">09</span>
            <div class="sec-intro-title">
              <span class="sec-intro-label on-light">Cases</span>
              <h2 class="sec-intro-h on-light">導入事例</h2>
            </div>
          </div>
          <a href="<?php echo esc_url( home_url( '/cases/' ) ); ?>" class="btn btn-outline-dark btn-sm" style="align-self:flex-end;">すべての事例を見る</a>
        </div>

        <div class="cases-grid fade-up d-100" data-stagger>
          <?php
          $shared_yumeho_cases = rinascente_fetch_shared_cases( 'yumeho', 6 );

          if ( ! empty( $shared_yumeho_cases ) ) :
              foreach ( $shared_yumeho_cases as $shared_case ) :
                  $product_color = 'mica30' === $shared_case['product_slug'] ? '#005f73' : '#0068b7';
                  ?>
          <a href="<?php echo esc_url( $shared_case['link'] ); ?>" class="case-card">
            <div class="case-card-img" style="background:linear-gradient(135deg,#001f3f,#003d7a);position:relative;overflow:hidden;">
              <img src="<?php echo esc_url( $shared_case['image_url'] ?: get_template_directory_uri() . '/assets/img/case_hospital.webp' ); ?>" alt="" loading="lazy" decoding="async" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
            </div>
            <div class="case-card-body">
              <div class="case-product-tag" style="color:<?php echo esc_attr( $product_color ); ?>;"><?php echo esc_html( $shared_case['product_name'] ); ?></div>
              <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:8px;line-height:1.45;"><?php echo esc_html( $shared_case['title'] ); ?></h3>
              <p style="font-size:0.83rem;color:var(--mid-gray);margin-bottom:12px;"><?php echo esc_html( $shared_case['excerpt'] ); ?></p>
              <div style="display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:0.8rem;font-weight:700;color:var(--charcoal);border-bottom:1px solid currentColor;">詳細を見る</span>
                <?php if ( $shared_case['facility'] ) : ?>
                <span style="font-size:0.72rem;color:var(--mid-gray);"><?php echo esc_html( $shared_case['facility'] ); ?></span>
                <?php endif; ?>
              </div>
            </div>
          </a>
                  <?php
              endforeach;
          else :
              $home_case_query_args = array(
                  'post_type'      => 'case_study',
                  'posts_per_page' => 6,
                  'orderby'        => array(
                      'menu_order' => 'ASC',
                      'date'       => 'DESC',
                  ),
              );
              if ( function_exists( 'rinascente_exclude_hidden_post_ids_from_query_args' ) ) {
                  $home_case_query_args = rinascente_exclude_hidden_post_ids_from_query_args( $home_case_query_args, 'case_study' );
              }
              $home_case_query = new WP_Query( $home_case_query_args );

              if ( $home_case_query->have_posts() ) :
                  while ( $home_case_query->have_posts() ) :
                      $home_case_query->the_post();
                      ?>
          <a href="<?php the_permalink(); ?>" class="case-card">
            <div class="case-card-img" style="background:linear-gradient(135deg,#001f3f,#003d7a);position:relative;overflow:hidden;">
              <?php if ( has_post_thumbnail() ) : ?>
              <?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'style' => 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover;' ) ); ?>
              <?php else : ?>
              <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/case_hospital.webp' ); ?>" alt="" loading="lazy" decoding="async" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
              <?php endif; ?>
            </div>
            <div class="case-card-body">
              <div class="case-product-tag" style="color:#0068b7;">YUMEHO</div>
              <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:8px;line-height:1.45;"><?php the_title(); ?></h3>
              <p style="font-size:0.83rem;color:var(--mid-gray);margin-bottom:12px;"><?php echo esc_html( get_the_excerpt() ); ?></p>
              <div style="display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:0.8rem;font-weight:700;color:var(--charcoal);border-bottom:1px solid currentColor;">詳細を見る</span>
              </div>
            </div>
          </a>
                      <?php
                  endwhile;
                  wp_reset_postdata();
              endif;
          endif;
          ?>
          <?php if ( $mica30_enabled ) : ?>
          <a href="<?php echo esc_url( rinascente_related_site_url( 'mica30' ) ); ?>" class="case-card">
            <div class="case-card-img" style="background:linear-gradient(135deg,#001a1f,#003040);position:relative;overflow:hidden;">
              <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/case_03.webp' ); ?>" alt="" loading="lazy" decoding="async" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
            </div>
            <div class="case-card-body">
              <div class="case-product-tag" style="color:#005f73;">MICA30</div>
              <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:8px;line-height:1.45;">循環器科カテーテル室への MICA30 導入</h3>
              <p style="font-size:0.83rem;color:var(--mid-gray);margin-bottom:12px;">バリアブルモードによる速度リアルタイム変更で、複雑なインターベンションに対応。</p>
              <div style="display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:0.8rem;font-weight:700;color:var(--charcoal);border-bottom:1px solid currentColor;">詳細を見る</span>
                <span style="font-size:0.72rem;color:var(--mid-gray);">循環器科</span>
              </div>
            </div>
          </a>
          <?php endif; ?>
        </div>

      </div>
    </section>

    <!-- ============ CTA ============ -->
    <section class="section bg-dark">
      <div class="container">
        <div class="cta-grid fade-up">
          <div>
            <span class="label" style="color:var(--gold);">Get in Touch</span>
            <span class="gold-line" style="margin:16px 0 24px;"></span>
            <h2 style="
              font-family: 'dnp-shuei-gothic-gin-std', var(--font-ja);
              font-size: clamp(1.7rem, 3.5vw, 3.5rem);
              font-style: normal;
              font-weight: 700;
              color: var(--white);
              line-height: 1.2;
              margin-bottom: 0;
            ">お気軽に<br><span style="color:var(--gold-light);">ご相談ください。</span></h2>
          </div>
          <div>
            <p style="color:rgba(255,255,255,0.6);margin-bottom:28px;line-height:1.75;">
              製品のご紹介から導入相談、資料請求、パートナーシップのご提案まで、幅広くお受けしています。
            </p>
            <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:32px;">
              <div style="display:flex;align-items:center;gap:8px;font-size:0.82rem;color:rgba(255,255,255,0.5);">
                <span style="width:6px;height:6px;border-radius:50%;background:var(--gold);flex-shrink:0;"></span>
                製品デモ・見学
              </div>
              <div style="display:flex;align-items:center;gap:8px;font-size:0.82rem;color:rgba(255,255,255,0.5);">
                <span style="width:6px;height:6px;border-radius:50%;background:var(--gold);flex-shrink:0;"></span>
                資料・カタログ請求
              </div>
              <div style="display:flex;align-items:center;gap:8px;font-size:0.82rem;color:rgba(255,255,255,0.5);">
                <span style="width:6px;height:6px;border-radius:50%;background:var(--gold);flex-shrink:0;"></span>
                補助金申請サポート
              </div>
              <div style="display:flex;align-items:center;gap:8px;font-size:0.82rem;color:rgba(255,255,255,0.5);">
                <span style="width:6px;height:6px;border-radius:50%;background:var(--gold);flex-shrink:0;"></span>
                パートナーシップ相談
              </div>
            </div>
            <div class="hero-actions">
              <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-gold btn-lg">お問い合わせ</a>
              <a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="btn btn-outline-light btn-lg">会員ログイン</a>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- ============ VIDEO MODAL ============ -->
  <div id="videoModal" role="dialog" aria-modal="true" aria-label="YUMEHO 製品動画">
    <div id="videoModalInner">
      <!-- Brackets -->
      <div class="modal-bracket-tl"></div>
      <div class="modal-bracket-tr"></div>
      <div class="modal-bracket-bl"></div>
      <div class="modal-bracket-br"></div>

      <!-- Close button -->
      <button id="videoModalClose" aria-label="閉じる" onclick="closeVideoModal()">✕</button>

      <!-- Header -->
      <div style="
        background:rgba(8,8,8,0.9);
        border:1px solid rgba(200,169,110,0.3);
        border-bottom:none;
        border-radius:8px 8px 0 0;
        padding:12px 20px;
        display:flex;align-items:center;justify-content:space-between;
      " class="video-modal-header">
        <div class="video-modal-brand" style="display:flex;align-items:center;gap:10px;">
          <span style="width:7px;height:7px;border-radius:50%;background:var(--gold);box-shadow:0 0 8px rgba(200,169,110,0.9);display:inline-block;animation:videoPulse 1.4s ease-in-out infinite;"></span>
          <span style="font-family:var(--font-display);font-size:1.1rem;font-style:italic;font-weight:300;color:var(--white);letter-spacing:0.06em;">YUMEHO</span>
          <span class="video-modal-system-label" style="font-size:0.65rem;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:rgba(200,169,110,0.6);">Rehabilitation System</span>
        </div>
        <a class="video-modal-link" href="<?php echo esc_url( rinascente_related_site_url( 'yumeho' ) ); ?>" style="display:inline-flex;align-items:center;justify-content:center;gap:0.45em;white-space:nowrap;flex-shrink:0;font-size:0.72rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:rgba(200,169,110,0.7);padding:5px 14px;border:1px solid rgba(200,169,110,0.25);border-radius:999px;transition:all 0.2s;" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)'" onmouseout="this.style.borderColor='rgba(200,169,110,0.25)';this.style.color='rgba(200,169,110,0.7)'"><span>製品サイトへ</span><span style="line-height:1;flex-shrink:0;">→</span></a>
      </div>

      <!-- Video -->
      <div style="position:relative;background:#000;border:1px solid rgba(200,169,110,0.3);border-top:none;border-radius:0 0 8px 8px;overflow:hidden;">
        <video id="videoModalVideo"
          src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/movie/yumeho_long_lite.mp4"
          controls playsinline
          preload="none"
          style="width:100%;display:block;aspect-ratio:16/9;object-fit:cover;"
        ></video>
        <!-- Scanline overlay -->
        <div style="position:absolute;inset:0;background:repeating-linear-gradient(to bottom,transparent 0px,transparent 3px,rgba(0,0,0,0.06) 3px,rgba(0,0,0,0.06) 4px);pointer-events:none;"></div>
        <!-- Gold bottom line -->
        <div style="position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent);opacity:0.5;pointer-events:none;"></div>
      </div>

      <!-- Footer info -->
      <div style="margin-top:12px;display:flex;align-items:center;justify-content:space-between;padding:0 4px;">
        <span style="font-size:0.65rem;color:rgba(255,255,255,0.25);letter-spacing:0.1em;text-transform:uppercase;">歩行支援リハビリシステム / 医療機器認証取得済み</span>
        <span style="font-size:0.65rem;color:rgba(200,169,110,0.4);letter-spacing:0.1em;">Rinascente Group</span>
      </div>
    </div>
  </div>

  <style>
  /* ---- Video Modal ---- */
  #videoModal {
    position:fixed;inset:0;z-index:1000;
    display:flex;align-items:center;justify-content:center;
    background:rgba(0,0,0,0);
    backdrop-filter:blur(0px);
    opacity:0;pointer-events:none;
    transition:background 0.4s, opacity 0.4s, backdrop-filter 0.4s;
  }
  #videoModal.open {
    background:rgba(0,0,0,0.88);
    backdrop-filter:blur(18px);
    opacity:1;pointer-events:auto;
  }
  #videoModalInner {
    position:relative;
    width:min(900px, 92vw);
    transform:scale(0.88) translateY(24px);
    transition:transform 0.45s cubic-bezier(0.22,1,0.36,1);
  }
  #videoModal.open #videoModalInner {
    transform:scale(1) translateY(0);
  }
  #videoModalClose {
    position:absolute;
    top:0;
    right:0;
    transform:translate(30%, -148%);
    z-index:4;
    background:rgba(8,8,8,0.72);border:1px solid rgba(255,255,255,0.2);
    border-radius:50%;width:36px;height:36px;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;color:rgba(255,255,255,0.7);font-size:1rem;
    transition:all 0.2s;
  }
  #videoModalClose:hover { border-color:var(--gold);color:var(--gold); }
  /* Corner brackets on modal */
  .modal-bracket-tl { position:absolute;top:-8px;left:-8px;width:20px;height:20px;border-top:1.5px solid var(--gold);border-left:1.5px solid var(--gold); }
  .modal-bracket-tr { position:absolute;top:-8px;right:-8px;width:20px;height:20px;border-top:1.5px solid var(--gold);border-right:1.5px solid var(--gold); }
  .modal-bracket-bl { position:absolute;bottom:-8px;left:-8px;width:20px;height:20px;border-bottom:1.5px solid var(--gold);border-left:1.5px solid var(--gold); }
  .modal-bracket-br { position:absolute;bottom:-8px;right:-8px;width:20px;height:20px;border-bottom:1.5px solid var(--gold);border-right:1.5px solid var(--gold); }

  @keyframes hudRingPulse {
    0%,100% { opacity:0.6; box-shadow:0 0 0 0 rgba(0,180,220,0); }
    50%      { opacity:1;   box-shadow:0 0 12px 2px rgba(0,180,220,0.1); }
  }
  @keyframes signalBlink {
    0%,100% { opacity:1; }
    50%     { opacity:0.2; }
  }
  @keyframes scanBar {
    0%   { transform:translateX(-100%); }
    100% { transform:translateX(200%); }
  }
  @keyframes signalWave {
    0%,100% { width:82%; opacity:1; }
    50%     { width:68%; opacity:0.7; }
  }
  @keyframes expandHint {
    0%,100% { opacity:0.6; border-color:rgba(200,169,110,0.3); }
    50%     { opacity:1;   border-color:rgba(200,169,110,0.7); box-shadow:0 0 6px rgba(200,169,110,0.2); }
  }
  @keyframes hudSpin {
    from { transform:rotate(0deg); }
    to   { transform:rotate(360deg); }
  }
  @keyframes videoPulse {
    0%, 100% { opacity:1; box-shadow:0 0 6px rgba(200,169,110,0.8); }
    50%       { opacity:0.3; box-shadow:0 0 2px rgba(200,169,110,0.2); }
  }
  @keyframes videoPanelIn {
    0%   { opacity:0; transform:translateY(-20px) scaleY(0.92); clip-path:inset(0 0 100% 0); filter:brightness(2); }
    30%  { opacity:0.7; clip-path:inset(0 0 40% 0); filter:brightness(1.5); }
    60%  { opacity:0.9; clip-path:inset(0 0 10% 0); filter:brightness(1.1); transform:translateY(2px) scaleY(1.01); }
    100% { opacity:1; transform:translateY(0) scaleY(1); clip-path:inset(0 0 0% 0); filter:brightness(1); }
  }
  .hero-video-wrap:hover .hero-video-hover { opacity:1 !important; }
  #heroVideoPanel { transform-origin: top right; }
  @keyframes glitchReveal {
    0%   { opacity:0; clip-path:inset(0 100% 0 0); transform:translateX(-6px); filter:brightness(2) blur(1px); }
    15%  { opacity:1; clip-path:inset(0 60% 0 0);  transform:translateX(4px);  filter:brightness(3) blur(0px); }
    30%  { opacity:0.6; clip-path:inset(0 30% 0 0); transform:translateX(-3px); filter:brightness(1.5); }
    50%  { opacity:1; clip-path:inset(0 10% 0 0);  transform:translateX(2px);  filter:brightness(1.2); }
    70%  { opacity:0.9; clip-path:inset(0 2% 0 0);  transform:translateX(-1px); filter:brightness(1.05); }
    85%  { opacity:1; clip-path:inset(0 0% 0 0);   transform:translateX(0.5px); filter:brightness(1); }
    100% { opacity:1; clip-path:inset(0 0% 0 0);   transform:translateX(0);    filter:brightness(1); }
  }
  @keyframes glitchFlicker {
    0%,100% { opacity:1; }
    20%  { opacity:0.3; transform:translateX(3px) skewX(1deg); filter:brightness(2); }
    40%  { opacity:1;   transform:translateX(-2px); }
    60%  { opacity:0.6; transform:translateX(2px) skewX(-0.5deg); }
    80%  { opacity:1;   transform:translateX(0); }
  }
  .hero-glitch-in {
    animation: glitchReveal 0.7s cubic-bezier(0.22,1,0.36,1) forwards;
  }
  .hero-glitch-flicker {
    animation: glitchFlicker 0.25s steps(2) forwards;
  }
  </style>

  <script>
  /* ---- Cell Morphing Canvas Animation ---- */
  (function() {
    const canvas = document.getElementById('cellCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    function resize() {
      canvas.width  = canvas.offsetWidth;
      canvas.height = canvas.offsetHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    // Simplex-like smooth noise via sin combinations
    function noise(x, y, t) {
      return (
        Math.sin(x * 1.3 + t * 0.7) * 0.3 +
        Math.sin(y * 1.1 - t * 0.5) * 0.3 +
        Math.sin((x + y) * 0.9 + t * 0.4) * 0.2 +
        Math.sin((x - y) * 0.7 - t * 0.3) * 0.2
      );
    }

    // Generate a morphing blob polygon
    function blobPoints(cx, cy, baseR, pts, t, seed) {
      const points = [];
      for (let i = 0; i < pts; i++) {
        const angle = (i / pts) * Math.PI * 2;
        const nx = Math.cos(angle + seed);
        const ny = Math.sin(angle + seed);
        const n = noise(nx * 2 + seed, ny * 2 + seed * 1.3, t);
        const r = baseR * (1 + n * 0.38);
        points.push([cx + Math.cos(angle) * r, cy + Math.sin(angle) * r]);
      }
      return points;
    }

    function drawBlob(points, color1, color2, cx, cy, r) {
      ctx.beginPath();
      const len = points.length;
      for (let i = 0; i < len; i++) {
        const [x, y] = points[i];
        const [nx, ny] = points[(i + 1) % len];
        const mx = (x + nx) / 2;
        const my = (y + ny) / 2;
        if (i === 0) ctx.moveTo(mx, my);
        else ctx.quadraticCurveTo(x, y, mx, my);
      }
      ctx.closePath();

      const grad = ctx.createRadialGradient(cx, cy, 0, cx, cy, r * 1.4);
      grad.addColorStop(0,   color1);
      grad.addColorStop(0.5, color2);
      grad.addColorStop(1,   'transparent');
      ctx.fillStyle = grad;
      ctx.fill();
    }

    // Cell definitions
    const cells = [
      // [xFrac, yFrac, radiusFrac, speed, seed, color1, color2]
      { xf:0.55, yf:0.45, rf:0.28, sp:0.32, sd:0.0,  c1:'rgba(0,140,200,0.55)',  c2:'rgba(0,80,140,0.18)'  },
      { xf:0.35, yf:0.60, rf:0.18, sp:0.45, sd:2.1,  c1:'rgba(0,160,180,0.45)',  c2:'rgba(0,100,130,0.12)' },
      { xf:0.72, yf:0.30, rf:0.14, sp:0.55, sd:4.5,  c1:'rgba(180,140,60,0.35)', c2:'rgba(140,100,30,0.10)' },
      { xf:0.20, yf:0.35, rf:0.12, sp:0.70, sd:1.3,  c1:'rgba(0,180,220,0.30)',  c2:'rgba(0,120,160,0.08)' },
      { xf:0.80, yf:0.65, rf:0.10, sp:0.90, sd:3.7,  c1:'rgba(200,160,80,0.28)', c2:'rgba(160,110,40,0.08)' },
      { xf:0.50, yf:0.75, rf:0.08, sp:1.10, sd:5.9,  c1:'rgba(0,200,240,0.22)',  c2:'rgba(0,140,180,0.06)' },
    ];

    // Mesh nodes — fine-grained particles for web/net effect
    const MESH_COUNT = 120;
    const meshNodes = Array.from({length: MESH_COUNT}, () => ({
      xf: Math.random(),
      yf: Math.random(),
      vx: (Math.random() - 0.5) * 0.00018,
      vy: (Math.random() - 0.5) * 0.00018,
      r:  Math.random() * 1.2 + 0.3,
      pulse: Math.random() * Math.PI * 2,   // phase offset for brightness pulse
      color: Math.random() > 0.6 ? '0,180,220' : Math.random() > 0.5 ? '200,160,80' : '100,210,230',
    }));

    // Glow accent particles (larger, fewer)
    const glowNodes = Array.from({length: 18}, () => ({
      xf: Math.random(), yf: Math.random(),
      vx: (Math.random() - 0.5) * 0.0002,
      vy: (Math.random() - 0.5) * 0.0002,
      r:  Math.random() * 3 + 1.5,
      alpha: Math.random() * 0.45 + 0.15,
      color: Math.random() > 0.5 ? '0,180,220' : '200,160,80',
    }));

    // Draw the mesh network between nodes
    function drawMesh(w, h, t) {
      const maxDist = Math.min(w, h) * 0.22;
      const positions = meshNodes.map(n => ({
        x: n.xf * w,
        y: n.yf * h,
        color: n.color,
        pulse: n.pulse,
      }));

      // Lines between nearby mesh nodes
      for (let i = 0; i < positions.length; i++) {
        for (let j = i + 1; j < positions.length; j++) {
          const dx = positions[j].x - positions[i].x;
          const dy = positions[j].y - positions[i].y;
          const dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < maxDist) {
            const fade = 1 - dist / maxDist;
            // Subtle pulse along line
            const pulse = (Math.sin(t * 1.2 + positions[i].pulse) * 0.5 + 0.5);
            const alpha = fade * fade * 0.18 * (0.6 + pulse * 0.4);
            ctx.beginPath();
            ctx.moveTo(positions[i].x, positions[i].y);
            ctx.lineTo(positions[j].x, positions[j].y);
            ctx.strokeStyle = `rgba(80,180,220,${alpha})`;
            ctx.lineWidth = fade * 0.7;
            ctx.stroke();
          }
        }
      }

      // Nodes as small glowing dots
      positions.forEach((p, i) => {
        const pulse = Math.sin(t * 1.5 + meshNodes[i].pulse) * 0.5 + 0.5;
        const alpha = 0.15 + pulse * 0.45;
        const r = meshNodes[i].r * (0.8 + pulse * 0.4);
        const g = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, r * 5);
        g.addColorStop(0,   `rgba(${p.color},${alpha})`);
        g.addColorStop(0.4, `rgba(${p.color},${alpha * 0.3})`);
        g.addColorStop(1,   'transparent');
        ctx.beginPath();
        ctx.arc(p.x, p.y, r * 5, 0, Math.PI * 2);
        ctx.fillStyle = g;
        ctx.fill();
      });
    }

    // Connector lines between nearby cell blobs
    function drawConnectors(w, h, t) {
      const positions = cells.map(c => ({
        x: c.xf * w + Math.sin(t * c.sp * 0.4 + c.sd) * w * 0.04,
        y: c.yf * h + Math.cos(t * c.sp * 0.3 + c.sd) * h * 0.04,
      }));
      for (let i = 0; i < positions.length; i++) {
        for (let j = i + 1; j < positions.length; j++) {
          const dx = positions[j].x - positions[i].x;
          const dy = positions[j].y - positions[i].y;
          const dist = Math.sqrt(dx * dx + dy * dy);
          const maxDist = Math.min(w, h) * 0.45;
          if (dist < maxDist) {
            const alpha = (1 - dist / maxDist) * 0.18;
            ctx.beginPath();
            ctx.moveTo(positions[i].x, positions[i].y);
            ctx.lineTo(positions[j].x, positions[j].y);
            ctx.strokeStyle = `rgba(120,200,240,${alpha})`;
            ctx.lineWidth = 1.0;
            ctx.stroke();
          }
        }
      }
    }

    let t = 0;
    function animate() {
      const w = canvas.width;
      const h = canvas.height;
      ctx.clearRect(0, 0, w, h);

      t += 0.006;

      // 1. Mesh network (behind everything)
      drawMesh(w, h, t);

      // 2. Cell-to-cell connectors
      drawConnectors(w, h, t);

      // 3. Morphing blobs
      cells.forEach(c => {
        const cx = c.xf * w + Math.sin(t * c.sp * 0.4 + c.sd) * w * 0.04;
        const cy = c.yf * h + Math.cos(t * c.sp * 0.3 + c.sd) * h * 0.04;
        const r  = c.rf * Math.min(w, h);
        const pts = blobPoints(cx, cy, r, 24, t * c.sp, c.sd);
        drawBlob(pts, c.c1, c.c2, cx, cy, r);
      });

      // 4. Move and draw glow accent dots
      glowNodes.forEach(p => {
        p.xf += p.vx; p.yf += p.vy;
        if (p.xf < 0) p.xf = 1; if (p.xf > 1) p.xf = 0;
        if (p.yf < 0) p.yf = 1; if (p.yf > 1) p.yf = 0;
        const px = p.xf * w, py = p.yf * h;
        const g = ctx.createRadialGradient(px, py, 0, px, py, p.r * 5);
        g.addColorStop(0,   `rgba(${p.color},${p.alpha})`);
        g.addColorStop(1,   'transparent');
        ctx.beginPath();
        ctx.arc(px, py, p.r * 5, 0, Math.PI * 2);
        ctx.fillStyle = g;
        ctx.fill();
      });

      // 5. Move mesh nodes
      meshNodes.forEach(n => {
        n.xf += n.vx; n.yf += n.vy;
        if (n.xf < 0) n.xf = 1; if (n.xf > 1) n.xf = 0;
        if (n.yf < 0) n.yf = 1; if (n.yf > 1) n.yf = 0;
      });

      requestAnimationFrame(animate);
    }
    animate();
  })();

  /* ---- Glitch / Noise Effect ---- */
  (function() {
    const canvas = document.getElementById('glitchCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    function resize() {
      canvas.width  = canvas.offsetWidth;
      canvas.height = canvas.offsetHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    // ---- Helpers ----
    function rand(min, max) { return Math.random() * (max - min) + min; }
    function randInt(min, max) { return Math.floor(rand(min, max)); }

    // Horizontal scanline slice displacement
    function drawScanlineShift(intensity) {
      const w = canvas.width, h = canvas.height;
      const slices = randInt(4, 14);
      for (let i = 0; i < slices; i++) {
        const sy = randInt(0, h);
        const sh = randInt(2, Math.max(4, h * 0.06 * intensity));
        const dx = rand(-w * 0.06 * intensity, w * 0.06 * intensity);
        // Copy a horizontal slice and draw shifted
        try {
          const img = ctx.getImageData(0, sy, w, sh);
          ctx.putImageData(img, dx, sy);
        } catch(e) {}
      }
    }

    // Digital noise blocks
    function drawNoiseBlocks(intensity) {
      const w = canvas.width, h = canvas.height;
      const blocks = randInt(2, Math.ceil(12 * intensity));
      for (let i = 0; i < blocks; i++) {
        const bw = randInt(20, w * 0.4);
        const bh = randInt(1, Math.max(2, 20 * intensity));
        const bx = randInt(0, w - bw);
        const by = randInt(0, h - bh);
        const r = randInt(0, 80), g = randInt(80, 200), b = randInt(160, 255);
        const a = rand(0.04, 0.18 * intensity);
        ctx.fillStyle = `rgba(${r},${g},${b},${a})`;
        ctx.fillRect(bx, by, bw, bh);
      }
    }

    // RGB channel separation lines
    function drawRGBSplit(intensity) {
      const w = canvas.width, h = canvas.height;
      const lines = randInt(1, Math.ceil(5 * intensity));
      for (let i = 0; i < lines; i++) {
        const y = randInt(0, h);
        const lh = randInt(1, 3);
        const offset = rand(4, 18 * intensity);
        ctx.fillStyle = `rgba(0,200,255,${rand(0.06, 0.18 * intensity)})`;
        ctx.fillRect(-offset, y, w, lh);
        ctx.fillStyle = `rgba(255,60,120,${rand(0.04, 0.12 * intensity)})`;
        ctx.fillRect(offset, y + lh, w, lh);
      }
    }

    // Full static noise overlay
    function drawStaticNoise(alpha) {
      const w = canvas.width, h = canvas.height;
      const imageData = ctx.createImageData(w, h);
      const data = imageData.data;
      for (let i = 0; i < data.length; i += 4) {
        const v = randInt(0, 255);
        data[i]   = v;
        data[i+1] = v;
        data[i+2] = v + randInt(0, 60);
        data[i+3] = Math.floor(alpha * 255);
      }
      ctx.putImageData(imageData, 0, 0);
    }

    // Horizontal full-width bright line sweep
    function drawSweepLine(y, alpha) {
      const w = canvas.width;
      const grad = ctx.createLinearGradient(0, y - 2, 0, y + 2);
      grad.addColorStop(0, 'transparent');
      grad.addColorStop(0.5, `rgba(180,230,255,${alpha})`);
      grad.addColorStop(1, 'transparent');
      ctx.fillStyle = grad;
      ctx.fillRect(0, y - 2, w, 4);
    }

    // ---- Intro sequence ----
    // Phase: 0=boot noise, 1=scanline sweep, 2=settling, 3=idle
    let phase = 0;
    let phaseStart = performance.now();
    let sweepY = 0;
    let idleTimer = 0;
    let glitchActive = false;
    let glitchFrames = 0;
    let glitchIntensity = 0;
    let nextGlitch = rand(2000, 5000); // ms until next random glitch
    let glitchBurst = 0; // consecutive bursts remaining

    function scheduleNextGlitch() {
      nextGlitch = rand(2000, 5500);
      idleTimer = 0;
    }

    function triggerGlitch(intensity, frames) {
      glitchActive = true;
      glitchIntensity = intensity;
      glitchFrames = frames;
    }

    let lastTime = performance.now();

    function drawGlitch(now) {
      const w = canvas.width, h = canvas.height;
      const elapsed = now - phaseStart;
      const dt = now - lastTime;
      lastTime = now;

      ctx.clearRect(0, 0, w, h);

      // --- INTRO PHASES ---
      if (phase === 0) {
        // Boot: heavy static + noise blocks for ~600ms
        const prog = Math.min(elapsed / 600, 1);
        const intensity = 1 - prog * 0.6;
        drawStaticNoise(0.35 * intensity);
        drawNoiseBlocks(intensity);
        drawRGBSplit(intensity);
        if (elapsed > 600) { phase = 1; phaseStart = now; sweepY = 0; }

      } else if (phase === 1) {
        // Scanline sweep top→bottom over ~800ms
        const prog = Math.min(elapsed / 800, 1);
        sweepY = prog * h;
        // Residual noise above sweep
        const noiseAlpha = (1 - prog) * 0.18;
        if (noiseAlpha > 0.01) drawStaticNoise(noiseAlpha);
        drawSweepLine(sweepY, 0.65);
        drawRGBSplit(0.4 * (1 - prog));
        drawNoiseBlocks(0.5 * (1 - prog));
        if (prog >= 1) { phase = 2; phaseStart = now; revealHeroElements(); revealVideoPanel(); }

      } else if (phase === 2) {
        // Settling: a few quick residual glitches over ~500ms
        const prog = Math.min(elapsed / 500, 1);
        if (Math.random() < 0.3) {
          drawScanlineShift(0.3 * (1 - prog));
          drawRGBSplit(0.2 * (1 - prog));
        }
        if (prog >= 1) { phase = 3; scheduleNextGlitch(); }

      } else {
        // --- IDLE: occasional random glitch ---
        idleTimer += dt;
        if (!glitchActive && idleTimer > nextGlitch) {
          glitchBurst = randInt(1, 3);
          triggerGlitch(rand(0.5, 0.9), randInt(7, 16));
          scheduleNextGlitch();
        }
        if (glitchActive) {
          drawScanlineShift(glitchIntensity);
          drawRGBSplit(glitchIntensity * 0.8);
          drawNoiseBlocks(glitchIntensity * 0.6);
          if (Math.random() < 0.25) drawSweepLine(rand(0, canvas.height), rand(0.2, 0.45));
          if (Math.random() < 0.2) drawStaticNoise(rand(0.03, 0.10));
          glitchFrames--;
          if (glitchFrames <= 0) {
            if (glitchBurst > 1) {
              glitchBurst--;
              glitchFrames = randInt(4, 10);
              glitchIntensity = rand(0.4, 0.8);
            } else {
              glitchActive = false;
            }
          }
        }
      }

      requestAnimationFrame(drawGlitch);
    }

    requestAnimationFrame(drawGlitch);

    // ---- Hero element sequential glitch reveal ----
    function revealHeroElements() {
      const els = [
        { id: 'hi-overline', delay: 0 },
        { id: 'hi-h1',       delay: 100 },
        { id: 'hi-sub',      delay: 240 },
        { id: 'hi-body',     delay: 400 },
        { id: 'hi-btns',     delay: 560 },
      ];
      els.forEach(({ id, delay }) => {
        setTimeout(() => {
          const el = document.getElementById(id);
          if (!el) return;
          // Brief flicker before main reveal
          el.style.opacity = '1';
          el.classList.add('hero-glitch-flicker');
          setTimeout(() => {
            el.classList.remove('hero-glitch-flicker');
            el.classList.add('hero-glitch-in');
            el.style.opacity = '';
          }, 120);
        }, delay);
      });
    }

  })();

  /* ---- Hero video panel reveal ---- */
  function revealVideoPanel() {
    const panel = document.getElementById('heroVideoPanel');
    if (!panel) return;
    setTimeout(() => {
      panel.style.animation = 'videoPanelIn 0.8s cubic-bezier(0.22,1,0.36,1) forwards';
    }, 300);
  }

  /* ---- Video modal open/close ---- */
  function toggleHeroVideo(wrap) {
    openVideoModal();
  }

  function openVideoModal() {
    const modal = document.getElementById('videoModal');
    const modalVideo = document.getElementById('videoModalVideo');
    const miniVideo = document.getElementById('heroVideo');
    if (!modal) return;
    // Pause mini video while modal is open
    if (miniVideo) miniVideo.pause();
    modal.classList.add('open');
    if (modalVideo) {
      modalVideo.pause();
      modalVideo.defaultMuted = false;
      modalVideo.muted = false;
      modalVideo.removeAttribute('muted');
      modalVideo.volume = 1;
      modalVideo.currentTime = 0;
      const playPromise = modalVideo.play();
      if (playPromise && typeof playPromise.catch === 'function') {
        playPromise.catch(() => {
          modalVideo.controls = true;
        });
      }
    }
    // Close on backdrop click
    modal.onclick = (e) => { if (e.target === modal) closeVideoModal(); };
  }

  function closeVideoModal() {
    const modal = document.getElementById('videoModal');
    const modalVideo = document.getElementById('videoModalVideo');
    const miniVideo = document.getElementById('heroVideo');
    if (!modal) return;
    modal.classList.remove('open');
    if (modalVideo) modalVideo.pause();
    if (miniVideo) miniVideo.play();
  }

  // ESC key to close
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeVideoModal();
  });

  /* ---- HUD Clock ---- */
  (function() {
    function updateClock() {
      const el = document.getElementById('hudClock');
      if (!el) return;
      const now = new Date();
      const hh = String(now.getHours()).padStart(2,'0');
      const mm = String(now.getMinutes()).padStart(2,'0');
      const ss = String(now.getSeconds()).padStart(2,'0');
      el.textContent = `${hh}:${mm}:${ss}`;
    }
    updateClock();
    setInterval(updateClock, 1000);
  })();

  /* ---- MV Background organic breathing canvas ---- */
  (function() {
    const canvas = document.getElementById('mvBgCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    const img = new Image();
    img.onerror = function() {
      this.onerror = null;
      this.src = '<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/mv_bg.jpg';
    };
    img.src = '<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/mv_bg.webp';

    function resize() {
      canvas.width  = canvas.offsetWidth;
      canvas.height = canvas.offsetHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    let t = 0;

    const GRID = 8;
    const OVERLAP = 6;

    function drawBreathing() {
      const w = canvas.width;
      const h = canvas.height;

      if (!img.complete || img.naturalWidth === 0) {
        requestAnimationFrame(drawBreathing);
        return;
      }

      t += 0.0055;

      ctx.clearRect(0, 0, w, h);

      const breathe  = Math.sin(t * 0.55) * 0.028
                     + Math.sin(t * 0.31) * 0.012
                     + Math.sin(t * 1.10) * 0.004;
      const scale    = 1.08 + breathe;

      const driftX = Math.sin(t * 0.38) * 0.018 + Math.cos(t * 0.22) * 0.009;
      const driftY = Math.sin(t * 0.28) * 0.014 + Math.cos(t * 0.17) * 0.007;

      const imgRatio    = img.naturalWidth / img.naturalHeight;
      const canvasRatio = w / h;
      let sw, sh;
      if (canvasRatio > imgRatio) {
        sw = w * scale;
        sh = sw / imgRatio;
      } else {
        sh = h * scale;
        sw = sh * imgRatio;
      }
      const sx = (w - sw) / 2 + driftX * w;
      const sy = (h - sh) / 2 + driftY * h;

      const cols = GRID;
      const rows = GRID;
      const cellW = sw / cols;
      const cellH = sh / rows;
      const srcW  = img.naturalWidth  / cols;
      const srcH  = img.naturalHeight / rows;

      for (let row = 0; row < rows; row++) {
        for (let col = 0; col < cols; col++) {
          const phase = col * 0.55 + row * 0.72;
          const warpX = Math.sin(t * 0.7  + phase) * 3.5
                      + Math.sin(t * 1.3  + phase * 1.4) * 1.5;
          const warpY = Math.cos(t * 0.6  + phase) * 3.0
                      + Math.cos(t * 1.1  + phase * 1.2) * 1.2;

          const bright = 0.88 + breathe * 8;
          ctx.filter = `brightness(${bright.toFixed(3)})`;

          const dx = sx + col * cellW + warpX;
          const dy = sy + row * cellH + warpY;

          const srcOverlapX = (srcW / cellW) * OVERLAP;
          const srcOverlapY = (srcH / cellH) * OVERLAP;
          ctx.drawImage(
            img,
            col * srcW - srcOverlapX,
            row * srcH - srcOverlapY,
            srcW  + srcOverlapX * 2,
            srcH  + srcOverlapY * 2,
            dx    - OVERLAP,
            dy    - OVERLAP,
            cellW + OVERLAP * 2,
            cellH + OVERLAP * 2
          );
        }
      }

      ctx.filter = 'none';
      requestAnimationFrame(drawBreathing);
    }

    img.onload  = drawBreathing;
    if (img.complete) drawBreathing();
  })();
  </script>

<?php get_footer(); ?>
