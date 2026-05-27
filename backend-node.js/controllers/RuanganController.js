const Ruangan = require('../models/Ruangan');

exports.getAll = async (req, res) => {
    try {
        const data = await Ruangan.findAll();
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.getById = async (req, res) => {
    try {
        const data = await Ruangan.findByPk(req.params.id);
        if (!data) return res.status(404).json({ status: 'error', message: 'Room not found' });
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.create = async (req, res) => {
    try {
        const { kode_ruangan, nama_ruangan, lokasi } = req.body;
        const data = await Ruangan.create({ kode_ruangan, nama_ruangan, lokasi });
        res.status(201).json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.update = async (req, res) => {
    try {
        const { kode_ruangan, nama_ruangan, lokasi } = req.body;
        const ruangan = await Ruangan.findByPk(req.params.id);
        if (!ruangan) return res.status(404).json({ status: 'error', message: 'Room not found' });

        await ruangan.update({ kode_ruangan, nama_ruangan, lokasi });
        res.json({ status: 'success', data: ruangan });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.delete = async (req, res) => {
    try {
        const ruangan = await Ruangan.findByPk(req.params.id);
        if (!ruangan) return res.status(404).json({ status: 'error', message: 'Room not found' });

        await ruangan.destroy();
        res.json({ status: 'success', message: 'Room deleted successfully' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};
