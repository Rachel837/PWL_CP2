const DraftPengadaan = require('../models/DraftPengadaan');
const DraftPengadaanDetail = require('../models/DraftPengadaanDetail');
const Barang = require('../models/Barang');
const KategoriBarang = require('../models/KategoriBarang');
const Inventaris = require('../models/Inventaris');
const User = require('../models/User');
const { Op } = require('sequelize');

// Mendapatkan semua draft pengadaan
exports.getAll = async (req, res) => {
    try {
        const data = await DraftPengadaan.findAll({
            include: [
                {
                    model: User,
                    as: 'pengguna',
                    attributes: ['id', 'nama', 'email']
                }
            ],
            order: [['created_at', 'DESC']]
        });
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

// Mendapatkan draft pengadaan berdasarkan ID (dengan detail)
exports.getById = async (req, res) => {
    try {
        const data = await DraftPengadaan.findByPk(req.params.id, {
            include: [
                {
                    model: DraftPengadaanDetail,
                    as: 'details',
                    include: [
                        {
                            model: Barang,
                            as: 'barang',
                            include: [
                                {
                                    model: KategoriBarang,
                                    as: 'kategori'
                                }
                            ]
                        },
                        {
                            model: Inventaris,
                            as: 'inventaris_lama'
                        }
                    ]
                },
                {
                    model: User,
                    as: 'pengguna',
                    attributes: ['id', 'nama', 'email']
                }
            ]
        });
        
        if (!data) {
            return res.status(404).json({ status: 'error', message: 'Draft pengadaan tidak ditemukan' });
        }
        
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

// Membuat draft pengadaan baru
exports.create = async (req, res) => {
    try {
        const { tahun, users_id, catatan } = req.body;
        
        if (!tahun || !users_id) {
            return res.status(400).json({ status: 'error', message: 'Tahun dan users_id diperlukan' });
        }
        
        const data = await DraftPengadaan.create({
            tahun,
            users_id,
            status: 'draft',
            catatan: catatan || ''
        });
        
        res.status(201).json({ status: 'success', message: 'Draft pengadaan berhasil dibuat', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

// Menambahkan detail barang ke draft pengadaan
exports.addDetail = async (req, res) => {
    try {
        const { draft_pengadaan_id, barang_id, jumlah, harga_estimasi, link_pembelian, inventaris_id_lama } = req.body;
        
        if (!draft_pengadaan_id || !barang_id || !jumlah) {
            return res.status(400).json({ status: 'error', message: 'Field yang diperlukan: draft_pengadaan_id, barang_id, jumlah' });
        }
        
        // Cek apakah draft pengadaan ada
        const draftExists = await DraftPengadaan.findByPk(draft_pengadaan_id);
        if (!draftExists) {
            return res.status(404).json({ status: 'error', message: 'Draft pengadaan tidak ditemukan' });
        }
        
        // Cek apakah barang ada
        const barangExists = await Barang.findByPk(barang_id);
        if (!barangExists) {
            return res.status(404).json({ status: 'error', message: 'Barang tidak ditemukan' });
        }
        
        const data = await DraftPengadaanDetail.create({
            draft_pengadaan_id,
            barang_id,
            jumlah,
            harga_estimasi: harga_estimasi || 0,
            link_pembelian: link_pembelian || '',
            status_approval: 'pending',
            inventaris_id: inventaris_id_lama || null
        });
        
        res.status(201).json({ status: 'success', message: 'Detail barang berhasil ditambahkan', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

// Mendapatkan detail draft pengadaan
exports.getDetails = async (req, res) => {
    try {
        const { draft_pengadaan_id } = req.params;
        
        const data = await DraftPengadaanDetail.findAll({
            where: { draft_pengadaan_id },
            include: [
                {
                    model: Barang,
                    as: 'barang',
                    include: [
                        {
                            model: KategoriBarang,
                            as: 'kategori'
                        }
                    ]
                },
                {
                    model: Inventaris,
                    as: 'inventaris_lama'
                }
            ]
        });
        
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

// Mengupdate detail barang dalam draft pengadaan
exports.updateDetail = async (req, res) => {
    try {
        const { id } = req.params;
        const { jumlah, harga_estimasi, link_pembelian, inventaris_id_lama, status_approval, catatan_kaprodi } = req.body;
        
        const detail = await DraftPengadaanDetail.findByPk(id);
        if (!detail) {
            return res.status(404).json({ status: 'error', message: 'Detail barang tidak ditemukan' });
        }
        
        await detail.update({
            jumlah: jumlah || detail.jumlah,
            harga_estimasi: harga_estimasi !== undefined ? harga_estimasi : detail.harga_estimasi,
            link_pembelian: link_pembelian !== undefined ? link_pembelian : detail.link_pembelian,
            inventaris_id: inventaris_id_lama !== undefined ? inventaris_id_lama : detail.inventaris_id,
            status_approval: status_approval !== undefined ? status_approval : detail.status_approval,
            catatan_kaprodi: catatan_kaprodi !== undefined ? catatan_kaprodi : detail.catatan_kaprodi
        });
        
        res.json({ status: 'success', message: 'Detail barang berhasil diupdate', data: detail });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

// Menghapus detail barang dari draft pengadaan
exports.deleteDetail = async (req, res) => {
    try {
        const { id } = req.params;
        
        const detail = await DraftPengadaanDetail.findByPk(id);
        if (!detail) {
            return res.status(404).json({ status: 'error', message: 'Detail barang tidak ditemukan' });
        }
        
        await detail.destroy();
        res.json({ status: 'success', message: 'Detail barang berhasil dihapus' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

// Mengupdate status draft pengadaan
exports.updateStatus = async (req, res) => {
    try {
        const { id } = req.params;
        const { status, catatan } = req.body;
        
        const draftPengadaan = await DraftPengadaan.findByPk(id);
        if (!draftPengadaan) {
            return res.status(404).json({ status: 'error', message: 'Draft pengadaan tidak ditemukan' });
        }
        
        await draftPengadaan.update({
            status: status || draftPengadaan.status,
            catatan: catatan !== undefined ? catatan : draftPengadaan.catatan
        });
        
        res.json({ status: 'success', message: 'Status draft pengadaan berhasil diupdate', data: draftPengadaan });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

// Menghapus draft pengadaan
exports.delete = async (req, res) => {
    try {
        const { id } = req.params;
        
        const draftPengadaan = await DraftPengadaan.findByPk(id);
        if (!draftPengadaan) {
            return res.status(404).json({ status: 'error', message: 'Draft pengadaan tidak ditemukan' });
        }
        
        // Hapus semua detail terlebih dahulu
        await DraftPengadaanDetail.destroy({ where: { draft_pengadaan_id: id } });
        
        // Hapus draft pengadaan
        await draftPengadaan.destroy();
        
        res.json({ status: 'success', message: 'Draft pengadaan berhasil dihapus' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

// Mendapatkan barang yang tersedia (untuk opsi pemilihan barang)
exports.getAvailableBarang = async (req, res) => {
    try {
        const data = await Barang.findAll({
            include: [
                {
                    model: KategoriBarang,
                    as: 'kategori'
                }
            ]
        });
        
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

// Mendapatkan inventaris yang bisa diganti
exports.getReplacementInventaris = async (req, res) => {
    try {
        const { barang_id } = req.params;
        
        const data = await Inventaris.findAll({
            where: { barang_id },
            include: [
                {
                    model: Barang,
                    as: 'barang',
                    include: [
                        {
                            model: KategoriBarang,
                            as: 'kategori'
                        }
                    ]
                }
            ]
        });
        
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

// Mendapatkan draft pengadaan berdasarkan user (kepala lab)
exports.getByUser = async (req, res) => {
    try {
        const { users_id } = req.params;
        
        const data = await DraftPengadaan.findAll({
            where: { users_id },
            include: [
                {
                    model: DraftPengadaanDetail,
                    as: 'details',
                    include: [
                        {
                            model: Barang,
                            as: 'barang',
                            include: [
                                {
                                    model: KategoriBarang,
                                    as: 'kategori'
                                }
                            ]
                        }
                    ]
                }
            ],
            order: [['created_at', 'DESC']]
        });
        
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};
