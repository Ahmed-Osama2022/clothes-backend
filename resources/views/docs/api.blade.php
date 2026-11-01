<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title>{{ $spec['title'] }} — API Reference ({{ $spec['version'] }})</title>
  <style>
    :root {
      --bg: #f8fafc;
      --panel: #ffffff;
      --ink: #0f172a;
      --muted: #64748b;
      --line: #e2e8f0;
      --accent: #1d4ed8;
      --code-bg: #0f172a;
      --code-ink: #e2e8f0;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      background: var(--bg);
      color: var(--ink);
      font: 15px/1.6 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    a { color: var(--accent); text-decoration: none; }
    a:hover { text-decoration: underline; }

    .topbar {
      border-bottom: 1px solid var(--line);
      background: var(--panel);
      padding: 20px 32px;
    }

    .topbar h1 {
      margin: 0 0 4px;
      font-size: 22px;
      letter-spacing: -0.01em;
    }

    .topbar p {
      margin: 0;
      color: var(--muted);
      font-size: 13px;
    }

    .pill {
      display: inline-block;
      padding: 1px 8px;
      border-radius: 999px;
      background: #eff6ff;
      color: var(--accent);
      font-size: 12px;
      font-weight: 600;
      vertical-align: middle;
    }

    .layout {
      display: grid;
      grid-template-columns: 240px minmax(0, 1fr);
      gap: 32px;
      max-width: 1180px;
      margin: 0 auto;
      padding: 32px;
    }

    .sidebar {
      position: sticky;
      top: 24px;
      align-self: start;
      font-size: 13px;
    }

    .sidebar h2 {
      margin: 18px 0 6px;
      font-size: 11px;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--muted);
    }

    .sidebar ul { margin: 0; padding: 0; list-style: none; }

    .sidebar li { margin: 2px 0; }

    .sidebar a { color: var(--ink); }

    main { min-width: 0; }

    section { margin-bottom: 44px; }

    h2.section-title {
      margin: 0 0 6px;
      font-size: 19px;
    }

    .section-lead {
      margin: 0 0 18px;
      color: var(--muted);
      font-size: 14px;
    }

    .conventions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
      gap: 12px;
    }

    .convention {
      background: var(--panel);
      border: 1px solid var(--line);
      border-radius: 8px;
      padding: 12px 14px;
      font-size: 13px;
    }

    .convention strong { display: block; margin-bottom: 2px; }

    .card {
      background: var(--panel);
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 0;
      margin-bottom: 12px;
      scroll-margin-top: 16px;
    }

    /* Each endpoint is a native <details>: collapsed by default, opens only
       when clicked. No JavaScript is involved in the fold itself. */
    .card > .card-head {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 10px;
      padding: 14px 22px;
      cursor: pointer;
      list-style: none;
      user-select: none;
    }

    .card > .card-head::-webkit-details-marker { display: none; }

    /* Chevron: points right when collapsed, down when open. */
    .card > .card-head::after {
      content: "▸";
      margin-left: auto;
      color: var(--muted);
      font-size: 13px;
      transition: transform 250ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card[open] > .card-head::after { transform: rotate(90deg); }

    /* While collapsing, `open` is still true until the animation ends — reset
       the chevron immediately so it does not lag behind the motion. */
    .card.card--closing > .card-head::after { transform: none; }

    .card[open] > .card-head { border-bottom: 1px solid var(--line); }

    .card-body { padding: 16px 22px 20px; }

    .badge {
      font: 600 11px/1.4 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
      letter-spacing: 0.04em;
      padding: 3px 8px;
      border-radius: 5px;
      color: #fff;
    }

    .badge--get { background: #2563eb; }
    .badge--post { background: #16a34a; }
    .badge--put, .badge--patch { background: #d97706; }
    .badge--delete { background: #dc2626; }

    .path {
      font: 600 14px/1.4 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
      word-break: break-all;
    }

    .auth {
      font-size: 11px;
      color: var(--muted);
      border: 1px solid var(--line);
      border-radius: 999px;
      padding: 2px 9px;
    }

    .card p { margin: 0 0 12px; }

    h4 {
      margin: 18px 0 8px;
      font-size: 12px;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--muted);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
      margin-bottom: 6px;
    }

    th, td {
      text-align: left;
      vertical-align: top;
      padding: 7px 10px;
      border-bottom: 1px solid var(--line);
    }

    th { color: var(--muted); font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; }

    code {
      font: 12px/1.5 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
      background: #eef2f7;
      padding: 1px 5px;
      border-radius: 4px;
    }

    pre {
      margin: 8px 0 0;
      padding: 14px 16px;
      background: var(--code-bg);
      color: var(--code-ink);
      border-radius: 8px;
      overflow-x: auto;
      font: 12px/1.6 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    }

    .status-line {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 14px;
    }

    .status {
      font: 600 11px/1.4 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
      padding: 2px 8px;
      border-radius: 5px;
      color: #fff;
    }

    .status--2xx { background: #15803d; }
    .status--3xx { background: #1d4ed8; }
    .status--4xx { background: #b45309; }
    .status--5xx { background: #b91c1c; }

    .status-line span:last-child { color: var(--muted); font-size: 13px; }

    footer {
      border-top: 1px solid var(--line);
      padding: 18px 32px;
      color: var(--muted);
      font-size: 12px;
      text-align: center;
    }

    @media (max-width: 860px) {
      .layout { grid-template-columns: 1fr; padding: 20px; }
      .sidebar { position: static; }
      .topbar { padding: 16px 20px; }
    }
  </style>
</head>
<body>
  @php
    // Anchor ids must survive route braces, e.g. GET /products/{product}
    // becomes "get-products-product". Str::slug would drop the "/" instead of
    // turning it into a separator, so normalize the path explicitly.
    $anchorFor = function (array $endpoint): string {
      $path = trim(preg_replace('/[^a-z0-9]+/i', '-', $endpoint['path']), '-');

      return strtolower($endpoint['method']) . ($path === '' ? '' : '-' . $path);
    };
  @endphp

  <header class="topbar">
    <h1>{{ $spec['title'] }} <span class="pill">{{ $spec['version'] }}</span></h1>
    <p>
      Base URL <code>{{ $spec['base_url'] }}</code>
      · Updated {{ $spec['updated_at'] }}
      · JSON only
    </p>
  </header>

  <div class="layout">
    <nav class="sidebar" aria-label="Contents">
      <h2>Getting started</h2>
      <ul>
        <li><a href="#overview">Overview</a></li>
        <li><a href="#conventions">Conventions</a></li>
      </ul>

      @foreach ($spec['groups'] as $group)
        <h2>{{ $group['name'] }}</h2>
        <ul>
          @foreach ($group['endpoints'] as $endpoint)
            <li>
              <a href="#{{ $anchorFor($endpoint) }}">
                {{ $endpoint['method'] }} {{ $endpoint['path'] }}
              </a>
            </li>
          @endforeach
        </ul>
      @endforeach

      <h2>Roadmap</h2>
      <ul>
        <li><a href="#planned">Planned endpoints</a></li>
      </ul>
    </nav>

    <main>
      <section id="overview">
        <h2 class="section-title">Overview</h2>
        <p class="section-lead">{{ $spec['description'] }}</p>
      </section>

      <section id="conventions">
        <h2 class="section-title">Conventions</h2>
        <p class="section-lead">Rules that apply to every endpoint below.</p>
        <div class="conventions">
          @foreach ($spec['conventions'] as $convention)
            <div class="convention">
              <strong>{{ $convention['name'] }}</strong>
              {{ $convention['detail'] }}
            </div>
          @endforeach
        </div>
      </section>

      @foreach ($spec['groups'] as $group)
        <section id="{{ $group['slug'] }}">
          <h2 class="section-title">{{ $group['name'] }}</h2>
          <p class="section-lead">{{ $group['description'] }}</p>

          @foreach ($group['endpoints'] as $endpoint)
            @php
              // A group shares one base, but a legacy route can override it
              // (e.g. GET /api/user next to the versioned /api/v1/auth/*).
              $base = $endpoint['base'] ?? $group['base'] ?? $spec['base_url'];
            @endphp
            <details class="card" id="{{ $anchorFor($endpoint) }}">
              <summary class="card-head">
                <span class="badge badge--{{ strtolower($endpoint['method']) }}">{{ $endpoint['method'] }}</span>
                <span class="path">{{ $base }}{{ $endpoint['path'] }}</span>
                <span class="auth">{{ $endpoint['auth'] }}</span>
              </summary>

              <div class="card-body">
                <p><strong>{{ $endpoint['summary'] }}</strong></p>
                <p>{{ $endpoint['description'] }}</p>

                @if (! empty($endpoint['parameters']))
                  <h4>Parameters</h4>
                  <table>
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>In</th>
                        <th>Type</th>
                        <th>Required</th>
                        <th>Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($endpoint['parameters'] as $parameter)
                        <tr>
                          <td><code>{{ $parameter['name'] }}</code></td>
                          <td>{{ $parameter['in'] }}</td>
                          <td>{{ $parameter['type'] }}</td>
                          <td>{{ $parameter['required'] ? 'Yes' : 'No' }}</td>
                          <td>{{ $parameter['description'] }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                @endif

                <h4>Responses</h4>
                @foreach ($endpoint['responses'] as $response)
                  <div class="status-line">
                    <span class="status status--{{ intdiv($response['status'], 100) }}xx">{{ $response['status'] }}</span>
                    <span>{{ $response['description'] }}</span>
                  </div>
                  <pre>{{ $response['example'] === null
                    ? 'No response body.'
                    : json_encode($response['example'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                @endforeach
              </div>
            </details>
          @endforeach
        </section>
      @endforeach

      <section id="planned">
        <h2 class="section-title">Planned endpoints</h2>
        <p class="section-lead">
          Documented contract only. These routes currently answer with an empty
          <code>200</code> (controller stubs) or are not registered yet, so do not
          build a client against them.
        </p>

        <div class="card">
          <div class="card-body">
            <table>
              <thead>
                <tr>
                  <th>Method</th>
                  <th>Path</th>
                  <th>Summary</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($spec['planned'] as $item)
                  <tr>
                    <td><span class="badge badge--{{ strtolower($item['method']) }}">{{ $item['method'] }}</span></td>
                    <td><code>{{ $spec['base_url'] }}{{ $item['path'] }}</code></td>
                    <td>{{ $item['summary'] }}</td>
                    <td>{{ $item['note'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>
  </div>

  <footer>
    {{ $spec['title'] }} {{ $spec['version'] }} — generated from <code>docs/api/v1.php</code>.
  </footer>

  <script>
    // The fold is native <details>/<summary>; this adds a smooth 250ms
    // expand/collapse (height + fade + slight slide) and keeps sidebar deep
    // links working. Users who prefer reduced motion get an instant toggle.
    const FOLD_DURATION = 250;
    const FOLD_EASING = 'cubic-bezier(0.4, 0, 0.2, 1)';
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function cardBody(details) {
      return details.querySelector('.card-body');
    }

    function expand(details) {
      const body = cardBody(details);
      details.classList.remove('card--closing');
      details.open = true;

      if (prefersReducedMotion || !body) {
        return;
      }

      body.getAnimations().forEach((animation) => animation.cancel());
      body.style.overflow = 'hidden';

      const animation = body.animate(
        [
          { maxHeight: '0px', paddingTop: '0px', paddingBottom: '0px', opacity: 0, transform: 'translateY(-6px)' },
          { maxHeight: `${body.scrollHeight}px`, paddingTop: '16px', paddingBottom: '20px', opacity: 1, transform: 'translateY(0)' },
        ],
        { duration: FOLD_DURATION, easing: FOLD_EASING }
      );

      animation.onfinish = () => {
        // Drop the inline clip so <pre> keeps its own scrollbars.
        body.style.overflow = '';
      };
    }

    function collapse(details) {
      const body = cardBody(details);

      if (prefersReducedMotion || !body) {
        details.open = false;
        return;
      }

      body.getAnimations().forEach((animation) => animation.cancel());
      body.style.overflow = 'hidden';
      details.classList.add('card--closing');

      const animation = body.animate(
        [
          { maxHeight: `${body.scrollHeight}px`, paddingTop: '16px', paddingBottom: '20px', opacity: 1, transform: 'translateY(0)' },
          { maxHeight: '0px', paddingTop: '0px', paddingBottom: '0px', opacity: 0, transform: 'translateY(-6px)' },
        ],
        { duration: FOLD_DURATION, easing: FOLD_EASING, fill: 'forwards' }
      );

      animation.onfinish = () => {
        details.open = false;
        details.classList.remove('card--closing');
        animation.cancel(); // release the fill so the closed card is clean
        body.style.overflow = '';
      };
    }

    document.querySelectorAll('details.card > summary').forEach((summary) => {
      summary.addEventListener('click', (event) => {
        event.preventDefault(); // native toggle would skip the animation

        const details = summary.parentElement;
        details.open ? collapse(details) : expand(details);
      });
    });

    // Sidebar links: jump to an endpoint and expand its card, including when
    // the hash changes without a full page load.
    function openCardFromHash() {
      let target = null;

      try {
        target = document.querySelector(location.hash);
      } catch (error) {
        // Malformed hash — nothing to open.
      }

      if (target && target.tagName === 'DETAILS' && !target.open) {
        expand(target);
      }
    }

    window.addEventListener('hashchange', openCardFromHash);
    openCardFromHash();
  </script>
</body>
</html>
