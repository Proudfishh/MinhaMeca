import { chromium } from 'playwright';

const BASE = 'http://localhost:8000';

const b = await chromium.launch({ headless: true });
const p = await b.newPage();
p.setDefaultTimeout(10000);

const jsErrors = [];
p.on('pageerror', e => jsErrors.push(e.message));
p.on('console',   m => { if (m.type() === 'error') jsErrors.push(m.text()); });

// Helpers escoped ao formulário cliente (único com CPF)
const cpfInput   = () => p.locator('input[name="cpf"][placeholder*="000"]').first();
const emailInput = () => p.locator('input[placeholder="seuemail@email.com"]').first();

try {
    // 1. Abrir login → mudar para tab cliente
    await p.goto(BASE + '/login');
    await p.waitForSelector('text=MinhaMeca');
    await p.click('button:has-text("Cliente")');
    await p.waitForTimeout(500);

    const btnVerStatus = await p.isVisible('button:has-text("Ver status do meu carro")');
    console.log('✅ Formulário login cliente visível:', btnVerStatus);
    await p.screenshot({ path: 'verify-cli-01-login.png' });

    // 2. Login inválido → erro
    await cpfInput().fill('000.000.000-00');
    await emailInput().fill('invalido@email.com');
    await p.click('button:has-text("Ver status do meu carro")');
    await p.waitForTimeout(600);
    const erroLogin = await p.isVisible('text=CPF ou e-mail').catch(() => false);
    console.log('✅ Erro login inválido:', erroLogin);
    await p.screenshot({ path: 'verify-cli-02-erro-login.png' });

    // 3. Login correto → portal
    await p.click('button:has-text("Cliente")');
    await p.waitForTimeout(300);
    await cpfInput().fill('123.456.789-00');
    await emailInput().fill('carlos@email.com');
    await p.click('button:has-text("Ver status do meu carro")');
    await p.waitForURL('**/cliente/**', { timeout: 8000 });
    console.log('✅ Login bem-sucedido → URL:', p.url());

    // 4. Portal carregado
    await p.waitForSelector('[x-data*="portalCliente"]');
    await p.waitForTimeout(1000);
    await p.screenshot({ path: 'verify-cli-03-portal.png', fullPage: true });

    const headerTxt = await p.textContent('header').catch(() => '');
    console.log('✅ Header com nome:', headerTxt.includes('Auto Center') || headerTxt.includes('Olá'));

    const emAndamento = await p.isVisible('text=Em andamento');
    console.log('✅ Bloco "Em andamento" visível:', emAndamento);

    const secaoHistorico = await p.isVisible('text=Histórico');
    console.log('✅ Bloco "Histórico" visível:', secaoHistorico);

    // 5. Badge de etapa presente
    const badges = await p.$$eval('span[style*="background"]', els =>
        [...new Set(els.map(e => e.textContent.trim()).filter(t => t.length > 0))]
    );
    console.log('✅ Badges de etapa:', badges);

    // 6. Seção serviços
    const temServicos = await p.isVisible('text=Serviços').catch(() => false);
    console.log('✅ Seção serviços visível:', temServicos);

    // 7. Card histórico — expandir
    await p.screenshot({ path: 'verify-cli-04-historico.png' });
    const cardsHist = await p.$$('[x-data="{ aberto: false }"]');
    console.log('✅ Cards de histórico:', cardsHist.length);
    if (cardsHist.length > 0) {
        await cardsHist[0].click();
        await p.waitForTimeout(500);
        const detalhes = await p.isVisible('text=Serviços realizados').catch(() => false);
        console.log('✅ Histórico expandido:', detalhes);
        await p.screenshot({ path: 'verify-cli-05-historico-expandido.png' });
    }

    // 8. Logout
    await p.click('button:has-text("Sair")');
    await p.waitForURL('**/login**', { timeout: 5000 });
    console.log('✅ Logout OK → URL:', p.url());

    // 9. Proteção — acesso sem auth redireciona
    await p.goto(BASE + '/cliente/veiculos');
    await p.waitForURL('**/login**', { timeout: 5000 });
    console.log('✅ Proteção auth.cliente funciona');

    // 10. JS errors
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
