import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: true });
const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
const page = await ctx.newPage();

const errors = [];
const warnings = [];
page.on('console', m => {
    if (m.type() === 'error') errors.push('[ERROR] ' + m.text());
    if (m.type() === 'warn')  warnings.push('[WARN] '  + m.text());
});
page.on('pageerror', e => errors.push('[PAGEERROR] ' + e.message));

// ── 1. LOGIN ────────────────────────────────────────────────────────────────
console.log('\n=== 1. LOGIN ===');
await page.goto('http://localhost:8000/login');
await page.waitForTimeout(800);

console.log('URL login:', page.url());
await page.fill('input[name="email"]', 'admin@oficina.com');
await page.fill('input[name="password"]', '123456');
await page.click('button[type="submit"]');
await page.waitForTimeout(1200);
console.log('URL após login:', page.url());

// ── 2. NAVEGAR PARA NOVA OS ──────────────────────────────────────────────────
console.log('\n=== 2. ACESSO /oficina/os/nova ===');
await page.goto('http://localhost:8000/oficina/os/nova');
await page.waitForTimeout(1000);

const novaOsUrl = page.url();
console.log('URL:', novaOsUrl);
if (!novaOsUrl.includes('/os/nova')) {
    console.log('PROBLEMA: redirecionado para', novaOsUrl, '— auth ou rota falhou');
}

await page.screenshot({ path: 'verify-nova-os-01-inicial.png' });
console.log('Screenshot: verify-nova-os-01-inicial.png');

// Checar título e estrutura básica
const pageTitle = await page.title();
const h1Text    = await page.locator('h1').first().textContent().catch(() => '?');
console.log('Title:', pageTitle);
console.log('H1:', h1Text?.trim());

// ── 3. STEP 1 — VERIFICAR ESTADO INICIAL ────────────────────────────────────
console.log('\n=== 3. STEP 1 — ESTADO INICIAL ===');
const stepIndicator = await page.locator('.hidden.sm\\:flex').first().textContent().catch(() => '?');
console.log('Stepper visível:', stepIndicator?.trim().substring(0, 80));

// Botão Avançar deve estar desabilitado (nenhum modo selecionado)
const btnAvancar = page.locator('button:has-text("Avançar")').first();
const avancarDisabled = await btnAvancar.getAttribute('disabled').catch(() => 'N/A');
const avancarClass    = await btnAvancar.getAttribute('class').catch(() => '?');
console.log('Botão Avançar disabled attr:', avancarDisabled);
console.log('Botão Avançar class:', avancarClass?.substring(0, 80));

// Tentar avançar sem selecionar modo — não deve funcionar
await btnAvancar.click().catch(() => {});
await page.waitForTimeout(300);
const stepAposCliqueForce = await page.evaluate(() => {
    const el = document.querySelector('[x-data]');
    return el ? el._x_dataStack?.[0]?.step : '?';
});
console.log('Step após clicar Avançar sem seleção:', stepAposCliqueForce);

// ── 4. STEP 1 — MODO "JÁ CADASTRADO" ────────────────────────────────────────
console.log('\n=== 4. STEP 1 — MODO EXISTENTE ===');
await page.click('button:has-text("Já cadastrado")');
await page.waitForTimeout(400);
await page.screenshot({ path: 'verify-nova-os-02-cliente-existente.png' });
console.log('Screenshot: verify-nova-os-02-cliente-existente.png');

// Verificar lista de clientes renderizada
const clienteCards = await page.locator('[x-data] .space-y-2 > [class*="rounded-lg"]').count();
console.log('Cards de cliente visíveis:', clienteCards);

// Busca
const inputBusca = page.locator('input[placeholder*="Buscar por nome"]');
await inputBusca.fill('Carlos');
await page.waitForTimeout(300);
const cardsAposBusca = await page.locator('[x-data] .space-y-2 > [class*="rounded-lg"]').count();
console.log('Cards após buscar "Carlos":', cardsAposBusca);

await inputBusca.fill('');
await page.waitForTimeout(200);

// Selecionar primeiro cliente
const primeiroCliente = page.locator('[x-data] .space-y-2 > div').first();
const nomeCliente = await primeiroCliente.textContent().catch(() => '?');
console.log('Primeiro cliente:', nomeCliente?.trim().substring(0, 50));
await primeiroCliente.click();
await page.waitForTimeout(300);

// Verificar seleção visual
const selecionadoClass = await primeiroCliente.getAttribute('class').catch(() => '?');
console.log('Classe após seleção:', selecionadoClass?.includes('border-spark') ? 'border-spark ATIVO ✓' : 'sem borda spark ✗');

// Botão Avançar agora deve funcionar
const avancarClassAposSel = await btnAvancar.getAttribute('class').catch(() => '?');
console.log('Avançar após selecionar cliente:', avancarClassAposSel?.includes('bg-spark') ? 'bg-spark ATIVO ✓' : 'ainda desabilitado ✗');

// ── 5. STEP 1 — MODO "NOVO CLIENTE" ─────────────────────────────────────────
console.log('\n=== 5. STEP 1 — MODO NOVO CLIENTE ===');
await page.click('button:has-text("Novo cliente")');
await page.waitForTimeout(300);

const inputNome = page.locator('input[placeholder="Ex: João da Silva"]');
const inputCpf  = page.locator('input[placeholder="000.000.000-00"]');
const inputTel  = page.locator('input[placeholder="(00) 00000-0000"]');

const nomeVisible = await inputNome.isVisible();
const cpfVisible  = await inputCpf.isVisible();
const telVisible  = await inputTel.isVisible();
console.log('Campo nome visível:', nomeVisible, '| CPF:', cpfVisible, '| Telefone:', telVisible);

// Avançar sem preencher — deve bloquear
const avancarSemNovo = await btnAvancar.getAttribute('class').catch(() => '?');
console.log('Avançar sem preencher novo cliente:', avancarSemNovo?.includes('cursor-not-allowed') ? 'bloqueado ✓' : 'desbloqueado ✗');

// Preencher e ver se desbloqueia
await inputNome.fill('Teste Silva');
await inputCpf.fill('111.222.333-44');
await inputTel.fill('(11) 98765-4321');
await page.waitForTimeout(200);
const avancarComNovo = await btnAvancar.getAttribute('class').catch(() => '?');
console.log('Avançar com novo cliente preenchido:', avancarComNovo?.includes('bg-spark') ? 'habilitado ✓' : 'ainda bloqueado ✗');

// ── 6. STEP 1 — MODO "SEM CLIENTE" ──────────────────────────────────────────
console.log('\n=== 6. STEP 1 — MODO SEM CLIENTE ===');
await page.click('button:has-text("Sem cliente")');
await page.waitForTimeout(300);

const msgSemCliente = await page.locator('.leading-relaxed').first().textContent().catch(() => '?');
console.log('Mensagem sem cliente:', msgSemCliente?.trim().substring(0, 80));

const avancarSemCliente = await btnAvancar.getAttribute('class').catch(() => '?');
console.log('Avançar com sem-cliente:', avancarSemCliente?.includes('bg-spark') ? 'habilitado ✓' : 'bloqueado ✗');

// ── 7. AVANÇAR PARA STEP 2 ──────────────────────────────────────────────────
console.log('\n=== 7. AVANÇAR PARA STEP 2 ===');

// Selecionar cliente existente para ir ao step 2 limpo
await page.click('button:has-text("Já cadastrado")');
await page.waitForTimeout(300);
await page.locator('[x-data] .space-y-2 > div').first().click();
await page.waitForTimeout(200);
await btnAvancar.click();
await page.waitForTimeout(500);

await page.screenshot({ path: 'verify-nova-os-03-step2-veiculo.png' });
console.log('Screenshot: verify-nova-os-03-step2-veiculo.png');

const stepAtual = await page.evaluate(() => {
    const el = document.querySelector('[x-data]');
    return el?._x_dataStack?.[0]?.step ?? '?';
});
console.log('Step atual após avançar:', stepAtual);

// Verificar que está no step 2
const h2Step2 = await page.locator('h2.font-display').textContent().catch(() => '?');
console.log('H2 no step 2:', h2Step2?.trim());

// ── 8. STEP 2 — MODO VEÍCULO EXISTENTE ──────────────────────────────────────
console.log('\n=== 8. STEP 2 — VEÍCULO EXISTENTE ===');
await page.click('button:has-text("Veículo existente")');
await page.waitForTimeout(400);

// O cliente foi selecionado como Carlos (id=1) — deve mostrar apenas seus veículos
const labelVeiculos = await page.locator('p[x-show="clienteSelecionado"]').textContent().catch(() => '?');
console.log('Label veículos do cliente:', labelVeiculos?.trim().substring(0, 60));

const veiculoCards = await page.locator('[x-show="veiculoMode === \'existente\'"] .space-y-2 > div').count();
console.log('Cards de veículo visíveis:', veiculoCards);

// Selecionar veículo
const primeiroVeiculo = page.locator('[x-show="veiculoMode === \'existente\'"] .space-y-2 > div').first();
const placaVeiculo = await primeiroVeiculo.textContent().catch(() => '?');
console.log('Primeiro veículo:', placaVeiculo?.trim().substring(0, 50));
await primeiroVeiculo.click();
await page.waitForTimeout(300);

const btnAvancarStep2 = page.locator('button:has-text("Avançar")').first();
const avancarStep2 = await btnAvancarStep2.getAttribute('class').catch(() => '?');
console.log('Avançar após selecionar veículo:', avancarStep2?.includes('bg-spark') ? 'habilitado ✓' : 'bloqueado ✗');

// ── 9. AVANÇAR PARA STEP 3 ──────────────────────────────────────────────────
console.log('\n=== 9. AVANÇAR PARA STEP 3 ===');
await btnAvancarStep2.click();
await page.waitForTimeout(500);
await page.screenshot({ path: 'verify-nova-os-04-step3-problema.png' });
console.log('Screenshot: verify-nova-os-04-step3-problema.png');

const h2Step3 = await page.locator('h2.font-display').textContent().catch(() => '?');
console.log('H2 no step 3:', h2Step3?.trim());

// ── 10. STEP 3 — PROBLEMA ────────────────────────────────────────────────────
console.log('\n=== 10. STEP 3 — PROBLEMA ===');

// Mini-resumo chips
const chips = await page.locator('.flex.flex-wrap.gap-2 .inline-flex').allTextContents().catch(() => []);
console.log('Chips resumo:', chips.map(c => c.trim().substring(0, 30)));

// Botão submit sem texto
const btnSubmit = page.locator('button:has-text("Abrir OS")');
const submitDisabled = await btnSubmit.getAttribute('class').catch(() => '?');
console.log('Abrir OS sem texto:', submitDisabled?.includes('cursor-not-allowed') ? 'bloqueado ✓' : 'desbloqueado ✗');

// Preencher texto curto (< 10 chars) — deve manter bloqueado
const textarea = page.locator('textarea');
await textarea.fill('curto');
await page.waitForTimeout(200);
const counterText = await page.locator('p .transition-colors').textContent().catch(() => '?');
console.log('Counter com texto curto:', counterText?.trim());

const submitComCurto = await btnSubmit.getAttribute('class').catch(() => '?');
console.log('Abrir OS com < 10 chars:', submitComCurto?.includes('cursor-not-allowed') ? 'bloqueado ✓' : 'desbloqueado ✗');

// Preencher texto válido
await textarea.fill('Barulho ao frear no lado dianteiro esquerdo ao reduzir velocidade.');
await page.waitForTimeout(300);
const submitComValido = await btnSubmit.getAttribute('class').catch(() => '?');
console.log('Abrir OS com texto válido:', submitComValido?.includes('bg-spark') ? 'habilitado ✓' : 'bloqueado ✗');

// ── 11. SELECIONAR MECÂNICO ───────────────────────────────────────────────────
console.log('\n=== 11. SELECT MECÂNICO ===');
const selectMecanico = page.locator('select');
await selectMecanico.selectOption({ label: 'Marcos Ferreira' });
await page.waitForTimeout(200);
const mecSelecionado = await selectMecanico.inputValue();
console.log('Mecânico selecionado:', mecSelecionado);

// ── 12. SUBMETER FORMULÁRIO ───────────────────────────────────────────────────
console.log('\n=== 12. SUBMETER FORMULÁRIO ===');
await page.screenshot({ path: 'verify-nova-os-05-pre-submit.png' });
console.log('Screenshot: verify-nova-os-05-pre-submit.png');

await btnSubmit.click();
await page.waitForTimeout(1500);

const urlAposSubmit = page.url();
console.log('URL após submit:', urlAposSubmit);
await page.screenshot({ path: 'verify-nova-os-06-pos-submit.png' });
console.log('Screenshot: verify-nova-os-06-pos-submit.png');

const flashMsg = await page.locator('.alert, [class*="success"], [class*="bg-green"]').first().textContent().catch(() => 'não encontrado');
console.log('Flash message:', flashMsg?.trim().substring(0, 80));

// ── 13. VOLTAR E VERIFICAR NAVEGAÇÃO ENTRE STEPS ─────────────────────────────
console.log('\n=== 13. NAVEGAÇÃO VOLTAR ===');
await page.goto('http://localhost:8000/oficina/os/nova');
await page.waitForTimeout(800);

// Tentar botão Voltar no step 1 — não deve existir
const btnVoltar = page.locator('button:has-text("Voltar")');
const voltarVisible = await btnVoltar.first().isVisible().catch(() => false);
console.log('Botão Voltar visível no step 1:', voltarVisible, '(deve ser false)');

// Avançar para step 2
await page.click('button:has-text("Sem cliente")');
await page.waitForTimeout(200);
await page.locator('button:has-text("Avançar")').first().click();
await page.waitForTimeout(400);

const voltarVisibleStep2 = await btnVoltar.first().isVisible().catch(() => false);
console.log('Botão Voltar visível no step 2:', voltarVisibleStep2, '(deve ser true)');

await btnVoltar.first().click();
await page.waitForTimeout(300);
const stepDepoisVoltar = await page.evaluate(() => {
    const el = document.querySelector('[x-data]');
    return el?._x_dataStack?.[0]?.step ?? '?';
});
console.log('Step após clicar Voltar:', stepDepoisVoltar, '(deve ser 1)');

// ── 14. TESTE MOBILE (viewport 390x844) ──────────────────────────────────────
console.log('\n=== 14. MOBILE ===');
await page.setViewportSize({ width: 390, height: 844 });
await page.goto('http://localhost:8000/oficina/os/nova');
await page.waitForTimeout(800);
await page.screenshot({ path: 'verify-nova-os-07-mobile.png' });
console.log('Screenshot: verify-nova-os-07-mobile.png');

// Verificar stepper mobile
const stepperMobile = await page.locator('.sm\\:hidden.mb-5').first().isVisible().catch(() => false);
console.log('Stepper mobile visível:', stepperMobile);

// Nav mobile fixed
const navMobile = await page.locator('.sm\\:hidden.fixed.bottom-0').first().isVisible().catch(() => false);
console.log('Nav mobile fixed visível:', navMobile);

// ── RESUMO ───────────────────────────────────────────────────────────────────
console.log('\n=== ERROS JS/CONSOLE ===');
if (errors.length) {
    errors.forEach(e => console.log(e));
} else {
    console.log('Nenhum erro de console detectado ✓');
}

if (warnings.length) {
    console.log('\nWarnings:');
    warnings.forEach(w => console.log(w));
}

await browser.close();
console.log('\nDONE');
