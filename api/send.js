import nodemailer from 'nodemailer';

export default async function handler(req, res) {
    // 1. Handle CORS (Allow browser requests)
    if (req.method === 'OPTIONS') {
        return res.status(200).end();
    }
    
    if (req.method !== 'POST') {
        return res.status(405).json({ error: 'Method not allowed' });
    }

    // 2. Security Check
    const { key, to, subject, body } = req.body;
    
    if (key !== process.env.API_SECRET) {
        return res.status(403).json({ error: 'Invalid API Key' });
    }

    // 3. Configure Gmail
    const transporter = nodemailer.createTransport({
        host: "smtp.gmail.com",
        port: 587,
        secure: false, // true for 465, false for other ports
        auth: {
            user: process.env.SMTP_USER,
            pass: process.env.SMTP_PASS,
        },
    });

    try {
        // 4. Send Mail
        // Handle "to" being an array or string
        const recipientList = Array.isArray(to) ? to.join(',') : to;

        await transporter.sendMail({
            from: `"Locq System" <${process.env.SMTP_USER}>`,
            to: recipientList,
            subject: subject || "New Notification",
            html: body || "No content provided",
        });

        return res.status(200).json({ status: 'success' });
    } catch (error) {
        console.error(error);
        return res.status(500).json({ error: error.message });
    }
}
