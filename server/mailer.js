// Email notifications via nodemailer. If SMTP env vars are not set, enquiries are
// still saved and the notification is logged to the console (no crash).
import nodemailer from "nodemailer";

const {
  SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, SMTP_SECURE,
  SMTP_FROM, NOTIFY_TO,
} = process.env;

const enabled = Boolean(SMTP_HOST && SMTP_USER && SMTP_PASS);
const recipients = (NOTIFY_TO || "shekharaiims@gmail.com,medicalozonecare@gmail.com")
  .split(",").map((s) => s.trim()).filter(Boolean);

let transporter = null;
if (enabled) {
  transporter = nodemailer.createTransport({
    host: SMTP_HOST,
    port: Number(SMTP_PORT) || 587,
    secure: SMTP_SECURE === "true" || Number(SMTP_PORT) === 465,
    auth: { user: SMTP_USER, pass: SMTP_PASS },
  });
}

const esc = (s) => String(s ?? "").replace(/[<>&]/g, (c) => ({ "<": "&lt;", ">": "&gt;", "&": "&amp;" }[c]));

export async function sendEnquiryNotification(enq) {
  const subject = `New Medical Ozone Care Enquiry - ${enq.enquiry_type || "Enquiry"}${enq.interested_product ? " · " + enq.interested_product : ""}`;
  const lines = [
    ["Name", enq.name],
    ["Phone", enq.phone],
    ["Email", enq.email],
    ["Enquiry Type", enq.enquiry_type],
    ["Interested Product", enq.interested_product],
    ["Message", enq.message],
    ["Source", enq.source],
    ["Received", enq.created_at],
  ];
  const text = lines.map(([k, v]) => `${k}: ${v || "-"}`).join("\n");
  const html = `
    <div style="font-family:Arial,sans-serif;max-width:560px">
      <h2 style="color:#008b7a;margin:0 0 12px">New Enquiry — Medical Ozone Care</h2>
      <table style="border-collapse:collapse;width:100%">
        ${lines.map(([k, v]) => `<tr>
          <td style="padding:8px 10px;border:1px solid #e3edf1;background:#f3fafb;font-weight:bold;width:38%">${esc(k)}</td>
          <td style="padding:8px 10px;border:1px solid #e3edf1">${esc(v) || "-"}</td></tr>`).join("")}
      </table>
      <p style="color:#60758b;font-size:12px;margin-top:14px">Sent automatically from the Medical Ozone Care website.</p>
    </div>`;

  if (!enabled) {
    console.log(`✉️  [email not configured] Would notify ${recipients.join(", ")}\n   Subject: ${subject}`);
    return { ok: false, skipped: true };
  }
  try {
    await transporter.sendMail({
      from: SMTP_FROM || `Medical Ozone Care <${SMTP_USER}>`,
      to: recipients.join(","),
      replyTo: enq.email || undefined,
      subject, text, html,
    });
    console.log(`✉️  Enquiry notification emailed to ${recipients.join(", ")}`);
    return { ok: true };
  } catch (err) {
    console.error("✉️  Email send failed:", err.message);
    return { ok: false, error: err.message };
  }
}

export const mailerEnabled = enabled;
