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
    await p.goto(BASE + '/oficina/financeiro');
    await p.waitForSelector('[x-data*="pendenciasPage"]');
    await p.waitForTimeout(800);
    console.log('✅ Página carregada');

    // 1. Botão pill — verificar que não é mais texto simples
    const btnRegistrar = p.locator('button:has-text("Registrar")').first();
    const btnClasses = await btnRegistrar.getAttribute('class');
    const ePill = btnClasses?.includes('rounded-full') && btnClasses?.includes('bg-spark');
    console.log('✅ Botão pill estilizado:', ePill);
    await p.screenshot({ path: 'verify-v2-01-lista.png' });

    // 2. Abrir modal — pagamento PARCIAL (R$400 de R$900 parcela 2)
    await btnRegistrar.click();
    await p.waitForTimeout(500);
    const modalH3 = await p.isVisible('h3:has-text("Registrar pagamento")');
    console.log('✅ Modal aberto:', modalH3);

    // Checar "Valor original da parcela" helper text
    const helperVisivel = await p.isVisible('text=Valor original da parcela');
    console.log('✅ Helper "Valor original" visível:', helperVisivel);

    // Limpar valor e digitar parcial
    await p.fill('input[x-model="mpValor"]', '400');
    await p.waitForTimeout(400);

    // Checar se seção "Saldo restante" aparece
    const saldoSection = await p.isVisible('text=Saldo restante:');
    console.log('✅ Seção saldo restante aparece (400 < original):', saldoSection);
    await p.screenshot({ path: 'verify-v2-02-saldo-restante.png' });

    // Opção "Agendar" deve estar selecionada por padrão
    const opcaoAgendar = await p.isVisible('text=Agendar pagamento do restante');
    console.log('✅ Opção "Agendar" visível:', opcaoAgendar);

    // Campo de data de vencimento aparece
    const dataRestanteInput = p.locator('[x-model="mpDataRestante"]');
    const dataRestanteVisivel = await dataRestanteInput.isVisible();
    console.log('✅ Campo data restante visível:', dataRestanteVisivel);

    // Confirmar deve estar desabilitado sem a data restante
    const btnConfirmar = p.locator('button:has-text("Confirmar")');
    const confirmarDesabilitado = await btnConfirmar.isDisabled();
    console.log('✅ Confirmar desabilitado sem data restante:', confirmarDesabilitado);

    // Preencher data restante
    const futuro = new Date();
    futuro.setDate(futuro.getDate() + 30);
    const dataFutura = futuro.toISOString().split('T')[0];
    await dataRestanteInput.fill(dataFutura);
    await p.waitForTimeout(200);

    const confirmarHabilitado = !(await btnConfirmar.isDisabled());
    console.log('✅ Confirmar habilitado após data restante:', confirmarHabilitado);
    await p.screenshot({ path: 'verify-v2-03-agendar-preenchido.png' });

    // Confirmar pagamento parcial com agendamento
    await btnConfirmar.click();
    await p.waitForTimeout(600);
    const toastParcial = await p.isVisible('text=Pagamento parcial registrado').catch(() => false);
    const toastNormal  = await p.isVisible('text=Pagamento registrado').catch(() => false);
    console.log('✅ Toast pagamento parcial:', toastParcial || toastNormal);
    await p.screenshot({ path: 'verify-v2-04-pos-parcial.png' });

    // 3. Testar opção "Dar como quitado"
    const btnRegistrar2 = p.locator('button:has-text("Registrar")').first();
    await btnRegistrar2.click();
    await p.waitForTimeout(500);

    await p.fill('input[x-model="mpValor"]', '200');
    await p.waitForTimeout(400);

    const saldoSection2 = await p.isVisible('text=Saldo restante:');
    console.log('✅ Saldo restante aparece para segundo teste:', saldoSection2);

    // Clicar em "Dar como quitado"
    const opcaoQuitar = p.locator('text=Dar como quitado');
    await opcaoQuitar.click();
    await p.waitForTimeout(300);
    await p.screenshot({ path: 'verify-v2-05-quitar.png' });

    const confirmarHabilitadoQuitar = !(await btnConfirmar.isDisabled());
    console.log('✅ Confirmar habilitado com "Dar como quitado":', confirmarHabilitadoQuitar);

    await btnConfirmar.click();
    await p.waitForTimeout(600);
    const toastQuitar = await p.isVisible('text=Pagamento registrado').catch(() => false);
    console.log('✅ Toast após quitar:', toastQuitar);
    await p.screenshot({ path: 'verify-v2-06-pos-quitar.png' });

    // 4. Pagamento integral (sem saldo) — seção não deve aparecer
    const btnsRegistrar = p.locator('button:has-text("Registrar")');
    const count = await btnsRegistrar.count();
    if (count > 0) {
        await btnsRegistrar.first().click();
        await p.waitForTimeout(500);
        // Não alterar o valor (é o valor original)
        const saldoSemParcial = await p.isVisible('text=Saldo restante:').catch(() => false);
        console.log('✅ Seção saldo NÃO aparece para pagamento integral:', !saldoSemParcial);
        await p.keyboard.press('Escape');
    }

    // 5. JS errors
    if (jsErrors.length) {
        console.log('⚠️ Erros JS:', jsErrors);
    } else {
        console.log('✅ Sem erros JavaScript');
    }

    await p.screenshot({ path: 'verify-v2-07-final.png', fullPage: false });
    console.log('\n🎉 VERIFICAÇÃO v2 CONCLUÍDA');

} catch (e) {
    console.error('❌ ERRO:', e.message);
    await p.screenshot({ path: 'verify-v2-erro.png', fullPage: true }).catch(() => {});
} finally {
    await b.close();
}
