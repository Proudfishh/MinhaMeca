import { chromium } from 'playwright';

const b = await chromium.launch({ headless: true });
const p = await b.newPage();

await p.goto('http://localhost:8000/login');
await p.fill('input[name="email"]', 'a@a.com');
await p.fill('input[name="password"]', 'a');
await p.click('button[type="submit"]');
await p.waitForURL('**/oficina/**');
await p.goto('http://localhost:8000/oficina/financeiro');
await p.waitForSelector('[x-data*="pendenciasPage"]');
await p.waitForTimeout(1000);

const items = await p.$$eval('.space-y-4 > *', els =>
    els.map(e => ({ tag: e.tagName, cls: e.className?.substring(0, 50) ?? '' }))
);
console.log('Itens diretos em .space-y-4:', items.length);
console.log(JSON.stringify(items, null, 2));

await b.close();
