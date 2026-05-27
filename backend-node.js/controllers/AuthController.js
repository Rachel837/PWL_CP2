const User = require('../models/User');
const Role = require('../models/Role');
const bcrypt = require('bcrypt');

exports.login = async (req, res) => {
    try {
        const { email, password } = req.body;
        
        const user = await User.findOne({
            where: { email },
            include: [{ model: Role, as: 'role' }]
        });

        if (!user) {
            return res.status(401).json({ status: 'error', message: 'Email atau password salah' });
        }

        const isMatch = await bcrypt.compare(password, user.password);
        if (!isMatch) {
            return res.status(401).json({ status: 'error', message: 'Email atau password salah' });
        }

        res.json({
            status: 'success',
            message: 'Login berhasil',
            data: {
                id: user.id,
                nama: user.nama,
                email: user.email,
                role: user.role ? user.role.nama : null
            }
        });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};
