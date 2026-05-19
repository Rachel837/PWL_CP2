require('dotenv').config();
const express = require('express');
const cors = require('cors');

const app = express();
const PORT = process.env.PORT || 5000;

// Middleware
app.use(cors()); // Mengizinkan Laravel untuk mengakses API ini
app.use(express.json()); // Memparsing request JSON

// Contoh Route
app.get('/api/test', (req, res) => {
    res.json({ message: 'Halo dari Node.js Backend!' });
});

// Import model Barang sebagai contoh endpoint
const Barang = require('./models/Barang');

app.get('/api/barang', async (req, res) => {
    try {
        const dataBarang = await Barang.findAll();
        res.json({
            status: 'success',
            data: dataBarang
        });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// Jalankan Server
app.listen(PORT, () => {
    console.log(`Server Node.js berjalan di http://localhost:${PORT}`);
});
