import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: true });
const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
const page = await ctx.newPage();

const allLogs = [];
page.on('console', m => allLogs.push(`[${m.type()}] ${m.text()}`));
page.on('pageerror', e => allLogs.push(`[PAGEERROR] ${e.message} — ${e.stack?.split('\n')[0]}`));

// Login
await page.goto('http://localhost:8000/login');
await page.waitForTimeout(500);
await page.fill('input[name="email"]', 'x@x.com');
await page.fill('input[name="password"]', '123');
await page.click('button[type="submit"]');
await page.waitForTimeout(1200);

// Navegar para Nova OS
await page.goto('http://localhost:8000/oficina/os/nova');

// Aguardar Alpine inicializar (ou timeout de 5s)
await page.waitForFunction(() => window.Alpine !== undefined, { timeout: 5000 }).catch(() => {});
await page.waitForTimeout(1000);

// Diagnóstico de estado
const diag = await page.evaluate(() => {
    const wizardEl = document.querySelector('[x-data*="novaOs"]');
    const bodyEl   = document.querySelector('body[x-data]');

    // Verifica Alpine
    const alpineLoaded  = typeof window.Alpine !== 'undefined';
    const alpineVersion = window.Alpine?.version ?? 'N/A';

    // Verifica função novaOs
    const novaOsExists  = typeof window.novaOs === 'function';

    // Verifica estado do componente via Alpine
    let componentData = null;
    if (wizardEl && window.Alpine) {
        try {
            componentData = Alpine.$data(wizardEl);
        } catch(e) {
            componentData = 'erro: ' + e.message;
        }
    }

    // Verifica visibilidade dos elementos chave
    function isVisible(sel) {
        const el = document.querySelector(sel);
        if (!el) return 'não encontrado';
        const style = window.getComputedStyle(el);
        return style.display !== 'none' && style.visibility !== 'hidden' ? 'visível' : 'oculto (display:' + style.display + ')';
    }

    // Pega o HTML real do x-data attribute
    const xDataAttr = wizardEl?.getAttribute('x-data')?.substring(0, 120);

    // Verifica step-1 div
    const step1Div = wizardEl?.querySelector('[x-show]');
    const step1XShow = step1Div?.getAttribute('x-show');
    const step1Display = step1Div ? window.getComputedStyle(step1Div).display : 'N/A';

    return {
        alpineLoaded,
        alpineVersion,
        novaOsExists,
        componentData: componentData ? {
            step: componentData.step,
            clienteMode: componentData.clienteMode,
        } : null,
        xDataAttr,
        step1XShow,
        step1Display,
        bodyXData: bodyEl?.getAttribute('x-data')?.substring(0, 60),
        wizardFound: !!wizardEl,
    };
});

console.log('\n=== DIAGNÓSTICO ALPINE ===');
console.log('Alpine carregado:', diag.alpineLoaded);
console.log('Alpine versão:', diag.alpineVersion);
console.log('novaOs() definida:', diag.novaOsExists);
console.log('Componente encontrado:', diag.wizardFound);
console.log('x-data attr (120 chars):', diag.xDataAttr);
console.log('Component data:', JSON.stringify(diag.componentData));
console.log('step-1 div x-show:', diag.step1XShow);
console.log('step-1 div display:', diag.step1Display);
console.log('body x-data:', diag.bodyXData);

// Tenta chamar novaOs manualmente no contexto da página para ver se dá erro
const novaOsCallResult = await page.evaluate(() => {
    if (typeof window.novaOs !== 'function') return 'novaOs não definida';
    try {
        const result = window.novaOs([], []);
        return { ok: true, step: result.step, keys: Object.keys(result) };
    } catch(e) {
        return { ok: false, error: e.message };
    }
});
console.log('\n=== TESTE MANUAL novaOs([], []) ===');
console.log(JSON.stringify(novaOsCallResult));

// Screenshot final
await page.screenshot({ path: 'verify-diag-nova-os.png', fullPage: true });
console.log('\nScreenshot: verify-diag-nova-os.png');

console.log('\n=== TODOS OS LOGS DE CONSOLE ===');
allLogs.forEach(l => console.log(l));

await browser.close();
console.log('\nDONE');
