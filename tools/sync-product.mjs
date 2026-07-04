// One-off content sync: updates ONE product in the live database from the seed
// file (server/data/products.js) by slug — without touching any other product,
// enquiries, admin or settings. Safe to run on an already-installed site.
//
// Usage (run from the project root, e.g. in the Hostinger Node app terminal):
//   node tools/sync-product.mjs                        # default slug below
//   node tools/sync-product.mjs ozone-generator-digital
//   node tools/sync-product.mjs slug-a slug-b ...       # sync several
//
// It matches products by slug, so the product's URL and identity are preserved.
// It will NOT create products that don't already exist (use the admin panel for
// brand-new products) — this only refreshes existing ones from the seed.

import { Products } from "../server/db.js";
import { seedProducts } from "../server/data/products.js";

const DEFAULT_SLUGS = ["ozone-generator-digital"];
const slugs = process.argv.slice(2).length ? process.argv.slice(2) : DEFAULT_SLUGS;

let updated = 0;
for (const slug of slugs) {
  const seed = seedProducts.find((p) => p.slug === slug);
  if (!seed) {
    console.log(`  ✗ ${slug}: not found in seed file — skipped`);
    continue;
  }
  const existing = Products.bySlug(slug);
  if (!existing) {
    console.log(`  ✗ ${slug}: not in database (add it in /admin first) — skipped`);
    continue;
  }
  Products.update(existing.id, seed);
  console.log(`  ✓ ${slug}: updated → "${seed.title}" (${seed.model_number})`);
  updated++;
}

console.log(`\nDone. ${updated} product(s) synced from the seed file.`);
