<?php header('Content-Type: text/css'); ?>
/* Reset & base */
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f5f5f5;
}

/* Nav */
.nav { padding: 12px 24px; background: white; box-shadow: 0 1px 4px rgba(0,0,0,0.1); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
.nav a { color: #1976d2; text-decoration: none; font-weight: 500; }
.nav a:hover { text-decoration: underline; }
.nav-links { display: flex; gap: 16px; }
.nav-links a { color: #1976d2; text-decoration: none; font-weight: 500; }
.nav-links a:hover { text-decoration: underline; }

/* Theme toggle */
.theme-toggle { background: none; border: 1px solid #ddd; border-radius: 6px; padding: 4px 10px; cursor: pointer; font-size: 1.1rem; color: #666; line-height: 1; }
.theme-toggle:hover { background: #f0f0f0; }

/* Intro */
.intro {
    max-width: 900px;
    margin: 0 auto 24px;
    padding: 24px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    position: relative;
}
.intro-wide { max-width: 95%; }
.intro h1 { font-size: 1.6rem; color: #2c3e50; margin-bottom: 8px; }
.intro p { color: #666; margin-bottom: 8px; }
.intro code { background: #f6f8fa; padding: 2px 6px; border-radius: 3px; font-size: 0.9em; }
.intro a { color: #1976d2; }
.intro blockquote { border-left: 4px solid #ddd; padding: 4px 12px; margin: 12px auto; max-width: 600px; color: #555; font-size: 0.9rem; text-align: left; }

/* Badges */
.intro-badges { position: absolute; top: 16px; right: 16px; display: flex; gap: 8px; align-items: center; }
.badge { padding: 3px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.badge-go { background: #e0f2f1; color: #00695c; }
.badge-rust { background: #fbe9e7; color: #bf360c; }
.badge-js { background: #fff8e1; color: #f57f17; }
.badge-size { background: #f3f4f6; color: #6b7280; }

/* Example blocks (split-view) */
.example {
    max-width: 900px;
    margin: 0 auto 24px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}
.example-header {
    padding: 16px 24px;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.example-header h2 { font-size: 1.1rem; color: #2c3e50; }
.example-body { padding: 16px; }
.example-body pre { margin: 0; padding: 16px; border-radius: 6px; overflow-x: auto; }
.example-sections { display: grid; grid-template-columns: 1fr 1fr; }
.example-output { padding: 20px 24px; }
.example-output .label,
.example-code .label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #888;
    margin-bottom: 10px;
    font-weight: 600;
}
.example-code {
    padding: 20px 24px;
    background: #1e1e2e;
    border-left: 1px solid #333;
}
.example-code pre {
    font-size: 12.5px;
    font-family: 'SF Mono', 'Fira Code', monospace;
    white-space: pre-wrap;
    color: #cdd6f4;
    line-height: 1.5;
    background: transparent;
    border: none;
    padding: 0;
}

/* Multi-pane layout */
.container {
    display: flex;
    gap: 20px;
    width: 95%;
    margin: 0 auto 24px;
}
.pane, .panel {
    flex: 1;
    overflow: auto;
    padding: 20px;
    border-radius: 8px;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.pane h2 { text-align: center; font-size: 1.4rem; margin-bottom: 4px; color: #2c3e50; padding-bottom: 8px; border-bottom: 1px solid #eee; }
.panel h2 { text-align: center; font-size: 1.3rem; color: #2c3e50; margin-bottom: 8px; }
.lib-link { display: block; text-align: center; font-size: 13px; color: #4078c0; text-decoration: none; margin-bottom: 12px; }
.lib-link:hover { text-decoration: underline; }

/* Timing */
.timing { font-size: 13px; color: #3a5d7a; font-weight: 600; background: #e8f4fd; padding: 4px 12px; border-radius: 20px; }
.timing-info {
    background: #f0f8ff;
    border: 1px solid #d0e3f0;
    border-radius: 4px;
    padding: 8px 12px;
    margin-bottom: 16px;
    font-size: 14px;
    color: #3a5d7a;
    font-weight: bold;
    text-align: center;
}

/* Pre */
pre {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 13px;
    border: 1px solid #eee;
    white-space: pre-wrap;
    word-break: break-word;
    overflow-x: auto;
}

/* Pane content (markdown rendering, sanitize output) */
.pane img { max-width: 100%; height: auto; }
.pane table { border-collapse: collapse; width: 100%; margin: 1em 0; }
.pane th, .pane td { border: 1px solid #ddd; padding: 8px; text-align: left; }
.pane th { background: #f6f8fa; }
.pane blockquote { border-left: 4px solid #ddd; padding: 0 1em; margin: 1em 0; color: #555; }
.pane pre { background: #f8f9fa; padding: 12px; border-radius: 4px; font-size: 13px; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; }
.pane code { background: #f6f8fa; padding: 2px 4px; border-radius: 3px; font-size: 0.9em; }

/* Footer */
footer { max-width: 900px; margin: 40px auto 20px; text-align: center; color: #999; font-size: 0.85rem; }
footer a { color: #999; text-decoration: underline; }

/* Dark theme */
html.dark body { background-color: #1a1a2e; color: #e0e0e0; }
html.dark .nav { background: #16213e; box-shadow: 0 1px 4px rgba(0,0,0,0.3); }
html.dark .nav a, html.dark .nav-links a { color: #64b5f6; }
html.dark .intro { background: #16213e; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
html.dark .intro h1 { color: #e0e0e0; }
html.dark .intro p { color: #aaa; }
html.dark .intro code { background: #1e2a45; color: #cdd6f4; }
html.dark .intro a { color: #64b5f6; }
html.dark .intro blockquote { border-left-color: #2a3a5c; color: #aaa; }
html.dark .badge-go { background: #1a332e; color: #4db6ac; }
html.dark .badge-rust { background: #332119; color: #ffab91; }
html.dark .badge-js { background: #33301a; color: #ffd54f; }
html.dark .badge-size { background: #1e2a45; color: #8b95a5; }
html.dark .example { background: #16213e; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
html.dark .example-header { background: #1e2a45; border-bottom-color: #2a3a5c; }
html.dark .example-header h2 { color: #e0e0e0; }
html.dark .example-body { background: #16213e; }
html.dark .timing { background: #1e2a45; color: #64b5f6; }
html.dark .timing-info { background: #1e2a45; border-color: #2a3a5c; color: #64b5f6; }
html.dark .example-output { color: #e0e0e0; }
html.dark .example-code { background: #0d1117; border-left-color: #21262d; }
html.dark .pane, html.dark .panel { background: #16213e; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
html.dark .pane h2, html.dark .panel h2 { color: #e0e0e0; border-bottom-color: #2a3a5c; }
html.dark .lib-link { color: #64b5f6; }
html.dark .pane th { background: #1e2a45; }
html.dark .pane th, html.dark .pane td { border-color: #2a3a5c; }
html.dark .pane blockquote { border-left-color: #2a3a5c; color: #aaa; }
html.dark .pane pre { background: #0d1117; border-color: #21262d; color: #cdd6f4; }
html.dark .pane code { background: #1e2a45; color: #cdd6f4; }
html.dark .panel pre { background: #0d1117; border-color: #21262d; color: #cdd6f4; }
html.dark pre { background: #0d1117; border-color: #21262d; color: #cdd6f4; }
html.dark .theme-toggle { border-color: #444; color: #ccc; }
html.dark .theme-toggle:hover { background: #2a3a5c; }
html.dark footer { color: #666; }
html.dark footer a { color: #666; }

/* Responsive */
@media (max-width: 768px) {
    .container { flex-direction: column; }
    .example-sections { grid-template-columns: 1fr; }
    .example-code { border-left: none; border-top: 1px solid #333; }
}
