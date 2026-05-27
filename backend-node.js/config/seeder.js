const Role = require('../models/Role');
const User = require('../models/User');
const KategoriBarang = require('../models/KategoriBarang');
const Barang = require('../models/Barang');
const Ruangan = require('../models/Ruangan');
const Inventaris = require('../models/Inventaris');

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

        // Seed Default Kepala Lab User for testing
        const kalabRole = await Role.findOne({ where: { nama: 'kepala laboratorium' } });
        if (kalabRole) {
            const hashedPassword = await bcrypt.hash('password', 10);
            const [kalabUser, created] = await User.findOrCreate({
                where: { email: 'kalab@gmail.com' },
                defaults: {
                    nama: 'Kepala Laboratorium',
                    email: 'kalab@gmail.com',
                    password: hashedPassword, // Terenkripsi
                    roles_id: kalabRole.id,
                    created_at: new Date()
                }
            });
            if (created) {
                console.log('Default kalab user created: kalab@gmail.com / password');
            }
        }

        // 3. Seed Kategori Barang
        const [kategoriInventaris] = await KategoriBarang.findOrCreate({
            where: { nama_kategori: 'Inventaris' },
            defaults: { nama_kategori: 'Inventaris' }
        });
        const [kategoriBhp] = await KategoriBarang.findOrCreate({
            where: { nama_kategori: 'BHP' },
            defaults: { nama_kategori: 'BHP' }
        });
        console.log('Categories checked/seeded successfully.');

        // 4. Seed Barang
        const [barang1] = await Barang.findOrCreate({
            where: { nama_barang: 'Laptop ASUS ROG' },
            defaults: {
                nama_barang: 'Laptop ASUS ROG',
                spesifikasi: 'Core i7, 16GB RAM, 512GB SSD',
                satuan: 'Unit',
                kategori_barang_id: kategoriInventaris.id
            }
        });
        const [barang2] = await Barang.findOrCreate({
            where: { nama_barang: 'Kursi Ergonomis' },
            defaults: {
                nama_barang: 'Kursi Ergonomis',
                spesifikasi: 'Bahan Mesh, Adjustable Armrest',
                satuan: 'Unit',
                kategori_barang_id: kategoriInventaris.id
            }
        });
        const [barang3] = await Barang.findOrCreate({
            where: { nama_barang: 'Kertas HVS A4 80gr' },
            defaults: {
                nama_barang: 'Kertas HVS A4 80gr',
                spesifikasi: 'Sinar Dunia A4 80gr 1 Rim',
                satuan: 'Rim',
                kategori_barang_id: kategoriBhp.id
            }
        });
        const [barang4] = await Barang.findOrCreate({
            where: { nama_barang: 'Tinta Printer Epson Hitam' },
            defaults: {
                nama_barang: 'Tinta Printer Epson Hitam',
                spesifikasi: 'Tinta hitam seri T6641',
                satuan: 'Botol',
                kategori_barang_id: kategoriBhp.id
            }
        });
        console.log('Barang checked/seeded successfully.');

        // 5. Seed Ruangan
        const [ruangan1] = await Ruangan.findOrCreate({
            where: { kode_ruangan: 'LAB-KOM-1' },
            defaults: {
                kode_ruangan: 'LAB-KOM-1',
                nama_ruangan: 'Laboratorium Komputer Utama',
                lokasi: 'Gedung A Lantai 2'
            }
        });
        console.log('Ruangan checked/seeded successfully.');

        // 6. Seed Inventaris (items that can be replaced)
        await Inventaris.findOrCreate({
            where: { kode_inventaris: 'INV/LAB-KOM-1/2024/001' },
            defaults: {
                kode_inventaris: 'INV/LAB-KOM-1/2024/001',
                kondisi: 'rusak ringan',
                tanggal_masuk: '2024-01-15',
                qr_code: 'INV/LAB-KOM-1/2024/001',
                foto_barang: '',
                status_barang: 'aktif',
                status_inventaris: 'aktif',
                barang_id: barang1.id,
                ruangan_id: ruangan1.id
            }
        });
        await Inventaris.findOrCreate({
            where: { kode_inventaris: 'INV/LAB-KOM-1/2024/002' },
            defaults: {
                kode_inventaris: 'INV/LAB-KOM-1/2024/002',
                kondisi: 'rusak berat',
                tanggal_masuk: '2024-01-15',
                qr_code: 'INV/LAB-KOM-1/2024/002',
                foto_barang: '',
                status_barang: 'aktif',
                status_inventaris: 'aktif',
                barang_id: barang1.id,
                ruangan_id: ruangan1.id
            }
        });
        console.log('Inventaris checked/seeded successfully.');
        
    } catch (error) {
        console.error('Failed to seed data:', error);
    }
}

module.exports = seedData;
