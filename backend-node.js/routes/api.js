const express = require('express');
const router = express.Router();

const RoleController = require('../controllers/RoleController');
const UserController = require('../controllers/UserController');
const RuanganController = require('../controllers/RuanganController');
const AuthController = require('../controllers/AuthController');

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

module.exports = router;
