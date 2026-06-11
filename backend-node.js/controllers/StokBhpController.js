const StokBhp = require('../models/StokBhp');
const Barang = require('../models/Barang');
const KategoriBarang = require('../models/KategoriBarang');

exports.getAll = async (req, res) => {
    try {
        const data = await StokBhp.findAll({
            include: [{
                model: Barang,
                as: 'barang',
                include: [{
                    model: KategoriBarang,
                    as: 'kategori'
                }]
            }]
        });
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.create = async (req, res) => {
    try {
        const { jumlah_stok, minimal_stok, barang_id } = req.body;
        if (!barang_id || jumlah_stok === undefined) {
            return res.status(400).json({ status: 'error', message: 'Field barang_id dan jumlah_stok diperlukan.' });
        }

        // Cek apakah barang ada
        const barang = await Barang.findByPk(barang_id);
        if (!barang) {
            return res.status(404).json({ status: 'error', message: 'Barang tidak ditemukan.' });
        }

        // Cek apakah stok untuk barang ini sudah terdaftar
        const existingStock = await StokBhp.findOne({ where: { barang_id } });
        if (existingStock) {
            return res.status(400).json({ status: 'error', message: 'Stok untuk barang ini sudah terdaftar. Silakan update stok yang ada.' });
        }

        const data = await StokBhp.create({
            jumlah_stok: String(jumlah_stok),
            minimal_stok: String(minimal_stok || '0'),
            barang_id
        });

        res.status(201).json({ status: 'success', message: 'Stok BHP berhasil didaftarkan.', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.update = async (req, res) => {
    try {
        const { id } = req.params;
        const { jumlah_stok, minimal_stok } = req.body;

        const stok = await StokBhp.findByPk(id);
        if (!stok) {
            return res.status(404).json({ status: 'error', message: 'Stok BHP tidak ditemukan.' });
        }

        await stok.update({
            jumlah_stok: jumlah_stok !== undefined ? String(jumlah_stok) : stok.jumlah_stok,
            minimal_stok: minimal_stok !== undefined ? String(minimal_stok) : stok.minimal_stok
        });

        res.json({ status: 'success', message: 'Stok BHP berhasil diupdate.', data: stok });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.delete = async (req, res) => {
    try {
        const { id } = req.params;
        const stok = await StokBhp.findByPk(id);
        if (!stok) {
            return res.status(404).json({ status: 'error', message: 'Stok BHP tidak ditemukan.' });
        }

        await stok.destroy();
        res.json({ status: 'success', message: 'Stok BHP berhasil dihapus.' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

// Mendapatkan daftar barang BHP yang belum terdaftar di stok_bhp
exports.getAvailableBhpItems = async (req, res) => {
    try {
        const kategoriBhp = await KategoriBarang.findOne({ where: { nama_kategori: 'BHP' } });
        if (!kategoriBhp) {
            return res.json({ status: 'success', data: [] });
        }

        const allBhpItems = await Barang.findAll({
            where: { kategori_barang_id: kategoriBhp.id }
        });

        const existingStocks = await StokBhp.findAll({ attributes: ['barang_id'] });
        const existingBarangIds = existingStocks.map(s => s.barang_id);

        const availableItems = allBhpItems.filter(b => !existingBarangIds.includes(b.id));

        res.json({ status: 'success', data: availableItems });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};
