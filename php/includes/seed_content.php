<?php
/**
 * Every editable site text. Each row: [ckey, group, label, type, value]
 * type: text | textarea | html | json   (json values are PHP arrays, encoded on insert)
 * Edit any of these later in Admin → Content.
 */

$disclaimer = "This website provides medical equipment information only. Use of medical ozone equipment should be under qualified medical supervision and applicable local regulations. Nothing on this site is a claim that ozone therapy treats, cures or prevents any disease.";

$instructionsBody = <<<'HTML'
<div class="doc-section" id="safety">
  <h2><span class="n">1</span> Safety First</h2>
  <div class="callout danger"><div><strong>Never breathe ozone directly.</strong> Ozone is harmful to the lungs if inhaled. Always operate in a ventilated area and route unused ozone to the built-in destructor port.</div></div>
  <div class="callout warn"><div><strong>Use medical-grade oxygen only.</strong> Do not use ambient air or industrial oxygen — ozone yield and purity depend on a clean O₂ feed.</div></div>
  <ul class="pd-highlights">
    <li>Keep the unit dry, upright and on a stable surface during use.</li>
    <li>Use only ozone-resistant tubing and accessories (supplied) — standard plastics degrade.</li>
    <li>Do not block the destructor outlet or ventilation slots.</li>
    <li>Ozone therapy must be administered by, or under the guidance of, a qualified practitioner.</li>
    <li>Switch off and disconnect oxygen before connecting or removing accessories.</li>
  </ul>
</div>
<div class="doc-section" id="setup">
  <h2><span class="n">2</span> Setup &amp; Connection</h2>
  <ol class="numlist">
    <li><div><strong>Choose your oxygen source.</strong> A medical oxygen cylinder (with the matching AOT regulator) or an oxygen concentrator with a correct flow meter.</div></li>
    <li><div><strong>Fit the regulator.</strong> Attach the AOT oxygen regulator that matches your cylinder valve (870 pin-index, 540 threaded, or BN bull-nose).</div></li>
    <li><div><strong>Connect oxygen to the generator.</strong> Run the silicone hose from the regulator outlet to the generator's O₂ inlet port.</div></li>
    <li><div><strong>Connect the output.</strong> Attach tubing to an O₃ outlet, then to your application accessory (stone diffuser for liquids, or male luer lock / 3-valve lock).</div></li>
    <li><div><strong>Connect the destructor.</strong> Route any unused-ozone line to the O₃ destructor port so excess ozone is neutralised.</div></li>
    <li><div><strong>Power up.</strong> Plug in the 100–240 V adaptor. The unit runs on 110–220 V AC at just 15 W.</div></li>
  </ol>
</div>
<div class="doc-section" id="operation">
  <h2><span class="n">3</span> Operation</h2>
  <ol class="numlist">
    <li><div><strong>Select the mode.</strong> Press the M1 / M2 switch. M1 covers levels L1–L4 (lower range); M2 covers L5–L6 (higher range).</div></li>
    <li><div><strong>Set the oxygen flow.</strong> Adjust the flow on your regulator (1/32 … 1 L/min). Lower flow = higher concentration.</div></li>
    <li><div><strong>Choose the level.</strong> Press an L1–L4 button (M1) or use the L5/L6 knobs (M2).</div></li>
    <li><div><strong>Wait for stable output.</strong> Allow about 30 seconds for the ozone concentration to stabilise.</div></li>
    <li><div><strong>Read the value.</strong> Cross-reference flow + level on the concentration chart below.</div></li>
  </ol>
  <div class="callout info"><div><strong>Example:</strong> For ~30 µg/ml, press M1, then L2, and set the regulator to 1/8 L/min (= 30.2 µg/ml).</div></div>
</div>
<div class="doc-section" id="maintenance">
  <h2><span class="n">4</span> After Use &amp; Care</h2>
  <ul class="pd-highlights">
    <li>After therapy, run oxygen-only for a few seconds to flush residual ozone, then power off.</li>
    <li>Disconnect the oxygen supply and depressurise the regulator.</li>
    <li>Rinse and dry reusable accessories; replace stone diffusers and tubing periodically.</li>
    <li>Wipe the housing with a dry or slightly damp cloth — never immerse the unit.</li>
    <li>Store in a cool, dry place away from direct sunlight.</li>
  </ul>
</div>
<div class="doc-section" id="warranty">
  <h2><span class="n">5</span> Warranty &amp; Support</h2>
  <p>The medical ozone generators are covered by a <strong>1-year warranty</strong> against manufacturing defects. Genuine spare accessories are available. For support call <a href="tel:+919958803980">+91 99588 03980</a> or email medicalozonecare@gmail.com. There are no user-serviceable parts inside.</p>
</div>
HTML;

$termsBody = <<<'HTML'
<p>Welcome to Medical Ozone Care. By accessing or using this website you agree to these Terms &amp; Conditions.</p>
<h3>1. Information only</h3><p>This website provides information about medical ozone equipment, accessories and related components, and a facility to submit enquiries. Details, specifications, images and prices may change without notice.</p>
<h3>2. Enquiries &amp; quotations</h3><p>Submitting an enquiry does not constitute an order or a binding contract. Prices shown are indicative; final pricing, availability, taxes and delivery are confirmed in a written quotation.</p>
<h3>3. Professional use</h3><p>Medical ozone equipment is intended for use by, or under the supervision of, qualified professionals in accordance with applicable local laws and regulations.</p>
<h3>4. Intellectual property</h3><p>All content on this site is the property of Medical Ozone Care unless otherwise stated and may not be reproduced without permission.</p>
<h3>5. Limitation of liability</h3><p>To the extent permitted by law, Medical Ozone Care is not liable for any indirect or consequential loss arising from use of this website or the equipment described on it.</p>
HTML;

$privacyBody = <<<'HTML'
<p>This Privacy Policy explains how Medical Ozone Care handles information you provide through this website.</p>
<h3>Information we collect</h3><p>When you submit an enquiry we collect the details you provide — name, phone, email, product interest and message — so we can respond.</p>
<h3>How we use it</h3><p>Your information is used only to respond to your enquiry, prepare quotations and provide support. We do not sell your personal information.</p>
<h3>Storage &amp; security</h3><p>Enquiries are stored securely. Inputs are validated and sanitised, and administrative access is password-protected.</p>
<h3>Your choices</h3><p>You may request access to, correction of, or deletion of your enquiry data by contacting medicalozonecare@gmail.com.</p>
HTML;

$disclaimerBody = <<<'HTML'
<div class="callout warn"><div><strong>Medical disclaimer.</strong> This website provides medical equipment information only. Use of medical ozone equipment should be under qualified medical supervision and applicable local regulations.</div></div>
<h3>No medical claims</h3><p>Nothing on this website should be interpreted as a claim that ozone therapy or any product treats, cures, prevents or diagnoses any disease or condition. Application areas are listed for general information only.</p>
<h3>Accuracy</h3><p>While we aim to keep specifications and prices accurate, information may contain errors or become outdated. Confirm details in a written quotation before purchase.</p>
<h3>Regulatory responsibility</h3><p>Buyers are responsible for ensuring that the purchase, possession and use of medical ozone equipment complies with all applicable laws in their jurisdiction.</p>
HTML;

$warrantyBody = <<<'HTML'
<h3>Warranty period</h3><p>The medical ozone generators carry a 1-year warranty against manufacturing defects from the date of delivery, unless stated otherwise on the product.</p>
<h3>What is covered</h3><p>Defects in materials and workmanship under normal, intended use. Genuine spare parts and accessories are available.</p>
<h3>What is not covered</h3><p>Damage from misuse, unauthorised repair or modification, use with incompatible gases/accessories, accidental damage, or normal wear of consumables (diffusers, tubing).</p>
<h3>How to claim</h3><p>Contact +91 99588 03980 or medicalozonecare@gmail.com with your purchase details and a description of the issue. Do not open the unit — no user-serviceable parts inside.</p>
HTML;

$usageBody = <<<'HTML'
<div class="callout danger"><div><strong>Safety.</strong> Never breathe ozone directly. Operate in a ventilated area and route unused ozone to the destructor port. Use medical-grade oxygen only.</div></div>
<h3>Qualified supervision</h3><p>Medical ozone equipment must be operated by, or under the supervision of, appropriately qualified professionals, in line with applicable local regulations and accepted protocols.</p>
<h3>Intended use</h3><p>Equipment described on this site is supplied as professional medical equipment. It is the user's responsibility to determine suitability and follow all safety instructions provided with the product.</p>
<h3>No guarantee of outcome</h3><p>Medical Ozone Care makes no representation regarding clinical outcomes. This website does not provide medical advice.</p>
HTML;

return [
    // ---------- GLOBAL ----------
    ['global.brand', 'Global', 'Brand name', 'text', 'Medical Ozone Care'],
    ['global.phone', 'Global', 'Phone (display)', 'text', '+91 99588 03980'],
    ['global.whatsapp', 'Global', 'WhatsApp number (digits only)', 'text', '919958803980'],
    ['global.email1', 'Global', 'Email 1', 'text', 'shekharaiims@gmail.com'],
    ['global.email2', 'Global', 'Email 2', 'text', 'medicalozonecare@gmail.com'],
    ['global.address', 'Global', 'Address', 'text', 'B-87, Madhu Vihar, Uttam Nagar, New Delhi – 110059, India'],
    ['global.website', 'Global', 'Website', 'text', 'www.medicalozonecare.co.in'],
    ['global.hours', 'Global', 'Business hours', 'text', 'Mon–Sat · 10:00–19:00 IST'],
    ['global.topbar', 'Global', 'Top bar message', 'text', 'Medical Ozone Generator AOT-MD-520 · German technology · CE certified'],
    ['global.cta_quote', 'Global', 'Quote button label', 'text', 'Request a Quote'],
    ['global.nav_home', 'Global', 'Nav: Home', 'text', 'Home'],
    ['global.nav_products', 'Global', 'Nav: Products', 'text', 'Products'],
    ['global.nav_instructions', 'Global', 'Nav: Instructions', 'text', 'Instructions'],
    ['global.nav_faq', 'Global', 'Nav: FAQ', 'text', 'FAQ'],
    ['global.nav_about', 'Global', 'Nav: About', 'text', 'About'],
    ['global.nav_contact', 'Global', 'Nav: Contact', 'text', 'Contact'],
    ['global.disclaimer', 'Global', 'Medical disclaimer', 'textarea', $disclaimer],

    // ---------- HOME ----------
    ['home.hero_badge', 'Home', 'Hero badge', 'text', 'Medical equipment information & enquiry website'],
    ['home.hero_title', 'Home', 'Hero title', 'text', 'Medical Ozone Generator'],
    ['home.hero_title_hl', 'Home', 'Hero title (highlighted word)', 'text', 'AOT-MD-520'],
    ['home.hero_subtitle', 'Home', 'Hero subtitle', 'textarea', 'Professional medical ozone generator product information, specifications, accessories and quotation enquiry for clinics, professionals and distributors — plus a digital V/C model and an ozone water & oil system.'],
    ['home.cta_primary', 'Home', 'Hero button 1', 'text', 'Request Quote'],
    ['home.cta_secondary', 'Home', 'Hero button 2', 'text', 'View Specifications'],
    ['home.cta_call', 'Home', 'Hero button 3 (call)', 'text', 'Call Now'],
    ['home.trust', 'Home', 'Hero trust badges', 'json', ['4–92.5 mg/L ozone range', 'Built-in ozone destructor', '1-year warranty']],
    ['home.stats', 'Home', 'Stat counters', 'json', [['num' => '6', 'label' => 'Concentration levels'], ['num' => '92.5', 'label' => 'Max mg/L ozone'], ['num' => '15W', 'label' => 'Low power draw'], ['num' => '1-yr', 'label' => 'Warranty']]],
    ['home.features_eyebrow', 'Home', 'Features eyebrow', 'text', 'Why the AOT-MD-520'],
    ['home.features_title', 'Home', 'Features title', 'text', 'Engineered for accurate, repeatable ozone therapy'],
    ['home.features_lead', 'Home', 'Features lead', 'textarea', 'Every detail — from the titanium-electrode core to the catalytic destructor — is built for clinical precision and safe everyday use.'],
    ['home.features', 'Home', 'Features (icon|title|text)', 'json', [
        ['icon' => 'shield', 'title' => 'Built-in Ozone Destructor', 'text' => 'A catalytic destructor neutralises unused ozone automatically.'],
        ['icon' => 'sliders', 'title' => '6 Concentration Levels', 'text' => 'Modes M1/M2 and levels L1–L6 for fine, repeatable dosing (4–92.5 mg/L).'],
        ['icon' => 'atom', 'title' => 'Corona-Discharge Core', 'text' => 'Titanium electrode and quartz tube produce clean, stable ozone.'],
        ['icon' => 'globe', 'title' => 'German Technology', 'text' => 'Engineered for accuracy, longevity and consistent output.'],
        ['icon' => 'plug', 'title' => 'Universal Power', 'text' => '110–220 V AC at just 15 W, with a 100–240 V adaptor included.'],
        ['icon' => 'badge', 'title' => 'CE Certified · 1-Year Warranty', 'text' => 'CE-marked construction with a full 12-month warranty.'],
    ]],
    ['home.showcase_eyebrow', 'Home', 'Showcase eyebrow', 'text', 'Flagship Device'],
    ['home.showcase_title', 'Home', 'Showcase title', 'text', 'AOT-MD-520 Medical Ozone Generator'],
    ['home.showcase_lead', 'Home', 'Showcase lead', 'textarea', 'A compact desktop unit that turns medical oxygen into a precisely metered ozone stream. Set the mode, pick a level, dial the flow — and let the built-in destructor handle the rest.'],
    ['home.showcase_checks', 'Home', 'Showcase checklist', 'json', ['Two modes (M1 / M2) with levels L1–L6 for fine dose control', 'Adjustable 4–92.5 mg/L ozone concentration with oxygen', 'Corona-discharge module — titanium electrode & quartz tube', 'Dual O₃ outlets and a built-in catalytic ozone destructor', 'Universal 110–220 V · just 15 W · CE certified']],
    ['home.showcase_img', 'Home', 'Showcase image path', 'text', 'assets/img/products/device-top.svg'],
    ['home.products_eyebrow', 'Home', 'Product range eyebrow', 'text', 'Our Range'],
    ['home.products_title', 'Home', 'Product range title', 'text', 'Medical ozone systems & accessories'],
    ['home.products_lead', 'Home', 'Product range lead', 'textarea', 'From the AOT-MD-520 to a digital V/C generator and an ozone water & oil system — plus regulators and genuine consumables.'],
    ['home.products_cta', 'Home', 'Product range button', 'text', 'Browse all products'],
    ['home.how_eyebrow', 'Home', 'How-it-works eyebrow', 'text', 'How It Works'],
    ['home.how_title', 'Home', 'How-it-works title', 'text', 'From oxygen to therapy in four steps'],
    ['home.how_lead', 'Home', 'How-it-works lead', 'textarea', 'Connect an oxygen source, meter the flow, select your level — clean ozone, ready to use.'],
    ['home.steps', 'Home', 'How-it-works steps', 'json', [
        ['title' => 'Connect oxygen', 'text' => 'Attach a medical oxygen cylinder or concentrator to the matching AOT regulator.'],
        ['title' => 'Set the flow', 'text' => 'Use the regulator to set oxygen flow from 1/32 up to 1 L/min for your target dose.'],
        ['title' => 'Choose a level', 'text' => 'Press M1/M2 and L1–L6 to dial in the exact ozone concentration you need.'],
        ['title' => 'Apply & destruct', 'text' => 'Deliver ozone via the diffuser or luer; unused ozone routes safely to the destructor.'],
    ]],
    ['home.benefits_eyebrow', 'Home', 'Benefits eyebrow', 'text', 'Built Right'],
    ['home.benefits_title', 'Home', 'Benefits title', 'text', 'Clinical-grade where it counts'],
    ['home.benefits_lead', 'Home', 'Benefits lead', 'textarea', 'The AOT-MD-520 pairs a durable corona-discharge core with thoughtful safety and usability details.'],
    ['home.benefits', 'Home', 'Benefits list', 'json', [
        ['icon' => 'atom', 'title' => 'Stable corona-discharge output', 'text' => 'Titanium electrode and quartz tube deliver consistent ozone, batch after batch.'],
        ['icon' => 'shield', 'title' => 'Safety-first design', 'text' => 'Built-in catalytic destructor neutralises unused ozone — no separate unit needed.'],
        ['icon' => 'sliders', 'title' => 'Precise, repeatable dosing', 'text' => 'A printed concentration chart maps every flow + level to an exact mg/L value.'],
        ['icon' => 'award', 'title' => 'Certified & warranted', 'text' => 'CE-marked construction with a full 12-month warranty and genuine spare parts.'],
    ]],
    ['home.benefits_img', 'Home', 'Benefits image path', 'text', 'assets/img/products/device-ports.svg'],
    ['home.chart_eyebrow', 'Home', 'Chart eyebrow', 'text', 'Reference'],
    ['home.chart_title', 'Home', 'Chart title', 'text', 'Ozone concentration chart (µg/ml)'],
    ['home.chart_lead', 'Home', 'Chart lead', 'textarea', 'Pick a mode and level, set the oxygen flow, and read off the exact ozone concentration.'],
    ['home.apps_eyebrow', 'Home', 'Applications eyebrow', 'text', 'Applications'],
    ['home.apps_title', 'Home', 'Applications title', 'text', 'Where medical ozone equipment is used'],
    ['home.apps_lead', 'Home', 'Applications lead', 'textarea', 'Common application areas for medical ozone systems, used by qualified professionals under appropriate supervision.'],
    ['home.apps_disclaimer', 'Home', 'Applications disclaimer', 'textarea', 'Application areas are listed for general information and are not a claim that ozone therapy treats, cures or prevents any disease. Use of medical ozone equipment should be under qualified medical supervision and applicable local regulations.'],
    ['home.tcards_eyebrow', 'Home', 'Testimonials eyebrow', 'text', 'Trusted By Practitioners'],
    ['home.tcards_title', 'Home', 'Testimonials title', 'text', 'What our customers say'],
    ['home.testimonials', 'Home', 'Testimonials', 'json', [
        ['quote' => 'The concentration control is spot-on and the build quality feels genuinely clinical. Setup with our oxygen cylinder took minutes.', 'name' => 'Dr. R. Mehta', 'role' => 'Wellness Clinic, Delhi'],
        ['quote' => 'Exactly the unit we needed for ozonated oil and water work. The built-in destructor gives real peace of mind in the room.', 'name' => 'S. Pillai', 'role' => 'Integrative Therapist'],
        ['quote' => 'Great after-sales support — they helped pick the right regulator for our cylinder valve and delivered fast.', 'name' => 'A. Kumar', 'role' => 'Veterinary Practice'],
    ]],
    ['home.cta_title', 'Home', 'Bottom CTA title', 'text', 'Ready to bring ozone therapy to your practice?'],
    ['home.cta_text', 'Home', 'Bottom CTA text', 'textarea', "Tell us your application and city — we'll recommend the right configuration, share pricing and arrange delivery anywhere in India."],
    ['home.cta_form_title', 'Home', 'CTA form title', 'text', 'Request a quote'],
    ['home.cta_form_note', 'Home', 'CTA form note', 'text', 'We reply within one business day.'],

    // ---------- PRODUCTS ----------
    ['products.hero_title', 'Products', 'Hero title', 'text', 'Our Product Range'],
    ['products.hero_subtitle', 'Products', 'Hero subtitle', 'textarea', 'A complete, compatible ozone-therapy system — generators, the right oxygen regulator for your cylinder, and genuine consumables.'],
    ['products.search_placeholder', 'Products', 'Search placeholder', 'text', 'Search products by name, model or category…'],
    ['products.empty', 'Products', 'No results text', 'text', 'No products match your search.'],

    // ---------- PRODUCT DETAIL ----------
    ['product.related_title', 'Product', 'Related title', 'text', 'You may also need'],
    ['product.cta_brochure', 'Product', 'Brochure button', 'text', 'Download Brochure'],
    ['product.tab_overview', 'Product', 'Tab: Overview', 'text', 'Overview'],
    ['product.tab_specs', 'Product', 'Tab: Specifications', 'text', 'Specifications'],
    ['product.tab_box', 'Product', 'Tab: In the Box', 'text', 'In the Box'],
    ['product.tab_chart', 'Product', 'Tab: Concentration Chart', 'text', 'Concentration Chart'],

    // ---------- INSTRUCTIONS ----------
    ['instructions.hero_title', 'Instructions', 'Hero title', 'text', 'User Guide & Instructions'],
    ['instructions.hero_subtitle', 'Instructions', 'Hero subtitle', 'textarea', 'Everything you need to set up and operate the AOT-MD-520 medical ozone generator safely and accurately.'],
    ['instructions.chart_title', 'Instructions', 'Chart heading', 'text', 'Concentration Chart (µg/ml)'],
    ['instructions.body', 'Instructions', 'Guide body (HTML)', 'html', $instructionsBody],

    // ---------- FAQ ----------
    ['faq.hero_title', 'FAQ', 'Hero title', 'text', 'Frequently Asked Questions'],
    ['faq.hero_subtitle', 'FAQ', 'Hero subtitle', 'textarea', "Quick answers about our medical ozone equipment. Can't find what you need? Contact us."],
    ['faq.items', 'FAQ', 'Questions & answers', 'json', [
        ['q' => 'What is the model number?', 'a' => 'Our flagship is the <strong>AOT-MD-520</strong> (6-level, with built-in ozone destructor). We also offer a digital V/C-display generator and an ozone water & oil system.'],
        ['q' => 'What is the ozone concentration range?', 'a' => 'The AOT-MD-520 delivers <strong>4–92.5 mg/L</strong>, adjustable with oxygen across 6 levels (M1/M2, L1–L6).'],
        ['q' => 'What accessories are included?', 'a' => 'A male luer lock (1), 3-valve lock (1), silicone hose (2 m), stone diffusers (2) and a 100–240 V adaptor (1).'],
        ['q' => 'Is warranty available?', 'a' => 'Yes — a <strong>1-year warranty</strong> against manufacturing defects. Genuine spare parts are available.'],
        ['q' => 'How can I request a quotation?', 'a' => 'Use the Request Quote button, the contact form, WhatsApp, or call +91 99588 03980.'],
        ['q' => 'Is it for professional use?', 'a' => 'Yes. Medical ozone equipment is intended for use by, or under the supervision of, qualified professionals per local regulations.'],
        ['q' => 'How is oxygen connected?', 'a' => "Connect a medical oxygen cylinder (or concentrator) to the matching AOT regulator, then run silicone hose to the generator's O₂ inlet."],
        ['q' => 'Do you provide distributor support?', 'a' => 'Yes — select "Distributor Enquiry" on the contact form or WhatsApp us for terms and details.'],
    ]],

    // ---------- ABOUT ----------
    ['about.hero_title', 'About', 'Hero title', 'text', 'About Medical Ozone Care'],
    ['about.hero_subtitle', 'About', 'Hero subtitle', 'textarea', 'We help clinics, therapists and wellness practitioners across India adopt ozone therapy with confidence — through reliable, certified equipment and genuine, hands-on support.'],
    ['about.story_eyebrow', 'About', 'Story eyebrow', 'text', 'Our Story'],
    ['about.story_title', 'About', 'Story title', 'text', 'Precision ozone, made accessible'],
    ['about.story', 'About', 'Story body (HTML)', 'html', "<p>Medical Ozone Care provides product information and supply support for medical ozone equipment, accessories and related components. We focus on a tightly curated range — the AOT-MD-520 generator, a digital V/C generator, an ozone water & oil system, matching oxygen regulators and genuine consumables — so every component works together.</p><p>From your first enquiry to long-term support, we're a direct line to people who understand the equipment. We help you choose the right configuration for your cylinder and application.</p>"],
    ['about.why_eyebrow', 'About', 'Why-us eyebrow', 'text', 'Why Choose Us'],
    ['about.why_title', 'About', 'Why-us title', 'text', 'What sets Medical Ozone Care apart'],
    ['about.why', 'About', 'Why-us cards', 'json', [
        ['icon' => 'badge', 'title' => 'Certified & Reliable', 'text' => 'CE-marked, German-technology devices built for consistent output.'],
        ['icon' => 'users', 'title' => 'Expert Guidance', 'text' => 'We match the right regulator and accessories to your cylinder and application.'],
        ['icon' => 'truck', 'title' => 'Fast Delivery', 'text' => 'Pan-India dispatch with careful packaging and tracking.'],
        ['icon' => 'headset', 'title' => 'Real Support', 'text' => 'Direct access to people who know the equipment — phone, WhatsApp or email.'],
        ['icon' => 'shield', 'title' => 'Safety Focused', 'text' => 'Built-in destructor and clear instructions for safe operation.'],
        ['icon' => 'award', 'title' => 'Warranty Backed', 'text' => '1-year warranty and genuine spare parts.'],
    ]],
    ['about.stats', 'About', 'Stat cards', 'json', [['num' => '100%', 'label' => 'Genuine equipment'], ['num' => '6', 'label' => 'Concentration levels'], ['num' => 'CE', 'label' => 'Certified devices'], ['num' => 'Pan-India', 'label' => 'Delivery & support']]],
    ['about.contact_title', 'About', 'Contact title', 'text', 'Contact Medical Ozone Care'],
    ['about.contact_lead', 'About', 'Contact lead', 'textarea', 'We typically reply within one business day. For the fastest response, call or WhatsApp us.'],

    // ---------- CONTACT ----------
    ['contact.hero_title', 'Contact', 'Hero title', 'text', "Let's talk ozone therapy"],
    ['contact.hero_subtitle', 'Contact', 'Hero subtitle', 'textarea', 'Questions about the AOT-MD-520, regulators or accessories? We\'re here to help you choose and get set up.'],
    ['contact.tile_call_title', 'Contact', 'Tile: Call title', 'text', 'Call Us'],
    ['contact.tile_wa_title', 'Contact', 'Tile: WhatsApp title', 'text', 'WhatsApp'],
    ['contact.tile_email_title', 'Contact', 'Tile: Email title', 'text', 'Email'],
    ['contact.form_title', 'Contact', 'Form title', 'text', 'Send us a message'],
    ['contact.form_lead', 'Contact', 'Form lead', 'textarea', "Fill in the form and our team will respond with pricing, availability and recommendations."],
    ['contact.info_title', 'Contact', 'Info title', 'text', 'Visit or reach us'],
    ['contact.map_query', 'Contact', 'Google map query', 'text', 'Madhu Vihar, New Delhi 110059'],
    ['contact.success', 'Contact', 'Success message', 'textarea', 'Thank you! Your enquiry has been received. Our team will contact you shortly.'],

    // ---------- FOOTER ----------
    ['footer.about', 'Footer', 'Footer about text', 'textarea', 'Precision medical ozone therapy equipment — generators, oxygen regulators and genuine accessories. German technology, CE-certified, backed by expert support.'],
    ['footer.products_title', 'Footer', 'Column: Products', 'text', 'Products'],
    ['footer.company_title', 'Footer', 'Column: Company', 'text', 'Company'],
    ['footer.contact_title', 'Footer', 'Column: Get in touch', 'text', 'Get in touch'],
    ['footer.bottom', 'Footer', 'Bottom line', 'text', 'All rights reserved.'],

    // ---------- SEO ----------
    ['seo.title', 'SEO', 'Default page title', 'text', 'Medical Ozone Care — Medical Ozone Generator AOT-MD-520'],
    ['seo.description', 'SEO', 'Meta description', 'textarea', 'Medical Ozone Care supplies the AOT-MD-520 ozone generator, a digital V/C generator, an ozone water & oil system, oxygen regulators and accessories. Product information, specifications and quotation enquiry. New Delhi, India.'],
    ['seo.keywords', 'SEO', 'Meta keywords', 'textarea', 'Medical Ozone Care, Medical Ozone Generator, AOT-MD-520, Ozone Generator Specifications, Medical Ozone Equipment India, Ozone Therapy Equipment, ozone water oil machine'],
    ['seo.og_image', 'SEO', 'OG image path', 'text', 'assets/img/products/device-hero.svg'],
    ['seo.base_url', 'SEO', 'Base URL (for sitemap)', 'text', 'https://www.medicalozonecare.co.in'],

    // ---------- LEGAL ----------
    ['legal.terms_title', 'Legal', 'Terms — title', 'text', 'Terms & Conditions'],
    ['legal.terms_body', 'Legal', 'Terms — body (HTML)', 'html', $termsBody],
    ['legal.privacy_title', 'Legal', 'Privacy — title', 'text', 'Privacy Policy'],
    ['legal.privacy_body', 'Legal', 'Privacy — body (HTML)', 'html', $privacyBody],
    ['legal.disclaimer_title', 'Legal', 'Disclaimer — title', 'text', 'Disclaimer'],
    ['legal.disclaimer_body', 'Legal', 'Disclaimer — body (HTML)', 'html', $disclaimerBody],
    ['legal.warranty_title', 'Legal', 'Warranty — title', 'text', 'Warranty Policy'],
    ['legal.warranty_body', 'Legal', 'Warranty — body (HTML)', 'html', $warrantyBody],
    ['legal.usage_title', 'Legal', 'Usage — title', 'text', 'Product Usage Disclaimer'],
    ['legal.usage_body', 'Legal', 'Usage — body (HTML)', 'html', $usageBody],
];
