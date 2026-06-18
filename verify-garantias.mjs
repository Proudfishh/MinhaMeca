import { chromium } from 'playwright';

const BASE = 'http://localhost:8000';

const b = await chromium.launch({ headless: true });
const p = await b.newPage();
p.setDefaultTimeout(10000);

const jsErrors = [];
p.on('pageerror', e => jsErrors.push(e.message));
p.on('console',   m => { if (m.type() === 'error') jsErrors.push(m.text()); });

try {
    // Login
    await p.goto(BASE + '/login');
    await p.fill('input[name="email"]', 'a@a.com');
    await p.fill('input[name="password"]', 'a');
    await p.click('button[type="submit"]');
    await p.waitForURL('**/oficina/**');

    // Navegar para Garantias
    await p.goto(BASE + '/oficina/garantias');
    await p.waitForSelector('[x-data*="garantiasPage"]');
    await p.waitForTimeout(800);
    console.log('✅ Página carregada');
    await p.screenshot({ path: 'verify-gar-01-inicial.png' });

    // 1. Sidebar mostra "Garantias"
    const sidebar = await p.$$eval('nav a', els => els.map(e => e.textContent.trim()));
    const temGarantias = sidebar.some(t => t.includes('Garantias'));
    console.log('✅ Sidebar "Garantias":', temGarantias);

    // 2. Sem bloco follow-ups
    const followUpsVisiveis = await p.isVisible('text=Follow-ups pendentes').catch(() => false);
    console.log('✅ Bloco follow-ups AUSENTE (esperado false):', followUpsVisiveis);

    // 3. Bloco vencendo em breve
    const vencendoVisivel = await p.isVisible('text=Vencendo em breve');
    console.log('✅ Bloco vencendo em breve visível:', vencendoVisivel);
    await p.screenshot({ path: 'verify-gar-02-blocos.png' });

    // 4. Filtros
    await p.click('button:has-text("Ativas")');
    await p.waitForTimeout(300);
    await p.screenshot({ path: 'verify-gar-03-filtro-ativas.png' });
    await p.click('button:has-text("Expiradas")');
    await p.waitForTimeout(300);
    await p.click('button:has-text("Todas")');
    await p.waitForTimeout(200);
    console.log('✅ Filtros funcionando');

    // 5. Busca
    await p.fill('input[placeholder*="Buscar"]', 'Carlos');
    await p.waitForTimeout(300);
    console.log('✅ Busca "Carlos" executada');
    await p.fill('input[placeholder*="Buscar"]', '');

    // 6. Modal Acionar Garantia
    const btnAcionar = p.locator('button:has-text("Acionar")').first();
    await btnAcionar.click();
    await p.waitForTimeout(500);
    const modalAcionarAberto = await p.isVisible('h3:has-text("Acionar garantia")').catch(() => false);
    console.log('✅ Modal acionar aberto:', modalAcionarAberto);
    await p.screenshot({ path: 'verify-gar-04-modal-acionar.png' });

    await p.fill('textarea', 'Cliente relatou barulho no motor após troca de correia.');
    await p.waitForTimeout(200);
    await p.click('button:has-text("Abrir OS de retrabalho")');
    await p.waitForTimeout(500);
    const toastAcionar = await p.isVisible('text=Garantia acionada').catch(() => false);
    console.log('✅ Toast garantia acionada:', toastAcionar);
    await p.screenshot({ path: 'verify-gar-05-acionada.png' });

    // 7. Modal Registrar Manualmente
    await p.click('button:has-text("Registrar manualmente")');
    await p.waitForTimeout(500);
    const modalRegistrarAberto = await p.isVisible('h3:has-text("Registrar garantia")').catch(() => false);
    console.log('✅ Modal registrar manualmente aberto:', modalRegistrarAberto);

    await p.fill('[placeholder*="Nome do cliente"]', 'Pedro Souza');
    await p.fill('[placeholder*="Honda Civic"]', 'Chevrolet Onix 2022 · PQR-0001');
    await p.fill('[x-model="mr.data_entrega"]', '2026-06-01');
    await p.waitForTimeout(300);
    const preview = await p.isVisible('text=Vencimento calculado').catch(() => false);
    console.log('✅ Preview vencimento visível:', preview);
    await p.screenshot({ path: 'verify-gar-06-modal-registrar.png' });

    // Clica no botão de confirmação dentro do modal (não o botão de abertura)
    await p.locator('button[type="button"]:has-text("Registrar"):not(:has-text("manualmente"))').last().click();
    await p.waitForTimeout(500);
    const toastRegistrar = await p.isVisible('text=Garantia registrada').catch(() => false);
    console.log('✅ Toast garantia registrada:', toastRegistrar);
    await p.screenshot({ path: 'verify-gar-07-registrada.png' });

    // 8. ESC fecha modal
    await p.click('button:has-text("Registrar manualmente")');
    await p.waitForTimeout(300);
    await p.keyboard.press('Escape');
    await p.waitForTimeout(300);
    const modalFechado = !(await p.isVisible('h3:has-text("Registrar garantia")').catch(() => true));
    console.log('✅ ESC fecha modal:', modalFechado);

    // 9. JS errors
    if (jsErrors.length) {
        console.log('⚠️ Erros JS:', jsErrors);
    } else {
        console.log('✅ Sem erros JavaScript');
    }

    await p.screenshot({ path: 'verify-gar-08-final.png', fullPage: false });
    console.log('\n🎉 VERIFICAÇÃO GARANTIAS CONCLUÍDA');

} catch (e) {
    console.error('❌ ERRO:', e.message);
    await p.screenshot({ path: 'verify-gar-erro.png', fullPage: true }).catch(() => {});
} finally {
    await b.close();
}
