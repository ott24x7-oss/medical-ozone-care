# Medical Ozone Care — Full-Stack Website

Professional medical-equipment website for **Medical Ozone Care** with product
information, pricing, quotation/enquiry capture, distributor enquiries, a complete
**admin dashboard**, email notifications, WhatsApp integration and SEO.

- **Frontend** — responsive landing + product pages (plain HTML/CSS/JS, no build step)
- **Backend** — Node.js + Express REST API
- **Database** — built-in **SQLite** (`node:sqlite`, no native build tools)
- **Admin** — email/password login (hashed), products CRUD, enquiry management,
  brochures, contact/SEO/legal settings
- **Product mockups** — scalable **SVG** illustrations (recreated from the brochure/photos)

> **Medical disclaimer:** This website provides medical equipment information only.
> Use of medical ozone equipment should be under qualified medical supervision and
> applicable local regulations. No medical-cure claims are made anywhere on the site.

---

## 1. Quick start (local)

> Requires **Node.js 22.9+** (tested on Node 24). No database server needed.

```bash
npm install          # installs express + nodemailer
npm start            # → http://localhost:3000
```

On first run the database is created and seeded with 9 products and an admin account.

- Dev mode (auto-reload + loads `.env` if present): `npm run dev`
- With a local `.env` file: `npm run start:prod`

---

## 2. Admin dashboard

Open **http://localhost:3000/admin**

| | |
|---|---|
| **Default email** | `medicalozonecare@gmail.com` |
| **Default password** | `admin12345` &nbsp;← change this |

Change credentials by setting `ADMIN_EMAIL` / `ADMIN_PASSWORD` **before the first run**
(the admin is seeded once). Passwords are hashed with scrypt; sessions use a signed
bearer token sent in the `Authorization` header (CSRF-safe).

Admin sections: **Dashboard** (stats), **Products** (add/edit/delete + image &
brochure upload, active/inactive), **Enquiries** (status New→Contacted→Quoted→Closed,
admin notes, delete), **Brochures** (upload per product + download counts),
**Contact Settings**, **SEO Settings**, **Legal Pages** (edit the 5 legal documents).

---

## 3. Environment variables

Copy `.env.example` → `.env` and edit. All are optional locally (sensible defaults),
but you should set `SESSION_SECRET` and `ADMIN_PASSWORD` for production.

| Variable | Purpose |
|---|---|
| `PORT` | Server port (default 3000) |
| `ADMIN_EMAIL` / `ADMIN_PASSWORD` | Seed admin (first run only) |
| `SESSION_SECRET` | Signs admin tokens — use a long random string |
| `SMTP_HOST/PORT/USER/PASS/SECURE/FROM` | SMTP for email notifications |
| `NOTIFY_TO` | Comma-separated recipients for new-enquiry emails |

---

## 4. How enquiry emails work

When a visitor submits any form, the enquiry is **saved to the database** and a
notification is sent to `NOTIFY_TO` (defaults to both business emails) with subject
`New Medical Ozone Care Enquiry - <type>`.

- If `SMTP_*` is **not** configured, the enquiry is still saved and the notification
  is **logged to the server console** (nothing breaks).
- For Gmail: enable 2FA, create an **App Password**, and use it as `SMTP_PASS`.

All enquiries are always visible in the admin **Enquiries** tab regardless of email.

---

## 5. How to add / edit products

1. Sign in to `/admin` → **Products** → **+ Add product** (or **Edit**).
2. Fill in title, model, category, price, description, etc.
   - **Highlights** — one per line
   - **Features** — one per line as `icon | title | text` (icons: shield, sliders,
     atom, globe, plug, badge, gauge, droplet, wind, settings, flask…)
   - **Specifications** — one per line as `key | value`
   - **Accessories** — one per line as `name | qty`
   - **Images** — upload a photo or add a path (e.g. a built-in SVG mockup)
3. Set **active/inactive** and **Save**. Public pages/API read from the database live.

Products are the single source of truth in SQLite. Initial catalogue is seeded from
`server/data/products.js`.

---

## 6. API reference

**Public**

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/health` | Health check |
| GET | `/api/company` | Company details + disclaimer |
| GET | `/api/categories` · `/api/enquiry-types` | Lookups |
| GET | `/api/products` | List (`?category=`, `?q=`, `?featured=true`) |
| GET | `/api/products/:slug` | Single product |
| GET | `/api/concentration-table` | AOT-MD-520 chart |
| GET | `/api/settings/public` | Contact / SEO / legal content |
| GET | `/api/brochure/:slug` | Download brochure (tracked) → redirect |
| POST | `/api/enquiries` | Submit enquiry (rate-limited, honeypot) |

**Admin** (header `Authorization: Bearer <token>`)

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/admin/login` | → `{ token }` |
| GET | `/api/admin/me` · `/api/admin/stats` | Session / dashboard stats |
| GET/POST | `/api/admin/products` | List (incl. inactive) / create |
| PUT/DELETE | `/api/admin/products/:id` | Update / delete |
| POST | `/api/admin/upload` | Base64 image/PDF upload |
| GET | `/api/admin/enquiries` | List enquiries |
| PATCH/DELETE | `/api/admin/enquiries/:id` | Status + note / delete |
| GET | `/api/admin/downloads` | Brochure download log |
| GET/PUT | `/api/admin/settings` | Contact / SEO / legal |

**SEO:** `/sitemap.xml` (auto from products) · `/robots.txt`

---

## 7. Database tables (SQLite)

- **products** — slug, title, model_number, category, price, price_value, featured,
  status, sort_order, has_concentration_table, warranty, tagline, short/full
  description, highlights, features, specifications, accessories, items, images,
  brochure_pdf, timestamps
- **enquiries** — name, phone, email, interested_product, enquiry_type, message,
  status, admin_note, source, timestamps
- **admins** — email, password_hash (scrypt), name
- **downloads** — product_slug, brochure, ip, created_at
- **settings** — key/value (contact, seo, legal overrides)

DB file: `server/data/ozonecare.db` (auto-created, git-ignored). Delete it to reseed.

---

## 8. Project structure

```
server/
  server.js        Express app: public + admin API, SEO, static, pages
  db.js            SQLite schema + queries + seeding
  auth.js          scrypt hashing + HMAC bearer tokens
  mailer.js        nodemailer (console fallback)
  data/
    products.js    seed catalogue + company + concentration table
    defaults.js    default contact / SEO / legal content
public/            frontend (index, products, product, instructions, faq,
                   about, contact, legal, admin, 404) + css/ js/ assets/
tools/gen_svgs.py  regenerates all SVG product mockups
```

---

## 9. Security

Header-based bearer tokens (no cookies → CSRF-safe) · scrypt-hashed passwords ·
input validation + length-capping + sanitisation · per-IP rate limiting on writes ·
form honeypot · admin routes auth-guarded · `x-powered-by` disabled · secrets via env.

---

## 10a. Deploy on Hostinger (Node.js)

> **Node version matters.** This app uses the built-in `node:sqlite`, which needs
> **Node.js 22.5+** (Node 24 recommended). In Hostinger's Node setup, pick the highest
> available Node version. If only Node ≤ 20 is offered, either (a) switch the DB layer to
> MySQL, or (b) use the **PHP version** in [`php/`](php/) instead (it's built for Hostinger
> shared hosting — see `php/README.md`).

1. hPanel → **Advanced → Node.js** (or **Setup Node.js App**) → **Create application**.
2. **Application root**: the folder you upload this repo to · **Startup file**: `server/server.js`
   · **Node version**: 22+ .
3. Pull the code: use hPanel **Git** (paste this repo's URL + branch `main`), or upload the
   files. Then run **`npm install`** (NPM Install button or terminal).
4. **Environment variables** (in the Node app UI): set `ADMIN_EMAIL`, `ADMIN_PASSWORD`
   (⚠ change from the default!), `SESSION_SECRET`, and optionally `SMTP_*` / `NOTIFY_TO`.
   `PORT` is provided by Hostinger automatically.
5. Start the app. The SQLite DB self-creates and seeds on first run under `server/data/`
   (ensure that folder is writable / persisted).

---

## 10. Deploy on Railway

1. Push this repo to GitHub (see §11).
2. On [railway.app](https://railway.app): **New Project → Deploy from GitHub repo**.
3. Railway auto-detects Node. Set **Build**: `npm install` · **Start**: `npm start`.
4. **Variables** tab — add `SESSION_SECRET`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`, and the
   `SMTP_*` / `NOTIFY_TO` values. (Railway injects these into `process.env`.)
5. **Networking → Generate Domain** to get a public URL.
6. **Persisting data:** SQLite writes to `server/data/`. Add a **Volume** mounted at
   `/app/server/data` so the database and uploads survive redeploys. (For higher scale,
   swap to Railway Postgres — the storage layer in `db.js` is isolated for this.)

Works the same on **Render** (Web Service, Build `npm install`, Start `npm start`,
add a Disk at `server/data`). For a generic VPS/Hostinger: `npm install`, set env,
run with a process manager (`pm2 start npm --name ozone -- start`) behind Nginx.

---

## 11. Git: commit & push

```bash
git init
git add .
git commit -m "Medical Ozone Care website (full-stack: products, admin, enquiries)"
git branch -M main
git remote add origin https://github.com/<you>/medical-ozone-care.git
git push -u origin main
```

---

## 12. Replace mockups with real photos

Product images are scalable **SVG mockups** created from the brochure/photos. To use
real photography: upload images per product in **Admin → Products → Edit → Images**,
or drop files into `public/assets/img/products/` and set their paths. Regenerate the
SVGs anytime with `npm run svgs` (requires Python + Pillow). Comments marking where
real photos can replace mockups are in `tools/gen_svgs.py` and `server/data/products.js`.

---

## Contact (business)

**Medical Ozone Care** · Shekhar Pathak
B-87, Madhu Vihar, Uttam Nagar, New Delhi – 110059, India
📞 +91 99588 03980 · ✉️ shekharaiims@gmail.com · medicalozonecare@gmail.com
🌐 www.medicalozonecare.co.in
