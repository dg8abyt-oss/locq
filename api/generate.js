export default function handler(req, res) {
    if (req.method !== 'POST') {
        return res.status(405).json({ error: 'Method not allowed' });
    }

    const { pin } = req.body;
    
    // Check against Environment Variable
    if (pin === process.env.GENERATOR_PIN) {
        return res.status(200).json({ key: process.env.API_SECRET });
    } else {
        return res.status(401).json({ error: 'Incorrect PIN' });
    }
}
