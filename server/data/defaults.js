// Default editable content (contact, SEO, legal pages). Admin overrides are stored
// in the settings table; the public API merges defaults + overrides.
import { company } from "./products.js";

export const MEDICAL_DISCLAIMER =
  "This website provides medical equipment information only. Use of medical ozone equipment should be under qualified medical supervision and applicable local regulations. Nothing on this site is a medical claim that ozone therapy treats, cures or prevents any disease.";

export const defaultSettings = {
  contact: {
    company: company.name,
    person: company.contactPerson,
    phone: company.phonePrimary,
    phoneRaw: company.phoneRaw,
    email1: company.emails[0],
    email2: company.emails[1],
    address: company.address,
    website: company.website,
    hours: "Mon–Sat · 10:00–19:00 IST",
  },
  seo: {
    siteTitle: "Medical Ozone Care — Medical Ozone Generator AOT-MD-520",
    siteDescription:
      "Medical Ozone Care supplies the AOT-MD-520 medical ozone generator, a digital V/C ozone generator, an ozone water & oil system, oxygen regulators and accessories. Product information, specifications and quotation enquiry. New Delhi, India.",
    keywords:
      "Medical Ozone Care, Medical Ozone Generator, AOT-MD-520, Ozone Generator Specifications, Medical Ozone Equipment India, Ozone Therapy Equipment, ozone water oil machine",
    ogImage: "/assets/img/products/device-hero.svg",
    baseUrl: "https://www.medicalozonecare.co.in",
  },
  legal: {
    terms: {
      title: "Terms & Conditions",
      body: `
<p>Welcome to Medical Ozone Care. By accessing or using this website you agree to these Terms & Conditions.</p>
<h3>1. Information only</h3>
<p>This website provides information about medical ozone equipment, accessories and related components, and a facility to submit enquiries. Product details, specifications, images and prices are provided for general information and may change without notice.</p>
<h3>2. Enquiries & quotations</h3>
<p>Submitting an enquiry does not constitute an order or a binding contract. Prices shown are indicative; final pricing, availability, taxes and delivery are confirmed in a written quotation.</p>
<h3>3. Professional use</h3>
<p>Medical ozone equipment is intended for use by, or under the supervision of, qualified professionals in accordance with applicable local laws and regulations.</p>
<h3>4. Intellectual property</h3>
<p>All content on this site is the property of Medical Ozone Care unless otherwise stated and may not be reproduced without permission.</p>
<h3>5. Limitation of liability</h3>
<p>To the extent permitted by law, Medical Ozone Care is not liable for any indirect or consequential loss arising from use of this website or the equipment described on it.</p>
<h3>6. Contact</h3>
<p>For questions about these terms, contact us at medicalozonecare@gmail.com.</p>`,
    },
    privacy: {
      title: "Privacy Policy",
      body: `
<p>This Privacy Policy explains how Medical Ozone Care handles information you provide through this website.</p>
<h3>Information we collect</h3>
<p>When you submit an enquiry we collect the details you provide — name, phone number, email, product interest and message — so that we can respond to you.</p>
<h3>How we use it</h3>
<p>Your information is used only to respond to your enquiry, prepare quotations and provide support. We do not sell your personal information.</p>
<h3>Storage & security</h3>
<p>Enquiries are stored securely. We apply reasonable technical and organisational measures to protect your data. Inputs are validated and sanitised, and administrative access is password-protected.</p>
<h3>Your choices</h3>
<p>You may request access to, correction of, or deletion of your enquiry data by contacting us at medicalozonecare@gmail.com.</p>
<h3>Third parties</h3>
<p>We may use email and hosting service providers to operate this website and respond to enquiries; these providers process data only on our behalf.</p>`,
    },
    disclaimer: {
      title: "Disclaimer",
      body: `
<div class="callout warn"><div><strong>Medical disclaimer.</strong> This website provides medical equipment information only. Use of medical ozone equipment should be under qualified medical supervision and applicable local regulations.</div></div>
<h3>No medical claims</h3>
<p>Nothing on this website should be interpreted as a claim that ozone therapy or any product treats, cures, prevents or diagnoses any disease or medical condition. Application areas are listed for general information only.</p>
<h3>Accuracy</h3>
<p>While we aim to keep specifications and prices accurate, information may contain errors or become outdated. Confirm details in a written quotation before purchase.</p>
<h3>Regulatory responsibility</h3>
<p>Buyers are responsible for ensuring that the purchase, possession and use of medical ozone equipment complies with all applicable laws and regulations in their jurisdiction.</p>`,
    },
    warranty: {
      title: "Warranty Policy",
      body: `
<h3>Warranty period</h3>
<p>The medical ozone generators carry a 1-year warranty against manufacturing defects from the date of delivery, unless stated otherwise on the specific product.</p>
<h3>What is covered</h3>
<p>The warranty covers defects in materials and workmanship under normal, intended use. Genuine spare parts and accessories are available from Medical Ozone Care.</p>
<h3>What is not covered</h3>
<p>The warranty does not cover damage from misuse, unauthorised repair or modification, use with incompatible gases or accessories, accidental damage, or normal wear of consumables (e.g. diffusers, tubing).</p>
<h3>How to claim</h3>
<p>To make a warranty claim, contact us at +91 99588 03980 or medicalozonecare@gmail.com with your purchase details and a description of the issue. Do not open the unit — there are no user-serviceable parts inside.</p>`,
    },
    usage: {
      title: "Product Usage Disclaimer",
      body: `
<div class="callout danger"><div><strong>Safety.</strong> Never breathe ozone directly. Operate in a ventilated area and route unused ozone to the destructor port. Use medical-grade oxygen only.</div></div>
<h3>Qualified supervision</h3>
<p>Medical ozone equipment must be operated by, or under the supervision of, appropriately qualified professionals, in line with applicable local regulations and accepted protocols.</p>
<h3>Intended use</h3>
<p>Equipment described on this site is supplied as professional medical equipment. It is the user's responsibility to determine suitability for a given application and to follow all safety instructions provided with the product.</p>
<h3>No guarantee of outcome</h3>
<p>Medical Ozone Care makes no representation regarding clinical outcomes. This page and the website do not provide medical advice.</p>`,
    },
  },
};

export const legalSlugs = {
  terms: "terms",
  privacy: "privacy",
  disclaimer: "disclaimer",
  warranty: "warranty",
  usage: "usage",
};
