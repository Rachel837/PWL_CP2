const Maintenance = require('../models/Maintenance');
const MaintenanceBhp = require('../models/MaintenanceBhp');
const StokBhp = require('../models/StokBhp');
const Inventaris = require('../models/Inventaris');
const Barang = require('../models/Barang');
const User = require('../models/User');

exports.getAll = async (req, res) => {
    try {
        const data = await Maintenance.findAll({
            include: [
                {
                    model: Inventaris,
                    as: 'inventaris',
                    include: [{ model: Barang, as: 'barang' }]
                },
                {
                    model: User,
                    as: 'user',
                    attributes: ['id', 'nama', 'email']
                }
            ],
            order: [['tanggal_maintenance', 'DESC']]
        });
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.getById = async (req, res) => {
    try {
        const data = await Maintenance.findByPk(req.params.id, {
            include: [
                {
                    model: Inventaris,
                    as: 'inventaris',
                    include: [{ model: Barang, as: 'barang' }]
                },
                {
                    model: User,
                    as: 'user',
                    attributes: ['id', 'nama', 'email']
                }
            ]
        });

        if (!data) {
            return res.status(404).json({ status: 'error', message: 'Log maintenance tidak ditemukan' });
        }

        const usedBhp = await MaintenanceBhp.findAll({
            where: { maintenance_id: data.id },
            include: [{ model: Barang, as: 'barang' }]
        });

        res.json({ 
            status: 'success', 
            data: {
                ...data.toJSON(),
                usedBhp: usedBhp.map(b => b.toJSON())
            }
        });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.create = async (req, res) => {
    try {
        const { tanggal_maintenance, kondisi_sebelum, kondisi_sesudah, tindakan, catatan, inventaris_id, users_id, bhps, foto_before, foto_after } = req.body;
        
        if (!inventaris_id || !users_id || !kondisi_sesudah) {
            return res.status(400).json({ status: 'error', message: 'Field inventaris_id, users_id, dan kondisi_sesudah diperlukan.' });
        }

        const inventaris = await Inventaris.findByPk(inventaris_id);
        if (!inventaris) {
            return res.status(404).json({ status: 'error', message: 'Barang inventaris tidak ditemukan.' });
        }

        const maintenance = await Maintenance.create({
            tanggal_maintenance: tanggal_maintenance || new Date().toISOString().split('T')[0],
            kondisi_sebelum: kondisi_sebelum || inventaris.kondisi,
            kondisi_sesudah,
            tindakan: tindakan || '',
            catatan: catatan || '',
            foto_before: foto_before || null,
            foto_after: foto_after || null,
            inventaris_id,
            users_id
        });

        await inventaris.update({ kondisi: kondisi_sesudah });

        const createdBhps = [];
        if (bhps && Array.isArray(bhps)) {
            for (const bhpItem of bhps) {
                const { bhp_id, jumlah_digunakan } = bhpItem;
                if (!bhp_id || !jumlah_digunakan || parseInt(jumlah_digunakan) <= 0) continue;

                const stokBhp = await StokBhp.findByPk(bhp_id);
                if (!stokBhp) {
                    throw new Error(`Stok BHP dengan ID ${bhp_id} tidak ditemukan.`);
                }

                const currentStock = parseInt(stokBhp.jumlah_stok || '0');
                const usedQty = parseInt(jumlah_digunakan);

                if (currentStock < usedQty) {
                    throw new Error(`Stok BHP untuk item ini tidak mencukupi (Tersedia: ${currentStock}, Dibutuhkan: ${usedQty}).`);
                }

                await stokBhp.update({
                    jumlah_stok: String(currentStock - usedQty)
                });

                const maintenanceBhp = await MaintenanceBhp.create({
                    bhp_id,
                    jumlah_digunakan: String(usedQty),
                    maintenance_id: maintenance.id,
                    barang_id: stokBhp.barang_id
                });
                createdBhps.push(maintenanceBhp);
            }
        }

        res.status(201).json({ 
            status: 'success', 
            message: 'Maintenance berhasil dicatat dan kondisi inventaris diperbarui.',
            data: {
                maintenance,
                usedBhps: createdBhps
            }
        });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.getInventarisList = async (req, res) => {
    try {
        const data = await Inventaris.findAll({
            include: [
                { model: Barang, as: 'barang' },
                { model: Ruangan, as: 'ruangan' }
            ]
        });
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

