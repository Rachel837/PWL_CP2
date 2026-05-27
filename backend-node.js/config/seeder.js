const Role = require('../models/Role');
const User = require('../models/User');

const bcrypt = require('bcrypt');

const roles = [
    'administrator',
    'kepala laboratorium',
    'ketua program studi',
    'staf administrasi',
    'staf laboratorium'
];

async function seedData() {
    try {
        // 1. Seed Roles
        for (const roleName of roles) {
            await Role.findOrCreate({
                where: { nama: roleName },
                defaults: { nama: roleName }
            });
        }
        console.log('Roles checked/seeded successfully.');

        // 2. Seed Default Admin User
        const adminRole = await Role.findOne({ where: { nama: 'administrator' } });
        if (adminRole) {
            const hashedPassword = await bcrypt.hash('password', 10);
            const [adminUser, created] = await User.findOrCreate({
                where: { email: 'admin@gmail.com' },
                defaults: {
                    nama: 'Administrator',
                    email: 'admin@gmail.com',
                    password: hashedPassword, // Terenkripsi
                    roles_id: adminRole.id,
                    created_at: new Date()
                }
            });
            if (created) {
                console.log('Default admin user created: admin@gmail.com / password');
            }
        }
        
    } catch (error) {
        console.error('Failed to seed data:', error);
    }
}

module.exports = seedData;
