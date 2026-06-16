const Role = require('../models/Role');
const User = require('../models/User');
const KategoriBarang = require('../models/KategoriBarang');
const Barang = require('../models/Barang');
const Ruangan = require('../models/Ruangan');
const Inventaris = require('../models/Inventaris');
const DraftPengadaan = require('../models/DraftPengadaan');
const DraftPengadaanDetail = require('../models/DraftPengadaanDetail');
const StokBhp = require('../models/StokBhp');

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

        // Seed Default Kaprodi User for testing
        const kaprodiRole = await Role.findOne({ where: { nama: 'ketua program studi' } });
        if (kaprodiRole) {
            const hashedPassword = await bcrypt.hash('password', 10);
            const [kaprodiUser, created] = await User.findOrCreate({
                where: { email: 'kaprodi@gmail.com' },
                defaults: {
                    nama: 'Ketua Program Studi',
                    email: 'kaprodi@gmail.com',
                    password: hashedPassword,
                    roles_id: kaprodiRole.id,
                    created_at: new Date()
                }
            });
            if (created) {
                console.log('Default kaprodi user created: kaprodi@gmail.com / password');
            }
        }

        // Seed Default Staf Admin User for testing
        const stafAdminRole = await Role.findOne({ where: { nama: 'staf administrasi' } });
        if (stafAdminRole) {
            const hashedPassword = await bcrypt.hash('password', 10);
            const [stafAdminUser, created] = await User.findOrCreate({
                where: { email: 'stafadmin@gmail.com' },
                defaults: {
                    nama: 'Staf Administrasi',
                    email: 'stafadmin@gmail.com',
                    password: hashedPassword,
                    roles_id: stafAdminRole.id,
                    created_at: new Date()
                }
            });
            if (created) {
                console.log('Default staf admin user created: stafadmin@gmail.com / password');
            }
        }

        // Seed Default Staf Lab User for testing
        const stafLabRole = await Role.findOne({ where: { nama: 'staf laboratorium' } });
        if (stafLabRole) {
            const hashedPassword = await bcrypt.hash('password', 10);
            const [stafLabUser, created] = await User.findOrCreate({
                where: { email: 'staflab@gmail.com' },
                defaults: {
                    nama: 'Staf Laboratorium',
                    email: 'staflab@gmail.com',
                    password: hashedPassword,
                    roles_id: stafLabRole.id,
                    created_at: new Date()
                }
            });
            if (created) {
                console.log('Default staf lab user created: staflab@gmail.com / password');
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
        const [barang5] = await Barang.findOrCreate({
            where: { nama_barang: 'Proyektor Epson EB-X51' },
            defaults: {
                nama_barang: 'Proyektor Epson EB-X51',
                spesifikasi: '3800 Lumens, XGA',
                satuan: 'Unit',
                kategori_barang_id: kategoriInventaris.id
            }
        });
        const [barang6] = await Barang.findOrCreate({
            where: { nama_barang: 'Papan Tulis Kaca' },
            defaults: {
                nama_barang: 'Papan Tulis Kaca',
                spesifikasi: 'Ukuran 240x120 cm',
                satuan: 'Unit',
                kategori_barang_id: kategoriInventaris.id
            }
        });
        console.log('Barang checked/seeded successfully.');

        const [ruangan1] = await Ruangan.findOrCreate({
            where: { kode_ruangan: 'LAB-KOM-1' },
            defaults: {
                kode_ruangan: 'LAB-KOM-1',
                nama_ruangan: 'Laboratorium Komputer Utama'
            }
        });
        const [ruangan2] = await Ruangan.findOrCreate({
            where: { kode_ruangan: 'LAB-KOM-2' },
            defaults: {
                kode_ruangan: 'LAB-KOM-2',
                nama_ruangan: 'Laboratorium Komputer Jaringan'
            }
        });
        const [ruanganGudang] = await Ruangan.findOrCreate({
            where: { kode_ruangan: 'GUDANG-1' },
            defaults: {
                kode_ruangan: 'GUDANG-1',
                nama_ruangan: 'Gudang Inventaris'
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
            where: { kode_inventaris: 'INV/2024/002' },
            defaults: {
                kode_inventaris: 'INV/2024/002',
                kondisi: 'rusak berat',
                tanggal_masuk: '2024-01-15',
                qr_code: 'INV-2024-002',
                foto_barang: '',
                status_barang: 'aktif',
                status_inventaris: 'tersedia',
                barang_id: barang1.id,
                ruangan_id: ruangan1.id
            }
        });
        // Item dipinjam
        await Inventaris.findOrCreate({
            where: { kode_inventaris: 'INV/2024/003' },
            defaults: {
                kode_inventaris: 'INV/2024/003',
                kondisi: 'Baik',
                tanggal_masuk: '2024-02-10',
                qr_code: 'INV-2024-003',
                foto_barang: '',
                status_barang: 'aktif',
                status_inventaris: 'dipinjam',
                barang_id: barang5.id,
                ruangan_id: ruangan2.id
            }
        });
        // Item pending verifikasi kondisi
        await Inventaris.findOrCreate({
            where: { kode_inventaris: 'INV/2024/004' },
            defaults: {
                kode_inventaris: 'INV/2024/004',
                kondisi: 'Baik',
                tanggal_masuk: '2024-03-20',
                qr_code: 'INV-2024-004',
                foto_barang: '',
                status_barang: 'aktif',
                status_inventaris: 'tersedia',
                barang_id: barang2.id,
                ruangan_id: ruangan1.id,
                kondisi_pending: 'rusak',
                status_verifikasi: 'pending'
            }
        });
        // Item BHP (Tinta Printer) di Inventaris
        await Inventaris.findOrCreate({
            where: { kode_inventaris: 'INV/2024/005' },
            defaults: {
                kode_inventaris: 'INV/2024/005',
                kondisi: 'Baik',
                tanggal_masuk: '2024-04-10',
                qr_code: 'INV-2024-005',
                foto_barang: '',
                status_barang: 'aktif',
                status_inventaris: 'tersedia',
                barang_id: barang4.id, // BHP
                ruangan_id: ruanganGudang.id
            }
        });
        
        console.log('Inventaris checked/seeded successfully.');

        // Seed Stok BHP
        await StokBhp.findOrCreate({
            where: { barang_id: barang3.id }, // Kertas HVS
            defaults: {
                jumlah_stok: '50',
                minimal_stok: '10',
                barang_id: barang3.id
            }
        });
        await StokBhp.findOrCreate({
            where: { barang_id: barang4.id }, // Tinta Printer
            defaults: {
                jumlah_stok: '15',
                minimal_stok: '5',
                barang_id: barang4.id
            }
        });
        console.log('Stok BHP checked/seeded successfully.');

        // 7. Seed Draft Pengadaan
        // Cek user Kalab
        const kalab = await User.findOne({ where: { email: 'kalab@gmail.com' } });
        if (kalab) {
            // Draft yang sudah disetujui
            const [draft1, createdDraft1] = await DraftPengadaan.findOrCreate({
                where: { tahun: '2025', catatan: 'Pengadaan awal semester (Disetujui)' },
                defaults: {
                    tahun: '2025',
                    users_id: kalab.id,
                    status: 'disetujui',
                    catatan: 'Pengadaan awal semester (Disetujui)'
                }
            });
            
            await DraftPengadaanDetail.findOrCreate({
                where: { draft_pengadaan_id: draft1.id, barang_id: barang1.id },
                defaults: {
                    jumlah: 3,
                    jumlah_diterima: 0,
                    harga_estimasi: 15000000,
                    status_approval: 'disetujui'
                }
            });
            await DraftPengadaanDetail.findOrCreate({
                where: { draft_pengadaan_id: draft1.id, barang_id: barang2.id },
                defaults: {
                    jumlah: 10,
                    jumlah_diterima: 0,
                    harga_estimasi: 800000,
                    status_approval: 'disetujui'
                }
            });
            await DraftPengadaanDetail.findOrCreate({
                where: { draft_pengadaan_id: draft1.id, barang_id: barang4.id },
                defaults: {
                    jumlah: 20,
                    jumlah_diterima: 0,
                    harga_estimasi: 85000,
                    status_approval: 'disetujui'
                }
            });

            // Draft yang baru diajukan (menunggu persetujuan kaprodi)
            const [draft2, createdDraft2] = await DraftPengadaan.findOrCreate({
                where: { tahun: '2025', catatan: 'Pengadaan mendesak lab (Diajukan)' },
                defaults: {
                    tahun: '2025',
                    users_id: kalab.id,
                    status: 'diajukan',
                    catatan: 'Pengadaan mendesak lab (Diajukan)'
                }
            });
            
            await DraftPengadaanDetail.findOrCreate({
                where: { draft_pengadaan_id: draft2.id, barang_id: barang5.id },
                defaults: {
                    jumlah: 1,
                    jumlah_diterima: 0,
                    harga_estimasi: 5000000,
                    status_approval: 'pending'
                }
            });
            
            console.log('Draft Pengadaan checked/seeded successfully.');
        }

    } catch (error) {
        console.error('Failed to seed data:', error);
    }
}

module.exports = seedData;
