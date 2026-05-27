const User = require('../models/User');
const Role = require('../models/Role');
const bcrypt = require('bcrypt');

exports.getAll = async (req, res) => {
    try {
        const data = await User.findAll({
            include: [{ model: Role, as: 'role' }]
        });
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.getById = async (req, res) => {
    try {
        const data = await User.findByPk(req.params.id, {
            include: [{ model: Role, as: 'role' }]
        });
        if (!data) return res.status(404).json({ status: 'error', message: 'User not found' });
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.create = async (req, res) => {
    try {
        const { nama, email, password, roles_id } = req.body;
        const hashedPassword = await bcrypt.hash(password, 10);
        const data = await User.create({ nama, email, password: hashedPassword, roles_id, created_at: new Date() });
        res.status(201).json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.update = async (req, res) => {
    try {
        const { nama, email, password, roles_id } = req.body;
        const pengguna = await User.findByPk(req.params.id);
        if (!pengguna) return res.status(404).json({ status: 'error', message: 'User not found' });

        let updateData = { nama, email, roles_id };
        if (password) {
            updateData.password = await bcrypt.hash(password, 10);
        }

        await pengguna.update(updateData);
        res.json({ status: 'success', data: pengguna });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.delete = async (req, res) => {
    try {
        const pengguna = await User.findByPk(req.params.id);
        if (!pengguna) return res.status(404).json({ status: 'error', message: 'User not found' });

        await pengguna.destroy();
        res.json({ status: 'success', message: 'User deleted successfully' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};
