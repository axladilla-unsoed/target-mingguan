<?php
/**
 * Target Mingguan - Antarmuka Utama
 * Dilengkapi autentikasi password statis .env, penyelesaian tugas mingguan,
 * dan tracking produktivitas bergaya heatmap GitHub mingguan.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/koneksi.php';

$isLoggedIn = !empty($_SESSION['logged_in']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Target Mingguan & Tracking Produktivitas</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #f4f7fb;
      --panel: #ffffff;
      --panel-alt: #f0f4fc;
      --primary: #1f4fc9;
      --primary-hover: #173fa6;
      --primary-soft: #e8effe;
      --success: #1a9c61;
      --success-hover: #147f4e;
      --success-soft: #daf7ea;
      --warning: #f59e0b;
      --danger: #dc2626;
      --danger-soft: #fee2e2;
      --text: #0f172a;
      --text-muted: #64748b;
      --border: #e2e8f0;
      --border-focus: #93c5fd;
      --shadow-sm: 0 2px 8px rgba(15, 23, 42, 0.04);
      --shadow: 0 12px 32px rgba(15, 23, 42, 0.07);
      --shadow-lg: 0 20px 48px rgba(15, 23, 42, 0.12);
      --radius: 16px;
      --radius-sm: 10px;

      /* Skala Hijau GitHub untuk Produktivitas */
      --gh-l0: #ebedf0;
      --gh-l1: #9be9a8;
      --gh-l2: #40c463;
      --gh-l3: #30a14e;
      --gh-l4: #216e39;
    }

    * { box-sizing: border-box; }

    html, body {
      margin: 0;
      min-height: 100%;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: radial-gradient(circle at 10% 0%, #e8f0fe 0%, #f4f7fb 45%, #eef2f8 100%);
      color: var(--text);
      -webkit-font-smoothing: antialiased;
    }

    body {
      padding: 24px 18px 64px;
    }

    .app {
      max-width: 1240px;
      margin: 0 auto;
    }

    /* HEADER & NAV */
    .header-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 12px 20px;
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(12px);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
      margin-bottom: 24px;
      flex-wrap: wrap;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-logo {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 800;
      font-size: 18px;
      box-shadow: 0 4px 12px rgba(31, 79, 201, 0.25);
    }

    .brand-title {
      font-size: 1.15rem;
      font-weight: 700;
      margin: 0;
      line-height: 1.2;
    }

    .brand-subtitle {
      font-size: 12px;
      color: var(--text-muted);
      margin: 0;
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      font-size: 12px;
      font-weight: 600;
      color: #166534;
      background: #dcfce7;
      padding: 6px 12px;
      border-radius: 999px;
      border: 1px solid #bbf7d0;
    }

    .status-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #22c55e;
      box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
    }

    /* NAV TABS */
    .nav-tabs {
      display: flex;
      align-items: center;
      gap: 8px;
      background: rgba(235, 240, 248, 0.85);
      padding: 5px;
      border-radius: 14px;
      border: 1px solid var(--border);
    }

    .nav-tab-btn {
      appearance: none;
      border: none;
      background: transparent;
      padding: 9px 18px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      color: var(--text-muted);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
    }

    .nav-tab-btn:hover {
      color: var(--text);
      background: rgba(255, 255, 255, 0.5);
    }

    .nav-tab-btn.active {
      background: white;
      color: var(--primary);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .tab-badge {
      font-size: 11px;
      padding: 2px 7px;
      border-radius: 999px;
      background: var(--primary-soft);
      color: var(--primary);
      font-weight: 700;
    }

    .logout-btn {
      appearance: none;
      background: #fff;
      border: 1px solid var(--border);
      color: var(--text-muted);
      padding: 8px 14px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.15s ease;
    }

    .logout-btn:hover {
      color: var(--danger);
      border-color: #fecaca;
      background: #fef2f2;
    }

    /* TOP BAR INFO */
    .topbar {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 24px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }

    .eyebrow {
      margin: 0 0 8px;
      font-size: 12px;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: var(--primary);
      font-weight: 700;
    }

    h1 {
      margin: 0;
      font-size: clamp(2rem, 2.5vw + 1rem, 3.2rem);
      line-height: 1.08;
      letter-spacing: -0.04em;
      font-weight: 800;
    }

    .summary-card {
      min-width: 280px;
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(8px);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 16px 20px;
      box-shadow: var(--shadow);
    }

    .summary-card .eyebrow {
      margin-bottom: 10px;
      color: var(--text-muted);
    }

    .summary-value {
      font-size: 1.65rem;
      font-weight: 800;
      line-height: 1.1;
    }

    .summary-value span:first-child {
      color: var(--primary);
    }

    .progress {
      height: 8px;
      border-radius: 999px;
      background: #e2e8f0;
      overflow: hidden;
      margin-top: 12px;
    }

    .progress-bar {
      height: 100%;
      width: 0%;
      background: linear-gradient(90deg, var(--primary), #38bdf8);
      border-radius: inherit;
      transition: width 0.3s ease;
    }

    /* TOOLBAR & BUTTONS */
    .toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 22px;
    }

    button {
      appearance: none;
      border: none;
      border-radius: 12px;
      padding: 11px 18px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    button:hover:not(:disabled) {
      transform: translateY(-1px);
    }

    button:disabled {
      opacity: 0.55;
      cursor: not-allowed;
      transform: none;
    }

    .primary-btn {
      background: linear-gradient(135deg, var(--primary) 0%, #2563eb 100%);
      color: white;
      box-shadow: 0 4px 14px rgba(31, 79, 201, 0.25);
    }

    .primary-btn:hover:not(:disabled) {
      box-shadow: 0 6px 18px rgba(31, 79, 201, 0.35);
    }

    /* TOMBOL SELESAIKAN TUGAS MINGGU INI */
    .complete-week-btn {
      background: linear-gradient(135deg, #15803d 0%, #16a34a 100%);
      color: white;
      font-size: 14px;
      padding: 11px 20px;
      border-radius: 12px;
      box-shadow: 0 4px 14px rgba(22, 163, 74, 0.25);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .complete-week-btn:hover:not(:disabled) {
      background: linear-gradient(135deg, #166534 0%, #15803d 100%);
      box-shadow: 0 6px 20px rgba(22, 163, 74, 0.35);
    }

    /* FORM WORKLOAD */
    .task-form {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(8px);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 20px;
      margin-bottom: 24px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: minmax(240px, 1.8fr) 180px 200px;
      gap: 14px;
    }

    .field {
      display: flex;
      flex-direction: column;
      gap: 7px;
      min-width: 0;
    }

    .field label {
      font-size: 11.5px;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--text-muted);
      font-weight: 700;
    }

    .field input,
    .field select,
    .field textarea {
      width: 100%;
      background: white;
      border: 1px solid var(--border);
      border-radius: 11px;
      padding: 11px 13px;
      font-size: 14px;
      color: var(--text);
      transition: border-color 0.15s ease, box-shadow 0.15s ease;
      font-family: inherit;
    }

    .field input:focus,
    .field select:focus,
    .field textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(31, 79, 201, 0.12);
    }

    .field textarea {
      min-height: 70px;
      resize: vertical;
    }

    .form-submit {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      padding-top: 14px;
    }

    /* DAYS GRID */
    .days-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(240px, 1fr));
      gap: 20px;
      align-items: start;
      width: 100%;
    }

    .day-card {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(8px);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 16px 14px 14px;
      box-shadow: var(--shadow);
      min-height: 270px;
      display: flex;
      flex-direction: column;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .day-card:hover {
      box-shadow: var(--shadow-lg);
    }

    .day-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 12px;
      padding-bottom: 10px;
      border-bottom: 1px solid #f1f5f9;
    }

    .day-header h3 {
      margin: 0;
      font-size: 1.05rem;
      font-weight: 700;
      color: var(--text);
    }

    .chip {
      display: inline-block;
      border-radius: 999px;
      background: var(--primary-soft);
      color: var(--primary);
      font-size: 12px;
      font-weight: 700;
      padding: 3px 9px;
    }

    .task-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .task-item {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      padding: 12px 10px;
      border-radius: 12px;
      background: #f8fafc;
      border: 1px solid #eef2f6;
      min-width: 0;
      transition: all 0.2s ease;
    }

    .task-item:hover {
      background: #ffffff;
      border-color: #cbd5e1;
      box-shadow: var(--shadow-sm);
    }

    .task-item.done {
      background: var(--success-soft);
      border-color: #bbf7d0;
    }

    .check-box {
      width: 20px;
      height: 20px;
      margin-top: 2px;
      accent-color: var(--success);
      cursor: pointer;
      flex-shrink: 0;
    }

    .task-body {
      flex: 1;
      min-width: 0;
    }

    .task-title {
      font-weight: 600;
      font-size: 14px;
      line-height: 1.4;
      word-break: break-word;
      color: var(--text);
    }

    .task-item.done .task-title {
      text-decoration: line-through;
      color: #166534;
      opacity: 0.75;
    }

    .task-meta {
      margin-top: 4px;
      font-size: 12px;
      color: var(--text-muted);
      line-height: 1.4;
      word-break: break-word;
    }

    .task-actions {
      display: flex;
      flex-direction: column;
      gap: 6px;
      min-width: 90px;
    }

    .move-select {
      padding: 6px 8px;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: #fff;
      color: var(--text);
      font-size: 11.5px;
      cursor: pointer;
    }

    .delete-btn {
      background: #fff1f2;
      border: 1px solid #ffe4e6;
      color: #e11d48;
      padding: 6px 8px;
      border-radius: 8px;
      font-size: 11.5px;
      font-weight: 700;
      cursor: pointer;
      text-align: center;
      transition: background-color 0.15s ease;
    }

    .delete-btn:hover {
      background: #fee2e2;
    }

    .empty-state {
      border: 1px dashed #cbd5e1;
      border-radius: 12px;
      background: #f8fafc;
      color: var(--text-muted);
      padding: 16px 12px;
      font-size: 13px;
      text-align: center;
      line-height: 1.4;
    }

    /* ======================================================== */
    /* VIEW TRACKING PRODUKTIVITAS (GITHUB HEATMAP MINGGUAN)   */
    /* ======================================================== */
    .productivity-section {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
    }

    .kpi-card {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(8px);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 18px 20px;
      box-shadow: var(--shadow-sm);
    }

    .kpi-label {
      font-size: 11.5px;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--text-muted);
      font-weight: 700;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .kpi-num {
      font-size: 2rem;
      font-weight: 800;
      color: var(--text);
      line-height: 1.1;
    }

    .kpi-sub {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 5px;
    }

    /* HEATMAP CARD */
    .heatmap-card {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 24px 26px;
      box-shadow: var(--shadow);
    }

    .heatmap-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 16px;
    }

    .heatmap-title-group h2 {
      margin: 0 0 4px;
      font-size: 1.25rem;
      font-weight: 700;
    }

    .heatmap-title-group p {
      margin: 0;
      font-size: 13px;
      color: var(--text-muted);
    }

    .year-selector {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .year-btn {
      padding: 6px 14px;
      border-radius: 999px;
      border: 1px solid var(--border);
      background: white;
      color: var(--text-muted);
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.15s ease;
    }

    .year-btn:hover {
      border-color: var(--primary);
      color: var(--primary);
    }

    .year-btn.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }

    /* HEATMAP GRID ALA GITHUB MINGGUAN */
    .heatmap-wrapper {
      overflow-x: auto;
      padding-bottom: 12px;
    }

    .heatmap-container {
      min-width: 780px;
    }

    /* MONTH HEADERS */
    .month-labels-row {
      display: grid;
      grid-template-columns: repeat(52, 1fr);
      gap: 4px;
      margin-bottom: 6px;
      font-size: 11px;
      color: var(--text-muted);
      font-weight: 600;
      user-select: none;
    }

    .month-header-cell {
      grid-column: span 4;
      text-align: left;
    }

    /* WEEKS GRID (52 WEEKS) */
    .weeks-grid {
      display: grid;
      grid-template-columns: repeat(52, 1fr);
      gap: 4px;
      margin-bottom: 16px;
    }

    .week-cell {
      aspect-ratio: 1 / 1;
      border-radius: 4px;
      background-color: var(--gh-l0);
      cursor: pointer;
      position: relative;
      transition: transform 0.15s ease, outline 0.15s ease;
      outline: 1px solid rgba(0, 0, 0, 0.05);
    }

    .week-cell:hover {
      transform: scale(1.3);
      z-index: 10;
      outline: 2px solid #0f172a;
    }

    .week-cell.level-0 { background-color: var(--gh-l0); }
    .week-cell.level-1 { background-color: var(--gh-l1); }
    .week-cell.level-2 { background-color: var(--gh-l2); }
    .week-cell.level-3 { background-color: var(--gh-l3); }
    .week-cell.level-4 { background-color: var(--gh-l4); }

    .week-cell.is-current {
      box-shadow: 0 0 0 2px #2563eb, 0 0 8px rgba(37, 99, 235, 0.4);
      outline: none;
    }

    /* LEGEND FOOTER */
    .heatmap-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding-top: 12px;
      border-top: 1px solid var(--border);
      flex-wrap: wrap;
      font-size: 12px;
      color: var(--text-muted);
    }

    .heatmap-legend {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .legend-box {
      width: 13px;
      height: 13px;
      border-radius: 3px;
      outline: 1px solid rgba(0,0,0,0.05);
    }

    /* MODAL WEEK DETAIL */
    .week-detail-box {
      background: #f8fafc;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 14px 18px;
      margin-top: 16px;
      display: none;
      animation: fadeIn 0.2s ease;
    }

    .week-detail-box.show {
      display: block;
    }

    /* ======================================================== */
    /* PROMPT LOGIN SEDERHANA                                   */
    /* ======================================================== */
    .login-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(15, 23, 42, 0.65);
      backdrop-filter: blur(16px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      z-index: 1000;
      opacity: 1;
      transition: opacity 0.3s ease;
    }

    .login-overlay.hidden {
      display: none;
      opacity: 0;
    }

    .login-card {
      background: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.8);
      border-radius: 20px;
      padding: 36px 32px;
      max-width: 420px;
      width: 100%;
      box-shadow: var(--shadow-lg);
      text-align: center;
      animation: scaleUp 0.25s ease;
    }

    @keyframes scaleUp {
      from { transform: scale(0.95); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(4px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-badge {
      width: 56px;
      height: 56px;
      border-radius: 16px;
      background: var(--primary-soft);
      color: var(--primary);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      margin-bottom: 16px;
    }

    .login-title {
      font-size: 1.45rem;
      font-weight: 800;
      margin: 0 0 6px;
    }

    .login-subtitle {
      font-size: 13.5px;
      color: var(--text-muted);
      margin: 0 0 24px;
      line-height: 1.45;
    }

    .login-input-group {
      position: relative;
      margin-bottom: 16px;
      text-align: left;
    }

    .login-input-group label {
      display: block;
      font-size: 11.5px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--text-muted);
      margin-bottom: 6px;
    }

    .password-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .password-wrapper input {
      width: 100%;
      padding: 12px 42px 12px 14px;
      border-radius: 12px;
      border: 1px solid var(--border);
      font-size: 15px;
      font-family: inherit;
      transition: all 0.15s ease;
    }

    .password-wrapper input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(31, 79, 201, 0.15);
    }

    .toggle-pass-btn {
      position: absolute;
      right: 8px;
      background: none;
      border: none;
      padding: 6px;
      cursor: pointer;
      color: var(--text-muted);
      border-radius: 6px;
    }

    .toggle-pass-btn:hover {
      color: var(--text);
      transform: none;
    }

    .login-error {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: var(--danger);
      font-size: 13px;
      padding: 10px 12px;
      border-radius: 10px;
      margin-bottom: 16px;
      display: none;
      text-align: left;
    }

    .login-submit-btn {
      width: 100%;
      padding: 13px;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
    }

    .login-note {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 18px;
      line-height: 1.4;
    }

    .login-note code {
      font-family: 'JetBrains Mono', monospace;
      background: #f1f5f9;
      padding: 2px 5px;
      border-radius: 4px;
      font-size: 11px;
    }

    /* CONFIRM DIALOG MODAL */
    .confirm-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(15, 23, 42, 0.5);
      backdrop-filter: blur(8px);
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
      z-index: 990;
    }

    .confirm-overlay.show {
      display: flex;
    }

    .confirm-card {
      background: #ffffff;
      border-radius: 18px;
      padding: 28px 24px;
      max-width: 440px;
      width: 100%;
      box-shadow: var(--shadow-lg);
      animation: scaleUp 0.2s ease;
    }

    .confirm-title {
      font-size: 1.3rem;
      font-weight: 800;
      margin: 0 0 8px;
    }

    .confirm-desc {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.5;
      margin: 0 0 20px;
    }

    .confirm-stats {
      background: #f8fafc;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 12px 14px;
      margin-bottom: 20px;
    }

    .confirm-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 10px;
    }

    /* TOAST NOTIFICATION */
    .toast {
      position: fixed;
      right: 20px;
      bottom: 20px;
      background: rgba(15, 23, 42, 0.95);
      color: white;
      border-radius: 12px;
      padding: 12px 18px;
      box-shadow: var(--shadow-lg);
      font-size: 14px;
      font-weight: 500;
      opacity: 0;
      transform: translateY(16px);
      transition: opacity 0.25s ease, transform 0.25s ease;
      pointer-events: none;
      max-width: 360px;
      z-index: 1100;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .toast.show {
      opacity: 1;
      transform: translateY(0);
    }

    .toast.error {
      background: rgba(220, 38, 38, 0.95);
    }

    .toast.success {
      background: rgba(22, 163, 74, 0.95);
    }

    /* TOOLTIP FOR HEATMAP */
    .heatmap-tooltip {
      position: fixed;
      background: #0f172a;
      color: white;
      padding: 7px 11px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 500;
      pointer-events: none;
      z-index: 1050;
      box-shadow: 0 6px 16px rgba(0,0,0,0.25);
      line-height: 1.4;
      display: none;
      transform: translate(-50%, -100%);
      margin-top: -8px;
      white-space: nowrap;
    }

    /* RESPONSIVE */
    @media (max-width: 1180px) {
      .days-grid {
        grid-template-columns: repeat(2, minmax(220px, 1fr));
      }
      .kpi-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 860px) {
      body {
        padding: 16px 12px 48px;
      }
      .topbar {
        align-items: stretch;
      }
      .summary-card {
        width: 100%;
      }
      .days-grid {
        grid-template-columns: 1fr;
      }
      .form-grid {
        grid-template-columns: 1fr;
      }
      .kpi-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="app">
    <!-- TOP HEADER BAR DENGAN TAB DAN STATUS -->
    <header class="header-bar">
      <div class="brand">
        <div class="brand-logo">✓</div>
        <div>
          <h2 class="brand-title">Target Mingguan</h2>
          <p class="brand-subtitle">Workload & Tracking Produktivitas</p>
        </div>
      </div>

      <!-- TAB MENU: TARGET VS PRODUKTIVITAS -->
      <nav class="nav-tabs" aria-label="Menu Aplikasi">
        <button id="tab-btn-tasks" class="nav-tab-btn active" type="button">
          🎯 Target Mingguan
        </button>
        <button id="tab-btn-productivity" class="nav-tab-btn" type="button">
          📊 Tracking Produktivitas
          <span id="streak-badge" class="tab-badge" style="display:none;">0 mgg</span>
        </button>
      </nav>

      <div class="header-actions">
        <div class="status-pill">
          <span class="status-dot"></span> MySQL Terhubung
        </div>
        <button id="logout-btn" class="logout-btn" type="button" title="Keluar dari sesi">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
          Keluar
        </button>
      </div>
    </header>

    <!-- ======================================================== -->
    <!-- VIEW 1: TARGET MINGGUAN (PAPAN HARIAN)                   -->
    <!-- ======================================================== -->
    <main id="view-tasks">
      <section class="topbar">
        <div>
          <p class="eyebrow">Workload Mingguan · Senin – Minggu</p>
          <h1>Jadwal Target<br>Minggu Berjalan</h1>
        </div>

        <aside class="summary-card" aria-live="polite">
          <p class="eyebrow">Pencapaian Minggu Ini</p>
          <div class="summary-value"><span id="done-count">0</span> / <span id="total-count">0</span> tugas</div>
          <div class="progress" aria-label="Progress minggu">
            <div id="progress-bar" class="progress-bar"></div>
          </div>
        </aside>
      </section>

      <!-- TOOLBAR: TOMBOL SELESAIKAN TUGAS MINGGU INI -->
      <div class="toolbar">
        <button id="complete-week-btn" class="complete-week-btn" type="button">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
          Selesaikan Tugas Minggu ini
        </button>
        <div style="font-size: 13px; color: var(--text-muted);">
          Pindahkan checklist ke riwayat & mulai minggu baru
        </div>
      </div>

      <!-- FORM TAMBAH WORKLOAD -->
      <form id="task-form" class="task-form">
        <div class="form-grid">
          <div class="field">
            <label for="task-title">Nama target / tugas</label>
            <input id="task-title" name="title" type="text" placeholder="Contoh: Materi Statistik & Machine Learning" required>
          </div>

          <div class="field">
            <label for="task-day">Hari</label>
            <select id="task-day" name="day"></select>
          </div>

          <div class="field">
            <label for="task-hours">Waktu / estimasi</label>
            <input id="task-hours" name="hours" type="text" placeholder="Contoh: 2 jam / 08:00 - 10:00">
          </div>
        </div>

        <div class="field" style="margin-top: 12px;">
          <label for="task-note">Catatan tambahan</label>
          <textarea id="task-note" name="note" placeholder="Catatan tugas, deadline, atau referensi link..."></textarea>
        </div>

        <div class="form-submit">
          <button class="primary-btn" type="submit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Workload
          </button>
        </div>
      </form>

      <!-- GRID HARI (SENIN - MINGGU) -->
      <section id="days-grid" class="days-grid" aria-live="polite"></section>
    </main>

    <!-- ======================================================== -->
    <!-- VIEW 2: TRACKING PRODUKTIVITAS (GITHUB HEATMAP MINGGUAN) -->
    <!-- ======================================================== -->
    <main id="view-productivity" class="productivity-section" style="display:none;">
      <!-- KPI STATS CARDS -->
      <section class="kpi-grid">
        <div class="kpi-card">
          <div class="kpi-label">🏆 Total Tugas Selesai</div>
          <div id="kpi-total-tasks" class="kpi-num">0</div>
          <div class="kpi-sub">Sepanjang riwayat tercatat</div>
        </div>

        <div class="kpi-card">
          <div class="kpi-label">📅 Minggu Tercatat</div>
          <div id="kpi-weeks-count" class="kpi-num">0</div>
          <div class="kpi-sub">Minggu yang telah diselesaikan</div>
        </div>

        <div class="kpi-card">
          <div class="kpi-label">⚡ Streak Mingguan</div>
          <div id="kpi-streak" class="kpi-num">0</div>
          <div class="kpi-sub">Minggu aktif berturut-turut</div>
        </div>

        <div class="kpi-card">
          <div class="kpi-label">📈 Rata-rata per Minggu</div>
          <div id="kpi-avg" class="kpi-num">0</div>
          <div class="kpi-sub">Tugas terselesaikan per minggu</div>
        </div>
      </section>

      <!-- GITHUB STYLE WEEKLY HEATMAP CARD -->
      <section class="heatmap-card">
        <div class="heatmap-header">
          <div class="heatmap-title-group">
            <h2>Peta Produktivitas Mingguan</h2>
            <p>Setiap kotak mewakili 1 minggu. Makin banyak tugas yang diselesaikan pada minggu itu, warna kotak makin pekat kehijauan.</p>
          </div>

          <div class="year-selector">
            <span style="font-size: 13px; font-weight: 600; color: var(--text-muted); margin-right: 4px;">Tahun:</span>
            <button class="year-btn" data-year="2025" type="button">2025</button>
            <button class="year-btn active" data-year="2026" type="button">2026</button>
            <button class="year-btn" data-year="2027" type="button">2027</button>
          </div>
        </div>

        <!-- HEATMAP CONTAINER (52 WEEKS) -->
        <div class="heatmap-wrapper">
          <div class="heatmap-container">
            <!-- LABEL BULAN -->
            <div id="month-labels-row" class="month-labels-row"></div>

            <!-- KOTAK MINGGU (1-52) -->
            <div id="weeks-grid" class="weeks-grid"></div>
          </div>
        </div>

        <!-- LEGENDA SKALA HIJAU ALA GITHUB -->
        <div class="heatmap-footer">
          <div>
            <span style="font-weight: 600; color: var(--text);">Tips:</span> Arahkan kursor atau klik kotak minggu untuk melihat rincian tanggal dan tugas.
          </div>
          <div class="heatmap-legend">
            <span>Sedikit</span>
            <div class="legend-box level-0" style="background: var(--gh-l0);" title="0 tugas"></div>
            <div class="legend-box level-1" style="background: var(--gh-l1);" title="1-2 tugas"></div>
            <div class="legend-box level-2" style="background: var(--gh-l2);" title="3-5 tugas"></div>
            <div class="legend-box level-3" style="background: var(--gh-l3);" title="6-8 tugas"></div>
            <div class="legend-box level-4" style="background: var(--gh-l4);" title="9+ tugas"></div>
            <span>Banyak</span>
          </div>
        </div>

        <!-- DETAIL MINGGU TERPILIH -->
        <div id="week-detail-box" class="week-detail-box">
          <div style="display: flex; align-items: center; justify-content: space-between;">
            <h4 id="detail-week-title" style="margin: 0; font-size: 15px; font-weight: 700;">Rincian Minggu</h4>
            <span id="detail-week-status" class="chip">Tercatat</span>
          </div>
          <p id="detail-week-desc" style="margin: 6px 0 0; font-size: 13.5px; color: var(--text-muted);"></p>
        </div>
      </section>
    </main>
  </div>

  <!-- ======================================================== -->
  <!-- MODAL PROMPT LOGIN SEDERHANA (PASSWORD STATIS .ENV)     -->
  <!-- ======================================================== -->
  <div id="login-overlay" class="login-overlay <?= $isLoggedIn ? 'hidden' : '' ?>">
    <div class="login-card">
      <div class="login-badge">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
      </div>
      <h2 class="login-title">Akses Target Mingguan</h2>
      <p class="login-subtitle">Masukkan password statis untuk membuka dan mengelola jadwal target Anda.</p>

      <form id="login-form">
        <div id="login-error" class="login-error"></div>

        <div class="login-input-group">
          <label for="login-password">Password</label>
          <div class="password-wrapper">
            <input id="login-password" type="password" placeholder="Masukkan password..." required autofocus>
            <button id="toggle-password" class="toggle-pass-btn" type="button" title="Tampilkan/Sembunyikan password">
              <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </button>
          </div>
        </div>

        <button id="login-btn" class="primary-btn login-submit-btn" type="submit">
          Masuk ke Dashboard
        </button>
      </form>

      <div class="login-note">
        Konfigurasi password dapat diubah melalui variabel <code>APP_PASSWORD</code> di file <code>.env</code>.
      </div>
    </div>
  </div>

  <!-- ======================================================== -->
  <!-- MODAL KONFIRMASI: SELESAIKAN TUGAS MINGGU INI            -->
  <!-- ======================================================== -->
  <div id="confirm-complete-modal" class="confirm-overlay">
    <div class="confirm-card">
      <h3 class="confirm-title">Selesaikan Tugas Minggu Ini?</h3>
      <p class="confirm-desc">
        Seluruh capaian tugas minggu ini akan diarsipkan ke riwayat produktivitas (heatmap GitHub) dan checklist tugas akan disiapkan kembali untuk minggu baru.
      </p>

      <div class="confirm-stats">
        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Capaian Saat Ini:</div>
        <div style="font-size: 16px; font-weight: 700; color: var(--text);">
          <span id="modal-done-count">0</span> dari <span id="modal-total-count">0</span> tugas selesai (<span id="modal-percentage">0%</span>)
        </div>
      </div>

      <div class="confirm-actions">
        <button id="cancel-complete-btn" class="logout-btn" type="button">Batal</button>
        <button id="confirm-complete-btn" class="complete-week-btn" type="button">
          ✓ Ya, Selesaikan & Catat
        </button>
      </div>
    </div>
  </div>

  <!-- TOOLTIP INTERAKTIF HEATMAP -->
  <div id="heatmap-tooltip" class="heatmap-tooltip"></div>

  <!-- TOAST NOTIFIKASI -->
  <div id="toast" class="toast" aria-live="polite"></div>

  <script>
    const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    const MONTH_NAMES = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    const state = {
      authenticated: <?= $isLoggedIn ? 'true' : 'false' ?>,
      activeTab: 'tasks',
      selectedYear: new Date().getFullYear(),
      tasks: [],
      productivityData: null
    };

    // DOM Elements - Auth
    const loginOverlay = document.getElementById('login-overlay');
    const loginForm = document.getElementById('login-form');
    const loginPassword = document.getElementById('login-password');
    const togglePasswordBtn = document.getElementById('toggle-password');
    const loginError = document.getElementById('login-error');
    const loginBtn = document.getElementById('login-btn');
    const logoutBtn = document.getElementById('logout-btn');

    // DOM Elements - Navigation Tabs
    const tabBtnTasks = document.getElementById('tab-btn-tasks');
    const tabBtnProductivity = document.getElementById('tab-btn-productivity');
    const viewTasks = document.getElementById('view-tasks');
    const viewProductivity = document.getElementById('view-productivity');
    const streakBadge = document.getElementById('streak-badge');

    // DOM Elements - Tasks View
    const taskForm = document.getElementById('task-form');
    const taskTitle = document.getElementById('task-title');
    const taskDay = document.getElementById('task-day');
    const taskHours = document.getElementById('task-hours');
    const taskNote = document.getElementById('task-note');
    const daysGrid = document.getElementById('days-grid');
    const doneCountEl = document.getElementById('done-count');
    const totalCountEl = document.getElementById('total-count');
    const progressBar = document.getElementById('progress-bar');
    const completeWeekBtn = document.getElementById('complete-week-btn');

    // DOM Elements - Confirm Complete Modal
    const confirmModal = document.getElementById('confirm-complete-modal');
    const modalDoneCount = document.getElementById('modal-done-count');
    const modalTotalCount = document.getElementById('modal-total-count');
    const modalPercentage = document.getElementById('modal-percentage');
    const cancelCompleteBtn = document.getElementById('cancel-complete-btn');
    const confirmCompleteBtn = document.getElementById('confirm-complete-btn');

    // DOM Elements - Productivity Heatmap View
    const kpiTotalTasks = document.getElementById('kpi-total-tasks');
    const kpiWeeksCount = document.getElementById('kpi-weeks-count');
    const kpiStreak = document.getElementById('kpi-streak');
    const kpiAvg = document.getElementById('kpi-avg');
    const monthLabelsRow = document.getElementById('month-labels-row');
    const weeksGrid = document.getElementById('weeks-grid');
    const weekDetailBox = document.getElementById('week-detail-box');
    const detailWeekTitle = document.getElementById('detail-week-title');
    const detailWeekDesc = document.getElementById('detail-week-desc');
    const detailWeekStatus = document.getElementById('detail-week-status');
    const yearButtons = document.querySelectorAll('.year-btn');
    const heatmapTooltip = document.getElementById('heatmap-tooltip');
    const toastEl = document.getElementById('toast');

    // Inisialisasi awal
    init();

    function init() {
      populateDayOptions();
      setupEventListeners();

      if (state.authenticated) {
        loadAppData();
      } else {
        loginOverlay.classList.remove('hidden');
        loginPassword.focus();
      }
    }

    function setupEventListeners() {
      // 1. Auth Events
      loginForm.addEventListener('submit', handleLogin);
      logoutBtn.addEventListener('click', handleLogout);
      togglePasswordBtn.addEventListener('click', togglePasswordVisibility);

      // 2. Tab Navigation
      tabBtnTasks.addEventListener('click', () => switchTab('tasks'));
      tabBtnProductivity.addEventListener('click', () => switchTab('productivity'));

      // 3. Task Form Submit
      taskForm.addEventListener('submit', handleCreateTask);

      // 4. Checkbox & Move Day & Delete Task (Event delegation)
      daysGrid.addEventListener('change', handleGridChange);
      daysGrid.addEventListener('click', handleGridClick);

      // 5. Selesaikan Tugas Minggu Ini Modal
      completeWeekBtn.addEventListener('click', openCompleteWeekModal);
      cancelCompleteBtn.addEventListener('click', closeCompleteWeekModal);
      confirmCompleteBtn.addEventListener('click', handleConfirmCompleteWeek);

      // 6. Year Selector Buttons
      yearButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          yearButtons.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          state.selectedYear = parseInt(btn.dataset.year, 10);
          fetchProductivity();
        });
      });
    }

    function switchTab(tab) {
      state.activeTab = tab;
      if (tab === 'tasks') {
        tabBtnTasks.classList.add('active');
        tabBtnProductivity.classList.remove('active');
        viewTasks.style.display = 'block';
        viewProductivity.style.display = 'none';
      } else {
        tabBtnTasks.classList.remove('active');
        tabBtnProductivity.classList.add('active');
        viewTasks.style.display = 'none';
        viewProductivity.style.display = 'flex';
        fetchProductivity();
      }
    }

    // ==========================================
    // AUTENTIKASI DENGAN PASSWORD STATIS .ENV
    // ==========================================
    async function handleLogin(e) {
      e.preventDefault();
      const password = loginPassword.value;
      if (!password) return;

      loginBtn.disabled = true;
      loginBtn.textContent = 'Memverifikasi...';
      loginError.style.display = 'none';

      try {
        const res = await fetch('api.php?action=login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ password })
        });
        const result = await res.json();

        if (result.success && result.authenticated) {
          state.authenticated = true;
          loginOverlay.classList.add('hidden');
          loginPassword.value = '';
          showToast('Login berhasil! Selamat datang.', 'success');
          loadAppData();
        } else {
          loginError.textContent = result.message || 'Password salah. Periksa file .env.';
          loginError.style.display = 'block';
          loginPassword.select();
        }
      } catch (err) {
        console.error(err);
        loginError.textContent = 'Gagal menghubungi server database.';
        loginError.style.display = 'block';
      } finally {
        loginBtn.disabled = false;
        loginBtn.textContent = 'Masuk ke Dashboard';
      }
    }

    async function handleLogout() {
      if (!confirm('Apakah Anda yakin ingin keluar?')) return;
      try {
        await fetch('api.php?action=logout');
      } catch (e) {
        console.error(e);
      }
      state.authenticated = false;
      loginOverlay.classList.remove('hidden');
      loginPassword.value = '';
      loginPassword.focus();
      showToast('Anda telah keluar.');
    }

    function togglePasswordVisibility() {
      if (loginPassword.type === 'password') {
        loginPassword.type = 'text';
      } else {
        loginPassword.type = 'password';
      }
    }

    // ==========================================
    // DATA LOADER
    // ==========================================
    async function loadAppData() {
      await fetchTasks();
      await fetchProductivity();
    }

    async function fetchTasks() {
      try {
        const response = await fetch('api.php?action=get_tasks');
        if (response.status === 401) {
          state.authenticated = false;
          loginOverlay.classList.remove('hidden');
          return;
        }
        const result = await response.json();
        if (result.success && Array.isArray(result.tasks)) {
          state.tasks = result.tasks;
          renderTasks();
        } else {
          showToast(result.message || 'Gagal memuat data target.', 'error');
        }
      } catch (error) {
        console.error(error);
        showToast('Koneksi ke database gagal.', 'error');
      }
    }

    // ==========================================
    // MANAJEMEN TUGAS MINGGUAN
    // ==========================================
    async function handleCreateTask(e) {
      e.preventDefault();
      const title = taskTitle.value.trim();
      if (!title) {
        showToast('Nama target tidak boleh kosong.', 'error');
        taskTitle.focus();
        return;
      }

      const newTask = {
        title,
        day: taskDay.value,
        hours: taskHours.value.trim(),
        note: taskNote.value.trim()
      };

      try {
        const response = await fetch('api.php?action=create', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(newTask)
        });
        const result = await response.json();

        if (!result.success) {
          throw new Error(result.message || 'Gagal menambahkan target.');
        }

        state.tasks.push(result.task);
        taskForm.reset();
        taskDay.value = 'Senin';
        taskTitle.focus();
        renderTasks();
        showToast(result.message || 'Workload berhasil ditambahkan.', 'success');
      } catch (error) {
        console.error(error);
        showToast(error.message, 'error');
      }
    }

    async function handleGridChange(e) {
      const target = e.target;

      // Toggle Checklist Selesai
      if (target.matches('input[type="checkbox"]')) {
        const taskId = target.dataset.id;
        const isDone = target.checked;
        const task = state.tasks.find(t => t.id === taskId);
        if (!task) return;

        const prevDone = task.done;
        task.done = isDone;
        renderTasks();

        try {
          const response = await fetch('api.php?action=toggle_done', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: taskId, done: isDone ? 1 : 0 })
          });
          const result = await response.json();
          if (!result.success) {
            throw new Error(result.message);
          }
        } catch (error) {
          console.error(error);
          task.done = prevDone;
          renderTasks();
          showToast('Gagal mengubah status: ' + error.message, 'error');
        }
      }

      // Pindahkan Hari
      if (target.matches('.move-select')) {
        const taskId = target.dataset.id;
        const newDay = target.value;
        const task = state.tasks.find(t => t.id === taskId);
        if (!task) return;

        const prevDay = task.day;
        task.day = newDay;
        renderTasks();

        try {
          const response = await fetch('api.php?action=move_day', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: taskId, day: newDay })
          });
          const result = await response.json();
          if (!result.success) {
            throw new Error(result.message);
          }
          showToast(`Tugas dipindahkan ke ${newDay}.`, 'success');
        } catch (error) {
          console.error(error);
          task.day = prevDay;
          renderTasks();
          showToast('Gagal memindahkan hari: ' + error.message, 'error');
        }
      }
    }

    async function handleGridClick(e) {
      const target = e.target;
      if (!target.matches('.delete-btn')) return;

      const taskId = target.dataset.id;
      const targetIndex = state.tasks.findIndex(t => t.id === taskId);
      if (targetIndex === -1) return;

      if (!confirm('Hapus tugas ini?')) return;

      const deletedTask = state.tasks[targetIndex];
      state.tasks.splice(targetIndex, 1);
      renderTasks();

      try {
        const response = await fetch('api.php?action=delete', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: taskId })
        });
        const result = await response.json();
        if (!result.success) {
          throw new Error(result.message);
        }
        showToast('Tugas berhasil dihapus.', 'success');
      } catch (error) {
        console.error(error);
        state.tasks.splice(targetIndex, 0, deletedTask);
        renderTasks();
        showToast('Gagal menghapus: ' + error.message, 'error');
      }
    }

    // ========================================================
    // ALUR SELESAIKAN TUGAS MINGGU INI (GANTI DARI RESET MINGGU)
    // ========================================================
    function openCompleteWeekModal() {
      const total = state.tasks.length;
      const done = state.tasks.filter(t => t.done).length;
      const percentage = total ? Math.round((done / total) * 100) : 0;

      modalDoneCount.textContent = String(done);
      modalTotalCount.textContent = String(total);
      modalPercentage.textContent = percentage + '%';

      confirmModal.classList.add('show');
    }

    function closeCompleteWeekModal() {
      confirmModal.classList.remove('show');
    }

    async function handleConfirmCompleteWeek() {
      closeCompleteWeekModal();
      confirmCompleteBtn.disabled = true;

      try {
        const response = await fetch('api.php?action=complete_week', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' }
        });
        const result = await response.json();

        if (!result.success) {
          throw new Error(result.message || 'Gagal menyelesaikan minggu ini.');
        }

        // Reset status lokal
        state.tasks.forEach(t => { t.done = false; });
        renderTasks();

        // Refresh riwayat produktivitas
        await fetchProductivity();

        showToast(result.message || 'Tugas minggu ini berhasil diselesaikan!', 'success');
      } catch (error) {
        console.error(error);
        showToast('Terjadi kesalahan: ' + error.message, 'error');
      } finally {
        confirmCompleteBtn.disabled = false;
      }
    }

    // ==========================================
    // RENDER PAPAN TUGAS
    // ==========================================
    function renderTasks() {
      const total = state.tasks.length;
      const done = state.tasks.filter(t => t.done).length;
      const percentage = total ? Math.round((done / total) * 100) : 0;

      doneCountEl.textContent = String(done);
      totalCountEl.textContent = String(total);
      progressBar.style.width = percentage + '%';

      daysGrid.innerHTML = '';

      DAYS.forEach(day => {
        const tasksForDay = state.tasks.filter(t => t.day === day);
        const card = document.createElement('article');
        card.className = 'day-card';

        const header = document.createElement('div');
        header.className = 'day-header';
        header.innerHTML = `<h3>${day}</h3><span class="chip">${tasksForDay.length}</span>`;

        const list = document.createElement('div');
        list.className = 'task-list';

        if (!tasksForDay.length) {
          const empty = document.createElement('div');
          empty.className = 'empty-state';
          empty.textContent = 'Belum ada workload di hari ini.';
          list.appendChild(empty);
        } else {
          tasksForDay.forEach(task => {
            const item = document.createElement('div');
            item.className = `task-item${task.done ? ' done' : ''}`;

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.checked = !!task.done;
            checkbox.className = 'check-box';
            checkbox.dataset.id = task.id;

            const body = document.createElement('div');
            body.className = 'task-body';

            const title = document.createElement('div');
            title.className = 'task-title';
            title.textContent = task.title;

            const meta = document.createElement('div');
            meta.className = 'task-meta';
            const metaParts = [];
            if (task.hours) metaParts.push(task.hours);
            if (task.note) metaParts.push(task.note);
            meta.textContent = metaParts.join(' • ');

            body.appendChild(title);
            if (metaParts.length) body.appendChild(meta);

            const actions = document.createElement('div');
            actions.className = 'task-actions';

            const select = document.createElement('select');
            select.className = 'move-select';
            select.dataset.id = task.id;
            DAYS.forEach(optionDay => {
              const option = document.createElement('option');
              option.value = optionDay;
              option.textContent = optionDay;
              if (optionDay === task.day) option.selected = true;
              select.appendChild(option);
            });

            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'delete-btn';
            deleteBtn.dataset.id = task.id;
            deleteBtn.textContent = 'Hapus';

            actions.appendChild(select);
            actions.appendChild(deleteBtn);

            item.appendChild(checkbox);
            item.appendChild(body);
            item.appendChild(actions);
            list.appendChild(item);
          });
        }

        card.appendChild(header);
        card.appendChild(list);
        daysGrid.appendChild(card);
      });
    }

    // ========================================================
    // TRACKING PRODUKTIVITAS (GITHUB STYLE HEATMAP MINGGUAN)
    // ========================================================
    async function fetchProductivity() {
      try {
        const response = await fetch(`api.php?action=get_productivity&year=${state.selectedYear}`);
        if (response.status === 401) {
          state.authenticated = false;
          loginOverlay.classList.remove('hidden');
          return;
        }
        const result = await response.json();

        if (result.success) {
          state.productivityData = result;
          renderProductivity(result);
        }
      } catch (err) {
        console.error('Gagal mengambil data produktivitas:', err);
      }
    }

    function renderProductivity(data) {
      // 1. KPI Stats
      const stats = data.stats;
      kpiTotalTasks.textContent = String(stats.total_completed || 0);
      kpiWeeksCount.textContent = String(stats.weeks_recorded || 0);
      kpiStreak.textContent = `${stats.current_streak || 0} mgg`;
      kpiAvg.textContent = String(stats.avg_per_week || 0);

      if (stats.current_streak > 0) {
        streakBadge.textContent = `🔥 ${stats.current_streak} mgg`;
        streakBadge.style.display = 'inline-block';
      } else {
        streakBadge.style.display = 'none';
      }

      // 2. Label Bulan (Jan - Des dipetakan ke 52 minggu)
      monthLabelsRow.innerHTML = '';
      let lastMonth = '';
      data.weeks.forEach((w, idx) => {
        const monthHeader = document.createElement('div');
        monthHeader.className = 'month-col';
        if (w.month_name !== lastMonth && (idx === 0 || idx % 4 === 0)) {
          monthHeader.textContent = w.month_name;
          lastMonth = w.month_name;
        }
        monthLabelsRow.appendChild(monthHeader);
      });

      // 3. Matriks 52 Kotak Minggu
      weeksGrid.innerHTML = '';
      data.weeks.forEach(week => {
        const cell = document.createElement('div');
        cell.className = `week-cell level-${week.level}`;
        if (week.is_current) {
          cell.classList.add('is-current');
        }

        cell.dataset.week = week.week_number;
        cell.dataset.label = week.week_label;
        cell.dataset.completed = week.completed_tasks;
        cell.dataset.total = week.total_tasks;
        cell.dataset.rate = week.completion_rate;
        cell.dataset.isCurrent = week.is_current ? '1' : '0';
        cell.dataset.isRecorded = week.is_recorded ? '1' : '0';

        // Event Hover Tooltip
        cell.addEventListener('mouseenter', showHeatmapTooltip);
        cell.addEventListener('mousemove', moveHeatmapTooltip);
        cell.addEventListener('mouseleave', hideHeatmapTooltip);

        // Event Klik Tampilkan Detail
        cell.addEventListener('click', () => showWeekDetail(week));

        weeksGrid.appendChild(cell);
      });
    }

    function showHeatmapTooltip(e) {
      const cell = e.currentTarget;
      const label = cell.dataset.label;
      const completed = cell.dataset.completed;
      const total = cell.dataset.total;
      const rate = cell.dataset.rate;
      const isCurrent = cell.dataset.isCurrent === '1';

      let html = `<strong>${label}</strong><br>`;
      if (parseInt(total, 10) > 0 || parseInt(completed, 10) > 0) {
        html += `${completed} dari ${total} tugas selesai (${rate}%)`;
      } else {
        html += `Belum ada catatan tugas`;
      }

      if (isCurrent) {
        html += `<br><span style="color:#60a5fa; font-size:11px;">● Minggu Berjalan (Live)</span>`;
      }

      heatmapTooltip.innerHTML = html;
      heatmapTooltip.style.display = 'block';
      moveHeatmapTooltip(e);
    }

    function moveHeatmapTooltip(e) {
      heatmapTooltip.style.left = e.clientX + 'px';
      heatmapTooltip.style.top = e.clientY + 'px';
    }

    function hideHeatmapTooltip() {
      heatmapTooltip.style.display = 'none';
    }

    function showWeekDetail(week) {
      weekDetailBox.classList.add('show');
      detailWeekTitle.textContent = week.week_label;

      if (week.is_current) {
        detailWeekStatus.textContent = 'Minggu Berjalan (Live)';
        detailWeekStatus.style.background = '#dbeafe';
        detailWeekStatus.style.color = '#1d4ed8';
      } else if (week.is_recorded) {
        detailWeekStatus.textContent = 'Terselesaikan & Tercatat';
        detailWeekStatus.style.background = '#dcfce7';
        detailWeekStatus.style.color = '#15803d';
      } else {
        detailWeekStatus.textContent = 'Belum Ada Data';
        detailWeekStatus.style.background = '#f1f5f9';
        detailWeekStatus.style.color = '#64748b';
      }

      detailWeekDesc.innerHTML = `
        <strong>${week.completed_tasks}</strong> dari <strong>${week.total_tasks}</strong> tugas selesai diselesaikan (${week.completion_rate}%).<br>
        Periode: ${week.start_date} s.d. ${week.end_date}.
      `;
    }

    // ==========================================
    // UTILITY HELPER
    // ==========================================
    function populateDayOptions() {
      DAYS.forEach(day => {
        const option = document.createElement('option');
        option.value = day;
        option.textContent = day;
        taskDay.appendChild(option);
      });
      taskDay.value = 'Senin';
    }

    function showToast(message, type = 'normal') {
      toastEl.textContent = message;
      toastEl.className = 'toast show';
      if (type === 'error') toastEl.classList.add('error');
      if (type === 'success') toastEl.classList.add('success');

      clearTimeout(showToast.timeoutId);
      showToast.timeoutId = setTimeout(() => {
        toastEl.classList.remove('show');
      }, 3000);
    }
  </script>
</body>
</html>
