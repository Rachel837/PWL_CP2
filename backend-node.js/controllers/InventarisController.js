const Inventaris = require('../models/Inventaris');
const Barang = require('../models/Barang');
const Ruangan = require('../models/Ruangan');

exports.getAll = async (req, res) => {
    try {
        const data = await Inventaris.findAll({
            include: [
                { model: Barang, as: 'barang' },
                { model: Ruangan, as: 'ruangan' }
            ],
            order: [['id', 'DESC']]
        });
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.getById = async (req, res) => {
    try {
        const data = await Inventaris.findByPk(req.params.id, {
            include: [
                { model: Barang, as: 'barang' },
                { model: Ruangan, as: 'ruangan' }
            ]
        });
        if (!data) {
            return res.status(404).json({ status: 'error', message: 'Item inventaris tidak ditemukan' });
        }
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.create = async (req, res) => {
    try {
        const { kode_inventaris, kondisi, tanggal_masuk, qr_code, foto_barang, status_barang, status_inventaris, barang_id, ruangan_id } = req.body;
        
        if (!barang_id) {
            return res.status(400).json({ status: 'error', message: 'Barang ID wajib diisi.' });
        }

        const data = await Inventaris.create({
            kode_inventaris,
            kondisi: kondisi || 'Baik',
            tanggal_masuk: tanggal_masuk || new Date().toISOString().split('T')[0],
            qr_code,
            foto_barang,
            status_barang: status_barang || 'aktif',
            status_inventaris: status_inventaris || 'tersedia',
            barang_id,
            ruangan_id,
            status_verifikasi: 'terverifikasi'
        });
        
        res.status(201).json({ status: 'success', message: 'Item inventaris berhasil dibuat', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.update = async (req, res) => {
    try {
        const inventaris = await Inventaris.findByPk(req.params.id);
        if (!inventaris) {
            return res.status(404).json({ status: 'error', message: 'Item inventaris tidak ditemukan' });
        }
        
        const { 
            kode_inventaris, kondisi, tanggal_masuk, qr_code, foto_barang, 
            status_barang, status_inventaris, barang_id, ruangan_id,
            kondisi_pending, foto_pending, status_verifikasi 
        } = req.body;
        
        await inventaris.update({
            kode_inventaris: kode_inventaris !== undefined ? kode_inventaris : inventaris.kode_inventaris,
            kondisi: kondisi !== undefined ? kondisi : inventaris.kondisi,
            tanggal_masuk: tanggal_masuk !== undefined ? tanggal_masuk : inventaris.tanggal_masuk,
            qr_code: qr_code !== undefined ? qr_code : inventaris.qr_code,
            foto_barang: foto_barang !== undefined ? foto_barang : inventaris.foto_barang,
            status_barang: status_barang !== undefined ? status_barang : inventaris.status_barang,
            status_inventaris: status_inventaris !== undefined ? status_inventaris : inventaris.status_inventaris,
            barang_id: barang_id !== undefined ? barang_id : inventaris.barang_id,
            ruangan_id: ruangan_id !== undefined ? ruangan_id : inventaris.ruangan_id,
            kondisi_pending: kondisi_pending !== undefined ? kondisi_pending : inventaris.kondisi_pending,
            foto_pending: foto_pending !== undefined ? foto_pending : inventaris.foto_pending,
            status_verifikasi: status_verifikasi !== undefined ? status_verifikasi : inventaris.status_verifikasi
        });
        
        res.json({ status: 'success', message: 'Item inventaris berhasil diperbarui', data: inventaris });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.delete = async (req, res) => {
    try {
        const inventaris = await Inventaris.findByPk(req.params.id);
        if (!inventaris) {
            return res.status(404).json({ status: 'error', message: 'Item inventaris tidak ditemukan' });
        }
        await inventaris.destroy();
        res.json({ status: 'success', message: 'Item inventaris berhasil dihapus' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};
