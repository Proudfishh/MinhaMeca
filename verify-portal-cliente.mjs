import { chromium } from 'playwright';

const BASE = 'http://localhost:8000';

const b = await chromium.launch({ headless: true });
const p = await b.newPage();
p.setDefaultTimeout(10000);

const jsErrors = [];
p.on('pageerror', e => jsErrors.push(e.message));
p.on('console',   m => { if (m.type() === 'error') jsErrors.push(m.text()); });

try {
    // 1. Login como cliente — qualquer clique entra direto
    await p.goto(BASE + '/login');
    await p.click('button:has-text("Cliente")');
    await p.waitForTimeout(400);
    await p.click('button:has-text("Ver status do meu carro")');
    await p.waitForURL('**/cliente/**', { timeout: 8000 });
    console.log('✅ Login cliente → URL:', p.url());
    await p.screenshot({ path: 'verify-cli-01-login.png' });

    // 2. Portal carregado
    await p.waitForSelector('[x-data*="portalCliente"]');
    await p.waitForTimeout(800);
    await p.screenshot({ path: 'verify-cli-02-portal.png', fullPage: true });

    const emAndamento = await p.isVisible('text=Em andamento');
    console.log('✅ Bloco "Em andamento":', emAndamento);

    const secaoHistorico = await p.isVisible('text=Histórico');
    console.log('✅ Bloco "Histórico":', secaoHistorico);

    // 3. Cards de OS — collapsed por padrão (sem barra de progresso visível)
    const progressoVisivel = await p.isVisible('text=Andamento').catch(() => false);
    console.log('✅ Progresso oculto (collapsed):', !progressoVisivel);

    // 4. Clicar no card para expandir
    const cardOS = p.locator('[x-data="{ aberto: false }"]').first();
    await cardOS.click();
    await p.waitForTimeout(400);
    const progressoAberto = await p.isVisible('text=Andamento').catch(() => false);
    console.log('✅ Progresso visível após expandir:', progressoAberto);

    const servicosVisiveis = await p.isVisible('text=Serviços').catch(() => false);
    console.log('✅ Serviços visíveis após expandir:', servicosVisiveis);
    await p.screenshot({ path: 'verify-cli-03-card-expandido.png' });

    // 5. Fechar clicando novamente
    await cardOS.click();
    await p.waitForTimeout(300);
    const progressoFechado = await p.isVisible('text=Andamento').catch(() => false);
    console.log('✅ Progresso oculto após fechar:', !progressoFechado);

    // 6. Card histórico — expandir
    const cardsHist = await p.$$('[x-data="{ aberto: false }"]');
    console.log('✅ Cards totais (ativas + histórico):', cardsHist.length);
    const ultimoCard = cardsHist[cardsHist.length - 1];
    await ultimoCard.click();
    await p.waitForTimeout(400);
    const detalhesHist = await p.isVisible('text=Serviços realizados').catch(() => false);
    console.log('✅ Histórico expandido:', detalhesHist);
    await p.screenshot({ path: 'verify-cli-04-historico.png' });

    // 7. Logout
    await p.click('button:has-text("Sair")');
    await p.waitForURL('**/login**', { timeout: 5000 });
    console.log('✅ Logout OK');

    // 8. Proteção — acesso sem auth redireciona
    await p.goto(BASE + '/cliente/veiculos');
    await p.waitForURL('**/login**', { timeout: 5000 });
    console.log('✅ Proteção auth.cliente funciona');

    // 9. JS errors
    if (jsErrors.length) {
        console.log('⚠️ Erros JS:', jsErrors);
    } else {
        console.log('✅ Sem erros JavaScript');
    }

    console.log('\n🎉 VERIFICAÇÃO PORTAL CLIENTE CONCLUÍDA');

} catch (e) {
    console.error('❌ ERRO:', e.message);
    await p.screenshot({ path: 'verify-cli-erro.png', fullPage: true }).catch(() => {});
} finally {
    await b.close();
}
