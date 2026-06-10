const express = require('express');
const router = express.Router();

const RoleController = require('../controllers/RoleController');
const UserController = require('../controllers/UserController');
const RuanganController = require('../controllers/RuanganController');
const AuthController = require('../controllers/AuthController');
const DraftPengadaanController = require('../controllers/DraftPengadaanController');

// Auth
router.post('/login', AuthController.login);

// Roles
router.post('/roles/seed', RoleController.seedRoles);
router.get('/roles', RoleController.getRoles);

// Users (Pengguna)
router.get('/users', UserController.getAll);
router.get('/users/:id', UserController.getById);
router.post('/users', UserController.create);
router.put('/users/:id', UserController.update);
router.delete('/users/:id', UserController.delete);

// Ruangan (Rooms)
router.get('/ruangan', RuanganController.getAll);
router.get('/ruangan/:id', RuanganController.getById);
router.post('/ruangan', RuanganController.create);
router.put('/ruangan/:id', RuanganController.update);
router.delete('/ruangan/:id', RuanganController.delete);

// Draft Pengadaan (Procurement Draft)
router.get('/draft-pengadaan', DraftPengadaanController.getAll);
router.get('/draft-pengadaan/:id', DraftPengadaanController.getById);
router.post('/draft-pengadaan', DraftPengadaanController.create);
router.put('/draft-pengadaan/:id', DraftPengadaanController.updateStatus);
router.delete('/draft-pengadaan/:id', DraftPengadaanController.delete);
router.get('/draft-pengadaan/user/:users_id', DraftPengadaanController.getByUser);

// Draft Pengadaan Detail (Procurement Draft Items)
router.post('/draft-pengadaan-detail', DraftPengadaanController.addDetail);
router.post('/draft-pengadaan/terima-barang', DraftPengadaanController.terimaBarang);
router.get('/draft-pengadaan-detail/:draft_pengadaan_id', DraftPengadaanController.getDetails);
router.put('/draft-pengadaan-detail/:id', DraftPengadaanController.updateDetail);
router.delete('/draft-pengadaan-detail/:id', DraftPengadaanController.deleteDetail);

// Available Items and Inventories
router.get('/barang-tersedia', DraftPengadaanController.getAvailableBarang);
router.get('/inventaris-pengganti/:barang_id', DraftPengadaanController.getReplacementInventaris);

module.exports = router;
