<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ config('app.name', 'ConnectCare CMS') }}</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

  <style>
    :root{
      --cc-blue:#007bff;
      --cc-violet:#6610f2;
      --cc-sky:#e9f2ff;
      --cc-gray:#f8f9fa;
      --cc-dark:#101522;
      --radius:22px;
    }
    html,body{height:100%}
    body{
      font-family: "Poppins",system-ui,-apple-system,Segoe UI,Roboto,Arial,Helvetica,sans-serif;
      background: radial-gradient(1200px 800px at 10% 10%, #eaf2ff 0%, #ffffff 40%) no-repeat,
                  linear-gradient(135deg, var(--cc-blue), var(--cc-violet)) fixed;
      background-blend-mode: soft-light, normal;
      color:#263142;
    }
    .glass{
      background: rgba(255,255,255,.86);
      box-shadow: 0 12px 40px rgba(0,0,0,.08);
      backdrop-filter: blur(10px);
      border-radius: var(--radius);
      border: 1px solid rgba(255,255,255,.5);
    }
    .btn-cc{
      background: linear-gradient(135deg,var(--cc-blue),var(--cc-violet));
      border: 0;
      color:#fff;
      padding:.85rem 1.5rem;
      border-radius: 999px;
      font-weight:600;
      box-shadow: 0 8px 24px rgba(44,71,255,.25);
    }
    .btn-cc:hover{ filter: saturate(1.1) brightness(1.02); color:#fff }
    .badge-soft{background: rgba(0,123,255,.12); color: var(--cc-blue); border-radius:999px}
    .icon-pill{
      display:inline-flex; align-items:center; justify-content:center;
      width:48px; height:48px; border-radius:14px;
      background: linear-gradient(135deg,#eef5ff,#ffffff);
      border:1px solid #edf1ff;
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
      color:var(--cc-blue);
    }
    .hero{
      padding-top: 96px; padding-bottom: 72px;
    }
    .section{
      padding: 72px 0;
    }
    .mock{
      background: linear-gradient(180deg,#f8faff,#ffffff);
      border:1px solid #eef2ff; border-radius:18px; height:260px;
      position:relative; overflow:hidden;
    }
    .mock:before,.mock:after{
      content:""; position:absolute; inset:auto 0 0 0; height:46%;
      background: linear-gradient(180deg,#ebf2ff,transparent);
    }
    .quote{
      border-left:4px solid var(--cc-blue);
      padding-left:1rem;
    }
    /* simple fade/slide */
    .reveal{ opacity:0; transform: translateY(18px); transition: all .6s ease}
    .reveal.show{ opacity:1; transform:none }
    @media (max-width: 992px){
      .hero{ padding-top: 72px; padding-bottom: 48px }
    }
  </style>
</head>
<body>

  <!-- NAV -->
  <nav class="navbar navbar-expand-lg navbar-dark" style="background:transparent;">
    <div class="container">
      <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
        <span class="icon-pill" style="width:36px;height:36px;border-radius:10px;"><i class="bi bi-heart"></i></span>
        <span>ConnectCare</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div id="nav" class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
          <li class="nav-item"><a class="nav-link text-white-50" href="#features">Features</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="#how">How it works</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="#testimonials">Stories</a></li>
          <li class="nav-item ms-lg-2">
            <a href="{{ route('login') }}" class="btn btn-light text-primary fw-semibold px-3 py-2 rounded-pill">
              <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <header class="hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-6">
          {{-- <span class="badge badge-soft px-3 py-2 small">Church Management System</span> --}}
          <h1 class="display-5 fw-bold text-white mt-3">
            Empowering Churches to <span class="text-warning">Care</span>, <span class="text-warning">Connect</span> & Grow.
          </h1>
          <p class="lead text-white-50 mt-3 mb-4">
            Manage members, first-timers, and follow-ups. Send SMS, submit reports manage service attendance and see insights—
            all in one simple, ministry-ready platform.
          </p>
          {{-- <div class="d-flex gap-3">
            <a href="{{ route('login') }}" class="btn-cc">
              <i class="bi bi-rocket-takeoff me-2"></i> Get Started
            </a>
            <a href="#preview" class="btn btn-outline-light rounded-pill fw-semibold px-4">
              <i class="bi bi-play-circle me-1"></i> See Preview
            </a>
          </div> --}}
        </div>
        <div class="col-lg-6">
          <div class="glass p-4">
            <div class="mock"></div>
            <div class="row text-white-75 mt-3 g-3">
              <div class="col-4">
                <div class="glass text-center p-3" style="background:rgba(255,255,255,.3);">
                  <div class="h4 mb-0">24K</div>
                  <small>Members</small>
                </div>
              </div>
              <div class="col-4">
                <div class="glass text-center p-3" style="background:rgba(255,255,255,.3);">
                  <div class="h4 mb-0">97%</div>
                  <small>Follow-ups</small>
                </div>
              </div>
              <div class="col-4">
                <div class="glass text-center p-3" style="background:rgba(255,255,255,.3);">
                  <div class="h4 mb-0">3x</div>
                  <small>Engagement</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /row -->
    </div>
  </header>

  <!-- FEATURES -->
  <section id="features" class="section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold text-white">What you can do with ConnectCare</h2>
        <p class="text-white-50 mb-0">Modern tools designed for ministry teams.</p>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-4 reveal">
          <div class="glass p-4 h-100">
            <div class="icon-pill mb-3"><i class="bi bi-people"></i></div>
            <h5 class="fw-semibold mb-2">Member Management</h5>
            <p class="text-muted mb-0">Centralize profiles, track first-timers & new converts, and segment audiences.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal">
          <div class="glass p-4 h-100">
            <div class="icon-pill mb-3"><i class="bi bi-kanban"></i></div>
            <h5 class="fw-semibold mb-2">Assignments & Follow-ups</h5>
            <p class="text-muted mb-0">Create tasks, assign to teams, and monitor progress in real time.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal">
          <div class="glass p-4 h-100">
            <div class="icon-pill mb-3"><i class="bi bi-chat-dots"></i></div>
            <h5 class="fw-semibold mb-2">Communications</h5>
            <p class="text-muted mb-0">Reach people with SMS now; plug in email/WhatsApp later—already designed for it.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal">
          <div class="glass p-4 h-100">
            <div class="icon-pill mb-3"><i class="bi bi-graph-up-arrow"></i></div>
            <h5 class="fw-semibold mb-2">Reports & Insights</h5>
            <p class="text-muted mb-0">View attendance, conversions, and trends that inform better ministry decisions.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal">
          <div class="glass p-4 h-100">
            <div class="icon-pill mb-3"><i class="bi bi-people-fill"></i></div>
            <h5 class="fw-semibold mb-2">Team Collaboration</h5>
            <p class="text-muted mb-0">Leader and staff views with just the right permissions, simple and secure.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal">
          <div class="glass p-4 h-100">
            <div class="icon-pill mb-3"><i class="bi bi-shield-check"></i></div>
            <h5 class="fw-semibold mb-2">Secure & Ready</h5>
            <p class="text-muted mb-0">Built on Laravel—role-based access, queues, and integrations done right.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section id="how" class="section">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-5">
          <h2 class="fw-bold text-white">How it works</h2>
          <p class="text-white-50">A straightforward flow that brings clarity to your ministry operations.</p>

          <div class="glass p-3 mb-3 reveal">
            <div class="d-flex align-items-start gap-3">
              <div class="icon-pill"><i class="bi bi-person-add"></i></div>
              <div>
                <h6 class="mb-1">1) Register</h6>
                <p class="text-muted mb-0">Add members, first-timers, and new converts.</p>
              </div>
            </div>
          </div>

          <div class="glass p-3 mb-3 reveal">
            <div class="d-flex align-items-start gap-3">
              <div class="icon-pill"><i class="bi bi-list-check"></i></div>
              <div>
                <h6 class="mb-1">2) Manage & Communicate</h6>
                <p class="text-muted mb-0">Assign follow-ups, track progress, and send targeted messages.</p>
              </div>
            </div>
          </div>

          <div class="glass p-3 reveal">
            <div class="d-flex align-items-start gap-3">
              <div class="icon-pill"><i class="bi bi-bar-chart-line"></i></div>
              <div>
                <h6 class="mb-1">3) Grow with Insight</h6>
                <p class="text-muted mb-0">See what’s working with clean reports that guide next steps.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="glass p-4 reveal">
            <div class="mock mb-3"></div>
            <div class="d-flex gap-3">
              <div class="mock flex-fill" style="height:140px"></div>
              <div class="mock flex-fill" style="height:140px"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- PREVIEW / SCREENSHOTS -->
  {{-- <section id="preview" class="section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold text-white">Designed for Simplicity. Built for Growth.</h2>
        <p class="text-white-50">Clean dashboards and flows that feel effortless.</p>
      </div>

      <div class="row g-4">
        <div class="col-md-6 reveal">
          <div class="glass p-4 h-100">
            <h6 class="fw-semibold mb-3"><i class="bi bi-speedometer2 me-1"></i> Dashboard Preview</h6>
            <div class="mock"></div>
          </div>
        </div>
        <div class="col-md-6 reveal">
          <div class="glass p-4 h-100">
            <h6 class="fw-semibold mb-3"><i class="bi bi-ui-checks-grid me-1"></i> Attendance & Reports</h6>
            <div class="mock"></div>
          </div>
        </div>
      </div>
    </div>
  </section> --}}

  <!-- TESTIMONIALS -->
  <section id="testimonials" class="section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold text-white">What leaders are saying</h2>
      </div>

      <div class="row g-4">
        <div class="col-lg-4 reveal">
          <div class="glass p-4 h-100">
            <p class="quote text-muted mb-3">“ConnectCare helped us follow up every first-timer within 48 hours. Our engagement tripled.”</p>
            <div class="d-flex align-items-center gap-3">
              <div class="icon-pill" style="width:44px;height:44px;border-radius:50%"><i class="bi bi-person"></i></div>
              <div><strong>Pastor Daniel</strong><div class="text-muted small">Lusaka</div></div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 reveal">
          <div class="glass p-4 h-100">
            <p class="quote text-muted mb-3">“The simplicity is brilliant. Our team leads finally have one source of truth.”</p>
            <div class="d-flex align-items-center gap-3">
              <div class="icon-pill" style="width:44px;height:44px;border-radius:50%"><i class="bi bi-person"></i></div>
              <div><strong>Sis. Chipo</strong><div class="text-muted small">Ndola</div></div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 reveal">
          <div class="glass p-4 h-100">
            <p class="quote text-muted mb-3">“SMS campaigns to new converts are now one click. It’s a game changer.”</p>
            <div class="d-flex align-items-center gap-3">
              <div class="icon-pill" style="width:44px;height:44px;border-radius:50%"><i class="bi bi-person"></i></div>
              <div><strong>Bro. Mwewa</strong><div class="text-muted small">Kitwe</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  {{-- <section class="section">
    <div class="container">
      <div class="glass p-5 text-center reveal" style="background: linear-gradient(135deg,#ffffffcc,#ffffffd9);">
        <h3 class="fw-bold mb-2">Start transforming your ministry today</h3>
        <p class="text-muted mb-4">It takes just a minute to get going. Your team will thank you.</p>
        <a href="{{ route('login') }}" class="btn-cc"><i class="bi bi-clipboard2-check me-2"></i> Try Free</a>
        <a href="#features" class="btn btn-outline-secondary rounded-pill ms-2 px-4">Learn more</a>
      </div>
    </div>
  </section> --}}

  <!-- FOOTER -->
  <footer class="py-4">
    <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between text-white-50">
      <div class="mb-2 mb-lg-0">
        &copy; {{ date('Y') }} ConnectCare CMS. All rights reserved.
      </div>
     
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // simple reveal on scroll
    const onReveal=()=>document.querySelectorAll('.reveal').forEach(el=>{
      const rect=el.getBoundingClientRect();
      if(rect.top<window.innerHeight-80) el.classList.add('show');
    });
    document.addEventListener('scroll', onReveal,{passive:true});
    window.addEventListener('load', onReveal);
  </script>
</body>
</html>
