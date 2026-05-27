const Role = require('../models/Role');

const roles = [
    'administrator',
    'kepala laboratorium',
    'ketua program studi',
    'staf administrasi',
    'staf laboratorium'
];

exports.seedRoles = async (req, res) => {
    try {
        const createdRoles = [];
        for (const roleName of roles) {
            const [role, created] = await Role.findOrCreate({
                where: { nama: roleName },
                defaults: { nama: roleName }
            });
            if (created) {
                createdRoles.push(role);
            }
        }
        res.json({ status: 'success', message: 'Roles seeded successfully', data: createdRoles });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};

exports.getRoles = async (req, res) => {
    try {
        const dataRoles = await Role.findAll();
        res.json({ status: 'success', data: dataRoles });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};
