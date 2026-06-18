import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: true });
const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
const page = await ctx.newPage();

const errors = [];
page.on('pageerror', e => errors.push(e.message));

// Login
await page.goto('http://localhost:8000/login');
await page.waitForTimeout(500);
await page.fill('input[name="email"]', 'x@x.com');
await page.fill('input[name="password"]', '123');
await page.click('button[type="submit"]');
await page.waitForTimeout(1200);

await page.goto('http://localhost:8000/oficina/os/nova');
await page.waitForFunction(() => window.Alpine !== undefined, { timeout: 5000 }).catch(() => {});
await page.waitForTimeout(800);

// Avançar direto para step 3 via Alpine.$data (evita colisão de seletores entre steps)
await page.evaluate(() => {
    const el = document.querySelector('[x-data*="novaOs"]');
    const data = Alpine.$data(el);
    // Simular seleção de cliente existente
    data.clienteMode = 'existente';
    data.clienteSelecionado = { id: 1, nome: 'Carlos Henrique Souza', cpf: '123.456.789-00', telefone: '(11) 99999-1111' };
    data.step = 2;
    // Simular seleção de veículo existente
    data.veiculoMode = 'existente';
    data.veiculoSelecionado = { id: 1, placa: 'ABC-1234', marca: 'Honda', modelo: 'Civic', ano: 2019, cor: 'Prata' };
    data.step = 3;
});
await page.waitForTimeout(500);
await page.screenshot({ path: 'verify-step3-direct.png' });
console.log('Screenshot step 3: verify-step3-direct.png');

// ── STEP 3: validação ─────────────────────────────────────────────────────────
console.log('\n=== STEP 3: VALIDAÇÃO ===');

// Verificar chips de resumo
const chipTexts = await page.evaluate(() => {
    return Array.from(document.querySelectorAll('.inline-flex.items-center.gap-1\\.5'))
        .filter(el => el.offsetParent !== null)
        .map(el => el.textContent?.trim().substring(0, 40));
});
console.log('Chips resumo (visíveis):', chipTexts);

// Verificar podeAvancar inicial (sem texto)
const podeAvancarInicial = await page.evaluate(() => {
    const el = document.querySelector('[x-data*="novaOs"]');
    return Alpine.$data(el).podeAvancar;
});
console.log('podeAvancar sem texto:', podeAvancarInicial === false ? 'false ✓' : 'true ✗ (ERRO)');

// Botão submit no desktop nav
const submitDesktop = page.locator('.hidden.sm\\:flex button[type="submit"]');
const classeInicial = await submitDesktop.getAttribute('class');
console.log('Submit desabilitado inicialmente:', classeInicial?.includes('cursor-not-allowed') ? 'SIM ✓' : 'NÃO ✗');

// Texto curto (< 10 chars)
await page.fill('textarea', 'curto');
await page.waitForTimeout(200);
const podeAvancar5 = await page.evaluate(() => Alpine.$data(document.querySelector('[x-data*="novaOs"]')).podeAvancar);
console.log('podeAvancar com "curto" (5 chars):', podeAvancar5 === false ? 'false ✓' : 'true ✗ (ERRO)');

const counter5 = await page.locator('textarea ~ p span[x-text]').textContent().catch(() => '?');
console.log('Counter com 5 chars:', counter5?.trim(), counter5?.trim() === '5' ? '✓' : '✗');

// Texto válido
await page.fill('textarea', 'Barulho ao frear no lado dianteiro esquerdo ao reduzir a velocidade.');
await page.waitForTimeout(300);
const podeAvancarValido = await page.evaluate(() => Alpine.$data(document.querySelector('[x-data*="novaOs"]')).podeAvancar);
console.log('podeAvancar com texto válido:', podeAvancarValido === true ? 'true ✓' : 'false ✗ (ERRO)');

const classeValido = await submitDesktop.getAttribute('class');
console.log('Submit habilitado com texto válido:', classeValido?.includes('bg-spark') ? 'SIM ✓' : 'NÃO ✗');
console.log('Submit sem cursor-not-allowed:', !classeValido?.includes('cursor-not-allowed') ? '✓' : '✗ (cursor-not-allowed presente)');

// ── SUBMISSÃO ────────────────────────────────────────────────────────────────
console.log('\n=== SUBMISSÃO ===');
await page.screenshot({ path: 'verify-pre-submit.png' });

await submitDesktop.click();
await page.waitForTimeout(2000);

const urlPos = page.url();
console.log('URL após submit:', urlPos);
console.log('Redirecionou para OS:', urlPos.includes('/os/OS-') ? '✓' : '✗');
await page.screenshot({ path: 'verify-pos-submit.png' });

// ── NAVEGAÇÃO VOLTAR ──────────────────────────────────────────────────────────
console.log('\n=== BOTÃO VOLTAR ===');
await page.goto('http://localhost:8000/oficina/os/nova');
await page.waitForFunction(() => window.Alpine !== undefined, { timeout: 5000 }).catch(() => {});
await page.waitForTimeout(800);

// No step 1, o botão Voltar deve estar oculto (x-show="step > 1")
const voltarVisStep1 = await page.locator('.hidden.sm\\:flex button:has-text("Voltar")').isVisible();
console.log('Voltar oculto no step 1:', !voltarVisStep1 ? '✓' : '✗');

// Avançar para step 2 via Alpine
await page.evaluate(() => {
    const data = Alpine.$data(document.querySelector('[x-data*="novaOs"]'));
    data.clienteMode = 'sem';
    data.step = 2;
    data.veiculoMode = 'sem';
});
await page.waitForTimeout(300);

const voltarVisStep2 = await page.locator('.hidden.sm\\:flex button:has-text("Voltar")').isVisible();
console.log('Voltar visível no step 2:', voltarVisStep2 ? '✓' : '✗');

// Clicar Voltar
await page.locator('.hidden.sm\\:flex button:has-text("Voltar")').click();
await page.waitForTimeout(300);

const stepAposVoltar = await page.evaluate(() => Alpine.$data(document.querySelector('[x-data*="novaOs"]')).step);
console.log('Step após Voltar:', stepAposVoltar, stepAposVoltar === 1 ? '✓' : '✗');

// ── MOBILE ───────────────────────────────────────────────────────────────────
console.log('\n=== MOBILE (390px) ===');
await page.setViewportSize({ width: 390, height: 844 });
await page.goto('http://localhost:8000/oficina/os/nova');
await page.waitForFunction(() => window.Alpine !== undefined, { timeout: 5000 }).catch(() => {});
await page.waitForTimeout(800);
await page.screenshot({ path: 'verify-mobile-nova-os.png' });
console.log('Screenshot mobile: verify-mobile-nova-os.png');

const mobileProgressBar = await page.locator('.sm\\:hidden.mb-5').isVisible();
const mobileFixedNav    = await page.locator('.sm\\:hidden.fixed.bottom-0').isVisible();
const desktopNavHidden  = await page.locator('.hidden.sm\\:flex').first().isVisible();
console.log('Barra de progresso mobile visível:', mobileProgressBar ? '✓' : '✗');
console.log('Nav mobile fixed visível:', mobileFixedNav ? '✓' : '✗');
console.log('Nav desktop oculta no mobile:', !desktopNavHidden ? '✓' : '✗');

// ── ERROS JS ─────────────────────────────────────────────────────────────────
console.log('\n=== ERROS JS ===');
console.log(errors.length === 0 ? 'Nenhum erro JS ✓' : errors);

await browser.close();
console.log('\nDONE');
