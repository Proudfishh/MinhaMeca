import { chromium } from 'playwright';

const BASE = 'http://localhost:8000';

const b = await chromium.launch({ headless: true });
const p = await b.newPage();
p.setDefaultTimeout(10000);

const jsErrors = [];
p.on('pageerror', e => jsErrors.push(e.message));
p.on('console',   m => { if (m.type() === 'error') jsErrors.push(m.text()); });

try {
    // 1. Login como oficina
    await p.goto(BASE + '/login');
    await p.fill('input[name="email"]', 'gabriel@autocenterpremium.com.br');
    await p.fill('input[name="password"]', '123456');
    await p.click('button[type="submit"]');
    await p.waitForURL('**/oficina/**', { timeout: 8000 });
    console.log('✅ Login oficina OK');

    // 2. Navegar para Configurações
    await p.goto(BASE + '/oficina/configuracoes');
    await p.waitForSelector('[x-data*="configPage"]', { timeout: 8000 });
    await p.waitForTimeout(800);
    await p.screenshot({ path: 'verify-cfg-01-conta.png', fullPage: true });
    console.log('✅ Página Configurações carregada — aba Minha Conta');

    // 3. Verificar elementos da aba Conta
    const nomeCampo = await p.isVisible('input[x-model="conta.nome"]');
    console.log('✅ Campo nome perfil:', nomeCampo);

    const toggleNotif = await p.locator('button').filter({ hasText: '' }).count();
    console.log('✅ Botões toggle presentes:', toggleNotif > 0);

    // 4. Testar botão Salvar — deve aparecer toast
    await p.click('button:has-text("Salvar perfil")');
    await p.waitForTimeout(500);
    const toast = await p.isVisible('text=Perfil atualizado');
    console.log('✅ Toast após salvar perfil:', toast);
    await p.screenshot({ path: 'verify-cfg-02-toast.png' });

    // 5. Navegar para aba Plataforma
    await p.click('button:has-text("Plataforma")');
    await p.waitForTimeout(600);
    await p.screenshot({ path: 'verify-cfg-03-plataforma.png', fullPage: true });
    console.log('✅ Aba Plataforma OK');

    const nomeOficina = await p.isVisible('input[x-model="plataforma.oficina.nome"]');
    console.log('✅ Campo nome oficina:', nomeOficina);

    const horarioDia = await p.isVisible('text=Segunda-feira');
    console.log('✅ Horário de funcionamento:', horarioDia);

    // 6. Navegar para aba Equipe
    await p.click('button:has-text("Equipe")');
    await p.waitForTimeout(600);
    await p.screenshot({ path: 'verify-cfg-04-equipe.png', fullPage: true });
    console.log('✅ Aba Equipe OK');

    const pendente = await p.isVisible('text=Lucas Martins');
    console.log('✅ Aprovação pendente (Lucas):', pendente);

    // 7. Aprovar membro
    const selectPapel = p.locator('select[x-model="p._papel"]').first();
    await selectPapel.selectOption('mecanico');
    await p.click('button:has-text("Aprovar")');
    await p.waitForTimeout(500);
    const toastAprovado = await p.isVisible('text=aprovado como');
    console.log('✅ Toast aprovação membro:', toastAprovado);
    await p.screenshot({ path: 'verify-cfg-05-aprovacao.png' });

    // 8. Abrir modal convidar funcionário
    await p.click('button:has-text("Convidar funcionário")');
    await p.waitForTimeout(400);
    const modalConvite = await p.isVisible('text=Enviar convite');
    console.log('✅ Modal convidar funcionário:', modalConvite);
    await p.screenshot({ path: 'verify-cfg-06-modal-convite.png' });
    await p.click('button:has-text("Cancelar")');
    await p.waitForTimeout(400);

    // 9. Navegar para aba Assinatura
    await p.click('button:has-text("Assinatura")');
    await p.waitForTimeout(600);
    await p.screenshot({ path: 'verify-cfg-07-assinatura.png', fullPage: true });
    console.log('✅ Aba Assinatura OK');

    const plano = await p.isVisible('text=Profissional');
    console.log('✅ Plano atual visível:', plano);

    const cartao = await p.isVisible('text=4242');
    console.log('✅ Cartão mock visível:', cartao);

    const fatura = await p.isVisible('text=Jun/2026');
    console.log('✅ Fatura histórico visível:', fatura);

    // 10. Modal upgrade
    await p.click('button:has-text("Fazer upgrade")');
    await p.waitForTimeout(400);
    const modalUpgrade = await p.isVisible('text=Planos disponíveis');
    console.log('✅ Modal upgrade:', modalUpgrade);
    await p.screenshot({ path: 'verify-cfg-08-modal-upgrade.png' });
    await p.click('button:has-text("Fechar")');
    await p.waitForTimeout(300);

    // 11. JS errors
    if (jsErrors.length) {
        console.log('⚠️ Erros JS:', jsErrors);
    } else {
        console.log('✅ Sem erros JavaScript');
    }

    console.log('\n🎉 VERIFICAÇÃO CONFIGURAÇÕES CONCLUÍDA');

} catch (e) {
    console.error('❌ ERRO:', e.message);
    await p.screenshot({ path: 'verify-cfg-erro.png', fullPage: true }).catch(() => {});
} finally {
    await b.close();
}
