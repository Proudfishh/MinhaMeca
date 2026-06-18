import { chromium } from 'playwright';

const BASE = 'http://localhost:8000';

async function login(page) {
    await page.goto(BASE + '/login');
    await page.waitForSelector('input[name="email"]', { timeout: 6000 });
    await page.fill('input[name="email"]', 'admin@test.com');
    await page.fill('input[name="password"]', '123456');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/oficina/**', { timeout: 8000 });
}

const browser = await chromium.launch({ headless: true });
const page    = await browser.newPage();
page.setDefaultTimeout(10000);

const errors = [];
page.on('pageerror', e => errors.push(e.message));
page.on('console',   m => { if (m.type() === 'error') errors.push(m.text()); });

try {
    // 1. Login
    await login(page);
    console.log('✅ Login OK — URL:', page.url());

    // 2. Navegar para /oficina/financeiro
    await page.goto(BASE + '/oficina/financeiro');
    await page.waitForSelector('[x-data*="pendenciasPage"]', { timeout: 8000 });
    await page.waitForTimeout(800); // Alpine init
    console.log('✅ Página Pendências carregada');

    // Screenshot inicial
    await page.screenshot({ path: 'verify-pendencias-01-inicial.png', fullPage: false });

    // 3. Sidebar — checar label "Pendências"
    const sidebarLinks = await page.$$eval('nav a, aside a', els =>
        els.map(e => e.textContent.trim()).filter(t => t.length > 0)
    );
    const temPendencias = sidebarLinks.some(t => t.includes('Pendências'));
    console.log('✅ Sidebar label "Pendências":', temPendencias, '| links:', sidebarLinks.slice(0,8));

    // 4. Métricas — 4 cards
    const metricTexts = await page.$$eval('.grid .bg-white p', els =>
        els.map(e => e.textContent.trim()).filter(t => t.length > 0)
    );
    console.log('✅ Textos métricas:', metricTexts.slice(0, 8));

    // 5. Cards de pendência
    await page.waitForTimeout(300);
    const cardCount = await page.$$eval('.space-y-4 > div', els => els.length);
    console.log('✅ Cards de pendência:', cardCount);

    // 6. Badges de status
    const badges = await page.$$eval('span[style*="background"]', els =>
        els.map(e => e.textContent.trim()).filter(t => t.length > 0)
    );
    console.log('✅ Badges de status:', badges);

    // 7. Filtro "Vencidas"
    await page.click('button:has-text("Vencidas")');
    await page.waitForTimeout(400);
    const cardsVencidas = await page.$$eval('.space-y-4 > div', els => els.length);
    console.log('✅ Filtro Vencidas — cards:', cardsVencidas);
    await page.screenshot({ path: 'verify-pendencias-02-filtro-vencidas.png' });

    // 8. Filtro "Pagas"
    await page.click('button:has-text("Pagas")');
    await page.waitForTimeout(400);
    const cardsPagas = await page.$$eval('.space-y-4 > div', els => els.length);
    console.log('✅ Filtro Pagas — cards:', cardsPagas);

    // 9. Voltar "Todas" + busca
    await page.click('button:has-text("Todas")');
    await page.waitForTimeout(300);
    await page.fill('input[placeholder*="Buscar"]', 'Carlos');
    await page.waitForTimeout(400);
    const cardsBusca = await page.$$eval('.space-y-4 > div', els => els.length);
    console.log('✅ Busca "Carlos" — cards:', cardsBusca);

    // Limpar busca
    await page.fill('input[placeholder*="Buscar"]', '');
    await page.waitForTimeout(200);

    // 10. Abrir modal Registrar Pagamento
    const btnRegistrar = page.locator('button:has-text("Registrar pagamento")').first();
    const temBotaoPag = await btnRegistrar.isVisible();
    console.log('✅ Botão "Registrar pagamento" visível:', temBotaoPag);

    if (temBotaoPag) {
        await btnRegistrar.click();
        await page.waitForTimeout(600);
        const modalPagAberto = await page.isVisible('h3:has-text("Registrar pagamento")').catch(() => false);
        console.log('✅ Modal pagamento aberto:', modalPagAberto);
        await page.screenshot({ path: 'verify-pendencias-03-modal-pagamento.png' });

        // Confirmar
        const btnConf = page.locator('button:has-text("Confirmar")');
        if (await btnConf.isVisible()) {
            await btnConf.click();
            await page.waitForTimeout(600);
            const toast1 = await page.isVisible('text=Pagamento registrado').catch(() => false);
            console.log('✅ Toast pagamento registrado:', toast1);
            await page.screenshot({ path: 'verify-pendencias-04-pos-pagamento.png' });
        }
    }

    // 11. Modal Nova Pendência — modo OS
    await page.click('button:has-text("Nova Pendência")');
    await page.waitForTimeout(600);
    const modalNovaAberto = await page.isVisible('h3:has-text("Nova Pendência")').catch(() => false);
    console.log('✅ Modal Nova Pendência aberto:', modalNovaAberto);
    await page.screenshot({ path: 'verify-pendencias-05-modal-nova-os.png' });

    // 12. Modo avulso
    await page.click('button:has-text("Avulsa")');
    await page.waitForTimeout(300);
    await page.fill('input[placeholder*="Nome do cliente"]', 'Joaquim Pereira');
    await page.fill('[x-model="nf.valor_avulso"]', '300');
    await page.fill('input[placeholder*="Ex: Adiantamento"]', 'Peças de freio');
    await page.screenshot({ path: 'verify-pendencias-06-modal-avulso.png' });

    // Avançar etapa 2
    const btnAvancar = page.locator('button:has-text("Avançar")');
    const avancarHabilitado = !(await btnAvancar.isDisabled().catch(() => true));
    console.log('✅ Botão Avançar habilitado:', avancarHabilitado);
    await btnAvancar.click();
    await page.waitForTimeout(500);
    const etapa2Visivel = await page.isVisible('text=Etapa 2 de 2').catch(() => false);
    console.log('✅ Etapa 2 aparece:', etapa2Visivel);
    await page.screenshot({ path: 'verify-pendencias-07-etapa2.png' });

    // Criar
    const btnCriar = page.locator('button:has-text("Criar Pendência")');
    if (await btnCriar.isVisible()) {
        await btnCriar.click();
        await page.waitForTimeout(600);
        const toast2 = await page.isVisible('text=Pendência criada').catch(() => false);
        console.log('✅ Toast nova pendência criada:', toast2);
        await page.screenshot({ path: 'verify-pendencias-08-nova-criada.png' });
    }

    // 13. ESC fecha modal
    await page.click('button:has-text("Nova Pendência")');
    await page.waitForTimeout(400);
    await page.keyboard.press('Escape');
    await page.waitForTimeout(400);
    const aindaAberto = await page.isVisible('h3:has-text("Nova Pendência")').catch(() => false);
    console.log('✅ ESC fecha modal:', !aindaAberto);

    // 14. Erros JS
    if (errors.length) {
        console.log('⚠️ Erros JS console:', errors);
    } else {
        console.log('✅ Sem erros de console JavaScript');
    }

    await page.screenshot({ path: 'verify-pendencias-09-final.png', fullPage: true });
    console.log('\n🎉 VERIFICAÇÃO CONCLUÍDA');

} catch (e) {
    console.error('❌ ERRO:', e.message);
    await page.screenshot({ path: 'verify-pendencias-erro.png', fullPage: true }).catch(() => {});
} finally {
    await browser.close();
}
