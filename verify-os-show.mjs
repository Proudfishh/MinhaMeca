import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: true });
const ctx = await browser.newContext();
const page = await ctx.newPage();

// Login first
await page.goto('http://localhost:8000/oficina/login');
await page.fill('input[name="email"]', 'admin@oficina.com');
await page.fill('input[name="password"]', '123456');
await page.click('button[type="submit"]');
await page.waitForTimeout(1000);

// Go to OS detail (servico stage)
await page.goto('http://localhost:8000/oficina/os/OS-2025-0047');
await page.waitForTimeout(1000);

const title  = await page.title();
const h2     = await page.locator('h2.font-display.font-bold').first().textContent().catch(() => '?');
const badge  = await page.locator('.inline-flex.items-center.gap-1\\.5').first().textContent().catch(() => '?');
const url    = page.url();
const errors = [];
page.on('console', m => { if (m.type() === 'error') errors.push(m.text()); });

console.log('URL:', url);
console.log('TITLE:', title);
console.log('Cliente h2:', h2?.trim());
console.log('Badge etapa:', badge?.trim());

await page.screenshot({ path: 'verify-os-show.png', fullPage: true });

// Also check pecas stage
await page.goto('http://localhost:8000/oficina/os/OS-2025-0048');
await page.waitForTimeout(800);
await page.screenshot({ path: 'verify-os-pecas.png', fullPage: true });

// Check finalizacao stage
await page.goto('http://localhost:8000/oficina/os/OS-2025-0046');
await page.waitForTimeout(800);
await page.screenshot({ path: 'verify-os-finalizacao.png', fullPage: true });

await browser.close();
console.log('JS errors:', errors.length ? errors : 'none');
console.log('DONE');
