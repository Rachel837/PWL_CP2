const User = require('../models/User');
const Role = require('../models/Role');
const bcrypt = require('bcrypt');

const RESTRICTED_ROLES = ['administrator', 'kepala laboratorium', 'ketua program studi'];

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

        if (roles_id) {
            const role = await Role.findByPk(roles_id);
            if (role && RESTRICTED_ROLES.includes(role.nama.toLowerCase())) {
                const existingUserWithRole = await User.findOne({ where: { roles_id } });
                if (existingUserWithRole) {
                    return res.status(400).json({ 
                        status: 'error', 
                        message: `Hanya diperbolehkan 1 akun untuk role ${role.nama}` 
                    });
                }
            }
        }

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

        if (roles_id && roles_id != pengguna.roles_id) {
            const role = await Role.findByPk(roles_id);
            if (role && RESTRICTED_ROLES.includes(role.nama.toLowerCase())) {
                const existingUserWithRole = await User.findOne({ where: { roles_id } });
                if (existingUserWithRole) {
                    return res.status(400).json({ 
                        status: 'error', 
                        message: `Hanya diperbolehkan 1 akun untuk role ${role.nama}` 
                    });
                }
            }
        }

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
